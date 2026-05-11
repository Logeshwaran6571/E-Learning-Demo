<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Aligns `assessments` with TestModel / TestController::createTest().
 * Without these columns INSERT fails and tests never persist (empty inventory).
 */
class AddExamConfigurationColumnsToAssessments extends Migration
{
    public function up()
    {
        $newFields = [];

        if (! $this->db->fieldExists('instructions', 'assessments')) {
            $newFields['instructions'] = ['type' => 'TEXT', 'null' => true];
        }
        if (! $this->db->fieldExists('shuffle_questions', 'assessments')) {
            $newFields['shuffle_questions'] = ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0];
        }
        if (! $this->db->fieldExists('shuffle_options', 'assessments')) {
            $newFields['shuffle_options'] = ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0];
        }
        if (! $this->db->fieldExists('proctored_exam', 'assessments')) {
            $newFields['proctored_exam'] = ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0];
        }
        if (! $this->db->fieldExists('browser_lockdown', 'assessments')) {
            $newFields['browser_lockdown'] = ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0];
        }
        if (! $this->db->fieldExists('show_results', 'assessments')) {
            $newFields['show_results'] = ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0];
        }
        if (! $this->db->fieldExists('allow_backtracking', 'assessments')) {
            $newFields['allow_backtracking'] = ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0];
        }
        if (! $this->db->fieldExists('pass_mark', 'assessments')) {
            $newFields['pass_mark'] = ['type' => 'INT', 'constraint' => 11, 'default' => 50];
        }
        if (! $this->db->fieldExists('attempts', 'assessments')) {
            $newFields['attempts'] = ['type' => 'INT', 'constraint' => 11, 'default' => 1];
        }

        if ($newFields !== []) {
            $this->forge->addColumn('assessments', $newFields);
        }
    }

    public function down()
    {
        $cols = [
            'instructions', 'shuffle_questions', 'shuffle_options', 'proctored_exam',
            'browser_lockdown', 'show_results', 'allow_backtracking', 'pass_mark', 'attempts',
        ];
        $toDrop = [];
        foreach ($cols as $c) {
            if ($this->db->fieldExists($c, 'assessments')) {
                $toDrop[] = $c;
            }
        }
        if ($toDrop !== []) {
            $this->forge->dropColumn('assessments', $toDrop);
        }
    }
}
