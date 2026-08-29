<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOjsJournals extends Migration
{
    protected $DBGroup = 'ojs_import';

    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
            ],
            'acronym' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => true,
            ],
            'base_url' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'context_path' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'api_key' => [
                'type' => 'TEXT',
                'comment' => 'API key de acesso ao OJS; nunca retornar em listagens ou logs.',
            ],
            'is_default' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'unsigned' => true,
                'default' => 0,
            ],
            'active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'unsigned' => true,
                'default' => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['base_url', 'context_path']);
        $this->forge->addKey(['active', 'is_default']);
        $this->forge->createTable('ojs_journals', true);
    }

    public function down()
    {
        $this->forge->dropTable('ojs_journals', true);
    }
}