/*! Additive Two Column Post - v0.1.0
 * http://github.com/thomasrstorey
 * Copyright (c) 2016; * Licensed MIT */
( function( window, undefined ) {
	'use strict';
	var $ = jQuery.noConflict();
	$( document ).ready(function(){
		var wrapper =  $('#additivecolumneditor-wrapper');
		var checkbox = $('#additive-two-column-toggle');

		if(checkbox.is(":checked")){
			console.log("CHECKED");
			// wrapper.attr("display", "block");
		} else {
			console.log("NOT CHECKED");
			wrapper.hide();
		}

		checkbox.click( function () {
			wrapper.toggle("fast");
		});
	});
} )( this );
