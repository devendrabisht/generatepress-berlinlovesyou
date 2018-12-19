<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'BLY_Widget_Customization' ) ) :

/**
 * Main BLY_Widget_Customization Class.
 *
 * @class BLY_Widget_Customization
 * @version	1.0.0
 */
class BLY_Widget_Customization {

	/**
	 * The single instance of the class.
	 *
	 * @var BLY_Widget_Customization
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main BLY_Widget_Customization Instance.
	 *
	 * Ensures only one instance of BLY_Widget_Customization is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see instantiate_pressfixer_customization()
	 * @return BLY_Widget_Customization - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * BLY_Widget_Customization Constructor.
	 */
	public function __construct() {
		add_action( 'in_widget_form', array( $this, 'bly_in_widget_form' ), 5, 3 );
		add_filter( 'widget_update_callback', array( $this, 'bly_widget_update_callback' ), 5, 3 );
		add_filter( 'dynamic_sidebar_params', array( $this, 'bly_dynamic_sidebar_params' ), 10, 1 );
	
	}


	public function bly_in_widget_form( $thisRef, $return, $instance ){
	    $instance = wp_parse_args(
	    	(array) $instance,
	    	array( 'bly_custom_css_class' => '' )
	    );
	    if ( ! isset( $instance['bly_custom_css_class'] ) ) {
	    	$instance['bly_custom_css_class'] = '';
	    }
	    ?>
	    <p>
	        <label for="<?php echo $thisRef->get_field_id('bly_custom_css_class'); ?>"><?php _e( 'Custom CSS Class(s)', 'TEXTDOMAIN' ); ?>:</label>
	        <input id="<?php echo $thisRef->get_field_id('bly_custom_css_class'); ?>" name="<?php echo $thisRef->get_field_name('bly_custom_css_class'); ?>" type="text" value="<?php echo $instance['bly_custom_css_class'];?>" />
	    </p>
	    <?php
	}

	public function bly_widget_update_callback( $instance, $new_instance, $old_instance ) {
	    $instance['bly_custom_css_class'] = strip_tags( $new_instance['bly_custom_css_class'] );
	    return $instance;
	}

	public function bly_dynamic_sidebar_params( $params ) {
	    global $wp_registered_widgets;
	    $widget_id = $params[0]['widget_id'];
	    $widget_obj = $wp_registered_widgets[$widget_id];
	    $widget_opt = get_option( $widget_obj['callback'][0]->option_name );
	    $widget_num = $widget_obj['params'][0]['number'];
	    if ( isset( $widget_opt[$widget_num]['bly_custom_css_class'] ) ) {
	    	$bly_custom_css_class = $widget_opt[$widget_num]['bly_custom_css_class'];
	        $params[0]['before_widget'] = preg_replace( '/class="/', 'class="'.$bly_custom_css_class, $params[0]['before_widget'], 1 );
	    }
	    return $params;
	}
}

endif;

/**
 * Main instance of BLY_Widget_Customization.
 */
BLY_Widget_Customization::instance();