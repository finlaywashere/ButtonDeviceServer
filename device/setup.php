<?php
$code = $_REQUEST['code'];
$lan = $_REQUEST['lan'];
$version = $_REQUEST['version'];
if(empty($code) || empty($lan) || empty($version)){
	error_log("Invalid request");
	exit();
}

$conn = mysqli_connect("localhost","prod","prod","production") or die("Failed to connect: ".$conn->connect_error);
// Make the code, lan ip, and version safe to use with SQL
$code = sanitize($conn,$code);
$lan = sanitize($conn,$lan);
$version = sanitize($conn,$version);
$wan = $_SERVER['REMOTE_ADDR'];
$date = date("Y-m-d");

$sql = "SELECT * FROM `registration` WHERE `security_code`=\"".$code."\";";

$result = mysqli_query($conn,$sql);
if(!$result || $result -> num_rows < 1){
        mysqli_close($conn);
	error_log("No matching code!");
        exit();
}

$defaultconf = "Not configured@finman292004@protonmail.com,Not configured@finman292004@protonmail.com,Not configured@finman292004@protonmail.com";

$sql = "SELECT * FROM `hardware` WHERE `security_code`=\"".$code."\";";
$result = mysqli_query($conn,$sql);
$sql = "INSERT INTO `hardware` (`security_code`, `current_firmware_version`, `last_connection_date`, `wan_ip`, `lan_ip`, `configuration`) VALUES (\"".$code."\",\"".$version."\",\"".$date."\",\"".$wan."\",\"".$lan."\", \"".$defaultconf."\");";
if($result && $result -> num_rows >= 1){
	$row = $result -> fetch_assoc();
	if(isset($row['current_firmware_version'])){
		$sql = "UPDATE `hardware` SET `current_firmware_version`=\"".$version."\", `last_connection_date`=\"".$date."\",`wan_ip`=\"".$wan."\",`lan_ip`=\"".$lan."\" WHERE `security_code`=\"".$code."\"";
	}
}
mysqli_query($conn,$sql);
mysqli_close($conn);
exit();
function sanitize($conn,$str){
    mysqli_escape_string($conn,$str);
    htmlspecialchars($str);
    return $str;
}

?>
