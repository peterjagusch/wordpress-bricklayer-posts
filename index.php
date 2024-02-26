<?php
/*
Plugin Name: Bricklayer posts
Plugin URI: http://438marketing.com/
Description: Add bricklayer styled content anywhere
Version: 1.0
Author: P.Jagusch
Author URI: http://438marketing.com
License: GPL
*/
/* This adds bricklayer styled content to any page. Add bricklayer to page and select which post types, 
number of posts to show and style of the bricklayer.
Automatic use of load more and 
*/




$plugin_dir_uri = plugin_dir_url( __FILE__ );


function get_ajax_posts(){
global $post;
$url = wp_get_referer();
$post_id = url_to_postid($url);

		$bricklayer_post_types = get_post_meta(  $post_id, '_custom-meta-box' , true);
		$showposts = get_post_meta(  $post_id, 'bricklayer_number_of_posts_to_show' , true);	    
		$postType = (isset($_POST["postType"])) ? $_POST["postType"] :  array( 'post');
		$page = (isset($_POST['pageNumber'])) ? $_POST['pageNumber'] : 0;
		$ppp = get_post_meta(  $post_id, 'bricklayer_number_of_posts_to_show' , true);

		header("Content-Type: text/html");

    $args = array(
        'suppress_filters' => true,
        'post_type' => $bricklayer_post_types,
        'posts_per_page' => $ppp,
        'cat' => 0,
        'paged'    => $page,
		'orderby' => 'date',
		'post_status'      => 'publish',
		'order' => 'DESC'
    );
	
    $loop = new WP_Query($args);
    $out = '';

    if ($loop -> have_posts()) :  while ($loop -> have_posts()) : $loop -> the_post();
        $categories = get_the_category(); 
        $cat_name = $categories[0]->cat_name;
        $type = get_post_type();
        $image  = wp_get_attachment_image_src( get_post_thumbnail_id(), 'entry-cropped' );
        $bg_image           = ( $image[0] != '' ) ? ' style="background-image: url(' . $image[0] . ');"' : '';
        $show_image           = ( $image[0] != '' ) ? ' src="' . $image[0] . '"' : '';
        $image_output       = '<img '. $show_image .'>';
        $image_output_class = 'with-image';
		$bgClass = "";
        $str = mb_strtolower($cat_name);
			$backgroundColor = get_post_meta($post->ID, 'background-color', true);
			$userLink = get_post_meta($post->ID, 'user-link', true);
			$bgcolor = "style='background-color:" .$backgroundColor ."'"; 		 
			$userLink = get_post_meta($post->ID, 'user-link', true);   
			if( $userLink!=""){ 
				$links = "window.open('" . $userLink ."','_blank')"; 
				}else{ 
				$links =  "location.href = '" .get_the_permalink() . "'";
			};
			if($image){
				$bgClass = 'brick-image';
			}
			if($backgroundColor !=""){ 
				$bg = $bgcolor; 
			}else{ 
				$bg = $bg_image; 				 
			}; 
			$userLinkClass="";
			if($userLink !=""){ 
				$userLinkClass = "user-link "; 
			}
			$backgroundColorClass="";
			if($backgroundColor !=""){ 
				$backgroundColorClass="color-set "; 
			}
			
		$out .= '<div onclick="'.$links.'" id="post-'.$post->ID.'" '.$bg.' class="' . $userLinkClass . $backgroundColorClass . $bgClass . ' brick-layout '. $str .'">
			
			<div class="inner"><h2>'.get_the_title().'</h2><h3><p>'.get_the_excerpt().'</p></h3></div></div>';
                endwhile;
            endif;
        wp_reset_postdata();
    die($out);
}

