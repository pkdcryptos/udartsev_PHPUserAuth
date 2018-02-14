# PHPUserAuth

Created by Vladimir S. Udartsev [http://udartsev.ru]
Used pure PHP and HTML. Tested on apache2 Ubuntu server.

- PHP 7.2
- MySQL 5.7
- Apache 2.4

**Includes**
- login / register / restore functions
- verification via email
- password recovery function via email
- simple SQL injection protection
- PHP Sessions function
- sha256 password protection algorithm

**Instructions**
1) clone files into your public directory
2) add your MySQL connection settings in */config/dbconf.php*
3) remove */createschema/.htaccess* file
4) run */createschema/.mysql.php* to create new tables to database
5) add */createschema/.htaccess* file (just copy from another folder)
6) run *yournomain.com*

Well done!

DEMO [http://phpuserauth.udartsev.ru]
