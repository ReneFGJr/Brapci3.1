<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateThinkerGroups extends Migration
{
    protected $DBGroup = 'default';
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'nome' => ['type' => 'VARCHAR', 'constraint' => 255],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('pensador_grupos', true, $this->db->DBDriver === 'MySQLi' ? ['ENGINE' => 'InnoDB'] : []);
        $this->forge->addField([
            'grupo_id' => ['type' => 'INT', 'unsigned' => true],
            'pensador_id' => ['type' => 'INT', 'unsigned' => true],
        ]);
        $this->forge->addKey(['grupo_id', 'pensador_id'], true);
        $this->forge->addForeignKey('grupo_id', 'pensador_grupos', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('pensador_id', 'pensadores', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pensador_grupo_membros', true, $this->db->DBDriver === 'MySQLi' ? ['ENGINE' => 'InnoDB'] : []);
    }
    public function down()
    {
        $this->forge->dropTable('pensador_grupo_membros', true);
        $this->forge->dropTable('pensador_grupos', true);
    }
}