add_action('wp_ajax_nopriv_get_ajax_posts', 'get_ajax_posts');
add_action('wp_ajax_get_ajax_posts', 'get_ajax_posts');
	

	
/*
	Add in templates for bricklayer page
*/	
add_filter( 'template_include', 'template_loader' , 99);
	function template_loader( $template ) {
		$bricklayerSetting = get_post_meta( get_the_ID(), 'bricklayerSetting', true);
		
		if ($bricklayerSetting == 'true'){
			//If Bricklayer settings are true, add bricklayer
			include( plugin_dir_path( __FILE__ ) . 'bricklayer-functions.php');
		}else{
			//Else, use normal template
			return $template;
		}
		
		//Load Bricklayer templates
		$file = '';

			$file   = 'header-template.php'; // the name of your custom template
			$find[] = $file;
			$find[] = '' . $file; // name of folder it could be in, in user's theme

		if ( $file ) {
			$template       = locate_template( array_unique( $find ) );
			if ( ! $template ) { 
				// if not found in theme, will use your plugin version
				$template = untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/' . $file;
			}
		}
		loadBrickLayer();
		return $template;
	}

	
	
/* 
	Define bricklayer to Admin Pages 
	Adds in Radio button for on and off
	Adds in Post types to be used
	Adds in Numbers of posts to show each load
	Adds in Dropdown for sizes (default, small, medium and large sizes)
	
	
*/
	
	
/* Define the custom box to add bricklayer to Admin Page */
add_action( 'add_meta_boxes', 'wpse_61041_add_custom_box' );

/* Adds a box to the main column on the Post and Page edit screens */
function wpse_61041_add_custom_box() {
    add_meta_box( 
        'wpse_61041_sectionid',
        'Add Bricklayer',
        'wpse_61041_inner_custom_box',
        'page',
        'side',
        'high'
    );
}

/* Prints the box content */
function wpse_61041_inner_custom_box($post)
{
	
    // Use nonce for verification
    wp_nonce_field( 'wpse_61041_wpse_61041_field_nonce', 'wpse_61041_noncename' );

    // Get saved value, if none exists, "default" is selected
    $saved = get_post_meta( $post->ID, 'bricklayerSetting', true);
    if( !$saved )
        $saved = 'default';

    $fields = array(
        'true'      => __('On', 'wpse'),
        'default'   => __('Off', 'wpse'),
    );

    foreach($fields as $key => $label)
    {
        printf(
            '<input type="radio" name="bricklayerSetting" value="%1$s" id="bricklayerSetting[%1$s]" %3$s />'.
            '<label for="bricklayerSetting[%1$s]"> %2$s ' .
            '</label><br>',
            esc_attr($key),
            esc_html($label),
            checked($saved, $key, false)
        );
    }
	echo "<br><strong>Post Types</strong><br>";
		custom_meta_box( $post);
		
	echo "<br>";
		bricklayer_html($post);
		bricklayer_get_meta( 'bricklayer_number_of_posts_to_show' );
	}


	
	
/*
Add Bricklayer checkboxes to pages
This part adds what post types should be added in to the bricklayer at this page
*/
function custom_meta_box( $post )
{
    // Get post meta value using the key from our save function in the second paramater.
    $custom_meta = get_post_meta($post->ID, '_custom-meta-box', true);
	
	foreach ( get_post_types( '', 'names' ) as $post_type ) {
		//ignore list
        $ignore_post_types  =   array(
        'reply',
        'topic',
        'report',
		'attachment',
		'revision',
		'nav_menu_item',
        'status'  
        );
                                                    
        if(in_array($post_type, $ignore_post_types))
		continue;
	
		?>
		<input type="checkbox" name="custom-meta-box[]" value="<?php echo $post_type; ?>" <?php if($custom_meta) { echo (in_array($post_type, $custom_meta)) ? 'checked="checked"' : '';}; ?> /><?php echo ucfirst($post_type) ?><br>
		<?php
	}
    ?>
	<?php 
}

/* Display the post dropdown box. */

function bricklayer_get_meta( $value ) {
	global $post;

	$field = get_post_meta( $post->ID, $value, true );
	if ( ! empty( $field ) ) {
		return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
	} else {
		return false;
	}
}


