$(function($) {
	
	// Esconde o alert ao invés de removê-lo do DOM
	$('.alert .close').on('click', function(e) {
        $(this).parent().hide();
    });
	
});
