<?php

namespace App\Controllers;

use App\Models\TemplateModel;
use App\Models\TemplateSectionModel;
use App\Models\TestModel;
use App\Models\TestPackModel;
use App\Models\QuestionModel;
use App\Models\EmployeeModel;
use App\Models\QuestionBankModel;
use CodeIgniter\Controller;

class TestController extends BaseController
{
    public function index()
    {
        $templateModel = new TemplateModel();
        $sectionModel = new TemplateSectionModel();
        $TestModel = new TestModel();
        $testPackModel = new TestPackModel();

        $templates = $templateModel->findAll();
        foreach ($templates as &$t) {
            $t['sections'] = $sectionModel->where('template_id', $t['id'])->findAll();
        }

        $Tests = $TestModel->findAll();
        foreach ($Tests as &$a) {
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

        try {
            $employees = (new EmployeeModel())->findAll();
            $questionBank = (new QuestionBankModel())->findAll();
        } catch (\Exception $e) {
            // Tables might not exist yet, use fallbacks
            $employees = [];
            $questionBank = [];
        }

        if (empty($employees)) {
            $employees = [
                ['id' => 1, 'name' => 'John Doe', 'email' => 'john.doe@company.com', 'type' => 'internal'],
                ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane.smith@company.com', 'type' => 'internal'],
                ['id' => 3, 'name' => 'Aditya Kumar', 'email' => 'aditya.k@gmail.com', 'type' => 'recruitment']
            ];
        }

        if (empty($questionBank)) {
            $questionBank = [
                ['id' => 1, 'text' => 'What is the output of 2 + "2"?', 'type' => 'MCQ', 'category' => 'JavaScript', 'difficulty' => 'Easy', 'marks' => 1],
                ['id' => 2, 'text' => 'Explain closures in JavaScript.', 'type' => '2-Mark', 'category' => 'JavaScript', 'difficulty' => 'Medium', 'marks' => 2]
            ];
        }

        return view('workflow', [
            'templates' => $templates,
            'Tests' => $Tests,
            'employees' => $employees,
            'questionBank' => $questionBank
        ]);
    }

    public function saveTemplate()
    {
        $templateModel = new TemplateModel();
        $sectionModel = new TemplateSectionModel();

        $data = $this->request->getJSON(true);
        if (!$data) {
            $data = $this->request->getPost();
        }

        if (!$data) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid data']);
        }

        $id = $data['id'] ?? null;
        $templateData = [
            'name' => $data['name'],
            'paper_title' => $data['name'], // Defaulting to name if not provided separately
            'duration' => $data['duration'] ?? 60,
            'total_marks' => $this->calculateTotalMarks($data['sections'] ?? []),
            'description' => $data['category'] ?? '' // Storing category in description for now
        ];

        if ($id) {
            $templateModel->update($id, $templateData);
            $templateId = $id;
            // Clean up old sections before re-inserting
            $sectionModel->where('template_id', $id)->delete();
        } else {
            $templateId = $templateModel->insert($templateData);
        }

        if (!$templateId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to save template']);
        }

        if (isset($data['sections']) && is_array($data['sections'])) {
            foreach ($data['sections'] as $sec) {
                $sectionModel->insert([
                    'template_id' => $templateId,
                    'section_name' => $sec['name'] ?? '',
                    'marks_type' => $sec['type'] ?? 'MCQ',
                    'num_questions' => $sec['count'] ?? 0,
                    'marks_per_question' => $sec['marks'] ?? 1
                ]);
            }
        }

        // Fetch the fresh data to return
        $savedTemplate = $templateModel->find($templateId);
        $savedTemplate['structure'] = json_encode($data['sections'] ?? []);

        return $this->response->setJSON([
            'status' => 'success', 
            'template' => $savedTemplate
        ]);
    }

    private function calculateTotalMarks($sections) {
        $total = 0;
        foreach($sections as $s) {
            $total += ($s['count'] ?? 0) * ($s['marks'] ?? 0);
        }
        return $total;
    }

