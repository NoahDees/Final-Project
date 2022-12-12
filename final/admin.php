<?php

include "conf.php";

$query = $conn->query('SELECT * from projects');
$project = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $conn->query('SELECT * from rewards');
$rewards = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $conn->query('SELECT * from users');
$users = $query->fetchAll(PDO::FETCH_ASSOC);

session_start();
if(!$_SESSION['userid']){
    header('Location: visitor.php');
}

//admin creation section
if (isset($_POST['admincreate'])){
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $password = password_hash($password, PASSWORD_DEFAULT);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo '<script>alert("Email formatting is invalid")</script>';
    }
    $query = $conn->prepare("SELECT * FROM admins where username=:username");
    $query->bindParam("username",$username,PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
    if ($result){
        echo '<script>alert("Username is already in use")</script>';
    }
    else{
        $sqlinsert = "INSERT INTO admins (firstname,lastname,username,email,password) VALUES (?,?,?,?,?)";
        $statement = $conn->prepare($sqlinsert);
        $statement->execute([$firstname,$lastname,$username,$email,$password]);
    }
}

//admin project update section
if (isset($_POST['updateproj'])){
    $updateID = $_POST['uID'];
    $name = $_POST['uname'];
    $desc = $_POST['udescription'];

    $userid = $_SESSION['userid'];
    $query = $conn->prepare("SELECT * FROM projects where userID=:userID");
    $query->bindParam("userID",$userid,PDO::PARAM_STR);
    $query->execute();
    $changeresult = $query->fetchAll(PDO::FETCH_ASSOC);

    $idarray = [];

    foreach($changeresult as $projectset){       
        $projid = $projectset["projectID"];
        array_push($idarray,$projid);
    }
    if (!in_array($updateID, $idarray)){
        echo '<script>alert("That project ID does not exist")</script>';
    }
    else{
        $sqlinsert = "UPDATE projects SET projectname=?,description=? WHERE projectID = ?";
        $statement = $conn->prepare($sqlinsert);
        $statement->execute([$name,$desc,$updateID]);
    }
}

//admin project delete setion
if (isset($_POST['delproj'])){
    $delID = $_POST['delID'];

    $userid = $_SESSION['userid'];
    $query = $conn->prepare("SELECT * FROM projects where userID=:userID");
    $query->bindParam("userID",$userid,PDO::PARAM_STR);
    $query->execute();
    $delresult = $query->fetchAll(PDO::FETCH_ASSOC);

    $idarray = [];

    foreach($delresult as $projectset){       
        $projid = $projectset["projectID"];
        array_push($idarray,$projid);
    }
    if (!in_array($delID, $idarray)){
        echo '<script>alert("That project ID does not exist")</script>';
    }
    else{
        $sqlinsert = "DELETE FROM projects WHERE projectID = ?";
        $statement = $conn->prepare($sqlinsert);
        $statement->execute([$delID]);
    }
}

//reward creation section
if (isset($_POST['newreward'])){
    $rewardprojectid = $_POST['rewardprojID'];
    $type = $_POST['rewardtype'];
    $price = $_POST['newrewardprice'];
    $dd = $_POST['dd'];

    $sqlinsert = "INSERT INTO rewards (projectID,rewardtype,rewardprice,duedate) VALUES (?,?,?,?)";
    $statement = $conn->prepare($sqlinsert);
    $statement->execute([$rewardprojectid,$type,$price,$dd]);
}

//reward update section
if (isset($_POST['updatereward'])){
    $updateID = $_POST['rewardID'];
    $type = $_POST['rewardtype'];
    $price = $_POST['newrewardprice'];
    $dd = $_POST['dd'];

    $sqlinsert = "UPDATE rewards SET rewardtype=?,rewardprice=?,duedate=? WHERE rewardID=?";
    $statement = $conn->prepare($sqlinsert);
    $statement->execute([$type,$price,$dd,$updateID]);
}
?>

<title>Admin Page</title>
<center><h1>Admin Page for <?php echo $_SESSION['adminusername'];?></h1></center>
<br>

<div class="container">
<h4>Create a new admin account here</h4>
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
</div>

<div class="container">
<?php

//Project printing area
echo "<br>";

$query = $conn->query('SELECT supportID,supportamount,projectID,userID from support');
$suppresult = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $conn->query('SELECT * from rewards');
$rewardresult = $query->fetchAll(PDO::FETCH_ASSOC);


