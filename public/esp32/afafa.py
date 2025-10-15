# MicroPython (ESP32)
# Conexión WiFi MANUAL (sin AP) + Registro automático de MAC (STA) en backend ASG
# 1) Edita WIFI_SSID y WIFI_PASSWORD con tu red WiFi.
# 2) Sube este archivo al ESP32 (como main.py o afafa.py) y ejecútalo.

import network
import time
import urequests
from machine import Pin, ADC
import gc
import utime

# --- CONFIG WiFi (MANUAL) ---
WIFI_SSID = "TU_SSID_AQUI"         # Cambiar
WIFI_PASSWORD = "TU_PASSWORD_AQUI" # Cambiar

# --- API GAS (existente) ---
API_BASE_URL = "https://pwa-1s1m.onrender.com"
API_GAS_URL = API_BASE_URL + "/api/send_gas_data"
API_VALVE_ENDPOINT = "/api/valve_status"
API_VALVE_KEY = "SUPER_SECRET_API_MLUS"

# --- API ASG (Render) ---
# Probar ambas rutas (con y sin index.php) para evitar 404 según config de Render
ASG_API_BASES = (
    "https://asg-pruebas.onrender.com",               # sin index.php
    "https://asg-pruebas.onrender.com/index.php"      # con index.php
)

# --- Variables globales ---
DEVICE_MAC = ""  # MAC de la interfaz STA (cliente WiFi)

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

# --- Utilidades válvula/buzzer/leds ---
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

# --- Envío de lecturas a API GAS existente ---
def enviar_lectura(nivel_gas):
    global DEVICE_MAC
    if not DEVICE_MAC:
        print("MAC no disponible.")
        return
    headers = {"Content-Type": "application/x-www-form-urlencoded"}
    data = "MAC=" + DEVICE_MAC + "&nivel_gas=" + str(nivel_gas)
    try:
        r = urequests.post(API_GAS_URL, data=data, headers=headers)
        r.close()
        print("Lectura enviada (" + str(nivel_gas) + ")")
    except Exception as e:
        print("Error al enviar lectura:", e)

# --- Consultar estado de válvula (API existente) ---
def obtener_estado_valvula_desde_api(mac, key):
    url = API_BASE_URL + API_VALVE_ENDPOINT + "?mac=" + mac + "&api_key=" + key
    try:
        r = urequests.get(url, timeout=5)
        if r.status_code == 200:
            try:
                estado = int(r.text.strip())
            except:
                estado = -1
            r.close()
            return estado
        r.close()
    except Exception as e:
        print("Error API válvula:", e)
    return -1

# --- Registrar MAC (STA) en ASG ---
def registrar_mac_en_backend():
    global DEVICE_MAC
    if not DEVICE_MAC:
        print("No se registró MAC: DEVICE_MAC vacío")
        return False
    headers = {"Content-Type": "application/x-www-form-urlencoded"}
    data = "mac=" + DEVICE_MAC
    for base in ASG_API_BASES:
        url = base + "/api/registrar_mac"
        try:
            print("POST:", url)
            r = urequests.post(url, data=data, headers=headers)
            try:
                snippet = r.text[:80]
            except:
                snippet = ""
            print("Registro MAC ->", r.status_code, snippet)
            ok = (r.status_code >= 200 and r.status_code < 300)
            r.close()
            if ok:
                print("URL usada:", url)
                return True
        except Exception as e:
            print("Fallo con", url, "=>", e)
    return False

# --- WiFi manual (sin AP) ---
def get_sta_mac_str(wlan):
    try:
        mac = wlan.config('mac')
        parts = []
        for b in mac:
            parts.append('{:02x}'.format(b))
        return (':'.join(parts)).upper()
    except Exception as e:
        print('No se pudo obtener la MAC STA:', e)
        return ''


def connect_wifi_manual(ssid, password, timeout=30):
    # Asegurar AP apagado
    ap = network.WLAN(network.AP_IF)
    try:
        ap.active(False)
    except:
        pass

    wlan = network.WLAN(network.STA_IF)
    wlan.active(True)

    if wlan.isconnected():
        print('Ya conectado a WiFi.')
        return wlan

    print('Conectando a WiFi:', ssid)
    try:
        wlan.connect(ssid, password)
    except Exception as e:
        print('Error al iniciar conexion WiFi:', e)
        return None

    start = time.time()
    while time.time() - start < timeout:
        if wlan.isconnected():
            print('Conectado a WiFi.')
            return wlan
        time.sleep(1)

    print('Timeout de conexion WiFi.')
    try:
        wlan.disconnect()
    except:
        pass
    return None

# --- Aplicación principal (mismo comportamiento de sensores) ---
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

gc.collect()

if not WIFI_SSID or not WIFI_PASSWORD or WIFI_SSID == "TU_SSID_AQUI":
    print("Configura WIFI_SSID y WIFI_PASSWORD en este archivo y vuelve a ejecutar.")
else:
    sta = network.WLAN(network.STA_IF)
    sta.active(True)

    wlan = connect_wifi_manual(WIFI_SSID, WIFI_PASSWORD, timeout=30)
    if wlan and wlan.isconnected():
        DEVICE_MAC = get_sta_mac_str(wlan)
        print("MAC Dispositivo (STA):", DEVICE_MAC)

        ok = registrar_mac_en_backend()
        if not ok:
            print("No se pudo confirmar el registro de MAC (se seguirá intentando en envíos de lectura).")

        run_combined_application()
    else:
        print('No fue posible conectar a WiFi. Verificar SSID/Password y cobertura.')
