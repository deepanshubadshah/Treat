 <?php
  require_once '../core/init.php';
  if(!is_logged_in()){
    header('Location: login.php');
  }
  if((!has_permission())){
    permission_error_redirect('../index.php');
  }

  include 'includes/head.php';
  include 'includes/navigation.php';
  //echo $_SESSION['SBperson'];

//  session_destroy();
$date = date('Y-m-d', time());
$distri = "Today's";

$exQuery ="SELECT * FROM expenses WHERE DATE(ex_date) = '$date' ORDER BY ex_date";

$txnQuery ="SELECT t.id, t.cart_id,  t.description, t.txn_date, t.grand_total, t.tax,t.payment_type, c.items
  FROM Orders t
  LEFT JOIN cart c ON t.cart_id = c.id
  WHERE DATE(t.txn_date) = '$date'
  ORDER BY t.txn_date";

if(isset($_GET['pre'])){
  $date = ((isset($_POST['pre_date']) && $_POST['pre_date'] != '')?sanitize($_POST['pre_date']):'');
  $to_date = ((isset($_POST['to_date']) && $_POST['to_date'] != '')?sanitize($_POST['to_date']):'');
  $distri = 'From '.$date.' To '.$to_date;

  if(empty($_POST['pre_date']) || empty($_POST['to_date'])){

    $errors[] = 'Date is required!';

  }

if(!empty($errors)){
  echo display_errors($errors);
}
else{
  $date = ((isset($_POST['pre_date']) && $_POST['pre_date'] != '')?sanitize($_POST['pre_date']):'');
  $to_date = ((isset($_POST['to_date']) && $_POST['to_date'] != '')?sanitize($_POST['to_date']):'');

  $txnQuery ="SELECT t.id, t.cart_id,  t.description, t.txn_date, t.grand_total, t.tax,t.payment_type, c.items
    FROM Orders t
    LEFT JOIN cart c ON t.cart_id = c.id
    WHERE (DATE(t.txn_date) >= '$date' AND DATE(t.txn_date) <= '$to_date')
    ORDER BY t.txn_date";

  $exQuery ="SELECT * FROM expenses WHERE (DATE(ex_date) >= '$date' AND DATE(ex_date) <= '$to_date') ORDER BY ex_date";
}
}
?>

<!-- Order to fill -->
<?php
    $txnResults = $db->query($txnQuery);
    $tot_sale=0;
    $tot_tax=0;
?>
<div class="col-md-12">
  <h3 class="text-center"><?php echo($distri);?> sale</h3>
  <table class="table table-condensed table-bordered table-striped">
    <thead><th>#</th><th>Order</th><th>Description</th><th>Total</th><th>Date</th></thead>
    <tbody>
      <?php $k=1; while($order = mysqli_fetch_assoc($txnResults)):?>
      <tr>
        <td><?php echo($k);?></td>
        <td><a href="orders.php?txn_id=<?php echo($order['id']);?>" class="btn btn-xs btn-info">Details</a></td>

        <td><?php echo($order['description']);?></td>
        <td><?php echo($order['grand_total']);?></td>
        <td><?php echo($order['txn_date']);?></td>
      </tr>
    <?php $tot_sale= $tot_sale+$order['grand_total'];$tot_tax= $tot_tax+$order['tax'];$k++; endwhile;?>
    </tbody>
  </table>
  <div>
    <h4>Total sale: <?php echo($tot_sale);?></h4>
    <h4>Total tax: <?php echo($tot_tax);?></h4>
  </div>
</div>

<?php
    $exResults = $db->query($exQuery);
    $tot_exp=0;
?>
<div class="col-md-12">
  <h3 class="text-center"><?php echo($distri);?> Expenses</h3>
  <table class="table table-condensed table-bordered table-striped">
    <thead><th>#</th><th>Description</th><th>Amount</th><th>Date</th></thead>
    <tbody>
      <?php $k=1; while($expen = mysqli_fetch_assoc($exResults)):?>
      <tr>
        <td><?php echo($k);?></td>
        <?php if($expen['us_reason'] == "Salary"):?>
        <td><?php echo('Salary to '.$expen['person_name']);?></td>
        <?php else:?>
        <td><?php echo($expen['ot_reason']);?></td>
        <?php endif;?>
        <td><?php echo($expen['amount']);?></td>
        <td><?php echo($expen['ex_date']);?></td>
      </tr>
    <?php $tot_exp= $tot_exp+$expen['amount'];$k++; endwhile;?>
    </tbody>
  </table>
  <div>
    <h4>Total Expenditure: <?php echo($tot_exp);?></h4>
  </div>
</div>

<div class="row">
<h3 class="text-center">Search by date</h3><hr>
<form action="index.php?pre=1" method="post" enctype="multipart/form-data">
<div class="form-group col-md-4">
  <label for="pre_date">Enter Date(From)*(YYYY-MM-DD):</label>
  <input type="text" name="pre_date" id="pre_date" class="form-control" placeholder="YYYY-MM-DD">
