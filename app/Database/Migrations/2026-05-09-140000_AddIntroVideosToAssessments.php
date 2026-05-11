<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIntroVideosToAssessments extends Migration
{
    public function up()
    {
        if ($this->db->fieldExists('intro_videos', 'assessments')) {
            return;
        }
        $this->forge->addColumn('assessments', [
            'intro_videos' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        if ($this->db->fieldExists('intro_videos', 'assessments')) {
            $this->forge->dropColumn('assessments', ['intro_videos']);
        }
    }
}
