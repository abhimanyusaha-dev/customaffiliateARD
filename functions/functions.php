<?php
if(get_option('timezone_string')):
    date_default_timezone_set(get_option('timezone_string'));
endif;
add_action('init', 'my_setcookie',0);

function my_setcookie(){
    global $wpdb;
    /* Staging Code Start */
    // echo 11;exit;
    /*
    if((isset($_GET['c']) && ($_GET['c']))){
    $traffic_source = $wpdb->get_var("select traffic_source from {$wpdb->prefix}custom_affiliate where custom_slug='".$_GET['c']."'");
    // echo 66;
        $path = parse_url(get_option('siteurl'), PHP_URL_PATH);
        $host = parse_url(get_option('siteurl'), PHP_URL_HOST);
        $cookie_days = get_option('cookie_days');
        if(isset($_COOKIE['custom_affiliate_slug']) && ($_COOKIE['custom_affiliate_slug']))
        {
            unset($_COOKIE['custom_affiliate_slug']);
            unset($_COOKIE['custom_affiliate_traffic_source']);
        }
        if(isset($_COOKIE['custom_affiliate_pvar']) && ($_COOKIE['custom_affiliate_pgen']))
        {
            unset($_COOKIE['custom_affiliate_pvar']);
            unset($_COOKIE['custom_affiliate_pgen']);
        }
        $time = time()+$cookie_days*60*60*24;
        setcookie('custom_affiliate_slug',$_GET['c'], $time, $path, $host);
        setcookie('custom_affiliate_traffic_source',$traffic_source, $time, $path, $host);
    }else{
    } */
    /* Staging Code END */
    /* Live Code Start */
    $dd=(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $url_components = parse_url($dd);
    // echo 65161;
    // print_r($url_components);
    if(!empty($url_components['query'])):
        parse_str($url_components['query'], $params);
        if((isset($params['c']) && ($params['c']))){
        // echo 77;
        $traffic_source = $wpdb->get_var("select traffic_source from {$wpdb->prefix}custom_affiliate where custom_slug='".$params['c']."'");
                $path = parse_url(get_option('siteurl'), PHP_URL_PATH);
                $host = parse_url(get_option('siteurl'), PHP_URL_HOST);
                $cookie_days = get_option('cookie_days');
                if(isset($_COOKIE['custom_affiliate_slug']) && ($_COOKIE['custom_affiliate_slug']))
                {
                    unset($_COOKIE['custom_affiliate_slug']);
                    unset($_COOKIE['custom_affiliate_traffic_source']);
                }
                // if(isset($_COOKIE['custom_affiliate_pvar']) && ($_COOKIE['custom_affiliate_pgen']))
                // {
                //     unset($_COOKIE['custom_affiliate_pvar']);
                //     unset($_COOKIE['custom_affiliate_pgen']);
                // }
                // if(isset($_COOKIE['custom_affiliate_slug_firsttime']))
                // {
                //     unset($_COOKIE['custom_affiliate_slug_firsttime']);
                // }
                // setcookie('custom_affiliate_slug_firsttime',$params['c'], time()+(get_option('cookie_days')*24*60*60*1000));

                // $GLOBALS['firsttime'] = '1';

                ?>
                <script>
                const dt = new Date();
                dt.setTime(dt.getTime() + (<?php echo $cookie_days;?>*24*60*60*1000));
                let expires = "expires="+ dt.toUTCString();
                document.cookie = "custom_affiliate_slug=<?php echo $params['c'];?>;" + expires + ";path=/";
                document.cookie = "custom_affiliate_traffic_source=<?php echo $traffic_source;?>;" + expires + ";path=/";
                </script>
                <?php
            }else{
            }
        endif;
    /* Live Code END */
}


add_filter( 'body_class', 'body_class_before_header' );
function body_class_before_header( $classes ) {

    if((isset($_COOKIE['custom_affiliate_slug']) && ($_COOKIE['custom_affiliate_slug'])) || (isset($_GET['c']) && ($_GET['c'])) ){
        global $wpdb;
        if(isset($_COOKIE['custom_affiliate_slug']) && ($_COOKIE['custom_affiliate_slug'])){
             $slug = $_COOKIE['custom_affiliate_slug'];
        }
        if(isset($_GET['c']) && ($_GET['c'])){
            $slug = $_GET['c'];
        }
        
        $all_affiliates = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}custom_affiliate where custom_slug='$slug'" );
        if($all_affiliates){    
        $classes[] = 'ca-'.$slug;
        $classes[] = 'haveca';
        //don't return here (only)
        }
    }

    //return $classes anyway
    return $classes;
}
add_action('wp_head', 'add_cust_aff_style');
function add_cust_aff_style(){
    if((isset($_COOKIE['custom_affiliate_slug']) && ($_COOKIE['custom_affiliate_slug'])) || (isset($_GET['c']) && ($_GET['c'])) ){
        global $wpdb;
        if(isset($_COOKIE['custom_affiliate_slug']) && ($_COOKIE['custom_affiliate_slug'])){
             $slug = $_COOKIE['custom_affiliate_slug'];
        }
        if(isset($_GET['c']) && ($_GET['c'])){
            $slug = $_GET['c'];
        }
        
        $all_affiliates = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}custom_affiliate where custom_slug='$slug'" );
        if($all_affiliates){
            if($all_affiliates[0]->cust_aff_style){
                echo "<style>".$all_affiliates[0]->cust_aff_style."</style>";
            }
        }
    }
}
function wpse_273872_pre_get_posts( $query ) {
    if((isset($_COOKIE['custom_affiliate_slug']) && ($_COOKIE['custom_affiliate_slug'])) || (isset($_GET['c']) && ($_GET['c'])) ){
        global $wpdb;
		if(isset($_COOKIE['custom_affiliate_slug']) && ($_COOKIE['custom_affiliate_slug'])){
        	 $slug = $_COOKIE['custom_affiliate_slug'];
		}
        if(isset($_GET['c']) && ($_GET['c'])){
            $slug = $_GET['c'];
        }
        
        $all_affiliates = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}custom_affiliate where custom_slug='$slug'" );
        if($all_affiliates){
            //echo ;
            $affiliate_type = $all_affiliates[0]->affiliate_type;
            $aff_exists=1;
            if(empty($_COOKIE['custom_affiliate_slug'])):
                $aff_exists=0;
            endif;
            if($affiliate_type=='generic_affiliate'){
                $generic_page_id = get_option('generic_page_id');
                if ( $query->is_main_query()){
                    $front_page_id = get_option('page_on_front');
                    $current_page_id = $query->get('page_id');
                    $is_static_front_page = 'page' == get_option('show_on_front');
                    
                    if ($is_static_front_page && $front_page_id == $current_page_id) {
                        $get_post_type=get_post_type($all_affiliates[0]->page_id);
                            if($aff_exists==0):
                                if($generic_page_id!=$front_page_id):
                                // $genre_url = add_query_arg('c', $slug, get_permalink($generic_page_id));

                                $query->set('page_id', $generic_page_id);
                                return $query;
                                // wp_redirect($genre_url);exit;
                                endif;

                            endif;
                    } 
                }

            }else{
                
                if ( $query->is_main_query() && ($all_affiliates[0]->page_id || $all_affiliates[0]->land_page_url)){
                    // if(is_product_category())
                    // {
                    //     print_r($all_affiliates[0]->cust_aff_product);

                    // }
                    $front_page_id = get_option('page_on_front');
                    $current_page_id = $query->get('page_id');
                    $is_static_front_page = 'page' == get_option('show_on_front');
                    if ($is_static_front_page && $front_page_id == $current_page_id) {
                        if($all_affiliates[0]->page_id):
                            $get_post_type=get_post_type($all_affiliates[0]->page_id);
                            if($get_post_type=='page'):
                            
                                if($aff_exists==0):
                                    if($all_affiliates[0]->page_id!=$front_page_id):
                                        // $page_url = add_query_arg('c', $slug, get_permalink($all_affiliates[0]->page_id));
                                        // wp_redirect($page_url);exit;
                                        $query->set('page_id', $all_affiliates[0]->page_id);
                                        return $query;
                                    endif;
                                endif;
                            endif;
                        elseif($all_affiliates[0]->land_page_url):
                            if($aff_exists==0):
                    // echo $slug;exit;
                                // echo $slug;exit;
                                //  wp_redirect($all_affiliates[0]->land_page_url."?c=$slug");exit;
                                    if(url_to_postid($all_affiliates[0]->land_page_url)!=$front_page_id):
                                        $land_page_url = add_query_arg('c', $slug, $all_affiliates[0]->land_page_url);
                                        wp_redirect($land_page_url);exit;
                                    endif;
                            endif;
                        endif;

                    
                    }
                }
            }
        }
    }
}
add_action( 'pre_get_posts', 'wpse_273872_pre_get_posts' ); 
add_shortcode( 'custom_affiliate_shortcode', 'custom_affiliate_shortcode_func' );
function custom_affiliate_shortcode_func( $atts, $content = "" ) {
   if((isset($_COOKIE['custom_affiliate_slug']) && ($_COOKIE['custom_affiliate_slug'])) || (isset($_GET['c']) && ($_GET['c'])) ){
        global $wpdb;
        if(isset($_COOKIE['custom_affiliate_slug']) && ($_COOKIE['custom_affiliate_slug'])){
        	 $slug = $_COOKIE['custom_affiliate_slug'];
		}
        if(isset($_GET['c']) && ($_GET['c'])){
            $slug = $_GET['c'];
        }
    $all_affiliates = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}custom_affiliate where custom_slug='$slug'" );
    if(!empty($all_affiliates)) {
        if($all_affiliates[0]->affiliate_type=="custom_affiliate" && !empty($all_affiliates[0]->cust_aff_text)){
            $content .= '<div class="banner-top-blueinfo-sec">
                <div class="row-flex">
                    <div class="blueinfo-text-col">';
                        if($all_affiliates[0]->cust_aff_image){
                            $image_attributes = wp_get_attachment_image_src($all_affiliates[0]->cust_aff_image);
                            if($image_attributes){
                                $content .= '<div class="info-auth-img">
                                    <img src="'.$image_attributes[0].'" alt="">
                                </div>';
                            }
                        }
                        $content .= '<div class="info-textbox">
                            <p><strong>'.$all_affiliates[0]->cust_aff_text.'</strong></p> 
                        </div>
                    </div>
                    <div class="blueinfo-btn-col">
                        <a href="'.$all_affiliates[0]->cust_aff_button_link.'" target="_self" class="button primary lowercase" style="border-radius:99px;padding:5px 30px 5px 30px;">
                            <span>'.$all_affiliates[0]->cust_aff_button_text.'</span>
                        </a>
                    </div>
                </div>
            </div>';
        }
    }
    }
    return  $content;
}
function so_20990199_product_query( $q ){ 
    global $wpdb;
	// print_r($q);
    $slug = "";
    //print_r($product->get_catalog_visibility());
    if ( ! is_admin() ) {
        if($q->get('post_type')=='product'){
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
                $all_affiliates = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}custom_affiliate where custom_slug='$slug'" );
		if (!empty($all_affiliates)) {
            
                    $affiliate_type = $all_affiliates[0]->affiliate_type;
                    if($affiliate_type=='generic_affiliate'){
                        $product_ids_on_sale = $wpdb->get_col("SELECT post_id from {$wpdb->prefix}postmeta where meta_key='generic-affiliate'");
                        // $q->set( 'post__in', (array) $product_ids_on_sale ); 
                        // return $q;
                    }
                    if($all_affiliates && $all_affiliates[0]->cust_aff_product_cat){
                        $cat_ids_on_sale = array_column(unserialize($all_affiliates[0]->cust_aff_product_cat),'cat_id');
                        $taxquery = array(
                            array(
                                'taxonomy' => 'product_cat',
                                'field' => 'id',
                                'terms' => $cat_ids_on_sale,
                                'operator'=> 'IN'
                            )
                        );

                        // $q->set( 'tax_query', $taxquery );
                        // return $q;
                    }
                    if($all_affiliates && $all_affiliates[0]->cust_aff_product){
                        $product_ids_on_sale = array_column(unserialize($all_affiliates[0]->cust_aff_product),'product_id');
                        if(is_product_category() )
                        {
                        //print_r($product->get_catalog_visibility());

                        }
                        // print_r($product_ids_on_sale);
                        // $q->set( 'post__in', (array) $product_ids_on_sale ); 
                        // return $q;
                    }
                }
            }
        }
    }
    return $q;
}
add_filter( 'pre_get_posts', 'so_20990199_product_query' );
function testing_woo_product_query( $q ){ 
    global $wpdb;
	//print_r($q);
    $testpage_id=get_queried_object_id();
    $slug = "";
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
		$all_affiliates = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}custom_affiliate where custom_slug='$slug'" );
        $affiliate_type = $all_affiliates[0]->affiliate_type;
        if($affiliate_type=='generic_affiliate'){
            // $product_ids_on_sale = $wpdb->get_col("SELECT post_id from {$wpdb->prefix}postmeta where meta_key='generic-affiliate'");
            // $q->set( 'post__in', (array) $product_ids_on_sale ); 
            // return $q;
            // if($testpage_id==120)
            // {
            //     //affiliates having products make categorywise
            //     $new_product_cat_check=[];
            //     if(!empty($product_ids_on_sale)):

            //         foreach($product_ids_on_sale as $check_pd_id):
            //             if(get_post_meta($check_pd_id,'generic-affiliate-show',true)==1):
            //                 $terms = get_the_terms ($check_pd_id, 'product_cat');
            //                 if ( !is_wp_error($terms)) : 
            //                     $skills_links = wp_list_pluck($terms, 'term_id'); 
            //                     // $skills_yo = implode(", ", $skills_links);
            //                     foreach($skills_links as $new_cat):
            //                         if($new_cat!=120){        
            //                             if(array_key_exists($new_cat,$new_product_cat_check))
            //                             {
            //                                 array_push($new_product_cat_check[$new_cat],$check_pd_id);        
            //                             }
            //                             else
            //                             {
            //                                 $new_product_cat_check[$new_cat]=[$check_pd_id];
            //                             }
            //                         }
            //                     endforeach;
            //                 endif;
            //             endif;
            //         endforeach;
                    

            //         //all shop all products loop
            //         $exclude_cat_shop_all=[];
            //         $other_products=[];
            //         // $in_prdct=[];
                    
            //         $all_ids = get_posts( array(
            //             'post_type' => 'product',
            //             'numberposts' => -1,
            //             'post_status' => 'publish',
            //             'fields' => 'ids',
            //             'tax_query' => array(
            //             array(
            //                 'taxonomy' => 'product_cat',
            //                 'field' => 'id',
            //                 'terms' => [$testpage_id],
            //                 'operator' => 'IN',
            //             )
            //             ),
            //         ) );
            //         foreach($all_ids as $check_pd_id):
            //             $terms = get_the_terms ($check_pd_id, 'product_cat');
            //             if ( !is_wp_error($terms)) : 
            //                 $skills_links = wp_list_pluck($terms, 'term_id'); 
            //                 foreach($skills_links as $new_cat):
            //                     if($new_cat!=120 && $new_cat!=98){
            //                         if(!array_key_exists($new_cat,$new_product_cat_check)):
            //                             if(array_key_exists($new_cat,$other_products)):
            //                                 if(!in_array($check_pd_id,$other_products[$new_cat])):
            //                                     array_push($other_products[$new_cat],$check_pd_id);
            //                                 else:
            //                                     $other_products[$new_cat]=[$check_pd_id];
            //                                 endif;
            //                             else:
            //                                 $other_products[$new_cat]=[$check_pd_id];
            //                             endif;
                                        

            //                         endif;

            //                     }
            //                 endforeach;
            //             endif;
            //             endforeach;
            //         $inproducts=[];
            //         $all_inproducts=array_merge($new_product_cat_check,$other_products);
            //         foreach($all_inproducts as $key => $inproduct):
            //             foreach($inproduct as $value):
            //             array_push($inproducts,$value);
            //             endforeach;
            //         endforeach;

            //     $taxquery = array(
            //         array(
            //             'taxonomy' => 'product_cat',
            //             'field' => 'id',
            //             'terms' => $testpage_id,
            //             'operator'=> 'IN'
            //         )
            //     );
            //     $q->set( 'tax_query', $taxquery );
            //     $q->set( 'post__in', $inproducts ); 
            //     return $q;
            //     endif;

            // }
            // else{
            //     $new_product_cat_check=[];
            //     if(!empty($product_ids_on_sale)):
            //         foreach($product_ids_on_sale as $check_pd_id):
            //         if(get_post_meta($check_pd_id,'generic-affiliate-show',true)==1):
            //             $terms = get_the_terms ($check_pd_id, 'product_cat');
            //             if ( !is_wp_error($terms)) : 
            //                 $skills_links = wp_list_pluck($terms, 'term_id'); 
            //                 // $skills_yo = implode(", ", $skills_links);
            //                 if(in_array($testpage_id,$skills_links)):
            //                     array_push($new_product_cat_check,$check_pd_id);
            //                 endif;
            //             endif;
            //         endif;
            //         endforeach;
            //         if(!empty($new_product_cat_check)):
            //                 $taxquery = array(
            //                     array(
            //                         'taxonomy' => 'product_cat',
            //                         'field' => 'id',
            //                         'terms' => [$testpage_id],
            //                         'operator'=> 'IN'
            //                     )
            //                 );
            //             $q->set( 'tax_query', $taxquery );
            //             $q->set( 'post__in', (array) $new_product_cat_check ); 
            //         endif;
            //     endif;
            // return $q;
            // }

        }
        if($all_affiliates && !empty($all_affiliates[0]->cust_aff_product_cat) && empty($all_affiliates[0]->cust_aff_product)){
            $cat_ids_on_sale = array_column(unserialize($all_affiliates[0]->cust_aff_product_cat),'cat_id');
            $taxquery = array(
                array(
                    'taxonomy' => 'product_cat',
                    'field' => 'id',
                    'terms' => $cat_ids_on_sale,
                    'operator'=> 'IN'
                )
            );

            // $q->set( 'tax_query', $taxquery );
            // return $q;
        }
		if($all_affiliates && empty($all_affiliates[0]->cust_aff_product_cat) && !empty($all_affiliates[0]->cust_aff_product)){
			$product_ids_on_sale = array_column(unserialize($all_affiliates[0]->cust_aff_product),'product_id');
			//print_r($product_ids_on_sale);
			$q->set( 'post__in', (array) $product_ids_on_sale ); 
            return $q;
		}
        if($all_affiliates && !empty($all_affiliates[0]->cust_aff_product_cat) && !empty($all_affiliates[0]->cust_aff_product))
        {
            // if($testpage_id==120)
            // {
            //     //affiliates having products make categorywise
            //     $product_ids_on_sale = array_column(unserialize($all_affiliates[0]->cust_aff_product),'product_id');
            //     $new_product_cat_check=[];
            //     if(!empty($product_ids_on_sale)):
            //         foreach($product_ids_on_sale as $check_pd_id):
            //         $terms = get_the_terms ($check_pd_id, 'product_cat');
            //         if ( !is_wp_error($terms)) : 
            //             $skills_links = wp_list_pluck($terms, 'term_id'); 
            //             foreach($skills_links as $new_cat):
            //                 if($new_cat!=120){

            //                     if(array_key_exists($new_cat,$new_product_cat_check))
            //                     {
            //                         array_push($new_product_cat_check[$new_cat],$check_pd_id);

            //                     }
            //                     else
            //                     {
            //                         $new_product_cat_check[$new_cat]=[$check_pd_id];
            //                     }
            //                 }
            //             endforeach;
            //         endif;
            //         endforeach;
                    

            //         //all shop all products loop
            //         $exclude_cat_shop_all=[];
            //         $other_products=[];
            //         // $in_prdct=[];
                    
            //         $all_ids = get_posts( array(
            //             'post_type' => 'product',
            //             'numberposts' => -1,
            //             'post_status' => 'publish',
            //             'fields' => 'ids',
            //             'tax_query' => array(
            //             array(
            //                 'taxonomy' => 'product_cat',
            //                 'field' => 'id',
            //                 'terms' => [$testpage_id],
            //                 'operator' => 'IN',
            //             )
            //             ),
            //         ) );
            //         foreach($all_ids as $check_pd_id):
            //             $terms = get_the_terms ($check_pd_id, 'product_cat');
            //             if ( !is_wp_error($terms)) : 
            //                 $skills_links = wp_list_pluck($terms, 'term_id'); 
            //                 foreach($skills_links as $new_cat):
            //                     if($new_cat!=120 && $new_cat!=98){
            //                         if(!array_key_exists($new_cat,$new_product_cat_check)):
            //                             if(array_key_exists($new_cat,$other_products)):
            //                                 if(!in_array($check_pd_id,$other_products[$new_cat])):
            //                                     array_push($other_products[$new_cat],$check_pd_id);
            //                                 else:
            //                                     $other_products[$new_cat]=[$check_pd_id];
            //                                 endif;
            //                             else:
            //                                 $other_products[$new_cat]=[$check_pd_id];
            //                             endif;
                                        

            //                         endif;

            //                     }
            //                 endforeach;
            //             endif;
            //             endforeach;
            //         $inproducts=[];
            //         $all_inproducts=array_merge($new_product_cat_check,$other_products);
            //         foreach($all_inproducts as $key => $inproduct):
            //             foreach($inproduct as $value):
            //             array_push($inproducts,$value);
            //             endforeach;
            //         endforeach;

            //     $taxquery = array(
            //         array(
            //             'taxonomy' => 'product_cat',
            //             'field' => 'id',
            //             'terms' => $testpage_id,
            //             'operator'=> 'IN'
            //         )
            //     );
            //     $q->set( 'tax_query', $taxquery );
            //     $q->set( 'post__in', $inproducts ); 
            //     return $q;
            //     endif;

            // }
            // else
             if(in_array($testpage_id,array_column(unserialize($all_affiliates[0]->cust_aff_product_cat),'cat_id')))
            {
                $cat_ids_on_sale = array_column(unserialize($all_affiliates[0]->cust_aff_product_cat),'cat_id');
                $product_ids_on_sale = array_column(unserialize($all_affiliates[0]->cust_aff_product),'product_id');
                //print_r($product_ids_on_sale);
                // $q->set( 'post__in', (array) $product_ids_on_sale ); 
                $new_product_cat_check=[];
                if(!empty($product_ids_on_sale)):
                    foreach($product_ids_on_sale as $check_pd_id):
                    $terms = get_the_terms ($check_pd_id, 'product_cat');
                    if ( !is_wp_error($terms)) : 
                        $skills_links = wp_list_pluck($terms, 'term_id'); 
                        // $skills_yo = implode(", ", $skills_links);
                        if(in_array($testpage_id,$skills_links)):
                            array_push($new_product_cat_check,$check_pd_id);
                        endif;
                    endif;
                    endforeach;
                    if(!empty($new_product_cat_check)):
                $taxquery = array(
                    array(
                        'taxonomy' => 'product_cat',
                        'field' => 'id',
                        'terms' => $cat_ids_on_sale,
                        'operator'=> 'IN'
                    )
                );
                $q->set( 'tax_query', $taxquery );
                        $q->set( 'post__in', (array) $new_product_cat_check ); 

                    endif;
                endif;
            }
        }
	}
	return $q;
}
add_action( 'woocommerce_product_query', 'testing_woo_product_query' );
function check_product_in_custom_affiliate($product){
        global $wpdb;
        $p_id =$product->get_id();
        $slug = "";
        $main_affiliates = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}custom_affiliate where affiliate_type='main' and status='active'" );
        if($main_affiliates){
            $slug =  $main_affiliates[0]->custom_slug;
        }
        if($product->get_parent_id()>0){
            $p_id = $product->get_parent_id();
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
                $affiliate_type = $all_affiliates[0]->affiliate_type;
                if($affiliate_type=='generic_affiliate'){
                    $generic_affiliate = get_post_meta($p_id,'generic-affiliate-rate',true);
                    if($generic_affiliate){
                        return true;
                    }
                }else{
                    $ret = true;
                    if($all_affiliates[0]->cust_aff_product){
                        $ret = false;
                        $cust_aff_product = unserialize($all_affiliates[0]->cust_aff_product);
                        foreach ($cust_aff_product as $key => $value){
                            if($p_id==$value['product_id']){
                                return true;
                            }
                        }
                    }
                    if($all_affiliates[0]->cust_aff_product_cat){
                        $ret = false;
                        $cust_aff_product_cat = unserialize($all_affiliates[0]->cust_aff_product_cat);
                        foreach ($cust_aff_product_cat as $key => $value){
                            if (is_object_in_term($p_id,'product_cat',$value['cat_id'])){
                                return true;
                            }
                        }
                    }
                    if($ret){
                       return true;     
                    }
                }
            }
            else{
                return true;
            }
        }else{
            return true;
        }
        return false; // X3 for testing
    }
