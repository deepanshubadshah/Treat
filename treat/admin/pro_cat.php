<?php
  require_once '../core/init.php';
  if(!is_logged_in()){
    login_error_redirect();
  }
  require_once '../helpers/helpers.php';
  include 'includes/head.php';
  include 'includes/navigation.php';
  //get procat from database
  $sql = "SELECT * FROM pro_cat ORDER BY category";
  $results = $db->query($sql);
  $errors = array();

//Edit category
if(isset($_GET['edit']) && !empty($_GET['edit'])){
  $edit_id = (int)$_GET['edit'];
  $edit_id = sanitize($edit_id);
  //echo $delete_id;
  $sql1 = "SELECT * FROM pro_cat WHERE id = '$edit_id'";
  $edit_result = $db->query($sql1);
  $ecategory = mysqli_fetch_assoc($edit_result);
  //header('Location: pro_cat.php');
}

//delete pro_cat
if(isset($_GET['delete']) && !empty($_GET['delete'])){
  $delete_id = (int)$_GET['delete'];
  $delete_id = sanitize($delete_id);
  //echo $delete_id;
  $sql = "DELETE FROM pro_cat WHERE id = '$delete_id'";
  $db->query($sql);
  header('Location: pro_cat.php');
}


  //If add form is submitted
  if (isset($_POST['add_submit'])) {

    $category = sanitize( $_POST['category']);
    //check if category is blank
    if($_POST['category'] == ''){
    $errors[] .= 'Please enter a category! ';
  }
  //check if category exists in database
  $sql = "SELECT * FROM pro_cat WHERE category = '$category'";
  if(isset($_GET['edit'])){
    $sql = "SELECT * FROM pro_cat WHERE category = '$category' AND id !='$edit_id'";
  }
  $result = $db->query($sql);
  $count = mysqli_num_rows($result);
  if($count > 0){
    $errors[] .= $category.' already exists. please Choose another category name...';
  }

  //display errors
  if(!empty($errors)){
    echo display_errors($errors);
  }
  else{
    //Add category to database
    $sql = "INSERT INTO pro_cat (category) VALUES ('$category')";
    if(isset($_GET['edit'])){
      $sql = "UPDATE pro_cat SET category ='$category' WHERE id = '$edit_id'";
    }
    $db->query($sql);
    header('Location: pro_cat.php');
  }
  }
?>
<h2 class="text-center">Category</h2><hr>
<!--category form-->
<div class="text-center">
  <form class="form-inline" action="pro_cat.php?<?php echo ((isset($_GET['edit']))?'edit='.$edit_id:'');?>" method="post">
    <div class="form-group">
      <?php
      $category_value='';
      if(isset($_GET['edit'])){
        $category_value = $ecategory['category'];
      }
      else{
        if(isset($_POST['category'])){
          $category_value = sanitize($_POST['category']);
        }
      }
      ?>
      <label for="category"><?php echo ((isset($_GET['edit']))?'Edit ':'Add a '); ?>Category: </label>
      <input type="text" name="category" id='category' class="form-control" value="<?php echo $category_value;?>">
      <?php if(isset($_GET['edit'])): ?>
        <a href="pro_cat.php" class="btn btn-default">Cancel</a>
      <?php endif; ?>
      <input type="submit" name="add_submit" value="<?php echo((isset($_GET['edit']))?'Edit ':'Add ')?>Category" class="btn btn-success">
    </div>
  </form>
</div>
<hr>

<table class="table table-bordered table-striped table-auto">
  <thead>
    <th></th><th>Categories</th><th></th>
  </thead>
  <tbody>
    <?php while($category = mysqli_fetch_assoc($results)): ?>
    <tr>
      <td><a href="pro_cat.php?edit=<?php echo $category['id'];?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-pencil"></span></a></td>
      <td><?php echo $category['category']; ?></td>
      <td><a href="pro_cat.php?delete=<?php echo $category['id'];?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-remove-sign"></span></a></td>
    </tr>
  <?php endwhile; ?>
  </tbody>
</table>
<?php include 'includes/footer.php';?>
