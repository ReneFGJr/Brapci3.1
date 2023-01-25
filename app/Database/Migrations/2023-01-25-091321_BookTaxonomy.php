<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class BookTaxonomy extends Migration
{
    protected $DBGroup = 'books';

    public function up()
    {
        $this->forge->addField([
            'id_bs' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'bs_rdf' => [
                'type'       => 'INT',
                'null' => true,
                'default' => 0,
            ],
            'bs_father' => [
                'type'       => 'INT',
                'null' => true,
                'default'=>0,
            ],
            'bs_order' => [
                'type'       => 'INT',
                'null' => true,
                'default' => 0,
            ],
            'bs_name' => [
                'type'       => 'varchar',
                'constraint'     => 100,
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
        $this->forge->addKey('id_bs', true);
        $this->forge->createTable('books_taxonomy');


        $secs = array(
            'Epistemologia e Estudos Históricos',
            'Organização e Representação do Conhecimento',
            'Mediação, Circulação e Apropriação da Informação',
            'Gestão da Informação e do Conhecimento',
            'Política e Economia da Informação',
            'Informação, Educação e Trabalho',
            'Produção e Comunicação da Informação em Ciência, Tecnologia & Inovação',
            'Informação e Tecnologia',
            'Museu, Patrimônio e Informação',
            'Informação e Memória',
            'Informação & Saúde',
            'Informação, Estudos Étnico-Raciais, Gênero e Diversidades',
            );
        $ord = 1;
        foreach($secs as $session_name)
            {
            $data = array(
                'bs_name' => $session_name,
                'bs_order' => ($ord++),
                'updated_at	' => '1900-01-01',
            );
            $this->db->table('books_taxonomy')->insert($data);

            }
    }

    public function down()
    {
        $this->forge->dropTable('books_taxonomy');
    }
}
