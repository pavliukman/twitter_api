<?php
require 'vendor/autoload.php';

// loading template engine
$loader = new Twig_Loader_Filesystem('view');
$twig = new Twig_Environment($loader);
echo $twig->render('index.html');
