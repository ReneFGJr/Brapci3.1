<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class PatentRPIData extends Migration
{
    protected $DBGroup = 'patent';

    public function up()
    {
        $this->forge->addField([
            'id_dt' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'dt_patent_nr' => [
                'type'       => 'INT',
                'null' => true,
            ],
            'dt_field' => [
                'type'       => 'INT',
                'null' => true,
            ],
            'dt_comment' => [
                'type'       => 'LONGTEXT',
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
        $this->forge->addKey('id_dt', true);
        $this->forge->createTable('RPI_data');
    }

    public function down()
    {
        $this->forge->dropTable('RPI_data');
    }
}
