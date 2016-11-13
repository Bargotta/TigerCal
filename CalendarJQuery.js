// // used to pass eventID to calendar.php
// if (window.XMLHttpRequest){
//     xmlhttp=new XMLHttpRequest();
// }

// else{
//     xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
// }

// // events list modal
// $('#EventInfoModal').on('show.bs.modal', function(e) {
//   	var eventId = $(e.relatedTarget).data('event-id');
// 	var PageToSendTo = "calendar.php?";
// 	var VariablePlaceholder = "id=";
// 	var MyVariable = eventId;
// 	var UrlToSend = PageToSendTo + VariablePlaceholder + MyVariable;
// 	// alert(UrlToSend);

// 	xmlhttp.open("GET", UrlToSend, true);
// 	xmlhttp.send();
// });

// script for highlighting selecting days
$(document).ready(function(){

  	$(".day").click(function() {
    	if ($(this).hasClass( "selected")) {
      		$(this).removeClass("selected");
    	}
	    else {
	      	$(".day").removeClass("selected");
	      	$(this).addClass("selected");
    	};
  	});
});

function showDetails(event) {
	var club = event.getAttribute("data-infoClub");
    var eventName = event.getAttribute("data-eventname");
    var startDate = event.getAttribute("data-date");
    var starttime = event.getAttribute("data-time");
    var enddate = event.getAttribute("data-enddate");
    var endtime = event.getAttribute("data-endtime");
    var location1 = event.getAttribute("data-location1");
    var location2 = event.getAttribute("data-location2");
    var location3 = event.getAttribute("data-location3");
    var location4 = event.getAttribute("data-location4");
    var location5 = event.getAttribute("data-location5");
    var location6 = event.getAttribute("data-location6");
    var location = "";
    var description = event.getAttribute("data-description");
	// $('#infoClub').html(club); Add back in once database has been updated with clubs
	$('#infoEventName').html(eventName);
	$('#infoStartDate').html(startDate);
	$('#infoStartTime').html(starttime);
	// $('#infoEndDate').html(enddate);
	$('#infoEndTime').html(endtime);
	// add all locations
	if (location1 != '') {
		location = location.concat('<b>- </b>').concat(location1).concat('<br>');
		if (location2 != '') {
			location = location.concat('<b>- </b>').concat(location2).concat('<br>');
			if (location3 != '') {
				location = location.concat('<b>- </b>').concat(location3).concat('<br>');
				if (location4 != '') {
					location = location.concat('<b>- </b>').concat(location4).concat('<br>');
					if (location5 != '') {
						location = location.concat('<b>- </b>').concat(location5).concat('<br>');
						if (location6 != '') {
							location = location.concat('<b>- </b>').concat(location6).concat('<br>');
						}
					}
				}
			}
		}
	}
	$('#infoLocation1').html(location);
	$('#infoDescription').html(description);

	$("#googleCalButton").click(function(){
    	alert(eventName+" test");
    	$.getScript("InsertGoogleCalendar.js", function(){

   			alert("Script loaded but not necessarily executed.");
   			insertEvent(eventName, startDate, starttime, enddate, endtime, location1, description);
   			alert("added " + eventName);
		});
// insertEvent(eventName, date, time, endDate, endTime, location, description)
	});
}

// script for showing events for a specific day (right sidebar)
$(".single, .double").click(function() {
  	$.post('EventsListPage.php', {dayIDCalendar2: $(this).attr("id")}, function(data) {
  		$('#EventsListCount').html(data);
  	});
  	$.post('TodaysEvents.php', {dayID: $(this).attr("id")}, function(data) {
    	$('#eventlist').html(data);
  	});
});

// script for when mouse is taken off each cell in calendar (right sidebar)
// $(".day").mouseout(function() {
//   	$('#eventlist').html("Hover over each day to see what events are going on!");
//   });

// script for instant searching (left sidebar on calendar page)
function searchq() {

	var searchTxt = $("input[name='search']").val();

	// $.post("EventSearch.php", {searchVal: searchTxt}, function(output) {
//	  	$("#output").html(output);
	$.post("EventsListPage.php", {searchVal: searchTxt}, function(output) {
	  	$("#EventsListCount").html(output);
	});
}

