<?php
/**
 * Plugin Name: Custom Affiliate Plugin
 * Version: 1.0.6
 * Description: This is a CustomAffiliate list plugin, With Per Product Shipping,Product Bundle Offer for Affiliates !
 * Author: Idealliving
 * Author URI: http://idealliving.com/
 */

if ( ! class_exists( 'CustomAffiliatePlugin' ) ) {
    define( 'CustomAffiliate_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
    define('CustomAffiliate_PLUGIN_URL',plugin_dir_url(__FILE__));
    class CustomAffiliatePlugin {
        /**
         * Constructor
         */

        public function __construct() {  
            $this->setup_actions();
            $this->init_hooks();

            $admin = new CustomAffiliate_Admin();
        }
        
        /**
         * Setting up Hooks
         */
        public function setup_actions() {
            //Main plugin hooks
            register_activation_hook( __FILE__, array( 'CustomAffiliatePlugin', 'activate' ) );
            register_deactivation_hook( __FILE__, array( 'CustomAffiliatePlugin', 'deactivate' ) );
        }
        
        /**
         * Activate callback
         */
        public static function activate(){
            require_once(CustomAffiliate_PLUGIN_PATH. 'includes/class-activate.php');
            $activate = new CustomAffiliate_Activate();
        }
        
        /**
         * Deactivate callback
         */
        public static function deactivate() {
        }
        public function init_hooks() {
            require_once(CustomAffiliate_PLUGIN_PATH. 'functions/functions.php');
            require_once(CustomAffiliate_PLUGIN_PATH. 'admin/class_admin.php');
        }

    }
    $wp_plugin_template = new CustomAffiliatePlugin();
    
}