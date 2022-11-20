<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class ViewsArticle extends Migration
{
    protected $DBGroup = 'click';

    public function up()
    {
        $this->forge->addField([
            'id_av' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'av_rdf' => [
                'type'       => 'INT',
                'default'     => 0,
                'null' => true,
            ],
            'av_views' => [
                'type'       => 'INT',
                'default'     => 1,
                'null' => true,
            ],
            'av_last_IP' => [
                'type'       => 'VARCHAR',
                'constraint'     => 16,
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
        $this->forge->addKey('id_av', true);
        $this->forge->addKey('av_rdf', false);
        $this->forge->createTable('views_rdf');

        /* Criar indice */
    }


    public function down()
    {
        $this->forge->dropTable('views_rdf');
    }
}