// add_filter( 'woocommerce_package_rates', 'custom_shipping_costs', 20, 2 );
function custom_shipping_costs( $rates, $package ) {
    global $wpdb;
    $slug = "";
    $disable = true;
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
            $all_affiliates = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}custom_affiliate where custom_slug='$slug'");
            if($all_affiliates){
                $time_now = time();
                $affiliate_offering_status = $all_affiliates[0]->affiliate_offering_status;
                $offer_start_time = $all_affiliates[0]->offer_start_time;
                $offer_end_time = $all_affiliates[0]->offer_end_time;
                if($affiliate_offering_status =="offer"){
                    if(($offer_start_time) && ($offer_end_time) && ($time_now>strtotime($offer_start_time)) && (strtotime($offer_end_time)>$time_now)){
                            $disable = false;
                    }
                }else{
                    $disable = false;
                }
                if($disable===false){
                    $affiliate_type = $all_affiliates[0]->affiliate_type;
                    if($affiliate_type=='generic_affiliate'){
                        // if($generic_affiliate){
                        // }
                    }else{
                            $prod_ship = false; 
                            $new_cost = $all_affiliates[0]->cust_aff_shipping_price;
                            $new_shipping_cost = 0;
                            $rate_change = 0;
                            if($all_affiliates[0]->cust_aff_product_cat){      
                                foreach ( WC()->cart->get_cart() as $cart_item ) {
                                    $product_id = $cart_item['product_id'];
                                    $cust_aff_product_cat = unserialize($all_affiliates[0]->cust_aff_product_cat);
                                    foreach ($cust_aff_product_cat as $key => $value){
                                        if (is_object_in_term($product_id,'product_cat',$value['cat_id'])){
                                            if($prod_ship==false){
                                                $prod_ship = true;
                                            }
                                            // if($new_shipping_cost<$value['shipping']){
                                            //     $new_shipping_cost += $value['shipping'];
                                            // }
                                            if(!empty($value['shipping'])):
                                                $new_shipping_cost += $value['shipping'];
                                                $rate_change++;
                                            endif;
                                        }
                                    }
                                    
                                }
                            }

                            if($all_affiliates[0]->cust_aff_product){      
                                foreach ( WC()->cart->get_cart() as $cart_item ) {
                                    $product_id = $cart_item['product_id'];
                                    $cust_aff_product = unserialize($all_affiliates[0]->cust_aff_product);
                                    foreach ($cust_aff_product as $key => $value){
                                        if($product_id==$value['product_id']){
                                            if($prod_ship==false){
                                                $prod_ship = true;
                                            }
                                            // if($new_shipping_cost<$value['shipping']){
                                            //     $new_shipping_cost += $value['shipping'];
                                            // }
               
                                            if(!empty($value['shipping'])):
                                                $new_shipping_cost += $value['shipping'];
                                                $rate_change++;
                                            endif;
                                        }
                                    }
                                    
                                }
                            }
                            if($prod_ship== true){
                                foreach( $rates as $rate_key => $rate ){
                                
                                    // Set rate cost
                                    // $rates[$rate_key]->cost = (float)$new_shipping_cost;
                                    if($new_shipping_cost==0){
                                        $rates[$rate_key]->label = "Free Shipping";
                                    }
                                    // echo $rate_change;exit;
                                    if($rate_change>0)
                                    {
                                        $rates[$rate_key]->cost = (float)$new_shipping_cost;                     
                                        
                                    }
                                    // Set taxes rate cost (if enabled)
                                    /*$taxes = array();
                                    foreach ($rates[$rate_key]->taxes as $key => $tax){
                                        if( $rates[$rate_key]->taxes[$key] > 0 )
                                            $taxes[$key] = $new_shipping_cost;
                                    } 
                                    $rates[$rate_key]->taxes = $taxes;*/
                                }    
                            }
                            
                        
                    }
                }
            }
        } 
    return $rates;
}

