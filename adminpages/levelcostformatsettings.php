<?php
//only admins can get this
if ( ! function_exists( "current_user_can" ) || ( ! current_user_can( "manage_options" ) && ! current_user_can( "pmpro_emailtemplates" ) ) ) {
	die( __( "You do not have permissions to perform this action.", "pmproet" ) );
}

global $wpdb, $msg, $msgt, $current_user;

require_once( PMPRO_DIR . "/adminpages/admin_header.php" );
echo "test 1 running";



function cost_format_settings() {
	echo "test 2 running";
    $custom_fields = array(
        'field1' => array(
            'field_name' => 'pmpro_hide_now',
            'field_type' => 'select',
            'label' => 'pmpro_hide_now_switcher',
            'value' => 'no'
        ) ,
        'field2' => array(
            'field_name' => 'pmpro_use_free',
            'field_type' => 'select',
            'label' => 'pmpro_use_free_switcher',
            'value' => 'no'
        ),
        'field3' => array(
            'field_name' => 'pmpro_use_slash',
            'field_type' => 'select',
            'label' => 'pmpro_use_slash_switcher',
            'value' => 'no'
        ),
        'field4' => array(
            'field_name' => 'pmpro_hide_decimals',
            'field_type' => 'select',
            'label' => 'pmpro_hide_decimals_switcher',
            'value' => 'no'
        ),
        'field5' => array(
            'field_name' => 'pmpro_abbreviate_time',
            'field_type' => 'select',
            'label' => 'pmpro_abbreviate_time_switcher',
            'value' => 'no'
        )
    );

    return $custom_fields;
}
add_filter('pmpro_custom_advanced_settings','cost_format_settings');
?>

	<form action="" method="post" enctype="multipart/form-data">
	<h2><?php _e( 'Cost Format', 'pmprocf' ); ?></h2>
	<table class="form-table">
	<tr class="status hide-while-loading" style="display:none;">
		<th scope="row" valign="top"></th>
		<td>
			<div id="message">
				<p class="status_message"></p>
			</div>

		</td>
	</tr>
	<tr>
	<th scope="row" valign="top">
		<label for="pmpro_hide_now_switcher">Hide the word "Now".</label>
	</th>
	<td>
	<select name="pmpro_hide_now_switcher" id="pmpro_hide_now_switcher">
	<option value="no"><?php _e('No', 'pmprocf'); ?></option>
	<option value="yes"><?php _e('Yes', 'pmprocf'); ?></option>
	</td>
	</tr>
	
	<tr>
	<th scope="row" valign="top">
		<label for="pmpro_use_free_switcher">Use "Free" instead of $0.00</label>
	</th>
	<td>
	<select name="pmpro_use_free_switcher" id="pmpro_use_free_switcher">
	<option value="no"><?php _e('No', 'pmprocf'); ?></option>
	<option value="yes"><?php _e('Yes', 'pmprocf'); ?></option>
	</td>
	</tr>
	
	<tr>
	<th scope="row" valign="top">
		<label for="pmpro_use_slash_switcher">Use "/" instead of "per".</label>
	</th>
	<td>
	<select name="pmpro_use_slash_switcher" id="pmpro_use_slash_switcher">
	<option value="no"><?php _e('No', 'pmprocf'); ?></option>
	<option value="yes"><?php _e('Yes', 'pmprocf'); ?></option>
	</td>
	</tr>
	
	<tr>
	<th scope="row" valign="top">
		<label for="pmpro_hide_decimals_switcher">Hide unnecessary decimals.</label>
	</th>
	<td>
	<select name="pmpro_hide_decimals_switcher" id="pmpro_hide_decimals_switcher">
	<option value="no"><?php _e('No', 'pmprocf'); ?></option>
	<option value="yes"><?php _e('Yes', 'pmprocf'); ?></option>
	</td>
	</tr>
	
	<tr>
	<th scope="row" valign="top">
		<label for="pmpro_abbreviate_time_switcher">Abbreviate "Month", "Week", and "Year" to "Mo", "Wk", and "Yr"</label>
	</th>
	<td>
	<select name="pmpro_abbreviate_time_switcher" id="pmpro_abbreviate_time_switcher">
	<option value="no"><?php _e('No', 'pmprocf'); ?></option>
	<option value="yes"><?php _e('Yes', 'pmprocf'); ?></option>
	</td>
	</tr>
	
	</table>
	<?php wp_nonce_field( 'pmprocf', 'security' ); ?>
	</form>

	<?php
	require_once( PMPRO_DIR . "/adminpages/admin_footer.php" );
	?>
