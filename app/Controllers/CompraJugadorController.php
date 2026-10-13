<?php

namespace App\Controllers;

use App\Models\EgresoModel;
use App\Models\JugadorModel;
use App\Models\UsuarioModel;

class CompraJugadorController extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Compra de jugadores',
        ];
        $egreso = new EgresoModel();
        $db = \Config\Database::connect();

        // Obtener equipos destino
        $queryEquiposDestino = "SELECT id, nombre, genero FROM equipos WHERE id = 10 OR id = 14";
        $equiposDestino = $db->query($queryEquiposDestino)->getResultArray();

        $data['equiposDestino'] = $equiposDestino;

        if ($this->request->getMethod() === 'POST') {
            $postData = $this->request->getPost();

            // Equipos origen
            $queryEquiposOrigen = "SELECT id, nombre, genero FROM equipos WHERE id = '{$postData['equipo_origen']}'";
            $equipoOrigen = $db->query($queryEquiposOrigen)->getFirstRow();
            
            // Almacena la id y el genero del equipo origen en la sesión
            session()->set('equipo_origen', $equipoOrigen->id);
            session()->set('genero_origen', $equipoOrigen->genero);
            session()->set('nombres', $postData['nombres']);
            session()->set('apellidos', $postData['apellidos']);
            

            // Datos para la tabla egresos
            $egresoData = [
                'concepto' => 'compra_jugadores',
                'monto' => $postData['monto'],
                'fecha' => date('Y-m-d'),
                'detalle' => 'Compra de jugador ' . $postData['nombres'] . ' ' . $postData['apellidos'] . ' origen ' . $equipoOrigen->nombre,
            ];

            $egreso->save($egresoData);

            return redirect()->to('/CompraJugadorController/registrarJugador')->with('success', 'Compra de jugador registrada exitosamente');
        }

        return view('direccion/compra_jugadores', $data);
    }


    public function obtenerEquiposOrigen($genero)
    {
        $db = \Config\Database::connect();
        $query = "SELECT id, nombre FROM equipos WHERE genero = ? AND id != 10 AND id != 14";
        $equipos = $db->query($query, [$genero])->getResultArray();
        echo json_encode($equipos);
    }


    public function registrarJugador()
    {
        $jugador = new JugadorModel();
        $db = \Config\Database::connect();

        if ($this->request->getMethod() === 'POST') {
            $postData = $this->request->getPost();

            // Obtener los valores de la sesión
            $equipoOrigen = session()->get('equipo_origen');
            $generoOrigen = session()->get('genero_origen');

            $jugadorData = [
                'posicion' => $postData['posicion'],
                'partidos_jugados' => 0,
                'tipo' => $postData['tipo'] == 'Sueldo' ? 'profesional' : 'aficionado',
                'sueldo' => $postData['tipo'] == 'Sueldo' ? $postData['monto'] : null,
                'ayuda_economica' => $postData['tipo'] == 'Ayuda economica' ? $postData['monto'] : null,
                'equipo_proviene_id_fk' => $equipoOrigen,  
                'numero_camiseta' => $postData['numero_camiseta'],
                'genero' => $generoOrigen,
            ];

            $jugador->save($jugadorData);

            // Obtén la ID del último jugador insertado
            $lastInsertedId = $db->insertID();

            session()->set('jugador_id', $lastInsertedId);

            return redirect()->to('/CompraJugadorController/registrarUsuario')->with('success', 'Jugador registrado exitosamente');
        } 

        else {
            $data = [
                'title' => 'Registrar nuevo jugador',
            ];

            return view('direccion/registrar_jugador', $data);
        }
    }

    public function registrarUsuario()
    {
        $user = new UsuarioModel();
        $db = \Config\Database::connect();
        $request = \Config\Services::request();
        $encrypter = \Config\Services::encrypter();

        if ($this->request->getMethod() === 'POST') {
            $postData = $this->request->getPost();

            // Obtener los valores de la sesión
            $nombres = session()->get('nombres');
            $apellidos = session()->get('apellidos');
            $jugador_id = session()->get('jugador_id');

            $userData = [
                'nombres' => $nombres,
                'apellidos' => $apellidos,
                'email' => $postData['email'],
                'run' => $postData['run'],
                'direccion' => $postData['direccion'],
                'telefono' => $postData['telefono'],
                //'password_hash' => password_hash('123456', PASSWORD_BCRYPT),
                //$clave = $this->request->getPost('password_hash'),
                $password = bin2hex($encrypter->encrypt(123456)),
                'password_hash' => $password,
                'rol' => 'jugador',
                'jugador_id_fk' => $jugador_id,
            ];

            $user->save($userData);

            return redirect()->to('/CompraJugadorController/index')->with('success', 'Usuario registrado exitosamente');
        } 

        else {
            $data = [
                'title' => 'Registrar nuevo usuario',
            ];

            return view('direccion/registrar_usuario', $data);
        }
    }

    public function __construct()
    {
        helper('form');
    }
}



