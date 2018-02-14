<!-- ### Vladimir S. Udartsev
### udartsev.ru -->
<!DOCTYPE html>
<html lang="en">
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
  <title>User Auth</title>
</head>
<body>

<p><a href="index.php">Login</a>
<a href="join.php">Register</a>
<a href="recovery.php">Forgot password?</a></p>

<div id="login">
	<table border="1" cellpadding="10" width="300">
	<td>
		<h1>Login</h1>
		<form action="" method="post">
	  		<p>email <input type="text" name="email" value="demo@demo.com"/></p>
	    	<p>password <input type="password" name="password" value="123"/></p>
	    	<p><input type="submit" value="send" name="send"/></p>
		</form>
	</td>
	</table>
</div>


<?php
/*Start The Session*/
session_start();

/*Includes Library*/
include_once dirname(__FILE__) . '/config/dbconf.php';
include_once dirname(__FILE__) . '/class/sqlconn.php';
include_once dirname(__FILE__) . '/class/userAuth.php';

/*Creating Database Sqlconn Class*/
$sqlconn = new sqlconn($mysqlSettings);

/*Creating userAuth Class*/
$auth = new userAuth($sqlconn);

/*User Authorization*/
if (isset($_POST['send'])) {
	if ($auth->authorize()) {
		echo '<h3>Welcome ' . $_SESSION['userName'] . '!</h3><a class="button btn" value="exit" name="exit" href="index.php?exit">Exit</a>';
	} else {echo $_SESSION['error'];}
}

/*User Exit*/
if (isset($_GET['exit'])) {
	$auth->userExit();
}

/*User Activation Code Check*/
if (isset($_GET['activation'])) {
	$activation = $auth->activation($_GET['activation']);
	if ($activation === true) {
		echo "<h4>Email " . $_SESSION['email'] . " activated! You can login.</h4>";
	} else {
		echo $_SESSION['error'];
	}
}

/*User Authorization Check*/
if ($auth->authorizeCheck()) {
	echo '<h3>Welcome ' . $_SESSION['userName'] . '!</h3><a class="button btn" value="exit" name="exit" href="index.php?exit">Exit</a>';
} else {
	/*If Errors -> Password Recovery*/
	if (isset($error)) {
		echo $_SESSION['error'];
	}
}

/*Closing Database Connection*/
$sqlconn->close();
unset($sqlconn);
?>
</body>
</html>


</html>
