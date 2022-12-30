<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class Qualis extends Migration
{
    protected $DBGroup = 'capes';
    public function up()
    {
        $this->forge->addField([
            'id_q' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],

            'q_issn' => [
                'type'       => 'VARCHAR',
                'constraint'     => 9,
                'null' => true,
            ],
            'q_event' => [
                'type'       => 'INT',
                'null' => true,
            ],
            'q_area' => [
                'type'       => 'INT',
                'null' => true,
            ],
            'q_estrato' => [
                'type'       => 'varchar',
                'constraint'     => 2,
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
        $this->forge->addKey('id_q', true);
        $this->forge->createTable('qualis');
    }


    public function down()
    {
        $this->forge->dropTable('qualis');
    }
}
