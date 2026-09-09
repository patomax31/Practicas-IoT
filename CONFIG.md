# CONFIGURACIÓN Y EJEMPLOS RÁPIDOS

## 1. Configuración Rápida del Proyecto

### Estructura de Archivos
```
/home/carlos/Documentos/UNI/IoT/
├── sensor_dht11.c          # Código embebido para Photon
├── index.html              # Página web principal
├── styles.css              # Estilos CSS
├── script.js               # Lógica JavaScript
├── README.md               # Documentación completa
├── REPORTE.md              # Reporte detallado de la actividad
├── TROUBLESHOOTING.md      # Guía de solución de problemas
├── CONFIG.md               # Este archivo
└── sensor_par.c            # Archivo original (legacy)
```

---

## 2. Checklist de Inicio Rápido

### Antes de Empezar
```
☐ Tener cuenta en https://www.particle.io
☐ Tener tarjeta Photon registrada
☐ Tener acceso a Particle IDE (https://build.particle.io)
☐ Sensor DHT11 disponible
☐ Cables y protoboard
☐ Navegador web moderno
```

### Paso 1: Hardware (5 min)
```
1. Conectar DHT11:
   VCC → 3V3
   GND → GND
   DATA → Pin 2

2. Conectar Photon a USB
3. Verificar que parpadea (modo conexión)
```

### Paso 2: Código en Photon (10 min)
```
1. Copiar sensor_dht11.c a Particle IDE
2. Instalar librerías:
   - adafruit/DHT
   - adafruit/Adafruit_Sensor
3. Compilar (Ctrl+Enter)
4. Seleccionar dispositivo Photon
5. Flash (cargar) el código
6. Esperar a que LED sea verde fijo
```

### Paso 3: Verificar en Particle Cloud (5 min)
```
1. Ir a https://build.particle.io
2. Seleccionar dispositivo Photon
3. En la lista de Variables, buscar:
   - TEMP
   - HUM
4. Hacer clic en GET
5. Debe mostrar valores numéricos válidos
6. Copiar el ID del dispositivo (ej: 25001d000847313037363132)
```

### Paso 4: Interfaz Web (5 min)
```
1. Guardar archivos HTML, CSS, JS
2. Abrir index.html en navegador
3. Ingresar:
   - Email: tu_email@particle.io
   - Contraseña: tu_contraseña
   - Device ID: [el copiado en paso 3]
4. Clic en "Conectar"
5. Esperar a que cambie estado a "Conectado"
```

### Paso 5: Verificar Sistema (5 min)
```
1. Debe aparecer:
   ✓ Temperatura actual (°C)
   ✓ Humedad actual (%)
   ✓ Gráficas vacías (se llenarán)
   
2. Esperar 1-2 minutos
3. Verificar que gráficas se llenan de datos
```

**Tiempo total: 30 minutos**

---

## 3. Cambiar Configuración Rápidamente

### Cambiar Pin del Sensor
```cpp
// En sensor_dht11.c, línea 7:
#define DHTPIN 2  // Cambiar a número deseado (3, 4, 5, etc)
```

### Cambiar Frecuencia de Lectura
```cpp
// En sensor_dht11.c, línea 32:
delay(2000);  // Cambiar a 5000 para cada 5 segundos
```

### Cambiar Nombres de Variables
```cpp
// En sensor_dht11.c, línea 19-20:
Particle.variable("TEMP", t);  // Cambiar "TEMP" a otro nombre (max 12 caracteres)
Particle.variable("HUM", h);   // Cambiar "HUM" a otro nombre
```

### Cambiar Frecuencia en Interfaz Web
```javascript
// En script.js, línea 85:
}, 60000);  // Cambiar a 120000 para cada 2 minutos
```

### Cambiar Número de Puntos en Gráfica
```javascript
// En script.js, línea 16:
const MAX_DATA_POINTS = 24;  // Cambiar a 12 o 48 según necesidad
```

---

## 4. Ejemplos de Código

### Ejemplo 1: Leer Solo Temperatura
```cpp
// Versión simplificada - solo temperatura
SYSTEM_THREAD(ENABLED);

#define DHTPIN 2
#define DHTTYPE DHT11

#include "adafruit/DHT/DHT.h"
DHT dht(DHTPIN, DHTTYPE);

double temperature = 0;

void setup() {
    dht.begin();
    Particle.variable("TEMP", temperature);
}

void loop() {
    temperature = dht.readTemperature();
    delay(2000);
}
```

