<?php
$explodedPath = explode("/", $_SERVER['SCRIPT_NAME']);
$projectPart = $explodedPath[count($explodedPath) - 2];

$githubLink = "https://raw.githubusercontent.com/MispiOS/documentation/refs/heads/main/" . $projectPart . "/";

$askedDocPage = substr($_SERVER["REQUEST_URI"], strlen($projectPart) + 2);
if(str_ends_with($askedDocPage, "/")) {
    $askedDocPage = substr($askedDocPage,0, strlen($askedDocPage) - 1);
}
$askedDocPage = (strlen($askedDocPage) == 0 ? "home.md" : $askedDocPage . (str_ends_with($askedDocPage, ".md") ? "" : ".md"));
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