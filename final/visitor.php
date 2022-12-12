<?php

include "conf.php";

$query = $conn->query('SELECT projectname,description from projects');
$project = $query->fetchAll(PDO::FETCH_ASSOC);

session_start();

//user signin section
if (isset($_POST['signin'])){
    $username = $_POST['username'];
    $password = $_POST['password'];
    $query = $conn->prepare("SELECT * FROM users where username=:username");
    $query->bindParam("username",$username,PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
    if (!$result){
        echo '<script>alert("Username is incorrect")</script>';
    }
    else{
        if (password_verify($password, $result['password'])){
            $_SESSION['userid'] = $result['userID'];
            $_SESSION['username'] = $result['username'];
            header('Location: user.php');
        }
        else{
            echo '<script>alert("Username/Password combo was incorrect")</script>';
        }
    }
}

//admin login section
if (isset($_POST['adminsignin'])){
    $adminusername = $_POST['adminusername'];
    $adminpassword = $_POST['adminpassword'];
    $query = $conn->prepare("SELECT * FROM admins where username=:username");
    $query->bindParam("username",$adminusername,PDO::PARAM_STR);
    $query->execute();
    $adminresult = $query->fetch(PDO::FETCH_ASSOC);
    if (!$adminresult){
        echo '<script>alert("Admin username is incorrect")</script>';
    }
    else{
        if (password_verify($adminpassword, $adminresult['password'])){
            $_SESSION['userid'] = $adminresult['adminID'];
            $_SESSION['adminusername'] = $adminresult['username'];
            header('Location: admin.php');
        }
        else{
            echo '<script>alert("Admin Username/Password combo was incorrect")</script>';
        }
    }
}

//user signup section
if (isset($_POST['signup'])){
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $password = password_hash($password, PASSWORD_DEFAULT);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo '<script>alert("Email formatting is invalid")</script>';
    }
    $query = $conn->prepare("SELECT * FROM users where username=:username");
    $query->bindParam("username",$username,PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
    if ($result){
        echo '<script>alert("Username is already in use")</script>';
    }
    else{
        $sqlinsert = "INSERT INTO users (firstname,lastname,username,email,password) VALUES (?,?,?,?,?)";
        $statement = $conn->prepare($sqlinsert);
        $statement->execute([$firstname,$lastname,$username,$email,$password]);
    }
}

?>

<title>Visitor Page</title>
<center><h1>Visitor Page</h1></center>
<br>

<div class="container">
<h4>Log into your account here</h4>
    <form class="row g-3" method="POST" name="signin">
        <div class="col-md-6">
            <label for="username" class="form-label">Username</label>
            <div class="input-group">
                <span class="input-group-text" id="inputGroupPrepend2">@</span>
                <input type="text" name="username" class="form-control" id="username"  aria-describedby="inputGroupPrepend2" required>
            </div>
        </div>
        <div class="col-md-6">
            <label for="validationDefault03" class="form-label">Password</label>
            <input type="password" name="password" class="form-control" id="password" required>
        </div>
        <div class="col-12">
            <button class="btn btn-primary" type="signin" name="signin" value="signin">Log in</button>
        </div>
    </form>
</div>

<div class="container">
    <h4>Log into your admin account here</h4>
    <form class="row g-3" method="POST" name="adminsignin">
        <div class="col-md-6">
            <label for="username" class="form-label">Admin Username</label>
            <div class="input-group">
                <span class="input-group-text" id="inputGroupPrepend2">@</span>
                <input type="text" name="adminusername" class="form-control" id="username"  aria-describedby="inputGroupPrepend2" required>
            </div>
        </div>
        <div class="col-md-6">
            <label for="validationDefault03" class="form-label">Password</label>
            <input type="password" name="adminpassword" class="form-control" id="password" required>
        </div>
        <div class="col-12">
            <button class="btn btn-primary" type="adminsignin" name="adminsignin" value="adminsignin">Admin Log in</button>
        </div>
    </form>
</div>

<div class="container">
<h4>Create your account here</h4>
    <form class="row g-3" method="POST" name="signup">
        <div class="col-md-6">
            <label for="validationDefault01" class="form-label">First name</label>
            <input type="text" class="form-control" id="firstname" name="firstname" required>
        </div>
        <div class="col-md-6">
            <label for="validationDefault02" class="form-label">Last name</label>
            <input type="text" class="form-control" id="lastname" name="lastname" required>
        </div>
        <div class="col-md-6">
            <label for="validationDefault01" class="form-label">Email Address</label>
            <input type="text" class="form-control" id="email" name="email" required>
        </div>
        <div class="col-md-6">
            <label for="validationDefaultUsername" class="form-label">Username</label>
            <div class="input-group">
                <span class="input-group-text" id="inputGroupPrepend2">@</span>
                <input type="text" class="form-control" id="username" name="username" aria-describedby="inputGroupPrepend2" required>
            </div>
        </div>
        <div class="col-md-6">
            <label for="validationDefault04" class="form-label">Password</label>
            <input type="text" class="form-control" id="password" name="password" required>
        </div>
        <div class="col-12">
            <button class="btn btn-primary" type="signup" name="signup" value="signup">Sign up</button>
        </div>
    </form>
    <br>
    <h2>Projects</h2>
</div>


<?php
foreach($project as $proj){
    echo '<br>';
	foreach($proj as $obj){
		echo '<h3><div class="container">';
			echo $obj;
		echo '</div></h3>';
	}
}
?>
