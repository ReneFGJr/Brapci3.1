<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class LattesDados extends Migration
{
    protected $DBGroup = 'lattes';
    public function up()
    {
        $this->forge->addField([
            'id_lt' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'lt_id' => [
                'type'       => 'varchar',
                'constraint'     => 16,
                'null' => true,
            ],
            'lt_idk' => [
                'type'       => 'varchar',
                'constraint'     => 10,
                'null' => true,
            ],
            'lt_name' => [
                'type'       => 'varchar',
                'constraint'     => 150,
                'null' => true,
            ],
            'lt_genre' => [
                'type'       => 'varchar',
                'constraint'     => 1,
                'null' => true,
            ],
            'lt_atualizacao' => [
                'type'    => 'TIMESTAMP',
                'null' => true,
            ],
            'lt_nacionalidade_id' => [
                'type'       => 'int',
                'null' => true,
            ],
            'lt_orcid' => [
                'type'       => 'varchar',
                'constraint'     => 20,
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
        $this->forge->addKey('id_lt', true);
        $this->forge->createTable('lattesdados');
    }

    public function down()
    {
        $this->forge->dropTable('lattesdados');
    }
}
