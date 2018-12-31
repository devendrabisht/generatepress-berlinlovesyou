<?php

include_once( 'includes/class-bly-widget-customization.php' );
include_once( 'includes/class-bly-post-listing-shortcode.php' );

if( ! function_exists( 'bly_generate_hamberger_icon_in_header' ) ) {
	add_action( 'generate_before_header_content', 'bly_generate_hamberger_icon_in_header', 10 );
	function bly_generate_hamberger_icon_in_header() {
		echo '<a href="#" class="bly-gp-menu-open"><i class="fa fa-bars"></i></a>';
	}
}

if( ! function_exists( 'bly_nav_menu_item_title_show_desc' ) ) {
	add_filter( 'nav_menu_item_title', 'bly_nav_menu_item_title_show_desc', 10, 4 );
	function bly_nav_menu_item_title_show_desc( $title, $item, $args, $depth ) {
		if( ! empty( $item->description ) ) {
			$title .= '<span class="bly-menu-desc">' . $item->description . '</span>';
		}
		return $title;
	}
}

if ( ! function_exists( 'bly_generate_right_slide_in_content' ) ) {
	add_action( 'generate_header', 'bly_generate_right_slide_in_content' );
	function bly_generate_right_slide_in_content() {
		?>
		<div class="bly-gp-menu-overlay"></div>
		<a href="#" class="bly-gp-menu-open">Open Menu</a>
		<div class="bly-gp-side-menu-wrapper">
			<a href="#" class="bly-gp-menu-close">×</a>
			<div class="bly-gp-side-menu-content">
				<?php if ( is_active_sidebar( 'right-slide-in' ) ) : ?>
					<?php dynamic_sidebar( 'right-slide-in' ); ?>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'bly_generate_widgets_init' ) ) {
	add_action( 'widgets_init', 'bly_generate_widgets_init' );
	/**
	 * Register widgetized area and update sidebar with default widgets
	 */
	function bly_generate_widgets_init() {
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

if( ! function_exists( 'bly_enqueue_script_and_style' ) ) {
	add_action( 'wp_enqueue_scripts', 'bly_enqueue_script_and_style' );
	function bly_enqueue_script_and_style() {
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
}