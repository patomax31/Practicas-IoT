var particle = new Particle();
var token = '612cfbc634dc3cdb979b7cc7b88a14e6627e0507';
var deviceId = '29002b000b47313037363132';
var pins = ['D7', 'D6', 'D5', 'D4', 'D3', 'D2', 'D1', 'D0'];

document.addEventListener('DOMContentLoaded', function () {
  var pinsControls = document.getElementById('pins-controls');

  pins.forEach(function (pin) {
    pinsControls.innerHTML +=
      '<div>' +
      '<label for="' + pin + '">' + pin + '</label>' +
      '<input type="range" id="' + pin + '" class="pin-slider"' +
      ' data-pin="' + pin + '" min="0" max="1" step="1" value="0"' +
      ' aria-label="Controlar ' + pin + '">' +
      '</div>';
  });

  pinsControls.addEventListener('input', function (event) {
    if (event.target.tagName !== 'INPUT') return;

    sendLedState(event.target.dataset.pin, event.target.value);
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
