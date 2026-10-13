<?php

namespace App\Controllers;

use App\Models\JugadorModel;
use App\Models\UsuarioModel;

class RegistrarJugadorController extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Registrar nuevo jugador',
        ];
        return view('direccion/registrar_nuevo_jugador', $data);
    }

    public function registrar()
    {
        $db = \Config\Database::connect();

        $jugador = new JugadorModel();

        if ($this->request->getMethod() === 'POST') {
            $postData = $this->request->getPost();
            $jugadorData = [
                'posicion' => $postData['posicion'],
                'partidos_jugados' => 0,
                'tipo' => $postData['tipo'],
                'sueldo' => $postData['tipo'] == 'profesional' ? $postData['monto'] : null,
                'ayuda_economica' => $postData['tipo'] == 'aficionado' ? $postData['monto'] : null,
                'equipo_proviene_id_fk' => $postData['equipo_origen'] === 'sin_equipo' ? null : $postData['equipo_origen'],
                'numero_camiseta' => $postData['numero_camiseta'],
                'genero' => $postData['genero'],
            ];

            $jugador->save($jugadorData);

            $lastInsertedId = $db->insertID();

            session()->set('jugador_id', $lastInsertedId);

            return redirect()->to('/RegistrarJugadorController/registrarUsuario')->with('success', 'Jugador registrado exitosamente');
        }
    }

    public function obtenerEquiposPorGenero($genero)
    {
        $db = \Config\Database::connect();
        $query = "SELECT id, nombre FROM equipos WHERE genero = ? AND id != 10 AND id != 14";
        $equipos = $db->query($query, [$genero])->getResultArray();
        echo json_encode($equipos);
    }

    public function registrarUsuario()
    {
        $db = \Config\Database::connect();
        $request = \Config\Services::request();
        $encrypter = \Config\Services::encrypter();
        $usuario = new UsuarioModel();


        if ($this->request->getMethod() === 'POST') {
            $postData = $this->request->getPost();
            $jugador_id = session()->get('jugador_id');

            $usuarioData = [
                'nombres' => $postData['nombres'],
                'apellidos' => $postData['apellidos'],
                'email' => $postData['email'],
                'run' => $postData['run'],
                'direccion' => $postData['direccion'],
                'telefono' => $postData['telefono'],
                $password = bin2hex($encrypter->encrypt(123456)),
                'password_hash' => $password,
                'rol' => 'jugador',
                'jugador_id_fk' => $jugador_id,
            ];

            $usuario->save($usuarioData);

            return redirect()->to('/RegistrarJugadorController/index')->with('success', 'Usuario registrado exitosamente');
        }
        else{
            $data = [
                'title' => 'Registrar nuevo usuario',
            ];

            return view('direccion/registrar_nuevo_usuario', $data); 
        }
    }

    public function __construct()
    {
        helper('form');
    }
}


