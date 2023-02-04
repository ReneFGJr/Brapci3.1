<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class KtoN extends Migration
{
    protected $DBGroup = 'lattes';
    public function up()
    {
        $this->forge->addField([
            'id_kn' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kn_idk' => [
                'type'       => 'varchar',
                'constraint'     => 12,
                'null' => true,
            ],
            'kn_idn' => [
                'type'       => 'varchar',
                'constraint'     => 15,
                'null' => true,
            ],
            'kn_status' => [
                'type'       => 'int',
                'null' => true,
                'default' => 0,
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
        $this->forge->addKey('id_kn', true);
        $this->forge->createTable('k_to_n');
    }

    public function down()
    {
        $this->forge->dropTable('k_to_n');
    }
}