function custom_redirect_shippingfdsfdsfds() {
    $has_affiliates=0;
    
    global $wpdb;
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
        if(empty($all_affiliates)){
            $has_affiliates=1;
        }
    }
    if($has_affiliates==0)
    {
        add_filter( 'woocommerce_package_rates', 'bbloomer_hide_free_shipping_for_shipping_class', 9999, 2 );
    }
}
// add_action('template_redirect', 'custom_redirect_shipping');
add_action( 'wp_ajax_nopriv_delete-aff', 'delete_aff' );
add_action( 'wp_ajax_delete-aff', 'delete_aff' );
function delete_aff(){
    $ca_id = $_POST['ca_id'];
    global $wpdb;
    $table_name = $wpdb->prefix . 'custom_affiliate';
    $wpdb->delete( $table_name, array('ca_id' => $ca_id) );
}
add_action( 'flatsome_before_header', 'add_offer_text_timmer');
function add_offer_text_timmer(){
    $poid=get_queried_object_id();
    $treat_as_home_page=get_field('treat_as_home_page',$poid); 
    if(is_front_page() || is_home() || (isset($treat_as_home_page) && $treat_as_home_page=='1')){
        global $wpdb;
        $slug ="";
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
                $offer_text = $all_affiliates[0]->offer_text;
                $offer_button_text = $all_affiliates[0]->offer_button_text;
                $offer_button_link = $all_affiliates[0]->offer_button_link;
                $offer_start_time = $all_affiliates[0]->offer_start_time;
                $offer_end_time = $all_affiliates[0]->offer_end_time;
                $time_now = time();
                if(($offer_text || $offer_button_text || $offer_button_link || $offer_start_time || $offer_end_time) && ($time_now>strtotime($offer_start_time)) && (strtotime($offer_end_time)>$time_now)){ ?>
                        <div class="timer-header">
                            <div class="flex-row container">
                                <div class="timer-inner">
                                    <?php 
                                    if($offer_text || $offer_button_text || $offer_button_link ){ ?>
                                    <div class="timer-head">
                                        <div class="timer-head-text"><?php echo $offer_text; ?>
                                            <?php if($offer_button_text): ?><a href="<?php echo $offer_button_link; ?>" class="button"><?php echo $offer_button_text; ?></a><?php endif; ?>
                                        </div>
                                    </div> 
                                    <?php 
                                    }
                                    
                                    if(($offer_start_time) && ($offer_end_time) ){ ?>
                                        <p class="timer-text">Offer ends in : <span id="demo"></span></p>
                                        <script type="text/javascript">
                                            var countDownDate = <?php echo strtotime($offer_end_time); ?> * 1000;
                                            var now = <?php echo $time_now; ?> * 1000;
                                            var x = setInterval(function() {// Get today's date and time
                                                now = now + 1000;
                                                // Find the distance between now and the count down date
                                                var distance = countDownDate - now;
                                                 
                                                // Time calculations for days, hours, minutes and seconds
                                                var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                                                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                                var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                                                
                                                // Output the result in an element with id="demo"
                                                document.getElementById("demo").innerHTML = days + "d " + hours + "h "
                                                + minutes + "m " + seconds + "s ";
                                                
                                                // If the count down is over, write some text 
                                                if (distance < 0) {
                                                    clearInterval(x);
                                                    location.reload();
                                                }
                                            }, 1000);
                                        </script>
                                    <?php 
                                    } ?>
                                </div>
                            </div>
                        </div>
                        <style type="text/css">
                            .timer-header {
                                background: #f63e28;
                                z-index: 999;
                                padding: 6px;
                            }
                            .timer-header .timer-head {
                                margin-bottom: 8px;
                            }
                            .timer-inner {
                                text-align: center;
                                width: 100%;
                            }
                            .timer-header .timer-inner p{
                                margin: 0;
                                color: #FFF; 
                                font-weight: 500; font-size: 20px;
                            }
                            .timer-header .timer-head-text {
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                color: #FFF;
                            }
                            .timer-header a.button {
                                margin: 0 !important;
                                display: inline-block;
                                width: auto;
                                vertical-align: middle !important;
                                box-shadow: none;
                                font-size: 14px;
                                background: #49ad2b;
                                margin-left: 8px !important;
                            }
                        </style>

                    <?php
                }
            }
        }
    }
}


