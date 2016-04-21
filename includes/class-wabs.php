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
    protected static $message = "";
    protected static $unique_id = "";
    protected static $scheduled = "";
    protected static $start_date = "";
    protected static $end_date = "";
    protected static $cta_class = "";
    protected static $link = "";
    protected static $target = "";
    protected static $button_text = "";
    protected static $action_symbol = "";
    protected static $text_color = "";
    protected static $action_color = "";
    protected static $background_color = "";
    protected static $top_spacer = "";
    protected static $options = array();

    private static $_active = null;
    private static $_defaults = array(  'behavior'      => 'toggle',
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

        self::$file    = $file;
        self::$version = $version;
        self::$plugin_dir = dirname( self::$file );
        self::$plugin_url = plugins_url( basename( dirname( self::$file ) ) );

        register_activation_hook( self::$file, array( $this, 'install' ) );

        // Setup action bar
        add_action( 'wp', array( $this, 'setup_action_bar' ), 10 );

        // Load frontend JS & CSS
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ),  10 );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );

        // Load admin JS & CSS
        // add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 10, 1 );
        // add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ), 10, 1 );

        // Handle localisation
        $this->load_plugin_textdomain();
        add_action( 'init', array( $this, 'load_localisation' ), 0 );

    }

    /**
     * Setup Object
     */
    private static function _setup_action_bar() { 

        global $post;

        $blog_id = get_option( 'page_for_posts' );

        $post_id = ( ! is_front_page() && is_home() ) ? $blog_id : $post->ID;

        $defaults = array(
            'unique_id'         =>  uniqid(),
            'active'            =>  'n',
            'message'           =>  NULL,
            'scheduled'         =>  'n',
            'start_date'        =>  '0',
            'end_date'          =>  current_time('timestamp') + DAY_IN_SECONDS,
            'cta_class'         =>  'without_cta',
            'link'              =>  NULL,
            'target'            =>  '_self',
            'button_text'       =>  NULL,
            'action_symbol'     =>  'delta',
            'text_color'        =>  '#FFFFFF',
            'action_color'      =>  '#FFFFFF',
            'background_color'  =>  '#753BBD', );
              
        $WABS = array();
        $output = "";

        foreach ( $defaults as $key => $value) {
            $WABS[$key] = self::_get_meta_value( $post_id, self::META.$key );
        }

        $WABS = wp_parse_args( $WABS, $defaults );
        $has_cta = ( trim( $WABS['link'] ) && trim( $WABS['button_text'] ) );
        $in_range = self::_check_in_range( $WABS['start_date'], $WABS['end_date'], current_time('timestamp') );
        $scheduled = bool_from_yn( $WABS['scheduled'] );
        $unique_id = sprintf( "%s_post_%d", esc_attr( $WABS['unique_id'] ), $post_id );

        if( $has_cta ) { $WABS['cta_class'] = 'with_cta'; } else { $WABS['cta_class'] = 'without_cta'; }
        if( self::_get_brightness( $WABS['background_color'] ) > 130 ) { $WABS['action_color'] = '#000000'; } else { $WABS['action_color'] = '#FFFFFF'; }
        
        if( ( $WABS['active'] && ! $scheduled ) || ( $WABS['active'] && $scheduled && $in_range ) ) {
           
            $html = '<div id=\'%1$s%2$s\' class=\'%1$sbar %1$s%3$s\' style=\'position:absolute;transform:translate(0px,-100%%);\'> <div class=\'%1$scontainer\'> <div class=\'%1$scol-0\'>&nbsp;</div> <div class=\'%1$sinner %1$scol-1\'> <div class=\'%1$smessage\'> <p> %4$s </p> </div> <div class=\'%1$scta\'> <a class=\'%1$sbutton\' href=\'%5$s\' target=\'%6$s\'>%7$s</a> </div> </div> <div class=\'%1$scol-2\'> <a href=\'javascript:void(0);\' id=\'%1$sclose_bar_%2$s\' class=\'%1$sclose_bar\'> %8$s </a> </div> </div> </div>';
            $output = sprintf( $html, self::TOKEN, esc_attr( $unique_id ), esc_attr( $WABS['cta_class'] ), self::_sanitize_js( $WABS['message'] ), esc_url( $WABS['link'] ), esc_attr( $WABS['target'] ), self::_sanitize_js( $WABS['button_text'] ), self::_action_symbol_type_js( $WABS['action_symbol'] ) );
        
        } else { return false; }

        $WABS = array_map( 'wp_kses_data', $WABS );

        self::$id = sprintf( "#%s%s", self::TOKEN, esc_attr( $unique_id ) );
        self::$html = $output;
        self::$top_spacer = self::_top_spacer($post_id);
        self::$options = self::$_defaults;

        foreach ( $WABS as $property => $value ) {
            self::${"$property"} = $value;
        }
        return true;

    }
    private static function _get_meta_value( $id = 0, $key='' ){
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
        $R = hexdec(substr($hex, 0, 2));
        $G = hexdec(substr($hex, 2, 2));
        $B = hexdec(substr($hex, 4, 2));
        return (($R * 299) + ($G * 587) + ($B * 114)) / 1000;
    }
    private static function _top_spacer( $post_id ) {
        return _wabs_top_spacer( $post_id );
    }
    private static function action_symbol_type( $type = null ) {
        return _wabs_action_symbol( ($type ) ? $type : self::$action_symbol );
    }
    private static function _action_symbol_type_js( $type = null ) {
        return _wabs_action_symbol( ($type ) ? $type : self::$action_symbol );
    }
    private static function _sanitize_js($arg) {
        return wp_slash( wp_kses_data( $arg ) );
    }
    private static function _check_in_range($start_date,$end_date,$date_now) {
      return (($date_now >= $start_date) && ($date_now <= $end_date));
    }


    /* PUBLIC FUNCTIONS */

    public function setup_action_bar(){

        self::$_active = ( is_null( self::$_active ) ) ? self::_setup_action_bar() : self::$_active ;

    }

    /**
     * Enqeue CSS with custom color values
     *
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function enqueue_styles(){

        if( self::$_active ) {

            wp_register_style( self::PLUGIN . '-frontend', esc_url( self::$plugin_url ) . '/css/' . self::PLUGIN . '.css', array(), self::$version );
            wp_enqueue_style(  self::PLUGIN . '-frontend' );

            // Custom color CSS
            $text_color       = self::$text_color;
            $action_color     = self::$action_color;
            $background_color = self::$background_color;

            $color_css = "
            .wabs_button {
                color: {$background_color};
                background-color: {$text_color}
            }
            .wabs_button:hover,
            .wabs_container,
            .wabs_message {
                color: {$text_color};
                background-color: {$background_color}
            }
            .wabs_close_bar .cls-1 {
                color: {$action_color}
            }
            .wabs_close_bar:hover .cls-1 {
                opacity: .4;
                color: {$action_color}
            }";
            wp_add_inline_style( self::PLUGIN . '-frontend', $color_css );
        }
    }

    public static function enqueue_scripts(){

        global $post;

        if( self::$_active ) {

            wp_enqueue_script( 'jquery-effects-core' );
            wp_enqueue_script( 'jquery-transit',  esc_url( self::$plugin_url ) . '/js/jquery.transit.min.js', array( 'jquery' ), self::$version, true );
            
            wp_register_script( self::PLUGIN . '-frontend', esc_url( self::$plugin_url ) . '/js/jquery.' . self::PLUGIN . '.js', array( 'jquery' ), self::$version, true );
            wp_enqueue_script(  self::PLUGIN . '-frontend' );

            wp_localize_script( self::PLUGIN . '-frontend', self::KEY . 'setting', 
                array( 
                    self::KEY . 'ID'              => self::$id,  
                    self::KEY . 'HTML'            => self::$html, 
                    self::KEY . 'active'          => self::$active,  
                    self::KEY . 'message'         => self::$message,
                    self::KEY . 'uniqueID'        => self::$unique_id, 
                    self::KEY . 'scheduled'       => self::$scheduled, 
                    self::KEY . 'startDate'       => self::$start_date, 
                    self::KEY . 'endDate'         => self::$end_date, 
                    self::KEY . 'ctaClass'        => self::$cta_class, 
                    self::KEY . 'link'            => self::$link, 
                    self::KEY . 'target'          => self::$target, 
                    self::KEY . 'buttonText'      => self::$button_text, 
                    self::KEY . 'actionSymbol'    => self::action_symbol_type(), 
                    self::KEY . 'textColor'       => self::$text_color,
                    self::KEY . 'actionColor'     => self::$action_color,  
                    self::KEY . 'backgroundColor' => self::$background_color, 
                    self::KEY . 'topSpacer'       => self::$top_spacer, 
                    self::KEY . 'options'         => self::$options,
                )
            );
        }
    }

    /**
     * Load admin CSS.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function admin_enqueue_styles ( $hook = '' ) {
        wp_register_style( self::PLUGIN . '-admin', esc_url( self::$plugin_url ) . '/css/admin.css', array(), self::$version );
        wp_enqueue_style(  self::PLUGIN . '-admin' );
    } // End admin_enqueue_styles ()

    /**
     * Load admin Javascript.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function admin_enqueue_scripts ( $hook = '' ) {
        wp_register_script( self::PLUGIN . '-admin', esc_url( self::$plugin_url ) . '/js/admin.js', array( 'jquery' ), self::$version );
        wp_enqueue_script(  self::PLUGIN . '-admin' );
    } // End admin_enqueue_scripts ()

    /**
     * Load plugin localisation
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function load_localisation () {
        load_plugin_textdomain( self::PLUGIN, false, dirname( plugin_basename( self::$file ) ) . '/lang/' );
    } // End load_localisation ()

    /**
     * Load plugin textdomain
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function load_plugin_textdomain () {
        $domain = self::PLUGIN;

        $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

        load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
        load_plugin_textdomain( $domain, false, dirname( plugin_basename( self::$file ) ) . '/lang/' );
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
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self( $file, $version );
        }
        return self::$_instance;
    } // End instance ()

    /**
     * Cloning is forbidden.
     *
     * @since 1.0.0
     */
    public function __clone () {
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), self::$version );
    } // End __clone ()

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup () {
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), self::$version );
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
        update_option( self::KEY . 'version', self::$version );
    } // End _log_version_number ()

}
