<?php

namespace App\Controllers;

use App\Models\JugadorModel;
use App\Models\EquipoModel;
use App\Models\UsuarioModel;
use App\Models\EquipoTecnicoModel;
use App\Models\SocioModel;
use App\Models\CustomModel;

class AdminDashboard extends BaseController
{
    public function Dashboard()
    {
        $titulo = [
            'title' => 'Ver Usuario Admin',
        ];

        return view('admin/admin_dashboard', $titulo);
    }

    public function equipoDatabase()
    {
        $titulo = [
            'title' => 'Ver Equipos Admin',
        ];

        $equipoModel = new EquipoModel();
        $equipos = $equipoModel->findAll();
        $equipos = array('equipos' => $equipos);

        $viewData = array_merge($titulo, $equipos);

        return view('admin/admin_equipo_dt', $viewData);
    }

    public function __construct()
    {
        helper(['form', 'encryption']);

        //$this->encryption = \Config\Services::encryption();
    }

    public function usuarioDatabase()
    {
        $titulo = [
            'title' => 'Ver Usuarios Admin',
        ];

        $usuarioModel = new UsuarioModel();
        $encrypter = \Config\Services::encrypter();
        $usuarios = $usuarioModel->findAll();
        $usuarios = array('usuarios' => $usuarios);

        foreach ($usuarios['usuarios'] as &$usuario) {
            $encryptedPassword = $usuario['password_hash'];
            $usuario['clavebuena'] = $encrypter->decrypt(hex2bin($encryptedPassword));
        }

        $data = ['usuarios' => $usuarios['usuarios']];

        $viewData = array_merge($titulo, $data);

        return view('admin/admin_usuarios_dt', $viewData);
    }

    public function jugadorDataBase()
    {

        $titulo = [
            'title' => 'Ver Jugadores Admin',
        ];

        $jugadorModel = new JugadorModel();
        $jugadores = $jugadorModel->findAll();
        $jugadores = array('jugadores' => $jugadores);

        $viewData = array_merge($titulo, $jugadores);
        return view('admin/admin_jugador_dt', $viewData);
    }

    public function equipotecnicoDatabase()
    {

        $db = db_connect();
        $equipotecnicoModel = new CustomModel($db);
        $equipots = $equipotecnicoModel->getEquipoTecnico();

        $data = [
            'equipots' => $equipots,
            'title' => 'Ver Equipo Tecnico Admin'
        ];

        return view('admin/admin_equipotecnico_dt', $data);

        //$equipotecnicoModel = new EquipoTecnicoModel();
        //$equipots=$equipotecnicoModel->findAll();
        //$equipots=array('equipots'=>$equipots);
        //return view('admin/admin_equipotecnico_dt', $equipots);
    }
    public function socioDatabase()
    {
        $db = db_connect();
        $socioModel = new CustomModel($db);
        $socios = $socioModel->getSocios();

        $data = [
            'socios' => $socios,
            'title' => 'Ver Socio Admin'
        ];
        
        return view('admin/admin_socio_dt', $data);

        //$socioModel = new SocioModel();
        //$socios=$socioModel->findAll();
        //$socios=array('socios'=>$socios);
        //return view('admin/admin_socio_dt', $socios);
    }

