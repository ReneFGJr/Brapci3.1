<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class ScientificOpinion extends Migration
{
    protected $DBGroup = 'pgcd';

    public function up()
    {
        $this->forge->addField([
            'id_chk' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'chk_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'chk_description' => [
                'type'       => 'TEXT',
                'null' => true,
            ],
            'chk_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
            ],
            'chk_order' => [
                'type'       => 'INT',
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
        $this->forge->addKey('id_chk', true);
        $this->forge->createTable('scientific_opinion_check');
    }

    public function down()
    {
        $this->forge->dropTable('scientific_opinion_check');
    }
}
