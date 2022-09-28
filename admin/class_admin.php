<?php
if ( ! class_exists( 'CustomAffiliate_Admin' ) ) {
    class CustomAffiliate_Admin {
        /**
         * Constructor
         */

        public function __construct() {  
            $this->add_admin_functionality();
            $affiliate_type_admin = new Affiliate_Type_Admin();
            $affiliate_type_admin->load_affiliate_type_hook();
            add_action('admin_menu',array( $this,'menufunc'));
            add_action( 'admin_enqueue_scripts', array($this,'load_admin_styles' ));
        }
        public function add_admin_functionality(){
            require_once(CustomAffiliate_PLUGIN_PATH. 'admin/class-affiliate-type.php');

        }
        public function load_admin_styles(){
            wp_enqueue_style( 'admin_css_ca', CustomAffiliate_PLUGIN_URL . 'public/css/ca-admin.css', false, '1.0.0' );
        }
        public function menufunc(){
            add_menu_page(__('Custom Funnels'), __('Custom Funnels','CustomAffiliate.com'), 'manage_options', 'customaffiliate', array(&$this, 'custom_affiliate_page'));
            add_submenu_page('customaffiliate', __('Settings','CustomAffiliate.com'), __('Settings','meuser.com'), 'manage_options', 'customaffiliate_settings', array(&$this, 'customaffiliate_settings'));
            add_submenu_page('customaffiliate', __('Import','CustomAffiliate.com'), __('Import','meuser.com'), 'manage_options', 'customaffiliate_import', array(&$this, 'customaffiliate_import'));
            $addpage = add_submenu_page( 
                '', 
                'Add Affiliate Page', 
                'Add Affiliate Page', 
                'manage_options', 
                'addaffiliatepage', 
                array( $this,'ca_admin_page_add_content')
            );
            add_action('load-'. $addpage, array( $this,'ca_load_admin_page_menu') );
            $mypage = add_submenu_page( 
                '', 
                'Edit Affiliate Page', 
                'Edit Affiliate Page', 
                'manage_options', 
                'editaffiliatepage', 
                array( $this,'ca_admin_page_edit_content')
            );
            add_action('load-'. $mypage, array( $this,'ca_load_admin_page_menu') );
            
        }
        public function ca_load_admin_page_menu(){

        }
        public function custom_affiliate_page(){
            global $wpdb;
            $all_affiliates = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}custom_affiliate");
            $i =1; ?>
            <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.css">

            <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.js"></script>
            <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
            <div class="custom-aff-heading">
            <h1>Custom Funnels</h1><a href="<?php echo admin_url('/admin.php'); ?>?page=addaffiliatepage">Add New</a> <a href="<?php echo admin_url('/admin.php');?>?page=customaffiliate&action=export_to_excel_affiliate">Export</a></div>
            <div class="custom-filter">
                <div class="custom-aff-filter">
                    <label>Custom Funnel Type</label>
                    <select name="affiliate_type" id="affiliate_type">
                        <option value="">ALL</option>
                        <option value="Generic Affiliate">Generic Affiliate</option>
                        <option value="Custom Affiliate">Custom Affiliate</option>
                        <option value="Sales Funnel">Sales Funnel</option>
                        <option value="Main">Main</option>
                    </select>
                </div>
                <div class="custom-aff-traffic-filter">
                    <label>Traffic Source</label>
                    <select name="traffic_source" id="traffic_source">
                        <option value="">ALL</option>
                        <option value="Email" data-id="email">Email</option>
                        <option value="Paid Social" data-id="paid_social">Paid Social</option>
                        <option value="Paid Search" data-id="paid_search">Paid Search</option>
                        <option value="Affiliates" data-id="affiliates">Affiliates</option>
                        <option value="Organic Social" data-id="organic_social">Organic Social</option>
                        <option value="Organic" data-id="organic">Organic</option>
                        <option value="Main" data-id="main">Main</option>
                        <option value="Other" data-id="other">Other</option>
                    </select>
                </div>
            </div>
            <table class="striped table-view-list affiliates" style="width:100%" id="cust-aff-listing">
                <thead>
                    <tr>
                        <th>Sl No</th>
                        <th>Title</th>
                        <th>Custom Slug</th>
                        <th>Type</th>
                        <th>Traffic Source</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <?php
                foreach ($all_affiliates as $each_affiliate) {
                ?>
                    <tr>
                        <td>
                            <?php echo $i; ?>
                        </td>
                        <td>
                            <?php 
                            if($each_affiliate->affiliate_type =='main'){ echo $each_affiliate->ca_title; }else{ ?>
                                <a href="<?php echo home_url().'?c='.$each_affiliate->custom_slug; ?>" target="_blank"><?php echo $each_affiliate->ca_title; ?></a>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if($each_affiliate->affiliate_type =='main'){ }else{ echo $each_affiliate->custom_slug; } ?>
                        </td>
                        <?php
                        $affiliate_type ="";
                        if($each_affiliate->affiliate_type =='sales_funnel'){
                                $affiliate_type ="Sales Funnel";
                        }
                        if($each_affiliate->affiliate_type =='custom_affiliate'){
                                $affiliate_type ="Custom Affiliate";
                        }
                        if($each_affiliate->affiliate_type =='generic_affiliate'){
                                $affiliate_type ="Generic Affiliate";
                        }
                        if($each_affiliate->affiliate_type =='main'){
                                $affiliate_type ="Main";
                        } ?>
                        <td>
                            <?php echo $affiliate_type; ?>
                        </td>
                        <td>
                            <?php echo ucwords(str_replace("_"," ",$each_affiliate->traffic_source)); ?>
                        </td>
                        <td>
                            <a href="<?php echo admin_url('/admin.php'); ?>?page=editaffiliatepage&ca_id=<?php echo $each_affiliate->ca_id ?>">Edit Funnel</a>
                            <a href="javascript:void(0)" class="delete-aff" data-caid="<?php echo $each_affiliate->ca_id ?>">Delete Funnel</a>
                        </td>
                    </tr>

                <?php $i++;
                }
                ?>
            </table>
            <script type="text/javascript">
                jQuery(document).ready(function($) {
                    $('#affiliate_type').on('change', function () {
                        $('#traffic_source option').show();
                        var affiliate_type = $(this).find('option:selected').val();
                        if(affiliate_type == "Generic Affiliate" || affiliate_type == "Custom Affiliate") {
                            $('#traffic_source').each(function () {
                                $("option:not(:contains('Affiliates'))", this).hide();
                            });
                        }else if(affiliate_type == "Sales Funnel") {
                            $('#traffic_source').each(function () {
                                $("option:contains('Affiliates'),option:contains('Main')", this).hide();
                            });
                        }else if(affiliate_type == "Main") {
                            $('#traffic_source').each(function () {
                                $("option:not(:contains('Main'))", this).hide();
                            });
                        }else{
                            $('#traffic_source option').show();
                        }
                    });
                    var table = jQuery('#cust-aff-listing').DataTable({ "bStateSave": true, "pageLength": 25,
                            "fnStateSave": function (oSettings, oData) {
                                localStorage.setItem('offersDataTables', JSON.stringify(oData));
                            },
                            "fnStateLoad": function (oSettings) {
                                return JSON.parse(localStorage.getItem('offersDataTables'));
                            }
                    });
                    
                    if(jQuery.cookie("affiliate_type")){
                        jQuery('#affiliate_type').val(jQuery.cookie("affiliate_type"));
                        var cookie_affiliate_type = jQuery.cookie("affiliate_type");
                        $('#traffic_source option').show();
                        if(cookie_affiliate_type == "Generic Affiliate" || cookie_affiliate_type == "Custom Affiliate") {
                            $('#traffic_source').each(function () {
                                $("option:not(:contains('Affiliates'))", this).hide();
                            });
                        }else if(cookie_affiliate_type == "Sales Funnel") {
                            $('#traffic_source').each(function () {
                                $("option:contains('Affiliates'),option:contains('Main')", this).hide();
                            });
                        }else if(cookie_affiliate_type == "Main") {
                            $('#traffic_source').each(function () {
                                $("option:not(:contains('Main'))", this).hide();
                            });
                        }else{
                            $('#traffic_source option').show();
                        }
                    }
                    if(jQuery.cookie("traffic_source")){
                        jQuery('#traffic_source').val(jQuery.cookie("traffic_source"));
                    }
                    jQuery('#affiliate_type').on( 'change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );
                        jQuery.cookie("affiliate_type",val,{ path: "/" });
                        table.columns(3).search( val ? '^'+val+'$' : '', true, false ).draw();
                    } );
                    jQuery('#traffic_source').on( 'change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );
                        jQuery.cookie("traffic_source",val,{ path: "/" });
                        table.columns(4).search( val ? '^'+val+'$' : '', true, false ).draw();
                    } );
                    jQuery('.delete-aff').on('click',function() {
                        jQuery.ajax({
                            type : "POST",
                            dataType : "json",
                            url : "<?php echo admin_url('admin-ajax.php'); ?>",
                            data : {action: "delete-aff","ca_id":jQuery(this).attr('data-caid')},
                            success: function(response) {
                                jQuery(this).parent().parent().remove();
                                alert("Funnel Delete Successfully");
                                location.reload();
                                
                            }
                        });
                    });
                });
            </script> <?php
        }
        public function ca_admin_page_add_content(){ 
            
            if(isset($_POST['add-custom-affiliate']))
            { 
                $data = array();
                $data['ca_title'] = $_POST['ca_title'];
                $data['affiliate_type'] = $_POST['affiliate_type'];
                $data['custom_slug'] = $_POST['custom_slug'];
                $data['traffic_source'] = $_POST['traffic_source'];
                $data['scriptcode'] = $_POST['scriptcode'];
                $data['offer_text'] = $_POST['offer_text'];
                $data['offer_button_text'] = $_POST['offer_button_text'];
                $data['offer_button_link'] = $_POST['offer_button_link'];
                $data['offer_start_time'] = $_POST['offer_start_time'];
                $data['offer_end_time'] = $_POST['offer_end_time'];
                $data['cust_aff_style'] = $_POST['cust_aff_style'];
                $data['affiliate_offering_status'] = $_POST['affiliate_offering_status'];
                
                if($_POST['affiliate_type'] =='custom_affiliate'){
                    if(isset($_FILES["cust_aff_image"])){
                        $files = $_FILES["cust_aff_image"];
                        $file = array(
                            'name' => $files['name'],
                            'type' => $files['type'],
                            'tmp_name' => $files['tmp_name'],
                            'error' => $files['error'],
                            'size' => $files['size']
                        );
                        $_FILES = array("upload_file" => $file);
                        $attachment_id = media_handle_upload("upload_file", 0);
                        if (is_wp_error($attachment_id)) {
                            echo $imagemessage = "Image is not upload";
                            $data['cust_aff_image'] ="";
                        }else{
                            $data['cust_aff_image'] =$attachment_id;
                        }
                        
                    }
                    $data['page_id'] = $_POST['page_id'];
                    $data['land_page_url'] = $_POST['land_page_url'];
                    $data['cust_aff_text'] = $_POST['cust_aff_text'];
                    $data['cust_aff_button_text'] = $_POST['cust_aff_button_text'];
                    $data['cust_aff_button_link'] = $_POST['cust_aff_button_link'];
                    $data['cust_aff_style'] = $_POST['cust_aff_style'];
                    if($_POST['product_rates'][0]['product_id']){
                        $c =  $_POST['product_rates']; 
                        $data['cust_aff_product'] =serialize($c);
                    }else{
                         $data['cust_aff_product'] ="";
                    }
                    $data['cust_aff_shipping_price'] =$_POST['shipping_price'];
                    /*
                    if($_POST['shipping_option']){
                        $data['cust_aff_shipping_id'] =$_POST['shipping_option'];
                        $data['cust_aff_shipping_price'] =$_POST['shipping_price'];
                    }else{
                        $data['cust_aff_shipping_id'] ="";
                        $data['cust_aff_shipping_price'] ="";
                    } */
                    if($_POST['product_cat_rates'][0]['cat_id']){
                        $cat =  $_POST['product_cat_rates'];
                        $data['cust_aff_product_cat'] =serialize($cat);
                    }else{
                        $data['cust_aff_product_cat'] ="";
                    }
                }else if($_POST['affiliate_type'] =='sales_funnel'){
                    $data['page_id'] = $_POST['page_id'];
                    $data['land_page_url'] = $_POST['land_page_url'];
                    if($_POST['product_rates'][0]['product_id']){
                        $c = $_POST['product_rates']; 
                        $data['cust_aff_product'] =serialize($c);
                    }else{
                        $data['cust_aff_product'] ="";
                    }
                    if($_POST['product_cat_rates'][0]['cat_id']){
                        $cat = $_POST['product_cat_rates'];
                        $data['cust_aff_product_cat'] =serialize($cat);
                    }else{
                        $data['cust_aff_product_cat'] ="";
                    }
                    $data['cust_aff_shipping_price'] =$_POST['shipping_price'];
                    /*if($_POST['shipping_option']){
                        $data['cust_aff_shipping_id'] =$_POST['shipping_option'];
                        $data['cust_aff_shipping_price'] =$_POST['shipping_price'];
                    }else{
                        $data['cust_aff_shipping_id'] ="";
                        $data['cust_aff_shipping_price'] ="";
                    }*/
                }else if($_POST['affiliate_type']=='main'){

                    $data['status'] = $_POST['main_status'];
                    $data['page_id'] = '';
                    if($_POST['product_rates'][0]['product_id']){
                        $c = $_POST['product_rates']; 
                        $data['cust_aff_product'] =serialize($c);
                    }else{
                        $data['cust_aff_product'] ="";
                    }
                    if($_POST['product_cat_rates'][0]['cat_id']){
                        $cat = $_POST['product_cat_rates'];
                        $data['cust_aff_product_cat'] =serialize($cat);
                    }else{
                        $data['cust_aff_product_cat'] ="";
                    }
                    $data['cust_aff_shipping_price'] =$_POST['shipping_price'];
                }else{

                }
                //print_r($data);
                global $wpdb;
                $tablename = $wpdb->prefix.'custom_affiliate';
                $wpdb->insert($tablename,$data);
                 //print_r($wpdb);
            } ?>
            <link rel="stylesheet" type="text/css" href="<?php echo CustomAffiliate_PLUGIN_URL; ?>public/css/jquery.datetimepicker.css"/>
            <div class="custom-aff-heading">
            <h1>Add Affiliate</h1><a href="<?php echo admin_url('/admin.php'); ?>?page=customaffiliate">Back</a></div>
            <form action="<?php echo admin_url('/admin.php'); ?>?page=addaffiliatepage" method="post" enctype="multipart/form-data" class="add-ca-form">
            <div class="funnel-details">
                <h3>Funnel Detail</h3>
                <p>
                    <label for="smashing-post-class"><?php _e( "Funnel Name"); ?></label>
                    <br />
                    <input type="text" name="ca_title" required>
                </p>
                <p>
                    <label for="smashing-post-class"><?php _e( "Funnel Type"); ?></label>
                    <br />
                    <select name="affiliate_type" id="affiliate_type" width="100%">
                        <option value="generic_affiliate">Generic Affiliate</option>
                        <option value="custom_affiliate">Custom Affiliate</option>
                        <option value="sales_funnel">Sales Funnel</option>
                        <option value="main">Main</option>
                    </select>
                </p>
                <p class="hide main" style="display:none;">
                    <label for="smashing-post-class"><?php _e( "Status"); ?></label>
                    <br />
                    <select name="main_status" id="main_status" width="100%">
                        <option value="active">Active</option>
                        <option value="inactive">In Active</option>
                    </select>
                </p>
                <p id="c_id">
                    <label for="smashing-post-class"><?php _e( "Custom Slug"); ?></label>
                    <br />
                    <input type="text" required name="custom_slug" id="custom_slug" value="" width="100">
                    <span><?php echo home_url() ?>?c=<b id="custom_slug_val"></b></span>
                </p>
                <p>
                    <label for="smashing-post-class"><?php _e( "Traffic Source"); ?></label>
                    <br />
                    <select name="traffic_source" id="traffic_source" width="100%">
                        <option value="email">Email</option>
                        <option value="paid_social">Paid Social</option>
                        <option value="paid_search">Paid Search</option>
                        <option value="affiliates">Affiliates</option>
                        <option value="organic_social">Organic Social</option>
                        <option value="organic">Organic</option>
                        <option value="main">Main</option>
                        <option value="other">Other</option>
                    </select>
                </p>
                <p>
                    <label for="smashing-post-class"><?php _e( "Script ID"); ?></label>
                    <br />
                    <input type="text" name="scriptcode" width="100%">
                </p>
            </div>
            <div class="promotion">
                <h3>Promotion</h3>
            <p>
                <label for="smashing-post-class"><?php _e( "Promotion Offer Text"); ?></label>
                <br />
                <input type="text" name="offer_text" width="100%">
            </p>
            <p>
                <label for="smashing-post-class"><?php _e( "Promotion Offer Button Text"); ?></label>
                <br />
                <input type="text" name="offer_button_text" width="100%">
            </p>
            <p>
                <label for="smashing-post-class"><?php _e( "Promotion Offer Button Link"); ?></label>
                <br />
                <input type="text" name="offer_button_link" width="100%">
            </p>
            <p>
                <label for="smashing-post-class"><?php _e( "Promotion Offer Start Time"); ?></label>
                <br />
                <input type="text" value="" id="offer_start_time" autocomplete="nope" name="offer_start_time" width="100%"/>
            </p>
            <p>
                <label for="smashing-post-class"><?php _e( "Promotion Offer End Time"); ?></label>
                <br />
                <input type="text" value="" id="offer_end_time" autocomplete="nope" name="offer_end_time" width="100%"/>
            </p>
            <p>
                <label for="smashing-post-class"><?php _e( "Funnel Offer Status"); ?></label>
                <br />
                <select name="affiliate_offering_status" id="affiliate_offering_status" width="100%">
                    <option value="normal">Normal</option>
                    <option value="offer">Offer</option>
                </select>
            </p>
            
          <p class="hide custom_affiliate sales_funnel" style="display: none;">
            <label for="smashing-post-class"><?php _e( "Landing Page"); ?></label>
            <br />
            <?php 
                                $args = array(
                                        'sort_order' => 'asc',
                                        'sort_column' => 'post_title',
                                        'hierarchical' => 1,
                                        'exclude' => '',
                                        'include' => '',
                                        'meta_key' => '',
                                        'meta_value' => '',
                                        'authors' => '',
                                        'child_of' => 0,
                                        'parent' => -1,
                                        'exclude_tree' => '',
                                        'number' => '',
                                        'offset' => 0,
                                        'post_type' => 'page',
                                        'post_status' => 'publish'
                                    ); 
                                
                                    $args2 = array(
                                        'orderby' => 'post_title',
                                        'order' => 'asc',
                                        // 'hierarchical' => 1,
                                        // 'exclude' => '',
                                        // 'include' => '',
                                        // 'meta_key' => '',
                                        // 'meta_value' => '',
                                        // 'authors' => '',
                                        // 'child_of' => 0,
                                        // 'parent' => -1,
                                        // 'exclude_tree' => '',
                                        // 'number' => '',
                                        // 'offset' => 0,
                                        'post_type' => ['product','product_variation'],
                                        'post_status' => 'publish',
                                        'posts_per_page' => '-1'
                                    ); 
                                $pages = get_pages($args); // get all pages based on supplied args
                                // $product_pages = get_posts($args2); // get all pages based on supplied args
                            ?>
                                <select name="page_id" >
                                <option value="">--Pages--</option>

                                    <?php 
                                        foreach($pages as $page)
                                        {
                                    ?>
                                            <option value="<?php echo $page->ID; ?>"><?php echo $page->post_title; ?></option>
                                    <?php 
                                        }
                                        
                                        /*
                                        ?>
                                                <option value="">--Products--</option>
                                        <?php 
                                        
                                        foreach($product_pages as $product_page)
                                        {
                                            ?>
                                                    <option value="<?php echo $product_page->ID; ?>"><?php echo $product_page->post_title; ?></option>
                                            <?php 
                                            
                                        } */
                                    ?>
                                </select>
            </p>
            
            <p class=" hide custom_affiliate sales_funnel" style="display: none;">
                <label for="smashing-post-class"><?php _e( "Landing Page URL"); ?></label>
                <input type="text" name="land_page_url" width="100%">
            </p>
            </div>
            <div class="funnel_offer_detail hide custom_affiliate"  style="display: none;">
            <h3>Affilate Detail</h3>
            <h6>
                Please use [custom_affiliate_shortcode] to show image text and button
            </h6>
            <p>
                <label for="smashing-post-class"><?php _e( "Image"); ?></label>
                <br />
                <input type="file" name="cust_aff_image" accept="image/png,image/jpeg" onchange="loadFile(event)">
                <img id="preview_image">
            </p>
            
            <p>
                <label for="smashing-post-class"><?php _e( "Affiliate Name"); ?></label>
                <br />
                <input type="text" name="cust_aff_text" width="100%">
            </p>
            <p>
                <label for="smashing-post-class"><?php _e( "Affiliate Button Text"); ?></label>
                <br />
                <input type="text" name="cust_aff_button_text" width="100%">
            </p>
            <p>
                <label for="smashing-post-class"><?php _e( "Affiliate Button Link"); ?></label>
                <br />
                <input type="text" name="cust_aff_button_link" width="100%">
            </p>
            </div>
            <p class="custom-style-box">
                <label for="smashing-post-class"><?php _e( "Custom Css Styling"); ?></label>
                <br />
                <textarea name="cust_aff_style"></textarea>
            </p>
        
        <?php
            if (in_array('woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins')))){ 
                $args = array(
                    'post_type'      => 'product',
                    'posts_per_page' => -1,
                );
                $products = get_posts($args);
                $product_cats = get_terms(array(
                                    'taxonomy' => 'product_cat',
                                    'hide_empty' => false,
                                )); ?>
            <div class="product-rates hide custom_affiliate sales_funnel main" style="display: none;">
                <h3>WooCommerce</h3>
                <table class="form-table wp-list-table widefat fixed posts affiliatewp-shipping" style="display:none">
                    <thead>
                        <tr>
                            <?php /*<th>Shipping Method</th> */ ?>
                            <th>Main Shipping Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr> <?php /*
                            <td class="demo">
                                <select name="shipping_option">
                                    <option value="">Please select the method</option>
                                <?php 
                                $shipping_methods = WC()->shipping->get_shipping_methods();
                                foreach($shipping_methods as $shipping_method){ ?>
                                    <option value="<?php echo $shipping_method->id; ?>"><?php echo $shipping_method->method_title; ?></option>
                                        <?php
                                } ?>
                                </select>
                            </td> */ ?>
                            <td><input type="text" name="shipping_price" class="test"></td>
                        </tr>
                    </tbody>
                </table>
                <table class="form-table wp-list-table widefat fixed posts affiliatewp-cat-rates">
                    <thead>
                        <tr>
                            <th>Product Category</th>
                            <th>Discount</th>
                            <th>Rate</th>
                            <th>Shipping</th>
                            <th style="width:5%;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="demo">
                                <select name="product_cat_rates[0][cat_id]"  placeholder="Select Categories" class="cat_test">
                                    <option value="">Select Category</option>
                                    <?php 
                                        if($product_cats):
                                            foreach($product_cats as $product_cat): ?>
                                                <option value="<?php echo $product_cat->term_id; ?>"><?php echo $product_cat->name;?></option>
                                            <?php endforeach;
                                        endif;
                                    ?>
                                </select>
                            </td>
                            <td>
                                <input name="product_cat_rates[0][discount]" type="text" value="" class="cat_test" />
                            </td>
                            <td>
                                <select name="product_cat_rates[0][rate]" class="cat_test"><option value="percentage">Percentage</option><option value="flat_rate">Flat Rate</option></select>
                            </td>
                            <td>
                                <input name="product_cat_rates[0][shipping]" type="text" value="" class="cat_test" />
                            </td>
                            <td>
                                <a href="javascript:void(0)" class="custom_aff_cat_remove_rate" style="background: url(<?php echo home_url(); ?>wp-admin/images/xit.gif) no-repeat;">×</a>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="1">
                                <button id="custom_aff_cat_new_rate_woocommerce"  class="button affwp_new_procat_rate">Add New Product Category Rate</button>
                            </th>
                            <th colspan="3">
                                
                            </th>
                        </tr>
                    </tfoot>
                </table>
                <table class="form-table wp-list-table widefat fixed posts affiliatewp-rates">
                    <thead>
                        <tr>
                            <th>Product/s</th>
                            <th>Discount</th>
                            <th>Rate</th>
                            <th>Shipping</th><?php /**/?>
                            <th style="width:5%;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="demo">
                                <select name="product_rates[0][product_id]"  placeholder="Select Products" class="prod_test">
                                    <option value="">Select Product</option>
                                    <?php 
                                        if($products):
                                            foreach($products as $product): ?>
                                                <option value="<?php echo $product->ID; ?>"><?php echo $product->post_title;?> - (<?php echo $product->ID; ?>)</option>
                                            <?php endforeach;
                                        endif;
                                    ?>
                                </select>
                            </td>
                            <td>
                                <input name="product_rates[0][discount]" type="text" value="" class="prod_test" />
                            </td>
                            <td>
                                <select name="product_rates[0][rate]" class="prod_test"><option value="percentage">Percentage</option><option value="flat_rate">Flat Rate</option></select>
                            </td>
                                
                            <td><input name="product_rates[0][shipping]" type="text" value="" class="prod_test" />
                            </td> <?php /**/?>
                            <td>
                                <a href="javascript:void(0)" class="custom_aff_remove_rate" style="background: url(<?php echo home_url(); ?>wp-admin/images/xit.gif) no-repeat;">×</a>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="1">
                                <button id="custom_aff_new_rate_woocommerce"  class="button affwp_new_rate">Add New Product Rate</button>
                            </th>
                            <th colspan="3">
                                
                            </th>
                        </tr>
                    </tfoot>
                </table>
                
            </div>

            <script type="text/javascript">
                jQuery(document).ready(function($) {
                    
                    $('.affwp_new_procat_rate').on('click', function(e) {
                        e.preventDefault();
                        var ClosestRatesTable = $(this).closest('.affiliatewp-cat-rates');

                        // clone the last row of the closest rates table
                        var row = ClosestRatesTable.find( 'tbody tr:last' );

                        // clone it
                        clone = row.clone();

                        // count the number of rows
                        var count = ClosestRatesTable.find( 'tbody tr' ).length;

                        // find and clear all inputs
                        clone.find( 'td input' ).val( '' );
                        clone.find( 'td select' ).val( '' );
                        // insert our clone after the last row
                        clone.insertAfter( row );

                        // replace the name of each input with the count
                        clone.find( '.cat_test' ).each(function() {
                            var name = $( this ).attr( 'name' );

                            name = name.replace( /\[(\d+)\]/, '[' + parseInt( count ) + ']');

                            $( this ).attr( 'name', name ).attr( 'id', name );
                        });
                    });

                    $("body").on("click",".custom_aff_cat_remove_rate",function(e){ 
                        var ClosestRatesTable = $(this).closest('.affiliatewp-cat-rates');
                        var count = ClosestRatesTable.find( 'tbody tr' ).length;
                        if(count==1){
                            $(this).closest('tr').find( 'td select' ).val( '' );
                            $(this).closest('tr').find( 'td input' ).val( '' );
                        }else{
                            $(this).closest('tr').remove();
                        }
                        e.preventDefault();

                    });



                    $('.affwp_new_rate').on('click', function(e) {
                        e.preventDefault();
                        var ClosestRatesTable = $(this).closest('.affiliatewp-rates');

                        // clone the last row of the closest rates table
                        var row = ClosestRatesTable.find( 'tbody tr:last' );

                        // clone it
                        clone = row.clone();

                        // count the number of rows
                        var count = ClosestRatesTable.find( 'tbody tr' ).length;

                        // find and clear all inputs
                        clone.find( 'td input' ).val( '' );
                        clone.find( 'td select' ).val( '' );
                        // insert our clone after the last row
                        clone.insertAfter( row );

                        // replace the name of each input with the count
                        clone.find( '.prod_test' ).each(function() {
                            var name = $( this ).attr( 'name' );

                            name = name.replace( /\[(\d+)\]/, '[' + parseInt( count ) + ']');

                            $( this ).attr( 'name', name ).attr( 'id', name );
                        });
                    });

                    $("body").on("click",".custom_aff_remove_rate",function(e){ 
                        var ClosestRatesTable = $(this).closest('.affiliatewp-rates');
                        var count = ClosestRatesTable.find( 'tbody tr' ).length;
                        if(count==1){
                            $(this).closest('tr').find( 'td select' ).val( '' );
                            $(this).closest('tr').find( 'td input' ).val( '' );
                        }else{
                            $(this).closest('tr').remove();
                        }
                        e.preventDefault();

                    });

                    

                });
            </script>

        <?php
            }
             ?>
        <div class="add-btn"><input type="submit" name="add-custom-affiliate" value="Submit"></div>
        </form>
        <script type="text/javascript">
            var loadFile = function(event) {
                var output = document.getElementById('preview_image');
                output.src = URL.createObjectURL(event.target.files[0]);
                output.onload = function() {
                  URL.revokeObjectURL(output.src) // free memory
                }
            };
            jQuery(document).ready(function($) {
                $('#affiliate_type').on('change', function () {
                        $('#traffic_source option').show();
                        var affiliate_type = $(this).find('option:selected').val();
                        if(affiliate_type == "Generic Affiliate" || affiliate_type == "Custom Affiliate") {
                            $('#traffic_source').each(function () {
                                $("option:not(:contains('affiliates'))", this).hide();
                            });
                        }else if(affiliate_type == "Sales Funnel") {
                            $('#traffic_source').each(function () {
                                $("option:contains('affiliates'),option:contains('main')", this).hide();
                            });
                        }else if(affiliate_type == "Main") {
                            $('#traffic_source').each(function () {
                                $("option:not(:contains('main'))", this).hide();
                            });
                        }else{
                            $('#traffic_source option').show();
                        }
                    });
                $('#custom_slug').on('keyup change paste', function(e){
                     $('#custom_slug_val').html($(this).val())
                });
                $('#affiliate_type').on('change', function(e){
                    var affiliate_type = $(this).val();
                    if(affiliate_type=='main'){
                        var timestamp = new Date().getTime();
                        jQuery('#c_id').hide();
                        jQuery('#custom_slug').val(timestamp);
                    }else{
                        jQuery('#c_id').show();
                        jQuery('#custom_slug').val(''); 
                    }
                    
                    jQuery(".hide").hide();
                    jQuery("."+affiliate_type).show();
                });
                $('#offer_start_time').datetimepicker({
                        format: 'Y-m-d H:i:00',
                        minDate: 0,
                        onShow:function( ct ){
                            this.setOptions({
                                maxDate:jQuery('#offer_end_time').val()?jQuery('#offer_end_time').val():false
                            })
                        },
                    });
                    $('#offer_end_time').datetimepicker({
                        format: 'Y-m-d H:i:00',
                        minDate: 0,
                        onShow:function( ct ){
                            this.setOptions({
                                minDate:jQuery('#offer_start_time').val()?jQuery('#offer_start_time').val():false
                            })
                        },
                    });
            });
        </script>
        <script src="<?php echo CustomAffiliate_PLUGIN_URL; ?>public/js/php-date-formatter.min.js"></script>
        <script src="<?php echo CustomAffiliate_PLUGIN_URL; ?>public/js/jquery.mousewheel.js"></script>
        <script src="<?php echo CustomAffiliate_PLUGIN_URL; ?>public/js/jquery.datetimepicker.js"></script>

        <?php
        }
        public function ca_admin_page_edit_content(){
            global $wpdb;
            
           if(isset($_POST['update-custom-affiliate']))
            { 
                $data = array();

                $data['ca_title'] = $_POST['ca_title'];
                $data['affiliate_type'] = $_POST['affiliate_type'];
                $data['custom_slug'] = $_POST['custom_slug'];
                $data['traffic_source'] = $_POST['traffic_source'];
                $data['scriptcode'] = $_POST['scriptcode'];
                $data['offer_text'] = $_POST['offer_text'];
                $data['offer_button_text'] = $_POST['offer_button_text'];
                $data['offer_button_link'] = $_POST['offer_button_link'];
                $data['offer_start_time'] = $_POST['offer_start_time'];
                $data['offer_end_time'] = $_POST['offer_end_time'];
                $data['cust_aff_style'] = $_POST['cust_aff_style'];
                $data['affiliate_offering_status'] = $_POST['affiliate_offering_status'];
                if($_POST['affiliate_type'] =='custom_affiliate'){
                    $data['cust_aff_image'] =$_POST['old_image_id'];
                    $old_image_id = $_POST['old_image_id'];
                    if(isset($_FILES["cust_aff_image"]) && $_FILES["cust_aff_image"]){
                        $files = $_FILES["cust_aff_image"];
                        $file = array(
                            'name' => $files['name'],
                            'type' => $files['type'],
                            'tmp_name' => $files['tmp_name'],
                            'error' => $files['error'],
                            'size' => $files['size']
                        );
                        $_FILES = array("upload_file" => $file);
                        $attachment_id = media_handle_upload("upload_file", 0);
                        if (is_wp_error($attachment_id)) {
                            //echo $imagemessage = "Image is not upload";
                            
                        }else{
                            $data['cust_aff_image'] =$attachment_id;
                            wp_delete_attachment($old_image_id);
                        }  
                    }
                    $data['page_id'] = $_POST['page_id'];
                    $data['land_page_url'] = $_POST['land_page_url'];
                    $data['cust_aff_text'] = $_POST['cust_aff_text'];
                    $data['cust_aff_button_text'] = $_POST['cust_aff_button_text'];
                    $data['cust_aff_button_link'] = $_POST['cust_aff_button_link'];
                    
                    
                    if($_POST['product_rates'][0]['product_id']){
                        $c = $_POST['product_rates']; 
                        $data['cust_aff_product'] =serialize($c);
                    }else{
                         $data['cust_aff_product'] ="";
                    }

                    if( $_POST['product_cat_rates'][0]['cat_id']){
                        $cat =  $_POST['product_cat_rates'];
                        $data['cust_aff_product_cat'] =serialize($cat);
                    }else{
                        $data['cust_aff_product_cat'] ="";
                    }
                    $data['cust_aff_shipping_price'] =$_POST['shipping_price'];
                    /*
                    if($_POST['shipping_option']){
                        $data['cust_aff_shipping_id'] =$_POST['shipping_option'];
                        $data['cust_aff_shipping_price'] =$_POST['shipping_price'];
                    }else{
                        $data['cust_aff_shipping_id'] ="";
                        $data['cust_aff_shipping_price'] ="";
                    }*/
                }else if($_POST['affiliate_type'] =='sales_funnel'){
                    $data['page_id'] = $_POST['page_id'];
                    $data['land_page_url'] = $_POST['land_page_url'];
                    if($_POST['product_rates'][0]['product_id']){
                        $c =  $_POST['product_rates']; 
                        $data['cust_aff_product'] =serialize($c);
                    }else{
                         $data['cust_aff_product'] ="";
                    }
                    if( $_POST['product_cat_rates'][0]['cat_id']){
                        $cat =  $_POST['product_cat_rates'];
                        $data['cust_aff_product_cat'] =serialize($cat);
                    }else{
                        $data['cust_aff_product_cat'] ="";
                    }
                    $data['cust_aff_shipping_price'] =$_POST['shipping_price'];
                    /*
                    if($_POST['shipping_option']){
                        $data['cust_aff_shipping_id'] =$_POST['shipping_option'];
                        $data['cust_aff_shipping_price'] =$_POST['shipping_price'];
                    }else{
                        $data['cust_aff_shipping_id'] ="";
                        $data['cust_aff_shipping_price'] ="";
                    } */
                }else if($_POST['affiliate_type'] =='main'){
                    $data['status'] = $_POST['main_status'];
                    $data['page_id'] = '';
                    if($_POST['product_rates'][0]['product_id']){
                        $c = $_POST['product_rates']; 
                        $data['cust_aff_product'] =serialize($c);
                    }else{
                        $data['cust_aff_product'] ="";
                    }
                    if($_POST['product_cat_rates'][0]['cat_id']){
                        $cat = $_POST['product_cat_rates'];
                        $data['cust_aff_product_cat'] =serialize($cat);
                    }else{
                        $data['cust_aff_product_cat'] ="";
                    }
                    $data['cust_aff_shipping_price'] =$_POST['shipping_price'];
                } 
                $where = array('ca_id' => $_GET['ca_id']); 
                $tablename = $wpdb->prefix.'custom_affiliate';            
                $wpdb->update( $tablename,$data,$where 
                );

            }
            $ca_id = $_GET['ca_id'];
            $all_affiliates = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}custom_affiliate where ca_id='$ca_id'" );
            //print_r($all_affiliates);
            $affiliate_type = $all_affiliates[0]->affiliate_type;
            //print_r($all_affiliates); ?>
            <div class="custom-aff-heading">
            
            <link rel="stylesheet" type="text/css" href="<?php echo CustomAffiliate_PLUGIN_URL; ?>public/css/jquery.datetimepicker.css"/>
            <h1>Edit Affiliate</h1><a href="<?php echo admin_url('/admin.php'); ?>?page=customaffiliate">Back</a></div>
            <form action="<?php echo admin_url('/admin.php'); ?>?page=editaffiliatepage&ca_id=<?php echo $ca_id; ?>" method="post" enctype="multipart/form-data" class="add-ca-form">
            <div class="funnel-details">
            <h3>Funnel Detail</h3>    
            <p>
                <label for="smashing-post-class"><?php _e( "Funnel Name"); ?></label>
                <br />
                <input type="text" name="ca_title" value="<?php echo $all_affiliates[0]->ca_title; ?>" required>
            </p>
            <p>
            <label for="smashing-post-class"><?php _e( "Funnel Type"); ?></label>
            <br />
            <select name="affiliate_type" id="affiliate_type" width="100%">
                <option value="generic_affiliate" <?php if($all_affiliates[0]->affiliate_type=='generic_affiliate'){ echo "selected"; } ?>>Generic Affiliate</option>
                <option value="custom_affiliate" <?php if($all_affiliates[0]->affiliate_type=='custom_affiliate'){ echo "selected"; } ?>>Custom Affiliate</option>
                <option value="sales_funnel" <?php if($all_affiliates[0]->affiliate_type=='sales_funnel'){ echo "selected"; } ?>>Sales Funnel</option>
                <option value="main" <?php if($all_affiliates[0]->affiliate_type=='main'){ echo "selected"; } ?>>Main</option>
            </select>
          </p>
          <p class="hide main" style="display:none;">
            <label for="smashing-post-class"><?php _e( "Status"); ?></label>
            <br />
            <select name="main_status" id="main_status" width="100%">
                <option value="" >Select the status</option>
                <option value="active" <?php if($all_affiliates[0]->status=='active'){ echo "selected"; } ?>>Active</option>
                <option value="inactive" <?php if($all_affiliates[0]->status=='inactive'){ echo "selected"; } ?>>In Active</option>
            </select>
          </p>
          <p id="c_id">
            <label for="smashing-post-class"><?php _e( "Custom Slug"); ?></label>
            <br />
            <input type="text" required name="custom_slug" id="custom_slug" value="<?php echo $all_affiliates[0]->custom_slug; ?>" width="100">
            <span><a href="<?php echo home_url().'?c='.$all_affiliates[0]->custom_slug; ?>" target="_blank"><?php echo home_url() ?>?c=<b id="custom_slug_val"><?php echo $all_affiliates[0]->custom_slug; ?></b></a></span>
          </p>
          <p>
                <label for="smashing-post-class"><?php _e( "Traffic Source"); ?></label>
                <br />
                <select name="traffic_source" id="traffic_source" width="100%">
                    <option <?php if($all_affiliates[0]->traffic_source == 'email'){ echo 'selected'; } ?> value="email">Email</option>
                    <option <?php if($all_affiliates[0]->traffic_source == 'paid_social'){ echo 'selected'; } ?> value="paid_social">Paid Social</option>
                    <option <?php if($all_affiliates[0]->traffic_source == 'paid_search'){ echo 'selected'; } ?> value="paid_search">Paid Search</option>
                    <option <?php if($all_affiliates[0]->traffic_source == 'affiliates'){ echo 'selected'; } ?> value="affiliates">Affiliates</option>
                    <option <?php if($all_affiliates[0]->traffic_source == 'organic'){ echo 'selected'; } ?> value="organic">Organic</option>
                    <option <?php if($all_affiliates[0]->traffic_source == 'organic_social'){ echo 'selected'; } ?> value="organic_social">Organic Social</option>
                    <option <?php if($all_affiliates[0]->traffic_source == 'other'){ echo 'selected'; } ?> value="other">Other</option>
                </select>
            </p>
            <p>
                <label for="smashing-post-class"><?php _e( "Script ID"); ?></label>
                <br />
                <input type="text" name="scriptcode" value="<?php echo $all_affiliates[0]->scriptcode; ?>" width="100%">
            </p>
            </div>
            <div class="promotion">
                <h3>Promotion</h3>
            <p>
                <label for="smashing-post-class"><?php _e( "Promotion Offer Text"); ?></label>
                <br />
                <input type="text" name="offer_text" value="<?php echo $all_affiliates[0]->offer_text; ?>" width="100%">
            </p>
            <p>
                <label for="smashing-post-class"><?php _e( "Promotion Offer Button Text"); ?></label>
                <br />
                <input type="text" name="offer_button_text" value="<?php echo $all_affiliates[0]->offer_button_text; ?>" width="100%">
            </p>
            <p>
                <label for="smashing-post-class"><?php _e( "Promotion Offer Button Link"); ?></label>
                <br />
                <input type="text" name="offer_button_link" value="<?php echo $all_affiliates[0]->offer_button_link; ?>" width="100%">
            </p>
            <p>
                <label for="smashing-post-class"><?php _e( "Promotion Offer Start Time"); ?></label>
                <br />
                <input type="text"  id="offer_start_time" autocomplete="nope" value="<?php echo $all_affiliates[0]->offer_start_time; ?>" name="offer_start_time" width="100%"/>
            </p>
            <p>
                <label for="smashing-post-class"><?php _e( "Promotion Offer End Time"); ?></label>
                <br />
                <input type="text" id="offer_end_time" autocomplete="nope" value="<?php echo $all_affiliates[0]->offer_end_time; ?>" name="offer_end_time" width="100%"/>
            </p>
            <p>
                <label for="smashing-post-class"><?php _e( "Funnel Offer Status"); ?></label>
                <br />
                <select name="affiliate_offering_status" id="affiliate_offering_status" width="100%">
                    <option value="normal" <?php if($all_affiliates[0]->affiliate_offering_status == 'normal'){ echo 'selected'; } ?>>Normal</option>
                    <option value="offer" <?php if($all_affiliates[0]->affiliate_offering_status == 'offer'){ echo 'selected'; } ?>>Offer</option>
                </select>
            </p>
            <p class=" hide custom_affiliate sales_funnel" style="display: none;">
            <label for="smashing-post-class"><?php _e( "Landing Page"); ?></label>
            <br />
            <?php 
                                $args = array(
                                        'sort_order' => 'asc',
                                        'sort_column' => 'post_title',
                                        'hierarchical' => 1,
                                        'exclude' => '',
                                        'include' => '',
                                        'meta_key' => '',
                                        'meta_value' => '',
                                        'authors' => '',
                                        'child_of' => 0,
                                        'parent' => -1,
                                        'exclude_tree' => '',
                                        'number' => '',
                                        'offset' => 0,
                                        'post_type' => 'page',
                                        'post_status' => 'publish'
                                    ); 
                                    $args2 = array(
                                        'orderby' => 'post_title',
                                        'order' => 'asc',
                                        // 'hierarchical' => 1,
                                        // 'exclude' => '',
                                        // 'include' => '',
                                        // 'meta_key' => '',
                                        // 'meta_value' => '',
                                        // 'authors' => '',
                                        // 'child_of' => 0,
                                        // 'parent' => -1,
                                        // 'exclude_tree' => '',
                                        // 'number' => '',
                                        // 'offset' => 0,
                                        'post_type' => ['product','product_variation'],
                                        'post_status' => 'publish',
                                        'posts_per_page' => '-1'
                                    ); 
                                $pages = get_pages($args); // get all pages based on supplied args
                                // $product_pages = get_posts($args2); // get all pages based on supplied args
                            ?>
                                <select name="page_id" >
                                    
                                <option value="">--Pages--</option>
                                    <?php 
                                    $p=0;
                                        foreach($pages as $page)
                                        {
                                    ?>
                                            <option <?php if($all_affiliates[0]->page_id == $page->ID){ echo 'selected'; $p++;} ?> value="<?php echo $page->ID; ?>"><?php echo $page->post_title; ?></option>
                                    <?php 
                                        }
                                        
                                         /*   
                                            ?>
                                            <option value="">--Products--</option>
                                        <?php 
                                        foreach($product_pages as $product_page)
                                        {
                                          
                                            ?>
                                                    <option <?php if($all_affiliates[0]->page_id == $product_page->ID && $p==0){ echo 'selected'; $p++;} ?> value="<?php echo $product_page->ID; ?>"><?php echo $product_page->post_title; ?></option>
                                            <?php 
                                            // endif;
                                            
                                        } */
                                    ?>
                                </select>
          </p>
          <p class=" hide custom_affiliate sales_funnel" style="display: none;">
            <label for="smashing-post-class"><?php _e( "Landing Page URL"); ?></label>
            <input type="text" name="land_page_url" value="<?php echo isset($all_affiliates[0]->land_page_url)?$all_affiliates[0]->land_page_url:NULL; ?>" width="100%">
         </p>
            </div>
            <div class="funnel_offer_detail hide custom_affiliate"  style="display: none;">
            <h3>Affilate Detail</h3>
            <h6>
                Please use [custom_affiliate_shortcode] to show image text and button
            </h6>
            <p class="hide custom_affiliate" style="display: none;">
                <label for="smashing-post-class"><?php _e( "Image"); ?></label>
                <br />
                <input type="hidden" name="old_image_id" value="<?php echo $all_affiliates[0]->cust_aff_image; ?>">
                <input type="file" name="cust_aff_image" accept="image/png,image/jpeg" onchange="loadFile(event)">
                <?php $image_attributes = wp_get_attachment_image_src($all_affiliates[0]->cust_aff_image); ?>
                <img id="preview_image" <?php if($image_attributes){ ?> src="<?php echo $image_attributes[0]; ?>" <?php } ?> >
            </p>
            
            <p class="hide custom_affiliate" style="display: none;">
                <label for="smashing-post-class"><?php _e( "Affiliate Name"); ?></label>
                <br />
                <input type="text" name="cust_aff_text" value="<?php echo $all_affiliates[0]->cust_aff_text; ?>" width="100%">
            </p>
            <p class="hide custom_affiliate" style="display: none;">
                <label for="smashing-post-class"><?php _e( "Affilate Button Text"); ?></label>
                <br />
                <input type="text" name="cust_aff_button_text" value="<?php echo $all_affiliates[0]->cust_aff_button_text; ?>" width="100%">
            </p>
            <p class="hide custom_affiliate" style="display: none;">
                <label for="smashing-post-class"><?php _e( "Affilate Button Link"); ?></label>
                <br />
                <input type="text" name="cust_aff_button_link" value="<?php echo $all_affiliates[0]->cust_aff_button_link; ?>" width="100%">
            </p>
            <p class="custom-style-box">
                <label for="smashing-post-class"><?php _e( "Custom Css Style"); ?></label>
                <br />
                <textarea name="cust_aff_style"><?php echo $all_affiliates[0]->cust_aff_style; ?></textarea>
            </p>
            </div>
          
        <?php
            if (in_array('woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins')))){ 
                $args = array(
                    'post_type'      => 'product',
                    'posts_per_page' => -1,
                );
                $products = get_posts($args);
                $product_cats = get_terms(array(
                                    'taxonomy' => 'product_cat',
                                    'hide_empty' => false,
                        )); ?>
            <div class="product-rates  hide custom_affiliate sales_funnel main" style="display: none;">
                <h3>WooCommerce</h3>
                <table class="form-table wp-list-table widefat fixed posts affiliatewp-shipping" style="display:none">
                    <thead>
                        <tr>
                            <?php /*<th>Shipping Method</th> */ ?>
                            <th>Main Shipping Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php /*
                            <td class="demo">
                                <select name="shipping_option">
                                    <option value="">Please select the method</option>
                                <?php 
                                //print_r(WC()->shipping->get_shipping_methods());
                                $shipping_methods = WC()->shipping->get_shipping_methods();
                                foreach($shipping_methods as $shipping_method){ ?>
                                    <option value="<?php echo $shipping_method->id; ?>" <?php if($all_affiliates[0]->cust_aff_shipping_id){ if($all_affiliates[0]->cust_aff_shipping_id==$shipping_method->id){ echo "selected"; } } ?>><?php echo $shipping_method->method_title; ?></option>
                                        <?php
                                } ?>
                                </select>
                            </td> */ ?>
                            <td><input type="text" name="shipping_price" <?php if($all_affiliates[0]->cust_aff_shipping_price){ ?> value="<?php echo $all_affiliates[0]->cust_aff_shipping_price; ?>" <?php } ?> class="test"></td>
                        </tr>
                    </tbody>
                </table>
                <table class="form-table wp-list-table widefat fixed posts affiliatewp-cat-rates">
                    <thead>
                        <tr>
                            <th>Product Category</th>
                            <th>Discount</th>
                            <th>Rate</th>
                            <th>Shipping</th>
                            <th style="width:5%;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        
                        if($all_affiliates[0]->cust_aff_product_cat){
                            $cust_aff_product_cat = unserialize($all_affiliates[0]->cust_aff_product_cat);
                            $j=0;
                            foreach ($cust_aff_product_cat as $key1 => $value1) {
                        ?>
                        <tr>
                            <td class="demo">
                                <select name="product_cat_rates[<?php echo $j; ?>][cat_id]" class="cat_test" placeholder="Select Categories">
                                    <option value="">Select Category</option>
                                    <?php 
                                        if($product_cats):
                                            foreach($product_cats as $product_cat): ?>
                                                <option <?php if($value1['cat_id']==$product_cat->term_id){ echo "selected"; } ?> value="<?php echo $product_cat->term_id; ?>"><?php echo $product_cat->name;?></option>
                                            <?php endforeach;
                                        endif;
                                    ?>
                                </select>
                            </td>
                            <td>
                                <input name="product_cat_rates[<?php echo $j; ?>][discount]" type="text" value="<?php echo $value1['discount']; ?>" class="cat_test" />
                            </td>
                            <td>
                                <select name="product_cat_rates[<?php echo $j; ?>][rate]" class="cat_test"><option <?php if(isset($value1['rate']) && $value1['rate']=="percentage"){ echo "selected"; } ?> value="percentage">Percentage</option><option <?php if(isset($value1['rate']) && $value1['rate']=="flat_rate"){ echo "selected"; } ?> value="flat_rate">Flat Rate</option></select>
                            </td>
                            <td>
                                <input name="product_cat_rates[<?php echo $j; ?>][shipping]" type="text" value="<?php echo $value1['shipping']; ?>" class="cat_test" />
                            </td>
                            <td>
                                <a href="javascript:void(0)" class="custom_aff_cat_remove_rate" style="background: url(<?php echo home_url(); ?>wp-admin/images/xit.gif) no-repeat;">×</a>
                            </td>
                        </tr>
                        <?php $j++;
                            }
                        }else{ ?>
                        <tr>
                            <td class="demo">
                                <select name="product_cat_rates[0][cat_id]" class="cat_test" placeholder="Select Categories">
                                    <option value="">Select Category</option>
                                    <?php 
                                        if($product_cats):
                                            foreach($product_cats as $product_cat): ?>
                                                <option value="<?php echo $product_cat->term_id; ?>"><?php echo $product_cat->name;?></option>
                                            <?php endforeach;
                                        endif;
                                    ?>
                                </select>
                            </td>
                            <td>
                                <input name="product_cat_rates[0][discount]" type="text"  class="cat_test" />
                            </td>
                            <td>
                                <select name="product_cat_rates[0][rate]" class="cat_test"><option value="percentage">Percentage</option><option value="flat_rate">Flat Rate</option></select>
                            </td>
                            <td>
                                <input name="product_cat_rates[0][shipping]" type="text" value="" class="cat_test" />
                            </td>
                            <td>
                                <a href="javascript:void(0)" class="custom_aff_cat_remove_rate" style="background: url(<?php echo home_url(); ?>wp-admin/images/xit.gif) no-repeat;">×</a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="1">
                                <button id="custom_aff_cat_new_rate_woocommerce"  class="button affwp_new_procat_rate">Add New Product Category Rate</button>
                            </th>
                            <th colspan="3">
                                
                            </th>
                        </tr>
                    </tfoot>
                </table>
                <table class="form-table wp-list-table widefat fixed posts affiliatewp-rates">
                    <thead>
                        <tr>
                            <th>Product/s</th>
                            <th>Discount</th>
                            <th>Rate</th>
                            <th>Shipping</th><?php /**/?>
                            <th style="width:5%;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if($all_affiliates[0]->cust_aff_product){
                            $cust_aff_product = unserialize($all_affiliates[0]->cust_aff_product);
                            $j=0;
                            foreach ($cust_aff_product as $key => $value) {
                        ?>
                        <tr>
                            <td class="demo">
                                <select name="product_rates[<?php echo $j; ?>][product_id]" class="prod_test" placeholder="Select Products">
                                    <option value="">Select Product</option>
                                    <?php 
                                        if($products):
                                            foreach($products as $product): ?>
                                                <option <?php if($value['product_id']==$product->ID){ echo "selected"; } ?> value="<?php echo $product->ID; ?>"><?php echo $product->post_title;?> - (<?php echo $product->ID; ?>)</option>
                                            <?php endforeach;
                                        endif;
                                    ?>
                                </select>
                            </td>
                            <td>
                                <input name="product_rates[<?php echo $j; ?>][discount]" type="text" value="<?php echo $value['discount']; ?>" class="prod_test"/>
                            </td>
                            <td>
                                <select name="product_rates[<?php echo $j; ?>][rate]" class="prod_test"><option <?php if(isset($value['rate']) && $value['rate']=="percentage"){ echo "selected"; } ?> value="percentage">Percentage</option><option <?php if(isset($value['rate']) && $value['rate']=="flat_rate"){ echo "selected"; } ?> value="flat_rate">Flat Rate</option></select>
                            </td>
                            <td>
                            <input name="product_rates[<?php echo $j; ?>][shipping]" type="text" value="<?php echo isset($value['shipping'])?$value['shipping']:NULL; ?>" class="prod_test" /> 
                            </td><?php /**/?>
                            <td>
                                <a href="javascript:void(0)" class="custom_aff_remove_rate" style="background: url(<?php echo home_url(); ?>wp-admin/images/xit.gif) no-repeat;">×</a>
                            </td>
                        </tr>
                        <?php $j++;
                            }
                        }else{ ?>
                        <tr>
                            <td class="demo">
                                <select name="product_rates[0][product_id]" class="prod_test" placeholder="Select Products">
                                    <option value="">Select Product</option>
                                    <?php 
                                        if($products):
                                            foreach($products as $product): ?>
                                                <option value="<?php echo $product->ID; ?>"><?php echo $product->post_title;?> - (<?php echo $product->ID; ?>)</option>
                                            <?php endforeach;
                                        endif;
                                    ?>
                                </select>
                            </td>
                            <td>
                                <input name="product_rates[0][discount]" type="text"  class="prod_test" />
                            </td>
                            <td>
                                <select name="product_rates[0][rate]" class="prod_test"><option value="percentage">Percentage</option><option value="flat_rate">Flat Rate</option></select>
                            </td>
                            <td>
                            <input name="product_rates[0][shipping]" type="text" value="" class="prod_test" />
                            </td><?php /**/ ?>
                            <td>
                                <a href="javascript:void(0)" class="custom_aff_remove_rate" style="background: url(<?php echo home_url(); ?>wp-admin/images/xit.gif) no-repeat;">×</a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="1">
                                <button id="custom_aff_new_rate_woocommerce"  class="button affwp_new_rate">Add New Product Rate</button>
                            </th>
                            <th colspan="3">
                                
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <script type="text/javascript">
                jQuery(document).ready(function($) {
                    $('.affwp_new_procat_rate').on('click', function(e) {
                        e.preventDefault();
                        var ClosestRatesTable = $(this).closest('.affiliatewp-cat-rates');

                        // clone the last row of the closest rates table
                        var row = ClosestRatesTable.find( 'tbody tr:last' );

                        // clone it
                        clone = row.clone();

                        // count the number of rows
                        var count = ClosestRatesTable.find( 'tbody tr' ).length;

                        // find and clear all inputs
                        clone.find( 'td input' ).val( '' );

                        // insert our clone after the last row
                        clone.insertAfter( row );

                        // replace the name of each input with the count
                        clone.find( '.cat_test' ).each(function() {
                            var name = $( this ).attr( 'name' );

                            name = name.replace( /\[(\d+)\]/, '[' + parseInt( count ) + ']');

                            $( this ).attr( 'name', name ).attr( 'id', name );
                        });
                    });

                    $("body").on("click",".custom_aff_cat_remove_rate",function(e){ 
                        var ClosestRatesTable = $(this).closest('.affiliatewp-cat-rates');
                        var count = ClosestRatesTable.find( 'tbody tr' ).length;
                        if(count==1){
                            $(this).closest('tr').find( 'td select' ).val( '' );
                            $(this).closest('tr').find( 'td input' ).val( '' );
                        }else{
                            $(this).closest('tr').remove();
                        }
                        e.preventDefault();

                    });
                    $('.affwp_new_rate').on('click', function(e) {
                        e.preventDefault();
                        var ClosestRatesTable = $(this).closest('.affiliatewp-rates');

                        // clone the last row of the closest rates table
                        var row = ClosestRatesTable.find( 'tbody tr:last' );

                        // clone it
                        clone = row.clone();

                        // count the number of rows
                        var count = ClosestRatesTable.find( 'tbody tr' ).length;

                        // find and clear all inputs
                        clone.find( 'td input' ).val( '' );

                        // insert our clone after the last row
                        clone.insertAfter( row );

                        // replace the name of each input with the count
                        clone.find( '.prod_test' ).each(function() {
                            var name = $( this ).attr( 'name' );

                            name = name.replace( /\[(\d+)\]/, '[' + parseInt( count ) + ']');

                            $( this ).attr( 'name', name ).attr( 'id', name );
                        });
                    });

                    $("body").on("click",".custom_aff_remove_rate",function(e){ 
                        var ClosestRatesTable = $(this).closest('.affiliatewp-rates');
                        var count = ClosestRatesTable.find( 'tbody tr' ).length;
                        if(count==1){
                            $(this).closest('tr').find( 'td select' ).val( '' );
                            $(this).closest('tr').find( 'td input' ).val( '' );
                        }else{
                            $(this).closest('tr').remove();
                        }
                        e.preventDefault();

                    });

                });
            </script>

        <?php
            }
             ?>
        <div class="add-btn"><input type="submit" name="update-custom-affiliate" value="Update"></div>
        </form>
        <script type="text/javascript">
            var loadFile = function(event) {
                var output = document.getElementById('preview_image');
                output.src = URL.createObjectURL(event.target.files[0]);
                output.onload = function() {
                  URL.revokeObjectURL(output.src) // free memory
                }
            };
            jQuery(document).ready(function($){
                var affiliate_type = '<?php echo $all_affiliates[0]->affiliate_type; ?>';
                if(affiliate_type){
                    if(affiliate_type=='main'){
                        jQuery('#c_id').hide();
                    }
                    jQuery(".hide").hide();
                    jQuery("."+affiliate_type).show();
                    $('#traffic_source option').show();
                    var affiliate_type = $(this).find('option:selected').val();
                    if(affiliate_type == "Generic Affiliate" || affiliate_type == "Custom Affiliate") {
                        $('#traffic_source').each(function () {
                            $("option:not(:contains('affiliates'))", this).hide();
                        });
                    }else if(affiliate_type == "Sales Funnel") {
                        $('#traffic_source').each(function () {
                            $("option:contains('affiliates'),option:contains('main')", this).hide();
                        });
                    }else if(affiliate_type == "Main") {
                        $('#traffic_source').each(function () {
                            $("option:not(:contains('main'))", this).hide();
                        });
                    }else{
                        $('#traffic_source option').show();
                    }
                }
                $('#affiliate_type').on('change', function () {
                    $('#traffic_source option').show();
                    var affiliate_type = $(this).find('option:selected').val();
                    if(affiliate_type == "Generic Affiliate" || affiliate_type == "Custom Affiliate") {
                        $('#traffic_source').each(function () {
                            $("option:not(:contains('affiliates'))", this).hide();
                        });
                    }else if(affiliate_type == "Sales Funnel") {
                        $('#traffic_source').each(function () {
                            $("option:contains('affiliates'),option:contains('main')", this).hide();
                        });
                    }else if(affiliate_type == "Main") {
                        $('#traffic_source').each(function () {
                            $("option:not(:contains('main'))", this).hide();
                        });
                    }else{
                        $('#traffic_source option').show();
                    }
                });
                $('#custom_slug').on('keyup change paste', function(e){
                     $('#custom_slug_val').html($(this).val())
                });
                $('#affiliate_type').on('change', function(e){
                    var affiliate_type = $(this).val();
                    if(affiliate_type=='main'){
                        jQuery('#c_id').hide();
                    }else{
                        jQuery('#c_id').show();
                    }
                    jQuery(".hide").hide();
                    jQuery("."+affiliate_type).show();
                });
                $('#offer_start_time').datetimepicker({
                    defaultDate : '<?php if($all_affiliates[0]->offer_start_time && $all_affiliates[0]->offer_start_time!="0000-00-00 00:00:00"){ echo date('Y-m-d H:i:s',strtotime($all_affiliates[0]->offer_start_time)); }else{ echo date('Y-m-d H:i:s'); } ?>',
                    format: 'Y-m-d H:i:00',
                    minDate: 0,
                    // maxDate: '-1Y',
                    onShow:function( ct ){
                        this.setOptions({
                            maxDate:jQuery('#offer_end_time').val()!="0000-00-00 00:00:00"?jQuery('#offer_end_time').val():'<?php echo date('Y-m-d H:i:s', strtotime('+5 years'))?>'
                        })
                    },
                });
                $('#offer_end_time').datetimepicker({
                    defaultDate : '<?php if($all_affiliates[0]->offer_end_time && $all_affiliates[0]->offer_end_time!="0000-00-00 00:00:00"){ echo date('Y-m-d H:i:s',strtotime($all_affiliates[0]->offer_end_time)); }else{ echo date('Y-m-d H:i:s'); } ?>',
                    format: 'Y-m-d H:i:00',
                    minDate: 0,
                    onShow:function( ct ){
                        this.setOptions({
                            minDate:jQuery('#offer_start_time').val()?jQuery('#offer_start_time').val():false
                        })
                    },
                });
            });

        </script>
        <script src="<?php echo CustomAffiliate_PLUGIN_URL; ?>public/js/php-date-formatter.min.js"></script>
        <script src="<?php echo CustomAffiliate_PLUGIN_URL; ?>public/js/jquery.mousewheel.js"></script>
        <script src="<?php echo CustomAffiliate_PLUGIN_URL; ?>public/js/jquery.datetimepicker.js"></script>
        <?php
        }
    
    public function customaffiliate_settings(){
        
        if(isset($_POST['update_setting']))
            {
               foreach ($_POST as $key => $value) {
                    if($key == 'update_setting'){

                    }else{
                        update_option($key,$value);   
                    }
                   
               }
            }
        ?>
        <div class="wrap">
            <h1>Settings</h1>
            <div class="settingcontainer">
                <form action="" method="post">
                    <div class="form-group">
                        <label>Days Store Cookie</label>
                        <div class="form-class">
                            <input type="number" name="cookie_days" value="<?php echo get_option('cookie_days'); ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Generic Home Page</label>
                    <?php 
                                $args = array(
                                        'sort_order' => 'asc',
                                        'sort_column' => 'post_title',
                                        'hierarchical' => 1,
                                        'exclude' => '',
                                        'include' => '',
                                        'meta_key' => '',
                                        'meta_value' => '',
                                        'authors' => '',
                                        'child_of' => 0,
                                        'parent' => -1,
                                        'exclude_tree' => '',
                                        'number' => '',
                                        'offset' => 0,
                                        'post_type' => 'page',
                                        'post_status' => 'publish'
                                    ); 
                                $pages = get_pages($args); // get all pages based on supplied args
                            ?><div class="form-class">
                                <select name="generic_page_id" required>
                                    <option value="">-SELECT-</option>
                                    <?php 
                                        foreach($pages as $page)
                                        {
                                    ?>
                                            <option <?php if(get_option('generic_page_id') == $page->ID){ echo 'selected'; } ?> value="<?php echo $page->ID; ?>"><?php echo $page->post_title; ?></option>
                                    <?php 
                                        }
                                    ?>
                                </select>
						</div>
                    </div>
                    <div class="form-group">
						<br><br>
                        <label></label>
                        <div class="form-class">
                            <input class="btn btn-primary" type="submit" name="update_setting" value="Update Settings">
                        </div>
                    </div>
                </form>
                <p><strong>Note :</strong>For checking Product is existing on not.Please use check_product_in_custom_affiliate($product) </p>
            </div>
        </div>
    <?php
    }
    public function customaffiliate_import(){ 
        global $wpdb;
        // Table name
        $insert_arrays =array();
        $tablename = $wpdb->prefix."custom_affiliate";
        // Import CSV
        if(isset($_POST['butimport'])){
            // File extension
            $extension = pathinfo($_FILES['import_file']['name'], PATHINFO_EXTENSION);
            // If file extension is 'csv'
            if(!empty($_FILES['import_file']['name']) && $extension == 'csv'){
                $totalInserted = 0;

                // Open file in read mode
                $csvFile = fopen($_FILES['import_file']['tmp_name'], 'r');

                fgetcsv($csvFile); // Skipping header row
                $i =0;
                //print_r(fgetcsv($csvFile));
                // Read file
                while(($csvData = fgetcsv($csvFile)) !== FALSE){
                        $csvData = array_map("utf8_encode", $csvData);

                        // Row column length
                        $dataLen = count($csvData);

                        // Skip row if length != 4
                        //if( !($dataLen == 10) ) continue;
                            if($_POST['delete_file']){
                                $wpdb->query('TRUNCATE TABLE '.$tablename);
                            }
                            $ca_title = trim($csvData[0]);
                            $custom_slug = trim($csvData[1]);
                            $affiliate = trim($csvData[2]);
                            if(strtolower($affiliate) =='sales'){
                                $affiliate_type ="sales_funnel";
                            }
                            if(strtolower($affiliate) =='custom'){
                                $affiliate_type ="custom_affiliate";
                            }
                            if(strtolower($affiliate) =='generic'){
                                $affiliate_type ="generic_affiliate";
                            }
                            if(is_numeric($csvData[3])){
                                $page_id = $csvData[3]; 
                            }else{
                                $page_id = 0;
                            }
                            if(is_numeric($csvData[4])){
                                $cust_aff_shipping_price = $csvData[4]; 
                            }else{
                                $cust_aff_shipping_price = 0;
                            }
                            $cust_aff_text =trim($csvData[5]);
                            $cust_aff_button_text =trim($csvData[6]);
                            $cust_aff_button_link =trim($csvData[7]);
                            $traffic_source =strtolower(str_replace(' ','_',$csvData[8]));
                            $cust_aff_product_cat[0] = array('cat_id'=> 15,'discount'=>'','rate'=>'percentage');
                            $cust_aff_product_cat[1] = array('cat_id' => 81, 'discount' =>'', 'rate' => 'percentage' );
                            $cust_aff_style =trim($csvData[9]);
                            $insert_arrays[] = array(
                                  'ca_title' =>$ca_title,
                                  'custom_slug' =>$custom_slug,
                                  'affiliate_type' =>$affiliate_type,
                                  'page_id' =>$page_id,
                                  'cust_aff_product'=>'',
                                  'cust_aff_shipping_price' =>$cust_aff_shipping_price,
                                  'cust_aff_product_cat'=>serialize($cust_aff_product_cat),
                                  'cust_aff_text' =>$cust_aff_text,
                                  'cust_aff_button_text' =>$cust_aff_button_text,
                                  'cust_aff_button_link' =>$cust_aff_button_link,
                                  'traffic_source' =>$traffic_source,
                                  'cust_aff_style' => $cust_aff_style
                                  );

                    $i++;
                }
                if($insert_arrays){
                    $this->wp_insert_rows($insert_arrays, $tablename, false, "primary_column");
                }
                echo "<h3 style='color: green;'>Data Insert Successfully</h3>";


            }else{
                echo "<h3 style='color: red;'>Invalid Extension</h3>";
            }

        }
        ?>
        <div class="ca-file-export">
            <div class="form-outer">
                <form method='post' action='<?php echo $_SERVER['REQUEST_URI']; ?>' enctype='multipart/form-data'>
                    <div class="form-wrap">
                        <input type="checkbox" name="delete_file" value="1">Delete Existing Data
                    </div>
                    <div class="form-wrap">
                        <input type="file" name="import_file" accept=".csv">
                    </div>
                    <div class="form-wrap">
                        <input type="submit" name="butimport" value="Import">
                    </div>
                </form>
            </div>
            <div class="ca-sample-file">
                <a href="data:text/csv;charset=utf-8,Title,C=,Type,Page_id,Shipping price,Custom Affiliate Text,Button text,Button link,Traffic source,Sust_aff_styl
                Xyz,xyz,Sales,1,19.95,Testing,Shop,,Paid,
                ABC,abc,Custom,1,19.95,Testing,Shop,,Paid,
                EFG,efg,Generic,1,19.95,Testing,Shop,,Paid," download="sample.csv">
                <button>Download Sample File</button>
                </a>
            </div>
        </div>
    <?php
    }
    public function wp_insert_rows($row_arrays = array(), $wp_table_name, $update = false, $primary_key = null) {
        global $wpdb;
        $wp_table_name = esc_sql($wp_table_name);
        // Setup arrays for Actual Values, and Placeholders
        $values        = array();
        $place_holders = array();
        $query         = "";
        $query_columns = "";
        
        $query .= "INSERT INTO `{$wp_table_name}` (";
        foreach ($row_arrays as $count => $row_array) {
            foreach ($row_array as $key => $value) {
                if ($count == 0) {
                    if ($query_columns) {
                        $query_columns .= ", " . $key . "";
                    } else {
                        $query_columns .= "" . $key . "";
                    }
                }
                
                $values[] = $value;
                
                $symbol = "%s";
                if (is_numeric($value)) {
                    if (is_float($value)) {
                        $symbol = "%f";
                    } else {
                        $symbol = "%d";
                    }
                }
                if (isset($place_holders[$count])) {
                    $place_holders[$count] .= ", '$symbol'";
                } else {
                    $place_holders[$count] = "( '$symbol'";
                }
            }
            // mind closing the GAP
            $place_holders[$count] .= ")";
        }
        
        $query .= " $query_columns ) VALUES ";
        
        $query .= implode(', ', $place_holders);
        
        if ($update) {
            $update = " ON DUPLICATE KEY UPDATE $primary_key=VALUES( $primary_key ),";
            $cnt    = 0;
            foreach ($row_arrays[0] as $key => $value) {
                if ($cnt == 0) {
                    $update .= "$key=VALUES($key)";
                    $cnt = 1;
                } else {
                    $update .= ", $key=VALUES($key)";
                }
            }
            $query .= $update;
        }
        
        $sql = $wpdb->prepare($query, $values);
        if ($wpdb->query($sql)) {
            return true;
        } else {
            return false;
        }
    }
}
}


