#include <DHT.h>
#include <Adafruit_Sensor.h>

SYSTEM_THREAD(ENABLED);

#define DHTPIN 2 // what digital pin we&#39;re connected to
#define DHTTYPE DHT11

DHT dht(DHTPIN, DHTTYPE);
double t; // temperatura en °C
double f; // temperatura en °F
double h; // porcentaje de hunedad
double hic; //indicce calor
int led1 = D7; // This one is the little blue LED on your board. On the Photon it is next to D7, and on the Core it is next to the USB jack.

void setup() {
pinMode(led1, OUTPUT);

Serial.begin(9600);
dht.begin();

}

//}
void loop() {
// Wait a few seconds between measurements.

delay(5000); // se interroga al sensor cada 5 segundos

// Sensor readings may also be up to 2 seconds &#39;old&#39; (its a very slow sensor)
h = dht.readHumidity();
// Read temperature as Celsius (the default)
t = dht.readTemperature();
// Read temperature as Fahrenheit (isFahrenheit = true)
f = dht.readTemperature(true);

hic = dht.computeHeatIndex(t, h, false);

// se verifica que la temperatura ambiente
if (t>20) { // si es mayor enciende el dispositivo de control
    digitalWrite(led1, HIGH);
    }
else { // en caso contrario se apaga el dispositivo de control
    digitalWrite(led1, LOW);
    }
String TEMP = String(t); // se convierte a cadena de caracteres el valor numérico del valor t

String H = String(h); // se convierte a cadena de caracteres el valor numérico del valor h

Particle.publish("Temp °C",TEMP, PRIVATE); // se publica el valor en la plataforma de Particle con el nombre de “TEMP, puede ser con el nombre que ud desee

}