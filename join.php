<!-- ### Vladimir S. Udartsev
### udartsev.ru -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
  <title>Register</title>
</head>
<body>

<p><a href="index.php">Login</a>
<a href="join.php">Register</a>
<a href="recovery.php">Forgot password?</a></p>

<div id="register">
  <table border="1" cellpadding="10" width="300">
  <td>
  <h1>Register</h1>
    <form action="" method="post">
      <p>firstname <input type="text" name="firstname" value=""/></p>
      <p>lastname <input type="text" name="lastname" value=""/></p>
      <p>email <input type="text" name="email" value=""/></p>
      <p>password <input type="password" name="password" id="" /></p>
      <p>repeate password <input type="password" name="password2" id="" /></p>
      <p><input type="submit" value="send" name="send"/></p>
    </form>
    </td>
    </table>
</div>

<?php
include_once dirname(__FILE__) . '/config/dbconf.php';
include_once dirname(__FILE__) . '/class/sqlconn.php';
include_once dirname(__FILE__) . '/class/userAuth.php';

/*Creating Database Sqlconn Class*/
$sqlconn = new sqlconn($mysqlSettings);

/*Creating userAuth Class*/
$auth = new userAuth($sqlconn);

/*If Button Cklicked*/
if (isset($_POST['send'])) {

	/*Do User Register*/
	$userReg = $auth->userRegister($_POST['firstname'], $_POST['lastname'], $_POST['email'], $_POST['password'], $_POST['password2']);

	/*Check For Errors*/
	if ($userReg == false) {

		/*If Registration Error*/
		echo '<h3>' . $_SESSION['error'] . '</h3>';
		/*echo "<pre>";
		var_dump($auth->error);*/
	} else {
		/*If User Registered OK*/
		echo '<h2>Registered Successull</h2> Please, <a href="index.php">authorize</a>.';
	}
}

/*Closing Database Connection*/
$sqlconn->close();
unset($sqlconn);
?>
</body>
</html>