add_action ( 'admin_init', function(){
    if(isset($_GET['action']) && $_GET['action']=='export_to_excel_affiliate'){
        global $wpdb;
        $all_affiliates = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}custom_affiliate" );
    
        // $field = array();
        // $value = array();
        require_once(get_stylesheet_directory().'/php-export-data.class.php');
        $exporter = new ExportDataExcel('browser', 'AirdoctorPro Affiliates.xls');
        
        $exporter->initialize(); 
        // $exporter->addRow($field); 
        // $exporter->addRow($value);
        // $exporter->addRow(array());
        // $exporter->addRow(array()); 
        $exporter->addRow(array('Title','Custom Slug','Type','Traffic Source','Scriptcode','Promotion Offer Text','Promotion Offer Button Text','Promotion Offer Button Link','Promotion Offer Start Time
        ','Promotion Offer End Time','Funnel Offer Status','Affiliate Name','Affilate Button Text','Affilate Button Link','Status','','Product Categories','','','','Products','',)); 
        $exporter->addRow(array('','','','','','','','','','','','','','','','Category Name','Category Discount','Discount Rate','Category Shipping','Product Name','Product Discount','Discount Rate','Product Shipping','Landing Page'));
        if($all_affiliates){
            foreach ($all_affiliates as $each_affiliate) {   
                $status="";         
                if($each_affiliate->affiliate_type =='main'){
                    $status=$each_affiliate->status;
                }
                $page_title="";
                if(!empty($each_affiliate->page_id)):
                    $page_title=get_the_title($each_affiliate->page_id);
                endif;
                
                $exporter->addRow(array($each_affiliate->ca_title,$each_affiliate->custom_slug,ucwords(str_replace("_"," ",$each_affiliate->affiliate_type)),ucwords(str_replace("_"," ",$each_affiliate->traffic_source)),$each_affiliate->scriptcode,$each_affiliate->offer_text,$each_affiliate->offer_button_text,$each_affiliate->offer_button_link,$each_affiliate->offer_start_time,$each_affiliate->offer_end_time,ucwords($each_affiliate->affiliate_offering_status),ucwords($each_affiliate->cust_aff_text),ucwords($each_affiliate->cust_aff_button_text),ucwords($each_affiliate->cust_aff_button_link),ucwords($status),'','','','','','','','',$page_title));
                $cust_aff_product_cat=unserialize($each_affiliate->cust_aff_product_cat);
                $cust_aff_product=unserialize($each_affiliate->cust_aff_product);
                if(!empty($cust_aff_product_cat) || !empty($cust_aff_product)):
                    $newarr['cat_array']=$cust_aff_product_cat;
                    $newarr['prd_array']=$cust_aff_product;
                    if(count($newarr['cat_array'])>=count($newarr['prd_array'])):
                        $t=0;
                        foreach($newarr['cat_array'] as $cat_prd_array):
                            $catname=isset($cat_prd_array['cat_id'])?get_term( $cat_prd_array['cat_id'] )->name:NULL;
                            $catdiscount=isset($cat_prd_array['discount'])?$cat_prd_array['discount']:NULL;
                            $catrate=isset($cat_prd_array['rate'])?ucwords(str_replace("_"," ",$cat_prd_array['rate'])):NULL;
                            $catshipping=isset($cat_prd_array['shipping'])?$cat_prd_array['shipping']:NULL;

                            $prdname=isset($newarr['prd_array'][$t]['product_id'])?wc_get_product($newarr['prd_array'][$t]['product_id'])->get_name():NULL;
                            $prddisc=isset($newarr['prd_array'][$t]['discount'])? $newarr['prd_array'][$t]['discount']:NULL;
                            $prdrate=isset($newarr['prd_array'][$t]['rate'])? ucwords(str_replace("_"," ",$newarr['prd_array'][$t]['rate'])):NULL;
                            $prdship=isset($newarr['prd_array'][$t]['shipping']) && !empty($newarr['prd_array'][$t]['shipping'])? $newarr['prd_array'][$t]['shipping'] :NULL;

                            $exporter->addRow(array($each_affiliate->ca_title,$each_affiliate->custom_slug,'','','','','','','','','','','','','',$catname,$catdiscount,$catrate,$catshipping,$prdname,$prddisc,$prdrate,$prdship,'')); 

                        $t++;
                        endforeach;
                    else:
                        $p=0;
                        foreach($newarr['prd_array'] as $cat_prd_array):
                            $catname=isset($newarr['cat_array'][$p]['cat_id'])?get_term( $newarr['cat_array'][$p]['cat_id'] )->name:NULL;
                            $catdiscount=isset($newarr['cat_array'][$p]['discount'])?$newarr['cat_array'][$p]['discount']:NULL;
                            $catrate=isset($newarr['cat_array'][$p]['rate'])?ucwords(str_replace("_"," ",$newarr['cat_array'][$p]['rate'])):NULL;
                            $catshipping=isset($newarr['cat_array'][$p]['shipping'])?$newarr['cat_array'][$p]['shipping']:NULL;

                            $prdname=isset($cat_prd_array['product_id'])?wc_get_product($cat_prd_array['product_id'])->get_name():NULL;
                            $prddisc=isset($cat_prd_array['discount'])? $cat_prd_array['discount']:NULL;
                            $prdrate=isset($cat_prd_array['rate'])? ucwords(str_replace("_"," ",$cat_prd_array['rate'])):NULL;
                            $prdship=isset($cat_prd_array['shipping']) && !empty($cat_prd_array['shipping'])? $cat_prd_array['shipping'] :NULL;

                            $exporter->addRow(array($each_affiliate->ca_title,$each_affiliate->custom_slug,'','','','','','','','','','','','','',$catname,$catdiscount,$catrate,$catshipping,$prdname,$prddisc,$prdrate,$prdship,'')); 

                        $p++;
                        endforeach;

                    endif;
                    // echo "<pre>";
                    // print_r($newarr);
                    // echo "</pre>";

                    // exit;
                    $exporter->addRow([]);
                endif;
            }
        }
        $exporter->finalize(); 

        exit();
    }
});