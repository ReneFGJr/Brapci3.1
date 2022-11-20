<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class Bugs extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_bug' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],

            'bug_name' => [
                'type'       => 'VARCHAR',
                'constraint'     => 100,
                'null' => true,
            ],
            'bug_user' => [
                'type'       => 'INT',
                'default'     => 1,
            ],
            'bug_problem' => [
                'type'       => 'TEXT',
                'null' => true,
            ],
            'bug_IP' => [
                'type'       => 'VARCHAR',
                'constraint'     => 16,
                'null' => true,
            ],
            'bug_status' => [
                'type'       => 'INT',
                'default'     => 1,
                'null' => true,
            ],
            'bug_solution' => [
                'type'       => 'TEXT',
                'null' => true,
            ],
            'bug_v' => [
                'type'       => 'INT',
                'default'     => 1,
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
        $this->forge->addKey('id_bug', true);
        $this->forge->createTable('bugs');

        /* Criar indice */
    }


    public function down()
    {
        $this->forge->dropTable('bugs');
    }
}
