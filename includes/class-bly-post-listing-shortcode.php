<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'BLY_Post_Listing_Shortcode' ) ) :

/**
 * Main BLY_Post_Listing_Shortcode Class.
 *
 * @class BLY_Post_Listing_Shortcode
 * @version	1.0.0
 */
class BLY_Post_Listing_Shortcode {

	/**
	 * The single instance of the class.
	 *
	 * @var BLY_Post_Listing_Shortcode
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main BLY_Post_Listing_Shortcode Instance.
	 *
	 * Ensures only one instance of BLY_Post_Listing_Shortcode is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see instantiate_pressfixer_customization()
	 * @return BLY_Post_Listing_Shortcode - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * BLY_Post_Listing_Shortcode Constructor.
	 */
	public function __construct() {
		add_shortcode( 'bly_infinite_post_listing', array( $this, 'bly_infinite_post_listing_content' ) );
		add_action( 'wp_ajax_nopriv_bly_load_more_post_listing', array( $this, 'bly_load_more_post_listing' ) );
		add_action( 'wp_ajax_bly_load_more_post_listing', array( $this, 'bly_load_more_post_listing' ) );
	}

	public function bly_load_more_post_listing( $atts = array() ) {
		ob_start();
		$offset = isset( $atts['offset'] ) ? intval( $atts['offset'] ) : 0;
		$offset = isset( $_POST['offset'] ) ? intval( $_POST['offset'] ) : $offset;
		$posts_per_page = isset( $atts['posts_per_page'] ) ? intval( $_POST['posts_per_page'] ) : 10;
		$render_content = isset( $_POST['render_content'] ) ? true : false;
		$atts = array(
			'posts_per_page' => $posts_per_page,
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
			$post_thumbnail_url = get_the_post_thumbnail_url( $post_id, 'full' );
			
			$post_categories = $this->get_post_primary_category( $post_id, 'category' );
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

	public function bly_infinite_post_listing_content( $atts = array() ) {
		ob_start();
		echo '<div class="bly-posts-list-section">';
			echo '<input type="hidden" id="bly-posts-list-offset" value="0" />';
			echo $this->bly_load_more_post_listing( $atts );
		echo '</div>';
		$show_load_more = isset( $atts['show_load_more'] ) ? $atts['show_load_more'] : 'no';
		if( 'yes' === $show_load_more ) {
			?>
			<div class="bly-load-more-btn-wrapper">
				<button class="button bly-load-more-btn"><?php _e( 'Load More', 'generatepress' ); ?></button>
			</div>
			<?php
		}
		$content = ob_get_clean();
		return $content;
	}

	public function get_post_primary_category( $post_id, $term='category', $return_all_categories = false ) {
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
	
}

endif;

/**
 * Main instance of BLY_Post_Listing_Shortcode.
 */
BLY_Post_Listing_Shortcode::instance();