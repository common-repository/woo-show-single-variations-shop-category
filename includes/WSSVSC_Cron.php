<?php
/**
* This class is loaded on the back-end since its main job is
* to display the Admin to box.
*/
class WSSVSC_Cron {
	
	public function __construct () {
		add_action( 'init', array( $this, 'init' ) );
	}
	public function init(){
		$argsa = array(
							'post_type' => 'product',
							'posts_per_page' => 200,
							'meta_query' => array(
												array(
														'key' => '_wssvsc_excludeenalls',
														'value' => 'yes',
														'compare' => 'NOT EXISTS'
													)
											),
		);
		$the_querya = new WP_Query( $argsa );
		if ( $the_querya->have_posts() ) {
				while ( $the_querya->have_posts() ) {
					
					$the_querya->the_post();
					global $post;
					$variation_id = $post->ID;
					if (!metadata_exists( 'post', $variation_id, '_wssvsc_excludeenalls' ) ) {
						update_post_meta( $variation_id, '_wssvsc_excludeenalls', 'no' );
					}
					if (!metadata_exists( 'post', $variation_id, '_wwsvsc_exclude_product_parent' ) ) {
						update_post_meta( $variation_id, '_wwsvsc_exclude_product_parent', 'no' );
					}
					
					/*if (!metadata_exists( 'post', $variation_id, '_wwsvsc_exclude_product_parent' ) ) {
						update_post_meta( $variation_id, '_wwsvsc_exclude_product_parent', '' );
					}*/
				}
		}
		$args = array(
					'post_type' => 'product_variation',
					'posts_per_page' => 200,
					'meta_query' => array(
												array(
														'key' => '_wssvsc_excludeenalls',
														'value' => 'yes',
														'compare' => 'NOT EXISTS'
													)
											),
			);
		$the_query = new WP_Query( $args );
		if ( $the_query->have_posts() ) {
			while ( $the_query->have_posts() ) {
				
				$the_query->the_post();
				global $post;
				$variation_id = $post->ID;
				$producta = wc_get_product( $variation_id );
				foreach( $producta->get_variation_attributes() as $taxonomya => $terms_sluga ){
					wp_set_post_terms( $variation_id, $terms_sluga, ltrim($taxonomya,'attribute_') );
				}
				$parent_product_id = wp_get_post_parent_id( $variation_id );
				if( $parent_product_id ) {
				
					$taxonomies = array(
						'product_cat',
						'product_tag'
					);
					foreach( $taxonomies as $taxonomy ) {
						$terms = (array) wp_get_post_terms( $parent_product_id, $taxonomy, array("fields" => "ids") );
						wp_set_post_terms( $variation_id, $terms, $taxonomy );
					}
					if ( !metadata_exists( 'post', $variation_id, '_wssvsc_excludeenalls' ) ) {
								update_post_meta( $variation_id, '_wssvsc_excludeenalls', 'no' );
					}
					if ( !metadata_exists( 'post', $variation_id, 'gmwsvs_child' ) ) {
								update_post_meta( $variation_id, 'gmwsvs_child', 'yes' );
					}
					if ( !metadata_exists( 'post', $variation_id, '_wwsvsc_exclude_product_parent' ) ) {
								update_post_meta( $variation_id, '_wwsvsc_exclude_product_parent', 'no' );
					}
					//if ( !metadata_exists( 'post', $variation_id, '_wwsvsc_exclude_product_single' ) ) {
								//update_post_meta( $variation_id, '_wwsvsc_exclude_product_single', 'no' );
					//}
					
					if ( !metadata_exists( 'post', $parent_product_id, 'gmwsvs_parent' ) ) {
								update_post_meta( $parent_product_id, 'gmwsvs_parent', 'yes' );
					}
				}
			}
		}
	}
}