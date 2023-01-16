<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class WikiTerm extends Migration
{
    protected $DBGroup = 'elastic';
    public function up()
    {
        $this->forge->addField([
            'id_t' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'varchar',
                'constraint'     => 150,
                'null' => true,
            ],
            'name_asc' => [
                'type'       => 'varchar',
                'constraint'     => 150,
                'null' => true,
            ],
            'lang' => [
                'type'       => 'varchar',
                'constraint'     => 5,
                'null' => true,
            ],
            'use' => [
                'type'       => 'int',
                'null' => true,
                'default' => 0,
            ],
            'uri' => [
                'type'       => 'varchar',
                'constraint'     => 150,
                'null' => true,
            ],
            'definition' => [
                'type'       => 'text',
                'null' => true,
            ],
            'classes' => [
                'type'       => 'text',
                'null' => true,
            ],
            'sources' => [
                'type'       => 'text',
                'null' => true,
            ],
            'status' => [
                'type'       => 'text',
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
        $this->forge->addKey('id_t', true);
        $this->forge->createTable('wiki');
    }

    public function down()
    {
        $this->forge->dropTable('wiki');
    }
}
