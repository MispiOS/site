<?php

$explodedPath = explode("/", $_SERVER['SCRIPT_NAME']);
$projectPart = $explodedPath[count($explodedPath) - 2];

$githubLink = "https://raw.githubusercontent.com/MispiOS/documentation/refs/heads/main/" . $projectPart . "/";

$explodedAskedPage = explode("?", $_SERVER["REQUEST_URI"]);
$askedDocPage = (count($explodedAskedPage) == 2 ? $explodedAskedPage[1] : "home.md");
$askedDocPage = str_replace("\/.\/", "\/", $askedDocPage);

$link = $githubLink . $askedDocPage;

$content = file_get_contents($link);

if($content == false) {
    echo "pas trouvé";
    //header("pageNotFound.php");
} else {
    include("../Parsedown.php");
    $parser = new Parsedown();
    $parser->setUrlsLinked(false);
    $content = $parser->text($content);
    echo $content;
}