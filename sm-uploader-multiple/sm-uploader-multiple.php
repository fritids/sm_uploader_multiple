<?php
/*
Plugin Name: SM Uploader Multiple
Plugin URI: -
Description: Create generic image lists.
Author: shnr.dev@gmail.com
Version: 0.1.0
Author URI: http://blog.shnr.net
License: GPLv2

    Copyright 2013 shnr (email : shnr.dev@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

/* =====================================
 * DEFINE
 */

define('SMUPL_M_DOMAIN', "sm-uploader-multiple");
define('SMUPL_M_OPTION_BASE_NAME', "sm_img");
define('SMUPL_M_PLG_DIR', dirname( plugin_basename( __FILE__ ) ));
define('SMUPL_M_PLG_FULLPATH', plugin_dir_path(__FILE__) );

load_plugin_textdomain(SMUPL_M_DOMAIN, false, SMUPL_M_PLG_DIR .'/lang');

define('SMUPL_M_TABLE', $wpdb->prefix . "smum_images");


/* =====================================
 * CLASS
 */

new Sm_Uploader_Multi();

class Sm_Uploader_Multi {

    function __construct()
    {
        // initialize
        if ( !get_option('smum_options') ) $this->smum_install();

        add_action('admin_menu', array(&$this, 'admin_menu'));
    }

    public function admin_menu()
    {
        $hook = add_menu_page(
            __('SM Uploader Multiple', SMUPL_M_DOMAIN),
            __('SM Uploader Multiple', SMUPL_M_DOMAIN),
            'update_core',
            SMUPL_M_DOMAIN,
            array(&$this, 'admin_page')
        );

        // Add submenu
        add_submenu_page(SMUPL_M_DOMAIN, __('SM Uploader Multiple Setting', SMUPL_M_DOMAIN), __('Display settings', SMUPL_M_DOMAIN), 'manage_options', SMUPL_M_DOMAIN.'&sm_section=sm_setting', array(&$this, 'admin_setting_page'));


        $hidden_field_name = "hf";
        $sm_param_name = "smi_id";

        wp_enqueue_style( 'smup_style', plugins_url("/lib/css/style.css", __FILE__), array(), false, 'all' );   

        if( isset($_GET[$sm_param_name]) && $_GET[$hidden_field_name] == 'Y' ) {
            add_action('admin_print_scripts-'.$hook, array(&$this, 'admin_scripts'));
            wp_enqueue_script( 'jquery-ui-sortable' );
        }
    }

