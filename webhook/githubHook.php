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
   strtolower($event) != "push" ||
   strtolower($typeRessource) != "repository") die;

$content = file_get_contents("php://input");

if($content == false) die;

$json = json_decode($content, true);

if($json == null) die;

$ref = $json["ref"];
if($ref != "refs/heads/main") die;

$commits = $json["commits"];

$timestamp = strtotime($commits[0]["timestamp"]);
$mostRecentCommit = $commits[0];

foreach($commits as $commit) {
    $commitTimestamp = strtotime($commit["timestamp"]);
    if($commitTimestamp > $timestamp) {
        $timestamp = $commitTimestamp;
        $mostRecentCommit = $commit;
    }
}

$added = $mostRecentCommit["added"];
$removed = $mostRecentCommit["removed"];
$modified = $mostRecentCommit["modified"];

$repositoryRawURL = "https://raw.githubusercontent.com/MispiOS/site/refs/heads/main/";
if(!str_ends_with($repositoryRawURL, "/")) {
    $repositoryRawURL .= "/";
}

foreach($added as $fileAdd) {
    $f = fopen("../" . $fileAdd, "w");
    $fileContent = file_get_contents($repositoryRawURL . $fileAdd);
    if($fileContent != false) {
        fwrite($f, $fileContent);
    }
}

foreach($removed as $fileRemoved) {
    if(file_exists($fileRemoved)) {
        unlink("../" . $fileRemoved);
    }
}

foreach($modified as $fileModified) {
    $f = fopen("../" . $fileModified, "w");
    $fileContent = file_get_contents($repositoryRawURL . $fileModified);
    if($fileContent != false) {
        fwrite($f, $fileContent);
    }
}
