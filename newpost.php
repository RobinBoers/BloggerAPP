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
    
    if(isset($_GET['blogid'])) {
        $blogId = $_GET['blogid'];
        $blog = $blogger->blogs->get($blogId);
        $blogName  = $blog->getName();
        $user = $blogger->users->get('self');
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
        echo "<h1 class='robin-title' contenteditable='true'>Add title</h1>";
        echo "<p>By ".$user->displayName." <button onclick='submit()'>Publish</button> <button onclick='draft()'>Save as draft</button></p>";
        ?>
            <!-- Include stylesheet -->
            <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

            <!-- Create the editor container -->
            <div id="editor">
            
            </div>

            <!-- Include the Quill library -->
            <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

            <form style="display:none !important;" class="robinform" action="postblog.php" method="post">
                <input id="robintitle" type="text" name="title">
                <input id="robincontent" type="text" name="content">
                <input id="robinblogid" type="text" name="blogid">
                <input type="submit" name="cool">
            </form>

            <form style="display:none !important;" class="draftform" action="postblog.php" method="post">
                <input id="draftblogid" type="text" name="blogid">
                <input id="drafttitle" type="text" name="title">
                <input id="draftcontent" type="text" name="content">
                <input id="draft" type="text" value="true" name="draft">
                <input type="submit" name="cool">
            </form>

            <!-- Initialize Quill editor -->
            <script>
                var quill = new Quill('#editor', {
                    theme: 'snow'
                });
                function submit() {
                    document.querySelector("#robincontent").value = document.querySelector(".ql-editor").innerHTML;
                    document.querySelector("#robintitle").value = document.querySelector(".robin-title").innerHTML;
                    document.querySelector("#robinblogid").value = "<?php echo $blogId ?>";
                    console.log("konijn");

                    document.querySelector(".robinform").submit();
                }
                function draft() {
                    document.querySelector("#draftblogid").value = "<?php echo $blogId ?>";
                    document.querySelector("#draftcontent").value = document.querySelector(".ql-editor").innerHTML;
                    document.querySelector("#drafttitle").value = document.querySelector(".robin-title").innerHTML;
                    console.log("konijn2");

                    document.querySelector(".draftform").submit();
                }
            </script>
        <?php
    }
} 
else {
    echo "<p>Er is helaas iets foutgegaan... :-(";
}


//creates a post object
//    $mypost = new Google_Service_Blogger_Post();
//    $mypost->setTitle('this is a test 1 title');
//    $mypost->setContent('this is a test 1 content');
//
//    $data = $blogger->posts->insert('7969045034789594187', $mypost); //post id needs here - put your blogger blog id
//     var_dump($data);


?>