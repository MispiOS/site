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

$modifySelf = false;

foreach($added as $fileAdded) {
    if(str_starts_with($fileAdd, ".")) continue;
    if($fileAdded != "webhook/githubHook.php") {
        $f = fopen("../" . $fileAdd, "w");
        $fileContent = file_get_contents($repositoryRawURL . $fileAdd);
        if($fileContent != false) {
            fwrite($f, $fileContent);
        }
    } else {
        $modifySelf = true;
    }
}

foreach($removed as $fileRemoved) {
    if(file_exists($fileRemoved)) {
        unlink("../" . $fileRemoved);
    }
}

foreach($modified as $fileModified) {
    if(str_starts_with($fileAdd, ".")) continue;
    if($fileModified != "webhook/githubHook.php") {
        $f = fopen("../" . $fileModified, "w");
        $fileContent = file_get_contents($repositoryRawURL . $fileModified);
        if($fileContent != false) {
            fwrite($f, $fileContent);
        }
    }else { $modifySelf = true; }
}

if($modifySelf) {
    $url = 'https://mom.cmi-info.fr/webhook/updateGit.php';
    $data = [];

    $options = [
        'http' => [
            'method'  => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'content' => http_build_query($data),
        ],
    ];
    $context  = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
}