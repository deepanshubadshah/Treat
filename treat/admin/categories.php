<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/treat/core/init.php';
if(!is_logged_in()){
  login_error_redirect();
}
//require_once '../helpers/helpers.php';
include 'includes/head.php';
include 'includes/navigation.php';


/*
$sql ="SELECT * FROM categories WHERE parent = 1";
$result= $db->query($sql);*/
$errors = array();
$category = '';
$post_parent = '';

//Delete Category
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
  $delete_id = (int)$_GET['delete'];
  $delete_id = sanitize($delete_id);
  $sql = "SELECT * FROM categories WHERE id = '$delete_id'";
  $result = $db->query($sql);
  $del_category = mysqli_fetch_assoc($result);
  $sqld ="SELECT * FROM categories WHERE parent = 1";
  $resultd= $db->query($sqld);
  while ($parentd = mysqli_fetch_assoc($resultd)):
      $parent_idd = (int)$parentd['id'];
      if($del_category['parent'] == $parent_idd)
      {
        $sql = "DELETE FROM categories WHERE parent = '$delete_id'";
        $db->query($sql);
      }
  endwhile;

  $dsql = "DELETE FROM categories WHERE id= '$delete_id'";
  $db->query($dsql);
  header('Location: categories.php');
}

//Edit category
if(isset($_GET['edit']) && !empty($_GET['edit']))
{
  $edit_id = (int)$_GET['edit'];
  $edit_id = sanitize($edit_id);
  $editsql = "SELECT * FROM categories WHERE id = '$edit_id'";
  $edit_result = $db->query($editsql);
  $edit_category = mysqli_fetch_assoc($edit_result);
}

//process form
if(isset($_POST) && !empty($_POST)){
  $post_parent = sanitize($_POST['parent']);
  $category = sanitize($_POST['category']);
  $sqlform = "SELECT * FROM categories WHERE category = '$category' AND parent = '$post_parent'";
  if(isset($_GET['edit'])){
    $id = $edit_category['id'];
    $sqlform = "SELECT * FROM categories WHERE category = '$category' AND parent = '$post_parent' AND id != '$id'";
  }
  $fresult = $db->query($sqlform);
  $count = mysqli_num_rows($fresult);
  //if categoryb is Empty
  if($category == ''){
    $errors[] .= 'The category cannot be left blank.';
  }
  if($post_parent == ''){
    $errors[] .= 'Parent cannot be left blank.';
  }
  //if already exists in db
  if($count > 0){
    $errors[] .= $category. ' already exists in. Please choose a new category.';
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
    $updatesql = "INSERT INTO categories (category, parent) VALUES ('$category','$post_parent')";
    if(isset($_GET['edit'])){
      $updatesql = "UPDATE categories SET category = '$category', parent= '$post_parent' WHERE id = '$edit_id'";
    }
    $db->query($updatesql);
    header('Location: categories.php');
  }
}
$category_value = '';
$parent_value = 1;
if(isset($_GET['edit'])){
  $category_value = $edit_category['category'];
  $parent_value = $edit_category['parent'];
}
else{
  if(isset($_POST)){
    $category_value= $category;
    $parent_value= $post_parent;
  }
}
 ?>

<h2 class="text-center">Categories</h2><hr>
<div class="row">

  <!--form-->
  <div class="col-md-6">
    <form class="form" action="categories.php<?php echo ((isset($_GET['edit']))?'?edit='.$edit_id:'');?>" method="post">
      <legend><?php echo ((isset($_GET['edit']))?'Edit':'Add A');?> Category</legend>
      <div id="errors"></div>
      <div class="form-group">
        <label for="parent">Parent</label>
          <select class="form-control" name="parent" id="parent">
            <option value="" <?php echo(($parent_value =='')?'selected':'');?>></option>
            <?php
            $sql1 ="SELECT * FROM categories WHERE parent = 1";
            $result1= $db->query($sql1);
            while ($parent = mysqli_fetch_assoc($result1)):
                $parent_id = (int)$parent['id'];
                $sql = "SELECT * FROM categories WHERE parent ='$parent_id'";
                $result = $db->query($sql);?>
            <?php  while($parent = mysqli_fetch_assoc($result)): ?>
                <option value="<?php echo $parent['id'];?>"<?php echo (($parent_value == $parent['id'])?'selected="selected"':'')?>><?php echo $parent['category'];?></option>
              <?php endwhile;?>
            <?php endwhile;?>
          </select>
      </div>
      <div class="form-group">
        <label for="category">Category</lable>
          <input type="text" class="form-control" id="category" name="category" value="<?php echo $category_value ;?>">
      </div>
      <div class="form-group">
        <input type="submit" value="<?php echo ((isset($_GET['edit']))?'Edit':'Add');?> Category" class="btn btn-success">
      </div>
    </form>
  </div>

  <!-- Category table -->
  <div class="col-md-6">
    <table class="table table-bordered">
      <thead>
        <th>Category</th><th>parent</th><th></th>
      </thead>
      <tbody>
        <?php
        $sql ="SELECT * FROM categories WHERE parent = 1";
        $result= $db->query($sql);
        while ($parent = mysqli_fetch_assoc($result)):
            $parent_id = (int)$parent['id'];
            $sql1 = "SELECT * FROM categories WHERE parent ='$parent_id'";
            $cresult = $db->query($sql1);
          ?>
          <tr class="bg-primary">
            <td><?php echo $parent['category'];?></td>
            <td>Parent</td>
            <td><!--
              <a href="#" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-pencil"></span></a>
              <a href="#" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-remove-sign"></span></a>
            --></td>
          </tr>
          <?php
            while ($child = mysqli_fetch_assoc($cresult)):
            $pparent_id = (int)$child['id'];
            $ssql = "SELECT * FROM categories WHERE parent ='$pparent_id'";
            $sresult = $db->query($ssql);
          // while ($schild = mysqli_fetch_assoc($sresult)): ?>
            <tr class="bg-info">
              <td><?php echo $child['category'];?></td>
              <td><?php echo $parent['category'];?></td>
              <td><!--
                <a href="#" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-pencil"></span></a>
                <a href="#" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-remove-sign"></span></a>
              --></td>
            </tr>
            <?php while ($fchild = mysqli_fetch_assoc($sresult)): ?>
              <tr class="bg-dark">
                <td><?php echo $fchild['category'];?></td>
                <td><?php echo $child['category'];?></td>
                <td>
                  <a href="categories.php?edit=<?php echo $fchild['id'];?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-pencil"></span></a>
                  <a href="categories.php?delete=<?php echo $fchild['id'];?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-remove-sign"></span></a>
                </td>
              </tr>
            <?php endwhile;?>
          <?php endwhile; ?>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include 'includes/footer.php';
?>
