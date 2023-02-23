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

if(isset($_GET["user"])) {
$user = trim($_GET["user"]);
$result = mysqli_query($link, "SELECT * FROM users where username = '" . $user . "'");
$ownUser = mysqli_query($link, "SELECT username FROM users WHERE username = '" . $_SESSION['username']."' AND username = '" . $user . "'");
$_GET['ownUser'] = mysqli_fetch_array($ownUser);

$alreadyFollowing = mysqli_query($link, "SELECT leadername,followername FROM follows WHERE 
leadername = '" . $user."' AND followername = '" . $_SESSION["username"] . "'");
$_GET['alreadyFollowing'] = mysqli_fetch_array($alreadyFollowing);

$followers = mysqli_query($link, "SELECT * FROM follows WHERE leadername = '" . $user . "'");
$following = mysqli_query($link, "SELECT * FROM follows WHERE followername = '" . $user . "'");

$hobbies = mysqli_query($link, "SELECT * FROM hobbies WHERE username = '".$user."'");

}
//implement hobbies
//show basic user profile, add follow button.

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$user = $_POST['user'];
    $result = mysqli_query($link, "SELECT * FROM users WHERE username = '" . $user . "'");
	$ownUser = mysqli_query($link, "SELECT username FROM users WHERE username = '" . $_SESSION['username']."' AND username = '" . $user . "'");
	$_GET['ownUser'] = mysqli_fetch_array($ownUser);
	$alreadyFollowing = mysqli_query($link, "SELECT leadername,followername FROM follows WHERE 
	leadername = '" . $user."' AND followername = '" . $_SESSION["username"] . "'");
	$_GET['alreadyFollowing'] = mysqli_fetch_array($alreadyFollowing);
	$followers = mysqli_query($link, "SELECT * FROM follows WHERE leadername = '" . $user . "'");
	$hobbies = mysqli_query($link, "SELECT * FROM hobbies WHERE username = '".$user."'");
    if (isset($_POST["follow"]) && !isset($_GET['alreadyFollowing'][0])) //and not already following
    {
        $sql = "INSERT INTO follows (leadername, followername) VALUES (?, ?)";
        if ($stmt = mysqli_prepare($link, $sql))
        {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $param_leader,$param_follower);
            // Set parameters
            $param_leader = $user;
			$param_follower = $_SESSION["username"];
            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt))
            {
                // Redirect
               header("location: userProfileFollow.php?user=".$user);

            }
            else
            {
                echo "Oopsies.";
            }
            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
	else if (isset($_POST["follow"]) && isset($_GET['alreadyFollowing'][0])) //and already following
    {
        mysqli_query($link,"DELETE FROM follows WHERE leadername = '" . $user."' AND followername = '" . $_SESSION['username'] . "'");
		header("location: userProfileFollow.php?user=".$user);
	}
	$result = mysqli_query($link, "SELECT * FROM users WHERE username = '" . $user . "'");
	$ownUser = mysqli_query($link, "SELECT username FROM users WHERE username = '" . $_SESSION['username']."' AND username = '" . $user . "'");
	$_GET['ownUser'] = mysqli_fetch_array($ownUser);
	$followers = mysqli_query($link, "SELECT * FROM follows WHERE leadername = '" . $user . "'");

$alreadyFollowing = mysqli_query($link, "SELECT leadername,followername FROM follows WHERE 
leadername = '" . $user."' AND followername = '" . $_SESSION["username"] . "'");
$_GET['alreadyFollowing'] = mysqli_fetch_array($alreadyFollowing);
$hobbies = mysqli_query($link, "SELECT * FROM hobbies WHERE username = '".$user."'");
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
    <script>
	function refreshPage(){
    window.location.reload();
} 
	</script>
</head>
<body>
	<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
	<input type="hidden" name="user" value="<?php echo $user;?>"/>
	
	    <section>
        <h1>User</h1>
        <table class="center">
            <?php   
				
                while($rows=mysqli_fetch_assoc($result))
                {
					
             ?>
				<tbody>
				<thead>
				<tr>
                <td><?php echo "Username: ".$rows['username'];?></td>
				<td><?php echo "E-mail: ".$rows['email'];?></td>
				<td><?php echo "Name: ".$rows['firstName']." ".$rows['lastName'];?></td>
				</tr>
				</thead>
				</tbody>	
            
            <?php
			
                }
             ?>
			 
        </table>
		<h1>Hobbies</h1>
        <table class="center">
            <?php   
				
                while($rows=mysqli_fetch_assoc($hobbies))
                {
					
             ?>
				<tbody>
				<thead>
				<tr>
                <td><?php echo $rows['hobby']." ";?></td>
				</tr>
				</thead>
				</tbody>	
            
            <?php
			
                }
             ?>
			 
        </table>
		<h1>Followers</h1>
		<table class="center">
            <?php   
				$i = 0;
                while($rows=mysqli_fetch_assoc($followers))
                {
					++$i;
             ?>
			 
				<thead>
				<tr>
				<td><a href="userProfileFollow.php?user= <?php echo $rows['followername'] ?>"><?php echo $rows['followername'];?></a></td>
                
				</tr>
				</thead>
				
					
            
            <?php
			
                }
             ?>
			 <h1> <?php echo $i ?> </h1>
        </table>
		
		<h1>Following</h1>
		<table class="center">
            <?php   
				$i = 0;
                while($rows=mysqli_fetch_assoc($following))
                {
					++$i;
             ?>
				
				<thead>
				<tr>
				<td><a href="userProfileFollow.php?user= <?php echo $rows['leadername'] ?>"><?php echo $rows['leadername'];?></a></td>
                
				</tr>
				</thead>
				
					
            
            <?php
			
                }
				
             ?>
			 <h1> <?php echo $i ?> </h1>
        </table>
	
    </section>
	<br>
	<br>
	<?php 
	if (empty($_GET['ownUser'][0]))
	{
		if(!empty($_GET['alreadyFollowing'][0]))
		{
	?>
	
	<div class="form-group">
            <input type="submit" class="btn btn-primary" value="Unfollow" name = "follow">
    </div>
	
	
	
	
	<?php 
		}
		else {
		?>
		
			
			<div class="form-group">
            <input type="submit" class="btn btn-primary" value="Follow" name = "follow">
    </div>
<?php 
	}
	}
	?>
	
</form>
    <p>
        <a href="welcome.php" class="btn btn-warning">Back to Blogs</a>
    </p>
</body>
</html>