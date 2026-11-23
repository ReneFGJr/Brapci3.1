<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSigninAccessLogs extends Migration
{
    protected $DBGroup = 'click';

    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'username' => ['type' => 'VARCHAR', 'constraint' => 255, 'default' => ''],
            'successful' => ['type' => 'TINYINT', 'constraint' => 1, 'unsigned' => true, 'default' => 0],
            'reason' => ['type' => 'VARCHAR', 'constraint' => 255, 'default' => ''],
            'ip_address' => ['type' => 'VARCHAR', 'constraint' => 45, 'default' => ''],
            'user_agent' => ['type' => 'VARCHAR', 'constraint' => 500, 'default' => ''],
            'created_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey('successful');
        $this->forge->addKey('ip_address');
        $this->forge->addKey('created_at');
        $this->forge->createTable('signin_access_logs', true, ['ENGINE' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('signin_access_logs', true);
    }
}
