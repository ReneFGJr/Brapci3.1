<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEditorApiKeyToOjsJournals extends Migration
{
    protected $DBGroup = 'ojs_import';

    public function up()
    {
        if (!$this->db->fieldExists('api_key_editor', 'ojs_journals')) {
            $this->forge->addColumn('ojs_journals', [
                'api_key_editor' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'api_key',
                    'comment' => 'API key de usuário OJS com papel editorial; nunca retornar em listagens ou logs.',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('api_key_editor', 'ojs_journals')) {
            $this->forge->dropColumn('ojs_journals', 'api_key_editor');
        }
    }
}