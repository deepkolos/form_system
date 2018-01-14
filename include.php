<?php
// session_start();
define("ROOT",dirname(__FILE__));
set_include_path('.'.PATH_SEPARATOR.ROOT.'/core'.PATH_SEPARATOR.get_include_path());
require_once("config.php");
require_once("sql.func.php");
Connect();
require_once("string.func.php");
require_once("utility.php");
require_once("log.php");
require_once("user.php");//一定要先require user.php,因为登陆为基础
$user = new User;
require_once("form.php");
$form = new Form($user);

