<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/treat/core/init.php';

$parentID = (int)$_POST['parentID'];
$selected = sanitize($_POST['selected']);

$childQuery = $db->query("SELECT * FROM categories WHERE parent = '$parentID' ORDER BY category");
ob_start();
?>
    <option value=""></option>
  <?php while($child = mysqli_fetch_assoc($childQuery)): ?>
    <option value="<?php echo $child['id'];?>"<?php echo(($selected == $child['id'])?' selected':'');?>><?php echo $child['category'];?></option>
  <?php endwhile; ?>
<?php echo ob_get_clean();?>
