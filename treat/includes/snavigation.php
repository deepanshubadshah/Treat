<?php
$fsql = "SELECT * FROM categories WHERE parent = 1";
$fpquery = $db->query($fsql);
while($parent = mysqli_fetch_assoc($pquery)) :
endwhile;

$spa = "        ";
?>

<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
  <div class="container">
    <div class="navbar-header">
      <a href="index.php" class="navbar-brand">Treat</a>
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
    </div>
    <div class="navbar-collapse collapse">
      <ul class="nav navbar-nav ">
        <ul class="nav navbar-nav ">
          <?php while($parent = mysqli_fetch_assoc($pquery)) : ?>
          <?php $parent_id = $parent['id'];
          $sql2 = "SELECT * FROM categories WHERE parent = '$parent_id'";
          $cquery = $db->query($sql2);
          ?>
            <!-- Menu Items -->
          <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo$parent['category']; ?><span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <?php while($child = mysqli_fetch_assoc($cquery)) : ?>
                <li><a href="category.php?cat=<?php echo($child['id']);?>"><?php echo $child['category']; ?></a></li>
                <?php endwhile; ?>
              </ul>
          </li>
        <?php endwhile; ?>
      </ul>
        <li><span style="display:inline-block; width: 240px;"></span></li>
        <li><a href="cart.php" class="cartimg justify-content-end"><span><img src="/treat/images/sz/pic/shcart.png"></span> My Cart</a></li>
      </ul>
    </div>
  </div>
</nav>
