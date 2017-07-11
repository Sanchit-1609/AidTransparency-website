$(function() {
	$("#accordion").accordion({
	    collapsible: true, 
		alwaysOpen: false, 
		active: false,
		autoHeight: false
	});
	$("#accordion-2").accordion({
	    collapsible: true, 
		alwaysOpen: false, 
		active: false,
		autoHeight: false
	});
	$("#accordion-3").accordion({
	    collapsible: true, 
		alwaysOpen: false, 
		active: false,
		autoHeight: false
	});
	$("#accordion-4").accordion({
	    collapsible: true, 
		alwaysOpen: false, 
		active: false,
		autoHeight: false
	});
	$("#accordion-5").accordion({
	    collapsible: true, 
		alwaysOpen: false, 
		active: false,
		autoHeight: false
	});
	
	$(".scrollable").scrollable().navigator();

	// scroll to to
	$('a.back-top').click(function(event) {
	event.preventDefault();
	$('body').scrollTo( { top:0, left:0}, 800 )
	}); 
	
	//dropdown boxes on the resources page
	
	//more documents button
	$('a.resource-dropdown').click(function(event) { // trigger click event on the button
		event.preventDefault(); //launches event to prevent the default link behavour
		$(this).siblings('.resource-content-wrapper').slideToggle('fast');
	}); 
	 
		/* search box */
	$('#s').focus(function() {
		
		var self=$(this);
		if(self.val()=='Search') {
			self.val('');
		}
	
	});
 
	 
}); //end script
