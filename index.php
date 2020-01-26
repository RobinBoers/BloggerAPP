<?php
// Load Google APIs
require_once 'api/vendor/autoload.php';

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set redirectUri to this script
$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];

$client = new Google_Client();
$client->setAuthConfig('client_secret_880444620094-cv72kevkvuhekou8pjma02k5sd60t8bu.apps.googleusercontent.com.json');
$client->setApplicationName('Bloggr');
$client->setRedirectUri($redirect_uri);
$client->setScopes(array('https://www.googleapis.com/auth/blogger')); 

if (!isset($_SESSION['access_token']) && isset($_GET['code'])) {
    $client->authenticate($_GET['code']);
    $access_token = $client->getAccessToken();
    $_SESSION['access_token'] = $access_token;
}

if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    $client->setAccessToken($_SESSION['access_token']);
    
    // Do things
    $blogger = new Google_Service_Blogger($client);
    
    if(isset($_GET['blog']) || isset($_GET['blogid'])) {
        if(isset($_GET['blog'])) {
            $blogUri = $_GET['blog'];
            $blog = $blogger->blogs->getByUrl($blogUri) or die("<p>Er is iets foutgegaan. Een mogelijke oorzaak is dat je het protecol voor de URI bent vergeten.</p>");
            $blogId = $blog->getId();
        } else if(isset($_GET['blogid'])) {
            $blogId = $_GET['blogid'];
            $blog = $blogger->blogs->get($_GET['blogid']);
        }
        
        $blogName  = $blog->getName();
//        $posts = $blogger->posts->listPosts($blogId);
          $posts = $blogger->posts->listPosts($blogId);
        echo"<div class='sidenav'>
                <h2><img class='logo' src='bloggerlogo.png'>Bloggr</h2>
                <a href='index.php?blogid=$blogId'>Posts</a>
                <a href='comments.php?blogid=$blogId'>Comments</a>
                <a href='stats.php?blogid=$blogId'>Statistics</a>
                <a href='settings.php?blogid=$blogId'>Settings</a>
                <p style='clear:both;'><a href='revoke.php'>Logout</a></p>
            </div>";
        echo "<title>Bloggr - Posts</title>";
        echo "<link href='css/main.css' rel='stylesheet' type='text/css'>";
        echo "<main>";
        echo "<h2>Current blog: <span>$blogName</span></h2>";
        echo "<h3>Posts <button onclick='window.location = \"newpost.php?blogid=$blogId\"'>New post</button></h3>";
        echo "<ul class='postList'>";
        foreach ($posts as $item) {
            echo("<li>
                    <a href='post.php?postid=$item->id&blogid=$blogId' class='postItem'>
                    <span class='postTitle'>$item->title </span> 
                    <span class='postAuthor'> ~ " . $item->author->displayName . "</span>
                    </a>
                </li>");
        }
        echo "</ul>";
//        echo "<h3>Drafts</h3>";
//        echo "<ul>";
//        foreach ($posts as $item) {
//            echo("<li>
//                    <a href='post.php?postid=$item->id&blogid=$blogId' class='postItem'>
//                    <span class='postTitle'>$item->title </span> 
//                    <span class='postAuthor'> ~ " . $item->author->displayName . "</span>
//                    </a>
//                </li>");
//        }
//        echo "</ul>";
    } 
    else {
        $user = $blogger->users->get('self');
        echo "<title>Welcome to Bloggr</title>";
        echo "<link href='css/main.css' rel='stylesheet' type='text/css'>";
        echo "<main>";
        echo "<h2><img class='logo-left' src='bloggerlogo.png'>Bloggr - Welkom</h2>";
        echo "<form method='get' style='clear:both;'><br><br>";
        echo "<h2>Hoi ".$user->displayName."!</h2>";
        echo "<p>Hallo, het ziet er naar uit dat je nog geen blogs hebt toegevoegd aan Bloggr!<br>";
        echo "Om te beginnen vul je hieronder je blogadress in.<p>";
        echo "<label for='blog'>Blogadress<label>";
        echo "<input name='blog' type='text' placeholder='Voer je blogadress in...'>";
        echo "<input type='submit' style='display:none;'>";
        echo "<p style='font-size:12px;'>* Inclusief http of https</p>";
        echo "</form>";
        echo "</main>";
    }

} else {
    $auth_url = $client->createAuthUrl();
    header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
}

?>