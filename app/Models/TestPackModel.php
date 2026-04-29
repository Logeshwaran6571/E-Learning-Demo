<?php namespace App\Models;
use CodeIgniter\Model;
class TestPackModel extends Model {
    protected $table = 'test_packs';
    protected $primaryKey = 'id';
    protected $allowedFields = ['assessment_id', 'pack_name', 'user_role', 'template_id'];
}
