<?php
//if(!$_SERVER["REQUEST_METHOD"] == "POST"){
//        exit("0");
//}
$code = $_REQUEST['code'];

if(empty($code)){
        exit("0");
}

$conn = mysqli_connect("localhost","prod","prod","production") or die("Failed to connect: ".$conn->connect_error);
// Make the code and button code safe to use with SQL
$code = sanitize($conn,$code);

$sql = "SELECT * FROM `registration` WHERE `security_code`=\"".$code."\";";

$result = mysqli_query($conn,$sql);
if(!$result || $result -> num_rows < 1){
        mysqli_close($conn);
        exit("0");
}

$sql = "SELECT * FROM `button_trigger` WHERE `code` = \"".$code."\" LIMIT 1;";
$result = mysqli_query($conn,$sql);
if($result -> num_rows < 1){
	mysqli_close($conn);
	exit("0");
}
$row = $result -> fetch_row();
echo $row[1];
$sql = "DELETE FROM `button_trigger` WHERE `code`=\"".$code."\" LIMIT 1;";
mysqli_query($conn,$sql);

mysqli_close($conn);
exit();
function sanitize($conn,$str){
    mysqli_escape_string($conn,$str);
    htmlspecialchars($str);
    return $str;
}
?>

