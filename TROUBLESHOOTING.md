# GUÍA DE SOLUCIÓN DE PROBLEMAS

## Problemas Comunes y Soluciones

### 1. El Dispositivo No Se Conecta a WiFi

#### Síntoma
La tarjeta Photon no aparece conectada en la consola de Particle.

#### Causas Posibles
- ❌ WiFi no está disponible
- ❌ Contraseña WiFi incorrecta
- ❌ Tarjeta Photon sin actualizar
- ❌ Problemas de hardware

#### Soluciones

**Paso 1**: Verificar conexión física
```
- Revisar que el LED RGB parpadea (indica búsqueda de WiFi)
- Si no parpadea, revisar conexión USB
```

**Paso 2**: Resetear la tarjeta
```cpp
// En código:
System.reset();

// O manualmente:
1. Desconectar USB
2. Esperar 5 segundos
3. Reconectar USB
4. Esperar a que se reinicie (LED parpadeará)
```

**Paso 3**: Re-configurar WiFi
```
1. Abrir Particle CLI
2. Ejecutar: particle serial wifi
3. Seleccionar red
4. Ingresar contraseña
5. Esperar confirmación
```

**Paso 4**: Actualizar firmware
```bash
particle update
```

---

### 2. El Sensor DHT11 No Lee Datos

#### Síntoma
```
Serial output:
Humedad: NaN %  Temperatura: NaN °C  NaN °F
```

#### Causas Posibles
- ❌ Conexión física defectuosa
- ❌ Pin incorrecto en el código
- ❌ Librería DHT no instalada
- ❌ Sensor DHT11 dañado

#### Soluciones

**Paso 1**: Verificar conexión física
```
Sensor DHT11          Pin Photon
VCC ---------> 3V3
GND ---------> GND
DATA --------> Pin 2 (o el especificado)

✓ Revisar que no hay soldaduras frías
✓ Verificar que los cables no están sueltos
```

**Paso 2**: Verificar pin en código
```cpp
#define DHTPIN 2  // Cambiar si es necesario
#define DHTTYPE DHT11

// Probar con un LED en el mismo pin primero:
pinMode(DHTPIN, OUTPUT);
digitalWrite(DHTPIN, HIGH);
delay(1000);
digitalWrite(DHTPIN, LOW);
```

**Paso 3**: Instalar librería correctamente
```
1. En Particle IDE: Libraries
2. Buscar: adafruit/DHT
3. Click en "Include in project"
4. Seleccionar dispositivo
5. Esperar a que se agregue
```

**Paso 4**: Probar sensor con código simple
```cpp
void setup() {
    Serial.begin(9600);
    dht.begin();
    delay(2000);
}

void loop() {
    float temp = dht.readTemperature();
    float hum = dht.readHumidity();
    
    Serial.printlnf("T: %f, H: %f", temp, hum);
    delay(2000);
}
```

**Paso 5**: Reemplazar sensor
```
Si todas las pruebas fallan, el sensor puede estar dañado
Solicitar sensor de repuesto
```

---

### 3. Particle.variable() No Aparece en la Nube

#### Síntoma
Variables no visibles en consola Particle o en la aplicación web.

#### Causas Posibles
- ❌ Variable declarada localmente (dentro de función)
- ❌ Nombre de variable > 12 caracteres
- ❌ Setup() no ejecutó correctamente
- ❌ Código compiló pero no subió

#### Soluciones

**Paso 1**: Verificar que la variable es global
```cpp
// ❌ INCORRECTO - Variables locales
void setup() {
    double temperatura = 0;
    Particle.variable("TEMP", temperatura);  // No funcionará
}

// ✓ CORRECTO - Variables globales
double temperatura = 0;  // Fuera de cualquier función

void setup() {
    Particle.variable("TEMP", temperatura);  // Funcionará
}
```

**Paso 2**: Verificar nombres de variables
```cpp
// ❌ INCORRECTO - Nombre muy largo
Particle.variable("TEMPERATURA_CELSIUS", temp);  // > 12 caracteres

// ✓ CORRECTO - Nombre corto
Particle.variable("TEMP", temp);  // 4 caracteres
```

**Paso 3**: Verificar en consola
```bash
$ particle cli
$ particle call DEVICE_ID digitalread 7

# O en consola web:
1. Ir a https://build.particle.io
2. Seleccionar dispositivo
3. Hacer scroll a "Variables"
4. Debe aparecer: TEMP y HUM
```

