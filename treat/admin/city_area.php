<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/treat/core/init.php';
if(!is_logged_in()){
  login_error_redirect();
}
//require_once '../helpers/helpers.php';
include 'includes/head.php';
include 'includes/navigation.php';

$sql ="SELECT * FROM areas WHERE parent_area = 0";
$result= $db->query($sql);
$errors = array();
$area = '';
$post_parent_area = '';

//Delete area
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
  $delete_id = (int)$_GET['delete'];
  $delete_id = sanitize($delete_id);
  $sql = "SELECT * FROM areas WHERE id = '$delete_id'";
  $result = $db->query($sql);
  $del_area = mysqli_fetch_assoc($result);
  if($del_area['parent_area'] == 0)
  {
    $sql = "DELETE FROM areas WHERE parent_area = '$delete_id'";
    $db->query($sql);
  }
  $dsql = "DELETE FROM areas WHERE id= '$delete_id'";
  $db->query($dsql);
  header('Location: city_area.php');
}

//Edit area
if(isset($_GET['edit']) && !empty($_GET['edit']))
{
  $edit_id = (int)$_GET['edit'];
  $edit_id = sanitize($edit_id);
  $editsql = "SELECT * FROM areas WHERE id = '$edit_id'";
  $edit_result = $db->query($editsql);
  $edit_area = mysqli_fetch_assoc($edit_result);
}

//process form
if(isset($_POST) && !empty($_POST)){
  $post_parent_area = sanitize($_POST['parent_area']);
  $area = sanitize($_POST['area']);
  $sqlform = "SELECT * FROM areas WHERE area = '$area' AND parent_area = '$post_parent_area'";
  if(isset($_GET['edit'])){
    $id = $edit_area['id'];
    $sqlform = "SELECT * FROM areas WHERE area = '$area' AND parent_area = '$post_parent_area' AND id != '$id'";
  }
  $fresult = $db->query($sqlform);
  $count = mysqli_num_rows($fresult);
  //if area is Empty
  if($area == ''){
    $errors[] .= 'The area cannot be left blank.';
  }
  //if already exists in db
  if($count > 0){
    $errors[] .= $area. ' already exists in. Please enter a new area.';
  }

//Display Errors or update Database
  if(!empty($errors)){
    //display Errors
    $display = display_errors($errors); ?>
    <script>
      jQuery('document').ready(function(){
        jQuery('#errors').html('<?php echo $display; ?>');
      });
    </script>

  <?php }
  else{
    //update database
    $updatesql = "INSERT INTO areas (area, parent_area) VALUES ('$area','$post_parent_area')";
    if(isset($_GET['edit'])){
      $updatesql = "UPDATE areas SET area = '$area', parent_area= '$post_parent_area' WHERE id = '$edit_id'";
    }
    $db->query($updatesql);
    header('Location: city_area.php');
  }
}
$area_value = '';
$parent_area_value = 0;
if(isset($_GET['edit'])){
  $area_value = $edit_area['area'];
  $parent_area_value = $edit_area['parent_area'];
}
else{
  if(isset($_POST)){
    $area_value= $area;
    $parent_area_value= $post_parent_area;
  }
}
 ?>

<h2 class="text-center">City Areas</h2><hr>
<div class="row">

  <!--form-->
  <div class="col-md-6">
    <form class="form" action="city_area.php<?php echo ((isset($_GET['edit']))?'?edit='.$edit_id:'');?>" method="post">
      <legend><?php echo ((isset($_GET['edit']))?'Edit':'Add An');?> Area</legend>
      <div id="errors"></div>
      <div class="form-group">
        <label for="parent_area">Parent Area</label>
          <select class="form-control" name="parent_area" id="parent_area">
            <option value="0"<?php echo (($parent_area_value == 0)? 'selected="selected"':'');?>>Parent Area</option>
            <?php while($parent_area = mysqli_fetch_assoc($result)): ?>
              <option value="<?php echo $parent_area['id'];?>"<?php echo (($parent_area_value == $parent_area['id'])?'selected="selected"':'')?>><?php echo $parent_area['area'];?></option>
            <?php endwhile;?>
          </select>
      </div>
      <div class="form-group">
        <label for="area">Area</lable>
          <input type="text" class="form-control" id="area" name="area" value="<?php echo $area_value ;?>">
      </div>
      <div class="form-group">
        <input type="submit" value="<?php echo ((isset($_GET['edit']))?'Edit':'Add');?> Area" class="btn btn-success">
      </div>
    </form>
  </div>

  <!-- Area table -->
  <div class="col-md-6">
    <table class="table table-bordered">
      <thead>
        <th>Area</th><th>Parent Area</th><th></th>
      </thead>
      <tbody>
        <?php
        $sql ="SELECT * FROM areas WHERE parent_area = 0";
        $result= $db->query($sql);
        while ($parent_area = mysqli_fetch_assoc($result)):
            $parent_area_id = (int)$parent_area['id'];
            $sql1 = "SELECT * FROM areas WHERE parent_area ='$parent_area_id'";
            $cresult = $db->query($sql1);
          ?>
          <tr class="bg-primary">
            <td><?php echo $parent_area['area'];?></td>
            <td>Parent Area</td>
            <td>
              <a href="city_area.php?edit=<?php echo $parent_area['id'];?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-pencil"></span></a>
              <a href="city_area.php?delete=<?php echo $parent_area['id'];?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-remove-sign"></span></a>
            </td>
          </tr>
          <?php while ($child = mysqli_fetch_assoc($cresult)): ?>
            <tr class="bg-info">
              <td><?php echo $child['area'];?></td>
              <td><?php echo $parent_area['area'];?></td>
              <td>
                <a href="city_area.php?edit=<?php echo $child['id'];?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-pencil"></span></a>
                <a href="city_area.php?delete=<?php echo $child['id'];?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-remove-sign"></span></a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include 'includes/footer.php';
?>
