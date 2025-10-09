import network
import time
import urequests
from machine import Pin, ADC
import usocket as socket
import gc
import utime

"""
Código fusionado:
- Mantiene la funcionalidad principal de lsitoo.py (válvula/relé, LEDs de nivel de gas, envío de lecturas y consulta de estado de válvula por API)
- Añade portal AP para configurar WiFi y conexión automática con credenciales guardadas

IMPORTANTE: Este código está pensado para MicroPython en ESP32.
"""

# --- Configuración WiFi/AP ---
CREDENTIALS_FILE = 'wifi_config.txt'
AP_SSID = 'ESP32-Config'
AP_PASSWORD = 'password123'
AP_IP = '192.168.4.1'

# --- Configuración API (Render) ---
API_BASE_URL = "https://pwa-1s1m.onrender.com"
API_GAS_URL = f"{API_BASE_URL}/api/send_gas_data"
API_VALVE_ENDPOINT = "/api/valve_status"
API_VALVE_KEY = "SUPER_SECRET_API_MLUS"  # Debe coincidir con el backend

# --- Variables Globales ---
DEVICE_MAC = ""  # Se completa al conectar WiFi
mac_ap_display = ""

# --- Pines GPIO (mantiene mapeo de lsitoo.py) ---
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

# --- Lógica de seguridad de gas (mantiene lsitoo.py) ---
gas_alarm_active_since = 0
AUTO_CLOSE_VALVE_DELAY_SECONDS = 10
valve_closed_due_to_gas = False

# --- HTML del portal de configuración ---
CONFIG_PAGE_HTML = """
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Configurar WiFi ESP32</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 16px; }
        .container { max-width: 520px; margin: auto; }
        .ssid-item { margin: 6px 0; }
        .message { margin: 12px 0; padding: 10px; border-radius: 6px; }
        .success { background: #e6ffed; border: 1px solid #2ecc71; }
        .error { background: #ffecec; border: 1px solid #e74c3c; }
        button { padding: 10px 14px; }
        .ssid-list { max-height: 240px; overflow-y: auto; border: 1px solid #ccc; padding: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Configuración WiFi ESP32</h2>
        {mac_address_placeholder}
        {message_placeholder}
        <form method="POST" action="/connect">
            <label>Redes WiFi encontradas:</label>
            <div class="ssid-list">
                {ssid_list_placeholder}
            </div>
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
            <div style="margin-top:12px">
                <button type="submit">Conectar</button>
            </div>
        </form>
    </div>
</body>
</html>
"""

# --- Control de válvula (mantiene lsitoo.py) ---
def abrir_valvula():
    rele.value(0)
    print("Válvula: ABIERTA (Relé=0)")


def cerrar_valvula():
    rele.value(1)
    print("Válvula: CERRADA (Relé=1)")


def beep_confirmation():
    for _ in range(3):
        buzzer_pin.value(1); time.sleep(0.1)
        buzzer_pin.value(0); time.sleep(0.1)


# --- LEDs según nivel de gas (mantiene lsitoo.py) ---
def mostrar_nivel_gas(nivel):
    LOW_GAS_THRESHOLD = 300
    HIGH_GAS_THRESHOLD = 400
    if nivel < LOW_GAS_THRESHOLD:
        led_verde.value(1); led_amarillo.value(0); led_rojo.value(0)
    elif LOW_GAS_THRESHOLD <= nivel < HIGH_GAS_THRESHOLD:
        led_verde.value(0); led_amarillo.value(1); led_rojo.value(0)
    else:
        led_verde.value(0); led_amarillo.value(0); led_rojo.value(1)


# --- API de envío de gas (mantiene lsitoo.py) ---
def enviar_lectura(nivel_gas):
    global DEVICE_MAC
    if not DEVICE_MAC:
        print("ERROR: MAC no disponible, no se envía lectura.")
        return
    headers = {"Content-Type": "application/x-www-form-urlencoded"}
    data = f"MAC={DEVICE_MAC}&nivel_gas={nivel_gas}"
    try:
        r = urequests.post(API_GAS_URL, data=data, headers=headers)
        r.close()
        print(f"Lectura enviada ({nivel_gas})")
    except Exception as e:
        print("ERROR al enviar lectura:", e)


