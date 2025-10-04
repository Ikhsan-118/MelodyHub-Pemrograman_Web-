<?php
session_start();
session_unset();
session_destroy();

// Redirect ke login
header("Location: login.php?msg=logout");
exit();
