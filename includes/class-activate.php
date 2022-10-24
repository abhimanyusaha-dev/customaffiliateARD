<?php
if ( ! class_exists( 'CustomAffiliate_Activate' ) ) {
	class CustomAffiliate_Activate {
        /**
         * Constructor
         */

        public function __construct() {  
            $this->activate_plugin();
        }
		public  function activate_plugin() {
			global $wpdb;		   
            $plugin_name_db_version = '1.0';
            $table_name = $wpdb->prefix . "custom_affiliate";

            $charset_collate = $wpdb->get_charset_collate();
            
            $sql = "CREATE TABLE IF NOT EXISTS $table_name (
                ca_id bigint(50) NOT NULL AUTO_INCREMENT,
                ca_title varchar(256) NOT NULL,
                custom_slug varchar(256) NOT NULL UNIQUE,
                affiliate_type varchar(50) NULL,
                status varchar(200) NOT NULL DEFAULT 'Inactive',
                page_id bigint(50) NULL,
                scriptcode varchar(500) NULL,
                offer_text VARCHAR(500) NULL,
                offer_button_text VARCHAR(255) NULL, 
                offer_button_link VARCHAR(255) NULL,
                offer_start_time DATETIME NULL, 
                offer_end_time DATETIME NULL,
                affiliate_offering_status ENUM('normal','offer') NOT NULL DEFAULT 'normal',
                cust_aff_product Text NULL,
                cust_aff_shipping_price float(10,2) NULL,
                cust_aff_product_cat Text NULL,
                cust_aff_image varchar(256) NULL,
                cust_aff_text varchar(500) NULL,
                cust_aff_button_text varchar(256) NULL,
                cust_aff_button_link varchar(256) NULL,
                traffic_source varchar(256) NULL,
                cust_aff_style Text NULL,
                land_page_url Text NULL,
                show_home ENUM(0,1) NOT NULL DEFAULT 1,
                only_default_var ENUM(0,1) NOT NULL DEFAULT 0,
                cust_aff_product_bundles Text NOT NULL,
                PRIMARY KEY (ca_id)
            ) $charset_collate;";

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $sql );

            add_option( 'plugin_name_db_version', $plugin_name_db_version );
            if(get_option('cookie_days',true)){
                update_option('cookie_days',10);
            }
            if(get_option('generic_page_id',true)){
                $front_page_id = get_option('page_on_front');
                update_option('generic_page_id',$front_page_id);
            }
            if(get_option('affiliate_current_site',true))
            {
                if(!str_contains(get_option('affiliate_current_site'),"_"))
                {                
                add_option('affiliate_current_site','AQT_LIVE');
                }
            }
            else            
            {
                add_option('affiliate_current_site','AQT_LIVE');
            }
            
            
        }
		
		
	}
}
?>