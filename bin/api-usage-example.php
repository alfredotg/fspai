<?php

$curl = curl_init();
curl_setopt($curl, CURLOPT_VERBOSE, 1);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

$api = function($path, $data) use($curl) {
    curl_setopt($curl, CURLOPT_URL, 'http://127.0.0.1:8000/api/v0'.$path);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    return json_decode(curl_exec($curl));
};

$folders = $api('/folders', ['limit' => 2]);

$folder = array_shift($folders);

$page = $api('/page/create', ['name' => 'About', 'folder_id' => $folder->id]);
$page = $api('/page/save', ['id' => $page->id, 'content' => '<h1>Hello</h1>']);

$url = $api('/page/publish', ['id' => $page->id]);
printf("%s\n", $url);