function wp_custom_sort_get_terms_args( $args, $taxonomies ) 
{
    if((isset($_COOKIE['custom_affiliate_slug']) && ($_COOKIE['custom_affiliate_slug'])) || (isset($_GET['c']) && ($_GET['c'])) ){
        global $wpdb;
        if(isset($_COOKIE['custom_affiliate_slug']) && ($_COOKIE['custom_affiliate_slug'])){
        	 $slug = $_COOKIE['custom_affiliate_slug'];
		}
        if(isset($_GET['c']) && ($_GET['c'])){
            $slug = $_GET['c'];
        }
        $all_affiliates = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}custom_affiliate where custom_slug='$slug'" );
            if(!empty($all_affiliates)) {
                    $affiliate_type = $all_affiliates[0]->affiliate_type;
                        if($all_affiliates && $all_affiliates[0]->cust_aff_product_cat){
                            $cat_ids_on_sale = array_column(unserialize($all_affiliates[0]->cust_aff_product_cat),'cat_id');
                            if(in_array("product_cat", $args['taxonomy'])):
                                // if(is_page('936') || is_shop()):                                
                                //     $args['include']=$cat_ids_on_sale;
                                // elseif(is_archive(  ) && $args['hide_empty']==1):
                                //     if(!in_array('product_type',$taxonomies)):
                                //         $args['include']=$cat_ids_on_sale;
                                //     endif;
                                //     // print_r($args)
                                // endif;
                            endif;
                            return $args;
                        }
                        else
                        {
                        return $args;

                        }
                }
                else
                {
                    return $args;

                }
    }
    else
    { 
        return $args;
    }
}
add_filter( 'get_terms_args', 'wp_custom_sort_get_terms_args', 10, 2 );