    public function smum_install()
    {
        global $wpdb;

        require_once(ABSPATH.'wp-admin/includes/upgrade.php');
    
        // Get collation info
        $charset_collate = "";
        if ( ! empty($wpdb->charset) )
            $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
        if ( ! empty($wpdb->collate) )
            $charset_collate .= " COLLATE $wpdb->collate";

        //checking if the table is already installed

        if($wpdb->get_var( sprintf("SHOW tables LIKE '%s'",SMUPL_M_TABLE) ) != SMUPL_M_TABLE) {
          $sql = "CREATE TABLE ".SMUPL_M_TABLE. " (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            title varchar(200),
            contents longtext,
            date_create datetime,
            date_update datetime,
            PRIMARY KEY id (id) ) $charset_collate
          ";
          dbDelta($sql);
        }
    }



    public function admin_page()
    {
        require SMUPL_M_PLG_FULLPATH.'/admin/settings.php';
    }


    public function admin_scripts()
    {
        wp_enqueue_media(); 
        wp_enqueue_script(
            'sm-uploader',
            plugins_url("/lib/js/sm-uploader.js", __FILE__),
            array('jquery'),
            filemtime(dirname(__FILE__).'/lib/js/sm-uploader.js'),
            false
        );
    }



    /**
     * Update gallery
     */
    public function update_gallery($id, $title, $data, $firsttime = false){
        global $wpdb;

        if($title == "")
            $title = date("Y-m-d H:i:s");

        if($firsttime){
            $sql = $wpdb->prepare(
              "
              INSERT INTO " . SMUPL_M_TABLE .
              "(title, contents, date_create, date_update)
              VALUES
              (%s, %s, %s, %s)",
              $title,
              serialize($data),
              date("Y-m-d H:i:s"),
              date("Y-m-d H:i:s")
            );
        }else{
            $sql = $wpdb->prepare(
              "Update " . SMUPL_M_TABLE .
              " SET title = %s, contents = %s, date_update = %s " .
              " WHERE id = %d",
              $title,
              serialize($data),
              date("Y-m-d H:i:s"),
              $id
              );
        }

        $wpdb->query($sql);
    }


    /**
     * get all galleries
     *
     * @return Array return attachment ids.
     */
    public function get_galleries()
    {
        global $wpdb;
        $result = $wpdb->get_results( 
                    "
                    SELECT      id, title, contents, date_update
                    FROM       " .SMUPL_M_TABLE. " 
                    ORDER BY id DESC
                    "
                ); 

        return $result;
    }



    /**
     * get_gallery_images
     *
     * @return Array return attachment ids.
     */
    public function get_gallery_images($gal_id = 0)
    {
        global $wpdb;
        $result = array();

        $gals = $wpdb->get_results( 
            "
            SELECT      title, contents
            FROM       " .SMUPL_M_TABLE. " 
            WHERE id = " . $gal_id
        );
        $result[0] = h($gals[0]->title);            
        $result[1] = unserialize($gals[0]->contents);            

        return $result;
    }



    /**
     * delete gallery
     */
    public function delete_my_gallery($id = 0)
    {
        $id = h($id);
        global $wpdb;

        if(!is_array($id))
        {
            $wpdb->query( 
                $wpdb->prepare( 
                    "
                     DELETE FROM " . SMUPL_M_TABLE ."
                     WHERE id = %d
                    ",
                    $id 
                    )
            );
        }
        return;
    }



    /**
     * Update setting
     */
    public function update_settings($data)
    {
        $settings = array(
            'title' => isset($data['disp_title'])? $data['disp_title'] : "",
            'id' => $data['ul_id'],
            'class' => $data['ul_class'],
            );

        update_option( SMUPL_M_DOMAIN.'-settings', $settings );

        return;
    }


}



/*
 * For Template use.
 * retrun as array.
 */
if(!function_exists("get_my_gallery")):
function smu_get_my_gallery($gal_id = 0){
    $results = array();
    $gallery = new Sm_Uploader_Multi();
    $results = $gallery->get_gallery_images($gal_id);

    return $results;
}
endif;

/*
 * For shortcode
 * get gallery like echo do_shortcode(' [get_sm_galleries id="7"]');
 */
add_shortcode('get_sm_galleries', 'smu_get_my_gallery_scode');
function smu_get_my_gallery_scode($atts) {
    if(!isset($atts['id']) || $atts['id'] == "")
        return;

    $gal_id = $atts['id'];

    $results_array = array();
    $gallery = new Sm_Uploader_Multi();
    $results_array = $gallery->get_gallery_images($gal_id);

    $options    = get_option(SMUPL_M_DOMAIN.'-settings');
    $opt_title  = (isset($options['title']))? $options['title'] : "";
    $opt_id     = (isset($options['id']))? $options['id'] : "";
    $opt_cl     = (isset($options['class']))? $options['class'] : "";

    $results = "";
    if($opt_title == "display"){
        $results .= '<p>' . $results_array[0] . "</p>\n";
    }

    $results .= '<ul id="' . $opt_id . '" class="' . $opt_cl . '">'  ."\n";
    foreach ($results_array[1] as $key => $value) {
        $results .='<li>' . wp_get_attachment_image( $value['img'] , array(80,80)) . "</li>\n";
    }
    $results .= "</ul>\n";

    return $results;
}



if(!function_exists("curPageURL")):
function curPageURL() {
    $pageURL = 'http';
    if(isset($_SERVER["HTTPS"])):
       if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
    endif;
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}
endif;


if(!function_exists("h")):
function h($str){
    if(is_array($str)){
        return array_map("h",$str);
    }else{
        return htmlspecialchars($str,ENT_QUOTES);
    }
}
endif;
