import network
import time
import urequests
from machine import Pin, ADC
import gc
import utime
import usocket as socket
socket.socket().close()

# --- Configuración WiFi/AP ---
CREDENTIALS_FILE = 'wifi_config.txt'
AP_SSID = 'ASG'  # Nombre del SSID del AP (sin MAC; la MAC se mostrará en el portal)
AP_PASSWORD = '12345678'
AP_IP = '192.168.4.1'

# --- Configuración API (Render) existente ---
API_BASE_URL = "https://pwa-1s1m.onrender.com"
API_GAS_URL = f"{API_BASE_URL}/api/send_gas_data"
API_VALVE_ENDPOINT = "/api/valve_status"
API_VALVE_KEY = "SUPER_SECRET_API_MLUS"

# --- Configuración API ASG (Render) ---
ASG_API_BASE = "https://asg-pruebas.onrender.com/index.php"
REGISTER_MAC_URL = f"{ASG_API_BASE}/api/registrar_mac"

# --- Variables globales ---
DEVICE_MAC = ""      # MAC de la interfaz STA (cliente WiFi)
mac_ap_display = ""  # MAC de la interfaz AP (solo informativa en consola)

# --- Pines ---
PIN_RELE = 27
rele = Pin(PIN_RELE, Pin.OUT)

MQ6_PIN_NUM = 34
mq6_sensor = ADC(Pin(MQ6_PIN_NUM))
mq6_sensor.atten(ADC.ATTN_11DB)

BUZZER_PIN_NUM = 32
buzzer_pin = Pin(BUZZER_PIN_NUM, Pin.OUT)

led_verde = Pin(2, Pin.OUT)
led_amarillo = Pin(4, Pin.OUT)
led_rojo = Pin(5, Pin.OUT)

gas_alarm_active_since = 0
AUTO_CLOSE_VALVE_DELAY_SECONDS = 10
valve_closed_due_to_gas = False

# --- Utilidades ---
def abrir_valvula():
    rele.value(0)
    print("Válvula: ABIERTA (Relé=0)")

def cerrar_valvula():
    rele.value(1)
    print("Válvula: CERRADA (Relé=1)")

def beep_confirmation():
    for _ in range(3):
        buzzer_pin.value(1)
        time.sleep(0.1)
        buzzer_pin.value(0)
        time.sleep(0.1)

def mostrar_nivel_gas(nivel):
    LOW_GAS_THRESHOLD = 300
    HIGH_GAS_THRESHOLD = 400
    if nivel < LOW_GAS_THRESHOLD:
        led_verde.value(1); led_amarillo.value(0); led_rojo.value(0)
    elif LOW_GAS_THRESHOLD <= nivel < HIGH_GAS_THRESHOLD:
        led_verde.value(0); led_amarillo.value(1); led_rojo.value(0)
    else:
        led_verde.value(0); led_amarillo.value(0); led_rojo.value(1)

# --- Envío de lecturas al API externo existente ---
def enviar_lectura(nivel_gas):
    global DEVICE_MAC
    if not DEVICE_MAC:
        print("MAC no disponible.")
        return
    headers = {"Content-Type": "application/x-www-form-urlencoded"}
    data = f"MAC={DEVICE_MAC}&nivel_gas={nivel_gas}"
    try:
        r = urequests.post(API_GAS_URL, data=data, headers=headers)
        r.close()
        print(f"Lectura enviada ({nivel_gas})")
    except Exception as e:
        print("Error al enviar lectura:", e)

# --- Consultar estado de válvula en API externo existente ---
def obtener_estado_valvula_desde_api(mac, key):
    url = f"{API_BASE_URL}{API_VALVE_ENDPOINT}?mac={mac}&api_key={key}"
    try:
        r = urequests.get(url, timeout=5)
        if r.status_code == 200:
            estado = int(r.text.strip())
            r.close()
            return estado
        r.close()
    except Exception as e:
        print("Error API válvula:", e)
    return -1

