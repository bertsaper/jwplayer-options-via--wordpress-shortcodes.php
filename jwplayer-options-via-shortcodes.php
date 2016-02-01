<?php
/**
 * @package JWPlayer_Options_Via_Shortcodes
 * @version 1.0
 */
/*
Plugin Name: JWPlayer Options Via Shortcodes
Plugin URI: https://github.com/bertsaper/jwplayer-options-via-wordpress-shortcodes
Description: Adds shortcode capability for creating code for Licensed JWPlayers via WordPress Shortcodes. After acticvation, see settings link "JWPlayer Options Settings" for instructions.
Author: Bert Saper
Version: 1.0
Author URI: http://saper.us
*/
/**
* Install Uninstall Delete Hooks
* Uses instructions from
* https://www.smashingmagazine.com/2011/09/how-to-create-a-wordpress-plugin/
*/
register_activation_hook(   __FILE__, array( 'JWPlayerOptionsSettingsPage', 'on_activation' ) );
register_deactivation_hook( __FILE__, array( 'JWPlayerOptionsSettingsPage', 'on_deactivation' ) );
register_uninstall_hook(    __FILE__, array( 'JWPlayerOptionsSettingsPage', 'on_uninstall' ) );
add_action( 'plugins_loaded', array( 'JWPlayerOptionsSettingsPage', 'init' ) );
class JWPlayerOptionsSettingsPage
{
    
    protected static $instance;
    public static function init()
    {
        is_null( self::$instance ) AND self::$instance = new self;
        return self::$instance;
    }
    public static function on_activation()
    {
        if ( ! current_user_can( 'activate_plugins' ) )
            return;
        $plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
        check_admin_referer( "activate-plugin_{$plugin}" );
    }
    public static function on_deactivation()
    {
        if ( ! current_user_can( 'activate_plugins' ) )
            return;
        $plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
        check_admin_referer( "deactivate-plugin_{$plugin}" );
    }
    public static function on_uninstall()
    {
        if ( ! current_user_can( 'activate_plugins' ) )
            return;
        check_admin_referer( 'bulk-plugins' );
        // Important: Check if the file is the one
        // that was registered during the uninstall hook.
        if ( __FILE__ != WP_UNINSTALL_PLUGIN )
            global $wpdb;
            $wpdb->query('DELETE  FROM  ' . $wpdb->prefix . 'options WHERE option_name = "jwplayer_options_option_name"');       
            return;
    }
    
    
     /**
     * Below learned from
     * https://codex.wordpress.org/Creating_Options_Pages
     */    
    
     /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }
    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin', 
            'JWPlayer Options Settings', 
            'manage_options', 
            'jwplayer-options-setting-admin', 
            array( $this, 'create_admin_page' )
        );
    }
    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'jwplayer_options_option_name' );
        ?>

<div class="wrap">
  <h2>Short Code for Licensed JW Players</h2>
  <h3>Please be sure to review the default entries beneath these instructions.</h3>
  <div style="max-width: 650px">
    <p>This Short Code:</p>
    <blockquote><strong>[videoInfo id=&quot;path_to_video.mp4|path_to_image.jpg&quot;]</strong></blockquote>
    <p>
    Will embed the JWPlayer, which defaults to HTML5 but will revert to Flash if needed. The video path and the image path are separated by a bar (|).
    </p>
    <p><strong>Please Note:</strong> If you want to embed more than one player per page, you will need to enter an alternative Div ID for by adding a bar and a name after the image address:</p>
    <blockquote>[videoInfo id=&quot;path_to_video.mp4|path_to_image.jpg<strong>|AnotherDivName&quot;]</strong></blockquote>
    <p>You can choose any name other than &quot;video-player1&quot;", which is the default. A third player would need a name different from the other two, etc.</p>
    <p>You can also over-right the default width entered below. To do so, you will also need to enter a Div ID name. This changes the width from the default to 40%:</p>
    <blockquote>[videoInfo id=&quot;path_to_video.mp4|path_to_image.jpg|DivName<strong>|40%&quot;]</strong></blockquote>
    <p>Similarly, changing the aspect ratio of the player requires the entry of a Div ID name and a width. This will change the aspect ratio to 4:3:</p>
    <blockquote>[videoInfo id=&quot;path_to_video.mp4|path_to_image.jpg|DivName|100%<strong>|4:3&quot;] </strong></blockquote>
  </div>
  <form method="post" action="options.php">
    <?php
                // This prints out all hidden setting fields
                settings_fields( 'jwplayer_options_option_group' );   
                do_settings_sections( 'jwplayer-options-setting-admin' );
                submit_button(); 
            ?>
  </form>
