# Actividad 3: IoT - Monitoreo de Temperatura y Humedad con Particle Cloud

## Descripción General

Este proyecto implementa un sistema de monitoreo en tiempo real de temperatura y humedad utilizando:
- **Hardware**: Tarjeta Photon de Particle + Sensor DHT11
- **Backend**: Particle Cloud (IoT Platform)
- **Frontend**: Interfaz web interactiva con gráficas en tiempo real

## Archivos del Proyecto

### 1. `sensor_dht11.c` - Código del Dispositivo Photon

**Descripción**: Código en C/C++ que se carga en la tarjeta Photon. Lee los datos del sensor DHT11 y los expone a través de la nube de Particle.

**Características principales**:
- Lectura de temperatura en Celsius y Fahrenheit
- Lectura de humedad relativa
- Cálculo del índice de calor
- Exposición de variables a la nube mediante `Particle.variable()`
- Salida en puerto serial para debugging

**Variables expuestas**:
- `TEMP`: Temperatura en grados Celsius
- `HUM`: Humedad en porcentaje (%)

**Configuración del hardware**:
```
Sensor DHT11 → Pin 2 (DHTPIN) de la tarjeta Photon
```

**Librerías requeridas** (instalar desde Particle IDE):
- `adafruit/DHT`
- `adafruit/Adafruit_Sensor`

### 2. `index.html` - Interfaz Web Interactiva

**Descripción**: Página HTML principal que muestra la interfaz de usuario para visualizar los datos del sensor en tiempo real.

**Componentes**:
- **Header**: Título y descripción del sistema
- **Estado de Conexión**: Indicador visual del estado de conexión
- **Tarjetas de Sensores**: Visualización actual de temperatura y humedad
- **Gráficas Interactivas**: Histórico de las últimas 24 mediciones
- **Información del Dispositivo**: Detalles y última actualización
- **Panel de Credenciales**: Formulario para conectarse a Particle Cloud

**Requisitos**:
- Conexión a Internet para acceder a Particle Cloud
- Navegador web moderno (Chrome, Firefox, Safari, Edge)
- Cuenta de Particle con dispositivo registrado

### 3. `styles.css` - Estilos y Diseño

**Descripción**: Archivo CSS que proporciona el diseño visual y la experiencia de usuario.

**Características**:
- Diseño responsivo (funciona en desktop, tablet y móvil)
- Gradientes modernos y animaciones suaves
- Temas de color personalizados para cada sensor
- Indicadores visuales de estado
- Sombras y efectos de profundidad

**Variables CSS personalizables**:
```css
--primary-color: #0066cc
--secondary-color: #ff6b6b (Temperatura)
--accent-color: #4ecdc4 (Humedad)
```

### 4. `script.js` - Lógica de Funcionamiento

**Descripción**: Archivo JavaScript que maneja toda la interacción con la API de Particle y la actualización dinámica de la interfaz.

**Funciones principales**:

- **`connectToParticle()`**: Autentica con la API de Particle usando las credenciales proporcionadas

- **`getDeviceVariables()`**: Obtiene los valores actuales de temperatura y humedad desde el dispositivo

- **`startReadingData()`**: Inicia el ciclo automático de lectura (cada 60 segundos)

- **`updateTemperatureDisplay(temp)`**: Actualiza el valor mostrado de temperatura

- **`updateHumidityDisplay(hum)`**: Actualiza el valor mostrado de humedad

- **`addTemperatureDataPoint(temp)`**: Añade un punto a la gráfica de temperatura

- **`addHumidityDataPoint(hum)`**: Añade un punto a la gráfica de humedad

- **`initializeCharts()`**: Crea las gráficas usando Chart.js

- **`exportDataToCSV()`**: Exporta los datos históricos en formato CSV

- **`clearData()`**: Limpia el histórico de datos

## Instrucciones de Uso

### Paso 1: Preparar el Hardware

1. Conectar el sensor DHT11 a la tarjeta Photon:
   - VCC → 3V3
   - GND → GND
   - DATA → Pin 2

2. Conectar la tarjeta Photon a internet (WiFi o USB)

### Paso 2: Cargar el Código en el Dispositivo

