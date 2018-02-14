<!-- ### Vladimir S. Udartsev
### udartsev.ru -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
  <title>Password Recovery</title>
</head>
<body>

<p><a href="index.php">Login</a>
<a href="join.php">Register</a>
<a href="recovery.php">Forgot password?</a></p>

<div id="recovery">
	<table border="1" cellpadding="10" width="300">
	<td>
	<h1>Password Recovery</h1>
    <form action="" method="post">
      <p>email <input type="text" name="email" value="your@mail.com"/></p>
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

	/*User Password Recovery Function*/
	$recovery = $auth->passwordRecovery($_POST['email']);

	/*Check For Recovery*/
	if ($recovery == false) {

		/*If Email Did Not Find*/
		/*echo "<pre>";
		var_dump($auth->error);*/
		echo $_SESSION['error'];
		return false;

	} else {
		/*If Email Found Successfully*/
		echo 'New password has been sent to your email address.';
	}

}

/*Closing Database Connection*/
$sqlconn->close();
unset($sqlconn);
?>
</body>
</html>