</div>
<?php
    }
    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'jwplayer_options_option_group', // Option group
            'jwplayer_options_option_name', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );
        add_settings_section(
            'setting_section_id', // ID
            'Default Player Settings (Width and Aspect Ration can be overidden via shortcode. See instructions above.)', // Title
            array( $this, 'print_section_info' ), // Callback
            'jwplayer-options-setting-admin' // Page
        );  
        add_settings_field(
            'video_width', // ID
            'Default Video Width (Generaly 100%). Can be overwritten via shortcode&mdash; see above.', // Title 
            array( $this, 'video_width_number_callback' ), // Callback
            'jwplayer-options-setting-admin', // Page
            'setting_section_id' // Section           
        );      
        add_settings_field(
            'aspect_ratio', 
            'Default Aspect Ratio (Generally 16:9). Can be overwritten via shortcode&mdash; see above.', 
            array( $this, 'aspect_ratio_callback' ), 
            'jwplayer-options-setting-admin', 
            'setting_section_id'
        );  
        
        add_settings_field(
            'player_path', 
            'Player JS Path from Theme Folder', 
            array( $this, 'player_path_callback' ), 
            'jwplayer-options-setting-admin', 
            'setting_section_id'
        );  
        
        add_settings_field(
            'player_key_path', 
            'Player Key JS Path from Theme Folder (Please add this to a file named with a js suffix: jwplayer.key=&quot;Your Player Key Number&quot;; ', 
            array( $this, 'player_key_path_callback' ), 
            'jwplayer-options-setting-admin', 
            'setting_section_id'
        );   
  
          add_settings_field(
            'player_version_number', 
            'JW Player Version Number (Added to the WP Array)', 
            array( $this, 'player_version_number_callback' ), 
            'jwplayer-options-setting-admin', 
            'setting_section_id'
        );      
                   
    }
    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['video_width'] ) )
            $new_input['video_width'] = sanitize_text_field( $input['video_width'] );
        if( isset( $input['aspect_ratio'] ) )
            $new_input['aspect_ratio'] = sanitize_text_field( $input['aspect_ratio'] );
            
        if( isset( $input['player_path'] ) )
            $new_input['player_path'] = sanitize_text_field( $input['player_path'] );
        
        if( isset( $input['player_key_path'] ) )
            $new_input['player_key_path'] = sanitize_text_field( $input['player_key_path'] );
        
        if( isset( $input['player_version_number'] ) )
            $new_input['player_version_number'] = sanitize_text_field( $input['player_version_number'] );
            
        return $new_input;      
    }
    
    
    /** 
     * Print instructions and the Section text
     */
    public function print_section_info()
    {
        
    print 'Enter your settings below:'; 
    
    }
    /** 
     * Get the settings option array and print one of its values
     */
    public function video_width_number_callback()
    {
        printf(
            '<input type="text" id="video_width" name="jwplayer_options_option_name[video_width]" value="%s" />',
            isset( $this->options['video_width'] ) ? esc_attr( $this->options['video_width']) : ''
        );
    }
    /** 
     * Get the settings option array and print one of its values
     */
    public function aspect_ratio_callback()
    {
        printf(
            '<input type="text" id="aspect_ratio" name="jwplayer_options_option_name[aspect_ratio]" value="%s" />',
            isset( $this->options['aspect_ratio'] ) ? esc_attr( $this->options['aspect_ratio']) : ''
        );
    }
    public function player_path_callback()
    {
        
        printf(
            '<input type="text" id="player_path" name="jwplayer_options_option_name[player_path]" value="%s" />',
            isset( $this->options['player_path'] ) ? esc_attr( $this->options['player_path']) : ''
        );
    }
    
    public function player_key_path_callback()
    {
        
        printf(
            '<input type="text" id="player_key_path" name="jwplayer_options_option_name[player_key_path]" value="%s" />',
            isset( $this->options['player_key_path'] ) ? esc_attr( $this->options['player_key_path']) : ''
        );
    }
    
    public function player_version_number_callback()
    {
        
        printf(
            '<input type="text" id="player_version_number" name="jwplayer_options_option_name[player_version_number]" value="%s" />',
            isset( $this->options['player_version_number'] ) ? esc_attr( $this->options['player_version_number']) : ''
        );
    }    
     
}
   
