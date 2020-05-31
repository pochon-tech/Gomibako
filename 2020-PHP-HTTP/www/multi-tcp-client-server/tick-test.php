<?php
declare(ticks=1);

// Mac での ¥n は、option + ¥
echo "hello\n";
function handler() {
    echo "tick\n";
}

echo "hello\n";
register_tick_function('handler');

echo "hello\n";
$test = 1;
if ($test == 1) { 
    echo "1\n";
}