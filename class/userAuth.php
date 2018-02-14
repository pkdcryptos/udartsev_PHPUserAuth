<?php
### Vladimir S. Udartsev
### udartsev.ru
/*User Authorization Class*/
class userAuth {

	/*Variables*/
	private $sqlconn;
	public $error;

	/*Autoconstruction Function*/
	function __construct($sqlconn) {
		/*Save Var*/
		$this->sqlconn = $sqlconn;

	}

	/*User Authorization Function*/
	public function authorize() {
		/*Vars*/
		$db = $this->sqlconn;

		/*Prepare Email To Query*/
		$email = $db->preapreString($_POST['email']);

		/*Prepare Password To Query and Hash it With Sha256 Algorythm*/
		$pass = hash('sha256', $db->preapreString($_POST['password']));

		/*Creating Query Request*/
		$query = $db->query("SELECT * FROM `users` WHERE  `email` =  '$email' AND  `password` = '$pass';");

		/*Check if Connection Is OK*/
		if ($query !== false) {
			/*Checking If User In The Database Already*/
			if ($query->num_rows !== 0) {

				/*If True, Cach User Data*/
				$data = mysqli_fetch_assoc($query);

				/*Save User Data to Session*/
				$_SESSION['userId'] = $data['id'];
				$_SESSION['userEmail'] = $data['email'];
				$_SESSION['userName'] = $data['firstname'] . ' ' . $data['lastname'];

				/*Generating Session Code*/
				$sessionCode = $this->generateCode(15);

				/*Prepearing Query*/
				$userAgent = $db->preapreString($_SERVER['HTTP_USER_AGENT']);

				/*Creating Checking Session Query Request */
				$query = $db->query("SELECT * FROM `users_sessions` WHERE `user_id`= '" . $_SESSION['userId'] . "'");

				if ($query->num_rows !== 0) {
					/*If User Session Row Already Exists In The Database -> update it*/
					$db->query("UPDATE `users_sessions` SET `session_code` = '" . $sessionCode . "', `session_user_agent` = '" . $userAgent . "' WHERE `user_id` = '" . $_SESSION['userId'] . "' ");
				} else {
					/*If NO User Session Row In Database -> add it*/
					$db->query("INSERT INTO `users_sessions` (`user_id`, `session_code`, `session_user_agent`) VALUES ('" . $_SESSION['userId'] . "', '$sessionCode', '$userAgent')");
				}

				/*Save User Cookies For 2 Weeks*/
				setcookie("userId", $_SESSION['userId'], time() + 3600 * 24 * 14);
				setcookie("userCode", $sessionCode, time() + 3600 * 24 * 14);

				/*Success Return*/
				return true;

			} else {
				/*If User Does Not in The Database*/
				/*Creating Query Request*/
				$query = $db->query("SELECT * FROM `users` WHERE  `email` =  '$email' ");

				/*Checking if User Password Entered Correct*/
				if ($query->num_rows !== 0) {
					$this->error['userError'] = 'Incorrect Password!';
					$_SESSION['error'] = 'Incorrect Password!';
				} else {
					$this->error['userError'] = 'User Does Not Exist!';
					$_SESSION['error'] = 'User Does Not Exist!';
				}

				/*False Return*/
				return false;
			}
		} else {
			$_SESSION['error'] = 'Database connection error!';
			return false;
		}
	}

	/*User Authorization Function*/
	public function authorizeCheck() {

		/*Checking $_SESSION Globals Exist*/
		if (isset($_SESSION['userId']) and isset($_SESSION['email'])) {
			return true;

		} else {

			/*Checking Cookies Exist*/
			if (isset($_COOKIE['userId']) and isset($_COOKIE['userCode'])) {

				/*If Cookies Exist -> Check It In Database*/
				/*Vars*/
				$db = $this->sqlconn;

				/*Prepare Query*/
				$userId = $db->preapreString($_COOKIE['userId']);
				$userCode = $db->preapreString($_COOKIE['userCode']);
				$userAgent = $db->preapreString($_SERVER['HTTP_USER_AGENT']);

				/*Creating Query Request*/
				$query = $db->query("SELECT * FROM `users_sessions` WHERE `user_id`= '$userId' ");

				/*Checking If User Session In The Database Already*/
				if ($query->num_rows !== 0) {

					/*If True, Cach User Sessoin Data*/
					$data = mysqli_fetch_assoc($query);

					/*Check The Session Data With User Request*/
					if ($data['session_code'] == $userCode and $data['session_user_agent'] == $userAgent) {

						/*Creating Query Request*/
						$query = $db->query("SELECT * FROM `users` WHERE `id`= '$userId' ");

						/*If True, Cach User Sessoin Data*/
						$data = mysqli_fetch_assoc($query);

						/*If Session Data Exist -> Save Cookies*/
						$_SESSION['userId'] = $userId;
						$_SESSION['userEmail'] = $data['email'];
						$_SESSION['userName'] = $data['firstname'] . ' ' . $data['lastname'];

						/*Update User Cookies For 2 Weeks*/
						setcookie("userId", $_SESSION['userId'], time() + 3600 * 24 * 14);
						setcookie("userCode", $userCode, time() + 3600 * 24 * 14);

						/*Success Return*/
						return true;

					} else {
						/*If Session Data Does Not Exist -> return false*/
						return false;
					}
				} else {
					/*If User Data Does Not Exist -> return false*/
					return false;
				}
			} else {
				/*If User Cookies Does Not Find -> return false*/
				return false;
			}

		}
	}

