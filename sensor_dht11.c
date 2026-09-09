// Actividad 3: Uso de función de nube Particle.variable
// Lectura de sensor DHT11 y envío de datos a la nube de Particle

SYSTEM_THREAD(ENABLED);

#define DHTPIN 2                    // Pin digital al que está conectado el sensor
#define DHTTYPE DHT11               // Tipo de sensor DHT

#include "adafruit/DHT/DHT.h"
DHT dht(DHTPIN, DHTTYPE);

double t;                           // Temperatura en °C
double f;                           // Temperatura en °F
double h;                           // Porcentaje de humedad
double hic;                         // Índice de calor

void setup() {
    Serial.begin(9600);
    dht.begin();
    
    // Exponer variables a la nube de Particle
    Particle.variable("TEMP", t);   // Se envía la temperatura del sensor
    Particle.variable("HUM", h);    // Se envía la humedad del sensor
}

void loop() {
    // Esperar algunos segundos entre mediciones
    // Las lecturas del sensor pueden tener hasta 2 segundos de retraso
    
    delay(2000);                    // Esperar 2 segundos
    
    // Leer humedad
    h = dht.readHumidity();
    
    // Leer temperatura en Celsius (por defecto)
    t = dht.readTemperature();
    
    // Leer temperatura en Fahrenheit (isFahrenheit = true)
    f = dht.readTemperature(true);
    
    // Calcular índice de calor
    hic = dht.computeHeatIndex(t, h, false);
    
    // Imprimir en puerto serial para debugging
    Serial.print("Humedad: ");
    Serial.print(h);
    Serial.print(" %  Temperatura: ");
    Serial.print(t);
    Serial.print(" °C  ");
    Serial.print(f);
    Serial.println(" °F");
}