**Paso 4**: Forzar re-compilación
```
1. En Particle IDE, hacer click en "Reset"
2. Compilar y cargar nuevamente
3. Esperar a que se complete (LED parpadea amarillo → verde)
```

---

### 4. Interfaz Web No Se Conecta

#### Síntoma
```
Error: "Conectando..." nunca termina
Console error: CORS, 401, 403, etc.
```

#### Causas Posibles
- ❌ Credenciales de Particle incorrectas
- ❌ Token expirado
- ❌ ID de dispositivo incorrecto
- ❌ Errores CORS

#### Soluciones

**Paso 1**: Verificar credenciales
```javascript
// En la consola del navegador (F12):
// Copiar estos valores del formulario
console.log("Email:", document.getElementById('username').value);
console.log("Dispositivo:", document.getElementById('deviceId_input').value);

// Verificar en https://build.particle.io
// Los mismos valores deben ser válidos allí
```

**Paso 2**: Verificar ID del dispositivo
```bash
# En terminal, con Particle CLI instalado:
particle list

# Debe mostrar algo como:
# Photon [25001d000847313037363132] (online)

# Copiar exactamente ese ID en la aplicación web
```

**Paso 3**: Limpiar almacenamiento
```javascript
// En consola del navegador:
localStorage.clear();
location.reload();

// Volver a ingresar credenciales manualmente
```

**Paso 4**: Habilitar modo de depuración
```javascript
// Agregar en script.js:
particle.login({ username: email, password: password })
    .then(data => {
        console.log("Login exitoso:", data);  // Ver respuesta
    })
    .catch(err => {
        console.error("Login fallido:", err);  // Ver error
        console.error("Status:", err.statusCode);
        console.error("Mensaje:", err.body.error_description);
    });
```

**Paso 5**: Verificar CORS
```
Si error menciona CORS:
1. Usar un servidor web en lugar de archivo local
2. O instalar extensión CORS en navegador (solo para desarrollo)
3. O usar proxy CORS: https://cors-anywhere.herokuapp.com/
```

---

### 5. Gráficas No Se Actualizan

#### Síntoma
Las gráficas de Chart.js no muestran datos o no se actualizan.

#### Causas Posibles
- ❌ Datos no se están recibiendo
- ❌ Gráfica no inicializada
- ❌ Error en JavaScript

#### Soluciones

**Paso 1**: Verificar en consola del navegador (F12)
```javascript
// Escribir en consola:
console.log("Datos de temperatura:", temperatureData);
console.log("Datos de humedad:", humidityData);
console.log("Etiquetas:", timeLabels);

// Debe mostrar arrays con datos si está funcionando
```

**Paso 2**: Verificar que las gráficas se inicializan
```javascript
// En consola:
console.log("tempChart:", tempChart);
console.log("humChart:", humChart);

// Deben mostrar objetos de Chart, no null o undefined
```

**Paso 3**: Forzar actualización manual
```javascript
// En consola:
if (tempChart) {
    tempChart.update();
}
if (humChart) {
    humChart.update();
}
```

**Paso 4**: Verificar que getDeviceVariables() se ejecuta
```javascript
// Agregar en script.js después de línea 100:
function getDeviceVariables() {
    console.log("Intentando obtener variables...");
    if (!isConnected || !token || !deviceId) {
        console.warn("No conectado:", { isConnected, token, deviceId });
        return;
    }
    // ... resto del código
}
```

**Paso 5**: Recrear las gráficas
```javascript
// Si las gráficas están corruptas:
if (tempChart) tempChart.destroy();
if (humChart) humChart.destroy();

initializeCharts();
```

---

### 6. Error "401 - Unauthorized"

#### Síntoma
```
Error en consola: "401 Unauthorized"
Mensaje en interfaz: "No se pudo conectar a Particle Cloud"
```

#### Causas
- ❌ Token expirado
- ❌ Contraseña incorrecta
- ❌ Cuenta Particle inactiva

#### Soluciones

```javascript
// Método 1: Limpiar y reintentar
localStorage.removeItem('token');
location.reload();

// Método 2: Verificar contraseña
// Ir a https://id.particle.io
// Cambiar contraseña si es necesario

// Método 3: Verificar estado de cuenta
// Ir a https://dashboard.particle.io
// Confirmar que dispositivo está registrado
```