</div>
<div class="form-group col-md-4">
  <label for="to_date">Enter Date(To)*(YYYY-MM-DD):</label>
  <input type="text" name="to_date" id="to_date" class="form-control" placeholder="YYYY-MM-DD">
</div>

<div class="form-group " style="margin-top:22px;margin-right:30px;padding: 6px 20px;font-size: 15px;">
  <input type="submit" value="Submit" class="btn btn-info" style="margin-top:18px;">
  <div class="clearfix"></div>
</div>
</form>
</div>


<div class="row">
  <!-- SAles By Month -->
  <?php
    $thisYr = date("Y");
    $lastYr = $thisYr - 1;
    $thisYrQ = $db->query("SELECT grand_total, txn_date FROM Orders WHERE YEAR(txn_date) = '{$thisYr}'");
    $lastYrQ = $db->query("SELECT grand_total, txn_date FROM Orders WHERE YEAR(txn_date) = '{$lastYr}'");
    $current = array();
    $last = array();
    $currentTotal =0;
    $lastTotal = 0;
    for($month=1;$month <=12 ;$month++){
      $last[(int)$month] =0;
      $current[(int)$month] =0;
    }
    while($x = mysqli_fetch_assoc($thisYrQ)){
      $month = date("m",strtotime($x['txn_date']));
      $current[(int)$month] =$current[(int)$month] + $x['grand_total'];
      $currentTotal = $currentTotal + $x['grand_total'];
    }
    while($y = mysqli_fetch_assoc($lastYrQ)){
      $month = date("m",strtotime($y['txn_date']));
      $last[(int)$month] += $y['grand_total'];
      $lastTotal += $y['grand_total'];
    }

  ?>
  <div class="col-md-4">
    <h3 class="text-center">Sales By Month</h3>
    <table class="table table-condensed table-bordered table-striped">
      <thead>
        <th></th>
        <th><?php echo($lastYr);?></th>
        <th><?php echo($thisYr);?></th>
      </thead>
      <tbody>
        <?php for($i =1;$i <= 12;$i++):
          $dt = DateTime::createFromFormat('!m',$i);
          ?>
          <tr<?php echo((date("m")==$i)?' class="info"':'');?>>
            <td><?php echo($dt->format("F"));?></td>
            <td><?php echo(array_key_exists($i,$last)?money($last[$i]):money(0));?></td>
            <td><?php echo(array_key_exists($i,$current)?money($current[$i]):money(0));?></td>
          </tr>
        <?php endfor;?>
        <tr>
          <td>Total</td>
          <td><?php echo(money($lastTotal));?></td>
          <td><?php echo(money($currentTotal));?></td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- Inventory -->
  <?php
    $iQuery = $db->query("SELECT * FROM inventory WHERE deleted = 0");
    $lowItems = array();
    while($invent = mysqli_fetch_assoc($iQuery)){
      $item = array();
        if($invent['quantity'] <= $invent['threshold']){

          $item = array(
            'title' => $invent['title'],
            'quantity' => $invent['quantity'],
            'threshold' => $invent['threshold']
          );
          $lowItems[] = $item;
        }
    }
  ?>
  <div class="col-md-8">
    <h3 class="text-center">Low Inventory</h3>
    <table class="table table-condensed table-bordered table-striped">
      <thead>
        <th>Item</th>
        <th>Quantity</th>
        <th>Threshold</th>
      </thead>
      <tbody>
      <?php foreach($lowItems as $item): ?>
        <tr<?php echo(($item['quantity']==0)?' class="danger"':'');?>>
          <td><?php echo($item['title']);?></td>
          <td><?php echo($item['quantity']);?></td>
          <td><?php echo($item['threshold']);?></td>
        </tr>
      <?php endforeach;?>
      </tbody>
    </table>
  </div>
</div>

<div class="row">
  <!-- Low prod -->
  <?php
    $iQuery = $db->query("SELECT * FROM Products WHERE deleted = 0");
    $lowprod = array();
    while($prod = mysqli_fetch_assoc($iQuery)){
      $iteml = array();
        if($prod['quantity'] <= $prod['threshold']){

          $iteml = array(
            'title' => $invent['title'],
            'quantity' => $invent['quantity'],
            'threshold' => $invent['threshold']
          );
          $lowprod[] = $iteml;
        }
    }
  ?>
  <div class="col-md-8">
    <h3 class="text-center">Low qty. Products</h3>
    <table class="table table-condensed table-bordered table-striped">
      <thead>
        <th>Product</th>
        <th>Quantity</th>
        <th>Threshold</th>
      </thead>
      <tbody>
      <?php foreach($lowprod as $produ): ?>
        <tr<?php echo(($produ['quantity']==0)?' class="danger"':'');?>>
          <td><?php echo($produ['title']);?></td>
          <td><?php echo($produ['quantity']);?></td>
          <td><?php echo($produ['threshold']);?></td>
        </tr>
      <?php endforeach;?>
      </tbody>
    </table>
  </div>
</div>




<!-- Sales By salers -->
<!--<div class="row">
<div class="col-md-6">

</div>
</div>
-->

<?php include 'includes/footer.php';?>
