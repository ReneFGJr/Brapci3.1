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
                'null' => false,
            ],
            'id_jnl' => [
                'type'       => 'INT',
                'null' => true,
            ],
            'collection' => [
                'type'       => 'varchar',
                'constraint'     => 2,
                'null' => true,
            ],
            'type' => [
                'type'       => 'varchar',
                'constraint'     => 15,
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
            'abstract' => [
                'type'       => 'text',
                'null' => true,
            ],
            'keywords' => [
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
            'pdf' => [
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
        $this->forge->addKey('id', true);
        $this->forge->addKey('article_id', true);
        $this->forge->createTable('dataset');
    }

    public function down()
    {
        $this->forge->dropTable('dataset');
    }
}