# --- API estado de válvula (mantiene lsitoo.py) ---
def obtener_estado_valvula_desde_api(mac_address, api_key):
    url = f"{API_BASE_URL}{API_VALVE_ENDPOINT}?mac={mac_address}&api_key={api_key}"
    try:
        r = urequests.get(url, timeout=5)
        status = r.status_code
        body = r.text.strip()
        r.close()
        if status == 200:
            return int(body)
        print("API válvula error:", status, body)
        return -1
    except Exception as e:
        print("ERROR API válvula:", e)
        return -1


# --- Utilidades WiFi/credenciales (tomado y adaptado del otro código) ---
def read_credentials(filename):
    try:
        with open(filename, 'r') as f:
            ssid = f.readline().strip()
            password = f.readline().strip()
            if ssid and password:
                return ssid, password
            else:
                return None, None
    except (OSError, ValueError):
        return None, None


def write_credentials(filename, ssid, password):
    try:
        with open(filename, 'w') as f:
            f.write(ssid + '\n')
            f.write(password + '\n')
        return True
    except OSError:
        return False


def connect_wifi_sta(ssid, password, timeout=25):
    global DEVICE_MAC
    wlan = network.WLAN(network.STA_IF)
    wlan.active(True)

    # Si ya está conectado a la red objetivo, mantener
    if wlan.isconnected():
        try:
            current = wlan.config('essid')
            current = current.decode('utf-8') if isinstance(current, bytes) else current
        except Exception:
            current = None
        if current == ssid:
            mac_bytes = wlan.config('mac')
            DEVICE_MAC = ':'.join(['{:02x}'.format(b) for b in mac_bytes]).upper()
            print("WiFi ya conectado a:", current, "MAC:", DEVICE_MAC)
            return True
        else:
            try:
                wlan.disconnect()
            except Exception:
                pass
            time.sleep(1)

    print("Conectando a WiFi:", ssid)
    try:
        wlan.connect(ssid, password)
    except Exception as e:
        print("Error iniciando conexión STA:", e)
        return False

    start = time.time()
    while time.time() - start < timeout:
        if wlan.isconnected():
            print("WiFi conectado.")
            mac_bytes = wlan.config('mac')
            DEVICE_MAC = ':'.join(['{:02x}'.format(b) for b in mac_bytes]).upper()
            print("MAC:", DEVICE_MAC)
            return True
        time.sleep(1)

    print("Timeout WiFi.")
    try:
        wlan.active(False)
    except Exception:
        pass
    return False


def start_config_ap(ssid, password, ip):
    global mac_ap_display
    ap = network.WLAN(network.AP_IF)
    ap.active(False)
    ap.active(True)
    try:
        ap.ifconfig((ip, '255.0.0.0', ip, ip))
    except Exception:
        pass
    try:
        ap.config(essid=ssid, password=password, authmode=network.AUTH_WPA_WPA2_PSK)
    except Exception:
        # Algunos firmwares permiten AP abierto si password vacío
        ap.config(essid=ssid)
    mac_bytes_ap = ap.config('mac')
    mac_ap_display = ':'.join(['{:02x}'.format(b) for b in mac_bytes_ap]).upper()
    print(f"AP de configuración activo: SSID={ssid} IP={ip} MAC_AP={mac_ap_display}")
    return ap


def scan_networks():
    wlan_sta = network.WLAN(network.STA_IF)
    if not wlan_sta.active():
        wlan_sta.active(True)
        time.sleep(1)
    networks = []
    try:
        networks = wlan_sta.scan()
        networks.sort(key=lambda x: x[3], reverse=True)
    except Exception:
        pass
    return networks


def generate_ssid_options(networks):
    if not networks:
        return "<div>No se encontraron redes WiFi.</div>"
    options_html = ""
    for i, net in enumerate(networks):
        try:
            ssid = net[0].decode('utf-8')
        except Exception:
            ssid = str(net[0])
        ssid_escaped = ssid.replace('&', '&amp;').replace('<', '&lt;').replace('>', '&gt;').replace('"', '&quot;').replace("'", '&#039;')
        checked = "checked" if i == 0 else ""
        options_html += f'<div class="ssid-item"><input type="radio" id="ssid_{i}" name="ssid" value="{ssid_escaped}" {checked}><label for="ssid_{i}">{ssid_escaped} (RSSI: {net[3]})</label></div>'
    return options_html


def parse_post_data(request):
    body_start = request.find('\r\n\r\n') + 4
    body = request[body_start:]
    params = {}
    pairs = body.split('&')
    for pair in pairs:
        parts = pair.split('=')
        if len(parts) == 2:
            key = parts[0]
            value = parts[1]
            key = key.replace('+', ' ').replace('%20', ' ')
            value = value.replace('+', ' ').replace('%20', ' ')
            params[key] = value
    return params