### Ejemplo 2: Publicar Eventos en la Nube
```cpp
// Agregar eventos además de variables

void setup() {
    dht.begin();
    Particle.variable("TEMP", t);
    Particle.variable("HUM", h);
}

void loop() {
    h = dht.readHumidity();
    t = dht.readTemperature();
    
    // Publicar evento solo si cambio significativo
    if (abs(t - lastTemp) > 1.0) {
        Particle.publish("temperature_change", String(t));
        lastTemp = t;
    }
    
    delay(2000);
}
```

### Ejemplo 3: Control Remoto con Función
```cpp
// Permitir encender/apagar un LED remotamente

int ledPin = D7;

void setup() {
    pinMode(ledPin, OUTPUT);
    Particle.function("toggle", toggleLED);
}

int toggleLED(String command) {
    if (command == "on") {
        digitalWrite(ledPin, HIGH);
        return 1;
    } else if (command == "off") {
        digitalWrite(ledPin, LOW);
        return 0;
    }
    return -1;  // Error
}

void loop() {
    // ...
}
```

### Ejemplo 4: Alertas de Temperatura
```javascript
// En script.js - agregar alertas

function updateTemperatureDisplay(temp) {
    const tempElement = document.getElementById('Temperatura');
    const formattedTemp = parseFloat(temp).toFixed(2);
    tempElement.textContent = formattedTemp;
    
    // Alerta si temperatura > 30°C
    if (temp > 30) {
        alert('⚠️ Temperatura alta: ' + formattedTemp + '°C');
        document.getElementById('Temperatura').style.color = 'red';
    } else {
        document.getElementById('Temperatura').style.color = 'black';
    }
}
```

### Ejemplo 5: Exportar Datos a CSV
```javascript
// Ya está implementado, pero aquí está la función:

function exportDataToCSV() {
    if (temperatureData.length === 0) {
        alert('No hay datos para exportar');
        return;
    }

    let csv = 'Hora,Temperatura (°C),Humedad (%)\n';
    for (let i = 0; i < temperatureData.length; i++) {
        csv += `${timeLabels[i]},${temperatureData[i]},${humidityData[i]}\n`;
    }

    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `datos_${new Date().toISOString().split('T')[0]}.csv`;
    a.click();
}
```

---

## 5. Atajos Útiles

### En Particle IDE
```
Ctrl + K        Compilar
Ctrl + Enter    Compilar y cargar
Ctrl + /        Comentar línea
Ctrl + Alt + J  Formato automático
Ctrl + F        Buscar
```

### En Navegador (DevTools)
```
F12             Abrir herramientas de desarrollo
Ctrl + Shift+I  Abrir DevTools
Ctrl + Shift+C  Selector de elementos
Ctrl + Shift+J  Consola
Ctrl + Shift+E  Network (red)
```

### En Terminal (Particle CLI)
```bash
particle login                  # Conectarse
particle list                   # Listar dispositivos
particle device-os current      # Ver versión firmware
particle compile photon         # Compilar localmente
particle flash DEVICE_ID file.bin  # Cargar código
particle serial monitor         # Ver puerto serial
particle serial wifi            # Configurar WiFi
```

---

## 6. Valores de Referencia

### Temperatura Típica
```
°C      Descripción
0       Punto de congelación
15      Día frío
20      Temperatura ambiente normal
25      Día caluroso
37      Temperatura corporal
100     Punto de ebullición
```

### Humedad Típica
```
%       Descripción / Sensación
0       Muy seco
20-30   Seco
40-60   Confortable
70-80   Húmedo
90-100  Muy húmedo
```

### Índice de Calor
```
°C      °F      Sensación
27      80      Confortable
29      85      Caluroso
32      90      Muy caluroso
35      95      Extremadamente caluroso
40      104     Peligrosamente caluroso
```

---

## 7. Limpieza y Mantenimiento

### Limpiar Datos
```javascript
// En consola del navegador (F12):
localStorage.clear();   // Limpiar localStorage
location.reload();      // Recargar página
```

### Resetear Photon
```bash
# Opción 1: Físicamente
Desconectar USB → Esperar 5 seg → Conectar

# Opción 2: Sistema
particle device-os reset DEVICE_ID

# Opción 3: En código
System.reset();
```

### Limpiar Datos en Particle Cloud
```bash
particle variable list DEVICE_ID
# No hay comando para limpiar, los datos se actualizan automáticamente
```

