<?php namespace App\Models;
use CodeIgniter\Model;
class QuestionModel extends Model {
    protected $table = 'questions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['test_pack_id', 'template_id', 'section_idx', 'type', 'question', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_answer', 'marks', 'knowledge_type', 'pedagogy', 'starter_code', 'language'];
}
