<?php
/**
 * Best Seller Products Widget.
 * @extends  WC_Widget
 */
if ( class_exists( 'WC_Widget' ) ) {
	add_action('widgets_init', 'homemarket_best_seller_load_widgets');

	function homemarket_best_seller_load_widgets() {
		register_widget( 'Homemarket_Best_Seller_Widget' );
	}

	class Homemarket_Best_Seller_Widget extends WC_Widget {

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->widget_cssclass    = 'woocommerce widget_best_seller_products';
			$this->widget_description = __( 'Display a list of your best seller products on your site.', 'homemarket' );
			$this->widget_id          = 'woocommerce_best_seller_products';
			$this->widget_name        = __( 'Homemarket: Best Seller Products', 'homemarket' );
			$this->settings           = array(
				'title'  => array(
					'type'  => 'text',
					'std'   => __( 'Best Sellers', 'homemarket' ),
					'label' => __( 'Title', 'homemarket' )
				),
				'number' => array(
					'type'  => 'number',
					'step'  => 1,
					'min'   => 1,
					'max'   => '',
					'std'   => 5,
					'label' => __( 'Number of products to show', 'homemarket' )
				)
			);

			parent::__construct();
		}

		/**
		 * Output widget.
		 *
		 * @see WP_Widget
		 *
		 * @param array $args
		 * @param array $instance
		 */
		public function widget( $args, $instance ) {

			if ( $this->get_cached_widget( $args ) ) {
				return;
			}

			ob_start();

			$number = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : $this->settings['number']['std'];

			//add_filter( 'posts_clauses',  array( WC()->query, 'order_by_rating_post_clauses' ) );

			$query_args = array( 
				'posts_per_page' => $number, 
				'no_found_rows' => 1, 
				'post_status' => 'publish', 
				'post_type' => 'product', 
				'orderby' => 'meta_value_num',
				'meta_key' => 'total_sales' 
			);

			$query_args['meta_query'] = WC()->query->get_meta_query();

			$r = new WP_Query( $query_args );

			if ( $r->have_posts() ) {

				$this->widget_start( $args, $instance );

				echo '<ul class="product_list_widget">';

				while ( $r->have_posts() ) {
					$r->the_post();
					wc_get_template( 'content-widget-product.php', array( 'show_rating' => true ) );
				}

				echo '</ul>';

				$this->widget_end( $args );
			}

			wp_reset_postdata();

			$content = ob_get_clean();

			echo $content;

			$this->cache_widget( $args, $content );
		}
	}
}