    public function guardaJugador()
    {


        $jugadorModel = new JugadorModel();
        $request = \Config\Services::request();
        $data = array(
            'posicion' => $request->getPostGet('posicion'),
            'partidos_jugados' => $request->getPostGet('partidos_jugados'),
            'tipo' => $request->getPostGet('tipo'),
            'sueldo' => $request->getPostGet('sueldo'),
            'ayuda_economica' => $request->getPostGet('ayuda_economica'),
            'numero_camiseta' => $request->getPostGet('numero_camiseta'),
            'equipo_proviene_id_fk' => $request->getPostGet('equipo_proviene_id_fk')

        );
        if ($request->getPostGet('id')) {
            $data['id'] = $request->getPostGet('id');
        }
        if ($jugadorModel->save($data) === false) {
            var_dump($jugadorModel->errors());
        }

        $titulo = [
            'title' => 'Ver Jugadores Admin',
        ];
        
        $jugadores = $jugadorModel->findAll();
        $jugadores = array('jugadores' => $jugadores);
        $viewData = array_merge($titulo, $jugadores);
        return view('admin/admin_jugador_dt', $viewData);
    }
    public function guardaUsuario()
    {

        $usuarioModel = new UsuarioModel();
        $request = \Config\Services::request();
        $encrypter = \Config\Services::encrypter();
        $data = array(
            'nombres' => $request->getPostGet('nombres'),
            'apellidos' => $request->getPostGet('apellidos'),
            'email' => $request->getPostGet('email'),
            'run' => $request->getPostGet('run'),
            'direccion' => $request->getPostGet('direccion'),
            'telefono' => $request->getPostGet('telefono'),
            //'password_hash' => $request->getPostGet('password_hash'),
            $clave = $this->request->getPost('password_hash'),
            $password = bin2hex($encrypter->encrypt($clave)),
            'password_hash' => $password,
            'rol' => $request->getPostGet('rol')
        );
        if ($request->getPostGet('id')) {
            $data['id'] = $request->getPostGet('id');
        }
        if ($usuarioModel->save($data) === false) {
            var_dump($usuarioModel->errors());
        }
        $usuarios = $usuarioModel->findAll();
        $usuarios = array('usuarios' => $usuarios);

        foreach ($usuarios['usuarios'] as &$usuario) {
            $encryptedPassword = $usuario['password_hash'];
            $usuario['clavebuena'] = $encrypter->decrypt(hex2bin($encryptedPassword));
        }

        $titulo = [
            'title' => 'Ver Usuarios Admin',
        ];
    
        $data = ['usuarios' => $usuarios['usuarios']];
        $viewData = array_merge($titulo, $data);

        return view('admin/admin_usuarios_dt', $viewData);
    }
    public function guardaEquipo()
    {

        $equipoModel = new EquipoModel();
        $request = \Config\Services::request();
        $data = array(
            'nombre' => $request->getPostGet('nombre'),
            'genero' => $request->getPostGet('genero'),
            'division_id_fk' => $request->getPostGet('division_id_fk'),

        );
        if ($request->getPostGet('id')) {
            $data['id'] = $request->getPostGet('id');
        }
        if ($equipoModel->save($data) === false) {
            var_dump($equipoModel->errors());
        }
        $titulo = [
            'title' => 'Ver Equipos Admin',
        ];
        
        $equipos = $equipoModel->findAll();
        $equipos = array('equipos' => $equipos);
        $viewData = array_merge($titulo, $equipos);
        return view('admin/admin_equipo_dt', $viewData);
    }
    public function guardaEquipoTecnico()
    {
        $equipotecnicoModel = new EquipoTecnicoModel();
        $request = \Config\Services::request();
        $data = array(
            'cargo' => $request->getPostGet('cargo'),
            'equipo_proviene_fk' => $request->getPostGet('equipo_proviene_fk'),
            'sueldo' => $request->getPostGet('sueldo'),
            'valor_hora_extra' => $request->getPostGet('valor_hora_extra'),
            'horas_extras_mes' => $request->getPostGet('horas_extras_mes')

        );
        if ($request->getPostGet('id')) {
            $data['id'] = $request->getPostGet('id');
        }
        if ($equipotecnicoModel->save($data) === false) {
            var_dump($equipotecnicoModel->errors());
        }
        $equipots = $equipotecnicoModel->findAll();
        $equipots = array('equipots' => $equipots);
        $viewData = [
            'equipots' => $equipots,
            'title' => 'Ver Equipo Tecnico Admin'
        ];
        return view('admin/admin_equipotecnico_dt', $viewData);
    }
    public function guardaSocio()
    {

        $socioModel = new SocioModel();
        $request = \Config\Services::request();
        $data = array(
            'fecha_pago' => $request->getPostGet('fecha_pago'),

        );
        if ($request->getPostGet('id')) {
            $data['id'] = $request->getPostGet('id');
        }
        if ($socioModel->save($data) === false) {
            var_dump($socioModel->errors());
        }
        $socios = $socioModel->findAll();
        $socios = array('socios' => $socios);
        $viewData = [
            'socios' => $socios,
            'title' => 'Ver Socio Admin'
        ];
        return view('admin/admin_socio_dt', $viewData);
    }