def web_server_handler(client_socket):
    gc.collect()
    request_bytes = client_socket.recv(2048)
    if not request_bytes:
        return None
    try:
        request = request_bytes.decode('utf-8')
    except UnicodeError:
        return None

    request_lines = request.split('\r\n')
    if not request_lines or len(request_lines[0].split(' ')) < 3:
        return None

    try:
        method, path, _ = request_lines[0].split(' ')
    except ValueError:
        return None

    response_status = "200 OK"
    content_type = "text/html"
    response_body = ""
    message = ""
    stop_server_signal = False

    if method == 'GET' and path == '/':
        networks = scan_networks()
        ssid_options_html = generate_ssid_options(networks)
        response_body = CONFIG_PAGE_HTML.format(
            message_placeholder="",
            ssid_list_placeholder=ssid_options_html,
            mac_address_placeholder=f'<p><strong>MAC AP:</strong> {mac_ap_display}</p>'
        )

    elif method == 'POST' and path == '/connect':
        post_data = parse_post_data(request)
        ssid_to_connect = post_data.get('ssid')
        password = post_data.get('password')
        if ssid_to_connect and password:
            ap_if = network.WLAN(network.AP_IF)
            ap_was_active = ap_if.active()
            if ap_was_active:
                ap_if.active(False)
                time.sleep(1)
            if connect_wifi_sta(ssid_to_connect, password):
                if write_credentials(CREDENTIALS_FILE, ssid_to_connect, password):
                    message = "¡Conectado exitosamente! Reiniciando en modo aplicación..."
                    response_body = CONFIG_PAGE_HTML.format(
                        message_placeholder=f'<div class="message success">{message}</div>',
                        ssid_list_placeholder='',
                        mac_address_placeholder=f'<p><strong>MAC AP:</strong> {mac_ap_display}</p>'
                    )
                    stop_server_signal = True
                else:
                    message = "Conectado, pero no se pudieron guardar las credenciales. Reinicia manualmente."
                    response_body = CONFIG_PAGE_HTML.format(
                        message_placeholder=f'<div class="message error">{message}</div>',
                        ssid_list_placeholder='',
                        mac_address_placeholder=f'<p><strong>MAC AP:</strong> {mac_ap_display}</p>'
                    )
            else:
                message = "Contraseña incorrecta o no se pudo conectar. Intenta de nuevo."
                if ap_was_active:
                    ap_if.active(True)
                    time.sleep(1)
                response_body = CONFIG_PAGE_HTML.format(
                    message_placeholder=f'<div class="message error">{message}</div>',
                    ssid_list_placeholder=generate_ssid_options(scan_networks()),
                    mac_address_placeholder=f'<p><strong>MAC AP:</strong> {mac_ap_display}</p>'
                )
        else:
            message = "Datos de conexión incompletos."
            response_body = CONFIG_PAGE_HTML.format(
                message_placeholder=f'<div class="message error">{message}</div>',
                ssid_list_placeholder=generate_ssid_options(scan_networks()),
                mac_address_placeholder=f'<p><strong>MAC AP:</strong> {mac_ap_display}</p>'
            )
    else:
        response_status = "404 Not Found"
        response_body = "<h1>404 Not Found</h1>"

    try:
        response_headers = f"HTTP/1.1 {response_status}\r\nContent-Type: {content_type}\r\nContent-Length: {len(response_body)}\r\nConnection: close\r\n\r\n"
        response = f"{response_headers}{response_body}"
        client_socket.sendall(response.encode('utf-8'))
    except Exception:
        pass
    finally:
        try:
            client_socket.close()
        except Exception:
            pass
        gc.collect()
        return stop_server_signal


def run_config_server():
    # Asegurar que STA no interfiera
    sta = network.WLAN(network.STA_IF)
    sta.active(True)
    if sta.isconnected():
        try:
            sta.disconnect()
        except Exception:
            pass
        time.sleep(1)

    ap = start_config_ap(AP_SSID, AP_PASSWORD, AP_IP)
    if not ap:
        return False

    s = None
    server_running = True
    try:
        s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        s.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
        s.bind((AP_IP, 80))
        s.listen(5)
        print("Servidor de configuración escuchando en http://" + AP_IP)
        while server_running:
            try:
                conn, addr = s.accept()
                stop_signal = web_server_handler(conn)
                if stop_signal:
                    server_running = False
            except OSError:
                pass
    except OSError:
        return False
    finally:
        try:
            if s:
                s.close()
        except Exception:
            pass
        try:
            if ap.active():
                ap.active(False)
        except Exception:
            pass
        return sta.isconnected()


