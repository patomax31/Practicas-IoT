// ============================================
// Actividad 3: IoT - Monitoreo en Tiempo Real
// Lectura de sensores desde Particle Cloud
// ============================================

// Variables globales
let particle = new Particle();
let token = null;
let deviceId = null;
let isConnected = false;

// Variables para almacenar datos históricos
let temperatureData = [];
let humidityData = [];
let timeLabels = [];
const MAX_DATA_POINTS = 24;

// Gráficas
let tempChart = null;
let humChart = null;

/**
 * Conectar a Particle Cloud con las credenciales proporcionadas
 */
function connectToParticle() {
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const deviceIdInput = document.getElementById('deviceId_input').value;

    if (!username || !password || !deviceIdInput) {
        alert('Por favor, complete todos los campos de credenciales');
        return;
    }

    updateStatus('Conectando...', 'connecting');

    // Realizar login en Particle
    particle.login({ username: username, password: password })
        .then(function (data) {
            token = data.body.access_token;
            deviceId = deviceIdInput;
            isConnected = true;

            document.getElementById('deviceId').textContent = deviceId;
            updateStatus('Conectado', 'connected');

            // Guardar credenciales en localStorage (solo el dispositivo, no las credenciales reales)
            localStorage.setItem('deviceId', deviceId);

            // Iniciar lectura de datos
            startReadingData();

            // Inicializar gráficas
            initializeCharts();
        })
        .catch(function (err) {
            console.error('Error de conexión:', err);
            updateStatus('Error de conexión', 'error');
            alert('No se pudo conectar a Particle Cloud. Verifique las credenciales.');
            isConnected = false;
        });
}

/**
 * Actualizar estado visual de conexión
 */
function updateStatus(message, status) {
    const statusIndicator = document.getElementById('statusIndicator');
    statusIndicator.innerHTML = '<i class="fa fa-circle"></i> ' + message;
    statusIndicator.className = 'status-indicator ' + status;
}

/**
 * Obtener datos de las variables del dispositivo
 */
function getDeviceVariables() {
    if (!isConnected || !token || !deviceId) {
        return;
    }

    // Obtener temperatura
    particle.getVariable({ deviceId: deviceId, name: 'TEMP', auth: token })
        .then(function (data) {
            console.log('Temperatura obtenida:', data);
            const temp = parseFloat(data.body.result);
            updateTemperatureDisplay(temp);
            addTemperatureDataPoint(temp);
        })
        .catch(function (err) {
            console.error('Error al obtener temperatura:', err);
        });

    // Obtener humedad
    particle.getVariable({ deviceId: deviceId, name: 'HUM', auth: token })
        .then(function (data) {
            console.log('Humedad obtenida:', data);
            const hum = parseFloat(data.body.result);
            updateHumidityDisplay(hum);
            addHumidityDataPoint(hum);
        })
        .catch(function (err) {
            console.error('Error al obtener humedad:', err);
        });

    // Actualizar timestamp
    document.getElementById('lastUpdate').textContent = new Date().toLocaleTimeString('es-ES');
}

/**
 * Actualizar la pantalla de temperatura
 */
function updateTemperatureDisplay(temp) {
    const tempElement = document.getElementById('Temperatura');
    const formattedTemp = parseFloat(temp).toFixed(2);
    tempElement.textContent = formattedTemp;
}

/**
 * Actualizar la pantalla de humedad
 */
function updateHumidityDisplay(hum) {
    const humElement = document.getElementById('Humedad');
    const formattedHum = parseFloat(hum).toFixed(2);
    humElement.textContent = formattedHum;
}

/**
 * Agregar punto de datos de temperatura al histórico
 */
function addTemperatureDataPoint(temp) {
    const time = new Date().toLocaleTimeString('es-ES', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });

    temperatureData.push(parseFloat(temp).toFixed(2));
    timeLabels.push(time);

    // Limitar el número de puntos de datos
    if (temperatureData.length > MAX_DATA_POINTS) {
        temperatureData.shift();
        timeLabels.shift();
    }

    // Actualizar gráfica
    if (tempChart) {
        tempChart.data.labels = timeLabels;
        tempChart.data.datasets[0].data = temperatureData;
        tempChart.update();
    }
}

/**
 * Agregar punto de datos de humedad al histórico
 */
function addHumidityDataPoint(hum) {
    humidityData.push(parseFloat(hum).toFixed(2));

    // Limitar el número de puntos de datos
    if (humidityData.length > MAX_DATA_POINTS) {
        humidityData.shift();
    }

    // Actualizar gráfica
    if (humChart) {
        humChart.data.labels = timeLabels;
        humChart.data.datasets[0].data = humidityData;
        humChart.update();
    }
}

