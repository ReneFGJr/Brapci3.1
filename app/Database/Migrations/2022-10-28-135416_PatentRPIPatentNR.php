<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class PatentRPIPatentNR extends Migration
{
    protected $DBGroup = 'patent';

    public function up()
    {
        $this->forge->addField([
            'id_p' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'p_nr' => [
                'type'       => 'VARCHAR',
                'constraint'     => 20,
                'null' => true,
            ],
            'p_use' => [
                'type'       => 'VARCHAR',
                'constraint'     => 20,
                'null' => true,
            ],
            /*
            'p_use' => [
                'type'       => 'VARCHAR',
                'constraint'     => 20,
                'null' => true,
            ],
            'p_section' => [
                'type'       => 'INT',
                'null' => true,
            ],
            */
            'p_country' => [
                'type'       => 'VARCHAR',
                'constraint'     => 3,
                'null' => true,
            ],
            'p_year' => [
                'type'       => 'VARCHAR',
                'constraint'     => 4,
                'null' => true,
            ],
            'p_number' => [
                'type'       => 'VARCHAR',
                'constraint'     => 20,
                'null' => true,
            ],
            'p_number_dv' => [
                'type'       => 'VARCHAR',
                'constraint'     => 2,
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
        $this->forge->addKey('id_p', true);
        $this->forge->createTable('RPI_patent_nr');
    }

    public function down()
    {
        $this->forge->dropTable('RPI_patent_nr');
    }
}
