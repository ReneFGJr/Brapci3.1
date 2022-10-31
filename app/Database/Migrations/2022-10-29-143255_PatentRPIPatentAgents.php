<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class PatentRPIPatentAgents extends Migration
{
    protected $DBGroup = 'patent';

    public function up()
    {
        $this->forge->addField([
            'id_pag' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'pag_patent' => [
                'type'       => 'INT',
                'null' => true,
            ],
            'pag_agent' => [
                'type'       => 'INT',
                'null' => true,
            ],
            'pag_type' => [
                'type'       => 'VARCHAR',
                'constraint'     => 1,
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
        $this->forge->addKey('id_pag', true);
        $this->forge->createTable('RPI_patent_agents');
    }

    public function down()
    {
        $this->forge->dropTable('RPI_patent_agents');
    }
}
