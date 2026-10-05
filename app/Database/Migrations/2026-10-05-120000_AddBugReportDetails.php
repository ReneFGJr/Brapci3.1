<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBugReportDetails extends Migration
{
    public function up()
    {
        foreach (['bug_url', 'bug_description'] as $field) {
            if (!$this->db->fieldExists($field, 'bugs')) {
                $this->forge->addColumn('bugs', [$field => ['type' => 'TEXT', 'null' => true]]);
            }
        }
        $this->db->query("UPDATE bugs SET bug_description = bug_solution, bug_solution = '' WHERE bug_problem = 'other' AND bug_status = 1 AND bug_description IS NULL");
    }

    public function down()
    {
        $this->db->query("UPDATE bugs SET bug_solution = bug_description WHERE bug_problem = 'other' AND bug_status = 1 AND (bug_solution IS NULL OR bug_solution = '')");
        $this->forge->dropColumn('bugs', ['bug_url', 'bug_description']);
    }
}
