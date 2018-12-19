<?php

add_action( 'wp_nav_menu_item_custom_fields', '_fields', 10, 4 );
function _fields( $id, $item, $depth, $args ) {
	die("COOL");
	// foreach ( self::$fields as $_key => $label ) :
	$_key = 'field-01';
	$label = __( 'Custom Field #1', 'menu-item-custom-fields-example' );
	$key   = sprintf( 'menu-item-%s', $_key );
	$id    = sprintf( 'edit-%s-%s', $key, $item->ID );
	$name  = sprintf( '%s[%s]', $key, $item->ID );
	$value = get_post_meta( $item->ID, $key, true );
	$class = sprintf( 'field-%s', $_key );
	?>
	<p class="description description-wide <?php echo esc_attr( $class ) ?>">
		<?php printf(
			'<label for="%1$s">%2$s<br /><input type="text" id="%1$s" class="widefat %1$s" name="%3$s" value="%4$s" /></label>',
			esc_attr( $id ),
			esc_html( $label ),
			esc_attr( $name ),
			esc_attr( $value )
		) ?>
	</p>
	<?php
	// endforeach;
}

/*
 * Saves new field to postmeta for navigation
 */
// add_action('wp_update_nav_menu_item', 'custom_nav_update',10, 3);
function custom_nav_update($menu_id, $menu_item_db_id, $args ) {
    if ( is_array($_REQUEST['menu-item-custom']) ) {
        $custom_value = $_REQUEST['menu-item-custom'][$menu_item_db_id];
        update_post_meta( $menu_item_db_id, '_menu_item_custom', $custom_value );
    }
}

/*
 * Adds value of new field to $item object that will be passed to     Walker_Nav_Menu_Edit_Custom
 */
// add_filter( 'wp_setup_nav_menu_item','custom_nav_item' );
function custom_nav_item($menu_item) {
    $menu_item->custom = get_post_meta( $menu_item->ID, '_menu_item_custom', true );
    return $menu_item;
}

/**
 * Add custom fields to $item nav object
 * in order to be used in custom Walker
 *
 * @access      public
 * @since       1.0 
 * @return      void
*/
// add custom menu fields to menu
// add_filter( 'wp_setup_nav_menu_item', 'rc_scm_add_custom_nav_fields' );
function rc_scm_add_custom_nav_fields( $menu_item ) {

    $menu_item->subtitle = get_post_meta( $menu_item->ID, '_menu_item_subtitle', true );
    return $menu_item;

}

/**
 * Save menu custom fields
 *
 * @access      public
 * @since       1.0 
 * @return      void
*/
// save menu custom fields
// add_action( 'wp_update_nav_menu_item', 'rc_scm_update_custom_nav_fields', 10, 3 );
function rc_scm_update_custom_nav_fields( $menu_id, $menu_item_db_id, $args ) {

    // Check if element is properly sent
    if ( is_array( $_REQUEST['menu-item-subtitle']) ) {
        $subtitle_value = $_REQUEST['menu-item-subtitle'][$menu_item_db_id];
        update_post_meta( $menu_item_db_id, '_menu_item_subtitle', $subtitle_value );
    }

}




