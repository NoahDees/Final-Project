<!DOCTYPE html>
<html lang="en">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.min.js" integrity="sha384-Atwg2Pkwv9vp0ygtn1JAojH0nYbwNJLPhwyoVbhoPwBhjQPR5VtM2+xf0Uwh9KtT" crossorigin="anonymous"></script>
<?php
$servername = "localhost";
$username = "setup";
$password = "newpassword";
$dbname = "crowdfoo";

try{
	$conn = new PDO("mysql:host=$servername;dbname=$dbname",$username,$password);
}
catch(PDOException $e){
	$error_message = $e->getMessage();
	echo $error_message;
}

$sqlinsert = "INSERT INTO admins (firstname,lastname,username,email,password,rewardID) VALUES (?,?,?,?,?,?)";
$statement = $conn->prepare($sqlinsert);
//insert your details in the 'example' slot
$password = password_hash('example', PASSWORD_DEFAULT);
//insert your first, last, and usernames and your email address below and uncomment this line
//run conf.php once in your browser, and then comment the execution line below once again

//$statement->execute(['noah','dees','deesn1','deesn1@nku.edu',$password,'1']);
?>