<?php
// Load Google APIs
require_once 'api/vendor/autoload.php';

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set redirectUri to this script
$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];

// Add Google Client
$client = new Google_Client();
$client->setAuthConfig('client_secret.json');
$client->setApplicationName('Bloggr');
$client->setRedirectUri($redirect_uri);
$client->setScopes(array('https://www.googleapis.com/auth/blogger')); 

// Not logged in yet and get a code? Logged in now!
if (!isset($_SESSION['access_token']) && isset($_GET['code'])) {
    $client->authenticate($_GET['code']);
    $access_token = $client->getAccessToken();
    $_SESSION['access_token'] = $access_token;
}

// If the users is logged in succesfully, we can do cool stuff!
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    
    // First set the accesstoken
    $client->setAccessToken($_SESSION['access_token']);
    
    // The cool stuff!
    $blogger = new Google_Service_Blogger($client);
    
    if(isset($_GET['blog']) || isset($_GET['blogid'])) {
        
        // If the user is logged in for the first time, I let him select his blog by URI
        if(isset($_GET['blog'])) {
            $blogUri = $_GET['blog'];
            $blog = $blogger->blogs->getByUrl($blogUri) or die("<p>Er is iets foutgegaan. Een mogelijke oorzaak is dat je het protecol voor de URI bent vergeten.</p>");
            $blogId = $blog->getId();
        } // After the first time, I'm using the blogID
        else if(isset($_GET['blogid'])) {
            $blogId = $_GET['blogid'];
            $blog = $blogger->blogs->get($_GET['blogid']);
        }
        
        // Getting the blogname to display
        $blogName  = $blog->getName();
        
        // Getting the blogposts
        $posts = $blogger->posts->listPosts($blogId);
        
        // The sitenavigation
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
        echo "<h2>Current blog: <span>$blogName</span></h2>"; // Displaying the blogname here
        echo "<h3>Posts <button onclick='window.location = \"newpost.php?blogid=$blogId\"'>New post</button></h3>";
        
        // Listing all of the posts the user has.
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
        
        // Work in progress: listing the drafts
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
    else { // If the user is logged in for the first time, I let him select his blog by URI
        // Getting the user's username
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
    // If the users isn't logged in yet, I'l send him to the Google Accounts page
    $auth_url = $client->createAuthUrl();
    header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
}

?>