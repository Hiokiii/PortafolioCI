# Demo de Los Alces en InfinityFree

## Estado de la revisión

La aplicación es PHP con CodeIgniter 4.7.5 y Dompdf 3.1.6, con una base
MySQL/MariaDB. No necesita ejecutar Node.js en el servidor. Las dependencias
de Composer deben instalarse localmente y subirse con la aplicación.

El comprobador del usuario confirmó PHP 8.4.25 y todas las extensiones.
La actualización se verificó localmente con PHP 8.4.26: 11 pruebas, 262
aserciones, comprobación de sintaxis de 174 archivos y auditoría de Composer
sin vulnerabilidades conocidas. Se adaptaron index.php, spark, preload.php,
la configuración y las comparaciones del método HTTP de los formularios.
No se ha publicado la demo ni probado sus flujos con la base de InfinityFree.

Cuenta y destino confirmados por el usuario:

- Dominio: losalces.infinityfree.me
- Carpeta del sitio: htdocs
- Host MySQL: sql109.infinityfree.com
- Usuario: if0_43157870
- Base de datos: if0_43157870_los_alces
- Puerto: 3306

La cuenta no permite CREATE VIEW. PartidosModel consulta directamente el SELECT
de app/Database/Queries/partidos_historial.sql. El SQL de demo ya no crea vistas.
Para completar una importación interrumpida, usar dist/reparar_importacion_demo.sql:
consulta information_schema antes de añadir cada índice, AUTO_INCREMENT y
clave foránea. Puede ejecutarse de nuevo y no elimina tablas ni vuelve a insertar
usuarios. El archivo antiguo completar_importacion_demo.sql presupone que faltan
todas las claves y no debe utilizarse si ya se crearon algunas.
Para una base nueva y vacía usar dist/los_alces_demo.sql.

## Paso inmediato: verificar el hosting

La comprobación inicial ya se completó. Eliminar `htdocs/check-host.php`.
Que writable aparezca ausente es normal antes de subir el proyecto.

La aplicación necesita intl, mbstring, mysqli y openssl. Dompdf utiliza dom;
gd puede ser necesario para imágenes. Si falta una extensión obligatoria,
confirmar su disponibilidad con el hosting antes de continuar.

## Pendientes antes de una demo pública

1. Preparar la copia de despliegue con las dependencias ya actualizadas:
   `composer install --no-dev --optimize-autoloader`. No subir las herramientas
   locales de .tools ni las dependencias de desarrollo.
2. Verificar los flujos de cada rol con datos de demo. Auto Routing Legacy ya
   está desactivado: ControllerRoutes.php declara los enlaces antiguos con
   filtros por rol. AgregarSponsor y ReporteEstadisticasMovil están protegidos.
   Las pruebas verifican los destinos de las rutas y rechazan accesos sin sesión;
   no sustituyen la prueba de inicio de sesión real con la base importada.
3. Activar CSRF y adaptar los formularios y solicitudes AJAX. Actualmente está
   desactivado. Hay acciones destructivas mediante GET que deben migrarse a POST.
4. Migrar las contraseñas: se cifran y descifran, con una clave incrustada en
   Config/Encryption.php. Los perfiles incluso recuperan la contraseña desde la
   sesión. Usar password_hash/password_verify y retirar la recuperación de la
   contraseña. No cambiar la clave existente sin migrar los usuarios: impediría
   iniciar sesión con las contraseñas cifradas del SQL.
5. Crear una base de demo con datos ficticios. El dump original contiene datos
   de usuarios; no publicarlo ni subir el SQL dentro de htdocs. Importar una
   copia saneada en phpMyAdmin, seleccionando la base existente. El dump no
   contiene CREATE DATABASE ni USE, por lo que no necesita crear otra base.
6. Comprobar las consultas DATE_FORMAT con tercer argumento es_ES: son de
   MariaDB y fallarán si esta cuenta usa MySQL. También revisar datos deportivos
   de 2023: las pantallas de próximos partidos pueden quedar vacías.
