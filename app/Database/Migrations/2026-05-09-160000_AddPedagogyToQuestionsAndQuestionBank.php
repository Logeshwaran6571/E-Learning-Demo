<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPedagogyToQuestionsAndQuestionBank extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('pedagogy', 'question_bank')) {
            $this->forge->addColumn('question_bank', [
                'pedagogy' => ['type' => 'VARCHAR', 'constraint' => 128, 'null' => true],
            ]);
        }

        if (! $this->db->fieldExists('pedagogy', 'questions')) {
            $this->forge->addColumn('questions', [
                'pedagogy' => ['type' => 'VARCHAR', 'constraint' => 128, 'null' => true],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('pedagogy', 'question_bank')) {
            $this->forge->dropColumn('question_bank', 'pedagogy');
        }

        if ($this->db->fieldExists('pedagogy', 'questions')) {
            $this->forge->dropColumn('questions', 'pedagogy');
        }
    }
}
