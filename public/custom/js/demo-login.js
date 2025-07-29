/**
 * Demo Login
 * */
$(function() {
	"use strict";

	$(document).ready(function() {
	    /**
	     * Admin Login
	     * */
		$(".admin").on("click", function(){
			$('input[name="email"]').val('admin@example.com');
			$('input[name="password"]').val('12345678');
			
		});

		/**
		 * Seller Login
		 * */
		$(".seller").on("click", function(){
			$('input[name="email"]').val('seller@example.com');
			$('input[name="password"]').val('12345678');
			
		});

		/**
		 * Purchase Login
		 * */
		$(".purchase").on("click", function(){
			$('input[name="email"]').val('purchase@example.com');
			$('input[name="password"]').val('12345678');
			
		});
		
	});

});