// script to bring up model to edit events (clubs page)
$("dd").on('click', function() {
	if ($(this).attr('id') == 'leftBar') {
	    $("#infoModal").modal("show");			    
	}
});

// script to bring up model to edit events (clubs page)
$("table tbody tr").on('click', function() {
	if ($(this).attr('id') == 'editEventTable') {
	    $("#myModal").modal("show");
	    $("#eventIDEdit").val($(this).closest('tr').children()[0].textContent);
	    $("#club").val($(this).closest('tr').children()[1].textContent);
	    $("#editEmail").val($(this).closest('tr').children()[2].textContent);

	    // var email = $(this).closest('tr').attr('class');
	    // $("#editEmail").val(email);
	    $("#eventName").val($(this).closest('tr').children()[3].textContent);
	    var d = new Date($(this).closest('tr').children()[4].textContent);
	    var date = d.toLocaleDateString();
	    date = formatDate(date);
	    $("#date").val(date);
	    $("#time").val($(this).closest('tr').children()[5].textContent);
	    var ed = new Date($(this).closest('tr').children()[6].textContent);
	    var endDate = ed.toLocaleDateString();
	    endDate = formatDate(endDate);
	    $("#endDate").val(endDate);
	    $("#endTime").val($(this).closest('tr').children()[7].textContent);
	    $("#location1").val($(this).closest('tr').children()[8].textContent);
	    $("#location2").val($(this).closest('tr').children()[9].textContent);
	    $("#location3").val($(this).closest('tr').children()[10].textContent);
	    $("#location4").val($(this).closest('tr').children()[11].textContent);
	    $("#cost").val($(this).closest('tr').children()[12].textContent.replace("$", ""));
	    var eligible = $(this).closest('tr').children()[13].textContent;
	    if (eligible == "YES") {
			document.getElementById('yes').checked = true;
	    }
	    else {
			document.getElementById('no').checked = true;
	    };
	    $("#briefDesc").val($(this).closest('tr').children()[14].textContent);
	}
});

function formatDate(date) {
	var dayArray = date.split("/");
	var month = parseInt(dayArray[0]);
	var day = parseInt(dayArray[1]);
	var year = parseInt(dayArray[2]);

	day = formatDigits(day);
	month = formatDigits(month);
	year = formatDigits(year);

	return year+'-'+month+'-'+day;
}

function formatDigits(n) {
	return n > 9 ? "" + n: "0" + n;
}

// =============================================================================================

// Code for smooth scrolling
$(document).ready(function(){
  	// Add smooth scrolling to all links in navbar + footer link
  	$(".navbar a, footer a[href='#myPage']").on('click', function(event) {

  	// Make sure this.hash has a value before overriding default behavior
  	if (this.hash !== "") {

	    // Prevent default anchor click behavior
	    event.preventDefault();

	    // Store hash
	    var hash = this.hash;

	    // Using jQuery's animate() method to add smooth page scroll
	    // The optional number (900) specifies the number of milliseconds it takes to scroll to the specified area
	    $('html, body').animate({
	      	scrollTop: $(hash).offset().top
	    }, 900, function(){

	    	// Add hash (#) to URL when done scrolling (default click behavior)
	    	window.location.hash = hash;
	    	});
    	} // End if
  	});
})
//End of code for smooth scrolling

// Code for Google Maps
var myCenter = new google.maps.LatLng(40.344372, -74.652298);

function initialize() {
var mapProp = {
	center:myCenter,
	zoom:15,
	scrollwheel:false,
	draggable:false,
	mapTypeId:google.maps.MapTypeId.ROADMAP
};

var map = new google.maps.Map(document.getElementById("googleMap"),mapProp);

var marker = new google.maps.Marker({
	position:myCenter,
});

marker.setMap(map);
}

google.maps.event.addDomListener(window, 'load', initialize);
// End of code for Google Maps