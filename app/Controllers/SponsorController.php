<?php

namespace App\Controllers;

use App\Models\SponsorModel;
use CodeIgniter\Controller;

class SponsorController extends BaseController
{
    public function registrar()
    {
        $sponsor = new SponsorModel();

        $validation = \Config\Services::validation();
        $validation->setRules($sponsor->validationRules);

        if ($this->request->getMethod() === 'POST') {
            $titulo = [
                'title' => 'Sponsor',
            ];
            if ($validation->withRequest($this->request)->run()) {
                $sponsor->save($this->request->getPost());
                // Redirigir a la página deseada después de guardar el sponsor
                return redirect()->to('SponsorController/registrar')->with('success', 'Sponsor registrado exitosamente');
            } else {
                // Si la validación falla, mostrar el cuadro de diálogo
                echo '<script>alert("Sponsor ya registrado");</script>';
                return view('direccion/agregar_sponsor', $titulo);
            }
        }
        $titulo = [
            'title' => 'Sponsor',
        ];
        return view('direccion/agregar_sponsor', $titulo);
    }

    public function __construct()
    {
        helper('form');
    }
}
