<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCommerceTables extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'producto' => ['type' => 'VARCHAR', 'constraint' => 100],
            'talla' => ['type' => 'ENUM', 'constraint' => ['S', 'M', 'L', 'XL', 'XXL'], 'null' => true],
            'precio' => ['type' => 'DECIMAL', 'constraint' => '10,0'],
            'genero' => ['type' => 'ENUM', 'constraint' => ['Hombre', 'Mujer'], 'null' => true],
            'detalle' => ['type' => 'TEXT', 'null' => true],
            'foto' => ['type' => 'LONGBLOB', 'null' => true],
            'stock' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('souvenirs', true);

        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'monto_compra' => ['type' => 'DECIMAL', 'constraint' => '10,0'],
            'articulos_vendidos' => ['type' => 'TEXT'],
            'nombre_comprador' => ['type' => 'VARCHAR', 'constraint' => 100],
            'telefono_comprador' => ['type' => 'INT', 'constraint' => 11],
            'direccion_envio' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'fecha_compra' => ['type' => 'DATETIME'],
            'metodo' => ['type' => 'ENUM', 'constraint' => ['despacho', 'retiro']],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('venta_souvenirs', true);

        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'monto' => ['type' => 'INT', 'constraint' => 11],
            'nombre_comprador' => ['type' => 'VARCHAR', 'constraint' => 100],
            'rut_comprador' => ['type' => 'VARCHAR', 'constraint' => 100],
            'id_partido_fk' => ['type' => 'INT', 'constraint' => 11],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('venta_entradas', true);

        // The prior enum did not allow income rows for commerce transactions.
        $this->forge->modifyColumn('ingresos', [
            'concepto' => ['name' => 'concepto', 'type' => 'VARCHAR', 'constraint' => 30, 'null' => false],
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('venta_entradas', true);
        $this->forge->dropTable('venta_souvenirs', true);
        $this->forge->dropTable('souvenirs', true);

        // Keep ingresos.concepto as VARCHAR so recorded sales are never lost.
    }
}
