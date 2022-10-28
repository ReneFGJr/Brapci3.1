<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class PatentRPI extends Migration
{
    protected $DBGroup = 'patent';

    public function up()
    {
        $this->forge->addField([
            'id_rpi' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'rpi_nr' => [
                'type'       => 'INT',
                'null' => true,
            ],
            'rpi_data' => [
                'type'       => 'DATE',
                'null' => true,
            ],
            'rpi_status' => [
                'type'       => 'INT',
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
        $this->forge->addKey('id_rpi', true);
        $this->forge->createTable('RPI_issue');
    }

    public function down()
    {
        $this->forge->dropTable('RPI_issue');
    }
}
