<?php namespace App\Models;

use CodeIgniter\Model;

class QuestionBankModel extends Model
{
    protected $table = 'question_bank';
    protected $primaryKey = 'id';
    protected $allowedFields = ['repository_id', 'question', 'type', 'category', 'difficulty', 'marks', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_answer', 'pedagogy'];
    protected $useTimestamps = true;
}
