<?php
require_once 'core/init.php';
require('fpdf17/fpdf.php');


$cart_id = sanitize($_POST['cart_id']);

$sub_total = sanitize($_POST['sub_total']);
$grand_total = sanitize($_POST['grand_total']);

$charge_amount = number_format($grand_total,2) * 100;

if($cart_id != '' ){
  $cartQ = $db->query("SELECT * FROM cart WHERE id = '{$cart_id}'");
  $result = mysqli_fetch_assoc($cartQ);
  $items = json_decode($result['items'],true);
  $i = 1;

  $item_count = 0;
}

$date = date('d/m/Y  h:i a', time());
$sp=130;

$pdf = new FPDF('P','mm','A4');

$pdf->AddPage();

//set font to ariel, bold, 16pt
$pdf->SetFont('Arial','B',20);
$pdf->Cell(189 ,10,'Yuvaan\'s Midway Treat',0,1,'C');
//dumy empty cel as a verticle spacer
$pdf->Cell(189 ,3,'',0,1);
$pdf->Line(10, 48, 199, 48);


//set font to ariel, bold, 14pt
$pdf->SetFont('Arial','',12);

//cell (width, height, text, border(1,0), end line(1,0), [align](L,C,R))

$pdf->Cell(140 ,5,'Contact no.- 070242 52222,',0,0);
$pdf->Cell(59 ,5,'N.H No.07, Seoni Road,',0,1);//end of line

//set font to nrmal 12pt
$pdf->SetFont('Arial','',12);

$pdf->Cell(22 ,5,'',0,0);
$pdf->Cell(118 ,5,'',0,0);
$pdf->Cell(59 ,5,'Lakhnadon, Distt Seoni,',0,1);

$pdf->Cell(140 ,5,'',0,0);
$pdf->Cell(59 ,5,'Madhya Pradesh (480001)',0,1);


$pdf->Cell(140 ,5,'Email: treat@gmail.com',0,0);
$pdf->Cell(59 ,5,'',0,1);

$pdf->Cell(140 ,5,'Visit us: www.midwaytreatlakhnadon.com',0,0);
$pdf->Cell(59 ,5,'GSTIN: 123456789123456',0,1);

$pdf->Cell(189 ,5,'',0,1);
$pdf->Cell(139 ,5,'',0,0);
$pdf->Cell(50 ,5,$date,0,1);

/*
//dumy empty cel as a verticle spacer
$pdf->Cell(189 ,15,'',0,1);//end of line

$pdf->SetFont('Arial','B',12);
//billing address
$pdf->Cell(115 ,5,$pname,0,0);
$pdf->SetFont('Arial','',12);
$pdf->Cell(74 ,5,$gender.','.$age.' years',0,1);

//add dummy cell at beginning of line
//$pdf->Cell(10 ,5,'',0,0);
$pdf->Cell(115 ,5,'+91-'.$mobile_no,0,0);
if($street!=''){
$pdf->Cell(74 ,5,$street.',',0,1);
}
else
{$pdf->Cell(74 ,5,'',0,1);}

//$pdf->Cell(10 ,5,'',0,0);
$pdf->Cell(115 ,5,'',0,0);
$pdf->Cell(74 ,5,$city.'.',0,1);

//$pdf->Cell(10 ,5,'',0,0);
//$pdf->Cell(90 ,5,'Addre',0,1);

//$pdf->Cell(10 ,5,'',0,0);
//$pdf->Cell(90 ,5,'phone',0,1);

//dumy empty cel as a verticle spacer
$pdf->Cell(189 ,20,'',0,1);//end of line

$pdf->Cell(15 ,5,'Ref by: ',0,0);
//invoice content
$pdf->SetFont('Arial','B',12);

$pdf->Cell(174 ,5,'Dr. '.$doctor_name,0,1);
*/
$pdf->SetFont('Arial','B',14);
$pdf->Cell(189 ,15,'',0,1);

$pdf->Cell(189 ,5,'Discription',0,1,'C');
$pdf->Cell(189 ,5,'',0,1);
$pdf->SetFont('Arial','B',12);

$pdf->Cell(10 ,5,'#',1,0);
$pdf->Cell(80 ,5,'Services',1,0);
$pdf->Cell(40 ,5,'Cost',1,0,'C');
$pdf->Cell(25 ,5,'Quantity',1,0,'C');
$pdf->Cell(34 ,5,'Total cost',1,1,'C');

$pdf->Cell(189 ,5,'',0,1);
$pdf->SetFont('Arial','',12);

//no are Right aligned so R after new line
$c=1;
foreach ($items as $item) {
  $product_id = $item['id'];
  $productQ = $db->query("SELECT * FROM Products WHERE id = '{$product_id}'");
  $product = mysqli_fetch_assoc($productQ);
$pdf->Cell(10 ,5,$c,0,0);
$pdf->Cell(80 ,5,$product['title'],0,0);
$pdf->Cell(40 ,5,money($product['price']),0,0,'C');
$pdf->Cell(25 ,5,$item['quantity'],0,0,'C');
$pdf->Cell(34 ,5,money(($item['quantity'] * $product['price'])).' ',0,1,'R');
$pdf->Cell(189 ,2,'',0,1);
$c=$c+1;
$sp=$sp+8;
}
$pdf->Line(10, $sp, 199, $sp);
$pdf->Cell(189 ,2,'',0,1);
//summary
$pdf->Cell(130 ,5,'',0,0);
$pdf->Cell(25 ,5,'subtotal',0,0);
$pdf->Cell(34 ,5,money($sub_total).' ',0,1,'R');
$pdf->Cell(189 ,0.5,'',0,1);


$pdf->Cell(79 ,5,'',0,0);
$pdf->Cell(76 ,5,'(CGST/SGST included) Grand total',0,0);
$pdf->Cell(34 ,5,money($grand_total).' ',0,1,'R');
$pdf->Cell(189 ,0.5,'',0,1);


$pdf->Cell(189 ,5,'',0,1);
$pdf->Cell(89 ,$sp-130,'Note : Errors and omissions excepted',0,1);

$pdf->Output();

?>
