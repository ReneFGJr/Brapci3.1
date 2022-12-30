<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\RawSql;
use CodeIgniter\Database\Migration;

class Journals extends Migration
{
    protected $DBGroup = 'capes';
    public function up()
    {
        $this->forge->addField([
            'id_j' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'j_name' => [
                'type'       => 'varchar',
                'constraint'     => 100,
                'null' => true,
            ],
            'j_issn' => [
                'type'       => 'varchar',
                'constraint'     => 9,
                'null' => true,
            ],
            'j_issn_l' => [
                'type'       => 'varchar',
                'constraint'     => 9,
                'default' => '',
                'null' => true,
            ],
            'j_country' => [
                'type'       => 'char',
                'constraint'     => 3,
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
        $this->forge->addKey('id_j', true);
        $this->forge->createTable('journals');
    }


    public function down()
    {
        $this->forge->dropTable('journals');
    }
}
