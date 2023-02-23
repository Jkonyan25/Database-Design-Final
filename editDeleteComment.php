<?php

// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true)
{
    header("location: login.php");
    exit;
}

require_once "config.php";

$todaysDate = date("Y/m/d");

if(isset($_GET["commentid"])) {
$commentid = trim($_GET["commentid"]);
$result = mysqli_query($link, "SELECT * FROM comments WHERE commentid = '" . $commentid . "'");
$blogid = trim($_GET["blogid"]);
}
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$commentid = $_POST['commentid'];
	$blogid = $_POST['blogid'];
    $dailyLimit = mysqli_query($link, "SELECT COUNT(cdate) FROM comments WHERE posted_by = '" . $_SESSION['username'] . "' AND cdate = '" . $todaysDate . "'");
    $_GET['sum'] = mysqli_fetch_array($dailyLimit);
    $blogLimit = mysqli_query($link, "SELECT COUNT(commentid) FROM comments WHERE posted_by = '" . $_SESSION['username'] . "'AND commentid = '" . $_POST['commentid'] . "'");
    $_GET['sumC'] = mysqli_fetch_array($blogLimit);
    $result = mysqli_query($link, "SELECT * FROM comments WHERE commentid = '" . $commentid . "'");
    $resultC = mysqli_query($link, "SELECT * FROM comments WHERE commentid = '" . $commentid . "'");
	if(!empty(trim($_POST["description"]))) 
	{
	$sql = "UPDATE comments SET description = (?) WHERE commentid = '".$commentid."' ";
	        if($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s",$param_description );
            // Set parameters
			$param_description = $_POST["description"];
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect 
				header("location: comment.php?blogid=".$blogid);
            } else{
                echo "Oops.";
            }
            // Close statement
            mysqli_stmt_close($stmt);
        }
	}
	if(isset($_POST["delete"]))
	{
		mysqli_query($link, "DELETE FROM comments WHERE commentid = '".$commentid."'");
		header("location: comment.php?blogid=".$blogid);
	}

$result = mysqli_query($link, "SELECT * FROM comments WHERE commentid = '" . $commentid . "'");
mysqli_close($link);
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <style>
        body{ font: 14px sans-serif; text-align: center; background-color: #f7fef6}
		    <style>
        table {
            margin: 0 auto;
            font-size: large;
            border: 1px solid black;
        }
  
        h1 {
            text-align: center;
            color: #006600;
            font-size: xx-large;
            font-family: 'Gill Sans', 'Gill Sans MT', 
            ' Calibri', 'Trebuchet MS', 'sans-serif';
        }
  
        td {
            background-color: #E4F5D4;
            border: 1px solid black;
        }
  
        th,
        td {
            font-weight: bold;
            border: 1px solid black;
            padding: 10px;
            text-align: center;
        }
  
        td {
            font-weight: lighter;
        }
    		.center {
  margin-left: auto;
  margin-right: auto;
}
textarea {
  resize: none;
}
	</style>
</head>
<body>
	<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
	<input type="hidden" name="commentid" value="<?php echo $commentid;?>"/>
	<input type="hidden" name="blogid" value="<?php echo $blogid;?>"/>		
	<section>
        <h1>Edit Comment</h1>
        <table class="center">
            <tr>
			 <td>User</td>
			 <td>Comment Contents</td>
			 </tr>
            <?php   
                while($rows=mysqli_fetch_assoc($result))
                {
             ?>
            <tr>
                <td><?php echo $rows['posted_by'];?></td>
				
                <td><textarea rows="4" cols="30" name = "description"><?php echo ($rows['description']);?></textarea></td>
            </tr>
            <?php
                }
             ?>
        </table>
    </section>
		<br>
		<br>
     	<div class="form-group">
                <input type="submit" class="btn btn-primary" value="Edit and Submit" name = "post">
				<input type="submit" class="btn btn-primary" value="Delete" name = "delete">
        </div>        
		<br>
		<a href="comment.php?blogid=<?php echo $blogid; ?>" class="btn btn-warning">Cancel</a>
		
		    
</form>
    <p>
        <a href="welcome.php" class="btn btn-warning">Back to Blogs</a>
		
    </p>
</body>
</html>