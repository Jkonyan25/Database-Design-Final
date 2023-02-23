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

$result = mysqli_query($link, "SELECT * FROM blogs JOIN blogstags ON blogs.blogid = '" . $blogid . "' AND blogstags.blogid = '" . $blogid . "'");
$resultC = mysqli_query($link, "SELECT * FROM comments WHERE blogid = '" . $blogid . "'");

$dailyLimit = mysqli_query($link, "SELECT COUNT(cdate) FROM comments WHERE posted_by = '" . $_SESSION['username'] . "' AND cdate = '" . $todaysDate . "'");
$_GET['sum'] = mysqli_fetch_array($dailyLimit);

$blogLimit = mysqli_query($link, "SELECT COUNT(blogid) FROM comments WHERE posted_by = '" . $_SESSION['username'] . "'AND blogid = '" . $_GET['blogid'] . "'");
$_GET['sumC'] = mysqli_fetch_array($blogLimit);

$ownLimit = mysqli_query($link, "SELECT COUNT(created_by) FROM blogs WHERE created_by = '" . $_SESSION['username'] . "'AND blogid = '" . $_GET['blogid'] . "'");
$_GET['ownBlog'] = mysqli_fetch_array($ownLimit);

$ownComment = mysqli_query($link, "SELECT (posted_by) FROM comments WHERE posted_by = '" . $_SESSION['username']."'");
$_GET['ownComment'] = mysqli_fetch_array($ownComment);
}
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$blogid = $_POST['blogid'];
    $dailyLimit = mysqli_query($link, "SELECT COUNT(cdate) FROM comments WHERE posted_by = '" . $_SESSION['username'] . "' AND cdate = '" . $todaysDate . "'");
    $_GET['sum'] = mysqli_fetch_array($dailyLimit);

    $blogLimit = mysqli_query($link, "SELECT COUNT(blogid) FROM comments WHERE posted_by = '" . $_SESSION['username'] . "'AND blogid = '" . $_POST['blogid'] . "'");
    $_GET['sumC'] = mysqli_fetch_array($blogLimit);
    $result = mysqli_query($link, "SELECT * FROM blogs JOIN blogstags ON blogs.blogid = '" . $blogid . "' AND blogstags.blogid = '" . $blogid . "'");
    $resultC = mysqli_query($link, "SELECT * FROM comments WHERE blogid = '" . $blogid . "'");
	$ownLimit = mysqli_query($link, "SELECT created_by FROM blogs WHERE created_by = '" . $_SESSION['username'] . "'AND blogid = '" . $blogid . "'");
	$_GET['ownBlog'] = mysqli_fetch_array($ownLimit);
	$ownComment = mysqli_query($link, "SELECT (posted_by) FROM comments WHERE posted_by = '" . $_SESSION['username']."'");
	$_GET['ownComment'] = mysqli_fetch_array($ownComment);
    $sentiment = "";
    if ($_POST["sentiments"] == "Positive")
    {
        $sentiment = "positive";
    }
    else
    {
        $sentiment = "negative";
    }

    if (($_GET['sum'][0] <= 2) && ($_GET['sumC'][0] < 1) && isset($_POST["submit"]) && !(empty(trim($_POST["comment"]))))
    {

        $sql = "INSERT INTO comments (sentiment, description, cdate, blogid, posted_by) VALUES (?, ?, ?, ?, ?)";
        // $id = mysqli_insert_id($link);
        if ($stmt = mysqli_prepare($link, $sql))
        {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sssis", $param_sentiment, $param_comment, $param_date, $blogid, $param_username);
            // Set parameters
            $param_sentiment = $sentiment;
            $param_comment = trim($_POST["comment"]);
            $param_date = date("Y/m/d");
            $param_username = $_SESSION["username"];

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt))
            {
                // Redirect
                
               header("location: comment.php?blogid=".$blogid);

            }
            else
            {
                echo "Oopsies.";
            }
            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

}
$result = mysqli_query($link, "SELECT * FROM blogs JOIN blogstags ON blogs.blogid = '" . $blogid . "' AND blogstags.blogid = '" . $blogid . "'");
//mysqli_close($link);

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
        <h1>Blog</h1>
        <table class="center">
            <?php   
				$previousID = "";
                while($rows=mysqli_fetch_assoc($result))
                {
             ?>
			 <?php if($previousID != $rows['blogid']): ?>
            <tr>
                <td><a href="userProfileFollow.php?user= <?php echo $rows['created_by'] ?>">User: <?php echo $rows['created_by'];?></a></td>
				<td > <a href="comment.php?blogid= <?php echo $rows['blogid'] ?>"> <?php echo $rows['subject'];?> </a></td>
                <td><?php echo ($rows['description']);?></td>
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
			$previousID = $rows['blogid'];
                }
             ?>
        </table>
		<h1>Comments</h1>
		<table class = "center">
            <?php   
                while($rows=mysqli_fetch_assoc($resultC))
                {
             ?>
            <tr>
			<td><a href="userProfileFollow.php?user= <?php echo $rows['posted_by'] ?>">User: <?php echo $rows['posted_by'];?></a></td>
                
				<td >  <?php echo $rows['description'];?> </td>
				
				<td >  
				<?php 
				if($rows['sentiment']=='negative') {
					echo '-';
				}
				else {
					echo '+';
				}
				?> 
				</td>
				<td >  <?php echo $rows['cdate'];?> </td>
                <?php if (!empty($_GET['ownComment'][0]) && $_GET['ownComment'][0] == $rows["posted_by"]): ?>
				<td > <a href="editDeleteComment.php?commentid= <?php echo $rows['commentid'] ?>&blogid=<?php echo $rows['blogid'] ?>"> Edit/Delete  </a></td>
                <?php endif; ?>
				<?php if (!empty($_GET['ownComment'][0]) &&$_GET['ownComment'][0] != $rows["posted_by"]): ?>
				<td > Community Comment </td>
                <?php endif; ?>
			
            </tr>
            <?php
                }
             ?>
        </table>
    </section>
	<br>
	<br>
	<?php if (empty($_GET['ownBlog'][0]) ): ?>
	<div class="form-group">
	
	<td><textarea name = "comment" placeholder="Comment..." rows="4" cols="30"></textarea></td>
	<br>
	 <label for="cars">Choose a sentiment:</label>
  <select name="sentiments" id="sentiments">
    <option value="Positive">Positive</option>
    <option value="Negative">Negative</option>
  </select>
  <br><br>
                <input type="submit" class="btn btn-primary" value="Post" name = "submit">
            </div>
	<?php endif; ?>		       
</form>
    <p>
        <a href="welcome.php" class="btn btn-warning">Back to Blogs</a>
    </p>
</body>
</html>