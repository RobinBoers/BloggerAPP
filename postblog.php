<?php
// Load Google APIs
require_once 'api/vendor/autoload.php';

session_start();

// Set redirectUri to this script
$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];

// Add Google Client
$client = new Google_Client();
$client->setAuthConfig('client_secret.json');
$client->setApplicationName('Bloggr');
$client->setRedirectUri($redirect_uri);
$client->setScopes(array('https://www.googleapis.com/auth/blogger')); 

// If the users is logged in succesfully, we can do cool stuff!
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    
    // First set the accesstoken
    $client->setAccessToken($_SESSION['access_token']);
    
    // The cool stuff!
    $blogger = new Google_Service_Blogger($client);
    
    if(isset($_POST['blogid'])) {
        
        // Getting bloginformation from the API
        $blogId = $_POST['blogid'];
        $blog = $blogger->blogs->get($blogId);
        $blogName  = $blog->getName();
        
        // Sitenavigation
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
            
            // Creating post
            $mypost = new Google_Service_Blogger_Post();
            $mypost->setTitle($_POST['title']);
            $mypost->setContent($_POST['content']);
            
            // Choosing action
            if(!isset($_GET['draft'])) {
                
                // Post blogpost
                $data = $blogger->posts->insert($blogId, $mypost);
                var_dump($data);
            } else {
                
                // Work in progress: post as draft
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