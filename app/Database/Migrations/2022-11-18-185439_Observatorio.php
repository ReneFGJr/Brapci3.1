<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class Observatorio extends Migration
{
    protected $DBGroup = 'observatorio';

    public function up()
    {
        $this->forge->addField([
            'id_obs' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],

            'obs_name' => [
                'type'       => 'VARCHAR',
                'constraint'     => 100,
                'null' => true,
            ],
            'obs_notes' => [
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
        $this->forge->addKey('id_obs', true);
        $this->forge->createTable('obs_projects');
    }


    public function down()
    {
        $this->forge->dropTable('obs_projects');
    }
}
