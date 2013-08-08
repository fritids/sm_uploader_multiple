<?php
$Sm_Uploader = new Sm_Uploader_Multi();

// define field names
$hidden_field_name = "hf";

if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
// Get posted value
    check_admin_referer( 'nonce' );
    
    // update and redirect.
    $data = h($_POST);  
    $Sm_Uploader->update_settings($data);
        
?>
<div class="updated"><p><strong><?php _e('Success!!', SMUPL_M_DOMAIN) ?></strong></p></div>
<?php
}

$options 	= get_option(SMUPL_M_DOMAIN.'-settings');
$opt_title 	= (isset($options['title']))? $options['title'] : "";
$opt_id		= (isset($options['id']))? $options['id'] : "";
$opt_cl 	= (isset($options['class']))? $options['class'] : "";

?>
<div class="wrap" >
<h2><?php _e('Display settings', SMUPL_M_DOMAIN); ?></h2>
<div id='poststuff'>

<div id='galleryList' class="postbox">
<h3 class=""><span><?php _e('Settings', SMUPL_M_DOMAIN); ?></span></h3>
<div class="inside">
<form name="form" method="post" action="">
<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
<table>
  <tr>
  	<th width="20%"><p><?php _e('Display Title', SMUPL_M_DOMAIN); ?></p></th>
  	<td><input type="checkbox" name="disp_title" value="display" <?php echo ($opt_title == 'display')? "checked":"" ?>> Display</td>
  </tr>
  <tr>
  	<th><p><?php _e('UL tag id', SMUPL_M_DOMAIN); ?></p></th>
  	<td><input type="text" name="ul_id" value="<?php echo ($opt_id != "")? $opt_id : ""; ?>" size="50"></td>
  </tr>
  <tr>
  	<th><p><?php _e('UL tag class', SMUPL_M_DOMAIN); ?></p></th>
  	<td><input type="text" name="ul_class" value="<?php echo ($opt_cl != "")? $opt_cl : ""; ?>" size="50">
  		<span><?php _e('If multiple class needed, split it by space.', SMUPL_M_DOMAIN); ?></span>
  	</td>
  </tr>

</table>
<?php if ( function_exists( 'wp_nonce_field' ) ) wp_nonce_field( 'nonce' ); ?>

<input type="submit" name="Submit" class="button-primary" value="<?php _e('Save Changes', SMUPL_M_DOMAIN) ?>" />
</form>

</div><!--/.inside-->
</div><!--/#galleryList-->
</div>


</div><!-- .wrap -->

