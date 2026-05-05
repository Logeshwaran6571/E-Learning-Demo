<?php namespace App\Models;

use CodeIgniter\Model;

class QuestionBankModel extends Model
{
    protected $table = 'question_bank';
    protected $primaryKey = 'id';
    protected $allowedFields = ['text', 'type', 'category', 'difficulty', 'marks', 'options', 'correct_answer'];
}
