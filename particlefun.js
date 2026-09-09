const deviceId = "29002b000b47313037363132";
const accessToken = "f8da603438014602d7aa230e47fe738b8770c092";

async function controlarLed(comando) {
const url = `https://api.particle.io/v1/devices/${deviceId}/led`;

const response = await fetch(url, {
method: "POST",
headers: { "Content-Type": "application/x-www-form-urlencoded" },
body: `access_token=${accessToken}&amp;arg=${comando}`,
});

const data = await response.json();
console.log("Respuesta:", data.return_value);
}

controlarLed("off");