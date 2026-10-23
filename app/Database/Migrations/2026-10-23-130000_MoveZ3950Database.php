<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MoveZ3950Database extends Migration
{
    public function up()
    {
        $source = $this->db->query("SELECT 1 FROM information_schema.tables WHERE table_schema = 'brapci' AND table_name = 'z3950_queue'")->getRowArray();
        $target = $this->db->query("SELECT 1 FROM information_schema.tables WHERE table_schema = 'brapci_z39.50' AND table_name = 'z3950_queue'")->getRowArray();
        if ($source && $target) throw new \RuntimeException('As duas filas existem; concilie os dados antes de migrar.');
        $sql = file_get_contents(APPPATH . 'Database/SQL/z3950.sql');
        foreach (explode(';', $sql) as $statement) {
            if (trim($statement) !== '') $this->db->query($statement);
        }
        if ($source) {
            $this->db->query('RENAME TABLE `brapci`.`z3950_queue` TO `brapci_z39.50`.`z3950_queue`');
        } elseif (!$target) {
            throw new \RuntimeException('Instale a fila original antes de migrar.');
        }
    }

    public function down()
    {
        throw new \RuntimeException('Reversão manual necessária para preservar a fila e os servidores cadastrados.');
    }
}