    public function borrarJugador()
    {
        $jugadorModel = new JugadorModel();
        $request = \Config\Services::request();
        $id = $request->getPostGet('id');
        $jugadorModel->delete($id);

        $titulo = [
            'title' => 'Ver Jugadores Admin',
        ];
        
        $jugadores = $jugadorModel->findAll();
        $jugadores = array('jugadores' => $jugadores);
        $viewData = array_merge($titulo, $jugadores);
        return view('admin/admin_jugador_dt', $viewData);

        // $jugadores = $jugadorModel->findAll();
        // $jugadores = array('jugadores' => $jugadores);
        // return view('admin/admin_jugador_dt', $jugadores);
    }
    public function borrarUsuario()
    {
        $usuarioModel = new UsuarioModel();
        $request = \Config\Services::request();
        $encrypter = \Config\Services::encrypter();
        $id = $request->getPostGet('id');
        $usuarioModel->delete($id);

        $usuarios = $usuarioModel->findAll();
        $usuarios = array('usuarios' => $usuarios);

        foreach ($usuarios['usuarios'] as &$usuario) {
            $encryptedPassword = $usuario['password_hash'];
            $usuario['clavebuena'] = $encrypter->decrypt(hex2bin($encryptedPassword));
        }

        $titulo = [
            'title' => 'Ver Usuarios Admin',
        ];
    
        $data = ['usuarios' => $usuarios['usuarios']];
        $viewData = array_merge($titulo, $data);

        return view('admin/admin_usuarios_dt', $viewData);

        // $usuarios = $usuarioModel->findAll();
        // $usuarios = array('usuarios' => $usuarios);
        // return view('admin/admin_usuarios_dt', $usuarios);
    }
    public function borrarEquipo()
    {
        $equipoModel = new EquipoModel();
        $request = \Config\Services::request();
        $id = $request->getPostGet('id');
        $equipoModel->delete($id);

        $titulo = [
            'title' => 'Ver Equipos Admin',
        ];
        
        $equipos = $equipoModel->findAll();
        $equipos = array('equipos' => $equipos);
        $viewData = array_merge($titulo, $equipos);
        return view('admin/admin_equipo_dt', $viewData);

        // $equipos = $equipoModel->findAll();
        // $equipos = array('equipos' => $equipos);
        // return view('admin/admin_equipo_dt', $equipos);
    }
    public function borrarEquipoTecnico()
    {
        $equipotecnicoModel = new EquipoTecnicoModel();
        $request = \Config\Services::request();
        $id = $request->getPostGet('id');
        $equipotecnicoModel->delete($id);
        
        $equipots = $equipotecnicoModel->findAll();
        $equipots = array('equipots' => $equipots);
        $viewData = [
            'equipots' => $equipots,
            'title' => 'Ver Equipo Tecnico Admin'
        ];
        return view('admin/admin_equipotecnico_dt', $viewData);
    }
    public function borrarSocio()
    {
        $socioModel = new SocioModel();
        $request = \Config\Services::request();
        $id = $request->getPostGet('id');
        $socioModel->delete($id);
        $socios = $socioModel->findAll();
        $socios = array('socios' => $socios);
        $viewData = [
            'socios' => $socios,
            'title' => 'Ver Socio Admin'
        ];
        return view('admin/admin_socio_dt', $viewData);

        // $socios = $socioModel->findAll();
        // $socios = array('socios' => $socios);
        // return view('admin/admin_socio_dt', $socios);
    }
    public function verAdminUsuario()
    {
        $titulo = [
            'title' => 'Ver Usuario Admin',
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
        return view('admin/admin_ver_perfil', $verCosas);
    }
    public function guardaAdminUsuario()
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
            'title' => 'Editar Usuario Admin',
            'clavebuena' => $clavebuena // Agrega el valor de $clavebuena al arreglo $titulo
        ];
        
        return view('admin/admin_ver_perfil', $titulo);
    }
}
