<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package storefront
 */
?>
<!-- End Header Div -->
</div>

<div id="more_posts"><span>Show More</span></div>
<script>
<?php 
	$bricklayer_post_types = get_post_meta(  get_the_ID(), '_custom-meta-box' );
	$postNumber = get_post_meta(  get_the_ID(), 'bricklayer_number_of_posts_to_show' , true);
?>
	
var ppp = <?php echo get_post_meta(  get_the_ID(), 'bricklayer_number_of_posts_to_show' , true); ?>; // Post per page
var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
		var postNumber = <?php echo $postNumber ?>;
		var postType = '<?php 
		foreach( $bricklayer_post_types as $key) {
			foreach($key as $item){
				  if ($item === end($key)){					  
					echo $item;
				  }else{
					echo $item .',';		  
				  }
			}
		} ?>';
		var sizeArray = '<?php echo get_post_meta(  get_the_ID(), 'brickstyle' , true);?>';
		var brickstyleCount = 'rowCount<?php echo get_post_meta(  get_the_ID(), 'brickstyleCount' , true);?>';
		jQuery( document ).ready(function() {
			jQuery(document.body).addClass(sizeArray);
			jQuery(document.body).addClass(brickstyleCount);
		});
</script>