add_action( 'in_widget_form', 'bly_in_widget_form', 5, 3 );
function bly_in_widget_form( $thisRef, $return, $instance ){
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

add_filter( 'widget_update_callback', 'bly_widget_update_callback', 5, 3 );
function bly_widget_update_callback( $instance, $new_instance, $old_instance ) {
    $instance['bly_custom_css_class'] = strip_tags( $new_instance['bly_custom_css_class'] );
    return $instance;
}

add_filter( 'dynamic_sidebar_params', 'bly_dynamic_sidebar_params' );
function bly_dynamic_sidebar_params( $params ) {
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


function get_post_primary_category($post_id, $term='category', $return_all_categories=false){
	$return = array();

	if (class_exists('WPSEO_Primary_Term')){
        // Show Primary category by Yoast if it is enabled & set
		$wpseo_primary_term = new WPSEO_Primary_Term( $term, $post_id );
		$primary_term = get_term($wpseo_primary_term->get_primary_term());

		if (!is_wp_error($primary_term)){
			$return['primary_category'] = $primary_term;
		}
	}

	if (empty($return['primary_category']) || $return_all_categories){
		$categories_list = get_the_terms($post_id, $term);

		if (empty($return['primary_category']) && !empty($categories_list)){
            $return['primary_category'] = $categories_list[0];  //get the first category
        }
        if ($return_all_categories){
        	$return['all_categories'] = array();

        	if (!empty($categories_list)){
        		foreach($categories_list as &$category){
        			$return['all_categories'][] = $category->term_id;
        		}
        	}
        }
    }

    return $return;
}

//custom hooks to save active filters
add_action( 'wp_ajax_nopriv_bly_load_more_post_listing', 'bly_load_more_post_listing' );
add_action( 'wp_ajax_bly_load_more_post_listing', 'bly_load_more_post_listing' );
function bly_load_more_post_listing( $atts = array() ) {
	ob_start();
	$offset = isset( $_POST['offset'] ) ? intval( $_POST['offset'] ) : 0;
	$render_content = isset( $_POST['render_content'] ) ? true : false;
	$atts = array(
		'posts_per_page' => 10,
		'offset' => $offset,
	);
	$args = array(
		'posts_per_page'   => $atts['posts_per_page'],
		'offset'           => $atts['offset'],
		'post_type'        => 'post',
		'post_status'      => 'publish',
	);
	$posts_array = get_posts( $args );
	foreach ( $posts_array as $key => $post ) {
		$post_id = $post->ID;
		
		$post_title = get_the_title( $post_id );
		$post_permalink = get_the_permalink( $post_id );
		$post_date = get_the_date( '', $post_id );
		$post_thumbnail_url = get_the_post_thumbnail_url( $post_id, 'post-thumbnail' );
		
		$post_categories = get_post_primary_category( $post_id, 'category' );
		$primary_category = $post_categories['primary_category'];
		$primary_category_name = $primary_category->name;
		$post_category = $primary_category_name;
		?>
		<div class="post-content-block">
			<a href="<?php echo $post_permalink; ?>" class="post-permalink">
				<div class="left-block">
					<div class="post-thumbnail">
						<img src="<?php echo $post_thumbnail_url; ?>" alt="<?php echo $post_title; ?>" />
					</div>
				</div>
				<div class="right-block">
					<div class="post-category">
						<?php echo $post_category; ?>
					</div>
					<div class="post-title">
						<h3><?php echo $post_title; ?></h3>
					</div>
					<div class="post-date">
						<?php echo $post_date; ?>
					</div>
				</div>
			</a>
		</div>
		<?php
	}
	$content = ob_get_clean();
	if( $render_content ) {
		echo $content;
		wp_die();
	}
	else {
		return $content;
	}
}

add_shortcode( 'bly_infinite_post_listing', 'bly_infinite_post_listing_content' );
function bly_infinite_post_listing_content( $atts = array() ) {
	ob_start();
	echo '<div class="bly-posts-list-section">';
		echo '<input type="hidden" id="bly-posts-list-offset" value="0" />';
		echo bly_load_more_post_listing();
	echo '</div>';
	$content = ob_get_clean();
	return $content;
}

add_action( 'wp_enqueue_scripts', 'berlinlovesyou_generate_enqueue_scripts' );
function berlinlovesyou_generate_enqueue_scripts() {
    if ( is_child_theme() ) {
        // load parent stylesheet first if this is a child theme
		wp_enqueue_style( 'parent-stylesheet', trailingslashit( get_template_directory_uri() ) . 'style.css', false );
    }
    // load active theme stylesheet in both cases
    wp_enqueue_style( 'theme-stylesheet', get_stylesheet_uri(), false );
    wp_enqueue_script( 'berlinlovesyou-script', trailingslashit( get_stylesheet_directory_uri() ) . 'assets/js/berlinlovesyou.js', array( 'jquery' ), time(), true );
    wp_localize_script( 
    	'berlinlovesyou-script', 
    	'berlinlovesyou_script_ajax', 
    	array(
    		'ajax_url' => admin_url( 'admin-ajax.php' )
    	)
    );
}


if ( ! function_exists( 'berlinlovesyou_generate_right_in_content' ) ) {
	add_action( 'generate_header', 'berlinlovesyou_generate_right_in_content' );
	/**
	 * Build the header.
	 *
	 * @since 1.3.42
	 */
	function berlinlovesyou_generate_right_in_content() {
		?>
		<div class="bly-gp-menu-overlay"></div>
		<a href="#" class="bly-gp-menu-open">Open Menu</a>

		<div class="bly-gp-side-menu-wrapper">
			<a href="#" class="bly-gp-menu-close">×</a>

			<div class="bly-gp-side-menu-content">
				<?php if ( is_active_sidebar( 'right-slide-in' ) ) : ?>
					<!-- <div class="right-slide-in-widget"> -->
						<?php dynamic_sidebar( 'right-slide-in' ); ?>
					<!-- </div> -->
				<?php endif; ?>
			</div>
			<!-- <ul>
				<li><a href="http://www.google.com" target="_blank" rel="nofollow">Google Search</a></li>
				<li><a href="http://www.yahoo.com" target="_blank" rel="nofollow">Yahoo Search</a></li>
				<li><a href="http://www.facebook.com" target="_blank" rel="nofollow">Facebook</a></li>
				<li><a href="http://www.flickr.com" target="_blank" rel="nofollow">Flickr</a></li>

			</ul> -->
		</div>
		<?php
	}
}

if ( ! function_exists( 'berlinlovesyou_generate_widgets_init' ) ) {
	add_action( 'widgets_init', 'berlinlovesyou_generate_widgets_init' );
	/**
	 * Register widgetized area and update sidebar with default widgets
	 */
	function berlinlovesyou_generate_widgets_init() {
		$widgets = array(
			'right-slide-in' => __( 'Right Slide In', 'generatepress' ),
		);

		foreach ( $widgets as $id => $name ) {
			register_sidebar( array(
				'name'          => $name,
				'id'            => $id,
				'before_widget' => '<aside id="%1$s" class="widget inner-padding %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => apply_filters( 'generate_start_widget_title', '<h2 class="widget-title">' ),
				'after_title'   => apply_filters( 'generate_end_widget_title', '</h2>' ),
			) );
		}
	}
}