function bricklayer_html( $post) {
	wp_nonce_field( '_bricklayer_nonce', 'bricklayer_nonce' ); ?>
	<p>
		<strong><label for="bricklayer_number_of_posts_to_show"><?php _e( 'Number of posts to show', 'bricklayer' ); ?></label></strong><br>
		<select name="bricklayer_number_of_posts_to_show" id="bricklayer_number_of_posts_to_show">
			<option <?php echo (bricklayer_get_meta( 'bricklayer_number_of_posts_to_show' ) === '4' ) ? 'selected' : '' ?>>4</option>
			<option <?php echo (bricklayer_get_meta( 'bricklayer_number_of_posts_to_show' ) === '8' ) ? 'selected' : '' ?>>8</option>
			<option <?php echo (bricklayer_get_meta( 'bricklayer_number_of_posts_to_show' ) === '12' ) ? 'selected' : '' ?>>12</option>
			<option <?php echo (bricklayer_get_meta( 'bricklayer_number_of_posts_to_show' ) === '16' ) ? 'selected' : '' ?>>16</option>
			<option <?php echo (bricklayer_get_meta( 'bricklayer_number_of_posts_to_show' ) === '20' ) ? 'selected' : '' ?>>20</option>
			<option <?php echo (bricklayer_get_meta( 'bricklayer_number_of_posts_to_show' ) === '24' ) ? 'selected' : '' ?>>24</option>
		</select>
	</p>
	<!--<p>
		<strong><label for="brickstyleCount"><?php _e( 'Brick Column Row', 'bricklayer' ); ?></label></strong><br>
		<select name="brickstyleCount" id="brickstyleCount">
			<option <?php echo (bricklayer_get_meta( 'brickstyleCount' ) === '2' ) ? 'selected' : '' ?>>2</option>
			<option <?php echo (bricklayer_get_meta( 'brickstyleCount' ) === '3' ) ? 'selected' : '' ?>>3</option>
			<option <?php echo (bricklayer_get_meta( 'brickstyleCount' ) === '4' ) ? 'selected' : '' ?>>4</option>
			<option <?php echo (bricklayer_get_meta( 'brickstyleCount' ) === '5' ) ? 'selected' : '' ?>>5</option>
		</select>
	</p>-->	
	<p>
		<strong><label for="brickstyle"><?php _e( 'brickstyle', 'bricklayer' ); ?></label></strong><br>
		<select name="brickstyle" id="brickstyle">
			<option <?php echo (bricklayer_get_meta( 'brickstyle' ) === 'RandomStyle' ) ? 'selected' : '' ?>>Default</option>
			<option <?php echo (bricklayer_get_meta( 'brickstyle' ) === 'Small' ) ? 'selected' : '' ?>>Small</option>
			<option <?php echo (bricklayer_get_meta( 'brickstyle' ) === 'Medium' ) ? 'selected' : '' ?>>Medium</option>
			<option <?php echo (bricklayer_get_meta( 'brickstyle' ) === 'Large' ) ? 'selected' : '' ?>>Large</option>
		</select>
	</p>	
	
	<?php
}
/*
Save the bricklayer dropdown options in pages
*/
add_action( 'save_post', 'bricklayer_save' );

function bricklayer_save( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! isset( $_POST['bricklayer_nonce'] ) || ! wp_verify_nonce( $_POST['bricklayer_nonce'], '_bricklayer_nonce' ) ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	if ( isset( $_POST['brickstyle'] ) )
		update_post_meta( $post_id, 'brickstyle', esc_attr( $_POST['brickstyle'] ) );
	
	//if ( isset( $_POST['brickstyleCount'] ) )
		//update_post_meta( $post_id, 'brickstyleCount', esc_attr( $_POST['brickstyleCount'] ) );
	
	
	if ( isset( $_POST['bricklayer_number_of_posts_to_show'] ) )
		update_post_meta( $post_id, 'bricklayer_number_of_posts_to_show', esc_attr( $_POST['bricklayer_number_of_posts_to_show'] ) );

	if ( isset($_POST['bricklayerSetting']) && $_POST['bricklayerSetting'] != "" ){
            update_post_meta( $post_id, 'bricklayerSetting', $_POST['bricklayerSetting'] );
      } 	
}


/*
Save the bricklayer options in pages
*/
add_action( 'save_post', 'save_custom_meta_box' );
function save_custom_meta_box()
{

    global $post;
    // Get our form field
    if(isset( $_POST['custom-meta-box'] ))
    {
        $custom = $_POST['custom-meta-box'];
        $old_meta = get_post_meta($post->ID, '_custom-meta-box', true);
        // Update post meta
        if(!empty($old_meta)){
            update_post_meta($post->ID, '_custom-meta-box', $custom);
        } else {
            add_post_meta($post->ID, '_custom-meta-box', $custom, true);
        }
    }
}



