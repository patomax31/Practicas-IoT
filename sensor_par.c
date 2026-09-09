SYSTEM_THREAD(ENABLED);
#include <DHT.h>

#define DHTPIN 2       // Pin digital conectado al DHT11
#define DHTTYPE DHT11

DHT dht(DHTPIN, DHTTYPE);

double t;   // Temperatura en °C
double f;   // Temperatura en °F
double h;   // Porcentaje de humedad
double hic; // Índice de calor

void setup() {
Serial.begin(9600);
dht.begin();
// Mensaje de inicio
Serial.println(&quot;====================================&quot;);
Serial.println(&quot; SISTEMA DE MONITOREO DHT11&quot;);
Serial.println(&quot;====================================&quot;);
Serial.println(&quot;Sensor DHT11 inicializado.&quot;);
Serial.println(&quot;Esperando lecturas...&quot;);
Serial.println();
}
void loop() {
// Esperar 5 segundos entre mediciones
delay(5000);
Serial.println(&quot;------------------------------------&quot;);
Serial.println(&quot;Realizando nueva medicion...&quot;);
// Leer humedad
h = dht.readHumidity();
// Leer temperatura en Celsius
t = dht.readTemperature();
// Leer temperatura en Fahrenheit
f = dht.readTemperature(true);
// Verificar si la lectura fue correcta

if (isnan(h) || isnan(t) || isnan(f)) {
Serial.println(&quot;ERROR: No se pudo leer el sensor DHT11.&quot;);
Serial.println(&quot;------------------------------------&quot;);
return;
}
// Calcular índice de calor
hic = dht.computeHeatIndex(t, h, false);
// Mostrar temperatura en Celsius
Serial.print(&quot;Temperatura: &quot;);
Serial.print(t);
Serial.println(&quot; °C&quot;);
// Mostrar temperatura en Fahrenheit
Serial.print(&quot;Temperatura: &quot;);
Serial.print(f);
Serial.println(&quot; °F&quot;);
// Mostrar humedad
Serial.print(&quot;Humedad: &quot;);
Serial.print(h);
Serial.println(&quot; %&quot;);
// Mostrar índice de calor
Serial.print(&quot;Indice de calor: &quot;);
Serial.print(hic);
Serial.println(&quot; °C&quot;);

}

// Convertir valores a String para Particle
String TEMP = String(t); String H = String(h); // Publicar temperatura en Particle Cloud
Particle.publish(&quot;Temp °C&quot;, TEMP, PRIVATE); // Publicar humedad en Particle Cloud
Particle.publish(&quot;% Humedad&quot;, H, PRIVATE);
Serial.println(&quot;Datos publicados en Particle Cloud.&quot;); Serial.println(&quot;-------------------------------
-----&quot;);
Serial.println();