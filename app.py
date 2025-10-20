from flask import Flask, jsonify
from flask_cors import CORS
import pymysql
import os
from datetime import datetime

app = Flask(__name__)
CORS(app)

# Configuración de tu base de datos Clever Cloud
DB_CONFIG = {
    'host': 'beqt3q0gyevl7xonxald-mysql.services.clever-cloud.com',
    'user': 'ujlzjpclm80hxedm',
    'password': '2OJyrxmcM62meKPHo7mu',
    'database': 'beqt3q0gyevl7xonxald',
    'charset': 'utf8mb4',
    'cursorclass': pymysql.cursors.DictCursor
}

@app.route('/')
def home():
    return jsonify({
        "status": "active", 
        "message": "Sentinel Gas Alarm API",
        "version": "1.0"
    })

@app.route('/api/current_data')
def get_current_data():
    try:
        connection = pymysql.connect(**DB_CONFIG)
        with connection.cursor() as cursor:
            # Obtener dispositivos activos
            cursor.execute('''
                SELECT MAC, nombre, ubicacion, ultimo_nivel_gas, ultima_actualizacion
                FROM dispositivos 
                WHERE estado_dispositivo IN ('disponible', 'asignado', 'en_uso')
                ORDER BY nombre
            ''')
            devices = cursor.fetchall()
            
            # Verificar si hay alertas activas
            alerts = []
            alarm_active = False
            for device in devices:
                if device['ultimo_nivel_gas'] > 400:  # Umbral crítico
                    alerts.append({
                        'device_mac': device['MAC'],
                        'device_name': device['nombre'],
                        'current_value': device['ultimo_nivel_gas'],
                        'threshold': 400
                    })
                    alarm_active = True
            
        connection.close()
        return jsonify({
            'devices': devices,
            'alerts': alerts,
            'alarm_active': alarm_active,
            'timestamp': datetime.now().isoformat(),
            'total_devices': len(devices)
        })
        
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/api/device/<mac_address>')
def get_device_data(mac_address):
    try:
        connection = pymysql.connect(**DB_CONFIG)
        with connection.cursor() as cursor:
            cursor.execute('''
                SELECT * FROM lecturas_gas 
                WHERE MAC = %s 
                ORDER BY fecha DESC 
                LIMIT 10
            ''', (mac_address,))
            readings = cursor.fetchall()
            
        connection.close()
        return jsonify({'readings': readings})
        
    except Exception as e:
        return jsonify({'error': str(e)}), 500

if __name__ == '__main__':
    port = int(os.environ.get('PORT', 5000))
    app.run(host='0.0.0.0', port=port)