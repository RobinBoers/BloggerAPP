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
        if(isset($_GET['postid'])) {
            $postId = $_GET['postid'];
            $post = $blogger->posts->get($blogId, $postId);
            echo "<h1 class='robin-title' contenteditable='true'>".$post->title."</h1>";
            echo "<p>By ".$post->author->displayName." <button onclick='submit()'>Update</button> <button onclick='revert()'>Revert to draft</button> <button onclick='deleted()'>Delete</button></p>";
            ?>
                <!-- Include stylesheet -->
                <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

                <!-- Create the editor container -->
                <div id="editor">
                  <?php echo $post->content ?>
                </div>

                <!-- Include the Quill library -->
                <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

                <form style="display:none !important;" class="robinform" action="updateblog.php" method="post">
                    <input id="robintitle" type="text" name="title">
                    <input id="robincontent" type="text" name="content">
                    <input id="robinblogid" type="text" name="blogid">
                    <input id="robinpostid" type="text" name="postid">
                    <input type="submit" name="cool">
                </form>

                <form style="display:none !important;" class="revertform" action="revertblog.php" method="post">
                    <input id="revertblogid" type="text" name="blogid">
                    <input id="revertpostid" type="text" name="postid">
                    <input type="submit" name="cool">
                </form>

                <form style="display:none !important;" class="delform" action="deleteblog.php" method="post">
                    <input id="delblogid" type="text" name="blogid">
                    <input id="delpostid" type="text" name="postid">
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
                        document.querySelector("#robinpostid").value = "<?php echo $postId ?>";
                        console.log("konijn");
                        
                        document.querySelector(".robinform").submit();
                    }
                    function revert() {
                        document.querySelector("#revertblogid").value = "<?php echo $blogId ?>";
                        document.querySelector("#revertpostid").value = "<?php echo $postId ?>";
                        console.log("konijn2");
                        
                        document.querySelector(".revertform").submit();
                    }
                    function deleted() {
                        document.querySelector("#delblogid").value = "<?php echo $blogId ?>";
                        document.querySelector("#delpostid").value = "<?php echo $postId ?>";
                        console.log("konijn3");
                        
                        document.querySelector(".delform").submit();
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

} else {
    header('Location: index.php');
}

?>