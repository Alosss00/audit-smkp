<?php
$content = file_get_contents('resources/views/admin/sub_elemens/index.php');
$lines = explode("\n", $content);

for ($i = 0; $i < count($lines); $i++) {
    $chunk = implode("\n", array_slice($lines, 0, $i + 1));
    $tmpFile = __DIR__ . '/tmp_test.php';
    file_put_contents($tmpFile, $chunk);
    
    // Check syntax
    exec('"C:\\laragon\\bin\\php\\php-8.1.10-Win32-vs16-x64\\php.exe" -l "' . $tmpFile . '" 2>&1', $output, $returnVar);
    if ($returnVar !== 0) {
        echo "First syntax error at line " . ($i + 1) . ":\n";
        echo "Line content: " . $lines[$i] . "\n";
        echo "PHP Error: " . implode("\n", $output) . "\n";
        unlink($tmpFile);
        exit;
    }
    unset($output);
}
echo "No syntax errors found in whole file!\n";
unlink($tmpFile);
