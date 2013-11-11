<?php
// Get arguments

$startdate = $_GET['start'];
$enddate = $_GET['end'];

//echo $startdate."\n".$enddate."\n";

// Make a MySQL Connection

include 'login_sql.php';

// Retrieve all the data from the table
$sql = "select payments.payment, sum(payments.total) as payments_total \n"
    . "from tickets inner join payments on tickets.ticketid = payments.ticketid\n"
    . "where 1 and tickets.datenew >= '".$startdate."' and tickets.datenew < '".$enddate."'\n"
    . "group by payments.payment";

$result = mysql_query($sql) or die(mysql_error());

// store the record of the table into $row
// Print out the contents of the entry

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

  <head>
    <title> Payment Type Report </title>
    <link rel="stylesheet" type="text/css" href="../css/reportstyle.css" />
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8" />

  </head>
  <body>';
echo "<table id='report-table' padding='5px' width='95%'>";
echo "<tr><th colspan='2'>
	<h1>Payment Types</h1><hr width='50%'/>
	<h5>".$startdate."  through  ".$enddate."</h5>

	</th></tr>";
echo "<tr>
	<th class='column-header' align='left'>Payment</th>
	<th class='column-header' align='right'>Total</th>
	</tr>";

$whichcolor = 0;

$total = 0.0;

while($row = mysql_fetch_array($result)){
	echo "<tr class='rowcolor".$whichcolor."'>";
	echo "<td align='left'>".$row['payment']."</td>";
	echo "<td align='right'>$".
		number_format ( floatval($row['payments_total']), 2 , '.' , ',' )
		."</td>";
	echo "</tr>\n<tr>";
	$whichcolor=1-$whichcolor;
	$total += floatval($row['payments_total']);

}

echo "<tr></tr>\n";
echo "<tr><td></td>";
echo "<td align='right' class='column-footer'>Total: $".
	number_format ( $total, 2 , '.' , ',' )
	."</td></tr>";
echo "</table>
	</body></html>";

?>