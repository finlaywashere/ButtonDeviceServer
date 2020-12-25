<?php
if(!$_SERVER["REQUEST_METHOD"] == "POST"){
	header("Location: /setup.html");
	exit();
}
$code = $_POST['code'];
$username = $_POST['username'];
$password = $_POST['password'];
if(empty($code) || empty($username) || empty($password)){
	header("Location: /setup.html?error=0");
        exit();
}
$conn = mysqli_connect("localhost","prod","prod","production") or die("Failed to connect: ".$conn->connect_error);
// Make the username and code safe to use with SQL
$code = sanitize($conn,$code);
$username = sanitize($conn,$username);
// Also hash the password
$password = password_hash($password,PASSWORD_DEFAULT);
$sql = "SELECT * FROM `registration` WHERE `security_code`=\"".$code."\";";

$result = mysqli_query($conn,$sql);
if(!$result){
        mysqli_close($conn);
	header("Location: /setup.html?error=1");
	exit();
}
if($result -> num_rows < 1){
	mysqli_close($conn);
        header("Location: /setup.html?error=0");
        exit();
}
$row = $result -> fetch_row();
// Idk why the other way doesn't work but idc
if($row[2] != "1970-01-01"){
	mysqli_close($conn);
        header("Location: /setup.html?error=4");
        exit();
}

$sql = "INSERT INTO `users` (`security_code`, `username`, `password`) VALUES (\"".$code."\",\"".$username."\",\"".$password."\");";
$result = mysqli_query($conn,$sql);
if(!$result){
	mysqli_close($conn);
        header("Location: /setup.html?error=2");
        exit();
}

$sql = "UPDATE `registration` SET `registration_date` = \"".date("Y-m-d")."\" WHERE `security_code` = \"".$code."\";";
$result = mysqli_query($conn,$sql);
if(!$result){
        mysqli_close($conn);
        header("Location: /setup.html?error=3");
        exit();
}
mysqli_close($conn);
header("Location: /setup.html?error=-1");
exit();

function sanitize($conn,$str){
    mysqli_escape_string($conn,$str);
    htmlspecialchars($str);
    return $str;
}
?>