function webroom_hide_coupon_field_on_woocommerce( $enabled ) {

	if ( is_checkout() || is_cart() ) {


		global $wpdb;

		$slug = '';

		if ( (isset($_COOKIE['custom_affiliate_slug']) && ($_COOKIE['custom_affiliate_slug'])) || (isset($_GET['c']) && ($_GET['c'])) ) {

			if (isset($_COOKIE['custom_affiliate_slug']) && ($_COOKIE['custom_affiliate_slug'])) {
				$slug = $_COOKIE['custom_affiliate_slug'];
			}
			if ( isset($_GET['c']) && ($_GET['c']) ) {
				$slug = $_GET['c'];
			}

			$affiliate = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}custom_affiliate where custom_slug='$slug' limit 0, 1");//and affiliate_type <> 'main' 
			if ( !empty($slug) && !empty($affiliate) && is_array($affiliate) && count($affiliate) > 0 ) {
				$affiliate_type = $affiliate[0]->affiliate_type;
                if($affiliate_type=="generic_affiliate" || $affiliate_type=="custom_affiliate"):
				$enabled = false;
                endif;
			}


		}


	}

	return $enabled;
}
add_filter( 'woocommerce_coupons_enabled', 'webroom_hide_coupon_field_on_woocommerce' );


function variable_cust_price_cart($p_id,$var_id,$item_price)
{
	
		global $wpdb;
	if((isset($_COOKIE['custom_affiliate_slug']) && ($_COOKIE['custom_affiliate_slug'])) || (isset($_GET['c']) && ($_GET['c'])) ){
		$slug = $_COOKIE['custom_affiliate_slug'];
		if(isset($_GET['c']) && ($_GET['c'])){
		$slug = $_GET['c'];
		}

		$all_affiliates = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}custom_affiliate where custom_slug='$slug'");
					
		$var_product = wc_get_product($var_id);
		// $var_product->get_price();
		
		if($all_affiliates){
		
			$affiliate_type = $all_affiliates[0]->affiliate_type;
            
            $time_now = time();
            $offer_start_time = $all_affiliates[0]->offer_start_time;
            $affiliate_offering_status = $all_affiliates[0]->affiliate_offering_status;
            $offer_end_time = $all_affiliates[0]->offer_end_time;
            $enable=false;
            if($affiliate_offering_status =="offer"){
                
                if(($offer_end_time!="0000-00-00 00:00:00" && $offer_start_time!="0000-00-00 00:00:00") && ($time_now>strtotime($offer_start_time)) && (strtotime($offer_end_time)>$time_now))
                {
                    $enable=false;
                }
                else
                {
                    $enable=true;
                }
                    
            }
                if($enable==false)
                {
                    if($affiliate_type=='generic_affiliate'){
                        
                        $generic_affiliate = get_post_meta($p_id,'generic-affiliate',true);
                        $generic_affiliate_rate = get_post_meta($p_id,'generic-affiliate-rate',true);
                        if($generic_affiliate_rate=="percentage"){
                            $new_price=($var_product->get_price()/(1-((float)$generic_affiliate/100)));
                            if($new_price!=$item_price):
                            $item_price = '<del>'.wc_price($new_price).'</del> '.$item_price;
                            endif;

                        }else{
                            $new_price=(float)$var_product->get_price()+(float)$generic_affiliate;
                            if($new_price!=$item_price):
                                $item_price = '<del>'.wc_price($new_price).'</del> '.$item_price;
                            endif;
                        }
                        
                    }
                    else{
                        
                        if($all_affiliates[0]->cust_aff_product_cat){
                            $cust_aff_product_cat = unserialize($all_affiliates[0]->cust_aff_product_cat);
                            foreach ($cust_aff_product_cat as $key => $value){
                                if (is_object_in_term($p_id,'product_cat',$value['cat_id'])){
                                    if($value['discount']){
                                        if($value['rate']=="percentage"){
                                            $new_price=$var_product->get_price()/(1-($value['discount']/100));
                                            if($new_price!=$item_price):
                                                $item_price = '<del>'.wc_price($new_price).'</del> '.$item_price;
                                            endif;
                                        }else{
                                            $new_price=(float)$var_product->get_price()+(float)$value['discount'];
                                            if($new_price!=$item_price):
                                                $item_price = '<del>'.wc_price($new_price).'</del> '.$item_price;
                                            endif;
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
                                            $new_price=($var_product->get_price()/(1-($value['discount']/100)));
                                            if($new_price!=$item_price):
                                                $item_price = '<del>'.wc_price($new_price).'</del> '.$item_price;
                                            endif;
                                        }else{
                                            $new_price=(float)$var_product->get_price()+(float)$value['discount'];
                                            if($new_price!=$item_price):
                                                $item_price = '<del>'.wc_price($new_price).'</del> '.$item_price;
                                            endif;
                                        }
                                    }
                                }
                            }
                            
                        }	
                    }
                }
                else
                {
                    return $item_price;
                }
            }
	}
    else{
        return wc_price($item_price);
    }
	return $item_price;
}

