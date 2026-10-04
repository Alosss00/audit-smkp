<?php
$tokens = token_get_all(file_get_contents('resources/views/admin/sub_elemens/index.php'));

foreach ($tokens as $token) {
    if (is_array($token)) {
        if ($token[2] >= 555 && $token[2] <= 575) {
            echo "Line {$token[2]}: " . token_name($token[0]) . " => " . json_encode($token[1]) . "\n";
        }
    } else {
        // Single character token like % or . or ( or )
        // Let's find line number by context
        echo "CHAR: " . json_encode($token) . "\n";
    }
}
