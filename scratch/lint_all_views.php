<?php
$viewsDir = 'resources/views';
$phpBinary = 'C:\\laragon\\bin\\php\\php-8.1.10-Win32-vs16-x64\\php.exe';

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsDir));
$phpFiles = [];
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $phpFiles[] = $file->getPathname();
    }
}

echo "Found " . count($phpFiles) . " PHP view files. Linting...\n";
$errors = 0;
foreach ($phpFiles as $file) {
    exec("\"$phpBinary\" -l \"$file\" 2>&1", $output, $returnCode);
    if ($returnCode !== 0) {
        echo "[SYNTAX ERROR] $file:\n" . implode("\n", $output) . "\n\n";
        $errors++;
    }
    unset($output);
}

if ($errors === 0) {
    echo "SUCCESS: All " . count($phpFiles) . " PHP view files parsed cleanly with ZERO syntax errors!\n";
} else {
    echo "TOTAL SYNTAX ERRORS FOUND: $errors\n";
}
