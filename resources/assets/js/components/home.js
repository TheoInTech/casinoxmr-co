$(document).ready(function() {

	var nextRaffleDate = new Date( $("#raffle-date").val() );
	var currentDate = new Date( $("#current-date").val() );
	var timeInterval, timeRemaining, seconds, minutes, hours, days;

	$("#link-privacy").on('click', function() {
		$("#modal-privacy-policy").modal("show");
	});

	$("#link-terms").on('click', function() {
		$("#modal-terms-and-conditions").modal("show");
	});

	timeInterval = setInterval(getTimeRemaining, 1000);
	function getTimeRemaining() {
		timeRemaining = parseInt((nextRaffleDate - currentDate) / 1000);
		days = parseInt(timeRemaining / 86400);
		timeRemaining = (timeRemaining % 86400);
		hours = parseInt(timeRemaining / 3600);
		timeRemaining = (timeRemaining % 3600);
		minutes = parseInt(timeRemaining / 60);
		timeRemaining = (timeRemaining % 60);
		seconds = parseInt(timeRemaining);

		$("#day-number").text( ('0' + days).slice(-2) );
		$("#day-rep").text( days > 1 ? 'days' : 'day' );

		$("#hour-number").text( ('0' + hours).slice(-2) );
		$("#hour-rep").text( hours > 1 ? 'hours' : 'hour' );

		$("#minute-number").text( ('0' + minutes).slice(-2) );
		$("#minute-rep").text( minutes > 1 ? 'minutes' : 'minute' );

		$("#second-number").text( ('0' + seconds).slice(-2) );
		$("#second-rep").text( seconds > 1 ? 'seconds' : 'second' );
		
		currentDate.setSeconds( currentDate.getSeconds() + 1 );
	}

});