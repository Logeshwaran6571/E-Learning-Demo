<?php

namespace App\Controllers;

use App\Models\TemplateModel;
use App\Models\TemplateSectionModel;
use App\Models\TestModel;
use App\Models\TestPackModel;
use App\Models\QuestionModel;
use App\Models\EmployeeModel;
use App\Models\QuestionBankModel;
use App\Models\QuestionBankRepositoryModel;
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

        $allTestsForUsage = $TestModel->select('id, name')->findAll();
        $testsById = [];
        foreach ($allTestsForUsage as $row) {
            $testsById[$row['id']] = $row['name'];
        }
        $allPacksForUsage = $testPackModel
            ->select('id, pack_name, template_id, assessment_id')
            ->findAll();
        $usageByTemplate = [];
        foreach ($allPacksForUsage as $pack) {
            $tid = $pack['template_id'] ?? null;
            if (!$tid)
                continue;
            if (!isset($usageByTemplate[$tid]))
                $usageByTemplate[$tid] = [];
            $usageByTemplate[$tid][] = [
                'pack_id' => $pack['id'],
                'pack_name' => $pack['pack_name'],
                'assessment_id' => $pack['assessment_id'],
                'assessment_name' => $testsById[$pack['assessment_id']] ?? 'Unknown Test',
            ];
        }

        foreach ($templates as &$t) {
            $t['sections'] = $sectionModel->where('template_id', $t['id'])->findAll();
            $t['questions'] = (new QuestionModel())->where('template_id', $t['id'])->findAll();
            $t['usage'] = $usageByTemplate[$t['id']] ?? [];
        }
        unset($t);

        $Tests = $TestModel->orderBy('id', 'DESC')->findAll();
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
            $this->normalizeIntroVideosOnTestRow($a);
        }

        try {
            $employees = (new EmployeeModel())->findAll();

            $repoModel = new QuestionBankRepositoryModel();
            $qModel = new QuestionBankModel();

            $repos = $repoModel->orderBy('id', 'DESC')->findAll();
            $questionBank = [];

            foreach ($repos as $repo) {
                $questionBank[] = [
                    'id' => $repo['id'],
                    'name' => $repo['name'],
                    'questions' => $qModel->where('repository_id', $repo['id'])->findAll()
                ];
            }

        } catch (\Exception $e) {
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
                [
                    'id' => 1,
                    'name' => 'General',
                    'questions' => [
                        ['id' => 1, 'question' => 'What is the output of 2 + "2"?', 'type' => 'MCQ', 'category' => 'General', 'option_a' => '4', 'option_b' => '22', 'option_c' => 'Error', 'option_d' => 'None', 'correct_answer' => 'B', 'marks' => 1],
                        ['id' => 2, 'question' => 'Explain closures in JavaScript.', 'type' => 'Short Answer', 'category' => 'General', 'marks' => 2]
                    ]
                ]
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
                    'marks_type' => $this->normalizeTemplateSectionMarksType($sec['type'] ?? 'MCQ'),
                    'num_questions' => $sec['count'] ?? 0,
                    'marks_per_question' => $sec['marks'] ?? 1
                ]);
            }
        }

        // Handle questions
        $questionModel = new QuestionModel();
        $questionModel->where('template_id', $templateId)->delete();
        if (isset($data['questions']) && is_array($data['questions'])) {
            foreach ($data['questions'] as $q) {
                $questionModel->insert([
                    'template_id' => $templateId,
                    'section_idx' => $q['sectionIdx'] ?? 0,
                    'type' => $this->normalizePersistedPackQuestionType((string) ($q['type'] ?? 'MCQ')),
                    'question' => $q['question'] ?? '',
                    'option_a' => $q['option_a'] ?? '',
                    'option_b' => $q['option_b'] ?? '',
                    'option_c' => $q['option_c'] ?? '',
                    'option_d' => $q['option_d'] ?? '',
                    'correct_answer' => $q['correct_answer'] ?? '',
                    'marks' => $q['marks'] ?? 1,
                    'pedagogy' => $this->normalizePedagogy($q['pedagogy'] ?? null),
                    'knowledge_type' => $q['knowledge_type'] ?? $q['pedagogy'] ?? null,
                ]);
            }
        }

        // Fetch the fresh data to return
        $savedTemplate = $templateModel->find($templateId);
        $savedTemplate['structure'] = json_encode($data['sections'] ?? []);
        $savedTemplate['sections'] = $sectionModel->where('template_id', $templateId)->findAll();
        $savedTemplate['questions'] = $questionModel->where('template_id', $templateId)->findAll();

        return $this->response->setJSON([
            'status' => 'success',
            'template' => $savedTemplate
        ]);
    }

    private function calculateTotalMarks($sections)
    {
        $total = 0;
        foreach ($sections as $s) {
            $total += ($s['count'] ?? 0) * ($s['marks'] ?? 0);
        }
        return $total;
    }

    private function syncTemplateQuestions(int $templateId, array $questions): void
    {
        $questionModel = new QuestionModel();
        $questionModel->where('template_id', $templateId)->delete();

        foreach ($questions as $q) {
            if (trim((string) ($q['question'] ?? '')) === '') {
                continue;
            }

            $questionModel->insert([
                'template_id' => $templateId,
                'section_idx' => $q['sectionIdx'] ?? $q['section_idx'] ?? 0,
                'type' => $this->normalizePersistedPackQuestionType((string) ($q['type'] ?? 'MCQ')),
                'question' => $q['question'] ?? '',
                'option_a' => $q['option_a'] ?? '',
                'option_b' => $q['option_b'] ?? '',
                'option_c' => $q['option_c'] ?? '',
                'option_d' => $q['option_d'] ?? '',
                'correct_answer' => $q['correct_answer'] ?? '',
                'marks' => $q['marks'] ?? 1,
                'knowledge_type' => $q['knowledge_type'] ?? $q['pedagogy'] ?? null,
                'pedagogy' => $this->normalizePedagogy($q['pedagogy'] ?? null),
            ]);
        }
    }

    private function getTemplateWithRelations(int $templateId): ?array
    {
        $templateModel = new TemplateModel();
        $sectionModel = new TemplateSectionModel();
        $questionModel = new QuestionModel();

        $template = $templateModel->find($templateId);
        if (!$template) {
            return null;
        }

        $template['sections'] = $sectionModel->where('template_id', $templateId)->findAll();
        $template['questions'] = $questionModel->where('template_id', $templateId)->findAll();

        return $template;
    }

    public function deleteTemplate($id)
    {
        $templateModel = new TemplateModel();
        $sectionModel = new TemplateSectionModel();

        // Delete associated sections first
        $sectionModel->where('template_id', $id)->delete();

        // Delete associated questions
        (new QuestionModel())->where('template_id', $id)->delete();

        // Delete the template
        $templateModel->delete($id);

        return $this->response->setJSON(['status' => 'success']);
    }

    public function createTest()
    {
        $model = new TestModel();
        $data = $this->request->getJSON(true);
        if (!$data)
            $data = $this->request->getPost();

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
            'add_video' => !empty($data['add_video']),
            'intro_videos' => $this->normalizeIntroVideosPayload($data['intro_videos'] ?? null),
            'pass_mark' => $data['pass_mark'] ?? 50,
            'attempts' => $data['attempts'] ?? 1,
            'pedagogy' => $this->normalizePedagogy($data['pedagogy'] ?? null),
            'status' => 'Draft'
        ]);

        if ($id === false) {
            $msg = $model->errors();
            $flat = is_array($msg) ? implode(' ', $msg) : '';
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $flat !== '' ? $flat : 'Could not save assessment (check database columns match migrations).',
            ]);
        }

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

    public function updateTestPackTemplate()
    {
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
        if (!$data)
            $data = $this->request->getPost();

        $update = [
            'name' => $data['name'],
            'category' => $data['category'],
            'code' => $data['code'],
            'assessment_type' => $data['assessment_type'] ?? null,
            'assigned_to' => $data['assigned_to'] ?? null,
            'description' => substr($data['description'] ?? '', 0, 500),
            'instructions' => $data['instructions'] ?? null,
            'proctored_exam' => $data['proctored_exam'] ?? false,
            'browser_lockdown' => $data['browser_lockdown'] ?? false,
            'show_results' => $data['show_results'] ?? false,
            'allow_backtracking' => $data['allow_backtracking'] ?? false,
            'add_video' => !empty($data['add_video']),
            'pedagogy' => $this->normalizePedagogy($data['pedagogy'] ?? null),
            'status' => $data['status'] ?? 'Active'
        ];
        if (array_key_exists('intro_videos', $data)) {
            $update['intro_videos'] = $this->normalizeIntroVideosPayload($data['intro_videos']);
        }
        $model->update($id, $update);
        return $this->response->setJSON(['status' => 'success']);
    }

    /**
     * JSON array of public URLs (max 5). Null means omit column on create; empty array clears.
     */
    protected function normalizeIntroVideosPayload($raw): ?string
    {
        if ($raw === null || $raw === '') {
            return null;
        }
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            $raw = is_array($decoded) ? $decoded : [];
        }
        if (!is_array($raw)) {
            return null;
        }
        $urls = array_values(array_filter(array_map('trim', array_map('strval', $raw))));
        if (count($urls) > 5) {
            $urls = array_slice($urls, 0, 5);
        }
        return json_encode($urls);
    }

    /**
     * Fix intro video URLs when the app base URL (port/subfolder) differs from what was stored on upload.
     */
    protected function normalizeIntroVideosOnTestRow(array &$row): void
    {
        if (empty($row['intro_videos'])) {
            return;
        }
        $json = is_string($row['intro_videos']) ? $row['intro_videos'] : json_encode($row['intro_videos']);
        $fixed = $this->rewriteIntroVideosJsonForClient($json);
        if ($fixed !== null) {
            $row['intro_videos'] = $fixed;
        }
    }

    protected function rewriteIntroVideosJsonForClient(?string $json): ?string
    {
        if ($json === null || $json === '') {
            return $json;
        }
        $arr = json_decode($json, true);
        if (!is_array($arr)) {
            return $json;
        }
        $out = [];
        foreach ($arr as $u) {
            $u = trim((string) $u);
            if ($u === '') {
                continue;
            }
            if (preg_match('#(/uploads/assessment_intro/.+)$#', $u, $m)) {
                $out[] = $m[1];
            } elseif (preg_match('#(/assessment_intro/.+)$#', $u, $m)) {
                // Backward compatibility: older records may miss the /uploads segment.
                $out[] = '/uploads' . $m[1];
            } elseif (strpos($u, 'uploads/assessment_intro/') === 0) {
                $out[] = '/' . $u;
            } elseif (strpos($u, 'assessment_intro/') === 0) {
                $out[] = '/uploads/' . $u;
            } elseif (strpos($u, '/uploads/assessment_intro/') === 0) {
                $out[] = $u;
            } else {
                $out[] = $u;
            }
        }
        return json_encode(array_values($out));
    }

    /**
     * Map UI/API question type tokens to persisted questions.type values.
     */
    protected function normalizePersistedPackQuestionType(string $type): string
    {
        $t = strtolower(trim($type));

        return $t === 'mcq' ? 'MCQ' : '2-Mark';
    }

    /**
     * True when batch CSV upload treats rows as MCQ (vs descriptive / short answer).
     */
    protected function uploadPackUsesMcqQuestions(?string $type): bool
    {
        return strtolower(trim((string) $type)) === 'mcq';
    }

    protected function normalizeQuestionBankStoredType(?string $type): string
    {
        $t = strtolower(trim((string) $type));

        return $t === 'mcq' ? 'MCQ' : 'Short Answer';
    }

    protected function normalizePedagogy($value): ?string
    {
        $v = trim((string) ($value ?? ''));

        return $v === '' ? null : substr($v, 0, 128);
    }

    /**
     * Persisted template_section.marks_type must align with uploadQuestions() lookups
     * (Multiple Choice vs Short Answer).
     */
    protected function normalizeTemplateSectionMarksType(?string $type): string
    {
        $raw = trim((string) $type);
        $t = strtolower($raw);

        if ($t === 'mcq' || str_contains($t, 'multiple')) {
            return 'Multiple Choice';
        }

        if ($t === 'descriptive' || str_contains($t, 'short answer') || str_contains($t, 'short_answer')) {
            return 'Short Answer';
        }

        return $raw !== '' ? $raw : 'Multiple Choice';
    }

    public function uploadIntroVideos($assessmentId)
    {
        $model = new TestModel();
        $row = $model->find((int) $assessmentId);
        if (!$row) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Assessment not found']);
        }

        $existing = [];
        if (!empty($row['intro_videos'])) {
            $decoded = json_decode($row['intro_videos'], true);
            if (is_array($decoded)) {
                $existing = $decoded;
            }
        }

        $uploaded = $this->request->getFileMultiple('videos');
        if (empty($uploaded)) {
            $uploaded = $this->request->getFileMultiple('videos[]');
        }
        if (!is_array($uploaded)) {
            $uploaded = [];
        }
        if (empty($uploaded)) {
            $single = $this->request->getFile('videos');
            if ($single && $single->isValid()) {
                $uploaded = [$single];
            }
        }

        $allowedMime = [
            'video/mp4',
            'video/webm',
            'video/quicktime',
            'video/x-msvideo',
            'video/mpeg',
            'video/ogg',
        ];
        $allowedExt = ['mp4', 'webm', 'mov', 'avi', 'mpeg', 'mpg', 'ogv', 'm4v', 'mkv'];
        $maxFiles = 5;
        $maxBytes = 120 * 1024 * 1024; // 120 MB per file

        $rootUpload = FCPATH . 'uploads';
        if (!is_dir($rootUpload)) {
            mkdir($rootUpload, 0755, true);
        }

        $targetDir = FCPATH . 'uploads/assessment_intro/' . (int) $assessmentId;
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $acceptedCount = 0;
        $rejectedCount = 0;
        foreach ($uploaded as $file) {
            if (count($existing) >= $maxFiles) {
                break;
            }
            if (!$file || !$file->isValid() || $file->hasMoved()) {
                $rejectedCount++;
                continue;
            }
            $mime = $file->getMimeType();
            $ext = strtolower((string) $file->getExtension());
            $mimeAllowed = $mime && in_array($mime, $allowedMime, true);
            $extAllowed = $ext !== '' && in_array($ext, $allowedExt, true);
            if (!$mimeAllowed && !$extAllowed) {
                $rejectedCount++;
                continue;
            }
            if ($file->getSize() > $maxBytes) {
                $rejectedCount++;
                continue;
            }

            $newName = $file->getRandomName();
            $file->move($targetDir, $newName);
            $relative = '/uploads/assessment_intro/' . (int) $assessmentId . '/' . $newName;
            $existing[] = $relative;
            $acceptedCount++;
        }

        if ($acceptedCount === 0 && !empty($uploaded)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $rejectedCount > 0
                    ? 'No valid video files were uploaded. Please upload MP4/WebM/MOV/AVI/MKV files.'
                    : 'No video files were uploaded.',
            ]);
        }

        $model->update((int) $assessmentId, ['intro_videos' => json_encode($existing)]);

        return $this->response->setJSON([
            'status' => 'success',
            'intro_videos' => $existing,
        ]);
    }

    public function deleteTest($id)
    {
        $TestModel = new TestModel();
        $testPackModel = new TestPackModel();

        // Delete associated test packs (and their questions)
        $packs = $testPackModel->where('assessment_id', $id)->findAll();
        foreach ($packs as $p) {
            $this->deletePack($p['id']);
        }

        // Delete the Test
        $TestModel->delete($id);

        return $this->response->setJSON(['status' => 'success']);
    }

    public function createTestPack()
    {
        $model = new TestPackModel();
        $questionModel = new QuestionModel();
        $data = $this->request->getPost();
        $templateId = (isset($data['template_id']) && $data['template_id'] !== '')
            ? (int) $data['template_id']
            : null;

        $packData = [
            'assessment_id' => $data['assessment_id'],
            'pack_name' => $data['pack_name'],
            'user_role' => $data['user_role'] ?? 'General',
            'template_id' => $templateId,
            'duration' => $data['duration'] ?? 60,
            'start_time' => $data['start_time'] ?? null,
            'end_time' => $data['end_time'] ?? null,
            'scheduled_date' => $data['scheduled_date'] ?? null,
            'instructions' => $data['instructions'] ?? null,
            'pass_mark' => $data['pass_mark'] ?? 50,
            'max_attempts' => $data['max_attempts'] ?? 1,
            'shuffle_questions' => $data['shuffle_questions'] ?? 0,
            'shuffle_options' => $data['shuffle_options'] ?? 0,
            'proctored_exam' => $data['proctored_exam'] ?? 0,
            'browser_lockdown' => $data['browser_lockdown'] ?? 0,
            'show_results' => $data['show_results'] ?? 0,
            'allow_backtracking' => $data['allow_backtracking'] ?? 0,
            'candidates' => $data['candidates'] ?? '',
            'candidates_type' => $data['candidates_type'] ?? 'all'
        ];

        if (isset($data['id']) && !empty($data['id'])) {
            $model->update($data['id'], $packData);
            $packId = $data['id'];
        } else {
            $packId = $model->insert($packData);
        }

        $decodedQuestions = [];

        // Save manual questions if provided
        if (isset($data['manual_questions'])) {
            $decodedQuestions = json_decode($data['manual_questions'], true);
            if (is_array($decodedQuestions)) {
                // If updating, we might want to keep existing ones or clear them
                // For this workflow, we'll clear and re-insert the manual ones
                $questionModel->where('test_pack_id', $packId)->delete();

                foreach ($decodedQuestions as $q) {
                    if (trim((string) ($q['question'] ?? '')) === '') {
                        continue;
                    }

                    $questionModel->insert([
                        'test_pack_id' => $packId,
                        'section_idx' => $q['sectionIdx'] ?? $q['section_idx'] ?? 0,
                        'question' => $q['question'],
                        'type' => $q['type'],
                        'option_a' => $q['option_a'] ?? '',
                        'option_b' => $q['option_b'] ?? '',
                        'option_c' => $q['option_c'] ?? '',
                        'option_d' => $q['option_d'] ?? '',
                        'correct_answer' => $q['correct_answer'] ?? '',
                        'marks' => $q['marks'] ?? 1,
                        'knowledge_type' => $q['knowledge_type'] ?? $q['pedagogy'] ?? null,
                        'pedagogy' => $this->normalizePedagogy($q['pedagogy'] ?? null),
                    ]);
                }
            }
        }

        // If no manual questions were sent, fall back to template questions.
        // This keeps published packs renderable in student view/evaluation flows.
        if (empty($decodedQuestions) && !empty($templateId)) {
            $templateQuestions = $questionModel->where('template_id', $templateId)->findAll();
            if (!empty($templateQuestions)) {
                $questionModel->where('test_pack_id', $packId)->delete();
                foreach ($templateQuestions as $q) {
                    if (trim((string) ($q['question'] ?? '')) === '') {
                        continue;
                    }

                    $questionModel->insert([
                        'test_pack_id' => $packId,
                        'section_idx' => $q['section_idx'] ?? 0,
                        'question' => $q['question'],
                        'type' => $q['type'] ?? 'MCQ',
                        'option_a' => $q['option_a'] ?? '',
                        'option_b' => $q['option_b'] ?? '',
                        'option_c' => $q['option_c'] ?? '',
                        'option_d' => $q['option_d'] ?? '',
                        'correct_answer' => $q['correct_answer'] ?? '',
                        'marks' => $q['marks'] ?? 1,
                        'knowledge_type' => $q['knowledge_type'] ?? $q['pedagogy'] ?? null,
                        'pedagogy' => $this->normalizePedagogy($q['pedagogy'] ?? null),
                    ]);
                }
            }
        }

        $syncedTemplate = null;
        if (
            !empty($templateId) &&
            ($data['sync_template_questions'] ?? '0') === '1' &&
            is_array($decodedQuestions)
        ) {
            $this->syncTemplateQuestions($templateId, $decodedQuestions);
            $syncedTemplate = $this->getTemplateWithRelations($templateId);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'id' => $packId,
            'template' => $syncedTemplate,
        ]);
    }

    public function publishTestPack()
    {
        $id = $this->request->getPost('id');
        if (!$id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Batch ID is missing']);
        }

        $model = new \App\Models\TestPackModel();
        try {
            $model->update($id, ['status' => 'published']);
            return $this->response->setJSON(['status' => 'success']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Mark a batch as having published final results to candidates (visible on live tests when complete).
     */
    public function publishPackResults()
    {
        $id = $this->request->getPost('id');
        if (!$id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Batch ID is missing']);
        }

        $model = new \App\Models\TestPackModel();
        try {
            $row = $model->find($id);
            if (!$row) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Batch not found']);
            }
            if (($row['status'] ?? '') !== 'published') {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Publish the batch first before releasing results.']);
            }
            $model->update($id, ['results_published' => 1]);

            return $this->response->setJSON(['status' => 'success', 'results_published' => 1]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
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
        $type = $this->request->getPost('type'); // MCQ vs descriptive UI token

        // Get the limit from template
        $tpModel = new \App\Models\TestPackModel();
        $tp = $tpModel->find($testPackId);
        if (!$tp)
            return redirect()->back()->with('error', 'Batch not found');

        $tsModel = new \App\Models\TemplateSectionModel();

        // Map upload type to template marks_type
        $isMcq = $this->uploadPackUsesMcqQuestions($type);
        $targetMarksType = $isMcq ? 'Multiple Choice' : 'Short Answer';
        $friendlyKind = $isMcq ? 'MCQ' : 'descriptive question';

        $sections = $tsModel->where('template_id', $tp['template_id'])
            ->where('marks_type', $targetMarksType)
            ->findAll();

        $limit = 0;
        foreach ($sections as $s) {
            $limit += (int) $s['num_questions'];
        }

        if ($file->isValid() && !$file->hasMoved()) {
            $csvData = file_get_contents($file->getTempName());
            $lines = explode("\n", $csvData);

            // Skip instruction lines (starting with #)
            $actualLines = [];
            foreach ($lines as $line) {
                if (trim($line) === '' || strpos(trim($line), '#') === 0)
                    continue;
                $actualLines[] = $line;
            }

            if (empty($actualLines))
                return redirect()->back()->with('error', 'CSV is empty or only contains instructions');

            $headers = str_getcsv(array_shift($actualLines));

            // Check count
            if (count($actualLines) > $limit) {
                return redirect()->back()->with('error', "Upload limit exceeded! This template only allows {$limit} {$friendlyKind} questions. You tried to upload " . count($actualLines));
            }

            foreach ($actualLines as $line) {
                if (empty(trim($line)))
                    continue;
                $row = str_getcsv($line);
                if (count($row) < count($headers))
                    continue;

                $data = array_combine($headers, $row);
                $data['test_pack_id'] = $testPackId;
                $data['type'] = $this->normalizePersistedPackQuestionType((string) $type);
                $ped = $data['pedagogy'] ?? $data['knowledge_type'] ?? null;
                $data['pedagogy'] = $this->normalizePedagogy($ped);
                $model->insert($data);
            }
            return redirect()->back()->with('success', 'Questions uploaded successfully.');
        }

        return redirect()->back()->with('error', 'Invalid file upload.');
    }

    public function downloadTemplate($type)
    {
        $filename = ($type == 'mcq') ? 'mcq_template.csv' : 'descriptive_question_template.csv';

        if ($type == 'mcq') {
            $instructions = "# MCQ UPLOAD INSTRUCTIONS:\n"
                . "# 1. Provide the question text in the 'question' column.\n"
                . "# 2. options A, B, C, D are required.\n"
                . "# 3. 'correct_answer' must be one of: A, B, C, D.\n"
                . "# 4. 'marks' should be 1.\n"
                . "# 5. 'pedagogy': label for this item (e.g. Bloom level, subject strand). Legacy CSV may use 'knowledge_type' instead.\n";
            $header = "question,option_a,option_b,option_c,option_d,correct_answer,marks,pedagogy";
        } else {
            $instructions = "# DESCRIPTIVE QUESTION CSV — UPLOAD INSTRUCTIONS:\n"
                . "# 1. Provide the question text in the 'question' column.\n"
                . "# 2. Provide the 'expected_answer' for evaluation.\n"
                . "# 3. 'marks' records the score weight (typically 2).\n"
                . "# 4. 'pedagogy': label for this item (e.g. Bloom level, subject strand). Legacy CSV may use 'knowledge_type' instead.\n";
            $header = "question,expected_answer,marks,pedagogy";
        }

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $instructions . $header . "\n";
        exit;
    }

    public function saveQuestion()
    {
        $model = new QuestionModel();
        $raw = $this->request->getJSON(true);
        if (!$raw) {
            $raw = $this->request->getPost();
        }

        if (empty($raw['test_pack_id'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Batch ID is missing']);
        }

        $questionText = trim((string) ($raw['question'] ?? $raw['content'] ?? ''));
        $type = $this->normalizePersistedPackQuestionType((string) ($raw['type'] ?? 'MCQ'));

        $row = [
            'test_pack_id' => (int) $raw['test_pack_id'],
            'type' => $type,
            'question' => $questionText,
            'option_a' => $raw['option_a'] ?? '',
            'option_b' => $raw['option_b'] ?? '',
            'option_c' => $raw['option_c'] ?? '',
            'option_d' => $raw['option_d'] ?? '',
            'correct_answer' => $raw['correct_answer'] ?? '',
            'marks' => (int) ($raw['marks'] ?? 1),
            'knowledge_type' => $raw['knowledge_type'] ?? null,
            'pedagogy' => $this->normalizePedagogy($raw['pedagogy'] ?? null),
        ];

        $id = $model->insert($row);
        if ($id) {
            return $this->response->setJSON(['status' => 'success', 'id' => $id]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to save question']);
    }

    public function getPackQuestions($id)
    {
        $questionModel = new QuestionModel();
        $packQuestions = $questionModel->where('test_pack_id', $id)->findAll();

        $tpModel = new TestPackModel();
        $pack = $tpModel->find($id);

        // Fetch template info
        $templateModel = new TemplateModel();
        $template = $templateModel->find($pack['template_id']);

        // Fetch template questions
        $templateQuestions = $questionModel->where('template_id', $template['id'])->findAll();

        $sections = (new TemplateSectionModel())->where('template_id', $template['id'])->findAll();

        // Fetch parent assessment for shuffle settings
        $testModel = new TestModel();
        $test = $testModel->find($pack['assessment_id']);
        if ($test) {
            $this->normalizeIntroVideosOnTestRow($test);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'pack' => $pack,
            'test' => $test,
            'template' => $template,
            'sections' => $sections,
            'packQuestions' => $packQuestions,
            'templateQuestions' => $templateQuestions
        ]);
    }

    public function getPackDuration($id)
    {
        $tpModel = new TestPackModel();
        $pack = $tpModel->find($id);
        if (!$pack) {
            return $this->response->setJSON(['status' => 'error']);
        }
        return $this->response->setJSON([
            'status' => 'success',
            'duration' => (int)$pack['duration']
        ]);
    }

    public function downloadTemplateByTemplateId($id)
    {
        $templateModel = new \App\Models\TemplateModel();
        $sectionModel = new \App\Models\TemplateSectionModel();

        $template = $templateModel->find($id);
        if (!$template)
            die("Template not found");

        $sections = $sectionModel->where('template_id', $id)->findAll();

        $filename = preg_replace('/[^a-z0-9_]/i', '_', $template['name']) . "_template.csv";

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        echo "# TEMPLATE: " . $template['name'] . "\n";
        echo "# INSTRUCTIONS: Fill in the questions below. For MCQs, provide 4 options and correct answer (A-D). For Short Answer, provide expected answer in correct_answer column. Use pedagogy for the teaching/learning label.\n";
        echo "section_name,question,type,option_a,option_b,option_c,option_d,correct_answer,marks,pedagogy\n";

        foreach ($sections as $s) {
            $count = (int) ($s['num_questions'] ?? 0);
            $marks = (int) ($s['marks_per_question'] ?? 0);
            $type = (stripos(($s['marks_type'] ?? ''), 'mcq') !== false || stripos(($s['marks_type'] ?? ''), 'multiple') !== false) ? 'MCQ' : 'Short Answer';
            $sectionName = $s['marks_type'] ?? $s['name'] ?? 'Section';

            for ($i = 0; $i < $count; $i++) {
                echo '"' . $sectionName . '","","' . $type . '","","","","","","' . $marks . '",""' . "\n";
            }
        }
        exit;
    }

    public function saveQBQuestion()
    {
        $model = new QuestionBankModel();
        $data = $this->request->getJSON(true);
        if (!$data)
            $data = $this->request->getPost();

        $id = $model->insert([
            'repository_id' => $data['repository_id'],
            'question' => $data['question'],
            'type' => $this->normalizeQuestionBankStoredType((string) ($data['type'] ?? 'MCQ')),
            'option_a' => $data['option_a'] ?? '',
            'option_b' => $data['option_b'] ?? '',
            'option_c' => $data['option_c'] ?? '',
            'option_d' => $data['option_d'] ?? '',
            'correct_answer' => $data['correct_answer'] ?? '',
            'marks' => $data['marks'] ?? 1,
            'category' => $data['category'] ?? '',
            'pedagogy' => $this->normalizePedagogy($data['pedagogy'] ?? null),
        ]);

        if ($id) {
            return $this->response->setJSON(['status' => 'success', 'id' => $id]);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to save question']);
    }

    public function updateQBQuestion($id)
    {
        $model = new QuestionBankModel();
        $data = $this->request->getJSON(true);
        if (!$data)
            $data = $this->request->getPost();

        $questionText = trim((string) ($data['question'] ?? ''));
        if ($questionText === '') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Question text is required'
            ]);
        }

        $model->update($id, [
            'question' => $questionText,
            'option_a' => $data['option_a'] ?? '',
            'option_b' => $data['option_b'] ?? '',
            'option_c' => $data['option_c'] ?? '',
            'option_d' => $data['option_d'] ?? '',
            'correct_answer' => $data['correct_answer'] ?? '',
            'pedagogy' => $this->normalizePedagogy($data['pedagogy'] ?? null),
        ]);

        return $this->response->setJSON(['status' => 'success']);
    }

    public function deleteQBQuestion($id)
    {
        $model = new QuestionBankModel();
        $model->delete($id);
        return $this->response->setJSON(['status' => 'success']);
    }

    public function saveQuestionBank()
    {
        $repoModel = new QuestionBankRepositoryModel();
        $data = $this->request->getJSON(true);
        if (!$data)
            $data = $this->request->getPost();

        $id = $repoModel->insert([
            'name' => $data['name']
        ]);

        if ($id) {
            return $this->response->setJSON([
                'status' => 'success',
                'id' => $id,
                'name' => $data['name'],
                'questions' => []
            ]);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to save bank']);
    }

    public function bulkSaveQBQuestions()
    {
        $model = new QuestionBankModel();
        $data = $this->request->getJSON(true);
        if (!$data)
            $data = $this->request->getPost();

        $repoId = $data['repository_id'] ?? null;
        $questions = $data['questions'] ?? [];

        if (!$repoId || empty($questions)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Missing data']);
        }

        foreach ($questions as $q) {
            $questionText = trim((string) ($q['question'] ?? ''));
            if ($questionText === '') {
                continue;
            }

            $model->insert([
                'repository_id' => $repoId,
                'question' => $questionText,
                'type' => $this->normalizeQuestionBankStoredType((string) ($q['type'] ?? 'MCQ')),
                'option_a' => $q['option_a'] ?? '',
                'option_b' => $q['option_b'] ?? '',
                'option_c' => $q['option_c'] ?? '',
                'option_d' => $q['option_d'] ?? '',
                'correct_answer' => $q['correct_answer'] ?? '',
                'marks' => $q['marks'] ?? 1,
                'category' => $q['section_name'] ?? '',
                'pedagogy' => $this->normalizePedagogy($q['pedagogy'] ?? null),
            ]);
        }

        return $this->response->setJSON(['status' => 'success']);
    }

    public function deleteQuestionBank($id)
    {
        $repoModel = new QuestionBankRepositoryModel();
        $qModel = new QuestionBankModel();

        // Delete all questions in this bank first
        $qModel->where('repository_id', $id)->delete();

        if ($repoModel->delete($id)) {
            return $this->response->setJSON(['status' => 'success']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to delete bank']);
    }
    public function getTests()
    {
        $TestModel = new TestModel();
        $testPackModel = new TestPackModel();
        $templateModel = new TemplateModel();

        $Tests = $TestModel->orderBy('id', 'DESC')->findAll();
        foreach ($Tests as &$a) {
            $a['test_packs'] = $testPackModel->where('assessment_id', $a['id'])->findAll();
            foreach ($a['test_packs'] as &$tp) {
                $tp['template'] = $templateModel->find($tp['template_id']);
            }
            $this->normalizeIntroVideosOnTestRow($a);
        }

        return $this->response->setJSON(['status' => 'success', 'tests' => $Tests]);
    }
}
