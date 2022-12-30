<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\RawSql;
use CodeIgniter\Database\Migration;

class QualisEvento extends Migration
{
    protected $DBGroup = 'capes';
    public function up()
    {
        $this->forge->addField([
            'id_ev' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'ev_name' => [
                'type'       => 'varchar',
                'constraint'     => 100,
                'null' => true,
            ],
            'ev_year_start' => [
                'type'       => 'int',
                'null' => true,
            ],
            'ev_year_end' => [
                'type'       => 'int',
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
        $this->forge->addKey('id_ev', true);
        $this->forge->createTable('qualis_event');
    }


    public function down()
    {
        $this->forge->dropTable('qualis_event');
    }
}
