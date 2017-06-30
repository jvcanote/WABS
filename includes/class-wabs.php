<?php 
/**
 * @category     WordPress_Plugin
 * @package      WABS
 * @author       WEBDOGS
 * @license      GPL-3.0+
 * @link         HTTPS://WEBDOGS.COM
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * **********************************************************************
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Woof Action Bar Scheduler
 *
 * @author Jacob Vega Canote <jacob@webdogs.com>
 */
class WABS 
{
    // woof_action_bar_scheduler
    const PLUGIN = "woof-action-bar-scheduler";
    const  TOKEN = "wabs_";
    const   META = "_wabs_";
    const    KEY = "WABS_";

    const SHORTCODE = '<div id=\'%1$s%2$s\' class=\'%1$sbar %1$s%3$s %1$s%9$s\' style=\'\' data-start-date=\'%10$d000\' data-end-date=\'%11$d000\' data-scheduled=\'%12$s\' data-background-color=\'%13$s\'> <div class=\'%1$scontainer\'> <div class=\'%1$scol-0\'>&nbsp;</div> <div class=\'%1$sinner %1$scol-1\'> <div class=\'%1$smessage\' data-text-color=\'%14$s\'> %4$s </div> <div class=\'%1$scta\'> <a class=\'%1$sbutton\' href=\'%5$s\' target=\'%6$s\'>%7$s</a> </div> </div> <div class=\'%1$scol-2\'> <a href=\'javascript:void(0);\' id=\'%1$sclose_bar_%2$s\' class=\'%1$sclose_bar\'> %8$s </a> </div> </div> </div>';
    
    const LOCALIZED = '<div id=\'%1$s%2$s\' class=\'%1$sbar %1$s%3$s %1$s%9$s\' style=\'position:absolute;transform:translate(0px,-100%%);\' data-start-date=\'%10$d000\' data-end-date=\'%11$d000\' data-scheduled=\'%12$s\' data-background-color=\'%13$s\'> <div class=\'%1$scontainer\'> <div class=\'%1$scol-0\'>&nbsp;</div> <div class=\'%1$sinner %1$scol-1\'> <div class=\'%1$smessage\' data-text-color=\'%14$s\'> %4$s </div> <div class=\'%1$scta\'> <a class=\'%1$sbutton\' href=\'%5$s\' target=\'%6$s\'>%7$s</a> </div> </div> <div class=\'%1$scol-2\'> <a href=\'javascript:void(0);\' id=\'%1$sclose_bar_%2$s\' class=\'%1$sclose_bar\'> %8$s </a> </div> </div> </div>';
    
    const CSSINLINE = '.%1$sbutton {color: %4$s; background-color: %2$s; } .%1$sbutton:hover, .%1$scontainer, .%1$smessage {color: %2$s; background-color: %4$s; } .%1$sclose_bar .cls-1 {color: %3$s; } .%1$sclose_bar:hover .cls-1 {opacity: .4; color: %3$s; }';


    /**
     * The single instance of WABS.
     * @var     object
     * @access  private
     * @since   1.0.0
     */
    private static $_instance = null;

    /**
     * Settings class object
     * @var     object
     * @access  public
     * @since   1.0.0
     */
    public static $settings = null;

    /**
     * The version number.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public static $version;

    /**
     * The main plugin file.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public static $file;

    /**
     * Server path to the plugin folder
     *
     * @var string
     */
    public static $plugin_dir;
    
    /**
     * URL to the plugin folder
     *
     * @var string
     */
    public static $plugin_url;

    /**
     * URL to the plugin folder
     *
     * @var string
     */
    protected static $id = "";
    protected static $css = "";
    protected static $html = "";
    protected static $active = "";
    protected static $global = "";
    protected static $message = "";
    protected static $unique_id = "";
    protected static $shortcode = "";
    protected static $scheduled = "";
    protected static $start_date = "";
    protected static $end_date = "";
    protected static $classes = "";
    protected static $link = "";
    protected static $target = "";
    protected static $button_text = "";
    protected static $button_class = "";
    protected static $button_color= "";
    protected static $action_symbol_xml = "";
    protected static $action_symbol = "";
    protected static $text_color = "";
    protected static $action_color = "";
    protected static $background_color = "";
    protected static $header_selector = "";
    protected static $top_spacer = "";
    protected static $options = array();

    private static $_active   = null;
    private static $_manual   = null;