1. Abrir [Particle IDE](https://build.particle.io)
2. Crear un nuevo proyecto
3. Copiar el contenido de `sensor_dht11.c`
4. Instalar las librerías necesarias:
   - `adafruit/DHT`
   - `adafruit/Adafruit_Sensor`
5. Compilar y descargar el código en la tarjeta Photon
6. Verificar en la consola serial que los datos se están leyendo correctamente

### Paso 3: Acceder a la Interfaz Web

1. Guardar los archivos HTML, CSS y JS en un servidor web o carpeta local
2. Abrir `index.html` en un navegador web
3. Ingresar las credenciales:
   - **Email**: Tu email de Particle
   - **Contraseña**: Tu contraseña de Particle
   - **ID del dispositivo**: El ID de tu tarjeta Photon (disponible en la consola de Particle)
4. Hacer clic en "Conectar"

### Paso 4: Monitorear en Tiempo Real

- Los datos se actualizan automáticamente cada 60 segundos
- Las gráficas muestran el histórico de las últimas 24 lecturas
- El timestamp se actualiza en "Última actualización"

## Características de la Función `Particle.variable()`

### ¿Qué hace?

La función `Particle.variable()` expone variables de un dispositivo a la nube de Particle, permitiendo leerlas remotamente desde cualquier aplicación conectada.

### Sintaxis

```cpp
Particle.variable("nombre_variable", variable);
```

- **nombre_variable**: Nombre con el que será accesible desde la nube (máximo 12 caracteres)
- **variable**: Referencia a una variable global (puede ser `int`, `float`, `double`, `bool`, `String` o `char[]`)

### Características importantes

✅ Solo admite **variables globales** (no locales dentro de funciones)
✅ Solo **lectura desde la nube** (no se pueden modificar remotamente)
✅ Límite de **20 variables publicadas por dispositivo**
✅ La lectura es muy rápida (~100ms)
✅ Ideal para monitoreo de sensores en tiempo real

## Datos Técnicos

### Sensor DHT11

- **Rango de temperatura**: 0°C a 50°C
- **Precisión de temperatura**: ±2°C
- **Rango de humedad**: 20% a 90%
- **Precisión de humedad**: ±5%
- **Tiempo de respuesta**: ~2 segundos

### Frecuencia de Lectura

- Lecturas cada **2 segundos** en el dispositivo
- Actualizaciones de la interfaz web cada **60 segundos**
- Máximo de **24 puntos** de datos en las gráficas

## Documentación del Código

### `sensor_dht11.c` - Explicación línea por línea

```cpp
SYSTEM_THREAD(ENABLED);
// Habilita threading del sistema para mejor rendimiento

#define DHTPIN 2
// Define el pin donde está conectado el sensor

#define DHTTYPE DHT11
// Especifica el tipo de sensor DHT

DHT dht(DHTPIN, DHTTYPE);
// Crea una instancia del sensor con los parámetros definidos

double t;
// Variable para almacenar la temperatura (debe ser global)

double h;
// Variable para almacenar la humedad (debe ser global)

void setup() {
    // Se ejecuta una sola vez al iniciar
    Serial.begin(9600);
    // Inicia la comunicación serial a 9600 baud
    
    dht.begin();
    // Inicializa el sensor
    
    Particle.variable("TEMP", t);
    // Expone la variable de temperatura a la nube
    
    Particle.variable("HUM", h);
    // Expone la variable de humedad a la nube
}

void loop() {
    // Se ejecuta continuamente
    delay(2000);
    // Espera 2 segundos entre mediciones
    
    h = dht.readHumidity();
    // Lee la humedad del sensor
    
    t = dht.readTemperature();
    // Lee la temperatura en Celsius
    
    f = dht.readTemperature(true);
    // Lee la temperatura en Fahrenheit
    
    hic = dht.computeHeatIndex(t, h, false);
    // Calcula el índice de calor
}
```

### `script.js` - Funciones clave

```javascript
// Conectar a Particle Cloud
particle.login({ username: email, password: contraseña })
    .then(data => {
        token = data.body.access_token;
        // Token de autenticación
    })

// Obtener valor de una variable
particle.getVariable({
    deviceId: deviceId,
    name: 'TEMP',
    auth: token
}).then(data => {
    console.log(data.body.result); // Valor actual
})

// Actualizar gráficas automáticamente
setInterval(() => {
    getDeviceVariables();
}, 60000); // Cada 60 segundos
```

## Solución de Problemas

### El dispositivo no se conecta

- Verificar que la tarjeta Photon está conectada a WiFi
- Confirmar que la cuenta de Particle es la correcta
- Revisar la consola de Particle para errores

### Las gráficas no se actualizan

- Verificar la conexión a internet
- Revisar la consola del navegador (F12) para errores JavaScript
- Confirmar que el ID del dispositivo es correcto

### Errores en la compilación

- Asegurar que las librerías DHT están instaladas
- Verificar que el código está en sintaxis C/C++ correcta
- Revisar que no hay caracteres especiales

### El sensor no lee datos

- Verificar la conexión física del sensor
- Probar con un cable diferente
- Revisar que el pin 2 no está siendo usado por otra función
- Comprobar la polaridad del sensor

## Requisitos Técnicos

### Hardware

- Tarjeta Particle Photon
- Sensor DHT11 o DHT22
- Resistencia de 4.7kΩ (opcional, algunos sensores ya la incluyen)
- Cable USB o fuente de alimentación
- Cables de conexión

### Software

- Cuenta en [Particle Cloud](https://build.particle.io)
- Navegador web moderno
- Conexión a Internet

### Librerías JavaScript (incluidas en el HTML)

- Particle API JS (v10)
- jQuery
- Chart.js
- Font Awesome (iconos)

## Mejoras Posibles

1. **Base de datos**: Almacenar datos históricos en una base de datos backend
2. **Alertas**: Notificaciones cuando temperatura o humedad superen límites
3. **Múltiples dispositivos**: Monitorear varios Photons simultáneamente
4. **Análisis**: Estadísticas como máximo, mínimo, promedio
5. **Exportación avanzada**: Generar reportes en PDF o Excel
6. **Autenticación**: Panel de usuarios para acceso seguro
7. **Control remoto**: Permitir controlar un relé u otros componentes

## Referencias

- [Documentación de Particle](https://docs.particle.io/)
- [API de Particle Cloud](https://docs.particle.io/reference/cloud-apis/api/)
- [Librería DHT de Adafruit](https://github.com/adafruit/DHT-sensor-library)
- [Chart.js Documentation](https://www.chartjs.org/)

## Conclusiones

Este proyecto demuestra:
- ✅ Uso efectivo de sensores analógicos con microcontroladores
- ✅ Integración de dispositivos IoT con plataformas en la nube
- ✅ Desarrollo de interfaces web responsivas e interactivas
- ✅ Monitoreo remoto de variables en tiempo real
- ✅ Almacenamiento y visualización de datos históricos
- ✅ Buenas prácticas en programación embebida y web

---

**Autor**: Estudiante de Ingeniería  
**Fecha**: 2024  
**Materia**: IoT y Sistemas Embebidos  
**Institución**: Universidad de Colima
