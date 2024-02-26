<?php
/**
 * The Header template for displaying the posts.
 *
 * This page template will display any functions hooked into the `page` action.
 * By default this includes a variety of product displays and the page content itself. To change the order or toggle these components
 * use the Homepage Control plugin.
 *
 * Template name: Bricklayer Page
 *
 */
 
 
get_header();
?>

<div class="bricklayer hidden" id="my-bricklayer">

<?php
//Include content 
	include( 'page-template.php');
?>
	

<?php 
//include footer
include( 'footer-template.php');

get_footer();



