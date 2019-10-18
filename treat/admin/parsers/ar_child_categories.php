<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/treat/core/init.php';

$area_parentID = (int)$_POST['area_parentID'];
$selected = sanitize($_POST['selected']);

if($area_parentID != ''){
$ar_childQuery = $db->query("SELECT * FROM areas WHERE parent_area = '$area_parentID' ORDER BY area");
ob_start();
?>
    <option value=""></option>
  <?php while($child_area = mysqli_fetch_assoc($ar_childQuery)): ?>
    <option value="<?php echo $child_area['id'];?>"<?php echo(($selected == $child_area['id'])?' selected':'');?>><?php echo $child_area['area'];?></option>
  <?php endwhile; ?>
<?php echo ob_get_clean();}?>
