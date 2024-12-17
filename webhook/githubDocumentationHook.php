<?php

$minimalVariables = array("HTTP_USER_AGENT", "HTTP_X_GITHUB_HOOK_ID", "HTTP_X_GITHUB_EVENT", "HTTP_X_GITHUB_DELIVERY", "HTTP_X_GITHUB_HOOK_INSTALLATION_TARGET_TYPE");

foreach($minimalVariables as $var) {
    if(!isset($_SERVER[$var])) die;
}

$user_agent = $_SERVER["HTTP_USER_AGENT"];
$hook_id = $_SERVER["HTTP_X_GITHUB_HOOK_ID"];
$event = $_SERVER["HTTP_X_GITHUB_EVENT"];
$delivery = $_SERVER["HTTP_X_GITHUB_DELIVERY"];
$typeRessource = $_SERVER["HTTP_X_GITHUB_HOOK_INSTALLATION_TARGET_TYPE"];

if(!str_starts_with($user_agent, "GitHub-Hookshot/") ||
   strtolower($event) != "release" ||
   strtolower($typeRessource) != "repository") die;

$content = file_get_contents("php://input");

if($content == false) die;

ob_end_clean();
header("Connection: close");
ignore_user_abort(true);
ob_start();
$size = ob_get_length();
header("Content-Length: $size");
ob_end_flush();
flush();

$json = json_decode($content, true);

if($json == null) die;

if($json["action"] != "created") die;

$nomFichier = "docs.zip";

$downloadURL = str_replace("/tag/", "/downloads/", $json["release"]["html_url"]) . "/" . $nomFichier;

file_put_contents("./" . $nomFichier, file_get_contents($downloadURL));

$zip = new ZipArchive;
$zip->open("./" . $nomFichier);
$zip->extractTo("../docs/", );
$zip->close();

unlink("./" . $nomFichier);

// télécharger le ZIP (github.com/.../.../releases/latest/TRUC.zip)
// extraire le ZIP
// supprimer le contenue de /docs
// mettre le contenu du ZIP dans /docs