var particle = new Particle();
var token;
particle.login({username: 'crosas6@ucol.mx', password: 'Leonard01234'}).then(
  function(data) {
     token = data.body.access_token;
  },
  function (err) {
    console.log('Could not log in.', err);
  }
);
setInterval(function() {
var breaker1 = document.getElementById('Breaker1');
breaker1.oninput = function() {
    var output = document.getElementById('state1');
    output.innerHTML = this.value;
  var Salida1=this.value;//
  particle.callFunction({ deviceId: '29002b000b47313037363132', name: 'led', argument: Salida1, auth: "f8da603438014602d7aa230e47fe738b8770c092", });
}
  },1000)
