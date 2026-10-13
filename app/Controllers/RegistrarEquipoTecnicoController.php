<?php

namespace App\Controllers;

use App\Models\EquipoTecnicoModel;
use App\Models\UsuarioModel;

class RegistrarEquipoTecnicoController extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Registrar nuevo miembro del equipo técnico',
        ];
        return view('direccion/registrar_nuevo_miembro', $data);
    }

    public function registrar()
    {
        $db = \Config\Database::connect();

        $miembro = new EquipoTecnicoModel();

        if ($this->request->getMethod() === 'POST') {
            $postData = $this->request->getPost();
            $miembroData = [
                'cargo' => $postData['cargo'],
                'equipo_proviene_id_fk' => $postData['equipo_origen'] === 'sin_equipo' ? null : $postData['equipo_origen'],
                'sueldo' => $postData['sueldo'],
                'valor_hora_extra' => $postData['valor_hora_extra'],
                'horas_extra_mes' => 0,
            ];

            $miembro->save($miembroData);

            $lastInsertedId = $db->insertID();

            session()->set('equipo_tecnico_id', $lastInsertedId);

            return redirect()->to('/RegistrarEquipoTecnicoController/registrarUsuario')->with('success', 'Miembro del equipo técnico registrado exitosamente');
        }
    }

    public function obtenerEquipos()
    {
        $db = \Config\Database::connect();
        $query = "SELECT id, nombre FROM equipos WHERE id != 10 AND id != 14";
        $equipos = $db->query($query)->getResultArray();
        echo json_encode($equipos);
    }

    public function registrarUsuario()
    {
        $db = \Config\Database::connect();
        $usuario = new UsuarioModel();
        $encrypter = \Config\Services::encrypter();

        if ($this->request->getMethod() === 'POST') {
            $postData = $this->request->getPost();
            $equipo_tecnico_id = session()->get('equipo_tecnico_id');

            $usuarioData = [
                'nombres' => $postData['nombres'],
                'apellidos' => $postData['apellidos'],
                'email' => $postData['email'],
                'run' => $postData['run'],
                'direccion' => $postData['direccion'],
                'telefono' => $postData['telefono'],
                $password = bin2hex($encrypter->encrypt(123456)),
                'password_hash' => $password,
                'rol' => 'equipo_tecnico',
                'equipo_tecnico_id_fk' => $equipo_tecnico_id,
            ];

            $usuario->save($usuarioData);

            return redirect()->to('/RegistrarEquipoTecnicoController/index')->with('success', 'Usuario registrado exitosamente');
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