function variable_cust_price($p_id,$var_id,$item_price)
{
	
		global $wpdb;
	if((isset($_COOKIE['custom_affiliate_slug']) && ($_COOKIE['custom_affiliate_slug'])) || (isset($_GET['c']) && ($_GET['c'])) ){
		$slug = $_COOKIE['custom_affiliate_slug'];
		if(isset($_GET['c']) && ($_GET['c'])){
		$slug = $_GET['c'];
		}

		$all_affiliates = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}custom_affiliate where custom_slug='$slug'");
					
		$var_product = wc_get_product($var_id);
		// $var_product->get_price();
		
		if($all_affiliates){
		
			$affiliate_type = $all_affiliates[0]->affiliate_type;
            
            $time_now = time();
            $offer_start_time = $all_affiliates[0]->offer_start_time;
            $affiliate_offering_status = $all_affiliates[0]->affiliate_offering_status;
            $offer_end_time = $all_affiliates[0]->offer_end_time;
            $enable=false;
            if($affiliate_offering_status =="offer"){
                // if(($offer_start_time) && ($offer_end_time) && (($offer_end_time=="0000-00-00 00:00:00" && $offer_start_time=="0000-00-00 00:00:00") || ($time_now<strtotime($offer_start_time)) && (strtotime($offer_end_time)<$time_now))){
                //     $enable=false;
                // }
                if(($offer_end_time!="0000-00-00 00:00:00" && $offer_start_time!="0000-00-00 00:00:00") && ($time_now>strtotime($offer_start_time)) && (strtotime($offer_end_time)>$time_now))
                {
                    $enable=false;
                }
                else
                {
                    $enable=true;
                }
                    
            }
            if($enable==false)
            {
                if($affiliate_type=='generic_affiliate'){
                    
                    $generic_affiliate = get_post_meta($p_id,'generic-affiliate',true);
                    $generic_affiliate_rate = get_post_meta($p_id,'generic-affiliate-rate',true);
                    if($generic_affiliate_rate=="percentage"){
                        $new_price=($var_product->get_price()/(1-((float)$generic_affiliate/100)));
                        if($new_price!=$item_price):
                            $item_price = '<del>'.wc_price($new_price).'</del> '.wc_price($item_price);
                        else:
                            $item_price=wc_price($item_price);
                        endif;

                    }else{
                        $new_price=(float)$var_product->get_price()+(float)$generic_affiliate;
                        if($new_price!=$item_price):
                            $item_price = '<del>'.wc_price($new_price).'</del> '.wc_price($item_price);
                        else:
                            $item_price=wc_price($item_price);
                        endif;
                    }
                    
                }
                else{
                    
                    if($all_affiliates[0]->cust_aff_product_cat){
                        $cust_aff_product_cat = unserialize($all_affiliates[0]->cust_aff_product_cat);
                        foreach ($cust_aff_product_cat as $key => $value){
                            if (is_object_in_term($p_id,'product_cat',$value['cat_id'])){
                                if($value['discount']){
                                    if($value['rate']=="percentage"){
                                        $new_price=$var_product->get_price()/(1-($value['discount']/100));
                                        if($new_price!=$item_price):
                                            $item_price = '<del>'.wc_price($new_price).'</del> '.wc_price($item_price);
                                        else:
                                            $item_price=wc_price($item_price);
                                        endif;
                                    }else{
                                        $new_price=(float)$var_product->get_price()+(float)$value['discount'];
                                        if($new_price!=$item_price):
                                            $item_price = '<del>'.wc_price($new_price).'</del> '.wc_price($item_price);
                                        else:
                                            $item_price=wc_price($item_price);
                                        endif;
                                    }
                                }
                                else
                                {
                                    $item_price=wc_price($item_price);
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
                                        $new_price=($var_product->get_price()/(1-($value['discount']/100)));
                                        if($new_price!=$item_price):
                                            if(strpos(strip_tags($item_price), get_woocommerce_currency_symbol()) !== false){
                                            }
                                            else
                                            {
                                                $item_price=wc_price($item_price);
                                            }
                                            $item_price = '<del>'.wc_price($new_price).'</del> '.$item_price;
                                        else:
                                            $item_price=wc_price($item_price);
                                        endif;
                                    }else{
                                        $new_price=(float)$var_product->get_price()+(float)$value['discount'];
                                        if($new_price!=$item_price):
                                            
                                            if(strpos(strip_tags($item_price), get_woocommerce_currency_symbol()) !== false){
                                            }
                                            else
                                            {
                                                $item_price=wc_price($item_price);
                                            }
                                            $item_price = '<del>'.wc_price($new_price).'</del> '.$item_price;
                                        else:
                                            $item_price=wc_price($item_price);
                                        endif;
                                    }
                                }
                                else
                                {
                                    $item_price=wc_price($item_price);
                                }
                            }
                        }
                        
                    }	
                }
            }
            else
            {
                return $item_price;
            }
		}
	}
    else{
        return wc_price($item_price);
    }
	return $item_price;
}

