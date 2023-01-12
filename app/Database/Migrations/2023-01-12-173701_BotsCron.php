<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class BotsCron extends Migration
{
    protected $DBGroup = 'bots';
    public function up()
    {
        $this->forge->addField([
            'id_task' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'task_id' => [
                'type'       => 'varchar',
                'constraint'     => 20,
                'null' => false,
            ],
            'task_status' => [
                'type'       => 'INT',
                'null' => true,
                'default'=>0,
            ],
            'task_propriry' => [
                'type'       => 'INT',
                'null' => true,
                'default' => 99,
            ],
            'task_offset' => [
                'type'       => 'INT',
                'null' => true,
                'default' => 99999,
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
        $this->forge->addKey('id_task', true);
        $this->forge->createTable('tasks');
    }

    public function down()
    {
        $this->forge->dropTable('tasks');
    }
}
