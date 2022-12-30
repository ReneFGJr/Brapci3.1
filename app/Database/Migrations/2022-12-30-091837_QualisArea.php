<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class QualisArea extends Migration
{
    protected $DBGroup = 'capes';
    public function up()
    {
        $this->forge->addField([
            'id_qa' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'qa_area' => [
                'type'       => 'varchar',
                'constraint'     => 40,
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
        $this->forge->addKey('id_qa', true);
        $this->forge->createTable('qualis_area');
    }


    public function down()
    {
        $this->forge->dropTable('qualis_area');
    }
}