# --- Registrar la MAC de este dispositivo (STA) en tu backend ASG ---
# Envía un POST a /api/registrar_mac con la MAC cuando hay Internet.
def registrar_mac_en_backend():
    global DEVICE_MAC
    if not DEVICE_MAC:
        print("No se registró MAC: DEVICE_MAC vacío")
        return
    try:
        headers = {"Content-Type": "application/x-www-form-urlencoded"}
        data = f"mac={DEVICE_MAC}"
        r = urequests.post(REGISTER_MAC_URL, data=data, headers=headers)
        try:
            snippet = r.text[:80]
        except:
            snippet = ""
        print("Registro MAC ->", r.status_code, snippet)
        r.close()
    except Exception as e:
        print("Error registrando MAC en ASG:", e)

# --- Manejo de credenciales WiFi ---
def read_credentials(filename):
    try:
        with open(filename, 'r') as f:
            ssid = f.readline().strip()
            pwd = f.readline().strip()
            return ssid, pwd
    except:
        return None, None

def write_credentials(filename, ssid, password):
    try:
        with open(filename, 'w') as f:
            f.write(ssid + '\n' + password + '\n')
        return True
    except:
        return False

# --- Conexión WiFi STA ---
def connect_wifi_sta(ssid, password, timeout=25):
    global DEVICE_MAC
    wlan = network.WLAN(network.STA_IF)
    wlan.active(True)

    # Obtener y fijar la MAC del dispositivo (STA) si no está
    if not DEVICE_MAC:
        try:
            mac = wlan.config('mac')
            DEVICE_MAC = ':'.join(['{:02x}'.format(b) for b in mac]).upper()
            print("MAC Dispositivo (STA):", DEVICE_MAC)
        except:
            pass

    if wlan.isconnected():
        # Algunos ports no exponen SSID conectado; intentamos sin comparar
        mac = wlan.config('mac')
        DEVICE_MAC = ':'.join(['{:02x}'.format(b) for b in mac]).upper()
        print("WiFi ya conectado. MAC STA:", DEVICE_MAC)
        registrar_mac_en_backend()
        return True

    print("Conectando a WiFi:", ssid)
    wlan.connect(ssid, password)
    start = time.time()
    while time.time() - start < timeout:
        if wlan.isconnected():
            mac = wlan.config('mac')
            DEVICE_MAC = ':'.join(['{:02x}'.format(b) for b in mac]).upper()
            print("Conectado! MAC STA:", DEVICE_MAC)
            registrar_mac_en_backend()
            return True
        time.sleep(1)
    print("Timeout de conexión.")
    return False

# --- AP y servidor web ---
def start_config_ap(ssid_prefix, password, ip):
    global mac_ap_display
    ap = network.WLAN(network.AP_IF)
    ap.active(True)

    # MAC del AP solo para logs
    mac_bytes = ap.config('mac')
    mac_ap_display = ':'.join(['{:02x}'.format(b) for b in mac_bytes]).upper()

    # AP protegido WPA/WPA2-PSK sin mostrar MAC en el SSID
    ap.config(essid=ssid_prefix, password=password, authmode=network.AUTH_WPA_WPA2_PSK)

    print("AP activo:")
    print("  SSID:", ssid_prefix)
    print("  IP:", ip)
    print("  MAC AP:", mac_ap_display)
    return ap

def scan_networks():
    wlan = network.WLAN(network.STA_IF)
    wlan.active(True)
    try:
        nets = wlan.scan()
        nets.sort(key=lambda x: x[3], reverse=True)
        return nets
    except:
        return []

def parse_post_data(request):
    body_start = request.find('\r\n\r\n') + 4
    body = request[body_start:]
    params = {}
    for pair in body.split('&'):
        if '=' in pair:
            kv = pair.split('=')
            if len(kv) >= 2:
                k = kv[0]
                v = kv[1]
                params[k] = v
    return params

