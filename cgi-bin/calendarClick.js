function calendarClick($elem) {
	var cal = new CalendarPopup();
	cal.select(  document.getElementById($elem),$elem,'yyyy-MM-dd');
	return false;
}