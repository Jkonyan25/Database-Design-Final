<?php
// Initialize the session
session_start();
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
require_once "config.php";

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
			margin-top: 10px;
			margin-bottom: 10px;
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

	
<?php
?></table>
</div>
	<form  action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
	1.  (6 pts) List all the users who have a count of two blogs or more,and one blog has a tag of 'X', and another blog has a 
        tag of 'Y'.
		<div class="form-group" style="margin-top:10px;">
		<textarea  wrap = "hard" rows="1" cols="20" placeholder = "User 1" name="userA" class="form-control"></textarea>
	 	<textarea wrap = "hard" rows="1" cols="20" placeholder = "User 2" name="userB" class="form-control" ></textarea>
   		<input style = "position:relative; top:-6px;" type="submit" class="btn btn-primary" value="List" name = "listTwoBlogs">
		<title>Phase 3</title>
		<table class="center">
   		<?php
        echo nl2br("\n");
		if(isset($_POST['listTwoBlogs'])) 
		{
            $tagx = (trim($_POST['userA']));
            $tagy = (trim($_POST['userB']));
			$result = mysqli_query($link, "SELECT DISTINCT created_by FROM blogs WHERE blogid IN (SELECT blogid FROM ((SELECT * FROM blogstags WHERE tag='$tagx') AS table1 JOIN (SELECT blogid AS blogid2, tag AS tag2 FROM blogstags WHERE tag='$tagy') AS table2 ON table1.blogid = table2.blogid2))");
			while($rows2=mysqli_fetch_assoc($result))
			{
				?><tr><td><?php echo nl2br($rows2['created_by'])?></td></tr><?php
			}?></table><?php
			
        }
        ?>
        </table>    
	</div>
	<form  action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
	2. (6 pts) List all the blogs of user X, such that all the comments are positive for these blogs. 
		<div class="form-group" style="margin-top:10px;">
        <textarea  wrap = "hard" rows="1" cols="20" placeholder = "User X" name="user01" class="form-control"></textarea>
        <input style = "position:relative; top:-6px;" type="submit" class="btn btn-primary" value="List" name = "listBlogsPos">
		<title>Phase 3</title>
		<table class="center">
		<?php
		echo nl2br("\n");
		if(isset($_POST['listBlogsPos'])) 
		{
		$userx = (trim($_POST['user01']));
		$result4 = mysqli_query($link, "SELECT * FROM blogs WHERE created_by = '$userx' AND (blogid NOT IN(SELECT blogid FROM comments WHERE sentiment = 'negative')) AND (blogid IN(SELECT blogid FROM comments))");
			while($rows=mysqli_fetch_assoc($result4))
			{
				?>
				<td><a<?php echo $rows['blogid'] ?>">Blog ID: <?php echo $rows['blogid'];?></a></td>
				<td><a<?php echo $rows['subject'] ?>">Subject: <?php echo $rows['subject'];?></a></td>
				<td> <textarea readonly rows="5" cols="30"><?php echo ($rows['description']);?></textarea></td>
				<td>Posted: <?php echo $rows['pdate'];?></td>
				<?php
			}
		}
		?>
		</table>
	</div>
		<?php
		echo nl2br("\n3. (6 pts) List the users who posted the most number of blogs on 04/25/2022; if there is a tie, list all the users who have a tie:  \n");
