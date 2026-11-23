// This #include statement was automatically added by the Particle IDE.
#include <Adafruit_Sensor.h>


// This #include statement was automatically added by the Particle IDE.
#include <DHT.h>

// This #include statement was automatically added by the Particle IDE.
#include <Ubidots.h>

SYSTEM_THREAD(ENABLED); // for running with/without internet connection

#ifndef TOKEN
#define TOKEN "BBUS-CrxB20qrNWQmWntN3zUGjxK6cmd6ul" // Token de Victor

Ubidots ubidots(TOKEN, UBI_TCP);
#include "math.h"; // libreria para funciones matematicas
#include "stdio.h"; // libreria estándar de entradas salidas

SYSTEM_THREAD(ENABLED);

#define DHTPIN 2 // what digital pin we&#39;re connected to
#define DHTTYPE DHT11

DHT dht(DHTPIN, DHTTYPE);
double t; // temperatura en °C
double f; // temperatura en °F
double h; // porcentaje de hunedad
double hic; //indicce calor
void setup() {
Serial.begin(9600);
dht.begin();
}

//}
void loop() {
// Wait a few seconds between measurements.
delay(10000); // se interroga al sensor cada 10 segundos
// Sensor readings may also be up to 2 seconds &#39;old&#39; (its a very slow sensor)
h = dht.readHumidity();
// Read temperature as Celsius (the default)
t = dht.readTemperature();
// Read temperature as Fahrenheit (isFahrenheit = true)
f = dht.readTemperature(true);

hic = dht.computeHeatIndex(t, h, false);
ubidots.add("TEMP", t);

bool bufferSent = false;
bufferSent = ubidots.send();
}