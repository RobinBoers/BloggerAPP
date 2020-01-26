<?php

// Revoke accesstoken
session_start();
session_destroy();

// Redirect user to homepage
header("Location: index.php");
?>