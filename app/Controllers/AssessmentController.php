<?php

namespace App\Controllers;

use App\Models\TemplateModel;
use App\Models\TemplateSectionModel;
use App\Models\AssessmentModel;
use App\Models\TestPackModel;
use App\Models\QuestionModel;
use CodeIgniter\Controller;

class AssessmentController extends BaseController
{
    public function index()
    {
        $templateModel = new TemplateModel();
        $sectionModel = new TemplateSectionModel();
        $assessmentModel = new AssessmentModel();
        $testPackModel = new TestPackModel();

        $templates = $templateModel->findAll();
        foreach ($templates as &$t) {
            $t['sections'] = $sectionModel->where('template_id', $t['id'])->findAll();
        }

        $assessments = $assessmentModel->findAll();
        foreach ($assessments as &$a) {
            $a['test_packs'] = $testPackModel->where('assessment_id', $a['id'])->findAll();
            foreach ($a['test_packs'] as &$tp) {
                $tp['template'] = $templateModel->find($tp['template_id']);
                // Count assigned questions per type
                $tp['counts'] = (new QuestionModel())->where('test_pack_id', $tp['id'])
                    ->select('type, COUNT(*) as count')
                    ->groupBy('type')
                    ->findAll();
            }
        }

        return view('workflow', [
            'templates' => $templates,
            'assessments' => $assessments
        ]);
    }

    public function saveTemplate()
    {
        $templateModel = new TemplateModel();
        $sectionModel = new TemplateSectionModel();

        $data = $this->request->getPost();
        
        $templateId = $templateModel->insert([
            'name' => $data['name'],
            'description' => $data['description'] ?? ''
        ]);

        if (isset($data['sections'])) {
            foreach ($data['sections'] as $sec) {
                $sectionModel->insert([
                    'template_id' => $templateId,
                    'marks_type' => $sec['type'],
                    'num_questions' => $sec['count'],
                    'knowledge_type' => $sec['knowledge']
                ]);
            }
        }

        return $this->response->setJSON(['status' => 'success', 'id' => $templateId]);
    }

    public function createAssessment()
    {
        $model = new AssessmentModel();
        $data = $this->request->getPost();
        $id = $model->insert([
            'name' => $data['name'],
            'category' => $data['category'],
            'code' => $data['code'],
            'assessment_type' => $data['assessment_type'] ?? null,
            'assigned_to' => $data['assigned_to'] ?? null,
            'description' => substr($data['description'] ?? '', 0, 500),
            'status' => 'Draft'
        ]);
        return $this->response->setJSON(['status' => 'success', 'id' => $id]);
    }

    public function deleteAssessment($id)
    {
        $model = new AssessmentModel();
        $model->delete($id);
        return $this->response->setJSON(['status' => 'success']);
    }

    public function createTestPack()
    {
        $model = new TestPackModel();
        $data = $this->request->getPost();
        $id = $model->insert([
            'assessment_id' => $data['assessment_id'],
            'pack_name' => $data['pack_name'],
            'user_role' => $data['user_role'],
            'template_id' => $data['template_id']
        ]);
        return $this->response->setJSON(['status' => 'success', 'id' => $id]);
    }

    public function deletePack($id)
    {
        $model = new TestPackModel();
        $model->delete($id);
        return $this->response->setJSON(['status' => 'success']);
    }

    public function uploadQuestions()
    {
        $model = new QuestionModel();
        $file = $this->request->getFile('file');
        $testPackId = $this->request->getPost('test_pack_id');
        $type = $this->request->getPost('type');

        if ($file->isValid() && !$file->hasMoved()) {
            $csvData = file_get_contents($file->getTempName());
            $lines = explode("\n", $csvData);
            $headers = str_getcsv(array_shift($lines));
            
            foreach ($lines as $line) {
                if (empty(trim($line))) continue;
                $row = str_getcsv($line);
                $data = array_combine($headers, $row);
                
                $data['test_pack_id'] = $testPackId;
                $data['type'] = $type;
                $model->insert($data);
            }
        }

        return redirect()->back();
    }

    public function downloadTemplate($type)
    {
        $filename = ($type == 'mcq') ? 'mcq_template.csv' : 'marks_template.csv';
        $header = ($type == 'mcq') 
            ? "question,option_a,option_b,option_c,option_d,correct_answer,marks,knowledge_type"
            : "question,expected_answer,marks,knowledge_type";

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $header . "\n";
        exit;
    }
}