/**
 * Adds meta boxes to the post editing screen
   This part adds the bricklayer options of background color and direct link to posts pages

*/
function bricklayer_add_custom_meta() {
$post_types = get_post_types();
  foreach( $post_types as $post_type_name ) 
    {
    //ignore list
    $ignore_post_types  =   array(
    'reply',
    'topic',
    'report',
    'status'  
	);
                                                    
if(in_array($post_type_name, $ignore_post_types))
continue;

if(is_post_type_hierarchical($post_type_name))
	continue;
                                                        
    $post_type_data = get_post_type_object( $post_type_name );
    if($post_type_data->show_ui === FALSE)
    continue;
	
	add_meta_box( 'prfx_meta', __( 'Bricklayer Post Options', 'prfx-textdomain' ), 'bricklayer_custom_meta_callback', $post_type_name ,'side', 'high');
	}
}
add_action( 'add_meta_boxes', 'bricklayer_add_custom_meta' );


/**
 * Outputs the content of the meta box
 */
function bricklayer_custom_meta_callback( $post ) {
    wp_nonce_field( basename( __FILE__ ), 'prfx_nonce' );
    $prfx_stored_meta = get_post_meta( $post->ID );
    ?>
 
    <p>
        <label for="background-color" class="prfx-row-title"><?php _e( 'Background color (HEX)', 'prfx-textdomain0' )?></label><br>
        <input type="text" name="background-color" id="background-color" value="<?php if ( isset ( $prfx_stored_meta['background-color'] ) ) echo $prfx_stored_meta['background-color'][0]; ?>" />
    </p>
   <p>
        <label for="user-link" class="prfx-row-title"><?php _e( 'Direct Link (Add http://)', 'prfx-textdomain1' )?></label><br>
        <input type="text" name="user-link" id="user-link" value="<?php if ( isset ( $prfx_stored_meta['user-link'] ) ) echo $prfx_stored_meta['user-link'][0]; ?>" />
    </p>
 
    <?php
}


/**
 * Saves the custom meta input, Background color and user link
 */
function bricklayer_custom_meta_save( $post_id ) {
 
    // Checks save status
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 'prfx_nonce' ] ) && wp_verify_nonce( $_POST[ 'prfx_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';
 
    // Exits script depending on save status
    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return;
    }
 
    // Checks for input and sanitizes/saves if needed
    if( isset( $_POST[ 'background-color' ] ) || isset( $_POST[ 'user-link' ] )  ) {
        update_post_meta( $post_id, 'background-color', sanitize_text_field( $_POST[ 'background-color' ] ) );
		 update_post_meta( $post_id, 'user-link', sanitize_text_field( $_POST[ 'user-link' ] ) );
    }
 
}
add_action( 'save_post', 'bricklayer_custom_meta_save' );




/* Load up Bricklayer If IE, Add columns instead since lack of support*/
function loadBrickLayer(){

if (preg_match('~MSIE|Internet Explorer~i', $_SERVER['HTTP_USER_AGENT']) || preg_match('/Edge/i', $_SERVER['HTTP_USER_AGENT']) || (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident/7.0; rv:11.0') !== false)) { 

		add_action( 'wp_enqueue_scripts', 'bricklayer_ie_scripts' );
  
	}else{ 
		add_action('wp_head','register_script_bricklayer_scripts');
		add_action('wp_head','bricklayer_scripts_loaded');
	}	
}	


function bricklayer_ie_scripts() {
	$plugin_dir_uri = plugin_dir_url( __FILE__ );
	wp_enqueue_script( 'bricklayer-script2', $plugin_dir_uri . 'js/custom_js.js', array(), '1.0.0', true );
	wp_enqueue_style( 'bricklayer-script3', $plugin_dir_uri . 'css/ie.css');
	wp_enqueue_style( 'bricklayer-script4', $plugin_dir_uri . 'css/grid.css');
}

