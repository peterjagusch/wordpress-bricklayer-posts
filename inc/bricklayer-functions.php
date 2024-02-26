<?php
 
function bricklayer_scripts_loaded() {
		echo  '<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bricklayer/0.2.4/bricklayer.min.css">';
		echo  '<script src="//cdnjs.cloudflare.com/ajax/libs/bricklayer/0.2.4/bricklayer.min.js"></script>';

		echo "<script>var scriptsLoaded = true;";
		echo "var doOnce = 0;";
		echo "jQuery( document ).ajaxComplete(function() {";
		echo " if(doOnce < 1){ ";
		echo " var bricklayer = new Bricklayer(document.querySelector('.bricklayer'));";
		echo " doOnce = doOnce+1;} ";
		echo "});</script>";

}

function register_script_bricklayer_scripts() {
		$plugin_dir_uri = plugin_dir_url( __FILE__ );
		wp_enqueue_script( 'bricklayer-script', $plugin_dir_uri . 'js/bricklayer.min.js', array(), '1.0.0', true );
		wp_enqueue_script( 'bricklayer-script2', $plugin_dir_uri . 'js/custom_js.js', array(), '1.0.0', true );
		wp_enqueue_style( 'bricklayer-script3', $plugin_dir_uri . 'css/grid.css');
		wp_enqueue_style( 'bricklayer-script4', $plugin_dir_uri . 'css/bricklayer.min.css');
		wp_register_script('my_amazing_script', plugins_url('js/bricklayer.min.js', __FILE__), array('jquery'),'1.1', true);
		wp_register_script('my_amazing_script2', plugins_url('js/custom_js.js', __FILE__), array('jquery'),'1.1', true);
		wp_enqueue_script('my_amazing_script');
		wp_enqueue_script('my_amazing_script2');
}
 