# --- Bucle principal de aplicación (mantiene lsitoo.py) ---
def run_combined_application():
    global gas_alarm_active_since, valve_closed_due_to_gas
    wlan = network.WLAN(network.STA_IF)
    print("Iniciando bucle principal de aplicación...")
    GAS_ALARM_THRESHOLD = 400000  # Mantiene valor de lsitoo.py

    while wlan.isconnected():
        try:
            gas_value = mq6_sensor.read()
            print("MQ6:", gas_value)
            mostrar_nivel_gas(gas_value)
            enviar_lectura(gas_value)
            now = utime.time()

            if gas_value > GAS_ALARM_THRESHOLD:
                if gas_alarm_active_since == 0:
                    gas_alarm_active_since = now
                    print("¡GAS DETECTADO! Temporizador", AUTO_CLOSE_VALVE_DELAY_SECONDS, "s.")
                buzzer_pin.value(1); time.sleep(0.3); buzzer_pin.value(0); time.sleep(0.2)
                elapsed = now - gas_alarm_active_since
                if elapsed >= AUTO_CLOSE_VALVE_DELAY_SECONDS and not valve_closed_due_to_gas:
                    print("Gas alto sostenido. Cerrando válvula.")
                    cerrar_valvula()
                    beep_confirmation()
                    valve_closed_due_to_gas = True
            else:
                buzzer_pin.value(0)
                if gas_alarm_active_since != 0:
                    print("Gas normalizado.")
                    gas_alarm_active_since = 0
                    valve_closed_due_to_gas = False

            if not valve_closed_due_to_gas:
                estado = obtener_estado_valvula_desde_api(DEVICE_MAC, API_VALVE_KEY)
                if estado == 0:
                    abrir_valvula()
                elif estado == 1:
                    cerrar_valvula()
                # otros: -1 => ignorar
            else:
                print("Válvula cerrada por seguridad. Ignorando API.")

            time.sleep(3)
        except Exception as e:
            print("Error de loop:", e)
            time.sleep(5)

        if not wlan.isconnected():
            print("WiFi perdido. Saliendo del bucle de aplicación.")
            break


# --- Arranque ---
print("Iniciando ESP32...")
# Asegurar interfaces limpias
sta = network.WLAN(network.STA_IF)
ap = network.WLAN(network.AP_IF)
try:
    sta.active(False)
    ap.active(False)
except Exception:
    pass

# Estado inicial de actuadores/LEDs
abrir_valvula()
led_verde.value(0); led_amarillo.value(0); led_rojo.value(0)
buzzer_pin.value(0)

try:
    while True:
        print("\n--- Bucle Principal ---")
        if sta.isconnected():
            if not DEVICE_MAC:
                mac_bytes = sta.config('mac')
                DEVICE_MAC = ':'.join(['{:02x}'.format(b) for b in mac_bytes]).upper()
            print("Conectado a WiFi. MAC:", DEVICE_MAC)
            run_combined_application()
            print("Aplicación detenida (probablemente WiFi perdido). Reintentando en 5s...")
        else:
            # Intentar con credenciales guardadas
            ssid_saved, pwd_saved = read_credentials(CREDENTIALS_FILE)
            if ssid_saved and pwd_saved:
                print("Credenciales guardadas encontradas. Intentando conectar...")
                if connect_wifi_sta(ssid_saved, pwd_saved, timeout=30):
                    print("Conexión establecida. Ejecutando app en la siguiente iteración.")
                else:
                    print("No fue posible conectar con credenciales guardadas. Iniciando AP de configuración.")
                    if run_config_server():
                        print("Configuración completada con éxito. Reintentando conexión...")
                    else:
                        print("No se completó la configuración. Reintentando...")
            else:
                print("No hay credenciales guardadas. Iniciando AP de configuración.")
                if run_config_server():
                    print("Configuración completada con éxito. Reintentando conexión...")
                else:
                    print("No se completó la configuración. Reintentando...")

        time.sleep(5)
except KeyboardInterrupt:
    print("Terminando...")
finally:
    try:
        network.WLAN(network.STA_IF).active(False)
        network.WLAN(network.AP_IF).active(False)
    except Exception:
        pass
    rele.value(0)
    buzzer_pin.value(0)
    led_verde.value(0); led_amarillo.value(0); led_rojo.value(0)
    print("Recursos liberados.")
