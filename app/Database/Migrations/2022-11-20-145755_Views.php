<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class Views extends Migration
{
    protected $DBGroup = 'click';

    public function up()
    {
        $this->forge->addField([
            'id_a' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'a_user' => [
                'type'       => 'INT',
                'default'     => 1,
            ],
            'a_IP' => [
                'type'       => 'VARCHAR',
                'constraint'     => 16,
                'null' => true,
            ],
            'a_v' => [
                'type'       => 'INT',
                'default'     => 1,
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
        $this->forge->addKey('id_a', true);
        $this->forge->createTable('views');

        /* Criar indice */
    }


    public function down()
    {
        $this->forge->dropTable('views');
    }
}
