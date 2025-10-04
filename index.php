<?php
// Redirect to login page immediately
// Note: ensure no output is sent before these headers.
header("Location: login.php");
exit();
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="refresh" content="0;url=login.php">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Redirecting…</title>
</head>
<body>
	<p>If you are not redirected automatically, <a href="login.php">click here to go to the login page</a>.</p>
</body>
</html>

