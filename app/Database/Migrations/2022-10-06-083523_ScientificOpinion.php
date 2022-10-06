<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class ScientificOpinion extends Migration
{
    protected $DBGroup = 'pgcd';

    public function up()
    {
        $this->forge->addField([
            'id_op' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'op_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'op_instituicao' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'op_curso' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'op_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
            ],
            'op_date' => [
                'type'       => 'DATE',
                'null' => true,
            ],
            'op_hora' => [
                'type'       => 'VARCHAR',
                'constraint' => '5',
                'null' => true,
            ],
            'op_local' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'op_membros' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
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
        $this->forge->addKey('id_op', true);
        $this->forge->createTable('scientific_opinion');

    }

    public function down()
    {
        $this->forge->dropTable('scientific_opinion');
    }
}
