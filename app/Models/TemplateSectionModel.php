<?php namespace App\Models;
use CodeIgniter\Model;
class TemplateSectionModel extends Model {
    protected $table = 'template_sections';
    protected $primaryKey = 'id';
    protected $allowedFields = ['template_id', 'section_name', 'marks_type', 'num_questions', 'marks_per_question', 'knowledge_type'];
}
