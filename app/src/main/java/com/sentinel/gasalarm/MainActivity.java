package com.sentinel.gasalarm;

import android.app.Notification;
import android.app.NotificationChannel;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.content.Context;
import android.content.Intent;
import android.graphics.Color;
import android.media.MediaPlayer;
import android.media.RingtoneManager;
import android.net.Uri;
import android.os.AsyncTask;
import android.os.Build;
import android.os.Bundle;
import android.os.Handler;
import android.os.Vibrator;
import android.view.View;
import android.view.WindowManager;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.app.NotificationCompat;

import org.json.JSONArray;
import org.json.JSONObject;

import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.URL;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.Locale;

public class MainActivity extends AppCompatActivity {

    private LinearLayout devicesLayout;
    private LinearLayout alarmOverlay;
    private TextView statusText;
    private TextView lastUpdateText;
    private Button silenceButton;
    
    private MediaPlayer alarmPlayer;
    private Vibrator vibrator;
    private NotificationManager notificationManager;
    private static final String CHANNEL_ID = "gas_alarm_channel";
    
    private Handler updateHandler = new Handler();
    private static final int UPDATE_INTERVAL = 5000; // 5 segundos
    
    // Configuración de la base de datos
    private static final String API_BASE_URL = "https://tu-servidor.herokuapp.com"; // Cambiar por tu URL
    
    // Umbrales de alerta
    private static final int CRITICAL_THRESHOLD = 500;
    private static final int WARNING_THRESHOLD = 400;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        
        // Mantener pantalla activa
        getWindow().addFlags(WindowManager.LayoutParams.FLAG_KEEP_SCREEN_ON);
        
        initializeViews();
        setupNotificationChannel();
        startPeriodicUpdates();
        
