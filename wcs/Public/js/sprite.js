// JavaScript Document

$(function(){
		 
		   $('#navigation li a').append('<span class="hover"></span>')
		   
		   $('#navigation li a').hover(function() {
	        //alert("sdf");
		$('.hover', this).stop().animate({
			'opacity': 1
			}, 700,'easeOutSine')
	
	},function() {
	
		// Stuff that happens when you unhover + the stop()
		$('.hover', this).stop().animate({
			'opacity': 0
			}, 700, 'easeOutQuad')
	
	})
});