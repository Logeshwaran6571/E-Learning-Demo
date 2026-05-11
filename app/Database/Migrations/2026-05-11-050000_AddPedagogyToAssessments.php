<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPedagogyToAssessments extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('pedagogy', 'assessments')) {
            $this->forge->addColumn('assessments', [
                'pedagogy' => ['type' => 'VARCHAR', 'constraint' => 128, 'null' => true],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('pedagogy', 'assessments')) {
            $this->forge->dropColumn('assessments', 'pedagogy');
        }
    }
}
