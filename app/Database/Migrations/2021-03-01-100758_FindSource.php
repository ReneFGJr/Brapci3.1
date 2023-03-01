<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class FindSource extends Migration
{
    protected $DBGroup = 'bibliofind';
    public function up()
    {
        $this->forge->addField([
            'id_src' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'src_ean13' => [
                'type'       => 'varchar',
                'constraint'     => 13,
                'null' => true,
            ],
            'src_id' => [
                'type'       => 'varchar',
                'constraint'     => 40,
                'null' => true,
            ],
            'src_status' => [
                'type'       => 'int',
                'default'     => 0,
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
        $this->forge->addKey('id_src', true);
        $this->forge->createTable('find_source');
    }

    public function down()
    {
        $this->forge->dropTable('find_source');
    }
}