        // Cargar datos iniciales
        new FetchDataTask().execute();
    }

    private void initializeViews() {
        devicesLayout = findViewById(R.id.devicesLayout);
        alarmOverlay = findViewById(R.id.alarmOverlay);
        statusText = findViewById(R.id.statusText);
        lastUpdateText = findViewById(R.id.lastUpdateText);
        silenceButton = findViewById(R.id.silenceButton);
        
        vibrator = (Vibrator) getSystemService(Context.VIBRATOR_SERVICE);
        
        silenceButton.setOnClickListener(v -> silenceAlarm());
    }

    private void setupNotificationChannel() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            notificationManager = getSystemService(NotificationManager.class);
            NotificationChannel channel = new NotificationChannel(
                CHANNEL_ID,
                "Alertas de Gas",
                NotificationManager.IMPORTANCE_HIGH
            );
            channel.setDescription("Alertas por niveles críticos de gas");
            channel.enableLights(true);
            channel.setLightColor(Color.RED);
            channel.enableVibration(true);
            channel.setVibrationPattern(new long[]{0, 1000, 500, 1000});
            notificationManager.createNotificationChannel(channel);
        }
    }

    private void startPeriodicUpdates() {
        updateHandler.postDelayed(new Runnable() {
            @Override
            public void run() {
                new FetchDataTask().execute();
                updateHandler.postDelayed(this, UPDATE_INTERVAL);
            }
        }, UPDATE_INTERVAL);
    }

    private class FetchDataTask extends AsyncTask<Void, Void, String> {
        @Override
        protected String doInBackground(Void... voids) {
            try {
                URL url = new URL(API_BASE_URL + "/api/current_data");
                HttpURLConnection connection = (HttpURLConnection) url.openConnection();
                connection.setRequestMethod("GET");
                connection.setConnectTimeout(10000);
                connection.setReadTimeout(10000);
                
                BufferedReader reader = new BufferedReader(
                    new InputStreamReader(connection.getInputStream())
                );
                
                StringBuilder response = new StringBuilder();
                String line;
                while ((line = reader.readLine()) != null) {
                    response.append(line);
                }
                reader.close();
                
                return response.toString();
                
            } catch (Exception e) {
                return "error: " + e.getMessage();
            }
        }

        @Override
        protected void onPostExecute(String result) {
            if (result.startsWith("error:")) {
                Toast.makeText(MainActivity.this, "Error de conexión", Toast.LENGTH_SHORT).show();
                return;
            }
            
            try {
                JSONObject data = new JSONObject(result);
                updateUI(data);
            } catch (Exception e) {
                e.printStackTrace();
            }
        }
    }

    private void updateUI(JSONObject data) {
        try {
            boolean alarmActive = data.getBoolean("alarm_active");
            JSONArray devices = data.getJSONArray("devices");
            JSONArray alerts = data.getJSONArray("alerts");
            
            // Actualizar estado general
            if (alarmActive) {
                statusText.setText("🚨 ALARMA ACTIVA");
                statusText.setTextColor(Color.RED);
                showAlarmOverlay();
                triggerAlarm();
            } else {
                statusText.setText("✅ SISTEMA NORMAL");
                statusText.setTextColor(Color.GREEN);
                hideAlarmOverlay();
                stopAlarm();
            }
            
            // Actualizar lista de dispositivos
            updateDevicesList(devices);
            
            // Actualizar timestamp
            String timestamp = new SimpleDateFormat("HH:mm:ss", Locale.getDefault())
                .format(new Date());
            lastUpdateText.setText("Última actualización: " + timestamp);
            
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    private void updateDevicesList(JSONArray devices) {
        devicesLayout.removeAllViews();
        
        try {
            for (int i = 0; i < devices.length(); i++) {
                JSONObject device = devices.getJSONObject(i);
                
                View deviceView = getLayoutInflater().inflate(R.layout.device_item, null);
                TextView deviceName = deviceView.findViewById(R.id.deviceName);
                TextView deviceLocation = deviceView.findViewById(R.id.deviceLocation);
                TextView gasLevel = deviceView.findViewById(R.id.gasLevel);
                View statusIndicator = deviceView.findViewById(R.id.statusIndicator);
                
                String name = device.getString("nombre");
                String location = device.getString("ubicacion");
                double gasValue = device.getDouble("ultimo_nivel_gas");
                
                deviceName.setText(name);
                deviceLocation.setText(location);
                gasLevel.setText(String.format(Locale.getDefault(), "%.1f ppm", gasValue));
                
                // Configurar color según nivel de gas
                if (gasValue > CRITICAL_THRESHOLD) {
                    gasLevel.setTextColor(Color.RED);
                    statusIndicator.setBackgroundColor(Color.RED);
                } else if (gasValue > WARNING_THRESHOLD) {
                    gasLevel.setTextColor(Color.YELLOW);
                    statusIndicator.setBackgroundColor(Color.YELLOW);
                } else {
                    gasLevel.setTextColor(Color.GREEN);
                    statusIndicator.setBackgroundColor(Color.GREEN);
                }
                
                devicesLayout.addView(deviceView);
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    private void showAlarmOverlay() {
        alarmOverlay.setVisibility(View.VISIBLE);
        getWindow().addFlags(WindowManager.LayoutParams.FLAG_SHOW_WHEN_LOCKED);
    }

    private void hideAlarmOverlay() {
        alarmOverlay.setVisibility(View.GONE);
        getWindow().clearFlags(WindowManager.LayoutParams.FLAG_SHOW_WHEN_LOCKED);
    }

    private void triggerAlarm() {
        // Vibrar
        if (vibrator != null && vibrator.hasVibrator()) {
            long[] pattern = {0, 1000, 500, 1000};
            vibrator.vibrate(pattern, 0);
        }
        
        // Sonido de alarma
        if (alarmPlayer == null) {
            try {
                Uri alarmSound = RingtoneManager.getDefaultUri(RingtoneManager.TYPE_ALARM);
                alarmPlayer = MediaPlayer.create(this, alarmSound);
                alarmPlayer.setLooping(true);
            } catch (Exception e) {
                e.printStackTrace();
            }
        }
        
        if (alarmPlayer != null && !alarmPlayer.isPlaying()) {
            alarmPlayer.start();
        }
        
        // Notificación
        showAlarmNotification();
    }

    private void stopAlarm() {
        // Detener vibración
        if (vibrator != null) {
            vibrator.cancel();
        }
        
        // Detener sonido
        if (alarmPlayer != null && alarmPlayer.isPlaying()) {
            alarmPlayer.stop();
            alarmPlayer.release();
            alarmPlayer = null;
        }
        
        // Cancelar notificación
        if (notificationManager != null) {
            notificationManager.cancel(1);
        }
    }

    private void silenceAlarm() {
        stopAlarm();
        Toast.makeText(this, "Alarma silenciada", Toast.LENGTH_SHORT).show();
    }

    private void showAlarmNotification() {
        Intent intent = new Intent(this, MainActivity.class);
        PendingIntent pendingIntent = PendingIntent.getActivity(
            this, 0, intent, PendingIntent.FLAG_UPDATE_CURRENT | PendingIntent.FLAG_IMMUTABLE
        );

        Notification notification = new NotificationCompat.Builder(this, CHANNEL_ID)
            .setContentTitle("🚨 Alerta de Gas Crítico")
            .setContentText("Nivel de gas supera límites seguros")
            .setSmallIcon(R.drawable.ic_alarm)
            .setPriority(NotificationCompat.PRIORITY_MAX)
            .setCategory(NotificationCompat.CATEGORY_ALARM)
            .setFullScreenIntent(pendingIntent, true)
            .setAutoCancel(true)
            .build();

        if (notificationManager != null) {
            notificationManager.notify(1, notification);
        }
    }

    @Override
    protected void onDestroy() {
        super.onDestroy();
        stopAlarm();
        updateHandler.removeCallbacksAndMessages(null);
    }
    
}   
