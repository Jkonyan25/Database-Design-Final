<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

require_once "config.php";

$empty_field = "";
$todaysDate = date("Y/m/d");
$dailyLimit = mysqli_query($link,"SELECT COUNT(pdate) FROM blogs WHERE created_by = '".$_SESSION['username']."' AND pdate = '".$todaysDate."'");
$_GET['sum'] = mysqli_fetch_array($dailyLimit);

$ownLimit = mysqli_query($link, "SELECT (created_by) FROM blogs WHERE created_by = '" . $_SESSION['username']."'");
$_GET['ownBlog'] = mysqli_fetch_array($ownLimit);
	
if($_SERVER["REQUEST_METHOD"] == "POST"){
	
	if(($_GET['sum'][0]<=1)&& isset($_POST["post"]) && !(empty(trim($_POST["subject"])) || empty(trim($_POST["description"])) || empty(trim($_POST["tags"]))))
	{
        // Prepare an insert statement
        $sql = "INSERT INTO blogs (blogid, subject, description, pdate, created_by) VALUES (?, ?, ?, ?, ?)";
        if($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "issss",$id, $param_subject, $param_description, $param_date, $param_username);
            
            // Set parameters
            $param_subject = $_POST["subject"];
			$param_description = $_POST["description"];
			$param_date = date("Y/m/d");
			$param_username = $_SESSION["username"];
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect 
            } else{
                echo "Oops.";
            }
            // Close statement
            mysqli_stmt_close($stmt);
        }
       $id = mysqli_insert_id($link);
		//echo($id);
		$tags_arr = explode (",", $_POST["tags"]); 
        // Prepare an insert statement
		for ($i = 0; $i < count($tags_arr); $i++) {
        $sql = "INSERT INTO blogstags (blogid, tag) VALUES (?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "is", $id, $param_tags);
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
}
$result = mysqli_query($link, "SELECT * FROM blogs INNER JOIN blogstags ON blogstags.blogid = blogs.blogid");

?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
	<style>
	body{ 
		background-color: #f7fef6; 
	}
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
		body {
   padding: 20px;
  text-align:center;
 }
 textarea {
  resize: none;
}
    </style>
</head>
<body >
<title>Blogs</title>
<h1 class="my-5">Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?>.</b></h1>
	<form  action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
	<div class="form-group">
         <textarea  wrap = "hard" rows="4" cols="30" placeholder = "Subject..." name="subject" class="form-control"></textarea>
    </div>   
	<div class="form-group">
          <textarea wrap = "hard" rows="4" cols="30" placeholder = "Description..." name="description" class="form-control" ></textarea>
    </div>   
	<div class="form-group"> 
         <textarea wrap = "hard" rows="4" cols="30" placeholder = "Tag 1, Tag 2, Tag n..." name="tags" class="form-control"></textarea>
    </div>   
	<div class="form-group">
        <input type="submit" class="btn btn-primary" value="Post" name = "post">
    </div>
	    <section>
        <h1>Blogs</h1>
        <table class="center">
            <?php   
				$previousID = "";
                while($rows=mysqli_fetch_assoc($result))
                {
             ?>
			
				<tr>
				<?php if($previousID != $rows['blogid']): ?>
					<td><a href="userProfileFollow.php?user= <?php echo $rows['created_by'] ?>">User: <?php echo $rows['created_by'];?></a></td>
					<td > Blog: <a href="comment.php?blogid= <?php echo $rows['blogid'] ?>"> <?php echo $rows['subject'];?> </a></td>
					<?php if (!empty($_GET['ownBlog'][0]) && $_GET['ownBlog'][0] == $rows["created_by"]): ?>
					<td > <a href="editDeleteBlog.php?blogid= <?php echo $rows['blogid'] ?>"> Edit/Delete  </a></td>
					<?php endif; ?>
					<?php if (!empty($_GET['ownBlog'][0]) &&$_GET['ownBlog'][0] != $rows["created_by"]): ?>
					<td > Community Blog </td>
					<?php endif; ?>
					<td> <textarea readonly rows="5" cols="30"><?php echo ($rows['description']);?></textarea></td>
					<td>Posted: <?php echo $rows['pdate'];?></td>
					<td><?php 
						$result2 = mysqli_query($link, "SELECT * FROM blogstags WHERE blogid = '" . $rows['blogid'] . "'");
						while($rows2=mysqli_fetch_assoc($result2)) {
						echo '#'.$rows2['tag']." ";
					}
					?></td>
					<?php endif; ?>
				</tr>
			
			<?php
				$previousID =$rows['blogid'];
                }
				mysqli_close($link);
			?>
			
        </table>
    </section>
	
</form>
		<a href="phase3.php" style = "position:relative; top:10px;" class="btn btn-danger ml-3">Phase 3</a>
    <p style = "position:relative; top:10px;">
        <a href="reset-password.php" class="btn btn-warning">Reset Your Password</a>
        <a href="logout.php" class="btn btn-danger ml-3">Sign Out of Your Account</a>
		
    </p>
</body>
</html>