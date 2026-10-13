<?php

namespace App\Controllers;

use App\Models\IngresosModel;
use App\Models\IngresoModel;
use App\Models\UsuarioModel;
use App\Models\JugadorModel;
use App\Models\PartidosModel;
use CodeIgniter\Controller;
use App\Models\PagoSocioModel;
use App\Models\CustomModel;
use Dompdf\Dompdf;
use CodeIgniter\API\ResponseTrait;


// Composer is loaded by CodeIgniter's bootstrap.




class SocioController extends BaseController
{
    use ResponseTrait;

    public function inicioSocios()
    {
        //Agregando Titulo a Cada View
        $titulo = [
            'title' => 'Inicio Socios',
        ];
        //Colocar Aqui proximos partidos
        $db = db_connect();
        //PROXIMOS PARTIDOS
        $query9 = $db->query('SELECT
        p.id,
        e_local.nombre AS equipo_local,
        e_visita.nombre AS equipo_visita,
        CONCAT(DAY(p.fecha), " de ", DATE_FORMAT(p.fecha, "%M", "es_ES"), " ", TIME_FORMAT(p.fecha, "%H:%i"), " hrs") AS fecha,
        c.nombre AS cancha
        FROM
            partidos p
        INNER JOIN equipos e_local ON
            e_local.id = p.equipo_local_fk
        INNER JOIN equipos e_visita ON
            e_visita.id = p.equipo_visita_fk
        LEFT JOIN cancha c ON
            p.ubicacion_fk = c.id
        WHERE
            p.fecha >= NOW() -- Filtrar por fechas mayores o iguales a la fecha actual
        ORDER BY
            p.fecha ASC;
        ');

        $results9 = $query9->getResult();

        // Preparar los datos en un formato adecuado
        $data9['results9'] = array();

        foreach ($results9 as $row9) {
            $data9['results9'][] = array(
                'id' => $row9->id,
                'equipo_local' => $row9->equipo_local,
                'equipo_visita' => $row9->equipo_visita,
                'fecha' => $row9->fecha,
                'cancha' => $row9->cancha
            );
        }

        $verData = array_merge($data9, $titulo);

        return view('socio/inicio_socios', $verData);
    }

    public function mostrarJugador()
    {
        // Agregando Titulo a Cada View
        $titulo = [
            'title' => 'Jugadores Socio',
        ];

        $db = db_connect();
        $jugadorModel = new CustomModel($db);
        $jugadores = $jugadorModel->getJugadores();
        $jugadores = array('jugadores' => $jugadores);

        //$jugadorModel = new JugadorModel();
        //$jugadores=$jugadorModel->findAll();
        //$jugadores=array('jugadores'=>$jugadores);
        //return view('admin/admin_equipotecnico_dt', ['jugadores' => $jugadores]);

        $verData = array_merge($jugadores, $titulo);
        return view('socio/ver_jugadores', $verData);
    }

    public function mostrarCampeonatos()
    {
        // Agregando Titulo a Cada View
        $titulo = [
            'title' => 'Campeonatos Socio',
        ];

        $db = db_connect();

        $campeonato = $db->query('SELECT
        e.nombre AS equipo,
        COUNT(*) AS partidos_jugados,
        SUM(CASE
            WHEN (r.goles_local > r.goles_visita AND r.equipo_local_fk = e.id)
                OR (r.goles_visita > r.goles_local AND r.equipo_visita_fk = e.id) THEN 1
            ELSE 0
        END) AS partidos_ganados,
        
        SUM(CASE
            WHEN r.goles_local = r.goles_visita THEN 1
            ELSE 0
        END) AS partidos_empatados,
        
        SUM(CASE
            WHEN (r.goles_local < r.goles_visita AND r.equipo_local_fk = e.id)
                OR (r.goles_visita < r.goles_local AND r.equipo_visita_fk = e.id) THEN 1
            ELSE 0
        END) AS partidos_perdidos,

        SUM(CASE
            WHEN r.equipo_local_fk = e.id THEN r.goles_local
            WHEN r.equipo_visita_fk = e.id THEN r.goles_visita
            ELSE 0
        END) AS goles_a_favor,

        SUM(CASE
            WHEN r.equipo_local_fk = e.id THEN r.goles_visita
            WHEN r.equipo_visita_fk = e.id THEN r.goles_local
            ELSE 0
        END) AS goles_en_contra,
        
        SUM(CASE
            WHEN r.equipo_local_fk = e.id THEN r.goles_local - r.goles_visita
            WHEN r.equipo_visita_fk = e.id THEN r.goles_visita - r.goles_local
            ELSE 0
        END) AS diferencia_goles,

        SUM(CASE
        WHEN (r.goles_local > r.goles_visita AND r.equipo_local_fk = e.id)
            OR (r.goles_visita > r.goles_local AND r.equipo_visita_fk = e.id) THEN 3
        WHEN r.goles_local = r.goles_visita AND r.equipo_local_fk = e.id THEN 1
        WHEN r.goles_local = r.goles_visita AND r.equipo_visita_fk = e.id THEN 1
        ELSE 0
        END) AS puntaje
    
        FROM
            resultados r
        INNER JOIN equipos e ON r.equipo_local_fk = e.id OR r.equipo_visita_fk = e.id
        WHERE
            r.campeonato_id_fk = 1
        GROUP BY
            e.id, e.nombre
        ORDER BY
            puntaje DESC, diferencia_goles desc;');

        $results = $campeonato->getResult();

        // Preparar los datos en un formato adecuado
        $data['results'] = array();

        foreach ($results as $row) {
            $data['results'][] = array(
                'equipo' => $row->equipo,
                'partidos_jugados' => $row->partidos_jugados,
                'partidos_ganados' => $row->partidos_ganados,
                'partidos_empatados' => $row->partidos_empatados,
                'partidos_perdidos' => $row->partidos_perdidos,
                'goles_a_favor' => $row->goles_a_favor,
                'goles_en_contra' => $row->goles_en_contra,
                'diferencia_goles' => $row->diferencia_goles,
                'puntaje' => $row->puntaje
            );
        }

        $viewData = array_merge($data, $titulo);

        return view('socio/ver_campeonatos', $viewData);
    }

    public function verPartidos()
    {
        $partidosModel = new PartidosModel();
        $partidos = $partidosModel->obtenerHistorial();

        // Pasar los datos a la vista
        $data['partidos'] = $partidos;

        // Agregando Titulo a Cada View
        $titulo = [
            'title' => 'Partidos',
        ];

        $verPartidos = array_merge($data, $titulo);

        return view('socio/ver_partidos', $verPartidos);
    }

    public function verEstadisticasJugadores()
    {
        $db = db_connect();
        //JUGADORES MASCULINOS

        $query = $db->query('SELECT
        u.nombres AS nombre,
        u.apellidos AS apellido,
        j.sueldo,
        j.ayuda_economica,
        j.posicion,
        COUNT(g.jugador_id_fk) AS goles,
        CASE
          WHEN l.fecha_fin_lesion > CURDATE() THEN "si"
          ELSE "no"
        END AS jugador_lesionado,
        CASE
          WHEN l.fecha_fin_lesion > CURDATE() THEN l.fecha_inicio_lesion
          ELSE NULL
        END AS fecha_inicio_lesion,
        CASE
          WHEN l.fecha_fin_lesion > CURDATE() THEN l.fecha_fin_lesion
          ELSE NULL
        END AS fecha_fin_lesion
        FROM
            jugadores j
            INNER JOIN usuarios u ON j.id = u.jugador_id_fk
            LEFT JOIN goles g ON j.id = g.jugador_id_fk
            LEFT JOIN lesiones l ON j.id = l.jugador_id_fk
        WHERE
            j.genero = "masculino"
        GROUP BY
            j.id;');

        $masculinos = $query->getResult();

        // Preparar los datos en un formato adecuado
        $data1['masculinos'] = array();

        foreach ($masculinos as $masculino) {
            $data1['masculinos'][] = array(
                'nombre' => $masculino->nombre,
                'apellido' => $masculino->apellido,
                'sueldo' => $masculino->sueldo,
                'ayuda_economica' => $masculino->ayuda_economica,
                'posicion' => $masculino->posicion,
                'goles' => $masculino->goles,
                'jugador_lesionado' => $masculino->jugador_lesionado,
                'fecha_inicio_lesion' => $masculino->fecha_inicio_lesion,
                'fecha_fin_lesion' => $masculino->fecha_fin_lesion
            );
        }
        //JUGADORES FEMENINOS
        $query2 = $db->query('SELECT
        u.nombres AS nombre,
        u.apellidos AS apellido,
        j.sueldo,
        j.ayuda_economica,
        j.posicion,
        COUNT(g.jugador_id_fk) AS goles,
        CASE
          WHEN l.fecha_fin_lesion > CURDATE() THEN "si"
          ELSE "no"
        END AS jugador_lesionado,
        CASE
          WHEN l.fecha_fin_lesion > CURDATE() THEN l.fecha_inicio_lesion
          ELSE NULL
        END AS fecha_inicio_lesion,
        CASE
          WHEN l.fecha_fin_lesion > CURDATE() THEN l.fecha_fin_lesion
          ELSE NULL
        END AS fecha_fin_lesion
        FROM
            jugadores j
            INNER JOIN usuarios u ON j.id = u.jugador_id_fk
            LEFT JOIN goles g ON j.id = g.jugador_id_fk
            LEFT JOIN lesiones l ON j.id = l.jugador_id_fk
        WHERE
            j.genero = "femenino"
        GROUP BY
            j.id;');

        $femeninos = $query2->getResult();

        // Preparar los datos en un formato adecuado
        $data2['femeninos'] = array();

        foreach ($femeninos as $femenino) {
            $data2['femeninos'][] = array(
                'nombre' => $femenino->nombre,
                'apellido' => $femenino->apellido,
                'sueldo' => $femenino->sueldo,
                'ayuda_economica' => $femenino->ayuda_economica,
                'posicion' => $femenino->posicion,
                'goles' => $femenino->goles,
                'jugador_lesionado' => $femenino->jugador_lesionado,
                'fecha_inicio_lesion' => $femenino->fecha_inicio_lesion,
                'fecha_fin_lesion' => $femenino->fecha_fin_lesion
            );
        }

        //Agregando Titulo a Cada View
        $titulo = [
            'title' => 'Estadistica de Jugadores',
        ];

        $verData = array_merge($data1, $data2, $titulo);

        return view('socio/ver_estadisticas.php', $verData);
    }

    public function verReportes()
    {
        //Agregando Titulo a Cada View
        $titulo = [
            'title' => 'Reportes',
        ];

        return view('templates/reportes', $titulo);
    }

    protected UsuarioModel $usuario;
    protected PagoSocioModel $pagoSocio;
    protected IngresosModel $ingresoModel;



    public function verMensualidad()
    {
        $titulo = [
            'title' => 'Mensualidad',
        ];

        $data = []; // Declarar la variable $data

        if ($this->request->getMethod() === 'POST') {
            try {

                // Sesión de usuario para obtener el email
                $email = session('emailUsuario');

                // Carga de modelos
                $this->pagoSocio = new PagoSocioModel();
                $this->usuario = new UsuarioModel();
                $this->ingresoModel = new IngresosModel();

                // Obtener datos de usuario a través de la sesión
                $resultadoUsuario = $this->usuario->buscarUsuarioPorEmail($email);
                $id = $resultadoUsuario->id;
                $montopredeterminado = $this->pagoSocio->select('monto')->first();
                $nombre = $resultadoUsuario->nombres . ' ' . $resultadoUsuario->apellidos;
                $fecha = date('Y-m-d');

                // Verificar si ya existe un pago de mensualidad para el mes actual y el ID de usuario
                $pagosPrevios = $this->ingresoModel->where('concepto', 'mensualidad')
                    ->where('fecha >=', date('Y-m-01'))
                    ->where('fecha <=', date('Y-m-t'))
                    ->where('id_usuario_fk', $id)
                    ->countAllResults();

                // Si ya existe un pago previo, mostrar mensaje y redirigir
                if ($pagosPrevios > 0) {
                    $data['mensaje'] = 'Ya has realizado el pago de la mensualidad para este mes.';
                    $this->pagoSocio = new PagoSocioModel();
                    $montojojo = $this->pagoSocio->select('monto')->first();

                    if ($montojojo !== null) {
                        $data1['monto_a_pagar'] = $montojojo['monto'];
                    } else {
                        $data1['monto_a_pagar'] = '0'; // O un valor predeterminado en caso de que no haya monto disponible
                    }
                    $verData = array_merge($data, $data1, $titulo);
                    return view('socio/ver_mensualidad', $verData);
                }

                $ingresoData = [
                    'monto' => $montopredeterminado,
                    'concepto' => 'mensualidad',
                    'fecha' => $fecha,
                    'id_usuario_fk' => $id,
                    'detalle' => 'Pago de mensualidad'
                ];

                // Validación de datos
                if ($montopredeterminado == null) {
                    $data['mensaje'] = 'No hay un monto predeterminado.';
                    $this->pagoSocio = new PagoSocioModel();
                    $montojojo = $this->pagoSocio->select('monto')->first();

                    if ($montojojo !== null) {
                        $data1['monto_a_pagar'] = $montojojo['monto'];
                    } else {
                        $data1['monto_a_pagar'] = '0'; // O un valor predeterminado en caso de que no haya monto disponible
                    }
                    $verData = array_merge($data, $data1, $titulo);
                    return view('socio/ver_mensualidad', $verData);
                }

                if ($this->ingresoModel->insert($ingresoData)) {

                    $monto = is_array($ingresoData['monto']) ? implode(', ', $ingresoData['monto']) : $ingresoData['monto'];
                    $concepto = is_array($ingresoData['concepto']) ? implode(', ', $ingresoData['concepto']) : $ingresoData['concepto'];
                    $fecha = is_array($ingresoData['fecha']) ? implode(', ', $ingresoData['fecha']) : $ingresoData['fecha'];
                    $id_usuario_fk = is_array($ingresoData['id_usuario_fk']) ? implode(', ', $ingresoData['id_usuario_fk']) : $ingresoData['id_usuario_fk'];
                    $detalle = is_array($ingresoData['detalle']) ? implode(', ', $ingresoData['detalle']) : $ingresoData['detalle'];

                    $dompdf = new Dompdf();
                    //Falta Arreglar Imagen
                    $html = '
                        <html>
                        <head>
                        <style>
                            body {
                                text-align: center;
                            }
                            h1 {
                                text-align: left;
                            }
                            .data-label {
                                text-align: left;
                            }
                            .image-container {
                                position: fixed;
                                bottom: 0;
                                right: 0;
                            }
                            .image-container img {
                                width: 80px; /* Ajusta el ancho de la imagen */
                                height: auto; /* Mantiene la proporción de aspecto */
                            }
                        </style>
                        </head>
                        <body>
                            <h1>Comprobante de Pago</h1>
                            <hr>
                            <h2> Su pago fue realizado con exito</h2>
                            <br>
                            <div class="content">
                                <p><span class="data-label">Monto:</span> ' . $monto . '</p>
                                <p><span class="data-label">Concepto:</span> ' . $concepto . '</p>
                                <p><span class="data-label">Fecha:</span> ' . $fecha .  '</p>
                                <p><span class="data-label">ID Usuario:</span> ' . $id_usuario_fk . '</p>
                                <p><span class="data-label">Detalle:</span> ' . $detalle . '</p>
                            </div>
                            <div class="image-container" style="position: fixed; bottom: 0px; right: 0px;">
                                <img src="../../public/images/losalces.png" alt="Alces"/>
                            </div>
                            
                        </body>
                        </html>';

                    $dompdf->loadHtml($html);
                    $customPaper = array(0, 0, 360, 360);
                    $dompdf->set_paper($customPaper);
                    $dompdf->render();
                    $filename = 'boletalosalces.pdf';
                    $dompdf->stream($filename);

                    $data['mensaje'] = 'Pago realizado exitosamente.';
                    $this->pagoSocio = new PagoSocioModel();
                    $montojojo = $this->pagoSocio->select('monto')->first();

                    if ($montojojo !== null) {
                        $data1['monto_a_pagar'] = $montojojo['monto'];
                    } else {
                        $data1['monto_a_pagar'] = '0'; // O un valor predeterminado en caso de que no haya monto disponible
                    }
                    $verData = array_merge($data, $data1, $titulo);
                    return view('socio/ver_mensualidad', $verData);
                } else {
                    $data['mensaje'] = 'Error de pago.';
                    $verData = array_merge($data, $titulo);
                    return view('socio/ver_mensualidad', $verData);
                }
            } catch (\Exception $e) {
                $data['mensaje'] = 'Ingreso no válido';
                $this->pagoSocio = new PagoSocioModel();
                $montojojo = $this->pagoSocio->select('monto')->first();

                if ($montojojo !== null) {
                    $data1['monto_a_pagar'] = $montojojo['monto'];
                } else {
                    $data1['monto_a_pagar'] = '0'; // O un valor predeterminado en caso de que no haya monto disponible
                }
                $verData = array_merge($data, $data1, $titulo);
                return view('socio/ver_mensualidad', $verData);
            }
        }

        $this->pagoSocio = new PagoSocioModel();
        $montojojo = $this->pagoSocio->select('monto')->first();

        if ($montojojo !== null) {
            $data1['monto_a_pagar'] = $montojojo['monto'];
        } else {
            $data1['monto_a_pagar'] = '0'; // O un valor predeterminado en caso de que no haya monto disponible
        }

        return view('socio/ver_mensualidad', array_merge($titulo, $data1));
    }




    public function verSocioUsuario()
    {
        $titulo = [
            'title' => 'Ver Usuario Socio',
        ];

        // Obtén una instancia del encrypter
        $encrypter = \Config\Services::encrypter();

        // Verifica si la contraseña encriptada está presente en la sesión
        if (session()->has('passwordUsuario')) {
            // Desencripta la contraseña almacenada en la sesión
            $encryptedPassword = session('passwordUsuario');
            $clavebuena = $encrypter->decrypt(hex2bin($encryptedPassword));
        } else {
            // La contraseña encriptada no está presente en la sesión, intenta obtenerla de la otra función
            $clave = $this->request->getPost('password_hash');
            $clavebuena = $encrypter->decrypt(hex2bin($clave));
        }

        // Combina los datos en un solo array
        $verCosas = array_merge(['clavebuena' => $clavebuena], $titulo);
        return view('socio/socio_ver_perfil', $verCosas);
    }


    public function socioverEstadisticas()
    {
        $db = db_connect();
        //JUGADORES MASCULINOS

        $query = $db->query('SELECT
        u.nombres AS nombre,
        u.apellidos AS apellido,
        j.sueldo,
        j.ayuda_economica,
        j.posicion,
        COUNT(g.jugador_id_fk) AS goles,
        CASE
          WHEN l.fecha_fin_lesion > CURDATE() THEN "si"
          ELSE "no"
        END AS jugador_lesionado,
        CASE
          WHEN l.fecha_fin_lesion > CURDATE() THEN l.fecha_inicio_lesion
          ELSE NULL
        END AS fecha_inicio_lesion,
        CASE
          WHEN l.fecha_fin_lesion > CURDATE() THEN l.fecha_fin_lesion
          ELSE NULL
        END AS fecha_fin_lesion
        FROM
            jugadores j
            INNER JOIN usuarios u ON j.id = u.jugador_id_fk
            LEFT JOIN goles g ON j.id = g.jugador_id_fk
            LEFT JOIN lesiones l ON j.id = l.jugador_id_fk
        WHERE
            j.genero = "masculino"
        GROUP BY
            j.id;');

        $masculinos = $query->getResult();

        // Preparar los datos en un formato adecuado
        $data1['masculinos'] = array();

        foreach ($masculinos as $masculino) {
            $data1['masculinos'][] = array(
                'nombre' => $masculino->nombre,
                'apellido' => $masculino->apellido,
                'sueldo' => $masculino->sueldo,
                'ayuda_economica' => $masculino->ayuda_economica,
                'posicion' => $masculino->posicion,
                'goles' => $masculino->goles,
                'jugador_lesionado' => $masculino->jugador_lesionado,
                'fecha_inicio_lesion' => $masculino->fecha_inicio_lesion,
                'fecha_fin_lesion' => $masculino->fecha_fin_lesion
            );
        }

        //JUGADORES FEMENINOS
        $query2 = $db->query('SELECT
        u.nombres AS nombre,
        u.apellidos AS apellido,
        j.sueldo,
        j.ayuda_economica,
        j.posicion,
        COUNT(g.jugador_id_fk) AS goles,
        CASE
          WHEN l.fecha_fin_lesion > CURDATE() THEN "si"
          ELSE "no"
        END AS jugador_lesionado,
        CASE
          WHEN l.fecha_fin_lesion > CURDATE() THEN l.fecha_inicio_lesion
          ELSE NULL
        END AS fecha_inicio_lesion,
        CASE
          WHEN l.fecha_fin_lesion > CURDATE() THEN l.fecha_fin_lesion
          ELSE NULL
        END AS fecha_fin_lesion
        FROM
            jugadores j
            INNER JOIN usuarios u ON j.id = u.jugador_id_fk
            LEFT JOIN goles g ON j.id = g.jugador_id_fk
            LEFT JOIN lesiones l ON j.id = l.jugador_id_fk
        WHERE
            j.genero = "femenino"
        GROUP BY
            j.id;');

        $femeninos = $query2->getResult();

        // Preparar los datos en un formato adecuado
        $data2['femeninos'] = array();

        foreach ($femeninos as $femenino) {
            $data2['femeninos'][] = array(
                'nombre' => $femenino->nombre,
                'apellido' => $femenino->apellido,
                'sueldo' => $femenino->sueldo,
                'ayuda_economica' => $femenino->ayuda_economica,
                'posicion' => $femenino->posicion,
                'goles' => $femenino->goles,
                'jugador_lesionado' => $femenino->jugador_lesionado,
                'fecha_inicio_lesion' => $femenino->fecha_inicio_lesion,
                'fecha_fin_lesion' => $femenino->fecha_fin_lesion
            );
        }

        //Agregando Titulo a Cada View
        $titulo = [
            'title' => 'Ver Estadisticas Socio',
        ];

        $verData = array_merge($data1, $data2, $titulo);

        return view('socio/socio_ver_estadisticas', $verData);
    }

    public function guardaSocioUsuario()
    {
        $usuarioModel = new UsuarioModel();
        $request = \Config\Services::request();
        $encrypter = \Config\Services::encrypter();

        // Obtén la contraseña en claro
        $clavebuena = $request->getPost('password_hash');
        // Encripta la contraseña
        $password = bin2hex($encrypter->encrypt($clavebuena));
        // Actualizar Clave de Session
        $session = session();
        $session->set('passwordUsuario', $password);


        $data = array(
            'nombres' => $request->getPostGet('nombres'),
            'apellidos' => $request->getPostGet('apellidos'),
            'email' => $request->getPostGet('email'),
            'run' => $request->getPostGet('run'),
            'direccion' => $request->getPostGet('direccion'),
            'telefono' => $request->getPostGet('telefono'),
            'password_hash' => $password // Utiliza la contraseña encriptada
        );

        if ($request->getPostGet('id')) {
            $data['id'] = $request->getPostGet('id');
        }

        if ($usuarioModel->save($data) === false) {
            var_dump($usuarioModel->errors());
        }

        // Agregando Titulo a Cada View
        $titulo = [
            'title' => 'Editar Usuario Socio',
            'clavebuena' => $clavebuena // Agrega el valor de $clavebuena al arreglo $titulo
        ];

        return view('socio/socio_ver_perfil', $titulo);
    }

    public function socioverEquipoTecnico()
    {
        $titulo = [
            'title' => 'Equipo Tecnico Socio'
        ];

        $db = db_connect();

        $query = $db->query('SELECT 
        concat(u.nombres, " ", u.apellidos) as nombre,
        et.cargo,
        CASE WHEN e.nombre is NULL THEN "Sin equipo previo" ELSE e.nombre END AS equipo_proviene,
        et.sueldo ,
        et.valor_hora_extra ,
        et.horas_extras_mes ,
        et.sueldo + et.valor_hora_extra * et.horas_extras_mes as total_a_pagar
        FROM equipo_tecnico et
        inner join usuarios u on
        u.equipo_tecnico_id_fk = et.id
        left join equipos e on
        e.id = et.equipo_proviene_fk ;');

        $equipotecnicos  = $query->getResult();

        // Preparar los datos en un formato adecuado
        $data1['equipotecnicos'] = array();

        foreach ($equipotecnicos as $equipotecnico) {
            $data1['equipotecnicos'][] = array(
                'nombre' => $equipotecnico->nombre,
                'cargo' => $equipotecnico->cargo,
                'equipo_proviene' => $equipotecnico->equipo_proviene,
                'sueldo' => $equipotecnico->sueldo,
                'valor_hora_extra' => $equipotecnico->valor_hora_extra,
                'horas_extras_mes' => $equipotecnico->horas_extras_mes,
                'total_a_pagar' => $equipotecnico->total_a_pagar
            );
        }

        $viewData = array_merge($data1, $titulo);

        return view('socio/socio_ver_equipotecnico', $viewData);
    }

    public function historialPagos()
    {

        $titulo = [
            'title' => 'Historial Pagos Socio'
        ];

        $ingresoModel = new IngresoModel();
        $pagos = $ingresoModel->findAll();
        $pagos = array('pagos' => $pagos);

        $viewData = array_merge($pagos, $titulo);

        return view('socio/socio_ver_historialpago', $viewData);
    }

    public function __construct()
    {
        helper('form');
        //helper('session');
        //helper('encryption');
    }

    public function getReporteEstadisticasMovil()
    {
        $db = db_connect();

        // Obtener estadísticas de jugadores masculinos
        $query1 = $db->query('SELECT
            u.nombres AS nombre,
            u.apellidos AS apellido,
            j.sueldo,
            j.ayuda_economica,
            j.posicion,
            COUNT(g.jugador_id_fk) AS goles,
            CASE
                WHEN l.fecha_fin_lesion > CURDATE() THEN "si"
                ELSE "no"
            END AS jugador_lesionado,
            CASE
                WHEN l.fecha_fin_lesion > CURDATE() THEN l.fecha_inicio_lesion
                ELSE NULL
            END AS fecha_inicio_lesion,
            CASE
                WHEN l.fecha_fin_lesion > CURDATE() THEN l.fecha_fin_lesion
                ELSE NULL
            END AS fecha_fin_lesion
            FROM
                jugadores j
                INNER JOIN usuarios u ON j.id = u.jugador_id_fk
                LEFT JOIN goles g ON j.id = g.jugador_id_fk
                LEFT JOIN lesiones l ON j.id = l.jugador_id_fk
            WHERE
                j.genero = "masculino"
            GROUP BY
                j.id;');

        $masculinos = $query1->getResultArray();

        // Obtener estadísticas de jugadores femeninos
        $query2 = $db->query('SELECT
            u.nombres AS nombre,
            u.apellidos AS apellido,
            j.sueldo,
            j.ayuda_economica,
            j.posicion,
            COUNT(g.jugador_id_fk) AS goles,
            CASE
                WHEN l.fecha_fin_lesion > CURDATE() THEN "si"
                ELSE "no"
            END AS jugador_lesionado,
            CASE
                WHEN l.fecha_fin_lesion > CURDATE() THEN l.fecha_inicio_lesion
                ELSE NULL
            END AS fecha_inicio_lesion,
            CASE
                WHEN l.fecha_fin_lesion > CURDATE() THEN l.fecha_fin_lesion
                ELSE NULL
            END AS fecha_fin_lesion
            FROM
                jugadores j
                INNER JOIN usuarios u ON j.id = u.jugador_id_fk
                LEFT JOIN goles g ON j.id = g.jugador_id_fk
                LEFT JOIN lesiones l ON j.id = l.jugador_id_fk
            WHERE
                j.genero = "femenino"
            GROUP BY
                j.id;');

        $femeninos = $query2->getResultArray();

        // Preparar los datos en un formato adecuado
        $reporte = [
            'masculinos' => $masculinos,
            'femeninos' => $femeninos
        ];

        // Retornar la respuesta como un objeto JSON
        return $this->respond($reporte);
    }
}
