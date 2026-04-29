<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRefinedFieldsToAssessments extends Migration
{
    public function up()
    {
        $this->forge->addColumn('assessments', [
            'assessment_type' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'after' => 'category'],
            'assigned_to' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'assessment_type'],
            'description' => ['type' => 'TEXT', 'null' => true, 'after' => 'assigned_to'],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('assessments', ['assessment_type', 'assigned_to', 'description']);
    }
}
