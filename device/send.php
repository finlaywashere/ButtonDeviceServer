<?php
if(!$_SERVER["REQUEST_METHOD"] == "POST"){
        exit();
}
$code = $_POST['code'];
$btn = $_POST['btn'];

if(empty($code) || empty($btn)){
        exit();
}

$conn = mysqli_connect("localhost","prod","prod","production") or die("Failed to connect: ".$conn->connect_error);
// Make the code and button code safe to use with SQL
$code = sanitize($conn,$code);
$btn = sanitize($conn,$btn);

$sql = "SELECT * FROM `registration` WHERE `security_code`=\"".$code."\";";

$result = mysqli_query($conn,$sql);
if(!$result || $result -> num_rows < 1){
        mysqli_close($conn);
        exit();
}

$sql = "INSERT INTO `button_trigger`(`code`, `button`) VALUES (\"".$code."\",\"".$btn."\")";
mysqli_query($conn,$sql);
mysqli_close($conn);
exit();
function sanitize($conn,$str){
    mysqli_escape_string($conn,$str);
    htmlspecialchars($str);
    return $str;
}
?>

