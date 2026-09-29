<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCitedPensador extends Migration
{
    protected $DBGroup = 'brapci_cited';

    public function up()
    {
        $this->forge->addField([
            'pensador_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'cited_normalize_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
            ],
        ]);

        $this->forge->addKey(['pensador_id', 'cited_normalize_id'], true);
        $this->forge->addKey('cited_normalize_id');
        $this->forge->addForeignKey('pensador_id', 'brapci.pensadores', 'id', 'CASCADE', 'CASCADE', 'fk_cited_pensador_pensador');
        $this->forge->addForeignKey('cited_normalize_id', 'cited_normalize', 'id_ca', 'CASCADE', 'CASCADE', 'fk_cited_pensador_normalize');
        $this->forge->createTable('cited_pensador', false, ['ENGINE' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('cited_pensador', true);
    }
}
