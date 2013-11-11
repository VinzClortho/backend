<?php
// Get arguments

$startdate = $_GET['start'];
$enddate = $_GET['end'];

//echo $startdate."\n".$enddate."\n";

// Make a MySQL Connection

include 'login_sql.php';

// Retrieve all the data from the table
$sql = "select weekday(tickets.datenew) as weekday, date(tickets.datenew) as date, hour(tickets.datenew) as startdate, hour(tickets.datenew)+1 as enddate, sum(productsout.units * productsout.price) as total\n"
    . "from tickets, productsout where tickets.ticketid = productsout.ticketid and \n"
    . "tickets.datenew >= '".$startdate."'\n"
    . "and tickets.datenew < '".$enddate."'\n"
    . "group by date(tickets.datenew), hour(tickets.datenew)";

$result = mysql_query($sql) or die(mysql_error());
$days = array("Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday");

// store the record of the table into $row
// Print out the contents of the entry

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

  <head>
    <title> Hourly Sales Report </title>
    <link rel="stylesheet" type="text/css" href="../css/reportstyle.css" />
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8" />

  </head>
  <body>';
echo "<table id='report-table' padding='5px' width='95%'>";
echo "<tr><th colspan='3'>
	<h1>Sales by Hour</h1><hr width='50%'/>
	<h5>".$startdate."  through  ".$enddate."</h5>

	</th></tr>";

$whichcolor = 0;
$currentDate = "";
$total = 0.0;
$subtotal = 0.0;

while($row = mysql_fetch_array($result)) {

	// new date
	if ($row['date'] != $currentDate) {

		// show footer if not first date
		if ($currentDate != "") {
			// date footer
			echo "<tr><td></td><td></td>";
			echo "<td align='right' class='column-footer'>Total: $".
				number_format ( $subtotal, 2 , '.' , ',' )
				."</td></tr><tr>"
				."<td colspan='4'><hr/></th></tr>";
			$subtotal = 0.0;
			$subunits = 0;
		}

		$currentDate = $row['date'];

		echo "<tr><td align='left' colspan='4'><big><u><b>".$days[$row['weekday']]." - ".$row['date']."</b></u></big></td></tr>
			<tr><td width='50px'></td>
			<th class='column-header' align='center'>Times</th>
			<th class='column-header' align='right'>Sales</th>
			</tr>";

	}


	echo "<tr class='rowcolor".$whichcolor."'>";
	echo "<td bgcolor='white'></td><td align='center'><small>";
	if ($row['startdate']<=12) {
		echo $row['startdate'].":00am - ";
	} else {
		echo (intval($row['startdate']) - 12).":00pm - ";
	}
	if ($row['enddate']<=12) {
		echo $row['enddate'].":00am";
	} else {
		echo (intval($row['enddate']) - 12).":00pm";
	}

	echo "</small></td>";
	echo "<td align='right'>$".
		number_format ( floatval($row['total']), 2 , '.' , ',' )
		."</td>";
	echo "</tr>\n<tr>";
	$whichcolor = 1 - $whichcolor;
	$total += floatval($row['total']);
	$subtotal += floatval($row['total']);
}

echo "<tr><td></td><td></td>";
echo "<td align='right' class='column-footer'>Total: $".
	number_format ( $subtotal, 2 , '.' , ',' )
	."</td></tr><tr>"
	."<td colspan='4'><hr/></th></tr>";


echo "<tr></tr>\n";
echo "<tr><td></td><td></td>";
echo "<td align='right' class='column-footer'>Total: $".
	number_format ( $total, 2 , '.' , ',' )
	."</td></tr>";
echo "</table>
	</body></html>";

?>