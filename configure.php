<?php
session_start();
if(!isset($_SESSION['username'])){
	header("Location: /login.html");
	exit();
}
$conn = mysqli_connect("localhost","prod","prod","production") or die("Failed to connect: ".$conn->connect_error);
$username = sanitize($conn,$_SESSION['username']);

$btn1 = sanitize($conn,$_REQUEST['btn1']);
$btn1e = sanitize($conn,$_REQUEST['btn1e']);
$btn2 = sanitize($conn,$_REQUEST['btn2']);
$btn2e = sanitize($conn,$_REQUEST['btn2e']);
$btn3 = sanitize($conn,$_REQUEST['btn3']);
$btn3e = sanitize($conn,$_REQUEST['btn3e']);

if(!isset($btn1) && !isset($btn1e) && !isset($btn2) && !isset($btn2e) && !isset($btn3) && !isset($btn3e)){
	mysqli_close($conn);
	header("Location: /configure.html?error=-1");
	exit();
}

$sql = "SELECT * FROM `users` WHERE `username`=\"".$username."\";";
$result = mysqli_query($conn,$sql);
if(!$result){
	mysqli_close($conn);
	header("Location: /configure.html?error=1");
        exit();
}
if($result -> num_rows < 1){
	mysqli_close($conn);
        header("Location: /configure.html?error=1");
        exit();
}
$row = $result -> fetch_assoc();
$code = $row['security_code'];

$sql = "SELECT * FROM `hardware` WHERE `security_code`=\"".$code."\";";
$result = mysqli_query($conn,$sql);
if(!$result){
        mysqli_close($conn);
        header("Location: /configure.html?error=2");
        exit();
}
if($result -> num_rows < 1){
        mysqli_close($conn);
        header("Location: /configure.html?error=2");
        exit();
}

$row = $result -> fetch_assoc();

$oldconfig = $row['configuration'];

# Now we create the new configuration!

$split = preg_split("~,~",$oldconfig);

# This is definately not the best way to do it but I don't even care, I'm tired

if(isset($btn1) || isset($btn1e)){
	$btn1c = $split[0];
	$btn1s = preg_split("~@~",$btn1c, 2);
	if($btn1 !== ''){
		$btn1s[0] = $btn1;
	}
	if($btn1e !== ''){
		$btn1s[1] = $btn1e;
	}
	$split[0] = $btn1s[0]."@".$btn1s[1];
}
if(isset($btn2) || isset($btn2e)){
	$btn2c = $split[1];
	$btn2s = preg_split("~@~",$btn2c, 2);
        if($btn2 !== ''){
                $btn2s[0] = $btn2;
        }
        if($btn2e !== ''){
                $btn2s[1] = $btn2e;
        }
        $split[1] = $btn2s[0]."@".$btn2s[1];

}
if(isset($btn3) || isset($btn3e)){
	$btn3c = $split[2];
	$btn3s = preg_split("~@~",$btn3c, 2);
	if($btn3 !== ''){
                $btn3s[0] = $btn3;
        }
        if($btn3e !== ''){
                $btn3s[1] = $btn3e;
        }
        $split[2] = $btn3s[0]."@".$btn3s[1];
}

# Now create the new config from the array
$config = $split[0].",".$split[1].",".$split[2];

$sql = "UPDATE `hardware` SET `configuration`=\"".$config."\" WHERE `security_code`=\"".$code."\";";
mysqli_query($conn,$sql);

mysqli_close($conn);

header("Location: /configure.html?error=-1");

exit();
function sanitize($conn,$str){
    mysqli_escape_string($conn,$str);
    htmlspecialchars($str);
    return $str;
}
?>