add_filter( 'woocommerce_available_payment_gateways', 'bbloomer_gateway_disable_for_shipping_rate' );
  
function bbloomer_gateway_disable_for_shipping_rate( $available_gateways ) {
   if ( ! is_admin() ) {
       
        if((isset($_COOKIE['custom_affiliate_slug']) && ($_COOKIE['custom_affiliate_slug'])) || (isset($_GET['c']) && ($_GET['c'])) ){
            global $wpdb;
            if(isset($_COOKIE['custom_affiliate_slug']) && ($_COOKIE['custom_affiliate_slug'])){
                $slug = $_COOKIE['custom_affiliate_slug'];
            }
            if(isset($_GET['c']) && ($_GET['c'])){
                $slug = $_GET['c'];
            }
            
            $all_affiliates = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}custom_affiliate where custom_slug='$slug'" );
            if($all_affiliates){
                            
                            
                if (  class_exists( 'WC_Deposits_Cart_Manager' ) ) {
                    
                            $has_depo=0;
                        if ( ! is_null( WC()->cart ) ) {
                            foreach ( WC()->cart->get_cart() as $cart_item ) {
                                if ( ! empty( $cart_item['is_deposit'] ) ) {
                                    $has_depo=1;
                                }
                            }
                        }
                // $chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
                // $chosen_shipping = $chosen_methods[0];
                    if($has_depo):
                            if ( isset( $available_gateways['affirm'] )) {
                                unset( $available_gateways['affirm'] );
                            }
                            if ( isset( $available_gateways['stripe_googlepay'] )) {
                                unset( $available_gateways['stripe_googlepay'] );
                            }
                            if ( isset( $available_gateways['ppcp-gateway'] )) {
                                unset( $available_gateways['ppcp-gateway'] );
                            }
                    endif;
                }
                
                
                }
        }
   }
   return $available_gateways;
}