$most = mysqli_query($link, 
"
SELECT  created_by,COUNT(blogid),RANK() OVER (ORDER BY COUNT(blogid) DESC) FROM blogs WHERE pdate = '2022-04-25' 
GROUP BY created_by ORDER BY COUNT(blogid) AND RANK() OVER (ORDER BY COUNT(blogid) DESC)= 1;
");
?><table class="center"><?php
while($rows=mysqli_fetch_assoc($most)) 
{
	if($rows['RANK() OVER (ORDER BY COUNT(blogid) DESC)']=='1') { //show those who tie first place
		
	?><tr><td><?php echo $rows["created_by"];?></td></tr><?php
	}
}
	?></table>
    </div>
	
	4. (6 pts) List the users who are followed by both X and Y. Usernames X and Y are inputs from the user. 
	<div class="form-group" style="margin-top:10px;">
         <textarea  wrap = "hard" rows="1" cols="20" placeholder = "User 1" name="user1" class="form-control"></textarea>
          <textarea wrap = "hard" rows="1" cols="20" placeholder = "User 2" name="user2" class="form-control" ></textarea>
        <input style = "position:relative; top:-6px;" type="submit" class="btn btn-primary" value="List" name = "listFollow">
		<?php
		echo nl2br("\n");
		if(isset($_POST['listFollow'])) {
		$user1 = (trim($_POST['user1']));
		
		$user2 = (trim($_POST['user2']));
		
		//echo $userx;
		$result2 = mysqli_query($link, "SELECT leadername FROM follows WHERE followername = '$user1' AND leadername IN (SELECT leadername FROM follows WHERE followername = '$user2');");
		?><table class="center"><?php
			while($rows2=mysqli_fetch_assoc($result2))
			{
				?><tr><td><?php echo nl2br($rows2['leadername'])?></td></tr><?php
			}?></table><?php
		}
		?></table>
    </div>

	5. (6 pts) List a user pair (A, B) such that they have at least one common hobby.
		<div class="form-group" style="margin-top:10px;">
			<input style = "position:relative; top:-6px;" type="submit" class="btn btn-primary" value="List" name = "listSameHobby">
			<?php
			echo nl2br("\n");
			if(isset($_POST['listSameHobby'])) {
			$result3 = mysqli_query($link, " SELECT DISTINCT E1.username, E1.hobby, E2.username AS username2, E2.hobby AS hobby2 FROM   hobbies E1, hobbies E2 WHERE  E1.hobby = E2.hobby AND E1.username < E2.username");?><table class="center"><?php
				while($rows3=mysqli_fetch_assoc($result3))
				{
					?><tr><td><?php echo nl2br($rows3['username'])?></td><td><?php echo nl2br($rows3['username2'])?></td><td><?php echo nl2br($rows3['hobby2'])?></td></tr><?php
				
				}?></table><?php
			}
			
/*echo nl2br("\n5. (6 pts) List a user pair (A, B) such that they have at least one common hobby.: \n");
$commonHobby = mysqli_query($link, "SELECT DISTINCT E1.username, E1.hobby, E2.username, E2.hobby FROM   hobbies E1, hobbies E2 WHERE  E1.hobby = E2.hobby AND E1.username < E2.username ");?><table class="center"><?php
while($rows=mysqli_fetch_assoc($commonHobby))
{?><tr><td><?php
	echo $rows['username'];?></td></tr><?php
}?></table><?php
*/
echo nl2br("\n6. (6 pts) Display all the users who never posted a blog: \n");
$neverBlogged = mysqli_query($link, "SELECT username FROM users WHERE username NOT IN(SELECT created_by FROM blogs) ");?><table class="center"><?php
while($rows=mysqli_fetch_assoc($neverBlogged))
{?><tr><td><?php
	echo $rows['username'];?></td></tr><?php
}?></table><?php

echo nl2br("\n7. (6 pts) Display all the users who never posted a comment: \n");
$neverCommented = mysqli_query($link, "SELECT username FROM users WHERE username NOT IN(SELECT posted_by FROM comments) ");?><table class="center"><?php
while($rows=mysqli_fetch_assoc($neverCommented))
{?><tr><td><?php
	echo $rows['username'];?></td></tr><?php
}?></table><?php

echo nl2br("\n8. (6 pts) Display all the users who posted some comments, but each of them is negative: \n");
$allNegative = mysqli_query($link, "SELECT DISTINCT posted_by FROM comments WHERE posted_by NOT IN (SELECT posted_by FROM comments WHERE sentiment = 'positive');");?><table class="center"><?php
while($rows=mysqli_fetch_assoc($allNegative))
{
	?><tr><td><?php echo $rows['posted_by'];?></td></tr><?php
}?></table><?php

echo nl2br("\n9. (6 pts) Display those users such that all the blogs they posted so far never received any negative comments: \n");
$noNegative = mysqli_query($link, "SELECT username FROM users WHERE username NOT IN (SELECT DISTINCT created_by FROM blogs WHERE blogid IN (SELECT blogid FROM comments WHERE sentiment = 'negative')) AND username IN (SELECT created_by FROM blogs)");?><table class="center"><?php
while($rows=mysqli_fetch_assoc($noNegative))
{
	?><tr><td><?php echo $rows['username'];?></td></td><?php
}
	?></table>
    </div>
	
	
	</form>
		
    <p>
        <a href="welcome.php" class="btn btn-danger ml-3">Back to Blogs</a>
    </p>
</body>
</html>