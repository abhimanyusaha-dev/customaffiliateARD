<?php
class Affiliate_Type_Admin {
	public function load_affiliate_type_hook(){
		

		add_action('woocommerce_product_options_inventory_product_data', array($this,'wdo_affiliate_type_product_data_fields'));
		add_action( 'woocommerce_new_order', array($this,'add_order_meta_custom_affiliate'),  1, 1  );
		add_action( 'woocommerce_admin_order_data_after_order_details', array($this,'show_order_meta_custom_affiliate') );
		add_action( 'woocommerce_process_product_meta', array($this,'save_custom_content_meta_box' ));

		global $wpdb;
	    $slug ="";
	    $disable = true;
	    $main_affiliates = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}custom_affiliate where affiliate_type='main' and status='active'" );
	    if($main_affiliates){
	        $slug =  $main_affiliates[0]->custom_slug;
	    }
	    if((isset($_COOKIE['custom_affiliate_slug']) && ($_COOKIE['custom_affiliate_slug'])) || (isset($_GET['c']) && ($_GET['c'])) || ($slug)){
	        if(isset($_COOKIE['custom_affiliate_slug']) && ($_COOKIE['custom_affiliate_slug'])){
	            $slug = $_COOKIE['custom_affiliate_slug'];
	        }
	        if(isset($_GET['c']) && ($_GET['c'])){
	            $slug = $_GET['c'];
	        }
	        $all_affiliates = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}custom_affiliate where custom_slug='$slug'");
	        if($all_affiliates){
	        	$time_now = time();
	            $offer_start_time = $all_affiliates[0]->offer_start_time;
	            $affiliate_offering_status = $all_affiliates[0]->affiliate_offering_status;
	            $offer_end_time = $all_affiliates[0]->offer_end_time;
	            if($affiliate_offering_status =="offer"){
		            if(($offer_start_time) && ($offer_end_time) && ($time_now>strtotime($offer_start_time)) && (strtotime($offer_end_time)>$time_now)){
		            	$disable = false;
		            }
		        }else{
		        	$disable = false;
		        }
	        }
	    }

	    if($disable===false){

			if (is_admin()){
			}else{
	        	if((isset($_COOKIE['custom_affiliate_slug']) && ($_COOKIE['custom_affiliate_slug'])) || (isset($_GET['c']) && ($_GET['c']))){
	        		add_filter( 'woocommerce_get_price_html', array($this,'elex_display_striked_out_price_for_variable'), 21, 2 );
					add_filter( 'woocommerce_cart_item_price', array($this, 'cart_price_view'), 1000, 3 );
					add_filter('woocommerce_product_variation_get_sale_price',array($this,'custom_price'), 99, 2);
					add_filter('woocommerce_product_variation_get_regular_price',array($this,'custom_price1'), 99, 2);
				
				
					add_filter('woocommerce_variation_prices_sale_price',array($this,'custom_price'), 99, 3);
					add_filter('woocommerce_variation_prices_reqular_price',array($this,'custom_price1'), 99, 3);

				
					add_filter('woocommerce_product_get_regular_price',array($this,'custom_price1'), 21, 2);
					add_filter('woocommerce_product_get_sale_price', array($this,'custom_price'), 21, 2);
	        	}
				// if($_SERVER['REQUEST_URI']!="/cart/"){
		        add_filter('woocommerce_product_variation_get_price',array($this,'custom_price'), 21, 2);
		        add_filter('woocommerce_variation_prices_price',array($this,'custom_price'), 21, 3);
				// }
		        add_filter('woocommerce_product_get_price',array($this,'custom_price'), 99, 2);

				
			}
		}

		
	}
	
 	public function wdo_affiliate_type_product_data_fields() {
    	global $post;
    	$generic_affiliate ="";
    	$generic_affiliate_rate ="";
    	$custom_affiliate ="";
    	$sales_funnel = "";
    	if($post){
    		$generic_affiliate = get_post_meta($post->ID,'generic-affiliate',true);
    		$generic_affiliate_rate = get_post_meta($post->ID,'generic-affiliate-rate',true);
    		$generic_affiliate_show = get_post_meta($post->ID,'generic-affiliate-show',true);
    	}
    	?>	
    		<div class="options_group">
    			<?php
		    	$options = array(""=>"Select the rate","percentage"=>"Percentage","flat_rate"=>"Flat Rate");
				woocommerce_wp_select(
				    array(
				      'id' => 'generic_affiliate_rates',
				      'label' => __( 'Generic Affiliate Rate', 'woocommerce' ),
				      'placeholder' => '',
				      'description' => __( 'Enter the value here.', 'woocommerce' ),
				      'options' =>  $options,
				      'value'=>$generic_affiliate_rate,
				    )
				);
				?>
			</div>
			<div class="options_group">
		    	<?php
				woocommerce_wp_text_input(
				    array(
				      'id' => 'generic_affiliate',
				      'label' => __( 'Generic Affiliate', 'woocommerce' ),
				      'placeholder' => '',
				      'description' => __( 'Enter the value here.', 'woocommerce' ),
				      'type' => 'number',
				      'value'=>$generic_affiliate,
				    )
				);
				?>
			</div>
			
			<div class="options_group">
		    	<?php
				$optionsStatus = array(""=>"Select the status","1"=>"Yes","0"=>"No");
				woocommerce_wp_select(
				    array(
				      'id' => 'generic_affiliate_show',
				      'label' => __( 'Generic Affiliate Show', 'woocommerce' ),
				      'placeholder' => '',
				      'description' => __( 'Select the status', 'woocommerce' ),
				      'options' =>  $optionsStatus,
				      'value'=>$generic_affiliate_show,
				    )
				);
				?>
			</div>
			
		<?php

	}
	public function save_custom_content_meta_box( $post_id ) {
        if(isset($_POST['generic_affiliate'])){
        	update_post_meta($post_id,'generic-affiliate', $_POST['generic_affiliate'] );
        }
        if(isset($_POST['generic_affiliate_rates'])){
        	update_post_meta($post_id,'generic-affiliate-rate', $_POST['generic_affiliate_rates'] );
        }
        if(isset($_POST['generic_affiliate_show'])){
        	update_post_meta($post_id,'generic-affiliate-show', $_POST['generic_affiliate_show'] );
        }
        
    }
	public function custom_price1($price, $product){
		// Delete product cached price  (if needed)
		wc_delete_product_transients($product->get_id());
		return $price; // X3 for testing
	}
	public function custom_price($price, $product){
		global $wpdb;
		// Fix Shopping cart price 2022-07-21
		if ( !empty($product->get_id()) /*&& !empty(wc_get_product($product->get_id()))*/ ) {

			$def_product = wc_get_product( $product->get_id()  );

			$raw_data = $def_product->get_data();

			$prod_regular_price = 0;
			$prod_sale_price = 0;
			$prod_price = 0;
			$prod_min_price = 0;

			if ( !empty($raw_data['regular_price']) ) {
				$prod_regular_price = (float)$raw_data['regular_price'];
				if ( $prod_regular_price > 0 && ( $prod_regular_price < $prod_min_price || $prod_min_price ==0 ) ) 
					$prod_min_price = $prod_regular_price;
			}
			if ( !empty($raw_data['sale_price']) ) {
				$prod_sale_price = (float)$raw_data['sale_price'];
				if ( $prod_sale_price > 0 && ( $prod_sale_price < $prod_min_price || $prod_min_price ==0 ) ) 
					$prod_min_price = $prod_sale_price;
			}
			if ( !empty($raw_data['price']) ) {
				$prod_price = (float)$raw_data['price'];
				if ( $prod_price > 0 && ( $prod_price < $prod_min_price || $prod_min_price ==0 ) ) 
					$prod_min_price = $prod_price;
			}

			if ( $price < $prod_min_price ) {
				return $price;
			}
		}
		$p_id =$product->get_id();
		$slug = "";
		if($product->get_parent_id()>0){
			$p_id = $product->get_parent_id();
		}
		$main_affiliates = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}custom_affiliate where affiliate_type='main' and status='active'" );
        if($main_affiliates){
            $slug =  $main_affiliates[0]->custom_slug;
        }

		wc_delete_product_transients($product->get_id());
		if((isset($_COOKIE['custom_affiliate_slug']) && ($_COOKIE['custom_affiliate_slug'])) || (isset($_GET['c']) && ($_GET['c'])) || ($slug) ){
	        
	        if(isset($_COOKIE['custom_affiliate_slug']) && ($_COOKIE['custom_affiliate_slug'])){
				 $slug = $_COOKIE['custom_affiliate_slug'];
			}
	        if(isset($_GET['c']) && ($_GET['c'])){
	            $slug = $_GET['c'];
	        }
        	$all_affiliates = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}custom_affiliate where custom_slug='$slug'");

			if($all_affiliates){
	            $affiliate_type = $all_affiliates[0]->affiliate_type;
	            if($affiliate_type=='generic_affiliate'){
	            	$generic_affiliate = get_post_meta($p_id,'generic-affiliate',true);
	            	$generic_affiliate_rate = get_post_meta($p_id,'generic-affiliate-rate',true);
	            	if($generic_affiliate){
	            		if($generic_affiliate_rate=="percentage"){
	            			$new_price=(float)$price-((float)$price*$generic_affiliate)/100;
	            			return round($new_price,2);
	            		}else{
	            			$new_price=(float)$price-(float)$generic_affiliate;
	            			return round($new_price,2);
	            		}
	            		
	            	}
	            }else{
	            	if($all_affiliates[0]->cust_aff_product_cat){
	            		$cust_aff_product_cat = unserialize($all_affiliates[0]->cust_aff_product_cat);
	            		foreach ($cust_aff_product_cat as $key => $value){
	            			if (is_object_in_term($p_id,'product_cat',$value['cat_id'])){
	            				if($value['discount']){
		            				if($value['rate']=="percentage"){
				            			$new_price=(float)$price-((float)$price*$value['discount'])/100;
				            			return round($new_price,2);
				            		}else{
				            			$new_price=(float)$price-(float)$value['discount'];
				            			return round($new_price,2);
				            		}
				            	}
	            			}
	            		}
	            	}

	            	if($all_affiliates[0]->cust_aff_product){
	            		$cust_aff_product = unserialize($all_affiliates[0]->cust_aff_product);
	            		foreach ($cust_aff_product as $key => $value){
	            			if($p_id==$value['product_id']){
	            				if($value['discount']){
		            				if($value['rate']=="percentage"){
				            			$new_price=(float)$price-((float)$price*$value['discount'])/100;
				            			return round($new_price,2);
				            		}else{
				            			$new_price=(float)$price-(float)$value['discount'];
				            			return round($new_price,2);
				            		}
				            	}
	            			}
	            		}
	            		
	            	}	
	            }
	        }
		}
		return $price; // X3 for testing
	}
	public function elex_display_striked_out_price_for_variable($price='', $product){
		global $wpdb;
		$p_id =$product->get_id();
		$prod_show = false;
		if($product->get_parent_id()>0){
			$p_id = $product->get_parent_id();
		}
		//wc_delete_product_transients($product->get_id());
		if((isset($_COOKIE['custom_affiliate_slug']) && ($_COOKIE['custom_affiliate_slug'])) || (isset($_GET['c']) && ($_GET['c'])) ){
	        
	        if(isset($_COOKIE['custom_affiliate_slug']) && ($_COOKIE['custom_affiliate_slug'])){
				 $slug = $_COOKIE['custom_affiliate_slug'];
			}
	        if(isset($_GET['c']) && ($_GET['c'])){
	            $slug = $_GET['c'];
	        }
        	$all_affiliates = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}custom_affiliate where custom_slug='$slug'");
			if($all_affiliates){
	            $affiliate_type = $all_affiliates[0]->affiliate_type;
	            if($affiliate_type=='generic_affiliate'){
	            	$generic_affiliate = get_post_meta($p_id,'generic-affiliate',true);
	            	if($generic_affiliate){
	            		$prod_show = true;
	            	}
	            }else{
	            	if($all_affiliates[0]->cust_aff_product){
	            		$cust_aff_product = unserialize($all_affiliates[0]->cust_aff_product);
	            		foreach ($cust_aff_product as $key => $value){
	            			if($value['discount']){
			            		if($p_id==$value['product_id']){
			            			$prod_show = true;
			            		}
			            	}
		            	}
	            	}
	            	if($all_affiliates[0]->cust_aff_product_cat){
	            		$cust_aff_product_cat = unserialize($all_affiliates[0]->cust_aff_product_cat);
	            		foreach ($cust_aff_product_cat as $key => $value){
	            			if (is_object_in_term($p_id,'product_cat',$value['cat_id'])){
	            				if($value['discount']){
	            					$prod_show = true;
	            				}
	            			}
	            		}
	            	}	
	            }
	        }
	    }
		$reg_price = '';
		if($product->is_type( 'variable' ) && $prod_show==true){
				$variations = $product->get_children();
				$reg_prices = array();
				$sale_prices = array();
				foreach ($variations as $value) {
				$single_variation=new WC_Product_Variation($value);
				array_push($reg_prices, $single_variation->get_regular_price());
				array_push($sale_prices, $single_variation->get_price());
			}
			sort($reg_prices);
			sort($sale_prices);
			$min_price = $reg_prices[0];
			$max_price = $reg_prices[count($reg_prices)-1];
			if($min_price == $max_price){
				$reg_price = wc_price($min_price);
			}else{
				$reg_price = wc_format_price_range($min_price, $max_price);
			}
			$min_price = $sale_prices[0];
			$max_price = $sale_prices[count($sale_prices)-1];
			if($min_price == $max_price){
				$sale_price = wc_price($min_price);
			}else{
				$sale_price = wc_format_price_range($min_price, $max_price);
			}
			$suffix = $product->get_price_suffix($price);
			return wc_format_sale_price($reg_price, $sale_price).$suffix;
		}
		// Added from - on 02-08-22
		if($product->get_parent_id()>0){
			$parent_product= wc_get_product($product->get_parent_id());
			
			if($parent_product->is_type( 'variable' ) && $prod_show==true){
				
				$single_variation=new WC_Product_Variation($product->get_id());
				$suffix = $product->get_price_suffix($price);
				return wc_format_sale_price($single_variation->get_regular_price(), $single_variation->get_price()).$suffix;
			}

		}
		else 
		{
			if($product->is_type( 'simple' ) && $prod_show==true){			
				
				$suffix = $product->get_price_suffix($price);
				return wc_format_sale_price($product->get_regular_price(), $product->get_price()).$suffix;
			}
			return $price;
		}
		// Added till - on 02-08-22

		return $price;
	}
	
	public function cart_price_view( $item_price, $cart_item ){

		$product = wc_get_product($cart_item['product_id']);
		$p_id =$product->get_id();
		$prod_show = false;
		if($product->get_parent_id()>0){
			$p_id = $product->get_parent_id();
		}
		//wc_delete_product_transients($product->get_id());
		if((isset($_COOKIE['custom_affiliate_slug']) && ($_COOKIE['custom_affiliate_slug'])) || (isset($_GET['c']) && ($_GET['c'])) ){
	        global $wpdb;
	        $slug = $_COOKIE['custom_affiliate_slug'];
	        if(isset($_GET['c']) && ($_GET['c'])){
	            $slug = $_GET['c'];
	        }
        	$all_affiliates = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}custom_affiliate where custom_slug='$slug'");
			if($all_affiliates){
	            $affiliate_type = $all_affiliates[0]->affiliate_type;
	            if($affiliate_type=='generic_affiliate'){
	            	$generic_affiliate = get_post_meta($p_id,'generic-affiliate',true);
	            	if($generic_affiliate){
	            		$prod_show = true;
	            	}
	            }else{
	            	if($all_affiliates[0]->cust_aff_product){
	            		$cust_aff_product = unserialize($all_affiliates[0]->cust_aff_product);
	            		$prod_id_array = array_column($cust_aff_product, 'product_id');
	            		if(($prod_id_array) && in_array($p_id,$prod_id_array)){
	            			$prod_show = true;
	            		}
	            	}
	            	if($all_affiliates[0]->cust_aff_product_cat){
	            		$cust_aff_product_cat = unserialize($all_affiliates[0]->cust_aff_product_cat);
	            		foreach ($cust_aff_product_cat as $key => $value){
	            			if (is_object_in_term($p_id,'product_cat',$value['cat_id'])){
	            				$prod_show = true;
	            			}
	            		}
	            	}		
	            }
	        }
	    }
	    if($prod_show==true){
			if($product->is_type( 'variable' ))
			{
				$item_price=variable_cust_price_cart($p_id,$cart_item['variation_id'],$item_price);
			}
			else
			{
				if(strip_tags($item_price)!=get_woocommerce_currency_symbol().$product->get_regular_price()):
	    			$item_price = '<del>'.get_woocommerce_currency_symbol().$product->get_regular_price().'</del> '.$item_price;
				endif;
			}
	    }
	    return $item_price;
	} 

	public function add_order_meta_custom_affiliate( $order_id ) {
		global $wpdb;
		$slug ="";
	  	$main_affiliates = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}custom_affiliate where affiliate_type='main' and status='active'" );
        if($main_affiliates){
            $slug =  $main_affiliates[0]->custom_slug;
        }
	    if((isset($_COOKIE['custom_affiliate_slug']) && ($_COOKIE['custom_affiliate_slug'])) || (isset($_GET['c']) && ($_GET['c'])) || ($slug) ){
	        
	        if(isset($_COOKIE['custom_affiliate_slug']) && ($_COOKIE['custom_affiliate_slug'])){
				 $slug = $_COOKIE['custom_affiliate_slug'];
			}
            if(isset($_GET['c']) && ($_GET['c'])){
                $slug = $_GET['c'];
            }
			$scriptcode = $wpdb->get_var("SELECT IFNULL(scriptcode, '') scriptcode FROM {$wpdb->prefix}custom_affiliate where custom_slug='$slug'" );
        	if(!empty($scriptcode)) { 
		       	update_post_meta($order_id, 'order_custom_affiliate_scriptcode', $scriptcode);
			}
		        update_post_meta($order_id,'order_custom_affiliate_slug',$slug );
		    }
	}
	public function show_order_meta_custom_affiliate( $order ){  
		$affiliate_slug = get_post_meta( $order->get_id(), 'order_custom_affiliate_slug', true );
		?>
		<br class="clear" />
		<?php if($affiliate_slug): ?>
			<div class="address">
				<p>C : <?php echo $affiliate_slug; ?></p>
			</div>  
		<?php endif;
	} 
	
}


?>