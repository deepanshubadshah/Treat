<?php
$db = mysqli_connect('127.0.0.1','root','','treat');
if (mysqli_connect_errno()) {
  echo 'Database connection failed with following errors: '. mysqli_connect_error();
  die();
}
session_start();
require_once $_SERVER['DOCUMENT_ROOT'].'/treat/config.php';
require_once BASEURL.'helpers/helpers.php';
//define('BASEURL', '/treat/');

$cart_id = '';
if(isset($_COOKIE[CART_COOKIE])){
  $cart_id = sanitize($_COOKIE[CART_COOKIE]);
}

if(isset($_SESSION['SBperson'])){
  $person_id = $_SESSION['SBperson'];
  $query = $db->query("SELECT * FROM persons WHERE id = '$person_id'");
  $person_data = mysqli_fetch_assoc($query);
  $fn = explode(' ', $person_data['person_name']);
  $person_data['first'] = $fn[0];
  if(count($fn) > 2){
  $person_data['last'] = $fn[1];
}
}

if(isset($_SESSION['success_flash'])){
  echo '<div class= "bg-success"><p class="text-success text-center">'.$_SESSION['success_flash'].'</p></div>';
  unset($_SESSION['success_flash']);
}

if(isset($_SESSION['error_flash'])){
  echo '<div class= "bg-danger"><p class="text-danger text-center">'.$_SESSION['error_flash'].'</p></div>';
  unset($_SESSION['error_flash']);
}



?>
