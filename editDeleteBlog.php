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

if(isset($_GET["blogid"])) {
$blogid = trim($_GET["blogid"]);

$result = mysqli_query($link, "SELECT * FROM blogs WHERE blogid = '" . $blogid . "' ");
$result2 = mysqli_query($link, "SELECT * FROM blogstags WHERE blogid = '" . $blogid . "' ");
}
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$blogid = $_POST['blogid'];
    $dailyLimit = mysqli_query($link, "SELECT COUNT(cdate) FROM comments WHERE posted_by = '" . $_SESSION['username'] . "' AND cdate = '" . $todaysDate . "'");
    $_GET['sum'] = mysqli_fetch_array($dailyLimit);

    $blogLimit = mysqli_query($link, "SELECT COUNT(blogid) FROM comments WHERE posted_by = '" . $_SESSION['username'] . "'AND blogid = '" . $_POST['blogid'] . "'");
    $_GET['sumC'] = mysqli_fetch_array($blogLimit);
    $result = mysqli_query($link, "SELECT * FROM blogs WHERE blogid = '" . $blogid . "'");
    $resultC = mysqli_query($link, "SELECT * FROM comments WHERE blogid = '" . $blogid . "'");
	$result2 = mysqli_query($link, "SELECT * FROM blogstags WHERE blogid = '" . $blogid . "' ");
	if(!empty(trim($_POST["subject"])) && !empty(trim($_POST["description"])) && !empty(trim($_POST["tag"]))) {
	$sql = "UPDATE blogs SET subject =  (?),description =(?) WHERE blogid = '".$blogid."' ";
	        if($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss",$param_subject,$param_description);
            
            // Set parameters
			$param_subject = $_POST["subject"];
			$param_description = $_POST["description"];
			
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect 
				//header("location: editDeleteBlog.php?blogid=".$blogid);
				//header("location: welcome.php");
            } else{
                echo "Oops.";
            }
            // Close statement
            mysqli_stmt_close($stmt);
        }
	
	
	
		$tags_arr = explode (",", $_POST["tag"]); 
		mysqli_query($link,"DELETE FROM blogstags WHERE blogid='".$blogid."';");
		
		// Prepare an insert statement
		for ($i = 0; $i < count($tags_arr); $i++) 
		{
			$sql = "INSERT INTO blogstags (blogid, tag) VALUES (?, ?)";
			if($stmt = mysqli_prepare($link, $sql))
			{
				// Bind variables to the prepared statement as parameters
				mysqli_stmt_bind_param($stmt, "is", $blogid, $param_tags);
				// Set parameters
				$param_tags = $tags_arr[$i];
				// Attempt to execute the prepared statement
				if(mysqli_stmt_execute($stmt)){
				// Redirect 
					header("location: welcome.php");
				} else{
					echo "Oopsie Doopsie.";
				}
				// Close statement
				mysqli_stmt_close($stmt);
			}
		}
	
	
}

	if(isset($_POST["delete"]))
	{
		mysqli_query($link, "DELETE FROM comments WHERE blogid = '".$blogid."'");
		mysqli_query($link, "DELETE FROM blogstags WHERE blogid = '".$blogid."'");
		mysqli_query($link, "DELETE FROM blogs WHERE blogid = '".$blogid."'");
		header("location: welcome.php");
	}

$result = mysqli_query($link, "SELECT * FROM blogs WHERE blogid = '" . $blogid . "' ");
$result2 = mysqli_query($link, "SELECT * FROM blogstags WHERE blogid = '" . $blogid . "' ");
mysqli_close($link);
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <style>
        body{ font: 14px sans-serif; text-align: center; background-color: #f7fef6 }
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
    <script>
	function refreshPage(){
    window.location.reload();
} 
	</script>
</head>
<body>
	<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
	<input type="hidden" name="blogid" value="<?php echo $blogid;?>"/>	
	    <section>
        <h1>Edit Blog</h1>
        <table class="center">
            <tr>
			 <td>User</td>
			 <td>Blog Subject</td>
			 <td>Blog Contents</td>
			 <td>Blog Tags</td>
			 </tr>
            <?php   
				
                while($rows=mysqli_fetch_assoc($result))
                {
             ?>
            <tr>
                <td><?php echo $rows['created_by'];?></td>
				<td><textarea rows="4" cols="30" name = "subject"><?php echo $rows['subject'];?></textarea></td>
                <td><textarea rows="4" cols="30" name = "description"><?php echo ($rows['description']);?></textarea></td>
				<td><textarea rows="4" cols="30" name = "tag"><?php 
						$i = 0;
						while($rows2=mysqli_fetch_assoc($result2)) {
						if($i!=0){echo ','.$rows2['tag'];}
						else{echo $rows2['tag'];}
						++$i;
					}
					?></textarea></td>
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
		   
</form>
    <p>
        <a href="welcome.php" class="btn btn-warning">Cancel</a>
    </p>
</body>
</html>