    public function deleteTemplate($id)
    {
        $templateModel = new TemplateModel();
        $sectionModel = new TemplateSectionModel();
        
        // Delete associated sections first
        $sectionModel->where('template_id', $id)->delete();
        
        // Delete the template
        $templateModel->delete($id);
        
        return $this->response->setJSON(['status' => 'success']);
    }

    public function createTest()
    {
        $model = new TestModel();
        $data = $this->request->getJSON(true);
        if (!$data) $data = $this->request->getPost();
        
        $id = $model->insert([
            'name' => $data['name'],
            'category' => $data['category'],
            'code' => $data['code'],
            'assessment_type' => $data['assessment_type'] ?? null,
            'assigned_to' => $data['assigned_to'] ?? null,
            'description' => substr($data['description'] ?? '', 0, 500),
            'instructions' => $data['instructions'] ?? null,
            'shuffle_questions' => $data['shuffle_questions'] ?? false,
            'shuffle_options' => $data['shuffle_options'] ?? false,
            'proctored_exam' => $data['proctored_exam'] ?? false,
            'browser_lockdown' => $data['browser_lockdown'] ?? false,
            'show_results' => $data['show_results'] ?? false,
            'allow_backtracking' => $data['allow_backtracking'] ?? false,
            'pass_mark' => $data['pass_mark'] ?? 50,
            'attempts' => $data['attempts'] ?? 1,
            'status' => 'Draft'
        ]);

        // Create initial test pack if provided
        if (!empty($data['pack_name']) && !empty($data['template_id'])) {
            $tpModel = new TestPackModel();
            $tpModel->insert([
                'assessment_id' => $id,
                'pack_name' => $data['pack_name'],
                'user_role' => 'Assigned Roles',
                'template_id' => $data['template_id'],
                'duration' => 60
            ]);
        }

        return $this->response->setJSON(['status' => 'success', 'id' => $id]);
    }

    public function updateTestPackTemplate() {
        $data = $this->request->getJSON(true);
        $packId = $data['pack_id'] ?? null;
        $templateId = $data['template_id'] ?? null;

        if (!$packId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Pack ID missing']);
        }

        $tpModel = new TestPackModel();
        $tpModel->update($packId, ['template_id' => $templateId]);

        return $this->response->setJSON(['status' => 'success']);
    }

    public function updateTest($id)
    {
        $model = new TestModel();
        $data = $this->request->getJSON(true);
        if (!$data) $data = $this->request->getPost();
        
        $model->update($id, [
            'name' => $data['name'],
            'category' => $data['category'],
            'code' => $data['code'],
            'assessment_type' => $data['assessment_type'] ?? null,
            'assigned_to' => $data['assigned_to'] ?? null,
            'description' => substr($data['description'] ?? '', 0, 500),
            'status' => $data['status'] ?? 'Active'
        ]);
        return $this->response->setJSON(['status' => 'success']);
    }

