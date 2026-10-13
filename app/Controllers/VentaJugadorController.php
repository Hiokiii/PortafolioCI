<?php

namespace App\Controllers;

use App\Models\IngresoModel;
use App\Models\UsuarioModel;
use App\Models\JugadorModel;
use App\Models\TraspasoModel;
use App\Models\EgresoModel; // Asegúrate de tener este modelo definido en tu proyecto

class VentaJugadorController extends BaseController
{
    public function registrarVentaJugadores()
    {
        $ingreso = new IngresoModel();
        $usuario = new UsuarioModel();
        $jugador = new JugadorModel();
        $traspaso = new TraspasoModel();
        $egreso = new EgresoModel(); // Instanciamos el modelo EgresoModel

        $validation = \Config\Services::validation();
        $validation->setRules($ingreso->getValidationRules());

        $data = [
            'title' => 'Ingresas Venta Jugador Direccion',
        ];

        $db = \Config\Database::connect();

        // Obtener generos de jugadores
        $queryMasculino = "SELECT j.id as jugador_id, u.id as usuario_id, CONCAT(u.nombres, ' ', u.apellidos) AS nombre_jugador
                           FROM jugadores j
                           INNER JOIN usuarios u ON u.jugador_id_fk = j.id
                           WHERE j.genero = 'masculino'";
        $masculinos = $db->query($queryMasculino)->getResultArray();

        $queryFemenino = "SELECT j.id as jugador_id, u.id as usuario_id, CONCAT(u.nombres, ' ', u.apellidos) AS nombre_jugador
                          FROM jugadores j
                          INNER JOIN usuarios u ON u.jugador_id_fk = j.id
                          WHERE j.genero = 'femenino'";
        $femeninos = $db->query($queryFemenino)->getResultArray();

        // Preparar los datos para las combobox
        $jugadorGeneros = [
            'masculino' => $masculinos,
            'femenino' => $femeninos,
        ];

        // Obtener equipos
        $queryEquipos = "SELECT id, nombre FROM equipos WHERE id != 10 AND id != 14";
        $equipos = $db->query($queryEquipos)->getResultArray();

        $data['jugadorGeneros'] = $jugadorGeneros;
        $data['equipos'] = $equipos;

        if ($this->request->getMethod() === 'POST') {
            $postData = $this->request->getPost();
            $postData['concepto'] = 'venta_jugadores';
            $postData['monto'];

            // Obtener el nombre del jugador
            $nombreJugador = array_merge($masculinos, $femeninos);
            $nombreJugador = array_filter($nombreJugador, function($jugador) use ($postData) {
                return $jugador['jugador_id'] == $postData['jugador_id'];
            });
            $nombreJugador = array_values($nombreJugador);
            $nombreJugador = $nombreJugador[0]['nombre_jugador'] ?? 'Desconocido';

            $postData['detalle'] = 'Venta de jugador ' . $nombreJugador;
            $postData['fecha'] = date('Y-m-d');
            $ingreso->save($postData);

            // Datos para los egresos
            $egresoData = [
                [
                    'concepto' => 'impuesto_venta',
                    'monto' => $postData['monto'] * 0.25,
                    'fecha' => date('Y-m-d'),
                    'detalle' => 'Comisión venta jugador ' . $nombreJugador . ' ANFA',
                ],
                [
                    'concepto' => 'impuesto_venta',
                    'monto' => $postData['monto'] * 0.25,
                    'fecha' => date('Y-m-d'),
                    'detalle' => 'Comisión venta jugador ' . $nombreJugador . ' Asociación del Bio Bio',
                ]
            ];
            
            // Guardar los datos de los egresos
            foreach ($egresoData as $egresoItem) {
                $egreso->save($egresoItem);
            }

            // Datos para la tabla traspaso
            $traspasoData = [
                'nombre_jugador' => $nombreJugador,
                'equipo_origen' => $postData['genero'] === 'masculino' ? 'Los Alces FC M' : 'Los Alces FC F',
                'equipo_destino' => $equipos[array_search($postData['equipo_id'], array_column($equipos, 'id'))]['nombre'],
                'fecha_traspaso' => date('Y-m-d'),
                'monto' => $postData['monto']
            ];
            
            // Guardar los datos en la tabla traspaso
            $traspaso->save($traspasoData);

            // Aquí eliminamos al jugador de la tabla jugadores y usuarios
            // $jugador->where('id', $postData['jugador_id'])->delete();
            // $usuario->where('jugador_id_fk', $postData['jugador_id'])->delete();

            return redirect()->to('VentaJugadorController/registrarVentaJugadores')->with('success', 'Venta de jugador registrada exitosamente');
        }

        return view('direccion/venta_jugadores', $data);
    }

    public function __construct()
    {
        helper('form');
    }
}




