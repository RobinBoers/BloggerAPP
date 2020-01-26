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
        
        
        if(isset($_GET['postid'])) {
            
            // Getting postinformation from API
            $postId = $_GET['postid'];
            $post = $blogger->posts->get($blogId, $postId);
            
            // Showing editor
            echo "<h1 class='robin-title' contenteditable='true'>".$post->title."</h1>";
            echo "<p>By ".$post->author->displayName." <button onclick='submit()'>Update</button> <button onclick='revert()'>Revert to draft</button> <button onclick='deleted()'>Delete</button></p>";
            ?>
                <!-- Include stylesheet for Quill  -->
                <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

                <!-- Create the editor container for Quill  -->
                <div id="editor">
                  <?php echo $post->content ?>
                </div>

                <!-- Include the Quill library -->
                <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

                <!-- Forms to send data to the script that updates, or posts blogposts -->
                <form style="display:none !important;" class="updateform" action="updateblog.php" method="post">
                    <input id="updatetitle" type="text" name="title">
                    <input id="updatecontent" type="text" name="content">
                    <input id="updateblogid" type="text" name="blogid">
                    <input id="updatepostid" type="text" name="postid">
                    <input id="update" type="text" name="update">
                    <input type="submit" name="update2">
                </form>

                <form style="display:none !important;" class="revertform" action="updateblog.php" method="post">
                    <input id="reverttitle" type="text" name="title">
                    <input id="revertcontent" type="text" name="content">
                    <input id="revertblogid" type="text" name="blogid">
                    <input id="revertpostid" type="text" name="postid">
                    <input id="revert" type="text" name="revert">
                    <input type="submit" name="revert2">
                </form>

                <form style="display:none !important;" class="delform" action="updateblog.php" method="post">
                    <input id="deltitle" type="text" name="title">
                    <input id="delcontent" type="text" name="content">
                    <input id="delblogid" type="text" name="blogid">
                    <input id="delpostid" type="text" name="postid">
                    <input id="delete" type="hidden" value="deleted" name="deleted">
                    <input type="submit" name="deleted2">
                </form>

                <!-- Initialize Quill editor -->
                <script>
                    var quill = new Quill('#editor', {
                        theme: 'snow'
                    });
                    function submit() {
                        document.querySelector("#updatecontent").value = document.querySelector(".ql-editor").innerHTML;
                        document.querySelector("#updatetitle").value = document.querySelector(".robin-title").innerHTML;
                        document.querySelector("#updateblogid").value = "<?php echo $blogId ?>";
                        document.querySelector("#updatepostid").value = "<?php echo $postId ?>";
                        console.log("konijn");
                        
                        document.querySelector(".updateform").submit();
                    }
                    function revert() {
                        document.querySelector("#revertcontent").value = document.querySelector(".ql-editor").innerHTML;
                        document.querySelector("#reverttitle").value = document.querySelector(".robin-title").innerHTML;
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