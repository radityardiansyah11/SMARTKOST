<?php
// logout.php
session_start();
session_destroy();
header("Location: index.php"); // Redirect ke index.php setelah logout
exit();
?>