---

### 7. Sensor Lee Valores Extraños

#### Síntomas
- Temperatura 255°C
- Humedad 999%
- Valores que varían drásticamente

#### Causas
- ❌ Conexión defectuosa
- ❌ Interferencia electromagnética
- ❌ Sensor DHT11 lento

#### Soluciones

```cpp
// Filtrar valores anómalos:
double temp = dht.readTemperature();

if (isnan(temp)) {
    Serial.println("Lectura inválida");
    return;
}

if (temp < 0 || temp > 50) {
    Serial.println("Valor fuera de rango");
    return;
}

// Procesar valor válido
t = temp;
```

---

### 8. Interfaz Web Lenta o Se Congela

#### Síntomas
- Aplicación responde lentamente
- Gráficas se dibujan lentamente
- Navegador se congela

#### Causas
- ❌ Muchos datos históricos (> 1000 puntos)
- ❌ Gráficas con muchas redibujadas
- ❌ JavaScript ineficiente

#### Soluciones

```javascript
// Reducir puntos de datos:
const MAX_DATA_POINTS = 24;  // Cambiar a 12 o 10

// Actualizar gráficas menos frecuentemente:
setInterval(function() {
    // ...
}, 120000);  // Cambiar de 60000 a 120000 (2 minutos)

// Usar requestAnimationFrame en lugar de setInterval:
function updateCharts() {
    if (tempChart) tempChart.update();
    if (humChart) humChart.update();
    requestAnimationFrame(updateCharts);
}
```

---

## Checklist de Debugging

```
☐ Verificar conexión USB
☐ Verificar LED RGB en Photon
☐ Verificar conexión WiFi
☐ Verificar puerto serial (9600 baud)
☐ Verificar que DHT11 está conectado a pin 2
☐ Verificar que librería DHT está instalada
☐ Verificar que variables son globales
☐ Verificar que Particle.variable() está en setup()
☐ Compilar y cargar código nuevamente
☐ Esperar a que LED se ponga verde
☐ Verificar en consola Particle que variables aparecen
☐ Verificar credenciales de Particle
☐ Verificar ID de dispositivo
☐ Abrir consola del navegador (F12)
☐ Revisar errores de consola
☐ Revisar errores de red
☐ Probar con curl o Postman
☐ Limpiar caché del navegador
☐ Intentar en otro navegador
☐ Reiniciar todo (dispositivo + navegador)
```

---

## Recursos de Ayuda

### Documentación Oficial
- [Particle Docs](https://docs.particle.io/)
- [DHT Librería](https://github.com/adafruit/DHT-sensor-library)
- [Particle CLI](https://docs.particle.io/tutorials/developer-tools/cli/)

### Foros y Comunidades
- [Particle Community](https://community.particle.io/)
- [Stack Overflow - Arduino Tag](https://stackoverflow.com/questions/tagged/arduino)
- [Arduino Forum](https://forum.arduino.cc/)

### Herramientas Útiles
- **Particle CLI**: `npm install -g particle-cli`
- **Arduino IDE**: Para debugging adicional
- **CURL**: Para probar API: `curl https://api.particle.io/v1/devices/{deviceId}/TEMP`
- **Postman**: Para testing de API

---

## Cómo Reportar un Problema

Si después de todas las soluciones el problema persiste:

### Información a incluir

1. **Descripción del problema**
   ```
   Ejemplo: "El sensor DHT11 siempre lee NaN"
   ```

2. **Código relevante**
   ```cpp
   // Pegar el código que causa el problema
   ```

3. **Mensajes de error exactos**
   ```
   Copiar y pegar texto completo del error
   ```

4. **Pasos para reproducir**
   ```
   1. Conectar sensor a pin 2
   2. Cargar código
   3. Abrir puerto serial
   4. Resultado: error
   ```

5. **Información de hardware**
   ```
   - Modelo: Photon / Argon / etc
   - Sensor: DHT11 / DHT22
   - Versión firmware: [ver con particle serial inspect]
   ```

6. **Capturas de pantalla o logs**
   ```
   Adjuntar salida de consola, puerto serial, etc
   ```

---

*Última actualización: 2024*
*Versión: 1.0*