def web_server_handler(sock):
    stop = False
    try:
        gc.collect()
        data = sock.recv(1024)
        if not data:
            return False
        try:
            req = data.decode()
        except:
            req = str(data)
        parts = req.split(' ', 2)
        method = parts[0] if len(parts) > 0 else 'GET'
        path = parts[1] if len(parts) > 1 else '/'

        # Rutas
        if method == 'GET' and path == '/style.css':
            # Servir CSS estático desde archivo local (en fragmentos)
            try:
                sock.send(b"HTTP/1.1 200 OK\r\nContent-Type: text/css\r\nConnection: close\r\n\r\n")
                with open('style.css', 'rb') as f:
                    while True:
                        chunk = f.read(256)
                        if not chunk:
                            break
                        sock.send(chunk)
            except:
                try:
                    sock.send(b"HTTP/1.1 404 Not Found\r\nContent-Type: text/plain\r\nConnection: close\r\n\r\nNot found")
                except:
                    pass

        elif method == 'GET':
            nets = scan_networks()
            gc.collect()
            # HTML + link a CSS externo (fragmentado)
            sock.send(b"HTTP/1.1 200 OK\r\nContent-Type: text/html\r\nConnection: close\r\n\r\n")
            sock.send(b"<!DOCTYPE html><html><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1'><title>Config WiFi</title><link rel='stylesheet' href='/style.css'></head><body><div class='container'><div class='card'>")
            sock.send(b"<h3>ESP32 Configurator</h3>")
            # Mostrar MAC del dispositivo (STA) en la página
            sock.send(b"<p class='kv'><b>MAC Dispositivo:</b> ")
            try:
                sock.send(DEVICE_MAC.encode())
            except:
                pass
            sock.send(b"</p>")
            sock.send(b"<form method='POST' action='/connect'>")
            sock.send(b"<div class='ssid-list'>")
            # Limitar a 8 redes para ahorrar memoria
            count = 0
            for net in nets:
                if count >= 8:
                    break
                ssid = net[0].decode() if isinstance(net[0], bytes) else str(net[0])
                rssi = net[3]
                # Calcular barras por RSSI
                if rssi > -55:
                    bars = 3
                elif rssi > -70:
                    bars = 2
                else:
                    bars = 1
                # Construir mini-icono de señal (inline styles para evitar depender del CSS)
                bars_html = ""
                for j in range(3):
                    active = (j < bars)
                    color = '#0072ff' if active else '#e5e7eb'
                    height = 4 + j * 4
                    bars_html += "<span style=\"display:inline-block;width:5px;margin-left:2px;background-color:{0};height:{1}px;border-radius:2px;\"></span>".format(color, height)
                line = "<label><input type='radio' name='ssid' value='{0}' {1}> {0} <span class='signal'>{2}</span></label>".format(ssid, "checked" if count == 0 else "", bars_html)
                try:
                    sock.send(line.encode())
                except:
                    pass
                count += 1
            sock.send(b"</div>")
            sock.send(b"<input type='password' name='password' placeholder='Contrasena WiFi' required>")
            sock.send(b"<button type='submit' class='btn'>Conectar</button></form>")
            sock.send(b"</div></div></body></html>")

        elif method == 'POST' and path == '/connect':
            form = parse_post_data(req)
            ssid = form.get('ssid')
            pwd = form.get('password')
            message = ""
            if ssid and pwd:
                if connect_wifi_sta(ssid, pwd):
                    write_credentials(CREDENTIALS_FILE, ssid, pwd)
                    message = "Conectado correctamente. Reiniciando..."
                    stop = True
                else:
                    message = "No se pudo conectar. Intenta otra vez."
            else:
                message = "Datos incompletos."
            # Respuesta corta con CSS
            sock.send(b"HTTP/1.1 200 OK\r\nContent-Type: text/html\r\nConnection: close\r\n\r\n")
            sock.send(b"<!DOCTYPE html><html><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1'><title>Config WiFi</title><link rel='stylesheet' href='/style.css'></head><body><div class='container'><div class='card'>")
            try:
                sock.send(("<p>" + message + "</p>").encode())
            except:
                pass
            sock.send(b"<p class='kv'><b>MAC Dispositivo:</b> ")
            try:
                sock.send(DEVICE_MAC.encode())
            except:
                pass
            sock.send(b"</p></div></div></body></html>")
        else:
            sock.send(b"HTTP/1.1 404 Not Found\r\nContent-Type: text/html\r\nConnection: close\r\n\r\n<!DOCTYPE html><html><body><h1>404 Not Found</h1></body></html>")

        gc.collect()
    finally:
        try:
            sock.close()
        except:
            pass
    return stop

