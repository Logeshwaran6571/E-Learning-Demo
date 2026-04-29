<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldsToAssessments extends Migration
{
    public function up()
    {
        $this->forge->addColumn('assessments', [
            'category' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'after' => 'name'],
            'code' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'after' => 'category'],
            'department' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'after' => 'code'],
            'batch_year' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true, 'after' => 'department'],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('assessments', ['category', 'code', 'department', 'batch_year']);
    }
}
