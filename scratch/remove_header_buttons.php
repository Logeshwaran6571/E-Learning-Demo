<?php
$path = 'c:\xampp\htdocs\codeigniter\Final_Assessment\Assessment\app\Views\workflow.php';
$content = file_get_contents($path);

// Use a regex to remove the two buttons
$pattern = '/<button\s+class="px-5 py-2.5 rounded-xl border border-blue-100 text-\[#2563eb\] font-bold text-\[13px\] flex items-center gap-2 hover:bg-blue-50 transition-all shadow-sm"\s+onclick="switchMainTab\(\'results\'\)">.*?<\/button>\s*<button\s+class="px-5 py-2.5 rounded-xl border border-blue-100 text-\[#2563eb\] font-bold text-\[13px\] flex items-center gap-2 hover:bg-blue-50 transition-all shadow-sm"\s+onclick="switchMainTab\(\'execution\'\)">.*?<\/button>/s';

if (preg_match($pattern, $content, $matches)) {
    $content = preg_replace($pattern, '', $content);
    file_put_contents($path, $content);
    echo "Success";
} else {
    echo "Failed to find buttons with regex";
}
?>
