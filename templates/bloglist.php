<?php
// This script shows a list of blogpost that an user has posted
// Made by Robin Boers

// load Google Api
require_once '../api/vendor/autoload.php';

session_start();

$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];

// Authenticate user with OAuth 
$client = new Google_Client();
$client->setAuthConfig('../client_secret.json');
$client->setApplicationName('Bloggr');
$client->setRedirectUri($redirect_uri);
$client->setScopes(array('https://www.googleapis.com/auth/blogger')); 

if (!isset($_SESSION['access_token']) && isset($_GET['code'])) {
    $client->authenticate($_GET['code']);
    $access_token = $client->getAccessToken();
    $_SESSION['access_token'] = $access_token;
}

if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    
    // First set the accesstoken
    $client->setAccessToken($_SESSION['access_token']);

    $blogger = new Google_Service_Blogger($client);

    // Get blog information
    $blog = $blogger->blogs->getByUrl('http://robinbdev.blogspot.com'); // Replace this URI with your blog's URI
    $blogId = $blog->getId();
    $blogName  = $blog->getName();

    $posts = $blogger->posts->listPosts($blogId);
    echo "<ul>";
        foreach ($posts as $item) {
            echo("<li>
                    <span>" . $item->title . "</span> 
                    <span> ~ " . $item->author->displayName . "</span>
                </li>");
        }
        echo "</ul>";
}
?>