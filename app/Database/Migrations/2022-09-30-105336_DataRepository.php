<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class DataRepository extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_rp' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'rp_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'rp_description' => [
                'type'       => 'TEXT',
                'null' => true,
            ],
            'rp_url' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'rp_group' => [
                'type'       => 'INT',
                'default' => 0,
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
        $this->forge->addKey('id_rp', true);
        $this->forge->createTable('research_data_repository');

    }

    public function down()
    {
        $this->forge->dropTable('research_data_repository');
    }
}