foreach($project as $proj){
    echo "<h4>";
    echo 'projectID = ',$proj["projectID"],' | rewardID = ',$proj["rewardID"],' | userID = ',$proj["userID"],' | Project Name = ',$proj["projectname"],' | Project Description = ',$proj["description"];
    echo '<br>';
    foreach($suppresult as $supp){
        if($supp["projectID"] == $proj["projectID"]){
            echo 'Support for this project = $',$supp["supportamount"],' from User: ',$supp["userID"],' | ';
        }
    }
    echo '<br>';
    foreach($rewardresult as $rew){
        if($rew["projectID"] == $proj["projectID"]){
            echo 'Reward for this project = ',$rew["rewardtype"],' | Reward price for this project = $',$rew["rewardprice"],' | Due date for this reward = ',$rew["duedate"],' | ';
        }
    }
    echo '<br><br>';
}

echo "</h4>";
?>
</div>

<div class="container">
    <h3>Update all projects here</h3>
    <form class="row g-2" method="POST" name="updateproj">
        <div class="col-md-4">
            <label for="validationDefault01" class="form-label">The ID of the project you want to update</label>
            <input type="text" class="form-control" id="uID" name="uID" required>
        </div>
        <div class="col-md-4">
            <label for="validationDefault01" class="form-label">Update Project Name</label>
            <input type="text" class="form-control" id="uname" name="uname" required>
        </div>
        <div class="col-md-8">
            <label for="validationDefault02" class="form-label">Update Project Description</label>
            <input type="text" class="form-control" id="udescription" name="udescription" required>
        </div>
        <div class="col-1">
            <button class="btn btn-primary" type="updateproj" name="updateproj" value="updateproj">Update your project</button>
        </div>
    </form>
</div>

<br>

<div class="container">
    <h3>DELETE all projects here!</h3>
    <form class="row g-2" method="POST" name="delproj">
        <div class="col-md-4">
            <label for="validationDefault01" class="form-label">The ID of the project you want to DELETE</label>
            <input type="text" class="form-control" id="delID" name="delID" required>
        </div>
        <div class="col-1">
            <button class="btn btn-danger" type="delproj" name="delproj" value="delproj">DELETE your project</button>
        </div>
    </form>
</div>

<br>

<div class="container">
    <h3>Create new rewards here</h3>
    <form class="row g-2" method="POST" name="newreward">
        <div class="col-md-4">
            <label for="validationDefault01" class="form-label">The project ID of the reward you want to create</label>
            <input type="text" class="form-control" id="rewardprojID" name="rewardprojID" required>
        </div>
        <div class="col-md-4">
            <label for="validationDefault01" class="form-label">New Reward Type</label>
            <input type="text" class="form-control" id="rewardtype" name="rewardtype" required>
        </div>
        <div class="col-md-8">
            <label for="validationDefault02" class="form-label">New Reward Price</label>
            <input type="text" class="form-control" id="newrewardprice" name="newrewardprice" required>
        </div>
        <div class="col-md-8">
            <label for="validationDefault02" class="form-label">New Reward Due Date (in the format yyyy-mm-dd)</label>
            <input type="text" class="form-control" id="dd" name="dd" required>
        </div>
        <div class="col-1">
            <button class="btn btn-primary" type="newreward" name="newreward" value="newreward">Create a reward system</button>
        </div>
    </form>
</div>

<br>

<div class="container">
    <h3>Update all rewards here</h3>
    <form class="row g-2" method="POST" name="updatereward">
        <div class="col-md-4">
            <label for="validationDefault01" class="form-label">The reward ID of the reward you want to update</label>
            <input type="text" class="form-control" id="rewardID" name="rewardID" required>
        </div>
        <div class="col-md-4">
            <label for="validationDefault01" class="form-label">New Reward Type</label>
            <input type="text" class="form-control" id="rewardtype" name="rewardtype" required>
        </div>
        <div class="col-md-8">
            <label for="validationDefault02" class="form-label">New Reward Price</label>
            <input type="text" class="form-control" id="newrewardprice" name="newrewardprice" required>
        </div>
        <div class="col-md-8">
            <label for="validationDefault02" class="form-label">New Reward Due Date (in the format yyyy-mm-dd)</label>
            <input type="text" class="form-control" id="dd" name="dd" required>
        </div>
        <div class="col-1">
            <button class="btn btn-primary" type="updatereward" name="updatereward" value="updatereward">Update a reward system</button>
        </div>
    </form>
</div>