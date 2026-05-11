<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * When set, candidates may see final scores (after admin publishes evaluated results).
 */
class AddResultsPublishedToTestPacks extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('results_published', 'test_packs')) {
            $this->forge->addColumn('test_packs', [
                'results_published' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 0,
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('results_published', 'test_packs')) {
            $this->forge->dropColumn('test_packs', 'results_published');
        }
    }
}
