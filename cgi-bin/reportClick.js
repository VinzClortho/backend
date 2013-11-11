function reportClick($arg) {

  var startdate = document.getElementById('startdate').value;
  var enddate = document.getElementById('enddate').value;
  
  document.getElementById('report').src="cgi-bin/" + $arg + "-report.php?start=" + startdate + "&end=" + enddate ;

}