<?php
$content = file_get_contents('resources/views/admin/sub_elemens/index.php');

// Let's test eval/compile of the file directly using token_get_all or php -l
exec('"C:\\laragon\\bin\\php\\php-8.1.10-Win32-vs16-x64\\php.exe" -l "resources/views/admin/sub_elemens/index.php" 2>&1', $output, $returnVar);
echo implode("\n", $output) . "\n";
