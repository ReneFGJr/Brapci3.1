<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class PatentRPISection extends Migration
{
    protected $DBGroup = 'patent';

    public function up()
    {
        $this->forge->addField([
            'id_rsec' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'rsec_code' => [
                'type'       => 'VARCHAR',
                'constraint'     => 20,
                'null' => true,
            ],
            'rsec_name' => [
                'type'       => 'VARCHAR',
                'constraint'     => 200,
                'null' => true,
            ],
            'rsec_group' => [
                'type'       => 'VARCHAR',
                'constraint'     => 20,
                'null' => true,
            ],
            'rsec_status' => [
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
        $this->forge->addKey('id_rsec', true);
        $this->forge->createTable('rpi_section');
    }

    public function down()
    {
        $this->forge->dropTable('rpi_section');
    }
}
