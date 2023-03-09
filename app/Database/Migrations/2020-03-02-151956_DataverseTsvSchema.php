<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;


class DataverseTsvSchema extends Migration
{
    protected $DBGroup = 'dataverse';

    public function up()
    {
        $this->forge->addField([
            'id_mt' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'mt_name' => [
                'type' => 'VARCHAR',
                'constraint' => '200'
            ],
            'mt_dataverseAlias' => [
                'type' => 'VARCHAR',
                'constraint' => '200'
            ],
            'mt_displayName' => [
                'type' => 'VARCHAR',
                'constraint' => '200'
            ],
            'mt_blockURI' => [
                'type' => 'VARCHAR',
                'constraint' => '200'
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp'
        ]);
        $this->forge->addKey('id_mt', true);
        $this->forge->createTable('dataverse_tsv_schema');
    }

    public function down()
    {
        //
    }
}