    public function deleteTest($id)
    {
        $TestModel = new TestModel();
        $testPackModel = new TestPackModel();
        
        // Delete associated test packs (and their questions)
        $packs = $testPackModel->where('assessment_id', $id)->findAll();
        foreach($packs as $p) {
            $this->deletePack($p['id']);
        }
        
        // Delete the Test
        $TestModel->delete($id);
        
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
            'template_id' => $data['template_id'],
            'duration' => $data['duration'] ?? 60
        ]);
        return $this->response->setJSON(['status' => 'success', 'id' => $id]);
    }

    public function deletePack($id)
    {
        $testPackModel = new TestPackModel();
        $questionModel = new QuestionModel();
        
        // Delete associated questions
        $questionModel->where('test_pack_id', $id)->delete();
        
        // Delete the pack
        $testPackModel->delete($id);
        
        return $this->response->setJSON(['status' => 'success']);
    }

    public function uploadQuestions()
    {
        $model = new QuestionModel();
        $file = $this->request->getFile('file');
        $testPackId = $this->request->getPost('test_pack_id');
        $type = $this->request->getPost('type'); // 'MCQ' or '2 Marks'

        // Get the limit from template
        $tpModel = new \App\Models\TestPackModel();
        $tp = $tpModel->find($testPackId);
        if (!$tp) return redirect()->back()->with('error', 'Batch not found');

        $tsModel = new \App\Models\TemplateSectionModel();
        
        // Map upload type to template marks_type
        $marksTypeMap = [
            'MCQ' => 'Multiple Choice',
            '2 Marks' => 'Short Answer'
        ];
        $targetMarksType = $marksTypeMap[$type] ?? $type;

        $sections = $tsModel->where('template_id', $tp['template_id'])
                            ->where('marks_type', $targetMarksType)
                            ->findAll();
        
        $limit = 0;
        foreach($sections as $s) {
            $limit += (int)$s['num_questions'];
        }

        if ($file->isValid() && !$file->hasMoved()) {
            $csvData = file_get_contents($file->getTempName());
            $lines = explode("\n", $csvData);
            
            // Skip instruction lines (starting with #)
            $actualLines = [];
            foreach($lines as $line) {
                if (trim($line) === '' || strpos(trim($line), '#') === 0) continue;
                $actualLines[] = $line;
            }

            if (empty($actualLines)) return redirect()->back()->with('error', 'CSV is empty or only contains instructions');

            $headers = str_getcsv(array_shift($actualLines));
            
            // Check count
            if (count($actualLines) > $limit) {
                return redirect()->back()->with('error', "Upload limit exceeded! This template only allows {$limit} {$type} questions. You tried to upload " . count($actualLines));
            }

            foreach ($actualLines as $line) {
                if (empty(trim($line))) continue;
                $row = str_getcsv($line);
                if (count($row) < count($headers)) continue;
                
                $data = array_combine($headers, $row);
                $data['test_pack_id'] = $testPackId;
                $data['type'] = ($type == 'MCQ') ? 'MCQ' : '2-Mark';
                $model->insert($data);
            }
            return redirect()->back()->with('success', 'Questions uploaded successfully.');
        }

        return redirect()->back()->with('error', 'Invalid file upload.');
    }

    public function downloadTemplate($type)
    {
        $filename = ($type == 'mcq') ? 'mcq_template.csv' : 'marks_template.csv';
        
        if ($type == 'mcq') {
            $instructions = "# MCQ UPLOAD INSTRUCTIONS:\n"
                          . "# 1. Provide the question text in the 'question' column.\n"
                          . "# 2. options A, B, C, D are required.\n"
                          . "# 3. 'correct_answer' must be one of: A, B, C, D.\n"
                          . "# 4. 'marks' should be 1.\n"
                          . "# 5. 'knowledge_type': Conceptual, Factual, Procedural, or Metacognitive.\n";
            $header = "question,option_a,option_b,option_c,option_d,correct_answer,marks,knowledge_type";
        } else {
            $instructions = "# 2-MARK UPLOAD INSTRUCTIONS:\n"
                          . "# 1. Provide the question text in the 'question' column.\n"
                          . "# 2. Provide the 'expected_answer' for evaluation.\n"
                          . "# 3. 'marks' should be 2.\n"
                          . "# 4. 'knowledge_type': Conceptual, Factual, Procedural, or Metacognitive.\n";
            $header = "question,expected_answer,marks,knowledge_type";
        }

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $instructions . $header . "\n";
        exit;
    }

    public function saveQuestion()
    {
        $model = new QuestionModel();
        $data = $this->request->getJSON(true);
        if (!$data) $data = $this->request->getPost();

        if (empty($data['test_pack_id'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Batch ID is missing']);
        }

        $id = $model->insert($data);
        if ($id) {
            return $this->response->setJSON(['status' => 'success', 'id' => $id]);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to save question']);
    }

    public function getPackQuestions($id)
    {
        $questionModel = new QuestionModel();
        $questions = $questionModel->where('test_pack_id', $id)->findAll();
        
        $tpModel = new TestPackModel();
        $pack = $tpModel->find($id);
        
        // Fetch template info for the header
        $templateModel = new TemplateModel();
        $template = $templateModel->find($pack['template_id']);

        $sections = (new TemplateSectionModel())->where('template_id', $template['id'])->findAll();
        
        return $this->response->setJSON([
            'status' => 'success',
            'pack' => $pack,
            'template' => $template,
            'sections' => $sections,
            'questions' => $questions
        ]);
    }
}
