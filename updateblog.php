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
        if(isset($_POST['postid']) && isset($_POST['title']) && isset($_POST['content'])) {
            
            // Getting postinformation from the API
            $postId = $_POST['postid'];
            $post = $blogger->posts->get($blogId, $postId);
            
            // Creating post
            $mypost = new Google_Service_Blogger_Post();
            $mypost->setTitle($_POST['title']);
            $mypost->setContent($_POST['content']);

            // Choosing action
            if(isset($_POST['revert'])) {
                
                // Check if it works
                echo "Reverted";
                
                // Revert blogpost to draft
                $data = $blogger->posts->update($blogId, $postId, $mypost); 
                $data = $blogger->posts->revert($blogId, $postId); 
                var_dump($data);
            } 
            else if(isset($_POST['deleted'])) {
                
                // Check if it works
                echo "Deleted";
                
                // Delete blogpost
                $data = $blogger->posts->delete($blogId, $postId); 
                var_dump($data);
            } 
            else if(isset($_POST['update'])) {
                
                // Check if it works
                echo "Updated";
                
                // Update blogpost
                $data = $blogger->posts->update($blogId, $postId, $mypost);
                var_dump($data);
            } 
            else if(isset($_POST['draft'])) {
                
                // Check if it works
                echo "Back to draft";
                
                // Update draft blogpost
                $mypost->isDraft(true);
                $data = $blogger->posts->update($blogId, $postId, $mypost);
                var_dump($data);
            }
        }
    } 
    else {
        echo "<p>Er is helaas iets foutgegaan... :-(<br><a href='index.php'>Terug</a>";
    }

} else {
    header('Location: index.php');
}

?>