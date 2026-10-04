<?php
$lines = file('resources/views/admin/sub_elemens/index.php');
for ($i = 500; $i <= 550; $i++) {
    echo ($i + 1) . ': ' . $lines[$i];
}
