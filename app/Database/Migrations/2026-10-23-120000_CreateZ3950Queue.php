<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateZ3950Queue extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'isbn' => ['type' => 'VARCHAR', 'constraint' => 13],
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'pending'],
            'attempts' => ['type' => 'INT', 'default' => 0],
            'available_at' => ['type' => 'DATETIME'],
            'locked_at' => ['type' => 'DATETIME', 'null' => true],
            'lock_token' => ['type' => 'VARCHAR', 'constraint' => 64, 'null' => true],
            'result' => ['type' => 'LONGTEXT', 'null' => true],
            'last_error' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME'],
            'updated_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('isbn');
        $this->forge->addKey(['status', 'available_at']);
        $this->forge->createTable('z3950_queue', true);
    }

    public function down()
    {
        $this->forge->dropTable('z3950_queue', true);
    }
}
