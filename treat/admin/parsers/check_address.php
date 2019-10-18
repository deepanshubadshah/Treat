<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/treat/core/init.php';
  $name = sanitize($_POST['full_name']);
  $mob_no = sanitize($_POST['mob_no']);
  $street = sanitize($_POST['street']);
  $street2 = sanitize($_POST['street2']);
  $city = sanitize($_POST['city']);
  $state = sanitize($_POST['state']);
  $zip_code = sanitize($_POST['zip_code']);
  $country = sanitize('India');
  $errors = array();
  $required = array(
    'full_name' => 'full_name',
    'mob_no'     => 'mob_no',
    'street'    => 'street Address',
    'city'      => 'city',
    'state'     => 'state',
    'zip_code'  => 'zip_code',

  );

  //check if all required fields are filled out
  foreach($required as $re => $d) {
    if(empty($_POST[$re]) || $_POST[$re] == ''){
      $errors[] = ' All fields with an astrisk(*) are required.';
      break;
    }
  }


//check if valid email Address
//if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
  //$errors[] = 'Please enter a valid email.';
//}

  if(!empty($errors)){
    echo (display_errors($errors));
  }
  else{
    echo ('passed');
  }
?>
