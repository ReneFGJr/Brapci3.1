<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class PatentRPIDepacho extends Migration
{
    protected $DBGroup = 'patent';

    public function up()
    {
        $this->forge->addField([
            'id_dsp' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'p_patent_nr' => [
                'type'       => 'INT',
                'null' => true,
            ],
            'p_issue' => [
                'type'       => 'INT',
                'null' => true,
            ],
            'p_section' => [
                'type'       => 'INT',
                'null' => true,
            ],
            'p_comment' => [
                'type'       => 'TEXT',
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
        $this->forge->addKey('id_dsp', true);
        $this->forge->createTable('RPI_depacho');
    }

    public function down()
    {
        $this->forge->dropTable('RPI_depacho');
    }
}