---

## 8. Recursos de Referencia Rápida

### URLs Importantes
```
https://build.particle.io              Particle IDE
https://dashboard.particle.io          Panel de control
https://docs.particle.io               Documentación
https://community.particle.io          Comunidad
https://console.particle.io            Consola Photon
```

### Código de Colores LED Photon
```
Verde fijo      Conectado y funcionando
Azul parpadeo   Escaneando WiFi
Magenta         Conectando
Rojo            Error o desconectado
Amarillo        Cargando código
```

### Límites de Particle
```
Variables publicadas:      20 por dispositivo
Funciones publicadas:      15 por dispositivo
Caracteres en nombre var:  64 (mostrar: 12)
Caracteres en valor:       622
Publicaciones por segundo: 1
API calls por segundo:     10
```

---

## 9. Comandos Útiles Terminal

### Con Particle CLI
```bash
# Instalar Particle CLI
npm install -g particle-cli

# Login
particle login

# Ver dispositivos
particle list

# Compilar
particle compile photon sensor_dht11.c

# Cargar
particle flash DEVICE_ID sensor_dht11.c

# Monitorear puerto serial
particle serial monitor --port com3

# Verificar variables
particle variable get DEVICE_ID TEMP --auth TOKEN

# Ver logs
particle logs DEVICE_ID
```

### Con cURL
```bash
# Obtener temperatura
curl https://api.particle.io/v1/devices/DEVICE_ID/TEMP \
  -H "Authorization: Bearer TOKEN"

# Obtener humedad
curl https://api.particle.io/v1/devices/DEVICE_ID/HUM \
  -H "Authorization: Bearer TOKEN"

# Obtener token (sustituir credenciales)
curl -u usuario@email.com:password \
  https://api.particle.io/oauth/token \
  -d grant_type=password
```

---

## 10. Plantilla HTML Mínima

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>IoT Sensor</title>
    <script src="https://cdn.jsdelivr.net/npm/particle-api-js@10/dist/particle.min.js"></script>
</head>
<body>
    <h1>Temperatura: <span id="temp">???</span>°C</h1>
    <h1>Humedad: <span id="hum">???</span>%</h1>
    
    <input id="email" placeholder="Email">
    <input id="pass" type="password" placeholder="Contraseña">
    <input id="device" placeholder="Device ID">
    <button onclick="connect()">Conectar</button>

    <script>
        let particle = new Particle();
        let token, deviceId;

        function connect() {
            const email = document.getElementById('email').value;
            const pass = document.getElementById('pass').value;
            deviceId = document.getElementById('device').value;

            particle.login({username: email, password: pass})
                .then(data => {
                    token = data.body.access_token;
                    readData();
                })
                .catch(err => console.error(err));
        }

        function readData() {
            particle.getVariable({deviceId, name: 'TEMP', auth: token})
                .then(data => {
                    document.getElementById('temp').textContent = 
                        data.body.result.toFixed(2);
                });

            particle.getVariable({deviceId, name: 'HUM', auth: token})
                .then(data => {
                    document.getElementById('hum').textContent = 
                        data.body.result.toFixed(2);
                });
        }

        setInterval(readData, 60000);
    </script>
</body>
</html>
```

---

## 11. Preguntas Frecuentes (FAQ)

**P: ¿Puedo cambiar el pin del sensor?**  
R: Sí, cambiar `#define DHTPIN 2` a cualquier otro pin digital.

**P: ¿Cuáles son las librerías requeridas?**  
R: `adafruit/DHT` y `adafruit/Adafruit_Sensor`

**P: ¿Qué tan rápido actualiza la interfaz web?**  
R: Cada 60 segundos (configurable en `script.js`)

**P: ¿Puedo usar DHT22 en lugar de DHT11?**  
R: Sí, solo cambiar `#define DHTTYPE DHT11` a `DHT22`

**P: ¿Cuántos datos históricos guarda?**  
R: Máximo 24 puntos (configurable en `script.js`)

**P: ¿Funciona sin Internet?**  
R: No, Particle Cloud requiere conexión a Internet

**P: ¿Dónde guarda los datos históricos?**  
R: En `localStorage` del navegador (máximo 5-10MB)

**P: ¿Puedo ver los datos en mi teléfono?**  
R: Sí, usando la app de Particle o accediendo a la página web desde móvil

---

**Versión**: 1.0  
**Última actualización**: 2024  
**Mantenedor**: [Tu nombre]
