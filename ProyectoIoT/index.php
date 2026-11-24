<?php
declare(strict_types=1);

session_start();

if (empty($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
?>

<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Panel administrativo | Hotel</title>
  <link rel="stylesheet" href="styles.css">

  <style>
    /* ---------- ESTRUCTURA GENERAL ---------- */

    body {
      margin: 0;
      min-height: 100vh;
      background: #f4f6f7;
    }

    .app-layout {
      display: flex;
      min-height: 100vh;
    }

    /* ---------- SIDEBAR ---------- */

    .sidebar {
      width: 255px;
      min-width: 255px;
      background: #172e35;
      color: #fff;
      display: flex;
      flex-direction: column;
      padding: 25px 16px;
      box-sizing: border-box;
    }

    .sidebar-brand {
      padding: 5px 12px 28px;
      border-bottom: 1px solid rgba(255,255,255,.12);
      margin-bottom: 25px;
    }

    .sidebar-brand h2 {
      color: #fff;
      font-size: 21px;
      margin: 0 0 6px;
    }

    .sidebar-brand span {
      color: #c3cfd0;
      font-size: 12px;
      letter-spacing: 1px;
      text-transform: uppercase;
    }

    .sidebar-label {
      color: #a9b9ba;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 1.3px;
      padding: 0 12px;
      margin: 0 0 12px;
      text-transform: uppercase;
    }

    .sidebar-nav {
      display: flex;
      flex-direction: column;
      gap: 7px;
    }

    .nav-item {
      display: flex;
      align-items: center;
      gap: 13px;
      width: 100%;
      padding: 13px 14px;
      border: 0;
      border-radius: 9px;
      background: transparent;
      color: #d5dfdf;
      font-size: 14px;
      text-align: left;
      cursor: pointer;
      transition: .2s;
    }

    .nav-item:hover {
      background: rgba(255,255,255,.09);
      color: #fff;
    }

    .nav-item.active {
      background: #304b4b;
      color: #fff;
      box-shadow: inset 3px 0 #d6bd8c;
    }

    .nav-icon {
      width: 21px;
      font-size: 18px;
      text-align: center;
    }

    .sidebar-bottom {
      margin-top: auto;
      padding-top: 25px;
      border-top: 1px solid rgba(255,255,255,.12);
    }

    .admin-info {
      color: #d5dfdf;
      font-size: 12px;
      padding: 0 12px;
      margin-bottom: 15px;
    }

    .logout-button {
      display: block;
      width: 100%;
      box-sizing: border-box;
      padding: 12px;
      background: #dc2626;
      color: #fff;
      text-align: center;
      text-decoration: none;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 600;
      transition: .2s;
    }

    .logout-button:hover {
      background: #b91c1c;
    }

    /* ---------- CONTENIDO PRINCIPAL ---------- */

    .main-area {
      flex: 1;
      min-width: 0;
      display: flex;
      flex-direction: column;
    }

    .topbar {
      min-height: 76px;
      background: #fff;
      border-bottom: 1px solid #e5e9eb;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 15px;
      padding: 15px 30px;
      box-sizing: border-box;
    }

    .topbar h1 {
      margin: 0;
      color: #263f43;
      font-size: 22px;
    }

    .topbar-status {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      flex-wrap: wrap;
      gap: 9px;
    }

    .content {
      padding: 28px;
      width: 100%;
      max-width: 1500px;
      box-sizing: border-box;
      margin: 0 auto;
    }

    .view-section[hidden] {
      display: none !important;
    }

    .section-heading {
      margin-bottom: 22px;
    }

    .section-heading h2 {
      margin: 0 0 7px;
      color: #263f43;
      font-size: 23px;
    }

    .section-heading p {
      margin: 0;
      color: #778589;
      font-size: 14px;
    }

    /* ---------- TARJETAS Y ESTADOS ---------- */

    .dashboard-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 20px;
    }

    .content .card {
      margin: 0 0 20px;
    }

    .dashboard-grid .card {
      margin: 0;
    }

    .status {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 15px;
    }

    .status .item {
      min-width: 0;
    }

    .hint {
      line-height: 1.6;
    }

    /* ---------- HABITACIONES ---------- */

    .rooms-placeholder {
      background: #fff;
      border: 1px solid #e5e9eb;
      border-radius: 13px;
      padding: 55px 25px;
      text-align: center;
      box-shadow: 0 3px 12px rgba(25,45,50,.04);
    }

    .rooms-placeholder .room-icon {
      width: 75px;
      height: 75px;
      margin: 0 auto 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      background: #edf2f1;
      color: #304b4b;
      font-size: 34px;
    }

    .rooms-placeholder h3 {
      color: #263f43;
      margin: 0 0 10px;
      font-size: 20px;
    }

    .rooms-placeholder p {
      color: #778589;
      max-width: 480px;
      margin: 0 auto;
      line-height: 1.6;
      font-size: 14px;
    }

    /* ---------- CROQUIS DE HABITACIONES ---------- */

    .rooms-legend {
      display: flex;
      flex-wrap: wrap;
      gap: 10px 18px;
      margin-bottom: 16px;
      font-size: 13px;
      color: #4b5b5f;
    }

    .rooms-legend span {
      display: inline-flex;
      align-items: center;
      gap: 7px;
    }

    .rooms-legend i {
      width: 26px;
      height: 14px;
      border-radius: 3px;
      display: inline-block;
    }

    .rooms-scroll {
      overflow-x: auto;
      padding-bottom: 10px;
    }

    .rooms-plan {
      min-width: 860px;
      display: grid;
      grid-template-columns: 1fr 1fr 70px 1fr 1fr;
      grid-template-rows: 235px 78px 235px;
      gap: 7px;
      padding: 7px;
      background: #111;
      border-radius: 4px;
    }

    .room {
      position: relative;
      border: 0;
      padding: 0;
      background: #c0925a;
      cursor: pointer;
      overflow: hidden;
      text-align: left;
      font-family: inherit;
      transition: filter .15s, transform .15s;
    }

    .room:hover { filter: brightness(1.08); }
    .room.selected { outline: 4px solid #d6bd8c; outline-offset: -4px; }

    .room .num {
      position: absolute;
      right: 12px;
      font-size: 15px;
      font-weight: 700;
      color: #3e2a12;
      background: rgba(255,255,255,.55);
      padding: 3px 9px;
      border-radius: 5px;
    }

    .room .bed {
      position: absolute;
      left: 10px;
      width: 38%;
      height: 46%;
      background: #f4a766;
      border: 2px solid #d38a4a;
      border-radius: 3px;
    }

    .room .bed::before {
      content: "";
      position: absolute;
      left: 0; right: 0;
      height: 38%;
      background: #fbf4ea;
    }

    .room .bed::after {
      content: "";
      position: absolute;
      left: 50%;
      width: 34%;
      height: 18%;
      transform: translateX(-50%);
      background: #f4a766;
      border: 1px solid #d38a4a;
      border-radius: 3px;
    }

    .room .bath {
      position: absolute;
      right: 10px;
      width: 31%;
      height: 40%;
      background: #f3f6f7;
      border: 4px solid #111;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 8px;
    }

    .room .bath::before {
      content: "";
      width: 16px;
      height: 22px;
      border: 2px solid #777;
      border-radius: 50% 50% 45% 45%;
      background: #fff;
    }

    .room .bath::after {
      content: "";
      width: 36%;
      height: 100%;
      margin-right: -8px;
      background: #cfe3ec;
      border-left: 2px solid #8fb1c0;
    }

    .room .status-rect {
      position: absolute;
      left: 10px;
      width: calc(69% - 30px);
      min-width: 110px;
      padding: 9px 6px;
      border-radius: 6px;
      text-align: center;
      font-size: 12.5px;
      font-weight: 800;
      letter-spacing: .6px;
      color: #fff;
      box-shadow: 0 2px 6px rgba(0,0,0,.3);
    }

    .st-ocupado    { background: #dc2626; }
    .st-desocupado { background: #16a34a; }
    .st-no_molestar{ background: #6b7280; }

    /* Fila superior: cama arriba, baño abajo */
    .room.top .num { top: 12px; }
    .room.top .bed { top: 10px; }
    .room.top .bed::before { top: 0; }
    .room.top .bed::after  { top: 6%; }
    .room.top .bath { bottom: 10px; }
    .room.top .status-rect { bottom: 12px; }

    /* Fila inferior: espejo */
    .room.bottom .num { bottom: 12px; }
    .room.bottom .bed { bottom: 10px; }
    .room.bottom .bed::before { bottom: 0; }
    .room.bottom .bed::after  { bottom: 6%; }
    .room.bottom .bath { top: 10px; }
    .room.bottom .status-rect { top: 12px; }

    .plan-wall { background: #111; }

    .plan-corridor {
      grid-column: 1 / -1;
      background: #fbf8ee;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #b7ad94;
      font-size: 12px;
      letter-spacing: 6px;
      text-transform: uppercase;
      background-image:
        linear-gradient(#e9e4d3 1px, transparent 1px),
        linear-gradient(90deg, #e9e4d3 1px, transparent 1px);
      background-size: 38px 38px;
    }

    .plan-stem {
      grid-column: 3;
      grid-row: 3;
      background: #fbf8ee;
      display: flex;
      align-items: flex-end;
      justify-content: center;
      padding-bottom: 8px;
      color: #8a7f63;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 1px;
      text-transform: uppercase;
      background-image:
        linear-gradient(#e9e4d3 1px, transparent 1px),
        linear-gradient(90deg, #e9e4d3 1px, transparent 1px);
      background-size: 38px 38px;
    }

    /* ---------- SIDEBAR DE HISTORIAL DE HABITACIÓN ---------- */

    .drawer-overlay {
      position: fixed;
      inset: 0;
      background: rgba(10,25,30,.4);
      z-index: 90;
    }

    .room-drawer {
      position: fixed;
      top: 0;
      right: 0;
      bottom: 0;
      width: 400px;
      max-width: 100%;
      background: #fff;
      z-index: 100;
      display: flex;
      flex-direction: column;
      box-shadow: -8px 0 30px rgba(0,0,0,.2);
      transform: translateX(100%);
      transition: transform .25s ease;
    }

    .room-drawer.open { transform: translateX(0); }

    .drawer-head {
      padding: 20px 22px;
      background: #172e35;
      color: #fff;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
    }

    .drawer-head h3 { margin: 0; font-size: 19px; }

    .drawer-close {
      border: 0;
      background: rgba(255,255,255,.12);
      color: #fff;
      width: 34px;
      height: 34px;
      border-radius: 8px;
      font-size: 20px;
      cursor: pointer;
    }

    .drawer-body { padding: 20px 22px; overflow-y: auto; flex: 1; }

    .drawer-current {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 14px;
      font-size: 13px;
      color: #4b5b5f;
    }

    .drawer-current .status-pill {
      color: #fff;
      font-weight: 800;
      font-size: 12px;
      letter-spacing: .6px;
      padding: 6px 12px;
      border-radius: 6px;
    }

    .state-buttons {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 8px;
      margin-bottom: 22px;
    }

    .state-buttons button {
      border: 0;
      border-radius: 7px;
      padding: 9px 4px;
      color: #fff;
      font-size: 11px;
      font-weight: 700;
      cursor: pointer;
      opacity: .85;
    }

    .state-buttons button:hover { opacity: 1; }

    .drawer-body h4 {
      margin: 0 0 12px;
      color: #263f43;
      font-size: 15px;
    }

    .timeline { list-style: none; margin: 0; padding: 0; }

    .timeline li {
      position: relative;
      padding: 0 0 16px 22px;
      border-left: 2px solid #e1e7e8;
      margin-left: 6px;
    }

    .timeline li::before {
      content: "";
      position: absolute;
      left: -7px;
      top: 2px;
      width: 12px;
      height: 12px;
      border-radius: 50%;
      background: var(--dot, #6b7280);
      border: 2px solid #fff;
    }

    .timeline .t-state { font-weight: 700; font-size: 13px; color: #263f43; }
    .timeline .t-who   { font-size: 13px; color: #4b5b5f; }
    .timeline .t-when  { font-size: 12px; color: #8a979a; }

    .drawer-empty { color: #8a979a; font-size: 14px; }

    /* ---------- RESPONSIVE ---------- */

    @media (max-width: 850px) {
      .app-layout {
        flex-direction: column;
      }

      .sidebar {
        width: 100%;
        min-width: 0;
        padding: 15px;
      }

      .sidebar-brand {
        padding: 5px 8px 15px;
        margin-bottom: 15px;
      }

      .sidebar-nav {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }

      .sidebar-bottom {
        margin-top: 15px;
        padding-top: 15px;
      }

      .admin-info {
        display: none;
      }

      .topbar {
        padding: 15px 20px;
      }

      .content {
        padding: 20px;
      }
    }

    @media (max-width: 560px) {
      .sidebar-nav {
        grid-template-columns: 1fr;
      }

      .topbar {
        align-items: flex-start;
        flex-direction: column;
      }

      .topbar-status {
        justify-content: flex-start;
      }

      .dashboard-grid,
      .status {
        grid-template-columns: 1fr;
      }

      .content {
        padding: 14px;
      }
    }
  </style>
</head>

<body>

<div class="app-layout">

  <!-- ================= SIDEBAR ================= -->

  <aside class="sidebar">

    <div class="sidebar-brand">
      <h2>Hotel</h2>
      <span>Panel administrativo</span>
    </div>

    <div class="sidebar-label">Menú principal</div>

    <nav class="sidebar-nav">

      <button class="nav-item active" type="button" data-view="inicio">
        <span class="nav-icon">⌂</span>
        Estado actual
      </button>

      <button class="nav-item" type="button" data-view="historial">
        <span class="nav-icon">◷</span>
        Historial
      </button>

      <button class="nav-item" type="button" data-view="registro">
        <span class="nav-icon">＋</span>
        Registrar persona
      </button>

      <button class="nav-item" type="button" data-view="personas">
        <span class="nav-icon">♙</span>
        Personas registradas
      </button>

      <button class="nav-item" type="button" data-view="habitaciones">
        <span class="nav-icon">▦</span>
        Vista de habitaciones
      </button>

    </nav>

    <div class="sidebar-bottom">
      <div class="admin-info">
        Sesión administrativa
      </div>

      <a href="logout.php" class="logout-button">
        Cerrar sesión
      </a>
    </div>

  </aside>

  <!-- ================= CONTENIDO ================= -->

  <div class="main-area">

    <header class="topbar">

      <h1 id="pageTitle">Estado actual</h1>

      <div class="topbar-status">
        <span id="conn" class="conn">Photon: conectando...</span>
        <span id="dbconn" class="conn">Base de datos: conectando...</span>
      </div>

    </header>

    <main class="content">

      <div id="banner" class="banner" hidden></div>

      <!-- ================= ESTADO ACTUAL ================= -->

      <section class="view-section" id="view-inicio">

        <div class="section-heading">
          <h2>Estado actual</h2>
          <p>Consulta el estado del sistema y la actividad más reciente.</p>
        </div>

        <div class="dashboard-grid">

          <div class="card">
            <h2>Estado del sistema</h2>

            <div class="status">
              <div class="item">
                <div class="label">Estado</div>
                <div class="value" id="ShowStatus">-</div>
              </div>

              <div class="item">
                <div class="label">Último acceso</div>
                <div class="value" id="ShowLast">-</div>
              </div>
            </div>
          </div>

          <div class="card">
            <h2>Información del sistema</h2>

            <p class="hint">
              Desde este panel puedes supervisar el funcionamiento
              del lector NFC y la comunicación con la base de datos.
            </p>

            <p class="hint">
              Los indicadores superiores muestran el estado de conexión
              del Photon y MySQL.
            </p>
          </div>

        </div>

      </section>

      <!-- ================= HISTORIAL ================= -->

      <section class="view-section" id="view-historial" hidden>

        <div class="section-heading">
          <h2>Historial de accesos</h2>
          <p>Consulta los registros de entrada y los intentos de acceso.</p>
        </div>

        <div class="card">

          <input id="filtro" type="text"
            placeholder="Buscar por nombre, UID, fecha o resultado"
            style="margin-bottom:12px">

          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>Foto</th>
                  <th>Nombre</th>
                  <th>Fecha</th>
                  <th>Hora</th>
                  <th>Resultado</th>
                </tr>
              </thead>

              <tbody id="logs"></tbody>
            </table>
          </div>

          <p class="empty muted" id="vacioLogs" hidden>
            Todavía no hay accesos registrados.
          </p>

          <p class="hint">
            El historial se guarda en la base de datos MySQL.
            La página copia a la base de datos los accesos que ve
            en el Photon, así que debe estar abierta para registrarlos.
          </p>

        </div>

      </section>

      <!-- ================= REGISTRAR PERSONA ================= -->

      <section class="view-section" id="view-registro" hidden>

        <div class="section-heading">
          <h2>Registrar persona</h2>
          <p>Agrega una persona y vincula su tarjeta de acceso NFC.</p>
        </div>

        <div class="card">

          <form id="form" autocomplete="off">

            <label for="nombre">Nombre</label>
            <input id="nombre" type="text" required>

            <label for="uid">UID de la tarjeta</label>

            <div class="row">
              <input id="uid" type="text"
                placeholder="Ej. 04A1B2C3" required>

              <button class="secondary" id="usarUltima" type="button">
                Usar última tarjeta desconocida
              </button>
            </div>

            <p class="hint">
              Pasa la tarjeta por el lector (será denegada)
              y presiona el botón.
            </p>

            <label for="foto">Foto (opcional)</label>
            <input id="foto" type="file" accept="image/*">

            <p class="hint">La foto se guarda en la base de datos.</p>

            <img id="preview" class="preview"
              alt="Vista previa" hidden>

            <div style="margin-top:16px">
              <button class="primary" id="guardar" type="submit">
                Guardar
              </button>
            </div>

            <div id="msg" class="msg" hidden></div>

          </form>

        </div>

      </section>

      <!-- ================= PERSONAS REGISTRADAS ================= -->

      <section class="view-section" id="view-personas" hidden>

        <div class="section-heading">
          <h2>Personas registradas</h2>
          <p>Administra las personas que cuentan con una tarjeta NFC.</p>
        </div>

        <div class="card">

          <div class="people" id="lista"></div>

          <p class="empty muted" id="vacioPersonas" hidden>
            Aún no hay personas registradas.
          </p>

        </div>

      </section>

      <!-- ================= HABITACIONES ================= -->

      <section class="view-section" id="view-habitaciones" hidden>

        <div class="section-heading">
          <h2>Vista general de habitaciones</h2>
          <p>Consulta y administra la información de las habitaciones del hotel.</p>
        </div>

        <div class="rooms-legend">
          <span><i class="st-ocupado"></i> OCUPADO</span>
          <span><i class="st-desocupado"></i> DESOCUPADO</span>
          <span><i class="st-no_molestar"></i> NO MOLESTAR</span>
          <span style="margin-left:auto">Haz clic en una habitación para ver su historial.</span>
        </div>

        <div class="rooms-scroll">
          <div class="rooms-plan" id="roomsPlan">
            <div class="plan-corridor">Pasillo</div>
            <div class="plan-stem">Entrada</div>
          </div>
        </div>

      </section>

    </main>

  </div>

</div>

<!-- ================= SIDEBAR HISTORIAL DE HABITACIÓN ================= -->

<div class="drawer-overlay" id="drawerOverlay" hidden></div>

<aside class="room-drawer" id="roomDrawer" aria-hidden="true">
  <div class="drawer-head">
    <h3 id="drawerTitle">Habitación</h3>
    <button class="drawer-close" id="drawerClose" type="button" aria-label="Cerrar">×</button>
  </div>

  <div class="drawer-body">
    <div class="drawer-current">
      <span>Estado actual</span>
      <span class="status-pill" id="drawerStatus">-</span>
    </div>

    <div class="state-buttons">
      <button type="button" class="st-ocupado" data-estado="ocupado">OCUPADO</button>
      <button type="button" class="st-desocupado" data-estado="desocupado">DESOCUPADO</button>
      <button type="button" class="st-no_molestar" data-estado="no_molestar">NO MOLESTAR</button>
    </div>

    <h4>Historial de la habitación</h4>
    <ul class="timeline" id="roomHistory"></ul>
    <p class="drawer-empty" id="roomHistoryEmpty" hidden>Esta habitación aún no tiene historial.</p>
  </div>
</aside>

<script>
// ============================================================
// NAVEGACIÓN DEL SIDEBAR
// ============================================================

var viewTitles = {
  inicio: "Estado actual",
  historial: "Historial de accesos",
  registro: "Registrar persona",
  personas: "Personas registradas",
  habitaciones: "Vista general de habitaciones"
};

document.querySelectorAll(".nav-item").forEach(function (button) {

  button.addEventListener("click", function () {

    var view = this.dataset.view;

    document.querySelectorAll(".nav-item").forEach(function (item) {
      item.classList.remove("active");
    });

    this.classList.add("active");

    document.querySelectorAll(".view-section").forEach(function (section) {
      section.hidden = true;
    });

    var selected = document.getElementById("view-" + view);

    if (selected) {
      selected.hidden = false;
    }

    document.getElementById("pageTitle").textContent =
      viewTitles[view] || "Panel administrativo";

  });

});

// ============================================================
// CONFIGURACIÓN: token y ID de tu Photon
// ============================================================

var token = "PON_AQUI_TU_TOKEN";
var deviceId = "29002b000b47313037363132";

// Cliente de la API de Particle
var particle = {
  base: "https://api.particle.io/v1/devices/",

  request: function (url, options) {
    return fetch(url, options).then(function (r) {
      return r.json().catch(function () { return {}; }).then(function (body) {
        if (!r.ok) {
          throw new Error(body.error_description || body.error || ("Error " + r.status));
        }
        return { statusCode: r.status, body: body };
      });
    });
  },

  getVariable: function (o) {
    return this.request(
      this.base + encodeURIComponent(o.deviceId) + "/" + encodeURIComponent(o.name),
      { headers: { Authorization: "Bearer " + o.auth } }
    );
  },

  callFunction: function (o) {
    return this.request(
      this.base + encodeURIComponent(o.deviceId) + "/" + encodeURIComponent(o.name),
      {
        method: "POST",
        headers: {
          Authorization: "Bearer " + o.auth,
          "Content-Type": "application/x-www-form-urlencoded"
        },
        body: new URLSearchParams({ arg: o.argument }).toString()
      }
    );
  }
};

// Cliente de nuestra API PHP que guarda en MySQL
function api(query, method, body) {
  var opts = { method: method || "GET" };

  if (body !== undefined) {
    opts.headers = { "Content-Type": "application/json" };
    opts.body = JSON.stringify(body);
  }

  return fetch("api.php?" + query, opts).then(function (r) {
    return r.json().catch(function () { return {}; }).then(function (b) {
      if (!r.ok) throw new Error(b.error || ("Error " + r.status));
      return b;
    });
  });
}

var $ = function (s) {
  return document.querySelector(s);
};

var lastUnknown = "";
var photonUids = null;
var lastSyncedHist = "";
var logsDb = [];
var peopleDb = [];

// ---------- Utilidades ----------

function fmtDate(t) {
  return new Date(t * 1000).toLocaleDateString("es-MX", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric"
  });
}

function fmtTime(t) {
  return new Date(t * 1000).toLocaleTimeString("es-MX", {
    hour: "2-digit",
    minute: "2-digit",
    second: "2-digit"
  });
}

function validTime(t) {
  return t > 1000000000;
}

function avatar(uid, name, hasPhoto, version) {
  var wrap = document.createElement("div");
  wrap.className = "avatar";

  var initial = ((name || "?").trim().charAt(0) || "?").toUpperCase();

  if (hasPhoto) {
    var img = document.createElement("img");
    img.alt = "";
    img.src = "api.php?a=foto&uid=" + encodeURIComponent(uid) + "&v=" + version;

    img.onerror = function () {
      img.remove();
      wrap.textContent = initial;
    };

    wrap.appendChild(img);
  } else {
    wrap.textContent = initial;
  }

  return wrap;
}

function td() {
  var cell = document.createElement("td");
  for (var i = 0; i < arguments.length; i++) {
    cell.append(arguments[i]);
  }
  return cell;
}

function setChip(sel, ok, text) {
  var c = $(sel);
  c.className = "conn" + (ok ? " on" : "");
  c.textContent = text;
}

function parseJson(text, fallback) {
  try {
    return JSON.parse(text);
  } catch (e) {
    return fallback;
  }
}

// El Photon guarda el nombre en 12 bytes como máximo
function cut12(name) {
  var out = "", enc = new TextEncoder();

  for (var ch of name) {
    if (enc.encode(out + ch).length > 12) break;
    out += ch;
  }

  return out;
}

function showMsg(text, kind) {
  var m = $("#msg");
  m.textContent = text;
  m.className = "msg " + kind;
  m.hidden = !text;
}

// ---------- Base de datos MySQL ----------

function refreshDb() {
  return Promise.all([
    api("a=logs&limit=200"),
    api("a=people")
  ]).then(function (r) {
    logsDb = r[0];
    peopleDb = r[1];

    setChip("#dbconn", true, "Base de datos: conectada");

    renderLogs();
    renderPeople();

  }).catch(function (err) {
    setChip("#dbconn", false, "Base de datos: error");
    console.error(err);
    showMsg(err.message, "err");
  });
}

// ---------- Lectura del Photon ----------

function readVar(name) {
  return particle.getVariable({
    deviceId: deviceId,
    name: name,
    auth: token
  }).then(function (data) {
    return String(data.body.result);
  });
}

function updatePhoton() {
  if (token === "PON_AQUI_TU_TOKEN") return;

  var names = ["estado", "historial", "personas", "uidlibre"];

  Promise.allSettled(names.map(readVar)).then(function (res) {
    var okCount = res.filter(function (r) {
      return r.status === "fulfilled";
    }).length;

    if (okCount === 0) {
      setChip("#conn", false, "Photon: sin conexión");
      console.error("Error al leer las variables:", res[0].reason);
      return;
    }

    setChip("#conn", true, "Photon: conectado");

    var val = function (i) {
      return res[i].status === "fulfilled" ? res[i].value : null;
    };

    if (val(0) !== null) {
      $("#ShowStatus").textContent = val(0);
    }

    if (val(3) !== null) {
      lastUnknown = val(3);
    }

    if (val(2) !== null) {
      var before = JSON.stringify(photonUids);

      photonUids = parseJson(val(2), []).map(function (p) {
        return p.u;
      });

      if (JSON.stringify(photonUids) !== before) {
        renderPeople();
      }
    }

    // Copia a MySQL los accesos nuevos del Photon
    var hist = val(1);

    if (hist !== null && hist !== lastSyncedHist) {
      api("a=sync", "POST", { logs: parseJson(hist, []) })
        .then(function () {
          lastSyncedHist = hist;
          return refreshDb();
        })
        .catch(function (err) {
          setChip("#dbconn", false, "Base de datos: error");
          console.error(err);
        });
    }
  });
}

// ---------- Historial ----------

function renderLogs() {
  var q = $("#filtro").value.trim().toLowerCase();
  var tbody = $("#logs");

  tbody.replaceChildren();

  var rows = logsDb.filter(function (l) {
    return [
      l.nombre || "desconocido",
      l.uid,
      fmtDate(l.ts),
      l.ok ? "permitido" : "denegado"
    ].join(" ").toLowerCase().indexOf(q) !== -1;
  });

  $("#vacioLogs").hidden = rows.length > 0;

  var last = logsDb[0];

  $("#ShowLast").textContent = last
    ? ((last.nombre || "Desconocido") +
      (validTime(last.ts) ? " · " + fmtTime(last.ts) : ""))
    : "-";

  rows.forEach(function (l) {
    var tr = document.createElement("tr");

    var box = document.createElement("div");
    var nameEl = document.createElement("div");

    nameEl.textContent = l.nombre || "Desconocido";

    if (!l.nombre) {
      nameEl.className = "muted";
    }

    var uidEl = document.createElement("code");
    uidEl.className = "muted";
    uidEl.textContent = l.uid;

    box.append(nameEl, uidEl);

    var badge = document.createElement("span");
    badge.className = "badge " + (l.ok ? "ok" : "no");
    badge.textContent = l.ok ? "Permitido" : "Denegado";

    tr.append(
      td(avatar(l.uid, l.nombre, l.foto, l.fv)),
      td(box),
      td(validTime(l.ts) ? fmtDate(l.ts) : "-"),
      td(validTime(l.ts) ? fmtTime(l.ts) : "-"),
      td(badge)
    );

    tbody.appendChild(tr);
  });
}

// ---------- Personas ----------

function renderPeople() {
  var list = $("#lista");

  list.replaceChildren();
  $("#vacioPersonas").hidden = peopleDb.length > 0;

  peopleDb.forEach(function (p) {
    var card = document.createElement("div");
    card.className = "person";

    var info = document.createElement("div");
    info.className = "info";

    var name = document.createElement("div");
    name.className = "name";
    name.textContent = p.nombre;

    var uid = document.createElement("code");
    uid.className = "muted";
    uid.textContent = p.uid;

    info.append(name, uid);

    // Si no está en el Photon, se puede reenviar
    var missing = photonUids !== null && photonUids.indexOf(p.uid) === -1;

    if (missing) {
      var warn = document.createElement("div");
      warn.className = "warn";
      warn.textContent = "No está en el Photon (su tarjeta no abre) ";

      var send = document.createElement("button");
      send.className = "secondary";
      send.type = "button";
      send.textContent = "Enviar al Photon";

      send.onclick = function () {
        particle.callFunction({
          deviceId: deviceId,
          name: "agregar",
          argument: p.uid + "," + cut12(p.nombre),
          auth: token
        }).then(function () {
          updatePhoton();
        }).catch(function (err) {
          showMsg("No se pudo enviar al Photon: " + err.message, "err");
        });
      };

      warn.append(send);
      info.append(warn);
    }

    var del = document.createElement("button");
    del.className = "danger";
    del.type = "button";
    del.textContent = "Eliminar";

    del.onclick = function () {
      if (!confirm(
        "¿Eliminar a " + p.nombre +
        "? Su tarjeta dejará de tener acceso. El historial se conserva."
      )) return;

      particle.callFunction({
        deviceId: deviceId,
        name: "borrar",
        argument: p.uid,
        auth: token
      })
      .then(function () {
        return api("a=people&uid=" + encodeURIComponent(p.uid), "DELETE");
      })
      .then(function () {
        updatePhoton();
        return refreshDb();
      })
      .catch(function (err) {
        showMsg("No se pudo eliminar: " + err.message, "err");
      });
    };

    card.append(avatar(p.uid, p.nombre, p.foto, p.fv), info, del);
    list.appendChild(card);
  });
}

// ---------- Registro de personas ----------

function resizeImage(file, max) {
  return createImageBitmap(file).then(function (bmp) {
    var scale = Math.min(1, max / Math.max(bmp.width, bmp.height));

    var c = document.createElement("canvas");
    c.width = Math.round(bmp.width * scale);
    c.height = Math.round(bmp.height * scale);

    c.getContext("2d").drawImage(bmp, 0, 0, c.width, c.height);

    return c.toDataURL("image/jpeg", 0.8);
  });
}

$("#foto").addEventListener("change", function () {
  var file = this.files[0];
  var prev = $("#preview");

  if (!file) {
    prev.hidden = true;
    return;
  }

  resizeImage(file, 200)
    .then(function (data) {
      prev.src = data;
      prev.hidden = false;
    })
    .catch(function () {
      prev.hidden = true;
      showMsg("No se pudo leer esa imagen", "err");
    });
});

$("#usarUltima").addEventListener("click", function () {
  if (!lastUnknown) {
    showMsg(
      "Aún no hay tarjetas desconocidas. Pasa la tarjeta por el lector e inténtalo de nuevo.",
      "err"
    );
    return;
  }

  $("#uid").value = lastUnknown;
  showMsg("UID copiado", "ok");
});

$("#filtro").addEventListener("input", renderLogs);

$("#form").addEventListener("submit", function (e) {
  e.preventDefault();
  showMsg("", "ok");

  var name = $("#nombre").value.trim();
  var uid = $("#uid").value.toUpperCase().replace(/[^0-9A-F]/g, "");

  if (!name) {
    showMsg("Escribe un nombre", "err");
    return;
  }

  if (name.length > 80) {
    showMsg("El nombre es demasiado largo (máximo 80 caracteres)", "err");
    return;
  }

  if (!/^([0-9A-F]{8}|[0-9A-F]{14}|[0-9A-F]{20})$/.test(uid)) {
    showMsg("El UID no es válido (8, 14 o 20 caracteres hexadecimales)", "err");
    return;
  }

  var btn = $("#guardar");
  btn.disabled = true;

  var file = $("#foto").files[0];

  // 1) Se registra en el Photon
  // 2) Se guarda en MySQL
  particle.callFunction({
    deviceId: deviceId,
    name: "agregar",
    argument: uid + "," + cut12(name),
    auth: token
  })
  .then(function (data) {
    var code = Number(data.body.return_value);

    if (code !== 1 && code !== -2) {
      var errors = {
        "-1": "Datos inválidos",
        "-3": "La lista del Photon está llena (máximo 12 personas)"
      };

      throw new Error(errors[String(code)] || ("Respuesta inesperada del Photon: " + code));
    }

    return (file ? resizeImage(file, 200) : Promise.resolve(null))
      .then(function (photo) {
        return api("a=people", "POST", {
          uid: uid,
          nombre: name,
          photo: photo
        });
      })
      .then(function () {
        $("#form").reset();
        $("#preview").hidden = true;

        showMsg(
          code === 1
            ? "Persona registrada"
            : "Ya estaba en el Photon; se guardó en la base de datos",
          "ok"
        );

        updatePhoton();
        return refreshDb();
      });
  })
  .catch(function (err) {
    showMsg("No se pudo registrar: " + err.message, "err");
  })
  .finally(function () {
    btn.disabled = false;
  });
});

// ---------- Habitaciones (croquis) ----------

var ROOMS = [
  { n: 101, row: "top",    col: 1 },
  { n: 102, row: "top",    col: 2 },
  { n: 103, row: "top",    col: 4 },
  { n: 104, row: "top",    col: 5 },
  { n: 105, row: "bottom", col: 1 },
  { n: 106, row: "bottom", col: 2 },
  { n: 107, row: "bottom", col: 4 },
  { n: 108, row: "bottom", col: 5 }
];

var ESTADOS = {
  ocupado:     { label: "OCUPADO",     cls: "st-ocupado",     color: "#dc2626" },
  desocupado:  { label: "DESOCUPADO",  cls: "st-desocupado",  color: "#16a34a" },
  no_molestar: { label: "NO MOLESTAR", cls: "st-no_molestar", color: "#6b7280" }
};

var roomStates = {};
var currentRoom = null;

function normEstado(e) {
  e = String(e || "").toLowerCase().trim().replace(/\s+/g, "_");
  return ESTADOS[e] ? e : "desocupado";
}

function buildRooms() {
  var plan = $("#roomsPlan");

  ROOMS.forEach(function (r) {
    var btn = document.createElement("button");
    btn.type = "button";
    btn.className = "room " + r.row;
    btn.id = "room-" + r.n;
    btn.style.gridColumn = r.col;
    btn.style.gridRow = r.row === "top" ? 1 : 3;

    var num = document.createElement("div");
    num.className = "num";
    num.textContent = "Hab. " + r.n;

    var bed = document.createElement("div");
    bed.className = "bed";

    var bath = document.createElement("div");
    bath.className = "bath";

    var st = document.createElement("div");
    st.className = "status-rect";

    btn.append(num, bed, bath, st);
    btn.addEventListener("click", function () { openRoom(r.n); });

    plan.appendChild(btn);
  });

  // Muro en la columna central de la fila superior
  var wall = document.createElement("div");
  wall.className = "plan-wall";
  wall.style.gridColumn = 3;
  wall.style.gridRow = 1;
  plan.appendChild(wall);

  paintRooms();
}

function paintRooms() {
  ROOMS.forEach(function (r) {
    var e = ESTADOS[normEstado(roomStates[r.n])];
    var el = $("#room-" + r.n + " .status-rect");
    el.className = "status-rect " + e.cls;
    el.textContent = e.label;
  });

  if (currentRoom) paintDrawerStatus();
}

function loadRooms() {
  return api("a=rooms").then(function (rows) {
    (rows || []).forEach(function (x) {
      roomStates[x.numero] = normEstado(x.estado);
    });
    paintRooms();
  }).catch(function (err) {
    console.error("Habitaciones:", err);
  });
}

function paintDrawerStatus() {
  var e = ESTADOS[normEstado(roomStates[currentRoom])];
  var pill = $("#drawerStatus");
  pill.textContent = e.label;
  pill.style.background = e.color;
}

function openRoom(n) {
  currentRoom = n;

  document.querySelectorAll(".room").forEach(function (r) {
    r.classList.toggle("selected", r.id === "room-" + n);
  });

  $("#drawerTitle").textContent = "Habitación " + n;
  paintDrawerStatus();

  $("#drawerOverlay").hidden = false;
  $("#roomDrawer").classList.add("open");
  $("#roomDrawer").setAttribute("aria-hidden", "false");

  loadRoomHistory(n);
}

function closeRoom() {
  currentRoom = null;

  document.querySelectorAll(".room.selected").forEach(function (r) {
    r.classList.remove("selected");
  });

  $("#drawerOverlay").hidden = true;
  $("#roomDrawer").classList.remove("open");
  $("#roomDrawer").setAttribute("aria-hidden", "true");
}

function loadRoomHistory(n) {
  var ul = $("#roomHistory");
  var empty = $("#roomHistoryEmpty");

  ul.replaceChildren();
  empty.hidden = true;

  api("a=room_history&room=" + encodeURIComponent(n)).then(function (rows) {
    if (currentRoom !== n) return;

    empty.textContent = "Esta habitación aún no tiene historial.";
    empty.hidden = rows.length > 0;

    rows.forEach(function (h) {
      var e = ESTADOS[normEstado(h.estado)];

      var li = document.createElement("li");
      li.style.setProperty("--dot", e.color);

      var st = document.createElement("div");
      st.className = "t-state";
      st.textContent = e.label;

      var who = document.createElement("div");
      who.className = "t-who";
      who.textContent = h.nombre || "Sin persona asociada";

      var when = document.createElement("div");
      when.className = "t-when";
      when.textContent = validTime(h.ts)
        ? fmtDate(h.ts) + " · " + fmtTime(h.ts)
        : "-";

      li.append(st, who, when);
      ul.appendChild(li);
    });
  }).catch(function (err) {
    empty.textContent = "No se pudo cargar el historial: " + err.message;
    empty.hidden = false;
  });
}

function setRoomState(estado) {
  if (!currentRoom) return;
  var n = currentRoom;

  api("a=room_state", "POST", { room: n, estado: estado })
    .then(function () {
      roomStates[n] = estado;
      paintRooms();
      loadRoomHistory(n);
    })
    .catch(function (err) {
      alert("No se pudo cambiar el estado: " + err.message);
    });
}

document.querySelectorAll(".state-buttons button").forEach(function (b) {
  b.addEventListener("click", function () { setRoomState(this.dataset.estado); });
});

$("#drawerClose").addEventListener("click", closeRoom);
$("#drawerOverlay").addEventListener("click", closeRoom);
document.addEventListener("keydown", function (e) {
  if (e.key === "Escape") closeRoom();
});

buildRooms();

// ---------- Inicio ----------

if (token === "PON_AQUI_TU_TOKEN") {
  var b = $("#banner");

  b.textContent =
    "Falta configurar el token: abre este archivo y reemplaza PON_AQUI_TU_TOKEN por tu token de acceso de Particle.";

  b.hidden = false;
  setChip("#conn", false, "Photon: sin configurar");
}

refreshDb();
updatePhoton();
loadRooms();

setInterval(function () {
  if (!document.hidden) updatePhoton();
}, 2000);

setInterval(function () {
  if (!document.hidden) { refreshDb(); loadRooms(); }
}, 15000);

</script>
</body>
</html>