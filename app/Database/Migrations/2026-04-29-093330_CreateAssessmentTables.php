<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAssessmentTables extends Migration
{
    public function up()
    {
        // Templates
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'description' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('templates', true);

        // Template Sections
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'template_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'marks_type' => ['type' => 'VARCHAR', 'constraint' => 50],
            'num_questions' => ['type' => 'INT', 'constraint' => 11],
            'knowledge_type' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('template_id', 'templates', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('template_sections', true);

        // Assessments
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'status' => ['type' => 'ENUM', 'constraint' => ['Draft', 'Active'], 'default' => 'Draft'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('assessments', true);

        // Test Packs
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'assessment_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'pack_name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'user_role' => ['type' => 'VARCHAR', 'constraint' => 100],
            'template_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('assessment_id', 'assessments', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('template_id', 'templates', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('test_packs', true);

        // Questions
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'test_pack_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'type' => ['type' => 'VARCHAR', 'constraint' => 50],
            'content' => ['type' => 'TEXT'],
            'option_a' => ['type' => 'TEXT', 'null' => true],
            'option_b' => ['type' => 'TEXT', 'null' => true],
            'option_c' => ['type' => 'TEXT', 'null' => true],
            'option_d' => ['type' => 'TEXT', 'null' => true],
            'correct_answer' => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
            'marks' => ['type' => 'INT', 'constraint' => 11],
            'knowledge_type' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('test_pack_id', 'test_packs', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('questions', true);
    }

    public function down()
    {
        $this->forge->dropTable('questions');
        $this->forge->dropTable('test_packs');
        $this->forge->dropTable('assessments');
        $this->forge->dropTable('template_sections');
        $this->forge->dropTable('templates');
    }
}
