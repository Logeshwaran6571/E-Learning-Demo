<?php

namespace App\Models;

use CodeIgniter\Model;

class QuestionBankRepositoryModel extends Model
{
    protected $table = 'question_bank_repositories';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'created_at', 'updated_at'];
    protected $useTimestamps = true;
}
