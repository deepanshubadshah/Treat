<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/treat/core/init.php';
  if(!is_logged_in()){
    login_error_redirect();
  }

  if((!has_permission())){
    permission_error_redirect('index.php');
  }

  include 'includes/head.php';
  include 'includes/navigation.php';
  //echo $_SESSION['SBperson'];

  $arparentQuery = $db->query("SELECT * FROM areas WHERE parent_area = 0 ORDER BY area");

  if(isset($_GET['delete'])){
    $delete_id = sanitize($_GET['delete']);
    $db->query("UPDATE persons SET deleted_person = 1 WHERE id = '$delete_id'");
    $_SESSION['success_flash']= 'person has been deleted!';
    header('Location: coworkers.php');
  }
  $sh_dbpath='';
  if(isset($_GET['add']) || isset($_GET['edit'])){
    $arparentQuery = $db->query("SELECT * FROM areas WHERE parent_area = 0 ORDER BY area");

    $person_name = ((isset($_POST['person_name']) && $_POST['person_name'] != '')?sanitize($_POST['person_name']):'');
    $adhaarno = ((isset($_POST['adhaarno']) && $_POST['adhaarno'] != '')?sanitize($_POST['adhaarno']):'');
    $mobile_no = ((isset($_POST['mobile_no']) && $_POST['mobile_no'] != '')?sanitize($_POST['mobile_no']):'');
    $username = ((isset($_POST['username']) && $_POST['username'] != '')?sanitize($_POST['username']):'');
    $password = ((isset($_POST['password']) && $_POST['password'] != '')?sanitize($_POST['password']):'');
    $confirm = ((isset($_POST['confirm']) && $_POST['confirm'] != '')?sanitize($_POST['confirm']):'');
    $permissions = ((isset($_POST['permissions']) && $_POST['permissions'])?sanitize($_POST['permissions']):'');
    $street = ((isset($_POST['street']) && $_POST['street'] != '')?sanitize($_POST['street']):'');
    $street2 = ((isset($_POST['street2']) && $_POST['street2'] != '')?sanitize($_POST['street2']):'');
    $parent_area = ((isset($_POST['parent_area']) && $_POST['parent_area'] != '')?sanitize($_POST['parent_area']):'');
    $state = ((isset($_POST['state']) && $_POST['state'] != '')?sanitize($_POST['state']):'');
    $zip_code = ((isset($_POST['zip_code']) && $_POST['zip_code'] != '')?sanitize($_POST['zip_code']):'');
    $country = ((isset($_POST['country']) && $_POST['country'] != '')?sanitize($_POST['country']):'');
    $reg_id = sanitize($person_data['id']);
    $sh_saved_image = '';
    $show = 0;


    if(isset($_GET['edit'])){
      $ed_id = (int)$_GET['edit'];
      $personResults = $db->query("SELECT * FROM persons WHERE id ='$ed_id'");
      $person = mysqli_fetch_assoc($personResults);

      if(isset($_GET['delete_image'])){
        $simgi = (int)$_GET['simgi'] - 1;
        $simages = explode(',',$person['person_img']);
        $simage_url = $_SERVER['DOCUMENT_ROOT'].$simages[$simgi];
        unlink($simage_url);
        unset($simages[$simgi]);
        $simagesString = implode(',',$simages);
        $db->query("UPDATE persons SET person_img = '{$simagesString}' WHERE id = '$ed_id'");
        header('Location: coworkers.php?edit='.$ed_id);
      }

      $person_name = ((isset($_POST['person_name']) && $_POST['person_name'] != '')?sanitize($_POST['person_name']):$person['person_name']);
      $adhaarno = ((isset($_POST['adhaarno']) && $_POST['adhaarno'] != '')?sanitize($_POST['adhaarno']):$person['adhaarno']);
      $mobile_no = ((isset($_POST['mobile_no']) && $_POST['mobile_no'] != '')?sanitize($_POST['mobile_no']):$person['mobile_no']);
      $username = ((isset($_POST['username']) && $_POST['username'] != '')?sanitize($_POST['username']):$person['username']);
      $permissions = ((isset($_POST['permissions']) && $_POST['permissions'])?sanitize($_POST['permissions']):$person['permissions']);
      $street = ((isset($_POST['street']) && $_POST['street'] != '')?sanitize($_POST['street']):$person['street']);
      $street2 = ((isset($_POST['street2']) && $_POST['street2'] != '')?sanitize($_POST['street2']):$person['street2']);
      $parent_area = ((isset($_POST['parent_area']) && $_POST['parent_area'] != '')?sanitize($_POST['parent_area']):$person['parent_area']);
      $state = ((isset($_POST['state']) && $_POST['state'] != '')?sanitize($_POST['state']):$person['state']);
      $zip_code = ((isset($_POST['zip_code']) && $_POST['zip_code'] != '')?sanitize($_POST['zip_code']):$person['zip_code']);
      $country = ((isset($_POST['country']) && $_POST['country'] != '')?sanitize($_POST['country']):$person['country']);
      $sh_saved_image = (($person['person_img'] != '')?$person['person_img']:'');
      $reg_id = sanitize($person_data['id']);
      $sh_dbpath = $sh_saved_image;

      $show = 1;
    }


    if($_POST){
      $sh_dbpath='';

      $errors = array();
      $usernameQuery = $db->query("SELECT * FROM persons WHERE username = '$username'");
      $usernameCount = mysqli_num_rows($usernameQuery);
      $mobQuery = $db->query("SELECT * FROM persons WHERE mobile_no = '$mobile_no'");
      $mobCount = mysqli_num_rows($mobQuery);
      $sh_uploadPath = array();
      $sh_uploadName = array();
      $sh_tmpLoc = array();

      $allowed = array('png','jpg','jpeg','gif');
      $sh_photoCount = count($_FILES['person_img']['name']);

      if(empty($_FILES)) {
        $sh_dbpath = $person['person_img'];
      }
      if($sh_photoCount > 0) {
        for($j = 0;$j<$sh_photoCount;$j++){
          $name = $_FILES['person_img']['name'][$j];
          $fileExt=sanitize('');
          $mimeExt=sanitize('');
          /*$photo = $_FILES['photo'];
          $name = $photo['name'];*/
          $nameArray = explode('.',$name);
          $fileName = $nameArray[0];
          if($nameArray[0]!=''){
          $fileExt = sanitize($nameArray[1]);
          }
          $mime = explode('/',$_FILES['person_img']['type'][$j]);
          $mimeType = $mime[0];
          if($mime[0]!=''){
          $mimeExt = sanitize($mime[1]);
          }

          $sh_tmpLoc[] = $_FILES['person_img']['tmp_name'][$j];
          $fileSize = $_FILES['person_img']['size'][$j];
          $sh_uploadName = md5(microtime().$j).'.'.$fileExt;
          $sh_uploadPath[] = BASEURL.'images/sz/prod/'.$sh_uploadName;
          if($j != 0 && $j < $sh_photoCount){
            $sh_dbpath .= ',';
          }
          $sh_dbpath .= '/treat/images/sz/prod/'.$sh_uploadName;
          if ($mimeType != 'image') {
            $errors[] = 'The file must be an image.';
          }
          if(!in_array($fileExt, $allowed)){
            $errors[] = 'The file extension must be a png, jpg, jpeg or gif.';
          }
          if ($fileSize > 15000000){
            $errors[] = 'The file size must be under 15MB.';
          }
          if($fileExt != $mimeExt && ($mimeExt == 'jpeg' && $fileExt != 'jpg')){
            $errors[] = 'File extension does not match the file. ' ;
          }
      }
    }

    /*  if($email != '' && $emailCount != 0){
        $errors[] = 'That email already exist in our database.';
      }*/

      if(isset($_GET['add'])){
        $required = array('person_name','adhaarno','username','password','confirm','mobile_no','permissions','parent_area');
      }
      else{
        $required = array('person_name','adhaarno','username','mobile_no','permissions','parent_area');
      }
      foreach($required as $f){
        if(empty($_POST[$f]) ){
          echo($f);
          $errors[] = 'All fields with an astrisk(*) are required.';
          break;
        }
      }

      if((strlen($mobile_no) != 10)){
        $errors[] = 'Your mobile number must be of 10 digits.';
      }

      if((strlen($adhaarno) != 12)){
        $errors[] = 'Adhaar no. number must be of 12 digits.';
      }
      if(isset($_GET['add'])){
        if(strlen($password) < 6){
          $errors[] = 'Your password must be at least 6 characters long.';
        }

        if($password != $confirm){
          $errors[] = 'Your passwords do not match.';
        }
      }

    /*  if(($username != '') && (!filter_var($username,FILTER_VALIDATE_EMAIL))){
        $errors[] = 'You must enter a valid email.';
      }  */

      if(!empty($errors)){
        echo display_errors($errors);
      }
      else{
        //upload person photo
        if($sh_photoCount > 0){
          for($j=0;$j<$sh_photoCount;$j++){
              move_uploaded_file($sh_tmpLoc[$j],$sh_uploadPath[$j]);
              //echo('reaching');
          }}
        // add person to database
        if(isset($_GET['add'])){
          $hashed = password_hash($password,PASSWORD_DEFAULT);
        }
        $insSql = "INSERT INTO persons (`person_name`,`adhaarno`,`mobile_no`,`username`,`password`,`permissions`,`street`,`street2`,`parent_area`,`state`,`zip_code`,`country`,`person_img`,`coworker_id`)
        VALUES ('$person_name','$adhaarno','$mobile_no','$username','$hashed','$permissions','$street','$street2','$parent_area','$state','$zip_code','$country','$sh_dbpath','$reg_id')";
        if(isset($_GET['edit'])){
        $insSql = "UPDATE persons SET `person_name` = '$person_name',`adhaarno` = '$adhaarno', `mobile_no` = '$mobile_no', `username` = '$username',
         `permissions` = '$permissions', `street` = '$street', `street2` = '$street2', `parent_area` = '$parent_area', `state` = '$state', `zip_code` = '$zip_code', `country` = '$country',`person_img` = '$sh_dbpath',
         `coworker_id` = '$reg_id'  WHERE id = $ed_id";
        }
        $db->query($insSql);
        $_SESSION['success_flash'] = 'person has been added!';
        header('Location: coworkers.php');
      }
    }

    ?>
    <div>
    <h2 class="text-center"><?php echo((isset($_GET['edit']))?'Edit':'Add A New');?> coworker</h2><hr>
    <form action="coworkers.php?<?php echo((isset($_GET['edit']))?'edit='.$ed_id:'add=1');?>" method="post" enctype="multipart/form-data">
      <div class="form-group col-md-6">
        <label for="person_name">person's Name*:</label>
        <input type="text" name="person_name" id="name" class="form-control" value="<?php echo($person_name);?>">
      </div>
      <div class="form-group col-md-6">
        <label for="adhaarno">Adhaar no.*:</label>
        <input type="text" name="adhaarno" id="adhaarno" class="form-control" value="<?php echo($adhaarno);?>">
      </div>
      <div class="form-group col-md-6">
        <label for="mobile_no">Mobile no*:</label>
        <input class="form-control" id="mobile_no" name="mobile_no" type="text" value="<?php echo($mobile_no);?>">
      </div>
      <div class="form-group col-md-6">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" class="form-control" value="<?php echo($username);?>">
      </div>
      <?php if(isset($_GET['add'])):?>
      <div class="form-group col-md-6">
        <label for="password">Password*:</label>
        <input type="password" name="password" id="password" class="form-control" value="<?php echo($password);?>">
      </div>
      <div class="form-group col-md-6">
        <label for="confirm">Confirm Password*:</label>
        <input type="password" name="confirm" id="confirm" class="form-control" value="<?php echo($confirm);?>">
      </div>
    <?php endif;?>
    <div class="form-group col-md-6">
      <label for="name">Permissions*:</label>
      <select class="form-control" name="permissions">
        <option value=""<?php echo(($permissions =='')?' selected':'');?>></option>
        <?php if(has_permission()):?>
          <option value="editor"<?php echo(($permissions =='editor')?' selected':'');?>>Editor</option>
          <option value="admin,editor"<?php echo(($permissions =='admin,editor')?' selected':'');?>>Admin</option>
        <?php endif;?>
      </select>
    </div>
      <div class="form-group col-md-6">
        <label for="street">Street Address:</label>
        <input class="form-control" id="street" name="street" type="text" data-stripe="address_line1" value="<?php echo($street);?>">
      </div>
      <div class="form-group col-md-6">
        <label for="street2">Street Address 2:</label>
        <input class="form-control" id="street2" name="street2" type="text" data-stripe="address_line2" value="<?php echo($street2);?>">
      </div>
      <div class="form-group col-md-3">
        <label for="parent_area">City*:</label>
        <select class="form-control" id="parent_area" name="parent_area">
          <option value=""<?php echo(($parent_area == '')?' selected':'');?>></option>
          <?php while($pa = mysqli_fetch_assoc($arparentQuery)): ?>
            <option value="<?php echo $pa['id'];?>"<?php echo(($parent_area == $pa['id'])?' selected':'');?>><?php echo $pa['area'];?></option>
          <?php endwhile;?>
        </select>
      </div>

      <div class="form-group col-md-3">
        <label for="state">State:</label>
        <input class="form-control" id="state" name="state" type="text" value="<?php echo($state);?>">
      </div>
      <div class="form-group col-md-3">
        <label for="zip_code">Zip Code:</label>
        <input class="form-control" id="zip_code" name="zip_code" type="text" data-stripe="address_zip" value="<?php echo($zip_code);?>">
      </div>
      <div class="form-group col-md-3">
        <label for="country">Country:</label>
        <input class="form-control" id="country" name="country" type="text" data-stripe="address_country" value="<?php echo($country);?>">
      </div>

      <div class="form-group col-md-6">
        <?php if($sh_saved_image != ''): ?>
          <?php
            $simgi = 1;
            $simages = explode(',',$sh_saved_image);
          ?>
          <?php foreach ($simages as $simage):?>
          <div class="sh_saved-image col-md-4">
            <img src="<?php echo($simage);?>" alt="sh_saved-image" class="img-thumb"/><br>
            <a href="coworkers.php?delete_image=1&edit=<?php echo($ed_id);?>&simgi=<?php echo($simgi);?>" class="text-danger">Delete Image</a>
          </div>
          <?php $simgi++;endforeach;?>
        <?php else:?>
          <label for="person_img">persone Photo:</label>
          <input type="file" name="person_img[]" id="person_img" class="form-control" multiple>
        <?php endif;?>
      </div>
      <div class="form-group col-md-6 text-right pull-right " style="margin-top:25px;">
        <a href="coworkers.php" class="btn btn-default">Cancel</a>
        <input type="submit" value="<?php echo((isset($_GET['edit']))?'Edit ':'Add ');?>person" class="btn btn-primary">
      </div><div class="clearfix"></div>
    </form>


    <?php
  }

  else{
  $personQuery = $db->query("SELECT * FROM persons WHERE deleted_person = 0 AND (permissions = 'admin,editor' OR permissions = 'editor') ORDER BY person_name");
  $i=0;
?>
<div>
  <div>
<h2 class="text-center">Coworkers</h2>
</div>
<a href="coworkers.php?add=1" class="btn btn-success pull-right" id="add-product-btn">Add New coworker</a>
<hr>
<table class="table table-bordered table-striped table-condensed">
  <thead><th></th><th>Name</th><th>Contact</th><th>Username</th><th>Join Date</th><th>Last Login</th><th>Permissions</th><th></th><th></th></thead>
  <tbody>
    <?php while($person = mysqli_fetch_assoc($personQuery)): ?>
      <?php if($person['id'] != 18): $i=$i+1;?>
      <tr>
        <td><?php echo($i);?></td>
        <td><?php echo($person['person_name']);?></td>
        <td><?php echo($person['mobile_no']);?></td>
        <td><?php echo($person['username']);?></td>
        <td><?php echo(pretty_date($person['join_date']));?></td>
        <td><?php echo(($person['last_login'] == '0000-00-00 00:00:00')?'Never':pretty_date($person['last_login']));?></td>
        <td><?php echo($person['permissions']);?></td>
        <td><a href="sal_iner.php?person_id=<?php echo($person['id']);?>" class="btn btn-xs btn-info">Salary</a></td>
        <td>
          <?php if(($person['id'] != $person_data['id']) || ($person['id'] != 18)):?>
            <a href="coworkers.php?delete=<?php echo($person['id']);?>" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-remove-sign"></span></a>
            <a href="coworkers.php?edit=<?php echo($person['id']);?>" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-pencil"></span></a>
          <?php endif;?>
        </td>
      </tr>
      <?php endif;?>
    <?php endwhile;?>
  </tbody>
</table>
</div>
<?php } include 'includes/footer.php';
?>

<script>
  jQuery('document').ready(function(){
    get_ar_child_options('<?php echo($area);?>');
  });

</script>
