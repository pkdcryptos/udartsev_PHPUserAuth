<?php
### Vladimir S. Udartsev
### udartsev.ru

include_once dirname(__FILE__) . '/../config/dbconf.php';
include_once dirname(__FILE__) . '/../class/sqlconn.php';

$db = new sqlconn($mysqlSettings);

/*DROP TABLES*/
$query = $db->query("DROP TABLE IF EXISTS `users`");
if (isset($db->error)) {
	echo "<pre>";
	var_dump($db->error);} else {echo "Database `users` DROPPED!<br>";}

/*CREATE TABLES*/
$query = $db->query("CREATE TABLE IF NOT EXISTS `users` (
	`id` INT(5) NOT NULL AUTO_INCREMENT PRIMARY KEY,
 	`firstname` VARCHAR(255) NULL,
 	`lastname` VARCHAR(255) NULL,
 	`email` VARCHAR(255) NOT NULL,
 	`password` VARCHAR(255) NOT NULL,
 	`activationCode` VARCHAR(255) NOT NULL UNIQUE,
 	`status` enum('0','1') NOT NULL DEFAULT '0'
 	)");

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

/*CREATE DATA*/
$query = $db->query("INSERT INTO `users` (`firstname`, `lastname`, `email`,	`password`,	`activationCode`, `status`)
	VALUES ('Demo', 'User', 'demo@demo.com', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', '1')");

if (isset($db->error)) {
	echo "<pre>";
	var_dump($db->error);} else {echo "`Demo User` data created successfull! DONE<br>";}

$db->close();
unset($db);