<?php
// Get arguments

$startdate = $_GET['start'];
$enddate = $_GET['end'];

//echo $startdate."\n".$enddate."\n";

// Make a MySQL Connection

include 'login_sql.php';

// Retrieve all the data from the table
$sql = "select categories.name as cat, productsout.name, sum(productsout.units) as units, sum(productsout.units * productsout.price) as sales, products.pricesell
from productsout
inner join tickets on productsout.ticketid=tickets.ticketid
join products on productsout.product = products.reference
join categories on products.category = categories.id
where tickets.datenew >= '".$startdate."'
	and tickets.datenew < '".$enddate."'
	group by productsout.name order by categories.name, name ASC";

$result = mysql_query($sql) or die(mysql_error());

// store the record of the table into $row
// Print out the contents of the entry

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

  <head>
    <title> Product Sales Report </title>
    <link rel="stylesheet" type="text/css" href="../css/reportstyle.css" />
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8" />

  </head>
  <body>';
echo "<table id='report-table' padding='5px' width='95%'>";
echo "<tr><th colspan='4'>
	<h1>Sales by Products</h1><hr width='50%'/>
	<h5>".$startdate."  through  ".$enddate."</h5>
	</th></tr>";


$whichcolor = 0;
$currentcat = "";
$total = 0.0;
$subtotal = 0.0;
$units = 0;
$subunits = 0;

// read all records from database

$records = array();

while($row = mysql_fetch_array($result)){
	$records[] = $row;
}


// cleanup records

$newRecords = array();

foreach ($records as &$row) {
	$priceEach = floatval($row['sales']) / floatval($row['units']);

	if ($priceEach < (floatval($row['pricesell']*0.6))) {
		$row['units']=floatval($row['units'])/2.0;
	}

	$name = trim($row['name']);
	$row['name'] = $name;

	$found = 0;

	foreach ($newRecords as &$newRow) {
		if ($newRow['name'] == $name && $found == 0) {
			$newRow['units'] += $row['units'];
			$newRow['sales'] += $row['sales'];
			$found = 1;
		}
	}

	if ($found == 0) {
		$newRecords[] = $row;
	}

}

$records = $newRecords;


// display results
foreach ($records as $row) {

	// new category
	if ($row['cat'] != $currentcat) {

		// show footer if not first category
		if ($currentcat != "") {
			// category footer
			echo "<tr><td></td><td></td>";
			echo "<td align='center' class='column-footer'>".$subunits."</td>";
			echo "<td align='right' class='column-footer'>Total: $".
				number_format ( $subtotal, 2 , '.' , ',' )
				."</td></tr><tr>"
				."<td colspan='4'><hr/></th></tr>";
			$subtotal = 0.0;
			$subunits = 0;
		}

		$currentcat = $row['cat'];

		echo "<tr><td align='left' colspan='4'><big><u><b>".$row['cat']."</b></u></big></td></tr>
			<tr><td width='50px'></td>
			<th class='column-header' align='left'>Name</th>
			<th class='column-header' align='center'>Units</th>
			<th class='column-header' align='right'>Sales</th>
			</tr>";

	}

	echo "<tr class='rowcolor".$whichcolor."'>";
	echo "<td bgcolor='white'></td><td align='left'>".$row['name']."</td>";
	echo "<td align='center'>".$row['units']."</td>";
	echo "<td align='right'>$".
		number_format ( floatval($row['sales']), 2 , '.' , ',' )
		."</td>";
	echo "</tr>\n";
	$whichcolor=1-$whichcolor;
	$total += floatval($row['sales']);
	$subtotal += floatval($row['sales']);
	$units += floatval($row['units']);
	$subunits += floatval($row['units']);

}

// category footer
echo "<tr><td></td><td></td>";
echo "<td align='center' class='column-footer'>".$subunits."</td>";
echo "<td align='right' class='column-footer'>$".
	number_format ( $subtotal, 2 , '.' , ',' )
	."</td></tr><tr>"
	."<td colspan='4'><hr/></th></tr>";

// report footer
echo "<tr><td colspan='2' align='right'><big><b><i><u>Totals</u></i></b></big></td><td align='center' class='column-footer'>".$units."</td>";
echo "<td align='right' class='column-footer'>$".
	number_format ( $total, 2 , '.' , ',' )
	."</td></tr>";

echo "</table>
	</body></html>";

?>