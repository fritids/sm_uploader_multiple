<?php
$Sm_Uploader = new Sm_Uploader_Multi();

    // define field names
    $hidden_field_name = "hf";

    // get id
    $gal_id = h($_GET['smi_id']);


    if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' && ( $_POST['gal_title'] != "" && $_POST['sm_img'] != "") ) {
    // Get posted value
        check_admin_referer( 'nonce' );
        
        $gal_id = $_POST['gal_id'];
        $new_title = $_POST['gal_title'];
        $opt_imgs = $_POST['sm_img'];
        $opt_titles = $_POST['sm_title'];

        $imgAmt = count($opt_imgs);// amount of uploaded image
        $ar = array();
        if($imgAmt > 0):
            for($i=0; $i<$imgAmt; $i++){
                $ar[$i]['img'] = $_POST['sm_img'][$i];
                $ar[$i]['title'] = $_POST['sm_title'][$i];
                //$ar[$i]['url'] = $_POST['sm_url'][$i];
            }
        endif;

        // update and redirect. 
        if($gal_id === 'n'): // New gallery
            $Sm_Uploader->update_gallery($gal_id, $new_title, $ar, true);
            $gal_id = mysql_insert_id();
        else:
            $Sm_Uploader->update_gallery($gal_id, $new_title, $ar);
        endif;
        
?>
<div class="updated"><p><strong><?php _e('Success!!', SMUPL_M_DOMAIN) ?></strong></p></div>
<?php
    }

?>

<div class="wrap" >
<h2><?php _e('Gallery image uploader', SMUPL_M_DOMAIN) ?></h2>
<div id='poststuff'>

<div id='galleryArea' class="postbox">
<h3 class=""><span><?php _e('Add images', SMUPL_M_DOMAIN); ?></span></h3>
<div class="inside">
<form name="form" method="post" action="">
<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
<input type="hidden" name="gal_id" value="<?php echo $gal_id; ?>">
<?php
    $gal_array = array();
    if($gal_id == 'n'){
        $gal_title = "";
        if(isset($ar)){
            $amtImages = count($ar);
            $gal_array = $ar;            
        }else{
            $amtImages = 1;
        }

    }else{
        $gal_array_val = $Sm_Uploader->get_gallery_images($gal_id);
        $gal_title = $gal_array_val[0];
        $gal_array = $gal_array_val[1];
        $amtImages = count($gal_array);
    }
?>
<p><?php _e("Gallery Title", SMUPL_M_DOMAIN); ?> : <input type="text" value="<?php echo $gal_title; ?>" name="gal_title" size="50"></p>
    <ul class='gal' id="sortable">
<?php
    for($i=0; $i<$amtImages; $i++){

        $imgId = (isset($gal_array[$i]['img']))? $gal_array[$i]['img'] : "";
        $image = wp_get_attachment_image( $imgId);
        $imgTitle = (isset($gal_array[$i]['title']))? $gal_array[$i]['title'] : "";

?>
        <li id="gal_<?php echo $i; ?>" class="cont">
            <table>
                <tr>
                <td width="30%">
                    <label class="title"><?php _e('Title', SMUPL_M_DOMAIN) ?>:</label>
                    <input type="text" name="sm_title[]" value="<?php echo $imgTitle; ?>" class="title" size="40">
                    <!--
                    <br>
                    <label class="title"><?php _e('URL', SMUPL_M_DOMAIN) ?>:</label>
                    <input type="text" name="sm_url[]" value="<?php echo $imgTitle; ?>" class="title" size="40">
                    -->
                </td>
                <td width="20%">
                    <label class="img"><?php _e('Image', SMUPL_M_DOMAIN) ?>:</label>
                    <input type="button" class="button demo-media" value="<?php _e('Select image', SMUPL_M_DOMAIN) ?>">
                </td>
                <td width="15%">
                    <div class="img">
                    <?php echo $image; ?>
                    <input type="hidden" name="sm_img[]" value="<?php echo $imgId; ?>" class="img">
                    </div>
                </td>
                <td>
                    <div class="addimg">
                        <ul>
                            <li><a href="#" class="add"><img src="<?php echo plugins_url() . '/' . SMUPL_M_PLG_DIR; ?>/img/add.png" alt="add" /><?php _e('Add image', SMUPL_M_DOMAIN) ?></a></li>
                            <li><a href="#" class="remove" <?php echo ($amtImages <= 1)? 'style="display:none"':''; ?>><img src="<?php echo plugins_url() . '/' .SMUPL_M_PLG_DIR; ?>/img/delete.png" alt="delete" /><?php _e('Remove image', SMUPL_M_DOMAIN) ?></a></li>
                        </ul>
                    </div>
                </td>
                </tr>
            </table>
        </li>
<?php
    }
?>
    </ul>
<?php if ( function_exists( 'wp_nonce_field' ) ) wp_nonce_field( 'nonce' ); ?>

<input type="submit" name="Submit" class="button-primary" value="<?php _e('Save Changes', SMUPL_M_DOMAIN) ?>" />
<span id="notice_message" class="notice" style="display:none"><?php  _e('Updeted contents.', SMUPL_M_DOMAIN); ?></span>
<span id="leave_message" class="" style="display:none"><?php  _e('Some contents updated. It will delete if you leave this page.', SMUPL_M_DOMAIN); ?></span>
</form>
</div><!--/.inside-->
</div><!--/#galleryArea-->
<a href="<?php echo admin_url( 'admin.php?page='. SMUPL_M_DOMAIN );?>"><?php  _e('Back to the List.', SMUPL_M_DOMAIN); ?></a>
</div><!--/#poststuff-->


</div><!-- .wrap -->
