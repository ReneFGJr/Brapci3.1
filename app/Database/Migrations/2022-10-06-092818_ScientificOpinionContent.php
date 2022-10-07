<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class ScientificOpinionContent extends Migration
{
    protected $DBGroup = 'pgcd';

    public function up()
    {
        $this->forge->addField([
            'id_opc' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'opc_id_op' => [
                'type'       => 'INT',
                'null' => true,
            ],
            'opc_field' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
            ],
            'opc_pag' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
            ],
            'opc_content' => [
                'type'       => 'TEXT',
                'null' => true,
            ],
            'opc_comment' => [
                'type'       => 'TEXT',
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
        $this->forge->addKey('id_opc', true);
        $this->forge->createTable('scientific_opinion_content');
    }

    public function down()
    {
        $this->forge->dropTable('scientific_opinion_content');
    }
}