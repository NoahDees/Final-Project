<?php

include "conf.php";

$query = $conn->query('SELECT * from projects');
$project = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $conn->query('SELECT * from rewards');
$rewardresult = $query->fetchAll(PDO::FETCH_ASSOC);

session_start();
if(!$_SESSION['userid']){
    header('Location: visitor.php');
}

$userid = $_SESSION['userid'];
$query = $conn->prepare("SELECT * FROM projects where userID=:userID");
$query->bindParam("userID",$userid,PDO::PARAM_STR);
$query->execute();
$changeresult = $query->fetchAll(PDO::FETCH_ASSOC);

//new project creation section
if (isset($_POST['newproj'])){
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $query = $conn->prepare("SELECT * FROM projects where projectname=:projectname");
    $query->bindParam("projectname",$name,PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
    if ($result){
        echo '<script>alert("Project name is already in use")</script>';
    }
    else{
        $sqlinsert = "INSERT INTO projects (rewardID,userID,projectname,description) VALUES (?,?,?,?)";
        $statement = $conn->prepare($sqlinsert);
        $userid = $_SESSION['userid'];
        $statement->execute(['1',$userid,$name,$desc]);
    }
}

//project update section
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

//project delete setion
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
?>

<title>User Page</title>
<center><h1>User Page for <?php echo $_SESSION['username'];?></h1></center>
<br>

<div class="container">
    <h3>Create a new project here</h3>
    <form class="row g-2" method="POST" name="newproj">
        <div class="col-md-4">
            <label for="validationDefault01" class="form-label">Project Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="col-md-8">
            <label for="validationDefault02" class="form-label">Project Description</label>
            <input type="text" class="form-control" id="description" name="description" required>
        </div>
        <div class="col-1">
            <button class="btn btn-primary" type="newproj" name="newproj" value="newproj">Create new project</button>
        </div>
    </form>
    <br>
    <h3>My Project(s)</h3>
</div>
<?php
echo '<div class="container">';
$idarray = [];

foreach($changeresult as $projectset){
    echo '<h4>';
    echo 'ID: ', $projectset["projectID"], ' <br>Name: ' ,$projectset["projectname"], ' <br>Description: ' ,$projectset["description"];
    $projid = $projectset["projectID"];
    array_push($idarray,$projid);
    echo '</h4><br>';
}
?>

<div class="container">
    <h3>Update your project here!</h3>
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
    <h3>DELETE your project here!</h3>
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

<?php
//Print all projects section for user
echo '<h4>All Projects</h4><h5>';
foreach($project as $proj){
    echo '<br>';
    echo 'Project Name = ',$proj["projectname"],' | Project Description: ',$proj["description"],' | ';
    foreach($rewardresult as $rew){
        if($rew["projectID"] == $proj["projectID"]){
            echo 'Reward for this project = ',$rew["rewardtype"],' | Reward price for this project = $',$rew["rewardprice"],' | ';
        }
    }
    echo '<form class="row g-3" method="POST" name="support">
<div class="col-md-4">
<label for="validationDefault04" class="form-label">Give monetary support $</label>
<input type="text" class="form-control" id="amount" name="amount" required>
</div>
<button class="btn btn-primary" type="amount" name="projectID" value=',$proj['projectID'],'>$ Support</button>
</form>';   
}
echo '</h5>';
if (isset($_POST['amount'])){
        $sqlinsert = "INSERT INTO support (supportamount,userID,projectID) VALUES (?,?,?)";
        $statement = $conn->prepare($sqlinsert);
        $userid = $_SESSION['userid'];
        $amount = $_POST['amount'];
        $projectid = $_POST['projectID'];
        $statement->execute([$amount,$userid,$projectid]);
}
?>