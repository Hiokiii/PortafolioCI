<?php

namespace App\Controllers;

use App\Models\EgresoModel;
use App\Models\UsuarioModel;
use App\Models\JugadorModel;
use App\Models\DirigenteModel;
use App\Models\EquipoTecnicoModel;
use CodeIgniter\Controller;

class EgresoController extends BaseController
{
    public function registrarPagoJugadores()
    {
        $egreso = new EgresoModel();
        $usuario = new UsuarioModel();
        $jugador = new JugadorModel();

        $validation = \Config\Services::validation();
        $validation->setRules($egreso->getValidationRules());

        $data = [
            'title' => 'Egresos',
        ];

        // Obtener los datos para las combobox
        $db = \Config\Database::connect();

        // Obtener generos de jugadores
        $queryMasculino = "SELECT CONCAT(u.nombres, ' ', u.apellidos) AS nombre_jugador, j.sueldo
                           FROM jugadores j
                           INNER JOIN usuarios u ON u.jugador_id_fk = j.id
                           WHERE j.genero = 'masculino' AND j.sueldo IS NOT NULL";
        $masculinos = $db->query($queryMasculino)->getResultArray();

        $queryFemenino = "SELECT CONCAT(u.nombres, ' ', u.apellidos) AS nombre_jugador, j.sueldo
                          FROM jugadores j
                          INNER JOIN usuarios u ON u.jugador_id_fk = j.id
                          WHERE j.genero = 'femenino' AND j.sueldo IS NOT NULL";
        $femeninos = $db->query($queryFemenino)->getResultArray();

        // Preparar los datos para las combobox
        $jugadorGeneros = [
            'masculino' => $masculinos,
            'femenino' => $femeninos,
        ];

        $data['jugadorGeneros'] = $jugadorGeneros;

        if ($this->request->getMethod() === 'POST') {
            if ($validation->withRequest($this->request)->run()) {
                $postData = $this->request->getPost();
                $postData['concepto'] = 'sueldo_jugadores';
                $postData['monto'];
                $postData['detalle'] = 'Pago de sueldo jugador ' . $postData['jugador_id'];
                $postData['fecha'] = date('Y-m-d');
                $egreso->save($postData);
                return redirect()->to('EgresoController/registrarPagoJugadores')->with('success', 'Pago de sueldo registrado exitosamente');

            } else {
                echo '<script>alert("Egreso no válido");</script>';
            }
        }

        return view('direccion/pago_jugadores', $data);
    }

    public function PagarSueldoEquipoTecnico()
    {
        $egreso = new EgresoModel();
        $usuario = new UsuarioModel();
        $equipoTecnico = new EquipoTecnicoModel();

        $validation = \Config\Services::validation();
        $validation->setRules($egreso->getValidationRules());

        $data = [
            'title' => 'Egresos Equipo Técnico',
        ];

        // Obtener los datos para las combobox
        $db = \Config\Database::connect();

        // Obtener equipo técnico
        $queryTecnico = "SELECT CONCAT(u.nombres, ' ', u.apellidos) AS nombre_tecnico, et.sueldo, et.horas_extras_mes, et.valor_hora_extra, et.horas_extras_mes * et.valor_hora_extra as total_horas_extra, et.horas_extras_mes * et.valor_hora_extra + et.sueldo as total_a_pagar
                         FROM equipo_tecnico et
                         INNER JOIN usuarios u ON u.equipo_tecnico_id_fk = et.id";
        $tecnicos = $db->query($queryTecnico)->getResultArray();

        $data['tecnicos'] = $tecnicos;

        if ($this->request->getMethod() === 'POST') {

                $postData = $this->request->getPost();
                $postData['concepto'] = 'sueldo_e_tecnico';
                $postData['monto'] = $postData['total_a_pagar'];
                $postData['detalle'] = 'Sueldo de equipo tecnico ' . $postData['tecnico_id'];
                $postData['fecha'] = date('Y-m-d');
                $egreso->save($postData);
                return redirect()->to('EgresoController/PagarSueldoEquipoTecnico')->with('success', 'Pago de sueldo registrado exitosamente');
        }

        return view('direccion/agregar_egreso_tecnico', $data);
    }

    public function PagarSueldoDirigente()
    {
        $egreso = new EgresoModel();

    $data = [
        'title' => 'Egresos Dirigentes',
    ];

    // Obtener los datos para las combobox
    $db = \Config\Database::connect();

    // Obtener dirigentes
    $queryDirigentes = "SELECT CONCAT(u.nombres, ' ', u.apellidos) AS nombre_dirigente, d.sueldo
                        FROM dirigente d
                        INNER JOIN usuarios u ON u.direccion_id_fk = d.id";
    $dirigentes = $db->query($queryDirigentes)->getResultArray();

    $data['dirigentes'] = $dirigentes;

    if ($this->request->getMethod() === 'POST') {

            $postData = $this->request->getPost();
            $postData['concepto'] = 'sueldo_dirigentes';
            $postData['monto'] = $postData['sueldo'];
            $postData['detalle'] = 'Pago de sueldo dirigente ' . $postData['dirigente_id'];
            $postData['fecha'] = date('Y-m-d');
            $egreso->save($postData);
            return redirect()->to('EgresoController/PagarSueldoDirigente')->with('success', 'Pago de sueldo registrado exitosamente');
    }

        return view('direccion/agregar_egreso_dirigente', $data);
    }

    public function pagarMensualidadAnfa()
{
    $egreso = new EgresoModel();

    // Obtener los datos para las combobox
    $db = \Config\Database::connect();

    // Obtener el valor de UTM
    $queryUtm = "SELECT vu.valor AS valor_utm, vu.valor * 2 AS total_a_pagar
                 FROM valor_utm vu";
    $utm = $db->query($queryUtm)->getRow();

    // Verificar si ya se ha realizado un pago este mes
    $queryPago = "SELECT *
                  FROM egresos
                  WHERE concepto = 'pago_mensualidad'
                  AND YEAR(fecha) = YEAR(CURRENT_DATE())
                  AND MONTH(fecha) = MONTH(CURRENT_DATE())";
    $pagoRealizado = $db->query($queryPago)->getRow();

    $data = [
        'title' => 'Pago Mensualidad ANFA',
        'utm' => $utm,
        'pagoRealizado' => $pagoRealizado != null
    ];

    if ($this->request->getMethod() === 'POST') {
        if ($pagoRealizado == null) {
            $postData = $this->request->getPost();
            $postData['concepto'] = 'pago_mensualidad';
            $postData['monto'] = $utm->total_a_pagar;
            $postData['detalle'] = 'Pago de mensualidad ANFA mes de ' . date('F Y');
            $postData['fecha'] = date('Y-m-d');
            $egreso->save($postData);
            return redirect()->to('EgresoController/pagarMensualidadAnfa')->with('success', 'Pago de mensualidad registrado exitosamente');
        } else {
            return redirect()->to('EgresoController/pagarMensualidadAnfa')->with('error', 'Pago de mensualidad ya realizado este mes');
        }
    }

    return view('direccion/agregar_pago_mensualidad_anfa', $data);
}



    public function __construct()
    {
        helper('form');
    }
}



