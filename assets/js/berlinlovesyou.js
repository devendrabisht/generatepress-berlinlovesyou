
jQuery( document ).ready( function( $ ) {

	return;

	$( window ).scroll( function() {
		if ( $( window).scrollTop() == $( document ).height() - $( window ).height() ) {
			var offset = jQuery( '#bly-posts-list-offset' ).val();
			offset = parseInt( offset );
			offset += 10;
			jQuery( '#bly-posts-list-offset' ).val( offset ); 
			$.ajax({
				url : berlinlovesyou_script_ajax.ajax_url,
				type : 'post',
				data : {
					action : 'bly_load_more_post_listing',
					offset : offset,
					render_content: 'yes'
				},
				success : function( data ) {
					jQuery( '.bly-posts-list-section' ).append( data );
				}
			});
		}
	});


	// return;
	var slide_wrp 		= ".bly-gp-side-menu-wrapper"; //Menu Wrapper
	var open_button 	= ".bly-gp-menu-open"; //Menu Open Button
	var close_button 	= ".bly-gp-menu-close"; //Menu Close Button
	var overlay 		= ".bly-gp-menu-overlay"; //Overlay

	$(slide_wrp).hide().css( {"right": -$(slide_wrp).outerWidth()+'px'}).delay(50).queue(function(){$(slide_wrp).show()}); 

	$(open_button).click(function(e){
		e.preventDefault();
		$(slide_wrp).css( {"right": "0px"});
		setTimeout(function(){
			$(slide_wrp).addClass('active');
		},50);
		$(overlay).css({"opacity":"1", "width":"100%"});
	});

	$(close_button).click(function(e){
		e.preventDefault();
		$(slide_wrp).css( {"right": -$(slide_wrp).outerWidth()+'px'});
		setTimeout(function(){
			$(slide_wrp).removeClass('active');
		},50);
		$(overlay).css({"opacity":"0", "width":"0"});
	});

	$(document).on('click', function(e) {
		if (!e.target.closest(slide_wrp) && $(slide_wrp).hasClass("active")){
			$(slide_wrp).css( {"right": -$(slide_wrp).outerWidth()+'px'}).removeClass('active');
			$(overlay).css({"opacity":"0", "width":"0"});
		}
	});
} );