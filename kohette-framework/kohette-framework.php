<?php
/*
Name: Kohette WDT Framework
URI: https://github.com/Kohette/Kohette-WordPress-Dev-Tools/
Description: Load the theme configuration and custom functions & features.
Author: Rafael Martín
Author URI: http://kohette.com/
Version: 1.6.2
*/



/**
* We define the global one that will have an array with all the general options of the site related to the themes
* @package Kohette Framework
*/
global $KTT_theme_options;


/**
* Class that manages the Framework.
*
* This class initializes all the processes required to implement kohette in the theme.
*
* @package Kohette Framework
*
* @param array $theme_config Array containing the initial variables for the framework such as textdomain, etc.
*/
class kohette_framework {

    private $theme_config;

    /**
    * Class constructor
    */
    public function __construct($theme_config = '') {

            $this->set_fw_constants();
            $this->set_theme_config($theme_config);
            $this->load_framework_functions(); // load custom functions

            $this->set_theme_options_global();
          	$this->load_framework_modules(); // load framework handy classes
            $this->load_framework_hooks(); // load custom functions
            $this->create_theme_options_page();
            $this->load_plugins();


    }

    /**
    * Class contructor
    */
    public function kohette_framework($theme_config) {
            self::__construct();
    }

    /**
    * This function is responsible for saving a global array of general options related to the theme.
    *
    * @global array $KTT_theme_options Array which contains the initial class information.
    * @global object $wpdb.
    */
    private function set_theme_options_global() {

          /**
          * We invoke the variable wpdb
          */
          global $wpdb, $KTT_theme_options;

          /**
          * In result we are going to form the Final array
          */
          $result = new stdClass();;

          /**
          * We execute a query that will delete all the saved theme options
          */
          $options = $wpdb->get_results( "SELECT option_name, option_value FROM $wpdb->options WHERE option_name LIKE '" . KTT_var_name() . "%%'" );

          /**
          * If we have not found options, we leave here
          */
          if (!$options) return;

          /**
          * We route for each result and we add it to the result
          */
          foreach ($options as $key => $value) if ($key) $result->{KTT_remove_prefix($value->option_name)} = maybe_unserialize($value->option_value);

          /**
          * We keep in the global
          */
          $KTT_theme_options = $result;

    }




    /**
    * set the default constants of the framework
    */
    private function set_fw_constants() {

        /**
        * this defines the path of the resources of the framework
        */
        define("KTT_FW_RESOURCES", get_parent_theme_file_path('kohette-framework/resources/'));

    }




    /**
    * set the basic configuration of the theme
    */
    private function set_theme_config($theme_config = '') {

        /**
        * We add the default theme config
        */
        $theme_config = wp_parse_args($theme_config, $this->load_theme_data_constants());

        /**
        * Before creating the framework instance with the configuration array we apply a filter to add information to the configuration that could be added by other functions. This is useful for each theme to add its own configuration of the framework.
        */
        $theme_config = apply_filters( 'KTT_theme_config', $theme_config );

        /**
        * If we do not have constants we leave here
        */
        if (!$theme_config) return;


        $this->theme_config = $theme_config;


        /**
        * We create the defined constants for the theme
        */
        if (isset($theme_config['constants'])) {
        foreach($theme_config['constants'] as $item => $value) {

            $this->$item = $theme_config['constants'][$item];
            define("THEME_" . strtoupper($item) , $this->$item);

        }
        }

    }



    /**
    * load framework custom functions
    */
    private function load_framework_functions() {
		include('functions/basic-functions.php');
    }



    /**
    * load framework handy classes
    */
    private function load_framework_modules() {

    	foreach (glob( get_parent_theme_file_path("kohette-framework/modules/*"), GLOB_ONLYDIR) as $filename) {
        	include('modules/' . basename($filename) . '/' . basename($filename) . '.php') ;
		  };

    }


    /**
    * load framework hooks to improve WordPress
    */
    private function load_framework_hooks() {

        foreach (glob(get_parent_theme_file_path("kohette-framework/hooks/*"), GLOB_ONLYDIR) as $filename) {
            include('hooks/' . basename($filename) . '/' . basename($filename) . '.php') ;
        };

    }


    /**
    * create the theme options admin page/menu
    */
    public function create_theme_options_page() {

        $args = array();
        $args['id']             = 'theme-options';
        $args['page_title']     = 'Theme Options';
        $args['menu_title']     = 'Theme options';
        $args['page']           = ''; //array( &$this, 'default_theme_options_page');

        $new_admin_page = new KTT_admin_menu($args);

    }

    function default_theme_options_page() {
        global $submenu;

    }



    /**
    * Start trigger
    */
    function start_kohette_framework() {

        global $pagenow;
        if ( is_admin() && isset($_GET['activated'] ) && $pagenow == "themes.php" ) {

            set_default_options();

        }
    }



    /**
    * include the plugin file in the theme
    */
    private function run_activate_plugin( $plugin_source ) {
	    include($plugin_source);
	  }



	  /**
    * load the list of plugins
    */
    function load_plugins($plugins = array()) {

      	require_once(ABSPATH . 'wp-admin/includes/plugin.php');

        /**
        * Before finally loading the array of plugins, we apply a filter to check if other functions of the theme want to add files to include. This is useful for each theme to add its files (post_types, scripts, etc)
        */
        $plugins = apply_filters( 'KTT_theme_plugins', $plugins);

        /**
        * If there are no plugins we leave here
        */
        if (!$plugins) return;

      	foreach ($plugins as $plugin => $plugin_config) {

      		$plugin_data = get_plugin_data($plugin_config['source']);

      		$this->run_activate_plugin($plugin_config['source']);

      	}

    }

    /**
    * load theme data through style.css
    */
    function load_theme_data_constants() {

        /**
        * We will return this array with the theme information
        */
        $result = array();

        /**
        * We get the theme data
        */
        $theme_data = wp_get_theme();

        /**
        * Create the array data
        */
        $result['constants']['textdomain'] = $theme_data->get("TextDomain");
        $result['constants']['prefix'] = $result['constants']['textdomain'] . '_';

        /**
        * this define a constant for every folder of the theme directory
        * if the folder is named "the libs" the constant with the path will  defined as THEME_THE_LIBS_PATH
        */
        foreach (glob(get_stylesheet_directory() . "/*", GLOB_ONLYDIR) as $f) {

            $name = basename($f);
            $name = str_replace(' ', '_', $name);
            $name = str_replace('-', '_', $name);

            $result['constants'][strtoupper($name) . '_PATH'] = $f;

        };

        /**
        * We return the Array
        */
        return $result;


    }




}
