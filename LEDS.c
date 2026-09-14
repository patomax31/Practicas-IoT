SYSTEM_THREAD(ENABLED);

int pinFromName(String pinName);
int ledToggle(String command);
int outputPins[] = {D0, D1, D2, D3, D4, D5, D6, D7};

void setup() {
	Particle.function("led", ledToggle);

	for (int index = 0; index < 8; index++) {
		pinMode(outputPins[index], OUTPUT);
		digitalWrite(outputPins[index], LOW);
	}
}

void loop() {
}

int pinFromName(String pinName) {
	if (pinName == "D0") return D0;
	if (pinName == "D1") return D1;
	if (pinName == "D2") return D2;
	if (pinName == "D3") return D3;
	if (pinName == "D4") return D4;
	if (pinName == "D5") return D5;
	if (pinName == "D6") return D6;
	if (pinName == "D7") return D7;

	return -1;
}

int ledToggle(String command) {
	int separator = command.indexOf(':');

	if (separator == -1) {
		return -1;
	}

	int pin = pinFromName(command.substring(0, separator));
	String state = command.substring(separator + 1);

	if (pin == -1 || (state != "0" && state != "1")) {
		return -1;
	}

	digitalWrite(pin, state == "1" ? HIGH : LOW);
	return state == "1" ? 1 : 0;
}