7. Se corrigieron los destinos de registro técnico y obtenerEquipos. Se retiró
   boletaMensualidad, que apuntaba a un método inexistente; el comprobante PDF
   se genera desde verMensualidad. El archivo valorUfModel.php declara ValorUtmModel; si se utiliza, debe
   coincidir el nombre de archivo con la clase para autoload en Linux.

## Recursos y límites

InfinityFree limita PHP/HTML/JS a 1 MB por archivo, .htaccess a 10 kB y otros
archivos a 10 MB. Los siguientes recursos locales exceden esos límites:

- public/Background5.mp4: 19.1 MB; no incluirlo en la subida. El video tiene
  ahora una imagen poster para mostrar un fondo cuando no se cargue. Para
  mantener video, usar un proveedor de video permitido por el hosting.
- public/plugins/pdfmake/pdfmake.js: 2.77 MB.
- public/plugins/pdfmake/pdfmake.min.js: 1.35 MB.
- public/plugins/jqvmap/maps/jquery.vmap.usa.counties.js: 1.14 MB.

Las plantillas utilizan pdfmake desde CDN; se cambió también la plantilla
admin a la misma URL que las otras. No subir las dos copias locales grandes.
No se encontraron referencias al mapa usa.counties en las vistas; omitirlo.
Revisar los límites de todos los archivos del paquete final, incluido vendor.

## Configuración y subida, después de resolver los pendientes

Copiar `deploy/infinityfree.env.example` como `.env` en la copia de despliegue
y completar la contraseña MySQL desde el panel. No enviar la contraseña por
chat ni subir la plantilla sin completar. La contraseña es la de MySQL de la
cuenta de hosting, no necesariamente la del acceso al área de clientes.

Usar inicialmente el esquema de URL que esté disponible. Instalar el certificado
SSL y cambiar app.baseURL a https antes de utilizar cuentas en la demo pública.
Después activar cookies seguras y la redirección a HTTPS.

Mantener esta estructura directamente dentro de htdocs, sin una carpeta
PortafolioCI adicional:

    htdocs/
      index.php
      .htaccess
      .env
      app/
      vendor/
      public/
      writable/

El proyecto usa index.php en la raíz y recursos bajo public/, por lo que no
hay que subir solamente public/ ni convertirlo al layout de un proyecto nuevo
sin adaptar las rutas. InfinityFree exige guardar los archivos bajo htdocs;
el .htaccess actualizado bloquea acceso HTTP a los directorios privados.

No incluir .git, node_modules, tests, el directorio dompdf duplicado, deploy,
archivos SQL ni logs/sesiones del equipo local. Crear writable/cache,
writable/logs, writable/session y writable/uploads vacíos y escribibles.
Mantener las protecciones .htaccess de app y writable. No aplicar permisos 777
como solución automática.

## Verificación de la demo

- Home, inicio de sesión y registro; recursos CSS/JS/imágenes sin errores.
- Inicio y cierre de sesión de cada rol con usuarios ficticios.
- Solicitudes a pantallas y métodos privados sin sesión deben rechazarse.
- Formularios, AJAX y exportación PDF; Linux distingue mayúsculas en archivos.
- No exponer .env, vendor, writable, .git ni dumps SQL por URL (403 o 404).
- Revisar los logs privados ante errores 500 y mantener production en el servidor.

PHP y Composer portátiles se instalaron en .tools desde sus sitios oficiales,
con verificación de hashes. PHP se ejecuta con .tools/php/php.exe y Composer
con .tools/php/php.exe .tools/composer.phar. Se ejecutaron las pruebas y el lint;
la conexión y los flujos contra la base de InfinityFree siguen pendientes.
Añadir .env a .gitignore no elimina una copia ya rastreada ni su historial.
La limpieza retiró .env del índice junto con vendor, node_modules y el antiguo
checkout dompdf. Los archivos locales se conservan; el historial anterior sigue
existiendo. No se modificó ni se incorporó al despliegue el contenido del .env local.

Referencias:

- https://www.infinityfree.com/
- https://forum.infinityfree.com/t/why-are-my-files-deleted-after-uploading-them/49310
- https://forum.infinityfree.com/t/how-to-migrate-your-website-to-infinityfree/49315
