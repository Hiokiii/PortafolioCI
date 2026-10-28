<?php

namespace App\Models;

use CodeIgniter\Model;

class VentaSouvenirsModel extends Model
{
    protected $table = 'venta_souvenirs';
    protected $primaryKey = 'id';

    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['monto_compra', 'articulos_vendidos', 'nombre_comprador', 'telefono_comprador','direccion_envio','fecha_compra','metodo'];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
}
