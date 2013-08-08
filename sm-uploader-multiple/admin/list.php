<?php
// if delete gallery,
$hidden_field_name = "hf";
$Sm_Uploader = new Sm_Uploader_Multi();

if( isset($_GET[ $hidden_field_name ]) && $_GET[ $hidden_field_name ] == 'D' ) {
    if(isset($_GET['smi_id'])):
        $Sm_Uploader->delete_my_gallery(h($_GET['smi_id']));
    endif;
}

?>

<script>
jQuery(document).ready(function($){
jQuery("#galleryList").find("ul.gal").children("li").hover(function(){
    jQuery(this).find("div.menus").stop(true, true).animate({opacity:0.8}, 200);
},function(){
    jQuery(this).find("div.menus").stop(true, true).animate({opacity:0}, 200);
});

disp();
});

function disp(){

    jQuery("div.menus").find('a.delete').click(function(){
        var comfirmmsg = "<?php _e('Are you sure?', SMUPL_M_DOMAIN); ?>";
        var canceledmsg = "<?php _e('Canceled.', SMUPL_M_DOMAIN); ?>";

        if(window.confirm(comfirmmsg)){
            location.href = jQuery(this).attr("href");
        }else{
            window.alert(canceledmsg);
        }
        return false;
    })

}
</script>
<div class="wrap" >
<h2><?php _e('Gallery image uploader', SMUPL_M_DOMAIN); ?></h2>
<div id='poststuff'>

<div id='galleryList' class="postbox">
<h3 class=""><span><?php _e('Your Galleries', SMUPL_M_DOMAIN); ?></span></h3>
<div class="inside">
    <ul class='gal' id="sortable">
<?php

    $curUrl = curPageURL();
    if(strstr($curUrl, "&")){
        $splited = explode("&", $curUrl);
        $curUrl = $splited[0];
    }

    if($wpdb->get_var( sprintf("SHOW tables LIKE '%s'",SMUPL_M_TABLE) ) == SMUPL_M_TABLE) {

        $gals=$Sm_Uploader->get_galleries();

        foreach ($gals as $key => $value) {
            $editUrl = $curUrl . '&' . $hidden_field_name . '=Y&' . $sm_param_name . '=' . $value->id;
            $deleteUrl = $curUrl . '&' . $hidden_field_name . '=D&' . $sm_param_name . '=' . $value->id;

            $cont = unserialize($value->contents);

            $title = $value->title;

            if(preg_match("/^[a-zA-Z0-9 \t\n\r\f]+$/", $title)){
                $title = (mb_strlen($title, 'UTF8') > 20 )? mb_substr($title,0,20).".." :$title;
            }else{ // if include multibyte strings
                $title = (mb_strlen($title, 'UTF8') > 12 )? mb_substr($title,0,12).".." :$title;
            }


            $update = date("F j, Y", strtotime($value->date_update));
            $img = '';
            $num_array = array();
            for($i=0; $i<count($cont); $i++){
                $num_array[] = $i;
            }
            shuffle($num_array);
            $max = (count($cont) <= 4)? count($cont) : 4; 
            for($i=0; $i<$max; $i++){
                $img .= wp_get_attachment_image( $cont[$num_array[$i]]['img'] , array(80,80));
            }

            echo "<li>\n";
            echo '<div class="inner">';
            echo '<div class="garea">'.$img.'</div>';
            echo '<div class="menus"><ul class="child">';
            echo '<li><a href="'. $editUrl . '">'. '<img src="' . plugins_url() . '/' . SMUPL_M_PLG_DIR . '/img/edit.png" alt="' . __('Edit Gallery', SMUPL_M_DOMAIN) . '" />' .'</a></li>';
            echo '<li><a href="'. $deleteUrl . '" class="delete">'. '<img src="' . plugins_url() . '/' . SMUPL_M_PLG_DIR . '/img/del.png" alt="' . __('Delete Gallery', SMUPL_M_DOMAIN) . '" />' .'</a></li>';
            echo '</ul>';
            echo '<p class="shortcode">[get_sm_galleries id="' . $value->id . '"]</p>';
            echo '</div>';
            echo '<p class="title">' . $title . '</p>';
            echo '<p class="update">' . __('Last Update : ', SMUPL_M_DOMAIN) . $update . '</p>';
            echo "</div></li>\n";
        }
    }

    /* this is new gallery link. */
    $newLink = $curUrl . '&' . $hidden_field_name . '=Y&' . $sm_param_name . '=n';
    echo '<li><div class="inner"><a href="'. $newLink . '" class="add"><div class="garea"><img src="' . plugins_url() . '/' . SMUPL_M_PLG_DIR. '/img/add-gallery.png" class="addnew" alt="Add new gallery"/></div><div class="menus add">'.__('Add New Gallery', SMUPL_M_DOMAIN).'</div></a></div></li>';



?>
    </ul>

</div><!--/.inside-->
</div><!--/#galleryList-->
</div>


</div><!-- .wrap -->
