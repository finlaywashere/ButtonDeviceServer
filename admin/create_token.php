<?php

$conn = mysqli_connect("localhost","prod","prod","production") or die("Failed to connect: ".$conn->connect_error);
$username = sanitize($conn,$_REQUEST['username']);
$sql = "SELECT * FROM `admins` WHERE `username`=\"".$username."\";";
$result = mysqli_query($conn,$sql);
if(mysqli_num_rows($result) == 0){
	mysqli_close($conn);
	exit("Error 1");
}
$row = mysqli_fetch_assoc($result);
$hash = $row["password"];
if(!password_verify($_REQUEST['password'],$hash)){
	mysqli_close($conn);
	exit("Error 2");
}
$code = random_str();
$sql = "INSERT INTO `registration` (`security_code`) VALUES (\"".$code."\");";
if(!mysqli_query($conn,$sql)){
	echo $conn -> error;
	mysqli_close($conn);
	exit("Error 3");
}
mysqli_close($conn);
echo "Code is: ".$code;
die();

function sanitize($conn,$str){
	mysqli_escape_string($conn,$str);
	htmlspecialchars($str);
	return $str;
}
function random_str(
	int $length = 16,
	string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyz'
	): string {
		$pieces = [];
		$max = mb_strlen($keyspace, '8bit') - 1;
		for ($i = 0; $i < $length; ++$i) {
			$pieces []= $keyspace[random_int(0, $max)];
		}
		return implode('', $pieces);
}

?>
