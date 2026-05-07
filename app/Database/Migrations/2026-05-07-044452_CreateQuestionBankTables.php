<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateQuestionBankTables extends Migration
{
    public function up()
    {
        // Question Bank Repositories (The "Banks")
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 255],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('question_bank_repositories', true);

        // Question Bank Questions
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'repository_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'question'       => ['type' => 'TEXT'],
            'type'           => ['type' => 'VARCHAR', 'constraint' => 50], // MCQ, Short Answer
            'option_a'       => ['type' => 'TEXT', 'null' => true],
            'option_b'       => ['type' => 'TEXT', 'null' => true],
            'option_c'       => ['type' => 'TEXT', 'null' => true],
            'option_d'       => ['type' => 'TEXT', 'null' => true],
            'correct_answer' => ['type' => 'TEXT', 'null' => true],
            'marks'          => ['type' => 'INT', 'constraint' => 11, 'default' => 1],
            'category'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'difficulty'     => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('repository_id', 'question_bank_repositories', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('question_bank', true);
    }

    public function down()
    {
        $this->forge->dropTable('question_bank');
        $this->forge->dropTable('question_bank_repositories');
    }
}
