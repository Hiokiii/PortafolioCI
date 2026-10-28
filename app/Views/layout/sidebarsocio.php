<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- RECORDAR AGREGAR TITULO A LOS CONTROLADORES (ej EN INICIO SOCIOS)-->
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.2.1/css/fontawesome.min.css">
    <!-- Bootstrap ICONS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!--CSS SIDEBAR -->
    <link rel="stylesheet" href="<?= base_url('public/css/sidebar_socio.css') ?>">

    <!-- Datatables CSS-->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css">
    <!-- DataTables Buttons CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">

</head>

<body>
    <div class="container-fluid">
        <div class="row flex-nowrap">
            <aside class="col-12 col-sm-2 col-xl-2 px-sm-2 px-0 bg-dark my-class">
                <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-2 text-white min-vh-100">
                    <a href="/">
                        <img src="<?= base_url() ?>/public/images/losalces.png" class="img-fluid" alt="Image description">

                    </a>
                    <a href="/" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                        <span class="hide-on-small fs-5 d-none d-sm-inline">Club Los Alces F.C.</span>
                    </a>

                    <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start" id="menu">
                        <li class="nav-item">
                            <a href="<?php echo base_url('InicioSocios'); ?>" class="nav-link align-middle px-0">
                                <i class="fs-4 bi-house"></i> <span class="hide-on-small ms-1 d-none d-sm-inline">Inicio</span>
                            </a>
                        </li>
                        <li>
                            <a href="#submenu1" data-bs-toggle="collapse" class="nav-link px-0 align-middle">
                                <i class="fs-4 bi-speedometer2"></i> <span class="hide-on-small ms-1 d-none d-sm-inline">Club</span> </a>
                            <ul class="collapse nav flex-column ms-1" id="submenu1" data-bs-parent="#menu">
                                <li class="w-100">
                                    <a href="<?php echo base_url('VerJugadores'); ?>" class="nav-link px-0"> <i class="fs-4 bi-person-badge"></i> <span class=" hide-on-small d-none d-sm-inline">Jugadores</span></a>
                                </li>
                                <li>
                                    <a href="<?php echo base_url('VerCampeonatos') ?>" class="nav-link px-0"> <i class="fs-4 bi-trophy"></i><span class=" hide-on-small d-none d-sm-inline">Campeonatos</span></a>
                                </li>
                                <li>
                                    <a href="<?php echo base_url('EquipoTecnicoSocio') ?>" class="nav-link px-0"> <i class="fs-4 bi-person-gear"></i><span class=" hide-on-small d-none d-sm-inline">Equipo Tecnico</span></a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="<?php echo base_url('VerPartidos'); ?>" class="nav-link align-middle px-0">
                                <i class="fs-4 bi-clock-history"></i> <span class="hide-on-small ms-1 d-none d-sm-inline">Partidos Jugados</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url('EstadisticasSocio'); ?>" class="nav-link align-middle px-0">
                                <i class="fs-4 bi-card-list"></i> <span class="hide-on-small ms-1 d-none d-sm-inline">Estadisticas Jugadores</span>
                            </a>
                        </li>
                        <li>
                            <a href="#submenu2" data-bs-toggle="collapse" class="nav-link px-0 align-middle">
                                <i class="fs-4 bi-coin"></i> <span class="hide-on-small ms-1 d-none d-sm-inline">Pagos</span> </a>
                            <ul class="collapse nav flex-column ms-1" id="submenu2" data-bs-parent="#menu">
                                <li class="w-100">
                                    <a href="<?php echo base_url('VerMensualidad'); ?>" class="nav-link px-0"> <i class="fs-4 bi-card-list"></i> <span class="hide-on-small d-none d-sm-inline">Pagar Mensualidad</span></a>
                                </li>
                                <li>
                                    <a href="<?php echo base_url('HistorialPagos'); ?>" class="nav-link px-0"><i class="fs-4 bi-card-list"></i> <span class="hide-on-small d-none d-sm-inline">Historial Pagos</span></a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                    <hr>
                    <div class="dropdown pb-4">
                        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2c/Default_pfp.svg/1200px-Default_pfp.svg.png" alt="hugenerd" width="30" height="30" class="rounded-circle">
                            <span class=" hide-on-small d-none d-sm-inline mx-1"><?= session('nombreUsuario') ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
                            <!--<li><a class="dropdown-item" href="#">New project...</a></li>-->
                            <li><a class="dropdown-item" href="#">Configuración</a></li>
                            <li><a class="dropdown-item" href="<?php echo base_url('PerfilSocio'); ?>">Ver Perfil</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" onclick="cerrarSesion()">Cerrar Sesión</a></li>
                        </ul>
                    </div>
                </div>
            </aside>
            <div class="col content">
                <?= $this->renderSection('contenido') ?>
            </div>
        </div>
    </div>
    <footer class="text-center text-black">
        <div class="container pt-4">
            <!-- Social media -->
            <section class="mb-4">
                <a class="btn btn-link btn-floating btn-lg text-dark m-1" href="#!" role="button" data-mdb-ripple-color="dark"><i class="bi bi-facebook"></i></a>
                <a class="btn btn-link btn-floating btn-lg text-dark m-1" href="#!" role="button" data-mdb-ripple-color="dark"><i class="bi bi-twitter"></i></a>
                <!-- <a class="btn btn-link btn-floating btn-lg text-dark m-1" href="#!" role="button" data-mdb-ripple-color="dark"><i class="bi bi-google"></i></a> -->
                <a class="btn btn-link btn-floating btn-lg text-dark m-1" href="#!" role="button" data-mdb-ripple-color="dark"><i class="bi bi-instagram"></i></a>
                <!-- <a class="btn btn-link btn-floating btn-lg text-dark m-1" href="#!" role="button" data-mdb-ripple-color="dark"><i class="bi bi-linkedin"></i></a> -->
                <!-- <a class="btn btn-link btn-floating btn-lg text-dark m-1" href="#!" role="button" data-mdb-ripple-color="dark"><i class="bi bi-github"></i></a> -->
                <p>© 2023 MiEmpresa S.A. Todos los derechos reservados.</p>
                <p>
                    Contáctanos: <a class="text-dark"> info@miempresa.com</a>
                </p>
            </section>
        </div>

    </footer>
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/vfs_fonts.js"></script>


    <script>
        $(document).ready(function() {
            var table = $('#example').DataTable({
                responsive: true,
                lengthChange: false,
                autoWidth: false,
                dom: 'Bfrtip', // Specify the buttons to be displayed
                buttons: ['copy', 'excel', 'pdf', 'print'] // Include the required buttons
            });
        });

        $(document).ready(function() {
            var table = $('#example1').DataTable({
                responsive: true,
                lengthChange: false,
                autoWidth: false,
                dom: 'Bfrtip', // Specify the buttons to be displayed
                buttons: ['copy', 'excel', 'pdf', 'print'] // Include the required buttons
            });
        });

        $(document).ready(function() {
            var table = $('#example2').DataTable({
                responsive: true,
                lengthChange: false,
                autoWidth: false,
                dom: 'Bfrtip', // Specify the buttons to be displayed
                buttons: ['copy', 'excel', 'pdf', 'print'] // Include the required buttons
            });
        });

        $(document).ready(function() {
            var table = $('#example4').DataTable({
                responsive: true,
                lengthChange: false,
                autoWidth: false,
                dom: 'Bfrtip', // Specify the buttons to be displayed
                buttons: ['copy', 'excel', 'pdf', 'print'] // Include the required buttons
            });
        });
    </script>

    <!-- Cerrar Sesion -->
    <script type="text/javascript">
        function cerrarSesion() {
            window.location.href = "<?php echo base_url('logout'); ?>";
        }
    </script>
</body>

</html>