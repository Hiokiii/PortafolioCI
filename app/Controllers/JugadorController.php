<?php

namespace App\Controllers;
use App\Models\CustomModel;
use App\Models\UsuarioModel;
use App\Models\PartidosModel;

class JugadorController extends BaseController
{
    public function __construct()
    {
        helper('form');
    }

    public function Dashboard()
    {
        // Agregando Titulo a Cada View
        $titulo = [ 
            'title' => 'Inicio Jugador',
        ];

        //Agrega Ultimos Partidos
        $partidosModel = new PartidosModel();
        $partidos = $partidosModel->obtenerHistorial();

        // Pasar los datos a la vista
        $data['partidos'] = $partidos;


        $verPartidos = array_merge($data, $titulo);

        return view('jugador/inicio_jugador', $verPartidos);
    }
    public function jugadorverCampeonatos()
    {
        // Agregando Titulo a Cada View
        $titulo = [ 
            'title' => 'Campeonatos Jugador',
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

        return view('jugador/jugador_ver_campeonatos', $viewData);
    }
    public function jugadorverEstadisticas()
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

        // Agregando Titulo a Cada View
        $titulo = [ 
            'title' => 'Estadisticas Jugador',
        ];

        $verData = array_merge($data1, $data2, $titulo);

        return view('jugador/jugador_ver_estadisticas', $verData);
    }
    public function jugadorverJugadores()
    {
        // Agregando Titulo a Cada View
        $titulo = [ 
            'title' => 'Jugadores Jugador',
        ];

        $db = db_connect();
        $jugadorModel = new CustomModel($db);
        $jugadores = $jugadorModel->getJugadores();
        $jugadores=array('jugadores' => $jugadores);

        //$jugadorModel = new JugadorModel();
        //$jugadores=$jugadorModel->findAll();
        //$jugadores=array('jugadores'=>$jugadores);
        //return view('admin/admin_equipotecnico_dt', ['jugadores' => $jugadores]);

        $verData= array_merge($jugadores, $titulo);

        return view('jugador/jugador_ver_jugadores', $verData);
    }
    public function jugadorverPartidos()
    {
        // Agregando Titulo a Cada View
        $titulo = [ 
            'title' => 'Partidos Jugador',
        ];

        return view('jugador/jugador_ver_partidos', $titulo);
    }
    public function jugadorverEquipoTecnico()
    {
        $titulo = [
            'title' => 'Equipo Tecnico Jugador'
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

        return view('jugador/jugador_ver_equipotecnico', $viewData);

    }
    public function verJugadorUsuario()
    {
        $titulo = [
            'title' => 'Ver Usuario Jugador',
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
        return view('jugador/jugador_ver_perfil', $verCosas);
    }
    public function guardaJugadorUsuario()
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
            'title' => 'Editar Usuario Jugador',
            'clavebuena' => $clavebuena // Agrega el valor de $clavebuena al arreglo $titulo
        ];
        
        return view('jugador/jugador_ver_perfil', $titulo);
    }

}
