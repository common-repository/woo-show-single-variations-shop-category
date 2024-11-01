<?php
/**
 * This class is loaded on the back-end since its main job is 
 * to display the Admin to box.
 */
class WSSVSC_Admin {
	
	public function __construct () {
		add_action( 'admin_enqueue_scripts', array( $this, 'WSSVSC_enqueue_select2_jquery' ) );
		add_action( 'admin_init', array( $this, 'WSSVSC_register_settings' ) );
		add_action( 'admin_menu', array( $this, 'WSSVSC_admin_menu' ) );
		add_filter( 'woocommerce_product_data_tabs', array( $this, 'WSSVSC_custom_product_tabs' ) );
		add_filter( 'woocommerce_product_data_panels', array( $this, 'WSSVSC_custom_product_panels' ) );
		add_action( 'woocommerce_process_product_meta', array( $this, 'WSSVSC_custom_save' ) );
		add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'WSSVSC_add_variation_settings_fields' ), 2000, 3 );
		add_action( 'woocommerce_save_product_variation', array( $this, 'WSSVSC_woo_add_custom_variation_fields_save' ), 10, 2 );
		add_action('wp', array( $this, 'WSSVSC_wp'), 10 );
		if ( is_admin() ) {
			return;
		}
		
	}
	public function WSSVSC_enqueue_select2_jquery() {
		 /*wp_register_style( 'WSSVSCselect2css', WSSVSC_PLUGINURL.'/css/select2.css', false, '1.0', 'all' );
		 wp_register_script( 'WSSVSCselect2', WSSVSC_PLUGINURL.'/js/select2.js', array( 'jquery' ), '1.0', true );
		 wp_enqueue_style( 'WSSVSCselect2css' );
		wp_enqueue_script( 'WSSVSCselect2' );*/
	}
	
	public function WSSVSC_wp() {
		
	}

	

	public function WSSVSC_add_variation_settings_fields( $loop, $variation_data, $variation_post ) {
		echo "<div style='background-color: #eee;padding: 5px 18px;'>";
		woocommerce_wp_text_input( array(
		    'id'            => '_wssvsc_custom_name'.$variation_post->ID,
		    'name'          => '_wssvsc_custom_name['.$variation_post->ID.']',
		    'value'         => get_post_meta( $variation_post->ID, '_wssvsc_custom_name', true ),
		    'type'          => 'text',
		    'custom_attributes' => array( 'disabled' => 'disabled' ),
		    'label'         => __( 'Variation Single Product For Custom Name', 'woocommerce' ),
		    'description'   => __( 'This Option support for just Work for <strong>Show Variations As Single Product</strong> . <em>For this feature you need to by <a href="https://www.codesmade.com/store/show-variations-on-shop-category-woocommerce-pro/"  target="_blank">Pro Version</a></em>', 'woocommerce' ),
		 	'wrapper_class' => 'form-row form-row-full',
		) );
		$_wssvsc_excludeenalls = get_post_meta( $variation_post->ID, '_wssvsc_excludeenalls', true );
	    ?>
	    <label class="tips" data-tip="<?php esc_html_e( 'Exclude Variation in shop and category page', 'woocommerce' ); ?>">
							<?php echo __( '&nbsp;&nbsp; Exclude Variation ', 'woocommerce' ); ?>
							<input type="checkbox" disabled class="checkbox" value='yes' name="_wssvsc_excludeenalls[<?php echo $variation_post->ID; ?>]" <?php checked( $_wssvsc_excludeenalls=='yes', true );  ?>/>
							<em>For this feature you need to by <a href="https://www.codesmade.com/store/show-variations-on-shop-category-woocommerce-pro/" target="_blank">Pro Version</a></em>
		</label>
		<?php
		$args = array(
		    'hide_empty' => false,
		);

		$product_categories = get_terms( 'product_cat', $args );
		$curre_product_categories = wp_get_post_terms($variation_post->ID, 'product_cat', array('fields' => 'ids'));
		
		?>
		<p class="form-field">
		<label><?php _e( 'Variation Category', 'woocommerce' ); ?></label>
		<select name="variation_cat[<?php echo $variation_post->ID;?>][]" class="js-bg-basic-multiple" multiple="multiple" data-placeholder="<?php _e( 'Search Product Category', 'woocommerce' ); ?>">
			<?php
			foreach ($product_categories as $key_categories => $value_categories) {
				echo '<option value="'.$value_categories->term_id.'" '.((in_array($value_categories->term_id, $curre_product_categories))?'selected':'').'>'.$value_categories->name.'</option>';
			}
			?>
		</select> 
		</p>
		<?php
		$product_tag = get_terms( 'product_tag', $args );
		$curre_product_tag = wp_get_post_terms($variation_post->ID, 'product_cat', array('fields' => 'ids'));
		?>
		<p class="form-field">
		<label><?php _e( 'Variation Tag', 'woocommerce' ); ?></label>
		<select name="variation_tag[<?php echo $variation_post->ID;?>][]" class="js-bg-basic-multiple" multiple="multiple" data-placeholder="<?php _e( 'Search Product Tag', 'woocommerce' ); ?>">
			<?php
			foreach ($product_tag as $key_tag => $value_tag) {
				echo '<option value="'.$value_tag->term_id.'" '.((in_array($value_tag->term_id, $curre_product_tag))?'selected':'').'>'.$value_tag->name.'</option>';
			}
			?>
		</select> 
		</p>
		<?php
		/*$producta = wc_get_product( $variation_post->ID );
		echo "<pre>";
		print_r($producta->get_variation_attributes());
		echo "</pre>";*/
		?>
		<style type="text/css">
			.js-bg-basic-multiple{
				width: 100%;
			}
		</style>
		<script>
			jQuery(document).ready(function() {
    
			    jQuery('.js-bg-basic-multiple').select2();
			});
		</script>
	    <?php

	    echo "</div>";
	}

	public function WSSVSC_woo_add_custom_variation_fields_save( $post_id ){
	 	
		$producta = wc_get_product( $post_id );

		foreach( $producta->get_variation_attributes() as $taxonomya => $terms_sluga ){
			wp_set_post_terms( $post_id, $terms_sluga, ltrim($taxonomya,'attribute_') );
		}
		
		if ( !metadata_exists( 'post', $post_id, '_wssvsc_excludeenalls' ) ) {
			$parent_product_id = wp_get_post_parent_id( $post_id );
	        if( $parent_product_id ) {
				$taxonomies = array(
	                'product_cat',
	                'product_tag'
	            );
				foreach( $taxonomies as $taxonomy ) {
					$terms = (array) wp_get_post_terms( $parent_product_id, $taxonomy, array("fields" => "ids") );
	                wp_set_post_terms( $post_id, $terms, $taxonomy );
				 }
				update_post_meta( $post_id, 'gmwsvs_is_tax_setup', 'yes' );
			}
	    }else{
	    	
	        wp_set_post_terms( $post_id, $_POST['variation_cat'][ $post_id ], 'product_cat' );
	        wp_set_post_terms( $post_id, $_POST['variation_tag'][ $post_id ], 'product_tag' );
	    }

        $_wssvsc_custom_name = $_POST['_wssvsc_custom_name'][ $post_id ];
		update_post_meta( $post_id, '_wssvsc_custom_name', esc_attr( $_wssvsc_custom_name ) );
		$_wssvsc_excludeenalls = $_POST['_wssvsc_excludeenalls'][ $post_id ];
		update_post_meta( $post_id, '_wssvsc_excludeenalls', esc_attr( $_wssvsc_excludeenalls ) );
	}

	public function WSSVSC_custom_product_tabs( $tabs) {
		$tabs['wwsvsc_tab'] = array(
			'label'		=> __( 'Single Variation', 'wssvsc' ),
			'target'  =>  'wwsvsc_tab_content',
	        'priority' => 60,
	        'class'   => array()
		);
		return $tabs;
	}

	public function WSSVSC_custom_product_panels() {
		global $post;
		?>
		<div id='wwsvsc_tab_content' class='panel woocommerce_options_panel'>
			<div class='options_group'>
				<?php
					woocommerce_wp_checkbox( array(
						'id' 		=> '_wwsvsc_exclude_product_single',
						'label' 	=> __( 'Exclude Single Variation', 'wssvsc' ),
						'custom_attributes' => array( 'disabled' => 'disabled' ),
						'description'   => __( 'Enable this option to exclude single variation on shop & category pages. <br/><em>For this feature you need to by <a href="https://www.codesmade.com/store/show-variations-on-shop-category-woocommerce-pro/" target="_blank">Pro Version</a></em>', 'wssvsc' ) 
					) );
				?>
				<?php
					woocommerce_wp_checkbox( array(
						'id' 		=> '_wwsvsc_exclude_product_parent',
						'label' 	=> __( 'Hide Parent Variable Product', 'wssvsc' ),
						'custom_attributes' => array( 'disabled' => 'disabled' ),
						'description'   => __( 'Enable this option to Hide parent variation on shop & category pages.<br/>  <em>For this feature you need to by <a href="https://www.codesmade.com/store/show-variations-on-shop-category-woocommerce-pro/" target="_blank">Pro Version</a></em>', 'wssvsc' ) 
					) );
				?>
		</div>
	</div>
		<?php
	}

	public function WSSVSC_custom_save( $post_id ) {
	
		$wwsvsc_exclude_product_single = isset( $_POST['_wwsvsc_exclude_product_single'] ) ? 'yes' : 'no';
		update_post_meta( $post_id, '_wwsvsc_exclude_product_single', $wwsvsc_exclude_product_single );

		$wwsvsc_exclude_product_parent = isset( $_POST['_wwsvsc_exclude_product_parent'] ) ? 'yes' : 'no';
		update_post_meta( $post_id, '_wwsvsc_exclude_product_parent', $wwsvsc_exclude_product_parent );

		//update_post_meta( $post_id, '_wssvsc_excludeenalls','yes' );
	}
	public function WSSVSC_admin_menu () {

		add_options_page('Woo Variation Settings', 'Woo Variation Settings', 'manage_options', 'WSSVSC', array( $this, 'WSSVSC_page' ));
	}

	public function WSSVSC_page() {

	
	?>
	

	<div>
	   
	   <h2><?php _e('WooCommerce Shop & Category Setting', 'gmwsvs'); ?></h2>
	   <div class="about-text">
	        <p>
				Thank you for using our plugin! If you are satisfied, please reward it a full five-star <span style="color:#ffb900">★★★★★</span> rating.                        <br>
	            <a href="https://wordpress.org/support/plugin/woo-show-single-variations-shop-category/reviews/?filter=5" target="_blank">Reviews</a>
	            | <a href="https://wordpress.org/support/plugin/woo-show-single-variations-shop-category" target="_blank">Discussion</a>
	            | <a href="https://www.codesmade.com/contact-us/" target="_blank">Support</a>
	        </p>
	    </div>
	   <form method="post" action="options.php">
	      <?php 
	      settings_fields( 'gmwsvs_options_group' ); 
	      $gmwsvs_enable_setting = get_option('gmwsvs_enable_setting');
	      $gmwsvs_hide_parent_product = get_option('gmwsvs_hide_parent_product');
	      
	      $gmwsvs_exclude_cat = array();
	      $gmwsvs_exclude_cat = get_option('gmwsvs_exclude_cat');
	 
	      ?>
	      <table class="form-table">
		         
		         <tr valign="top">
		            <th scope="row">
		               <label for="gmwsvs_enable_setting"><?php _e('Enable', 'gmwsvs'); ?></label>
		            </th>
		            <td>
		               <input class="regular-text" type="checkbox" id="gmwsvs_enable_setting" <?php echo (($gmwsvs_enable_setting=='yes')?'checked':'') ; ?> name="gmwsvs_enable_setting" value="yes" />
		            </td>
		         </tr>
		         
	            <tr valign="top">
		            <th scope="row">
		               <label for="gmwsvs_hide_parent_product"><?php _e('Variable Parent Product', 'gmwsvs'); ?></label>
		            </th>
		            <td>
		               <input class="regular-text" type="checkbox" disabled="" id="gmwsvs_hide_parent_product" <?php echo (($gmwsvs_hide_parent_product=='yes')?'checked':'') ; ?> name="gmwsvs_hide_parent_product" value="yes" />
		               <?php _e('Hide Parent Product of Variable Product', 'gmwsvs'); ?>
		               <p class="description"><?php _e('<strong>Note:</strong> This option will be work for just <strong>Show Variations As Single Product</strong><br><em>For this feature you need to by <a href="https://www.codesmade.com/store/show-variations-on-shop-category-woocommerce-pro/"  target="_blank">Pro Version</a></em>', 'gmwsvs'); ?></p>
		            </td>
		         </tr>
		         <tr valign="top">
		            <th scope="row">
		               <label><?php _e('Exclude Category', 'gmwsvs'); ?></label>
		            </th>
		            <td>
		            	<ul class="gmwsvs_exclude" style="pointer-events:none;opacity:0.7;">
			               <?php 
			               $args = array(
			               	'taxonomy'              => 'product_cat',
			               	'selected_cats'         => $gmwsvs_exclude_cat
			               );
			               wp_terms_checklist( 0 , $args ); 
			               ?>
			           </ul>
			           <em>For this feature you need to by <a href="https://www.codesmade.com/store/show-variations-on-shop-category-woocommerce-pro/"  target="_blank">Pro Version</a></em>
		            </td>
		         </tr>
	      </table>
	      <input type="hidden" name="action_wssvs_op" value="update">
	      <?php  submit_button(); ?>
	   </form>
	   
	</div>
	<Style>
		.gmwsvs_exclude .children{
			margin-left: 25px;
		}
	</Style>
	<?php
	}

	public function WSSVSC_register_settings() {

		
		register_setting( 'gmwsvs_options_group', 'gmwsvs_enable_setting', array( $this, 'gmwsvs_accesstoken_callback' ) );

		register_setting( 'gmwsvs_options_group', 'gmwsvs_hide_parent_product', array( $this, 'gmwsvs_accesstoken_callback' ) );
		
		if(isset($_REQUEST['action_wssvs_op']) && $_REQUEST['action_wssvs_op']=='update'){
			update_option('gmwsvs_exclude_cat', $_REQUEST['tax_input']['product_cat']);
		}
		
		if(isset($_REQUEST['action']) && $_REQUEST['action']=='run_process'){

				


				wp_redirect(  get_admin_url().'options-general.php?page=WSSVSC&msg=success' );
			exit;
		}
	}
	
	
	public function gmwsvs_accesstoken_callback($option) {
		if ( empty( $option ) ) {
		}
		return $option;
	}

	
	
}


?>