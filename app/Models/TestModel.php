<?php namespace App\Models;
use CodeIgniter\Model;
class TestModel extends Model {
    protected $table = 'assessments';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'status', 'category', 'code', 'department', 'batch_year', 'assessment_type', 'assigned_to', 'description', 'instructions', 'shuffle_questions', 'shuffle_options', 'proctored_exam', 'browser_lockdown', 'show_results', 'allow_backtracking', 'pass_mark', 'attempts'];
    protected $useTimestamps = true;
}