    private static $_scripts  = array(  'jquery', 
                                        'jquery-ui-core', 
                                        'jquery-effects-core',
                                        'jquery-touch-punch', 
                                        'hoverIntent', 
                                        'html5shiv' );

    private static $_defaults = array(  'behavior'      => 'toggle',
                                        'zIndex'        => 100,
                                        'speedIn'       => 600,
                                        'speedOut'      => 400,
                                        'daysHidden'    => 15, 
                                        'daysReminder'  => 90,
                                        'debug'         => false );

    /**
     * Constructor function.
     * @access  private
     * @since   1.0.0
     * @return  void
     */
    private function __construct ( $file = '', $version = '1.0.0' ) {

        // Load plugin environment variables
        SELF::$file    = $file;
        SELF::$version = $version;
        SELF::$plugin_dir = dirname( SELF::$file );
        SELF::$plugin_url = plugins_url( basename( dirname( SELF::$file ) ) );

        register_activation_hook( SELF::$file, array( $this, 'install' ) );

        // Setup action bar
        // todo: check the cookie and bail early
        add_action( 'wp', array( $this, 'setup_action_bar' ), 10 );

        // Load frontend JS & CSS
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ),  10 );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );

        // Shortcode support
        add_shortcode( 'WABS', array( $this, 'do_shortcode') );
        
        // Load admin JS & CSS
        // add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 10, 1 );
        // add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ), 10, 1 );

        // Handle localisation
        $this->load_plugin_textdomain();
        add_action( 'init', array( $this, 'load_localisation' ), 0 );

    }

    public function do_shortcode( $atts ) {
        $atts['shortcode'] = 'y';
        // SELF::$_manual = true;
        if ( $this->_setup_action_bar( $atts ) )
            return SELF::$html;
    }

    /**
     * Setup Object
     */
    private static function _setup_action_bar( $atts = array() ) { 

        $defaults = array(
            'unique_id'         =>  uniqid(),
            'active'            =>  '0',
            'global'            =>  '0',
            'shortcode'         =>  '0',
            'scheduled'         =>  '0',
            'start_date'        =>  '0',
            'classes'           =>  'wabs_bar wabs_without_cta',
            'target'            =>  '_self',
            'link'              =>  '',
            'message'           =>  '',
            'button_text'       =>  '',
            'header_selector'   =>  '',
            'action_symbol_xml' =>  '',
            'action_symbol'     =>  'delta',
            'text_color'        =>  '#FFFFFF',
            'action_color'      =>  '#FFFFFF',
            'background_color'  =>  '#753BBD' 
        );
        
        // Args Array
        $WABS = array();

        // HTML String
        $output = "";

        // Class selectors
        $classes = array( SELF::TOKEN.'bar' );

        // Post Number
        $post_id = SELF::_get_post_id();

        /**
         * 
         * Setup WABS 
         * configuration args
         *
         */
        foreach ( $defaults as $key => $value)
            $WABS[$key] = SELF::_get_meta_value( $post_id, SELF::META.$key );

        // merge defaults
        $WABS = wp_parse_args( $WABS, $defaults );

        /**
         * 
         * Setup Shortcode
         * verify shortcode, add classes
         *
         */
        $do_shortcode = SELF::_is_true( apply_filters( SELF::TOKEN.'do_shortcode', $atts['shortcode'] ) ); 

        if ( $do_shortcode ) {

            // verify shortcode 
            if ( SELF::_is_true( $atts['shortcode'] ) ) {

                // unset valid ID or bail
                if ( ! empty( $atts['id'] ) && $atts['id'] == $WABS['unique_id'] )
                    unset( $atts['id'] );
                else
                    return false;

                // merge shortcode atts
                $WABS = shortcode_atts( $WABS, $atts );

                // add CTA classes
                array_push( $classes, 'shortcode', 'is_manual', 'without_action' );
            }
        }

        /////////////////////////
        // WABS Setup Complete //
        /////////////////////////

        /**
         * 
         * Setup general
         *
         * veritfy configuration, 
         * add selector classes
         *
         */ 

        // bail if deactivated
        if ( ! SELF::_is_true( $WABS['active'] ) )
            return false;

        /**
         * 
         * Action Symbol
         * add class
         *
         */ 
        if ( ! $do_shortcode ) {
            $WABS['action_symbol_xml'] = SELF::_action_symbol_js( $WABS['action_symbol'] );
            array_push( $classes, sanitize_html_class( $WABS['action_symbol'] ) );
        }
    
        /**
         * 
         * Schedule 
         * add classes
         *
         */
        $scheduled = SELF::_is_true( $WABS['scheduled'] );

        if ( $scheduled )
            array_push( $classes, 'scheduled' );

        /**
         * 
         * Set CTA
         *
         * verify values, add classes
         * flatten array to string
         *
         */
        $has_cta = ( boolval( trim( $WABS['link'] ) ) && boolval( trim( $WABS['button_text'] ) ) );

        array_push( $classes, ( $has_cta ? 'with_cta' : 'without_cta' ) );
        
        /**
         * 
         * Set Classes
         * flatten array to string
         *
         */
        $WABS['classes'] = implode( ' ' . SELF::TOKEN, $classes );

        /**
         * 
         * Set Text Colors
         * verify brightness, set colors
         *
         */
        $brightness = SELF::_get_brightness( $WABS['background_color'] );

        $WABS['action_color'] = ( $brightness > 130 ? '#000000' : '#FFFFFF' ); 
        $WABS['text_color']   = ( $WABS['text_color'] ? $WABS['text_color'] : $WABS['action_color'] ); 

        /**
         * 
         * Set Link Target
         * set value
         *
         */
        $WABS['target'] = ( SELF::_is_true( $WABS['target'] ) ? '_blank' : '_self' );

        /**
         * 
         * Set Unique ID
         * set value
         *
         */
        $unique_id = sprintf( "%s_post_%d", esc_attr( $WABS['unique_id'] ), $post_id );

        /**
         * 
         * Setup HTML
         * sinitize data, format output 
         *
         */

        $html = ( SELF::_is_true( $WABS['shortcode'] ) ? SELF::SHORTCODE : SELF::LOCALIZED );

        $vfset = array( 

             1 => esc_attr( SELF::TOKEN ), 

             2 => esc_attr( $unique_id ), 

             3 => esc_attr( $WABS['classes'] ), 

             4 => SELF::_sanitize_js( $WABS['message'] ), 

             5 => esc_url( $WABS['link'] ), 

             6 => esc_attr( $WABS['target'] ), 

             7 => SELF::_sanitize_js( $WABS['button_text'] ), 

             8 => SELF::_action_symbol_js( $WABS['action_symbol'] ), 

             9 => sanitize_html_class( $WABS['action_symbol'], "none" ),

            10 => esc_attr( $WABS['start_date'] ),
            
            11 => esc_attr( $WABS['end_date'] ),

            12 => esc_attr( $WABS['scheduled'] ),

            13 => esc_attr( $WABS['background_color'] ),

            14 => esc_attr( $WABS['text_color'] ),

            15 => esc_attr( $WABS['action_color'] ),

        );

        // apply values to format string
        $output = vsprintf( $html, $vfset );

        /**
         * 
         * Update Properties
         *
         */
        foreach ( $WABS as $property => $value )
            SELF::${"{$property}"} = $value;

        SELF::$id = sprintf( "#%s%s", esc_attr( SELF::TOKEN ), esc_attr( $unique_id ) );

        SELF::$html = $output;

        // SELF::$action_symbol_xml = SELF::_action_symbol_js();
        // SELF::$action_symbol = SELF::_action_symbol();
        SELF::$top_spacer    = SELF::_top_spacer( esc_attr( $post_id ) );
        
        SELF::$options       = SELF::_js_options();
        SELF::$options['behavior'] = ( ( is_numeric( stripos( $WABS['action_symbol'], 'vector' ) ) ) ? 'close' : 'toggle' );

        return true;
    }

    private static function _get_post_id() {
        global $post;
        $blog_id   = get_option( 'page_for_posts' );
        $global_id = get_option( SELF::TOKEN.'global_id' );

        if( $global_id && '1' !== SELF::_get_meta_value( absint( $global_id ), SELF::META.'global' ) )
            delete_option( SELF::TOKEN.'global_id' );
        elseif( $global_id && '1' === SELF::_get_meta_value( absint( $global_id ), SELF::META.'global' ) )
            $post_id = absint( $global_id );
        elseif( ! is_front_page() && is_home() ) 
            $post_id = absint( $blog_id ); 
        else
            $post_id = $post->ID;

        return $post_id;
    }
    private static function _get_meta_value( $id = 0, $key='' ){
        // the fast way
        global $wpdb;
        return maybe_unserialize( 
            $wpdb->get_var(
                $wpdb->prepare(
                   "SELECT meta_value 
                    FROM {$wpdb->postmeta} 
                    WHERE {$wpdb->postmeta}.meta_key = '%s' 
                    AND {$wpdb->postmeta}.post_id = %d", $key, $id)
                )
            );
    }
    private static function _get_brightness($hex) {
        $hex = str_replace('#', '', $hex);
        $R   = hexdec(substr($hex, 0, 2));
        $G   = hexdec(substr($hex, 2, 2));
        $B   = hexdec(substr($hex, 4, 2));
        return (($R * 299) + ($G * 587) + ($B * 114)) / 1000;
    }
    private static function _js_options( $defaults = NULL ) {
        return _wabs_js_options(( $defaults ? $defaults : SELF::$_defaults ));
    }
    private static function _action_symbol( $type = null ) {
        return _wabs_action_symbol(( $type ? $type : SELF::$action_symbol ));
    }
    private static function _top_spacer( $unique_id ) {
        return _wabs_top_spacer( $unique_id );
    }
    private static function _action_symbol_js( $type = null ) {
        return _wabs_action_symbol(( $type ? $type : SELF::$action_symbol ));
    }
    private static function _sanitize_js( $arg = null ) {
        return $arg;
    }
    private static function _check_in_range($start_date,$end_date,$date_now) {
      return (($date_now >= $start_date) && ($date_now <= $end_date));
    }
    private static function _is_true($val, $return_null=false){
        $boolval = ( is_string($val) ? filter_var( preg_replace_callback( '|(^y$)?(^n$)?|i', function ($matches) { return isset($matches[1]) ? ( 'y' == strtolower( $matches[1] ) ? 'yes' : 'no' ) : false ; }, $val, -1 ), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE ) : boolval( $val ) );
        return ( $boolval===null && !$return_null ? boolval( $val ) : $boolval );
    }


    /* PUBLIC */

    public function save_global_id( $values, $post_id ){

        // bail if not post edit screen
        if ( ! $post_id )
            return;

        // clear previous
        delete_post_meta( $post_id, SELF::META.'global' );

        if ( (  is_array( $values ) && in_array('1',    $values ) ) 
        ||   ( is_string( $values ) &&          '1' === $values ) ) {

            // global option
            $global_id = get_option( SELF::TOKEN.'global_id' );
            
            // clear previous
            if ( $global_id ) 
                delete_post_meta( $global_id, SELF::META.'global' );

            // set new global
            add_post_meta( $post_id, SELF::META.'global', '1' );
            update_option( SELF::TOKEN.'global_id', $post_id );
        }
        return;
    }

    public function setup_action_bar(){        
        // todo: check the cookie

        // Plugins to filter to disable 'wabs_active_action_bar'
        SELF::$_active = apply_filters( SELF::TOKEN.'active_action_bar', ( SELF::$_active ?: SELF::_setup_action_bar() ));

        // Plugins to filter to disable 'wabs_manual_action_bar'
        SELF::$_manual = apply_filters( SELF::TOKEN.'manual_action_bar', ( SELF::$_manual ?: SELF::$shortcode ));
    }

    /**
     * Enqeue CSS with custom color values
     *
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function enqueue_styles(){

        // should we filter here?
        if ( SELF::$_active ) {

            wp_register_style( SELF::PLUGIN . '-frontend', esc_url( SELF::$plugin_url ) . '/css/' . SELF::PLUGIN . '.css', array(), SELF::$version );
            wp_enqueue_style(  SELF::PLUGIN . '-frontend' );

            // Custom color CSS

            $vfset = array(

                1 => esc_attr( SELF::TOKEN ),

                2 => esc_attr( SELF::$text_color ),

                3 => esc_attr( SELF::$action_color ),

                4 => esc_attr( SELF::$background_color ),

            );

            $color_css = vsprintf( SELF::CSSINLINE, $vfset );

            wp_add_inline_style( SELF::PLUGIN . '-frontend', $color_css );
        }
    }

    public static function enqueue_scripts(){

        // should we filter here?
        // if ( SELF::$_active ) {
        wp_register_script( 'jquery-transit',    esc_url( SELF::$plugin_url ) . '/js/jquery.transit.min.js',   array( 'jquery', 'jquery-effects-core' ), SELF::$version, true );
        wp_register_script( SELF::PLUGIN . '-frontend', esc_url( SELF::$plugin_url ) . '/js/jquery.' . SELF::PLUGIN . '.js',  array( 'jquery-transit' ), SELF::$version, true );
        foreach ( SELF::$_scripts as $script )
            wp_enqueue_script( $script );

        wp_enqueue_script( 'jquery-transit' );
        wp_enqueue_script(  SELF::PLUGIN . '-frontend' );
        wp_localize_script( SELF::PLUGIN . '-frontend', SELF::KEY . 'setting', 

            apply_filters( SELF::KEY . 'frontend_settings', 

                array( 
                    /*SELF::KEY . */'ID'                => SELF::$id,  
                    /*SELF::KEY . */'HTML'              => SELF::$html, 
                    /*SELF::KEY . */'active'            => SELF::$active,  
                    /*SELF::KEY . */'message'           => SELF::$message,
                    /*SELF::KEY . */'uniqueID'          => SELF::$unique_id, 
                    /*SELF::KEY . */'scheduled'         => SELF::$scheduled, 
                    /*SELF::KEY . */'startDate'         => SELF::$start_date,
                    /*SELF::KEY . */'endDate'           => SELF::$end_date, 
                    /*SELF::KEY . */'classes'           => SELF::$classes, 
                    /*SELF::KEY . */'link'              => SELF::$link, 
                    /*SELF::KEY . */'target'            => SELF::$target, 
                    /*SELF::KEY . */'buttonText'        => SELF::$button_text, 
                    /*SELF::KEY . */'actionSymbol'      => SELF::$action_symbol, 
                    /*SELF::KEY . */'actionSymbolXML'   => SELF::$action_symbol_xml, 
                    /*SELF::KEY . */'textColor'         => SELF::$text_color,
                    /*SELF::KEY . */'actionColor'       => SELF::$action_color,  
                    /*SELF::KEY . */'backgroundColor'   => SELF::$background_color, 
                    /*SELF::KEY . */'headerSelector'    => SELF::$header_selector, 
                    /*SELF::KEY . */'topSpacer'         => SELF::$top_spacer, 
                    /*SELF::KEY . */'options'           => SELF::$options,
                ),

                get_the_id() 
            )
        );
    }

    /**
     * Load admin CSS.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function admin_enqueue_styles ( $hook = '' ) {
        wp_register_style( SELF::PLUGIN . '-admin', esc_url( SELF::$plugin_url ) . '/css/' . SELF::PLUGIN . '-admin.css', array(), SELF::$version );
        wp_enqueue_style(  SELF::PLUGIN . '-admin' );
    } // End admin_enqueue_styles ()

    /**
     * Load admin Javascript.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function admin_enqueue_scripts ( $hook = '' ) {
        wp_register_script( SELF::PLUGIN . '-admin', esc_url( SELF::$plugin_url ) . '/js/' . SELF::PLUGIN . '-admin.js', array( 'jquery' ), SELF::$version );
        wp_enqueue_script(  SELF::PLUGIN . '-admin' );
    } // End admin_enqueue_scripts ()

    /**
     * Load plugin localisation
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function load_localisation () {
        load_plugin_textdomain( SELF::PLUGIN, false, dirname( plugin_basename( SELF::$file ) ) . '/lang/' );
    } // End load_localisation ()

    /**
     * Load plugin textdomain
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function load_plugin_textdomain () {
        $domain = SELF::PLUGIN;

        $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

        load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
        load_plugin_textdomain( $domain, false, dirname( plugin_basename( SELF::$file ) ) . '/lang/' );
    } // End load_plugin_textdomain ()

    /**
     * Main WABS Instance
     *
     * Ensures only one instance of WABS is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @see WABS()
     * @return Main WABS instance
     */
    public static function instance ( $file = '', $version = '1.0.0' ) {
        SELF::$_instance = ( SELF::$_instance ?: new SELF( $file, $version ) );
        return SELF::$_instance;
    } // End instance ()

    /**
     * Cloning is forbidden.
     *
     * @since 1.0.0
     */
    public function __clone () {
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), SELF::$version );
    } // End __clone ()

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup () {
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), SELF::$version );
    } // End __wakeup ()

    /**
     * Installation. Runs on activation.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function install () {
        $this->_log_version_number();
    } // End install ()

    /**
     * Log the plugin version number.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    private function _log_version_number () {
        update_option( SELF::KEY . 'version', SELF::$version );
    } // End _log_version_number ()

}
