<?php

namespace App\Models;

use CodeIgniter\Model;

class VentaEntradasModel extends Model
{
    protected $table = 'venta_entradas';
    protected $primaryKey = 'id';

    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['monto', 'nombre_comprador', 'rut_comprador', 'id_partido_fk'];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
}
