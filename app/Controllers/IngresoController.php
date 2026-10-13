<?php namespace App\Controllers;

use App\Models\IngresoModel;
use App\Models\SponsorModel;
use CodeIgniter\Controller;

class IngresoController extends BaseController
{
    public function registrar()
    {
        $ingreso = new IngresoModel();
        $sponsor = new SponsorModel();

        $validation = \Config\Services::validation();
        $validation->setRules($ingreso->validationRules);

        $sponsors = $sponsor->findAll();
        $sponsorNames = array_column($sponsors, 'nombre', 'id');
        $sponsorAmounts = array_column($sponsors, 'monto_por_partido', 'id');

        $data = [
            'title' => 'Ingresos Direccion',
            'sponsorNames' => $sponsorNames,
            'sponsorAmounts' => $sponsorAmounts,
        ];

        if ($this->request->getMethod() === 'POST')
        {
                $postData = $this->request->getPost();
                $postData['monto'] = $postData['concepto'] === 'sponsor' ? $sponsorAmounts[$postData['sponsor_id']] : $postData['monto'];
                $postData['detalle'] = $postData['concepto'] === 'sponsor' ? $sponsorNames[$postData['sponsor_id']] : $postData['detalle'];

                $ingreso->save($postData);
                
                // Redirigir a la página deseada después de guardar el ingreso
                return redirect()->to('IngresoController/registrar')->with('success', 'Ingreso registrado exitosamente');
        }
        //SAQUE ESTAS LINEAS PORQUE DABAN ERROR AL ENTRAR, REVISAR (DIEGOS) 
        //else {
        //     // Si la validación falla, mostrar el cuadro de diálogo
        //     echo '<script>alert("Ingreso no válido");</script>';
        //     return view('direccion/agregar_ingreso', $data);
        // }
        return view('direccion/agregar_ingreso', $data);
    }
    public function __construct()
    {
        helper('form');
    }
}












