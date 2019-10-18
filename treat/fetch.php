<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/treat/core/init.php';
  if(!is_logged_in()){
    login_error_redirect();
  }

  if((!has_permission()) && (!has_permission_editor())){
    permission_error_redirect('index.php');
  }

$output = '';
?>
<style>
#search {
    width: 30em;  height: 2em;
}
</style>
<?php
if(isset($_POST["query"]))
{
 $search = mysqli_real_escape_string($db, $_POST["query"]);
 $query = "
  SELECT * FROM Products
  WHERE title LIKE '%".$search."%'
 ";
}
else
{
 $query = "
  SELECT * FROM Products ORDER BY title
 ";
}
$result = mysqli_query($db, $query);
if(mysqli_num_rows($result) > 0)
{
 $output .= '
  <div>
  <ul>
 ';
 while($row = mysqli_fetch_array($result))
 {
  $output .= '
  <button type="button" id="search" class="btn btn-lg btn-default" style="padding: 5px 460px;" onclick="add_to_cart('.$row['id'].',1);">'.$row["title"].'<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>Rs '.$row["price"].'<span>&nbsp;&nbsp;&nbsp;&nbsp;</span></button>
  ';
 }
 echo $output;
}
else
{
 echo 'Data Not Found';
}

?>
<?php  include 'includes/footer.php'; ?>
