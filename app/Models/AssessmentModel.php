<?php namespace App\Models;
use CodeIgniter\Model;
class AssessmentModel extends Model {
    protected $table = 'assessments';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'status', 'category', 'code', 'department', 'batch_year', 'assessment_type', 'assigned_to', 'description'];
    protected $useTimestamps = true;
}
