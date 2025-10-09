# MicroPython (ESP32)
# Registrar MAC (STA) en backend ASG usando WiFi manual (sin AP)
# 1) Edita WIFI_SSID y WIFI_PASSWORD.
# 2) Ejecuta el script en el ESP32.

import network
import time
import urequests
import gc

# --- CONFIG ---
WIFI_SSID = "TU_SSID_AQUI"          # Cambiar
WIFI_PASSWORD = "TU_PASSWORD_AQUI"  # Cambiar

# Backend ASG (Render). Se probaran ambas rutas.
ASG_API_BASES = (
    "https://asg-pruebas.onrender.com",              # sin index.php
    "https://asg-pruebas.onrender.com/index.php"     # con index.php
)

WIFI_CONNECT_TIMEOUT = 30
RETRY_DELAY_SECONDS = 10

# --- UTILS ---
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


def connect_wifi(ssid, password, timeout):
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


def register_mac(mac_str):
    if not mac_str:
        print('MAC vacia, no se envia.')
        return False

    headers = {'Content-Type': 'application/x-www-form-urlencoded'}
    data = 'mac=' + mac_str

    for base in ASG_API_BASES:
        url = base + '/api/registrar_mac'
        try:
            print('POST:', url)
            r = urequests.post(url, data=data, headers=headers)
            try:
                preview = r.text[:120]
            except:
                preview = ''
            print('Respuesta:', r.status_code, preview)
            ok = (r.status_code >= 200 and r.status_code < 300)
            r.close()
            if ok:
                print('URL usada:', url)
                return True
        except Exception as e:
            print('Fallo con', url, '=>', e)
            # probar siguiente base
    return False


# --- MAIN ---
print('Iniciando registro de MAC (WiFi manual)...')
print('SSID objetivo:', WIFI_SSID)

while True:
    gc.collect()

    if (not WIFI_SSID) or (not WIFI_PASSWORD) or (WIFI_SSID == 'TU_SSID_AQUI'):
        print('Configura WIFI_SSID y WIFI_PASSWORD en este archivo y vuelve a ejecutar.')
        break

    wlan = connect_wifi(WIFI_SSID, WIFI_PASSWORD, WIFI_CONNECT_TIMEOUT)
    if wlan and wlan.isconnected():
        mac_sta = get_sta_mac_str(wlan)
        print('MAC Dispositivo (STA):', mac_sta)

        ok = register_mac(mac_sta)
        if ok:
            print('Registro de MAC completado.')
        else:
            print('No se pudo confirmar el registro de MAC.')

        try:
            print('IP:', wlan.ifconfig()[0])
        except:
            pass
        print('Listo. Fin del script.')
        break
    else:
        print('No fue posible conectar a WiFi. Reintentando en', RETRY_DELAY_SECONDS, 'segundos...')
        time.sleep(RETRY_DELAY_SECONDS)
