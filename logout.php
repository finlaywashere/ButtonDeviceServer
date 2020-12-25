<?php
session_start();
unset($_SESSION['username']);
header("Location: /setup.html");
exit();
?>
