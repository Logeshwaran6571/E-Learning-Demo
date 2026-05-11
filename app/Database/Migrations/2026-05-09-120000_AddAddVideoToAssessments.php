<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAddVideoToAssessments extends Migration
{
    public function up()
    {
        if ($this->db->fieldExists('add_video', 'assessments')) {
            return;
        }
        $this->forge->addColumn('assessments', [
            'add_video' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
            ],
        ]);
    }

    public function down()
    {
        if ($this->db->fieldExists('add_video', 'assessments')) {
            $this->forge->dropColumn('assessments', ['add_video']);
        }
    }
}
