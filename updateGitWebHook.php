<?php
$rawUrl = "https://raw.githubusercontent.com/MispiOS/site/refs/heads/main/webhook/githubHook.php";

$f = fopen("webhook/githubHook.php", "w");

if($f == false) die;

$newContent = file_get_contents($rawUrl);

if($newContent == false) die;

fwrite($f, $newContent);