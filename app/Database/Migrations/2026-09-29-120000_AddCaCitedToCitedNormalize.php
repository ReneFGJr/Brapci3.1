<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCaCitedToCitedNormalize extends Migration
{
    protected $DBGroup = 'brapci_cited';

    public function up()
    {
        if (!$this->db->fieldExists('ca_cited', 'cited_normalize')) {
            $this->forge->addColumn('cited_normalize', [
                'ca_cited' => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            ]);
        }
        $this->db->query('UPDATE cited_normalize n LEFT JOIN (
            SELECT ca_normalized, COUNT(DISTINCT ca_rdf) AS total
            FROM cited_article WHERE ca_rdf > 0 GROUP BY ca_normalized
        ) a ON a.ca_normalized = n.id_ca SET n.ca_cited = COALESCE(a.total, 0)');
    }

    public function down()
    {
        $this->forge->dropColumn('cited_normalize', 'ca_cited');
    }
}
