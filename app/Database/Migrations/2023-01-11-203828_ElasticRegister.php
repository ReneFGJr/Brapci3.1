<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class ElasticRegister extends Migration
{
    protected $DBGroup = 'elastic';
    public function up()
    {
    $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'article_id' => [
                'type'       => 'INT',
                'null' => true,
            ],
            'id_jnl' => [
                'type'       => 'INT',
                'null' => true,
            ],
            'title' => [
                'type'       => 'text',
                'null' => true,
            ],
            'authors' => [
                'type'       => 'text',
                'null' => true,
            ],
            'fulltext' => [
                'type'       => 'longtext',
                'null' => true,
            ],
            'year' => [
                'type'       => 'varchar',
                'constraint'     => 4,
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
        $this->forge->addKey('id', true);
        $this->forge->createTable('dataset');
    }

    public function down()
    {
        $this->forge->dropTable('dataset');
    }
}
