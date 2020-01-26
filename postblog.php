<?php
// Load Google APIs
require_once 'api/vendor/autoload.php';

session_start();

// Set redirectUri to this script
$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];

$client = new Google_Client();
$client->setAuthConfig('client_secret_880444620094-cv72kevkvuhekou8pjma02k5sd60t8bu.apps.googleusercontent.com.json');
$client->setApplicationName('Bloggr');
$client->setRedirectUri($redirect_uri);
$client->setScopes(array('https://www.googleapis.com/auth/blogger')); 

if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    $client->setAccessToken($_SESSION['access_token']);
    
    // Do things
    $blogger = new Google_Service_Blogger($client);
    
    if(isset($_POST['blogid'])) {
        $blogId = $_POST['blogid'];
        $blog = $blogger->blogs->get($blogId);
        $blogName  = $blog->getName();
        echo"<div class='sidenav'>
                <h2><img class='logo' src='bloggerlogo.png'>Bloggr</h2>
                <a href='index.php?blogid=$blogId'>Posts</a>
                <a href='comments.php?blogid=$blogId'>Comments</a>
                <a href='stats.php?blogid=$blogId'>Statistics</a>
                <a href='settings.php?blogid=$blogId'>Settings</a>
                <p style='clear:both;'><a href='revoke.php'>Logout</a></p>
            </div>";
        echo "<title>Bloggr - Edit</title>";
        echo "<link href='css/main.css' rel='stylesheet' type='text/css'>";
        echo "<main>";
        echo "<h2>Current blog: <span>$blogName</span></h2>";
        if(isset($_POST['title']) && isset($_POST['content'])) {
            $mypost = new Google_Service_Blogger_Post();
            $mypost->setTitle($_POST['title']);
            $mypost->setContent($_POST['content']);
            
            if(!isset($_GET['draft'])) {
                $data = $blogger->posts->insert($blogId, $mypost);
                var_dump($data);
            } else {
                $mypost->isDraft(true);
                $data = $blogger->posts->insert($blogId, $mypost);
                var_dump($data);
            }
        }
    } 
    else {
        echo "<p>Er is helaas iets foutgegaan... :-(<br><a href='index.php'>Terug</a>";
    }
    
    
    //creates a post object
//    $mypost = new Google_Service_Blogger_Post();
//    $mypost->setTitle('this is a test 1 title');
//    $mypost->setContent('this is a test 1 content');
//
//    $data = $blogger->posts->insert('7969045034789594187', $mypost); //post id needs here - put your blogger blog id
//     var_dump($data);

} else {
    header('Location: index.php');
}

?>