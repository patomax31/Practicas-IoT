# Control de accesos NFC con MySQL (InfinityFree)

Archivos:
- `nfc_web.ino`  -> código del Photon (es el mismo de antes, no cambió).
- `index.html`   -> la página. Lee el Photon con getVariable/callFunction.
- `api.php`      -> guarda y lee en MySQL (personas, fotos e historial).
- `config.php`   -> datos de conexión a tu base de datos.

## Cómo funciona
1. La página lee las variables del Photon cada 2 segundos.
2. Cuando ve accesos nuevos en el historial del Photon, los copia a MySQL (sin duplicarlos).
3. Al registrar a alguien, la página lo da de alta en el Photon (para que su tarjeta abra)
   y lo guarda en MySQL con nombre completo y foto.
4. El historial y la lista de personas que ves en la página vienen de MySQL.

IMPORTANTE: los accesos se copian a MySQL solo mientras la página está abierta en algún navegador.
El Photon recuerda los últimos 8 accesos; si pasan más de 8 con la página cerrada, los más
viejos se pierden.

## Publicar en InfinityFree
1. En el panel: "MySQL Databases" -> crea una base de datos (por ejemplo "accesos").
   Anota el host (algo como sql123.infinityfree.com), el nombre completo de la base de datos
   (con prefijo, como if0_12345678_accesos) y el usuario. La contraseña es la de tu cuenta de hosting.
2. Edita `config.php` con esos cuatro datos.
3. En `index.html`, pon tu token de Particle en `var token = "..."` (el deviceId ya trae el tuyo).
4. Sube `index.html`, `api.php` y `config.php` a la carpeta `htdocs` de tu hosting.
   Abre la página desde tu dirección (no con doble clic): `api.php` tiene que estar en el mismo sitio.
5. Las tablas se crean solas al abrir la página. Puedes verlas en phpMyAdmin
   (tablas `personas` y `accesos`).

## Si algo falla
- Chip "Base de datos: error": el mensaje aparece en rojo debajo del formulario. Casi siempre es
  un dato mal escrito en `config.php` (host, nombre de la base de datos, usuario o contraseña).
- Chip "Photon: sin conexión": revisa el token y que el Photon esté en línea.
- Una persona sale con "No está en el Photon": presiona "Enviar al Photon" (pasa si el Photon
  se reinició sin guardar o se cargó código nuevo con la memoria borrada).

## Seguridad
Es un proyecto personal y no tiene contraseña: quien conozca la dirección de tu página puede
ver el historial y modificar personas. Además, tu token de Particle queda visible en el código
de `index.html` para cualquiera que abra la página.
