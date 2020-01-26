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
    
    if(isset($_GET['blogid'])) {
        
        // Getting bloginformation from the API
        $blogId = $_GET['blogid'];
        $blog = $blogger->blogs->get($blogId);
        $blogName  = $blog->getName();
        $user = $blogger->users->get('self');
        
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
        echo "<h1 class='robin-title' contenteditable='true'>Add title</h1>";
        echo "<p>By ".$user->displayName." <button onclick='submit()'>Publish</button> <button onclick='draft()'>Save as draft</button></p>";
        ?>
            <!-- Include stylesheet for Quill -->
            <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

            <!-- Create the editor container for Quill -->
            <div id="editor">
            
            </div>

            <!-- Include the Quill library -->
            <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>


            <!-- Forms to send data to the script that updates, or posts blogposts -->
            <form style="display:none !important;" class="postform" action="postblog.php" method="post">
                <input id="posttitle" type="text" name="title">
                <input id="postcontent" type="text" name="content">
                <input id="postblogid" type="text" name="blogid">
                <input type="submit" name="post">
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
                    document.querySelector("#postcontent").value = document.querySelector(".ql-editor").innerHTML;
                    document.querySelector("#posttitle").value = document.querySelector(".robin-title").innerHTML;
                    document.querySelector("#postblogid").value = "<?php echo $blogId ?>";
                    console.log("konijn");

                    document.querySelector(".postform").submit();
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
?>