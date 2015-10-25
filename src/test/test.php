<?php
require "../core/mvc/Router.php";
require '../compat.php';

benchmark();
for ($i = 0; $i < 36000; $i++) {
    \core\mvc\Router::buildUrl("m.c.a/abc/d:e/f:{$i}");
}
print_r(benchmark('end', 1));
