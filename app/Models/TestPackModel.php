<?php namespace App\Models;
use CodeIgniter\Model;
class TestPackModel extends Model {
    protected $table = 'test_packs';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'assessment_id', 'pack_name', 'user_role', 'template_id', 'duration', 
        'scheduled_date', 'start_time', 'end_time', 'candidates', 'candidates_type', 
        'status', 'instructions', 'pass_mark', 'max_attempts', 'shuffle_questions', 
        'shuffle_options', 'proctored_exam', 'browser_lockdown', 'show_results', 
        'allow_backtracking'
    ];
}
