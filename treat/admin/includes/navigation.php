<?php
$sql = "SELECT * FROM categories WHERE parent = '0'";
$pquery = $db->query($sql);
?>

<nav class="navbar navbar-default navbar-fixed-top">
  <div class="container">
    <div class="navbar-header">
      <a href="/treat/admin/index.php" class="navbar-brand">Treat Admin</a>
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
    </div>
    <div class="navbar-collapse collapse">
        <ul class="nav navbar-nav">
          <!-- Menu Items -->
          <li><a href="../index.php">Billing</a></li>
          <li><a href="pro_cat.php">Categories</a></li>
          <?php if(has_permission('admin')): ?>
          <li><a href="expenses.php">Expenses</a></li>
          <?php endif; ?>
          <li><a href="products.php">Products</a></li>
          <li><a href="inventory.php">Inventory</a></li>
          <li><a href="archive.php">Archived</a></li>
          <?php if(has_permission('admin')): ?>
          <li><a href="city_area.php">Areas</a></li>
          <li><a href="coworkers.php">Coworkers</a></li>
          <?php endif; ?>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">Hello <?php echo $person_data['first'];?>!
            <span class="caret"></span>
          </a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="change_password.php">Change Password</a></li>
            <li><a href="logout.php">Log Out</a></li>
          </ul>
        </li>
      </ul>
    </div>
          <!--<li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $parent['category']; ?><span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="#"></a></li>
              </ul>
          </li>-->
  </div>
</nav>
