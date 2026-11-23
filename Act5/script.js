var particle = new Particle();
var token = "612cfbc634dc3cdb979b7cc7b88a14e6627e0507";

var temperatureSlider = document.getElementById("Ktemp");
var temperatureOutput = document.getElementById("KvaluesTemp");
var currentTemperature = document.getElementById("ShowTemp");
var currentStatus = document.getElementById("ShowStatus");
var deviceId = "29002b000b47313037363132";

function updateCurrentTemperature() {
  particle.getVariable({
    deviceId: deviceId,
    name: "temperatura",
    auth: token,
  })
    .then(function (data) {
      var temperature = Number(data.body.result);

      if (Number.isFinite(temperature)) {
        currentTemperature.textContent = temperature.toFixed(1) + " °C";
      }
    })
    .catch(function (error) {
      console.error("Error al obtener la temperatura:", error);
    });

  particle.getVariable({
    deviceId: deviceId,
    name: "estado",
    auth: token,
  })
    .then(function (data) {
      currentStatus.textContent = String(data.body.result);
    })
    .catch(function (error) {
      console.error("Error al obtener el estado:", error);
    });
}

temperatureSlider.oninput = function () {
  temperatureOutput.textContent = this.value + " °C";
  var limiteTemperatura = this.value;
  particle.callFunction({
    deviceId: deviceId,
    name: "DHT_led",
    argument: limiteTemperatura,
    auth: token,
  });
};

temperatureOutput.textContent = temperatureSlider.value + " °C";
updateCurrentTemperature();
setInterval(updateCurrentTemperature, 1000);
