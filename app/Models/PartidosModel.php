<?php namespace App\Models;

use CodeIgniter\Model;

class PartidosModel extends Model
{
    protected $table = 'partidos';

    public function obtenerHistorial(): array
    {
        // Shared hosting does not grant CREATE VIEW. Run the SELECT directly.
        $sql = file_get_contents(APPPATH . 'Database/Queries/partidos_historial.sql');
        return $this->db->query($sql)->getResultArray();
    }
}
?>
