<?php
require "../core/Router.php";
require '../compat.php';

benchmark();
for ($i = 0; $i < 36000; $i++) {
    \core\Router::buildUrl("m.c.a/abc/d:e/f:{$i}");
}
print_r(benchmark('end', 1));
