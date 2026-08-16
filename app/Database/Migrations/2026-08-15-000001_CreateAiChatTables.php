<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAiChatTables extends Migration
{
    protected $DBGroup = 'brapci_ai';

    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'INT', 'constraint' => 5, 'unsigned' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 150],
            'description' => ['type' => 'TEXT', 'null' => true],
            'system_prompt' => ['type' => 'LONGTEXT', 'null' => true],
            'context' => ['type' => 'LONGTEXT', 'null' => true],
            'default_model' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true)->addKey('user_id');
        $this->forge->createTable('ai_projects', true);

        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'INT', 'constraint' => 5, 'unsigned' => true],
            'project_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'title' => ['type' => 'VARCHAR', 'constraint' => 255],
            'model' => ['type' => 'VARCHAR', 'constraint' => 150],
            'system_prompt' => ['type' => 'LONGTEXT', 'null' => true],
            'status' => ['type' => 'ENUM', 'constraint' => ['active', 'archived', 'deleted'], 'default' => 'active'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true)->addKey('user_id')->addKey('project_id')->addKey('updated_at');
        $this->forge->addForeignKey('project_id', 'ai_projects', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('ai_chats', true);

        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'chat_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'role' => ['type' => 'ENUM', 'constraint' => ['system', 'user', 'assistant', 'tool']],
            'content' => ['type' => 'LONGTEXT'],
            'model' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'tokens_input' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'tokens_output' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'generation_time_ms' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'status' => ['type' => 'ENUM', 'constraint' => ['completed', 'streaming', 'cancelled', 'error'], 'default' => 'completed'],
            'request_id' => ['type' => 'VARCHAR', 'constraint' => 64, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true)->addKey('chat_id')->addKey('created_at');
        $this->forge->addUniqueKey(['chat_id', 'request_id']);
        $this->forge->addForeignKey('chat_id', 'ai_chats', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('ai_messages', true);

        $this->forge->addField([
            'user_id' => ['type' => 'INT', 'constraint' => 5, 'unsigned' => true],
            'default_model' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'temperature' => ['type' => 'DECIMAL', 'constraint' => '3,2', 'default' => 0.70],
            'num_ctx' => ['type' => 'INT', 'unsigned' => true, 'default' => 8192],
            'stream' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('user_id', true);
        $this->forge->createTable('ai_user_settings', true);
    }

    public function down()
    {
        $this->forge->dropTable('ai_user_settings', true);
        $this->forge->dropTable('ai_messages', true);
        $this->forge->dropTable('ai_chats', true);
        $this->forge->dropTable('ai_projects', true);
    }
}
