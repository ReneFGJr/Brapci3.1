<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class PatentRPIAgents extends Migration
{
    protected $DBGroup = 'patent';

    public function up()
    {
        $this->forge->addField([
            'id_ag' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'ag_use' => [
                'type'       => 'INT',
                'null' => true,
            ],
            'ag_name' => [
                'type'       => 'VARCHAR',
                'constraint'     => 100,
                'null' => true,
            ],
            'ag_type' => [
                'type'       => 'VARCHAR',
                'constraint'     => 1,
                'null' => true,
            ],
            'ag_country' => [
                'type'       => 'VARCHAR',
                'constraint'     => 3,
                'null' => true,
            ],
            'ag_state' => [
                'type'       => 'VARCHAR',
                'constraint'     => 3,
                'null' => true,
            ],
            'ag_url' => [
                'type'       => 'VARCHAR',
                'constraint'     => 100,
                'null' => true,
            ],
            'ag_ror' => [
                'type'       => 'VARCHAR',
                'constraint'     => 100,
                'null' => true,
            ],
            'ag_email' => [
                'type'       => 'VARCHAR',
                'constraint'     => 100,
                'null' => true,
            ],
            'ag_notes' => [
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
        $this->forge->addKey('id_ag', true);
        $this->forge->createTable('RPI_agents');
    }

    public function down()
    {
        $this->forge->dropTable('RPI_agents');
    }
}
