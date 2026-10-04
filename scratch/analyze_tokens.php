<?php
$tokens = token_get_all(file_get_contents('resources/views/admin/sub_elemens/index.php'));
$line = 1;
$stack = [];

foreach ($tokens as $token) {
    if (is_array($token)) {
        $name = token_name($token[0]);
        $text = $token[1];
        $line = $token[2];
        if (in_array($token[0], [T_IF, T_FOREACH, T_FOR, T_WHILE, T_SWITCH])) {
            $stack[] = [$name, $text, $line];
        } elseif (in_array($token[0], [T_ENDIF, T_ENDFOREACH, T_ENDFOR, T_ENDWHILE, T_ENDSWITCH])) {
            $last = array_pop($stack);
            // echo "Matched $name (line $line) with {$last[0]} (line {$last[2]})\n";
        }
    }
}

if (!empty($stack)) {
    echo "Unclosed blocks:\n";
    print_r($stack);
} else {
    echo "All control structures are properly closed!\n";
}