/**
 * Inicializar las gráficas con Chart.js
 */
function initializeCharts() {
    // Configuración común para gráficas
    const chartOptions = {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: true,
                labels: {
                    font: {
                        size: 12
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: false,
                grid: {
                    color: 'rgba(0, 0, 0, 0.1)'
                }
            },
            x: {
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                }
            }
        }
    };

    // Gráfica de Temperatura
    const tempCtx = document.getElementById('tempChart').getContext('2d');
    tempChart = new Chart(tempCtx, {
        type: 'line',
        data: {
            labels: timeLabels,
            datasets: [{
                label: 'Temperatura (°C)',
                data: temperatureData,
                borderColor: '#ff6b6b',
                backgroundColor: 'rgba(255, 107, 107, 0.1)',
                borderWidth: 2,
                pointRadius: 4,
                pointBackgroundColor: '#ff6b6b',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                tension: 0.4
            }]
        },
        options: {
            ...chartOptions,
            plugins: {
                ...chartOptions.plugins,
                title: {
                    display: true,
                    text: 'Temperatura en Tiempo Real'
                }
            }
        }
    });

    // Gráfica de Humedad
    const humCtx = document.getElementById('humChart').getContext('2d');
    humChart = new Chart(humCtx, {
        type: 'line',
        data: {
            labels: timeLabels,
            datasets: [{
                label: 'Humedad (%)',
                data: humidityData,
                borderColor: '#4ecdc4',
                backgroundColor: 'rgba(78, 205, 196, 0.1)',
                borderWidth: 2,
                pointRadius: 4,
                pointBackgroundColor: '#4ecdc4',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                tension: 0.4
            }]
        },
        options: {
            ...chartOptions,
            scales: {
                ...chartOptions.scales,
                y: {
                    ...chartOptions.scales.y,
                    max: 100,
                    beginAtZero: true
                }
            },
            plugins: {
                ...chartOptions.plugins,
                title: {
                    display: true,
                    text: 'Humedad en Tiempo Real'
                }
            }
        }
    });
}

/**
 * Iniciar lectura automática de datos
 * Se ejecuta cada 60 segundos (60000 ms)
 */
function startReadingData() {
    // Realizar lectura inmediata
    getDeviceVariables();

    // Establecer intervalo para lecturas periódicas
    setInterval(function () {
        if (isConnected) {
            getDeviceVariables();
        }
    }, 60000); // 60 segundos
}

/**
 * Recuperar credenciales guardadas del localStorage
 */
function loadSavedCredentials() {
    const savedDeviceId = localStorage.getItem('deviceId');
    if (savedDeviceId) {
        document.getElementById('deviceId_input').value = savedDeviceId;
    }
}

/**
 * Exportar datos históricos a CSV
 */
function exportDataToCSV() {
    if (temperatureData.length === 0) {
        alert('No hay datos para exportar');
        return;
    }

    let csvContent = 'data:text/csv;charset=utf-8,Hora,Temperatura (°C),Humedad (%)\n';

    for (let i = 0; i < temperatureData.length; i++) {
        csvContent += `${timeLabels[i]},${temperatureData[i]},${humidityData[i]}\n`;
    }

    const encodedUri = encodeURI(csvContent);
    const link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', `datos_sensor_${new Date().toISOString().split('T')[0]}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

/**
 * Limpiar todos los datos históricos
 */
function clearData() {
    if (confirm('¿Está seguro de que desea limpiar todos los datos históricos?')) {
        temperatureData = [];
        humidityData = [];
        timeLabels = [];

        if (tempChart) {
            tempChart.data.labels = timeLabels;
            tempChart.data.datasets[0].data = temperatureData;
            tempChart.update();
        }

        if (humChart) {
            humChart.data.labels = timeLabels;
            humChart.data.datasets[0].data = humidityData;
            humChart.update();
        }

        alert('Datos históricos eliminados');
    }
}

// Inicialización al cargar la página
document.addEventListener('DOMContentLoaded', function () {
    console.log('Página cargada - Sistema de monitoreo IoT iniciado');
    loadSavedCredentials();
    updateStatus('Desconectado', 'disconnected');
});

// Manejar desconexión antes de cerrar la página
window.addEventListener('beforeunload', function () {
    if (isConnected) {
        console.log('Desconectando de Particle Cloud');
        isConnected = false;
    }
});
