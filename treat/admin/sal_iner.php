<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/treat/core/init.php';
if(!is_logged_in()){
  login_error_redirect();
}

if((!has_permission())){
  permission_error_redirect('index.php');
}

$person_id = sanitize((int)$_GET['person_id']);
?>
<form action="salary.php" method="post" id="salform" enctype="multipart/form-data">
  <input type="hidden" name="person_id" value="<?php echo($person_id);?>">
  <div class="form-group">
  <input type="submit" value="Proceed" class="btn btn-primary">
  </div>
</form>

<script type="text/javascript">
    document.getElementById('salform').submit(); // SUBMIT FORM
</script>