# --- Ejecución del servidor de configuración ---
def run_config_server():
    start_config_ap(AP_SSID, AP_PASSWORD, AP_IP)
    time.sleep(1)
    try:
        s = socket.socket()
        s.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
        s.bind((AP_IP, 80))
        s.listen(5)
        print("Servidor web en http://192.168.4.1")
        while True:
            conn, addr = s.accept()
            if web_server_handler(conn):
                break
    except OSError as e:
        print("Error en servidor:", e)
    finally:
        try:
            s.close()
        except:
            pass
        time.sleep(1)
        print("Configuración completada.")
    return True

# --- Bucle operativo ---
def run_combined_application():
    global gas_alarm_active_since, valve_closed_due_to_gas
    wlan = network.WLAN(network.STA_IF)
    print("Entrando al modo operativo...")
    GAS_ALARM_THRESHOLD = 400000

    while wlan.isconnected():
        gas = mq6_sensor.read()
        print("Lectura MQ6:", gas)
        mostrar_nivel_gas(gas)
        enviar_lectura(gas)
        now = utime.time()

        if gas > GAS_ALARM_THRESHOLD:
            if gas_alarm_active_since == 0:
                gas_alarm_active_since = now
                print("Gas detectado, temporizador iniciado.")
            buzzer_pin.value(1)
            time.sleep(0.3)
            buzzer_pin.value(0)
            time.sleep(0.2)
            if now - gas_alarm_active_since >= AUTO_CLOSE_VALVE_DELAY_SECONDS and not valve_closed_due_to_gas:
                cerrar_valvula()
                beep_confirmation()
                valve_closed_due_to_gas = True
        else:
            gas_alarm_active_since = 0
            valve_closed_due_to_gas = False
            buzzer_pin.value(0)

        if not valve_closed_due_to_gas:
            estado = obtener_estado_valvula_desde_api(DEVICE_MAC, API_VALVE_KEY)
            if estado == 0:
                abrir_valvula()
            elif estado == 1:
                cerrar_valvula()

        time.sleep(3)

# --- Inicio del sistema ---
print("Iniciando ESP32...")
abrir_valvula()
led_verde.value(0); led_amarillo.value(0); led_rojo.value(0)
buzzer_pin.value(0)

sta = network.WLAN(network.STA_IF)
sta.active(True)

# Asegurar que DEVICE_MAC sea la MAC de la interfaz STA desde el inicio
if not DEVICE_MAC:
    try:
        mac = sta.config('mac')
        DEVICE_MAC = ':'.join(['{:02x}'.format(b) for b in mac]).upper()
        print("MAC Dispositivo (STA):", DEVICE_MAC)
    except:
        pass

try:
    while True:
        print("\n--- Bucle Principal ---")
        ssid, pwd = read_credentials(CREDENTIALS_FILE)

        if sta.isconnected():
            if not DEVICE_MAC:
                try:
                    mac = sta.config('mac')
                    DEVICE_MAC = ':'.join(['{:02x}'.format(b) for b in mac]).upper()
                except:
                    pass
            print("WiFi conectado, ejecutando aplicación... MAC STA:", DEVICE_MAC)
            registrar_mac_en_backend()
            run_combined_application()

        elif ssid and pwd:
            print("Intentando conectar con credenciales guardadas...")
            if connect_wifi_sta(ssid, pwd, timeout=30):
                print("Conectado con éxito.")
            else:
                print("Error al conectar, abriendo AP para reconfigurar.")
                run_config_server()
        else:
            print("No hay credenciales guardadas. AP activo hasta configuración.")
            run_config_server()
            while True:
                ssid, pwd = read_credentials(CREDENTIALS_FILE)
                if ssid and pwd:
                    print("Credenciales detectadas. Intentando conexión...")
                    if connect_wifi_sta(ssid, pwd, timeout=30):
                        print("Conexión establecida. Cerrando AP y continuando.")
                        network.WLAN(network.AP_IF).active(False)
                        break
                time.sleep(5)

        time.sleep(3)

except KeyboardInterrupt:
    print("Terminando ejecución.")
    network.WLAN(network.AP_IF).active(False)
    network.WLAN(network.STA_IF).active(False)
