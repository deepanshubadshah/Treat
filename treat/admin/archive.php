<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/treat/core/init.php';
if(!is_logged_in()){
  login_error_redirect();
}
include 'includes/head.php';
include 'includes/navigation.php';

//Delete products
if(isset($_GET['restore'])){
  $id = sanitize($_GET['restore']);
  $db->query("UPDATE Products SET `deleted` = 0, `featured` = 0 WHERE id = '$id'");
  header('Location: archive.php');
}
?>

<?php
$dsql = "SELECT * FROM Products WHERE deleted = 1";
$dpresults = $db->query($dsql);
?>

<h2 class="text-center">Archived Products</h2>
<hr>
<table class="table table-bordered table-condensed table-striped">
  <thead class="text-center"><th>Restore</th><th>Price</th><th>Category</th><th>Sold</th></thead>
  <tbody>
    <?php while($product = mysqli_fetch_assoc($dpresults)):
       ?>
      <tr>
        <td>
          <a href="archive.php?restore=<?php echo $product['id'];?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-refresh"></span></a>
        </td>
        <td><?php echo $product['title'];?></td>
        <td><?php echo money($product['price']);?></td>
        <td>0</td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

<?php  include 'includes/footer.php'; ?>
