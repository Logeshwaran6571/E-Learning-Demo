<?php
$path = 'c:\xampp\htdocs\codeigniter\Final_Assessment\Assessment\app\Views\workflow.php';
$content = file_get_contents($path);

$old1 = '                    <button class="btn-red-rounded px-6" onclick="openQuestionBankModal()">
                        <i class="bi bi-journal-bookmark me-2"></i> Question Bank
                    </button>';
$old2 = '                    <button class="btn-red-rounded px-6" onclick="openQuickTemplateModal()">
                        <i class="bi bi-file-earmark-plus me-2"></i> Create Template
                    </button>';
$old3 = '                    <button class="btn-red-rounded px-6" onclick="openCreateTest()">
                        <i class="bi bi-plus-lg me-2"></i> New Test Name
                    </button>';

// Reorder
$new = $old3 . "\n" . $old1 . "\n" . $old2;

// Since exact match might fail due to line endings, let's use a more flexible regex or just find the block
$block = $old1 . "\n" . $old2 . "\n" . $old3;

if (strpos($content, $old1) !== false) {
    $content = str_replace($block, $new, $content);
    file_put_contents($path, $content);
    echo "Success";
} else {
    echo "Failed to find block";
}
?>
