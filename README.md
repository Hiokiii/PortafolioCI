

# PORTAFOLIO CASO 8 - CLUB DE FUTBOL LOS ALCES

Portafolio, realizacion de aplicacion WEB de un club de futbol.

## Tabla de Contenidos

- [Requisitos](#requisitos)
- [Instalación](#instalación)
- [Uso](#uso)
- [Contribución](#contribución)
- [Licencia](#licencia)
- [Contacto](#contacto)

## Requisitos

Actualización para InfinityFree: PHP 8.4+, CodeIgniter 4.7.5 y Dompdf 3.1.6.
Instalar las versiones del lock con `composer install`, en lugar de actualizar
dependencias sin revisar. Ver `deploy/INFINITYFREE.md` para el estado de la
migración y los pasos de despliegue. Las instrucciones de XAMPP siguientes
corresponden a la instalación original.

- Composer Update (Tener composer instalado)
- Revisar Archivo .env app.baseURL = 'http://localhost:TUPUERTOENXAMPP/PortafolioCI/'
- XAMPP Control Panel v3.0.0 o LASTEST y sacarle al comentario ';' a extension=intl ( XAMPP -> APACHE -> Config -> PHP.ini)
- Subir Archivo SQL (club_de_futbol.sql) a MySQL 

## Instalación

Las dependencias no se guardan en Git. Después de clonar, ejecutar
`composer install` y, si se necesitan los recursos de npm, `npm ci`.
Copiar `.env.example` a `.env` y completar la configuración local.
Para InfinityFree, usar `deploy/infinityfree.env.example` en la copia de subida.
Composer instala Dompdf dentro de `vendor`; no se necesita el antiguo directorio
`dompdf` de la raíz. Mantener `composer.lock` y `package-lock.json` versionados.

Clonar Repositorio (dejar en la carpeta htdocs)   directorio:..\xampp\htdocs\PROYECTO AQUI
Revisar Las dependencias


## Uso

-Iniciar sesion
Usuarios 
- Admin= di.servietti@duocuc.cl contraseña: 123455
- Socio= rangamind@gmail.com contraseña: 123456
- Direccion = aperegrini@gmail.com contraseña: 123456
- Equipo Tecnico = jsampaoli@gmail.com contraseña: 123456
- Jugador = chupete.suazo@gmail.com contraseña: 123456

Se pueden Crear Socios en registro.
Se pueden revisar demas usuarios desde admin.

## Contribución

- Diego Servietti
- Ricardo Garrido
- Diego Barros

## Licencia

Este proyecto está bajo la Licencia Patito de Goma
© 2023 MiEmpresa S.A. Todos los derechos reservados.

## Contacto

Cualquier Integrante del grupo
di.servietti@duocuc.cl
ricardogarrido398@gmail.com
dieg.barros@duocuc.cl


## Módulo de compras

El módulo público de tienda y venta de entradas usa las tablas `souvenirs`, `venta_souvenirs` y `venta_entradas`. En desarrollo, aplica la migración con `php spark migrate` y luego importa `app/Database/Queries/compras.sql` para cargar el catálogo demo. En InfinityFree/phpMyAdmin, importa ese mismo SQL después del dump principal; crea las tablas que falten, amplía `ingresos.concepto` y carga productos e imágenes del catálogo, sin copiar ventas ni datos personales.