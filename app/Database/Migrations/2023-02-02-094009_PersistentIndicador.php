<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class PersistentIndicador extends Migration
{
    protected $DBGroup = 'persistent_indicador';
    public function up()
    {
        $this->forge->addField([
            'id_pi' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'pi_id' => [
                'type'       => 'varchar',
                'constraint'     => 150,
                'null' => true,
            ],
            'pi_url' => [
                'type'       => 'varchar',
                'constraint'     => 150,
                'null' => true,
            ],
            'pi_json' => [
                'type'       => 'text',
                'null' => true,
            ],
            'pi_active' => [
                'type'       => 'int',
                'null' => true,
                'default' => 0,
            ],
            'pi_status' => [
                'type'       => 'varchar',
                'constraint'     => 15,
                'null' => true,
            ],
            'pi_citation' => [
                'type'       => 'text',
                'null' => true,
            ],
            'pi_title' => [
                'type'       => 'text',
                'null' => true,
            ],
            'pi_creators' => [
                'type'       => 'text',
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
        $this->forge->addKey('id_pi', true);
        $this->forge->createTable('persistent_id');
    }

    public function down()
    {
        $this->forge->dropTable('persistent_id');
    }

}
