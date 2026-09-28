<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePensadores extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nome' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'nome_citacao' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'link_wikipedia' => [
                'type' => 'VARCHAR',
                'constraint' => 2048,
                'null' => true,
            ],
            'link_wikidata' => [
                'type' => 'VARCHAR',
                'constraint' => 2048,
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('pensadores', true);
    }

    public function down()
    {
        $this->forge->dropTable('pensadores', true);
    }
}
