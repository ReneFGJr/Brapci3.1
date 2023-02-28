<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class BibliofindIsbn extends Migration
{
    protected $DBGroup = 'bibliofind';
    public function up()
    {
        $this->forge->addField([
            'id_isbn' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'isbn_ean13' => [
                'type'       => 'varchar',
                'constraint'     => 13,
                'null' => true,
            ],
            'isbn_ean10' => [
                'type'       => 'varchar',
                'constraint'     => 13,
                'null' => true,
            ],
            'isbn_vol' => [
                'type'       => 'varchar',
                'constraint'     => 3,
                'null' => true,
            ],
            'isbn_title' => [
                'type'       => 'text',
                'null' => true,
            ],
            'isbn_status' => [
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
        $this->forge->addKey('id_isbn', true);
        $this->forge->createTable('find_isbn');
    }

    public function down()
    {
        $this->forge->dropTable('find_isbn');
    }
}
