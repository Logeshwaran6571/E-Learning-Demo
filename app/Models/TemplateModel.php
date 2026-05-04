<?php
namespace App\Models;
use CodeIgniter\Model;
class TemplateModel extends Model
{
    protected $table = 'templates';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'paper_title', 'duration', 'total_marks', 'description'];
    protected $useTimestamps = true;
}



