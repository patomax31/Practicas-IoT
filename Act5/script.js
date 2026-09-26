var particle = new Particle();
var token = '612cfbc634dc3cdb979b7cc7b88a14e6627e0507';

var temperatureSlider = document.getElementById('Ktemp');
var temperatureOutput = document.getElementById('KvaluesTemp');
var currentTemperature = document.getElementById('ShowTemp');

temperatureSlider.oninput = function() {
  temperatureOutput.textContent = this.value + ' °C';
  currentTemperature.textContent = this.value + ' °C';
  var limiteTemperatura = this.value;
  particle.callFunction({
    deviceId: '29002b000b47313037363132',
    name: 'DHT_led',
    argument: limiteTemperatura,
    auth: token
  });
 }
temperatureSlider.oninput();
