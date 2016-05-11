/**
 * Additive Two Column Post
 * http://github.com/thomasrstorey
 *
 * Copyright (c) 2016 Thomas R Storey
 * Licensed under the MIT license.
 */

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
