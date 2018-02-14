<?php
### Vladimir S. Udartsev
### udartsev.ru

include_once dirname(__FILE__) . '/../config/dbconf.php';
include_once dirname(__FILE__) . '/../class/sqlconn.php';

$db = new sqlconn($mysqlSettings);
$query = $db->query("CREATE TABLE IF NOT EXISTS `users` (
	`id` INT(5) NOT NULL AUTO_INCREMENT PRIMARY KEY,
 	`firstname` VARCHAR(255) NULL,
 	`lastname` VARCHAR(255) NULL,
 	`email` VARCHAR(255) NOT NULL,
 	`password` VARCHAR(255) NOT NULL)");

if (isset($db->error)) {
	echo "<pre>";
	var_dump($db->error);} else {echo "Database `users` created successfull!. DONE<br>";}

$query = $db->query("CREATE TABLE IF NOT EXISTS `users_sessions` (
	`id` INT(5) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`user_id` INT(11) NULL,
 	`session_code` VARCHAR(15) NULL,
 	`session_user_agent` VARCHAR(255) NULL)");

if (isset($db->error)) {
	echo "<pre>";
	var_dump($db->error);} else {echo "Database `users_sessions` created successfull!. DONE<br>";}

$db->close();
unset($db);