var particle = new Particle();
var token = '612cfbc634dc3cdb979b7cc7b88a14e6627e0507';
var deviceId = '29002b000b47313037363132';
var pins = ['D7', 'D6', 'D5', 'D4', 'D3', 'D2', 'D1', 'D0'];

document.addEventListener('DOMContentLoaded', function () {
  var pinsControls = document.getElementById('pins-controls');
  var breaker = document.getElementById('Breaker1');
  var breakerState = document.getElementById('state1');

  pins.forEach(function (pin) {
    pinsControls.innerHTML +=
      '<div>' +
      '<label for="' + pin + '">' + pin + '</label>' +
      '<select id="' + pin + '" data-pin="' + pin + '">' +
      '<option value="0">OFF</option>' +
      '<option value="1">ON</option>' +
      '</select>' +
      '</div>';
  });

  pinsControls.addEventListener('change', function (event) {
    if (event.target.tagName !== 'SELECT') return;

    sendLedState(event.target.dataset.pin, event.target.value);
  });

  breaker.addEventListener('input', function () {
    breakerState.value = this.value;
    sendLedState('D7', this.value);
  });
});

function sendLedState(pin, state) {
  if (!token) {
    console.error('Particle todavía no ha autenticado la sesión.');
    return;
  }

  particle.callFunction({
    deviceId: deviceId,
    name: 'led',
    argument: pin + ':' + (state === '1' ? '1' : '0'),
    auth: token
  }).then(function (response) {
    console.log(pin + ' actualizado:', response.body);
  }).catch(function (error) {
    console.error('No se pudo actualizar ' + pin, error);
  });
}

particle.login({
  username: 'dgarcia70@ucol.mx',
  password: '#Elmausegamer29'
}).then(function (data) {
  token = data.body.access_token;
}).catch(function (error) {
  console.error('No se pudo iniciar sesión en Particle.', error);
});
