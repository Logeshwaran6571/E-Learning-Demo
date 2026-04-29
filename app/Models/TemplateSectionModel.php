<?php namespace App\Models;
use CodeIgniter\Model;
class TemplateSectionModel extends Model {
    protected $table = 'template_sections';
    protected $primaryKey = 'id';
    protected $allowedFields = ['template_id', 'marks_type', 'num_questions', 'knowledge_type'];
}
