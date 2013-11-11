<?php
// Get arguments

$startdate = $_GET['start'];
$enddate = $_GET['end'];

//echo $startdate."\n".$enddate."\n";

// Make a MySQL Connection

include 'login_sql.php';

// Retrieve all the data from the table
$sql = "select productsout.name, sum(productsout.units) as units, sum(productsout.units*productsout.price) as sales\n"
    . "from productsout\n"
    . "inner join products on productsout.product=products.reference\n"
    . "inner join tickets on productsout.ticketid=tickets.ticketid\n"
    . "where (category = 22 or category = 23 or category = 24 or category = 25 or category = 27 or category = 28)\n"
    . "and tickets.datenew >= '".$startdate."' and tickets.datenew < '".$enddate."'\n"
    . "group by productsout.name";

$result = mysql_query($sql) or die(mysql_error());

// store the record of the table into $row
// Print out the contents of the entry

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

  <head>
    <title> Alcohol Sales Report </title>
    <link rel="stylesheet" type="text/css" href="../css/reportstyle.css" />
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8" />

  </head>
  <body>';
echo "<table id='report-table' padding='5px' width='95%'>";
echo "<tr><th colspan='3'>
	<h1>Alcohol Sales</h1><hr width='50%'/>
	<h5>".$startdate."  through  ".$enddate."</h5>

	</th></tr>";
echo "<tr>
	<th class='column-header' align='left'>Name</th>
	<th class='column-header' align='center'>Units</th>
	<th class='column-header' align='right'>Sales</th>
	</tr>";

$whichcolor = 0;

$total = 0.0;

while($row = mysql_fetch_array($result)){
	echo "<tr class='rowcolor".$whichcolor."'>";
	echo "<td align='left'>".$row['name']."</td>";
	echo "<td align='center'>".$row['units']."</td>";
	echo "<td align='right'>$".
		number_format ( floatval($row['sales']), 2 , '.' , ',' )
		."</td>";
	echo "</tr>\n<tr>";
	$whichcolor=1-$whichcolor;
	$total += floatval($row['sales']);

}

echo "<tr></tr>\n";
echo "<tr><td></td><td></td>";
echo "<td align='right' class='column-footer'>Total: $".
	number_format ( $total, 2 , '.' , ',' )
	."</td></tr>";
echo "</table>
	</body></html>";

?>