	/*User Register Function*/
	function userRegister($firstname, $lastname, $email, $password, $password2) {

		/*Check User*/
		$userCheck = $this->check_new_user($firstname, $lastname, $email, $password, $password2);

		if ($userCheck == true) {

			/*Vars*/
			$db = $this->sqlconn;

			/*Prepare Vars For Query*/
			$firstname = $db->preapreString($firstname);
			$lastname = $db->preapreString($lastname);
			$email = $db->preapreString($email);

			/*Prepare Password To Query and Hash it With Sha256 Algorythm*/
			$pass = hash('sha256', $db->preapreString($password));

			/*Creating Query Request*/
			$query = $db->query("INSERT INTO `users` (`firstname`, `lastname`, `email`, `password`) VALUES ('$firstname', '$lastname', '$email', '$pass');");

			/*Checking If User In The Database Already*/
			if ($query == true) {
				return true;
			} else {
				/*Registration Insert DB Error*/
				$this->error['SQLUserInsertError'] = 'New user SQL insert error: ' . $query->error;
				$_SESSION['error'] = 'Can not insert new user!';
				return false;
			}
		} else {
			return false;
		}
	}

	/*Check New User Exists*/
	private function check_new_user($firstname, $lastname, $email, $password, $password2) {

		/*Checking Inserted Data Validation*/
		if (empty($firstname) or empty($lastname) or empty($email) or empty($password) or empty($password2)) {
			$this->error['registerError'] = 'All fields are required!';
			$_SESSION['error'] = 'All fields are required!';
			return false;
		}

		if ($password != $password2) {
			$this->error['registerError'] = 'The passwords you entered do not match';
			$_SESSION['error'] = 'The passwords you entered do not match';
			return false;
		}

		if (strlen($password) < 3 or strlen($password) > 30) {
			$this->error['registerError'] = 'The password must be between 3 and 30 characters in length';
			$_SESSION['error'] = 'The password must be between 3 and 30 characters in length';
			return false;
		}

		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$this->error['registerError'] = 'Your email is not valid!';
			$_SESSION['error'] = 'Your email is not valid!';
			return false;
		}

		/*Check User in Database*/
		$db = $this->sqlconn;

		/*Prepare Vars For Query*/
		$email = $db->preapreString($email);

		/*Creating Query Request*/
		$query = $db->query("SELECT * FROM `users` WHERE  `email` =  '$email' ");

		/*Checking if User Password Entered Correct*/
		if ($query->num_rows !== 0) {

			/*If Email Already In Database*/
			$this->error['userError'] = 'User email already used!';
			$_SESSION['error'] = 'Email already used!';
			return false;

		} else {
			return true;
		}
	}

	/*User Exit Function*/
	public function userExit() {

		/*Set Empty Cookies*/
		unset($_COOKIE['userId']);
		unset($_COOKIE['userCode']);
		setcookie("userId", '', time() - 3600);
		setcookie("userCode", '', time() - 3600);

		/*Kill The Session*/
		session_destroy();

		/*Redirect To Index Page*/
		header("Location: index.php");
	}

	/*Password Recovery Function*/
	public function passwordRecovery($email) {

		/*Check Email*/
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$this->error['emailValidationError'] = 'Invalid email entered!';
			$_SESSION['error'] = 'Invalid email entered!';
			return false;
		}

		/*Set Sqlconn Var Into $db*/
		$db = $this->sqlconn;

		/*SQL string prepare*/
		$email = $db->preapreString($email);

		/*Creating Query Request*/
		$query = $db->query("SELECT * FROM `users` WHERE  `email` =  '$email';");

		/*Checking For User Email Exists In Database*/
		if ($query->num_rows !== 0) {

			/*Generatin New Password*/
			$newPass = $this->generateCode(8);

			/*Prepare Password To Query and Hash it With Sha256 Algorythm*/
			$newPassDB = hash('sha256', $db->preapreString($newPass));

			/*Preapre Message*/
			$message = "Email: $email \nPassword: $newPass\n\n Best regards, %sitename% administrator.\n";
			echo ($message);

			/*Set Email Headers*/
			$headers = 'From: webmaster@example.com' . "\r\n" .
			'Reply-To: webmaster@example.com' . "\r\n" .
			'X-Mailer: PHP/' . phpversion();

			/*Sending Email*/
			if (mail($email, "New password", $message, $headers)) {

				/*If True, Cach User Data*/
				$data = mysqli_fetch_assoc($query);
				$userId = $data['id'];

				/*Add New Pass to Database*/
				unset($query);
				//$query = $db->query("UPDATE `users` SET `password`='" . $newPassDB . "' WHERE `id` = '" . $query . "';");
				return true;

			} else {
				/*Recovery Error*/
				$this->error['passwordRecoveryError'] = 'Password recovery error. Please, contact to administrator.';
				$_SESSION['error'] = 'Password recovery error. Please, contact to administrator.';
				return false;
			}

		} else {
			/*If User Email Did Not Found*/
			$this->error['passwordRecovery'] = 'User e-mail does not exists!';
			$_SESSION['error'] = 'User e-mail does not exists!';
			return false;
		}
	}

	/*Function Random Code Generating*/
	function generateCode($length) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
		$code = "";
		$clen = strlen($chars) - 1;
		while (strlen($code) < $length) {
			$code .= $chars[mt_rand(0, $clen)];
		}
		return $code;
	}
}
?>