/**
* Short Code for Video
* This Short Code:
* [videoInfo id="path_to_video.mp4|path_to_image.jpg"]
* Will embed the jwplayer, which defaults to HTML5 but can run Flash if needed.
* The video path and the image path are separated by a bar (|).
*/    
    
    
/**
* Get values
*/ 
$jwplayer_options_option_array = get_option( 'jwplayer_options_option_name' );
$player_path = $jwplayer_options_option_array["player_path"];
$player_key_path = $jwplayer_options_option_array["player_key_path"];
$player_version_number = $jwplayer_options_option_array["player_version_number"];
    
/**
* Enque the JW Player Script
*/
if (strlen($player_path) > 3) {
    wp_register_script('jw_player', (get_stylesheet_directory_uri() . $player_path), $player_version_number);
    wp_enqueue_script('jw_player'); 
}
if (strlen($player_key_path) > 3) {
    wp_register_script('jw_player_key', (get_stylesheet_directory_uri() . $player_key_path), $player_version_number);
    wp_enqueue_script('jw_player_key');
}   
 function video_output($atts, $content = null)
{
    /**
    * Get values
    */ 
    $jwplayer_options_option_array = get_option( 'jwplayer_options_option_name' );
    $video_width = $jwplayer_options_option_array["video_width"];
    $aspect_ratio = $jwplayer_options_option_array["aspect_ratio"];
    
   
    
   /**
   * This is the default video div ID name. It can be overwritten if it becomes the third element
   * (separated by a bar (|) in the shortcode ID string. This is needed if more than 1
   * video is to be embedded on a page
   */
   
   $playerID = "video-player1";
    
    extract(shortcode_atts( array('id' => ''), $atts));
    $jwplayerOutput = explode("|", $id);
    
   /**
   * if an entry overrides the default video player id
   */
   
   if( isset( $jwplayerOutput[2] ) )            
    $playerID = $jwplayerOutput[2];    
   /**
   * if an entry overrides the default width
   */
   
   if( isset( $jwplayerOutput[3] ) )            
    $video_width = $jwplayerOutput[3];
    
   /**
   * if an entry overrides the default aspect ratio
   */
   
   if( isset( $jwplayerOutput[4] ) )            
    $aspect_ratio = $jwplayerOutput[4];    
             
    $output  = '<div id="' . $playerID . '">' . chr(10);
    $output .=  chr(9);     
    $output .= '<script type="text/javascript">// <![CDATA[' . chr(10);    
    $output .=  chr(9) . chr(9);    
    $output .= 'jwplayer("' . $playerID . '").setup({' . chr(10);
    $output .=  chr(9) . chr(9);        
    $output .= 'file: "' . $jwplayerOutput[0] . '",' .chr(10); 
    $output .=  chr(9) . chr(9);       
    $output .= 'image: "' . $jwplayerOutput[1] . '",' .chr(10);
    $output .=  chr(9) . chr(9);        
    $output .= 'width: "' . $video_width . '",' . chr(10);
    $output .=  chr(9) . chr(9);          
    $output .= 'aspectratio: "' . $aspect_ratio . '",' . chr(10);
    $output .=  chr(9) . chr(9);    
    $output .= 'wmode: "transaparent"' . chr(10);
    $output .=  chr(9) . chr(9);           
    $output .= '});' . chr(10);
    $output .=  chr(9);       
    $output .= '// ]]></script>' . chr(10);     
    $output .= '</div>' . chr(10);    
    return $output;
}
add_shortcode('videoInfo', 'video_output');   

?>
