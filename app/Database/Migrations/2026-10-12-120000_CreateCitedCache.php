<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCitedCache extends Migration
{
    protected $DBGroup = 'brapci_cited';

    public function up()
    {
        $this->forge->addField([
            'pensador_id' => ['type' => 'INT', 'unsigned' => true],
            'cited_article_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'cache_date' => ['type' => 'DATE'],
            'search_term' => ['type' => 'VARCHAR', 'constraint' => 255],
            'created_at' => ['type' => 'DATETIME'],
        ]);

        $this->forge->addKey(['pensador_id', 'cited_article_id', 'cache_date'], true);
        $this->forge->addKey('cache_date');
        $this->forge->addKey('cited_article_id');
        $this->forge->addForeignKey('pensador_id', 'brapci.pensadores', 'id', 'CASCADE', 'CASCADE', 'fk_cached_pensador');
        $this->forge->addForeignKey('cited_article_id', 'cited_article', 'id_ca', 'CASCADE', 'CASCADE', 'fk_cached_article');
        $this->forge->createTable('cached', true, ['ENGINE' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('cached', true);
    }
}
