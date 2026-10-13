<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use CodeIgniter\Controller;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\RequestInterface;


$autoload['libraries'] = ['session'];

class Home extends BaseController
{
    public function index()
    {
        $db = db_connect();
        //EQUIPOS Y GOLES DEL PARTIDO
        $query = $db->query('SELECT
        e1.nombre AS equipo_local,
        SUM(CASE WHEN g.jugador_id_fk IS NOT NULL THEN 1 ELSE 0 END) AS goles_equipo_local,
        e2.nombre AS equipo_visita,
        SUM(CASE WHEN g.jugador_id_fk IS NULL THEN 1 ELSE 0 END) AS goles_equipo_visita,
        CONCAT(UPPER(DATE_FORMAT(p.fecha, "%b")), " ", DATE_FORMAT(p.fecha, "%d", "es_ES"), " / ", TIME_FORMAT(p.fecha, "%H:%i"), " hrs") AS fecha
        FROM
            partidos p
        INNER JOIN equipos e1 ON
            p.equipo_local_fk = e1.id
        INNER JOIN equipos e2 ON
            p.equipo_visita_fk = e2.id
        LEFT JOIN goles g ON
            p.id = g.partido_id_fk
        WHERE
            p.id = (
                SELECT
                    id
                FROM
                    partidos
                WHERE
                    fecha <= NOW()
                ORDER BY
                    fecha DESC
                LIMIT 1
            );
        ');

        $results = $query->getResult();

        $data['results'] = $results;
        //GOLES EQUIPO LOCAL   
        $query2 = $db->query('SELECT
        e1.nombre AS nombre_equipo,
        CONCAT(j.numero_camiseta, " ", u.nombres, " ", u.apellidos) AS nombre_jugador,
        g.minuto AS minuto_gol
        FROM
            partidos p
        INNER JOIN equipos e1 ON
            p.equipo_local_fk = e1.id
        LEFT JOIN goles g ON
            p.id = g.partido_id_fk
        LEFT JOIN jugadores j ON
            j.id = g.jugador_id_fk
        LEFT JOIN usuarios u ON
            j.id = u.jugador_id_fk 
        INNER JOIN (
            SELECT
                MAX(fecha) AS ultima_fecha
            FROM
                partidos
            WHERE
                fecha <= NOW()
        ) AS sub ON p.fecha = sub.ultima_fecha
        WHERE
            g.jugador_visita IS null
        order by g.minuto asc;');

        $results2 = $query2->getResult();

        // Preparar los datos en un formato adecuado
        $data2['results2'] = array();

        foreach ($results2 as $row) {
            $data2['results2'][] = array(
                'nombre_equipo' => $row->nombre_equipo,
                'minuto_gol' => $row->minuto_gol,
                'nombre_jugador' => $row->nombre_jugador
            );
        }
        //GOLES EQUIPO VISITANTE
        $query6 = $db->query(' SELECT
        e2.nombre AS nombre_equipo,
        g.jugador_visita AS nombre_jugador,
        g.minuto AS minuto_gol
        FROM
            partidos p
        INNER JOIN equipos e2 ON
            p.equipo_visita_fk = e2.id
        LEFT JOIN goles g ON
            p.id = g.partido_id_fk
        INNER JOIN (
            SELECT
                MAX(fecha) AS ultima_fecha
            FROM
                partidos
            WHERE
                fecha <= NOW()
        ) AS sub ON p.fecha = sub.ultima_fecha
        WHERE
            g.jugador_id_fk  IS null
        order by g.minuto asc;');

        $results6 = $query6->getResult();

        // Preparar los datos en un formato adecuado
        $data6['results6'] = array();

        foreach ($results6 as $row) {
            $data6['results6'][] = array(
                'nombre_equipo' => $row->nombre_equipo,
                'minuto_gol' => $row->minuto_gol,
                'nombre_jugador' => $row->nombre_jugador
            );
        }

        //CAMBIOS EQUIPO LOCAL
        $query3 = $db->query('SELECT
        CONCAT(j2.numero_camiseta ," ",u_sal.nombres, " ", u_sal.apellidos) AS jugador_saliente,
        CONCAT(j.numero_camiseta ," ",u_ent.nombres, " ", u_ent.apellidos) AS jugador_entrante,
        c.minuto
        FROM
            partidos p
        INNER JOIN cambios c ON
            p.id = c.partido_fk
        LEFT JOIN usuarios u_sal ON
            c.jugador_saliente_fk = u_sal.jugador_id_fk
        LEFT JOIN usuarios u_ent ON
            c.jugador_entrante_fk = u_ent.jugador_id_fk
        left join jugadores j on
            c.jugador_entrante_fk = j.id 
        left join jugadores j2 on
            c.jugador_saliente_fk = j2.id 
        INNER JOIN (
            SELECT
                MAX(fecha) AS ultima_fecha
            FROM
                partidos
            WHERE
                fecha <= NOW()
        ) AS sub ON p.fecha = sub.ultima_fecha
        ORDER BY
            c.minuto ASC;');

        $results3 = $query3->getResult();

        // Preparar los datos en un formato adecuado
        $data3['results3'] = array();

        foreach ($results3 as $row) {
            $data3['results3'][] = array(
                'jugador_saliente' => $row->jugador_saliente,
                'jugador_entrante' => $row->jugador_entrante,
                'minuto' => $row->minuto
            );
        }
        //CAMBIOS EQUIPO VISITANTE
        $query4 = $db->query('SELECT
        e.nombre AS nombre_equipo,
        ce.nombre_jugador_saliente,
        ce.nombre_jugador_entrante,
        ce.minuto
        FROM
            cambios_externo ce
        INNER JOIN partidos p ON
            ce.partido_id_fk = p.id
        LEFT JOIN equipos e ON
            e.id = p.equipo_visita_fk
        WHERE
            p.fecha <= NOW() -- Filtrar por fecha inferior o igual a la fecha actual
            AND p.fecha = (
                SELECT MAX(p2.fecha)
                FROM partidos p2
                WHERE p2.fecha <= NOW() -- Filtrar por fecha inferior o igual a la fecha actual
            )
        ORDER BY
            ce.minuto asc;');

        $results4 = $query4->getResult();

        // Preparar los datos en un formato adecuado
        $data4['results4'] = array();

        foreach ($results4 as $row) {
            $data4['results4'][] = array(
                'nombre_equipo' => $row->nombre_equipo,
                'jugador_saliente' => $row->nombre_jugador_saliente,
                'jugador_entrante' => $row->nombre_jugador_entrante,
                'minuto' => $row->minuto
            );
        }
        //TARJETAS VISITA
        $query5 = $db->query('SELECT
        CONCAT(u.nombres, " ", u.apellidos) AS jugador,
        tp.tarjeta,
        tp.minuto,
        el.nombre AS equipo
        FROM
            tarjetas_partido tp
        LEFT JOIN jugadores j ON
            j.id = tp.jugador_fk
        LEFT JOIN usuarios u ON
            u.jugador_id_fk = j.id
        LEFT JOIN partidos p ON
            p.id = tp.partido_fk
        LEFT JOIN equipos el ON
            el.id = p.equipo_local_fk
        LEFT JOIN equipos ev ON
            ev.id = p.equipo_visita_fk
        WHERE
            p.fecha = (
                SELECT
                    MAX(fecha)
                FROM
                    partidos
                where fecha<= now()
            )
            AND tp.jugador_fk IS NOT null
        order by tp.minuto asc;');

        $results5 = $query5->getResult();

        // Preparar los datos en un formato adecuado
        $data5['results5'] = array();

        foreach ($results5 as $row) {
            $data5['results5'][] = array(
                'jugador' => $row->jugador,
                'tarjeta' => $row->tarjeta,
                'minuto' => $row->minuto,
                'equipo' => $row->equipo
            );
        }
        //TARJETAS EQUIPO VISITA
        $query7 = $db->query('SELECT
        CASE
            WHEN tp.jugador_fk IS NOT NULL THEN CONCAT(u.nombres, " ", u.apellidos)
            ELSE tp.jugador_externo
        END AS jugador,
        tp.tarjeta,
        tp.minuto,
        ev.nombre AS equipo
        FROM
            tarjetas_partido tp
        LEFT JOIN jugadores j ON
            j.id = tp.jugador_fk
        LEFT JOIN usuarios u ON
            u.jugador_id_fk = j.id
        LEFT JOIN partidos p ON
            p.id = tp.partido_fk
        LEFT JOIN equipos el ON
            el.id = p.equipo_local_fk
        LEFT JOIN equipos ev ON
            ev.id = p.equipo_visita_fk
        WHERE
            p.fecha = (
                SELECT
                    MAX(fecha)
                FROM
                    partidos
                where fecha<= now()
            )
            AND tp.jugador_fk IS null
        order by tp.minuto asc;');

        $results7 = $query7->getResult();

        // Preparar los datos en un formato adecuado
        $data7['results7'] = array();

        foreach ($results7 as $row) {
            $data7['results7'][] = array(
                'jugador' => $row->jugador,
                'tarjeta' => $row->tarjeta,
                'minuto' => $row->minuto,
                'equipo' => $row->equipo
            );
        }

        $query8 = $db->query('SELECT
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

        $results8 = $query8->getResult();

        // Preparar los datos en un formato adecuado
        $data8['results8'] = array();

        foreach ($results8 as $row) {
            $data8['results8'][] = array(
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

        $viewData = array_merge($data, $data2, $data3, $data4, $data5, $data6, $data7, $data8);

        echo view('templates/header');
        echo view('home/home', $viewData);
        echo view('templates/footer');
    }


    public function homesocios()
    {
        return view('templates/header') . view('home/home_socios') . view('templates/footer');
    }

    public function actividadesEspeciales()
    {
        return view('templates/header') . view('home/actividades_especiales') . view('templates/footer');
    }

    public function proximoPartido()
    {
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
            p.fecha >= NOW() 
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

        return view('templates/header') . view('home/proximo_partido', $data9) . view('templates/footer');
    }

    public function homeiniciosesion()
    {
        return view('home/iniciar_sesion');
    }

    public function homeregistro()
    {
        return view('home/registrarse');
    }

    protected UsuarioModel $usuario;
    //INICIAR SESION
    public function validarIngreso()

    {

        $email1 = $this->request->getPost("email");
        $id = $this->request->getPost("id");
        if (filter_var($email1, FILTER_VALIDATE_EMAIL)) {
            $email = filter_var($email1, FILTER_SANITIZE_EMAIL);
            $this->usuario = new UsuarioModel();
            $resultadoUsuario = $this->usuario->buscarUsuarioPorEmail($email);
        } else {
            $data = ['tipo' => 'danger', 'mensaje' => 'Usuario  y/o clave invalido'];
            return view(('home/iniciar_sesion'), $data);
        }

        if ($resultadoUsuario) {
            $encrypter = \Config\Services::encrypter();
            $claveBD = $encrypter->decrypt(hex2bin($resultadoUsuario->password_hash));
            $clave = $this->request->getPost("password");
            $nombres = explode(' ', $resultadoUsuario->nombres);
            $apellidos = explode(' ', $resultadoUsuario->apellidos);

            $primerNombre = $nombres[0];
            $primerApellido = $apellidos[0];
            if ($clave == $claveBD) {
                $data = [
                    "nombreUsuario" => $resultadoUsuario->nombres . ' ' . $resultadoUsuario->apellidos,
                    "emailUsuario" => $resultadoUsuario->email,
                    "idUsuario" => $resultadoUsuario->id,
                    "nombresUsuario" => $resultadoUsuario->nombres,
                    "apellidosUsuario" => $resultadoUsuario->apellidos,
                    "runUsuario" => $resultadoUsuario->run,
                    "direccionUsuario" => $resultadoUsuario->direccion,
                    "telefonoUsuario" => $resultadoUsuario->telefono,
                    "passwordUsuario" => $resultadoUsuario->password_hash


                ];
                session()->set($data);

                //Buscar rol del usuario

                $userModel = new UsuarioModel();
                $query = $userModel->select('rol')->where('email', $email1)->get()->getRow()->rol;

                // Control de vistas por Rol

                if ($query) {
                    switch ($query) {
                        case  'administrador':
                            //redirecciona a vista de administrador
                            return redirect()->to(base_url('AdminDashboard'));

                        case 'direccion':
                            //redirecciona a vista de direccion 
                            return redirect()->to(base_url('DireccionHome'));

                        case 'jugador':
                            //redirecciona a vista de jugador
                            return redirect()->to(base_url('InicioJugador'));

                        case 'entrenador':
                            //redirecciona a vista de entrenador
                            return redirect()->to(base_url('InicioEquipoTecnico'));

                        case 'equipo_tecnico':
                            //redirecciona a vista de equipo_tecnico
                            return redirect()->to(base_url('InicioEquipoTecnico'));

                        case 'socio':
                            //redirecciona a vista de socio

                            return redirect()->to(base_url('InicioSocios'));
                    }
                }

                // return redirect()->to(base_url() . '/Home');
            } else {
                $data = ['tipo' => 'danger', 'mensaje' => 'Usuario  y/o clave invalido'];
                return view(('home/iniciar_sesion'),  $data);
            }
        } else {
            // print_r($_POST);
            $data = ['tipo' => 'danger', 'mensaje' => 'Usuario  y/o clave invalido'];
            return view(('home/iniciar_sesion'), $data);
        }
    }

    //inicio de sesion aplicacion movil
    public function validarIngresoMovil()

    {

        $email1 = $this->request->getPost("email");
        $id = $this->request->getPost("id");
        if (filter_var($email1, FILTER_VALIDATE_EMAIL)) {
            $email = filter_var($email1, FILTER_SANITIZE_EMAIL);
            $this->usuario = new UsuarioModel();
            $resultadoUsuario = $this->usuario->buscarUsuarioPorEmail($email);
        } else {
            $data = ['tipo' => 'danger', 'mensaje' => 'Usuario  y/o clave invalido'];
            return view(('home/iniciar_sesion'), $data);
        }

        if ($resultadoUsuario) {
            $encrypter = \Config\Services::encrypter();
            $claveBD = $encrypter->decrypt(hex2bin($resultadoUsuario->password_hash));
            $clave = $this->request->getPost("password");
            $nombres = explode(' ', $resultadoUsuario->nombres);
            $apellidos = explode(' ', $resultadoUsuario->apellidos);

            $primerNombre = $nombres[0];
            $primerApellido = $apellidos[0];
            if ($clave == $claveBD) {
                $data = [
                    "nombreUsuario" => $resultadoUsuario->nombres . ' ' . $resultadoUsuario->apellidos,
                    "emailUsuario" => $resultadoUsuario->email,
                    "idUsuario" => $resultadoUsuario->id,


                ];
                session()->set($data);

                //Buscar rol del usuario

                $userModel = new UsuarioModel();
                $query = $userModel->select('rol')->where('email', $email1)->get()->getRow()->rol;

                // Control de vistas por Rol

                if ($query) {
                    switch ($query) {

                        case 'socio':
                            //redirecciona a vista de socio

                            return $this->response->setJSON([
                                'status' => 'success',

                            ]);
                    }
                }

                // return redirect()->to(base_url() . '/Home');
            } else {
                $data = ['tipo' => 'danger', 'mensaje' => 'Usuario  y/o clave invalido'];
                return view(('home/iniciar_sesion'),  $data);
            }
        } else {
            // print_r($_POST);
            $data = ['tipo' => 'danger', 'mensaje' => 'Usuario  y/o clave invalido'];
            return view(('home/iniciar_sesion'), $data);
        }
    }

    //Cerrar Sesion (Aplicacion Movil)
    public function cerrarSesion()
    {
        session()->destroy();
        return redirect()->to(base_url('Home'));
    }

    public function cerrarSesionMovil()
    {
        session()->destroy();
        return $this->response->setJSON([
            'status' => 'success',

        ]);
    }


    //REGISTRARSE
    public function register()
    {
        $data = [];

        if ($this->request->getMethod() === 'POST') {


            // Validar los datos del formulario
            $rules = [
                'nombres' => 'required',
                'apellidos' => 'required',
                'email' => 'required|valid_email',
                'run' => 'required',
                'direccion' => 'required',
                'telefono' => 'required',
                'password_hash' => 'required'
            ];
            try {
                if ($this->validate($rules)) {
                    $encrypter = \Config\Services::encrypter();
                    // Obtener los datos del formulario
                    $firstName = $this->request->getPost('nombres');
                    $lastName = $this->request->getPost('apellidos');
                    $email = $this->request->getPost('email');
                    $run = $this->request->getPost('run');
                    $address = $this->request->getPost('direccion');
                    $phone = $this->request->getPost('telefono');
                    $clave = $this->request->getPost('password_hash');
                    $password = bin2hex($encrypter->encrypt($clave));
                    // Guardar los datos en la base de datos
                    $userModel = new UsuarioModel();
                    $userData = [
                        'nombres' => $firstName,
                        'apellidos' => $lastName,
                        'email' => $email,
                        'run' => $run,
                        'direccion' => $address,
                        'telefono' => $phone,
                        'password_hash' => $password,
                        'rol' => 'socio'
                    ];
                    $database = \Config\Database::connect();
                    $queryEmail = $database->table('usuarios')->where('email', $email)->get();



                    if ($queryEmail->getNumRows() > 0) {
                        $data = ['tipo' => 'danger', 'mensaje' => 'El correo electrónico está duplicado '];
                        return view(('home/registrarse'), $data);
                    } else {
                        try {
                            $userModel->insert($userData);
                            return redirect()->to('IniciarSesion');
                        } catch (\Exception $e) {
                            $data = ['tipo' => 'danger', 'mensaje' => 'Error al registrar usuario '];
                            return view(('home/registrarse'), $data);
                        }
                    }


                    // Redirigir al usuario a una página de éxito o mostrar un mensaje
                    // de éxito en la misma página.

                } else {
                    $data = ['tipo' => 'danger', 'mensaje' => 'Datos invalidos '];
                    return view(('home/registrarse'), $data);
                }
            } catch (\Exception $data) {
                $data = ['tipo' => 'danger', 'mensaje' => 'Error al registrar usuario '];
                return view(('home/registrarse'), $data);
            }
            // Cargar la vista del formulario de registro

        }
        return view('home/registrarse', $data);
    }
}
