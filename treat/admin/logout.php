<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/treat/core/init.php';
unset($_SESSION['SBperson']);
header('Location: login.php');
?>
