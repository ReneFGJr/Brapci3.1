<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class LattesCurriculo extends Migration
{
    protected $DBGroup = 'lattes';

    public function up()
    {
        $this->forge->addField([
            'id_cv' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],

            'cv_name' => [
                'type'       => 'VARCHAR',
                'constraint'     => 100,
                'null' => true,
            ],
            'cv_NRO_ID_CNPQ' => [
                'type'       => 'VARCHAR',
                'constraint'     => 16,
            ],
            'cv_SGL_PAIS' => [
                'type'       => 'VARCHAR',
                'constraint'     => 3,
                'null' => true,
            ],
            'COD_AREA' => [
                'type'       => 'VARCHAR',
                'constraint'     => 6,
                'null' => true,
            ],
            'cv_COD_NIVEL' => [
                'type'       => 'VARCHAR',
                'constraint'     => 1,
                'null' => true,
            ],
            'DT_ATUALIZA' => [
                'type'       => 'DATE',
                'null' => true,
            ],
            'DTA_CARGA' => [
                'type'       => 'DATE',
                'null' => true,
            ],
            'created_at' => [
                'type'    => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type'       => 'TIMESTAMP',
                'null' => true,
            ],

        ]);
        $this->forge->addKey('id_cv', true);
        $this->forge->addKey('cv_NRO_ID_CNPQ', true, 16);
        //$this->forge->addKey('cv_name', true);
        $this->forge->createTable('lattes_curriculo');

        /* Criar indice */
    }

    public function down()
    {
        $this->forge->dropTable('lattes_curriculo');
    }
}
