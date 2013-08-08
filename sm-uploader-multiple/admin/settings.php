 <?php
global $wpdb;
// define field names
$hidden_field_name = "hf";
$sm_param_name = "smi_id";

// setting page
if( isset($_GET['sm_section']) && $_GET['sm_section'] == 'sm_setting'){
    require SMUPL_M_PLG_FULLPATH.'/admin/sm_setting.php';
}else{

	// gallery pages
	if( isset($_GET[$sm_param_name]) && $_GET[$hidden_field_name] == 'Y' ) {
	    require SMUPL_M_PLG_FULLPATH.'/admin/upload.php';
	}else{
	    require SMUPL_M_PLG_FULLPATH.'/admin/list.php';
	}

}
