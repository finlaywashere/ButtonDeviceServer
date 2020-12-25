<?php
session_start();
if(isset($_SESSION['username'])){
	header("Location: /configure.html");
	exit();
}
if(!isset($_REQUEST['username']) || !isset($_REQUEST['password'])){
	header("Location: /login.html");
	exit();
}

$conn = mysqli_connect("localhost","prod","prod","production") or die("Failed to connect: ".$conn->connect_error);
$username = sanitize($conn,$_REQUEST['username']);
$password = $_REQUEST['password'];

$sql = "SELECT * FROM `users` WHERE `username`=\"".$username."\";";
$result = mysqli_query($conn,$sql);
if(!$result){
	mysqli_close($conn);
	header("Location: /login.html?error=1");
	exit();
}
echo $_REQUEST['username'];
if($result -> num_rows < 1){
	mysqli_close($conn);
	header("Location: /login.html?error=2");
	exit();
}
$row = $result -> fetch_assoc();
$hash = $row['password'];
if(!password_verify($password,$hash)){
	mysqli_close($conn);
	header("Location: /login.html?error=2");
	exit();
}
# Password is correct
$_SESSION['username'] = $username;

mysqli_close($conn);
header("Location: /configure.html");
exit();
function sanitize($conn,$str){
    mysqli_escape_string($conn,$str);
    htmlspecialchars($str);
    return $str;
}
?>