// add_filter( 'woocommerce_get_item_data', 'display_cart_item_custom_meta_data', 10, 2 );
function display_cart_item_custom_meta_data( $item_data, $cart_item ) {
    // $meta_key = 'PR CODE';
    if ( isset($cart_item['is_deposit']) && $cart_item['is_deposit']==1 ) {
        // $cart_item['line_subtotal']=$cart_item['full_amount'];
        // $cart_item['line_total']=$cart_item['full_amount'];
    //     $item_data[] = array(
    //         'key'       => $meta_key,
    //         'value'     => $cart_item['add_size'][$meta_key],
    //     );
    }
    echo "<pre>";
    print_r($cart_item);
    return $item_data;
}

// // Save cart item custom meta as order item meta data and display it everywhere on orders and email notifications.
// add_action( 'woocommerce_checkout_create_order_line_item', 'save_cart_item_custom_meta_as_order_item_meta', 10, 4 );
// function save_cart_item_custom_meta_as_order_item_meta( $item, $cart_item_key, $values, $order ) {
//     $meta_key = 'PR CODE';
//     if ( isset($values['add_size']) && isset($values['add_size'][$meta_key]) ) {
//         $item->update_meta_data( $meta_key, $values['add_size'][$meta_key] );
//     }
// }

// add_filter( 'woocommerce_cart_item_subtotal', 'show_coupon_item_subtotal_discountt', 100, 3 );
function show_coupon_item_subtotal_discountt( $subtotal, $cart_item, $cart_item_key ){
    global $woocommerce;
    //$cartsub = wp_proce(0);
    $cartsub = 0;
    if ( isset($cart_item['is_deposit']) && $cart_item['is_deposit']==1 ) {
        $cart_item['line_subtotal']=$cart_item['full_amount'];
        $cart_item['line_total']=$cart_item['full_amount'];
    }
    $line_subtotal = $cart_item['line_subtotal'];//$110
    $line_total    = number_format(floor(($cart_item['line_total']*100))/100,2);//144.51
    if( $line_subtotal !== $line_total ) {
        $discount = 0;
        $cpn=0;
        foreach ( WC()->cart->get_coupons() as $code => $coupon_notsure ) {
            $coupon = new WC_Coupon($code);
             $discount_type = $coupon->get_discount_type(); // Get coupon discount type
            if ($discount_type=="percent"){
                $discount += $line_subtotal*($coupon->get_amount()/100);
            }
            if ($discount_type=="fixed"){
                $discount += $coupon->get_amount();
            }
            $cpn++; 
        }
        
        
        
        $subtotal_tax  = $cart_item['line_subtotal_tax'];//$12.20
        $total_tax     = $cart_item['line_tax'];//10.39598131
        $incl_taxes    = WC()->cart->display_prices_including_tax() && $cart_item['data']->is_taxable();

        $raw_subtotal = $incl_taxes ? $line_subtotal + $subtotal_tax : $line_subtotal;
        $raw_total    = $incl_taxes ? $line_total + $total_tax : $line_total;
        ///$subtotal     = sprintf( '<del>%s</del> <ins>%s<ins>',  wc_price($raw_subtotal), wc_price($raw_total) );
        $subtotal     = sprintf( '<ins>%s<ins>',  wc_price($raw_subtotal) );
        $cartsub1 =  (float)((float)($raw_total)+$cartsub);
        $cartsub = (floor($cartsub1*100)/100);
        $woocommerce->cart->total=($line_subtotal-$discount)+$total_tax;

$woocommerce->cart->cart_contents_total =($line_subtotal-$discount)+$total_tax;
        
        //print_r($woocommerce->cart);
        //$subtotal=$cartsub;
    }else{
        $cartsub=$line_subtotal;
    }
    $woocommerce->cart->subtotal =$cartsub;
    
    
    //$cart_item['line_subtotal']=$line_subtotal;
    return $subtotal;
}
