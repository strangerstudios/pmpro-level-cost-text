<?php
/*
Plugin Name: Paid Memberships Pro - Custom Level Cost Text Add On
Plugin URI: http://www.paidmembershipspro.com/wp/pmpro-custom-level-cost-text/
Description: Modify the default level cost text per level, per discount code, or globally via advanced settings.
Version: .3.2
Author: Stranger Studios
Author URI: http://www.strangerstudios.com
Text Domain: pmpro-level-cost-text
*/

//Set up settings in Advanced Settings
function pclct_cost_format_settings() {
	$custom_fields = array(
		'pmpro_custom_level_cost_heading' => array(
			'field_name' => 'pmpro_custom_level_cost_heading',
			'field_type' => 'heading',
        	'label' 	 => __('Level Cost Text Settings', "pmpro-level-cost-text"),
		),
        'pmpro_hide_now' => array(
            'field_name' => 'pmpro_hide_now',
            'field_type' => 'select',
            'label'		 => __('Remove "Now"', "pmpro-level-cost-text"),
            'description' => __('Remove the word "now" from level cost text.', "pmpro-level-cost-text"),
            'options' => array(0 => 'No', 1 => 'Yes'),
		),
        'pmpro_use_free' => array(
            'field_name' => 'pmpro_use_free',
            'field_type' => 'select',
            'label'		 => __('Use "Free"', "pmpro-level-cost-text"),
            'description' => __('Use the word "Free" instead of $0.00', "pmpro-level-cost-text"),
            'options' => array(0 => 'No', 1 => 'Yes'),
		),
        'pmpro_use_slash' => array(
            'field_name' => 'pmpro_use_slash',
            'field_type' => 'select',
            'label'		 => __('Use Slashes', "pmpro-level-cost-text"),
            'description' => __('Use "/" instead of "per"', "pmpro-level-cost-text"),
            'options' => array(0 => 'No', 1 => 'Yes'),
		),
        'pmpro_hide_decimals' => array(
            'field_name' => 'pmpro_hide_decimals',
            'field_type' => 'select',
            'label'		 => __('Hide Unnecessary Decimals', "pmpro-level-cost-text"),
            'description' => __('Hide unnecessary decimals', "pmpro-level-cost-text"),
            'options' => array(0 => 'No', 1 => 'Yes'),
		),
        'pmpro_abbreviate_time' => array(
            'field_name' => 'pmpro_abbreviate_time',
            'field_type' => 'select',
            'label'		 => __('Abbreviate Billing Periods', "pmpro-level-cost-text"),
            'description' => __('Abbreviate "Month", "Week", and "Year" to "Mo", "Wk", and "Yr"', "pmpro-level-cost-text"),
            'options' => array(0 => 'No', 1 => 'Yes'),
        )
	);
	
	return $custom_fields;
}
add_filter('pmpro_custom_advanced_settings','pclct_cost_format_settings');

//Adds format options specified in advanced settings
function pclct_format_cost($cost) {
	global $pmpro_currency, $pmpro_currencies;
	
	// If numeric, run through pmpro_round_price as a base
	if ( is_numeric( $cost ) && function_exists( 'pmpro_round_price' ) ) {
		$cost = pmpro_round_price( $cost );
		
		// Format based on currency. This adds zeroes back after the decimal.
		if ( function_exists( 'pmpro_get_currency') ) {
			$currency = pmpro_get_currency();
			$cost = number_format( $cost, $currency['decimals'], $currency['decimal_separator'], $currency['thousands_separator'] );
		}
	}
	
	if(pmpro_getOption('pmpro_hide_now') == 'Yes'){
		$cost = str_replace(" now", "", $cost);
	}
	
	if(pmpro_getOption('pmpro_use_free') == 'Yes'){
		global $pmpro_currency_symbol;
		$cost = str_replace($pmpro_currency_symbol.'0.00', __('Free', "pmpro-level-cost-text"), $cost);
		$cost = str_replace('0.00'.$pmpro_currency_symbol, __('Free', "pmpro-level-cost-text"), $cost);
		$cost = str_replace($pmpro_currency_symbol.'0,00', __('Free', "pmpro-level-cost-text"), $cost);
		$cost = str_replace('0,00'.$pmpro_currency_symbol, __('Free', "pmpro-level-cost-text"), $cost);
	}
	
	if(pmpro_getOption('pmpro_use_slash') == 'Yes'){
		$cost = str_replace(" per ", "/", $cost);
	}
	
	if(pmpro_getOption('pmpro_hide_decimals') == 'Yes'){
		if ( ! empty( $pmpro_currency )
		&& is_array( $pmpro_currencies[$pmpro_currency] )
		&& isset( $pmpro_currencies[$pmpro_currency]['decimal_separator'] ) ) {
			$decimal_separator = $pmpro_currencies[$pmpro_currency]['decimal_separator'];
		} else {
			$decimal_separator = '.';
		}
		
		$parts = explode( $decimal_separator, $cost );
		if ( ! empty( $parts[1] ) && $parts[1] == 0 ) {
			$cost = $parts[0];
		}
	}
	
	if(pmpro_getOption('pmpro_abbreviate_time') == 'Yes'){
		$cost = str_replace("Year", "Yr", $cost);
		$cost = str_replace("Week", "Wk", $cost);
		$cost = str_replace("Month", "Mo", $cost);
	}
	
	return $cost;
}

//Switches out variables within '!!' with the intended value
function pclct_apply_variables( $custom_text, $cost, $level ) {
	$search = array(
		"!!default_cost_text!!",
		"!!short_cost_text!!",
		"!!level_name!!",
		"!!level_description!!",
		"!!initial_payment!!",
		"!!billing_amount!!",
		"!!cycle_number!!",
		"!!cycle_period!!",
		"!!billing_number!!",
		"!!billing_period!!",
		"!!billing_limit!!",
		"!!trial_amount!!",
		"!!trial_limit!!",
		"!!expiration_number!!",
		"!!expiration_period!!",
	);
	
	$replace = array(
		pclct_format_cost($cost),
		pclct_format_cost(str_replace("The price for membership is ", "", $cost)),
		$level->name,
		$level->description,
		pclct_format_cost($level->initial_payment),
		pclct_format_cost($level->billing_amount),
		$level->cycle_number,
		pclct_format_cost($level->cycle_period),
		$level->cycle_number,
		pclct_format_cost($level->cycle_period),
		$level->billing_limit,
		pclct_format_cost($level->trial_amount),
		$level->trial_limit,
		$level->expiration_number,
		pclct_format_cost($level->expiration_period)
	);
	
	$search  = apply_filters('pclct_variables', $search);
	$replace = apply_filters('pclct_variables_content', $replace, $cost, $level);

	return str_replace($search, $replace, $custom_text);
}

function pclct_list_variables(){
	?>
    <style>
	.template_reference dt {display: block;float: left;font-weight: bold; min-width: 160px;margin-right: 10px;}
	.template_reference dd {display: block;margin-left: 170px; }
    </style>
    <dl>
        <dt>!!default_cost_text!!</dt>
		<dd><?php _e('Ex: "The price for membership is $20.00 now and then $10.00 per Year."', "pmpro-level-cost-text"); ?></dd>
        <dt>!!short_cost_text!!</dt>
		<dd><?php _e('Ex: "$20.00 now and then $10.00 per Year."', "pmpro-level-cost-text"); ?></dd>
        <dt>!!level_name!!</dt>
		<dd><?php _e('The name of the level the user is registering for.', "pmpro-level-cost-text"); ?></dd>
        <dt>!!level_description!!</dt>
		<dd><?php _e('The description for the level the user is registering for.', "pmpro-level-cost-text"); ?></dd>
        <dt>!!initial_payment!!</dt>
		<dd><?php _e('The initial payment for the level the user is registering for.', "pmpro-level-cost-text"); ?></dd>
        <dt>!!billing_amount!!</dt>
		<dd><?php _e('How much the user has to pay for a recurring subscription.', "pmpro-level-cost-text"); ?></dd>
        <dt>!!cycle_number!!</dt>
		<dd><?php _e('How many cycle periods must pass for one recurring subscription cycle to be complete.', "pmpro-level-cost-text"); ?></dd>
        <dt>!!cycle_period!!</dt>
		<dd><?php _e('The unit of time cycle_number uses to measure.', "pmpro-level-cost-text"); ?></dd>
        <dt>!!billing_limit!!</dt>
		<dd><?php _e('The total number of recurring billing cycles. 0 is infinite.', "pmpro-level-cost-text"); ?></dd>
        <dt>!!trial_amount!!</dt>
		<dd><?php _e('The cost of one recurring payment during the trial period.', "pmpro-level-cost-text"); ?></dd>
        <dt>!!trial_limit!!</dt>
		<dd><?php _e('The number of billing cycles that are at the trial price.', "pmpro-level-cost-text"); ?></dd>
        <dt>!!expiration_number!!</dt>
		<dd><?php _e('The number expiration periods until the membership expires.', "pmpro-level-cost-text"); ?></dd>
        <dt>!!expiration_period!!</dt>
		<dd><?php _e('The unit of time expiration_number is measured in.', "pmpro-level-cost-text"); ?></dd>
    </dl>
	<?php
}

/*
	This set of functions adds a level cost text field to the edit membership levels page
*/
//add level cost text field to level price settings
function pclct_pmpro_membership_level_after_other_settings()
{
	$level_id = intval($_REQUEST['edit']);
	if($level_id > 0)
		$level_cost_text = pmpro_getCustomLevelCostText($level_id);
	else
		$level_cost_text = "";
	?>
<h3 class="topborder"><?php _e("Custom Level Cost Text", "pmpro-level-cost-text"); ?></h3>
<p><?php echo sprintf(__('Override the default level cost using the available placeholders or custom text. Make sure the prices in this text match your settings above. You can modify the format of the default text in %s', 'pmpro-level-cost-text'), '<a href="' . admin_url('admin.php?page=pmpro-advancedsettings') . '">' . __('Advanced Settings', 'pmpro-level-cost-text') . '.</a>');?></p>
    <table class="form-table">
        <tbody>
        <tr>
			<th scope="row" valign="top"><label for="level_cost_text"><?php _e('Level Cost Text', 'pmpro-level-cost-text');?>:</label></th>
            <td>
				<textarea name="level_cost_text" rows="4" style="width: 100%;"><?php echo esc_textarea($level_cost_text);?></textarea>
				<br /><small><?php echo sprintf(__('Leave blank to use the default text generated by your %s', 'pmpro-level-cost-text'), '<a href="' . admin_url('admin.php?page=pmpro-advancedsettings') . '">' . __('Advanced Settings', 'pmpro-level-cost-text') . '.</a>');?></small>
            </td>
        </tr>
        <tr>
			<th scope="row" valign="top"><label for="level_cost_text"><label for="variable_references"><?php _e('Placeholder&nbsp;Reference', 'pmpro-level-cost-text'); ?>:</label></th>
            <td>
				<div class="template_reference" style="background: #FAFAFA; border: 1px solid #CCC; color: #666; padding: 5px;">
					<p><em><?php _e('Insert these variables in the level cost text field above.', 'pmpro-level-cost-text'); ?></em></p>
					<?php pclct_list_variables(); ?>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
	<?php
}
add_action("pmpro_membership_level_after_other_settings", "pclct_pmpro_membership_level_after_other_settings");

//save level cost text when the level is saved/added
function pclct_pmpro_save_membership_level($level_id)
{
	pmpro_saveCustomLevelCostText($level_id, wp_kses_post( wp_unslash( $_REQUEST['level_cost_text'] ) ) );			//add level cost text for this level
}
add_action("pmpro_save_membership_level", "pclct_pmpro_save_membership_level");

//update subscription start date based on the discount code used
function pclct_pmpro_level_cost_text_levels($cost, $level)
{
	global $wpdb;
	
	$custom_text = pmpro_getCustomLevelCostText($level->id);
	if(!empty($custom_text))
	{
		$cost = pclct_apply_variables($custom_text, $cost, $level);
	}
	else{
		$cost = pclct_format_cost($cost);
	}
	
	return $cost;
}
add_filter("pmpro_level_cost_text", "pclct_pmpro_level_cost_text_levels", 15, 2);		//priority 15, so discount code text will override this

/*	
	This function will save a level_cost_text for a discount code into an array stored in pmpro_code_level_cost_text.
*/
function pmpro_saveCustomLevelCostText($level_id, $level_cost_text)
{
	$all_level_cost_text = get_option("pmpro_level_cost_text", array());
	
	$all_level_cost_text[$level_id] = $level_cost_text;
	
	update_option("pmpro_level_cost_text", $all_level_cost_text);
}

/*
	This function will return the level cost text for a discount code/level combo
*/
function pmpro_getCustomLevelCostText($level_id)
{
	$all_level_cost_text = get_option("pmpro_level_cost_text", array());
	
	if(!empty($all_level_cost_text[$level_id]))
	{
		return $all_level_cost_text[$level_id];
	}
	
	//didn't find it
	return "";
}


/*
	This next set of functions adds the level cost text field to the edit discount code page
*/
//add level cost text field to level price settings
function pclct_pmpro_discount_code_after_level_settings($code_id, $level)
{
	$level_cost_text = pmpro_getCodeCustomLevelCostText($code_id, $level->id);
	?>
    <table>
        <tbody class="form-table">
        <tr>
            <td>
       			<tr>
					<th scope="row" valign="top"><label for="level_cost_text"><?php _e('Level Cost Text', 'pmpro-level-cost-text');?>:</label></th>
            		<td>
						<textarea name="level_cost_text[]" rows="4" style="width: 100%;"><?php echo esc_textarea($level_cost_text);?></textarea>
						<br /><small><?php echo sprintf(__('Leave blank to use the default text generated by your %s', 'pmpro-level-cost-text'), '<a href="' . admin_url('admin.php?page=pmpro-advancedsettings') . '">' . __('Advanced Settings', 'pmpro-level-cost-text') . '.</a>');?></small>
            		</td>
        		</tr>
        	</td>
        </tr>
        <tr>
		<th scope="row" valign="top"><label for="level_cost_text"><label for="variable_references"><?php _e('Placeholder&nbsp;Reference', 'pmpro-level-cost-text'); ?>:</label></th>
            <td>
				<div class="template_reference" style="background: #FAFAFA; border: 1px solid #CCC; color: #666; padding: 5px;">
					<p><em><?php _e('Insert these variables in the level cost text field above.', 'pmpro-level-cost-text'); ?></em></p>
					<?php pclct_list_variables(); ?>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
	<?php
}
add_action("pmpro_discount_code_after_level_settings", "pclct_pmpro_discount_code_after_level_settings", 10, 2);

//save level cost text for the code when the code is saved/added
function pclct_pmpro_save_discount_code_level($code_id, $level_id)
{
	$all_levels_a = array_map( 'intval', $_REQUEST['all_levels'] );							//array of level ids checked for this code
	$level_cost_text_a = $_REQUEST['level_cost_text'];			//level_cost_text for levels checked
	
	if(!empty($all_levels_a))
	{
		$key = array_search($level_id, $all_levels_a);				//which level is it in the list?
		pmpro_saveCodeCustomLevelCostText($code_id, $level_id, wp_kses_post( wp_unslash( $level_cost_text_a[$key] ) ) );			//add level cost text for this level
	}
}
add_action("pmpro_save_discount_code_level", "pclct_pmpro_save_discount_code_level", apply_filters('pclct_pmpro_level_cost_text_priority', 99), 2);

//update level cost text based on the discount code used
function pclct_pmpro_level_cost_text_code($cost, $level)
{
	global $wpdb;
	
	//check if a discount code is being used
	if(!empty($level->code_id))
		$code_id = $level->code_id;
	elseif(!empty($_REQUEST['discount_code']))
		$code_id = $wpdb->get_var($wpdb->prepare( "SELECT id FROM $wpdb->pmpro_discount_codes WHERE code = %s LIMIT 1", $_REQUEST['discount_code'] ) );
	else
		$code_id = false;
	
	//used?
	if(!empty($code_id))
	{
		//we have a code						
		$level_cost_text = pmpro_getCodeCustomLevelCostText($code_id, $level->id);
		
		if(!empty($level_cost_text))
		{
			//return $level_cost_text;
			//$cost = $level_cost_text;
			$cost = pclct_apply_variables($level_cost_text, $cost, $level);
			return $cost;
		}
	}
	
	return $cost;
}
add_filter("pmpro_level_cost_text", "pclct_pmpro_level_cost_text_code", 20, 2);

/*	
	This function will save a level_cost_text for a discount code into an array stored in pmpro_code_level_cost_text.
*/
function pmpro_saveCodeCustomLevelCostText($code_id, $level_id, $level_cost_text)
{
	$all_level_cost_text = get_option("pmpro_code_level_cost_text", array());
	
	//make sure we have an array for the code
	if(empty($all_level_cost_text[$code_id]))
		$all_level_cost_text[$code_id] = array();
	
	$all_level_cost_text[$code_id][$level_id] = $level_cost_text;
	
	update_option("pmpro_code_level_cost_text", $all_level_cost_text);
}

/*
	This function will return the level cost text for a discount code/level combo
*/
function pmpro_getCodeCustomLevelCostText($code_id, $level_id)
{
	$all_level_cost_text = get_option("pmpro_code_level_cost_text", array());
	
	if(!empty($all_level_cost_text[$code_id]))
	{
		if(!empty($all_level_cost_text[$code_id][$level_id]))
			return $all_level_cost_text[$code_id][$level_id];
		}
	
	//didn't find it
	return "";
}

/**
 * Add links to the action links
 */
function plct_add_action_links($links) {
	$cap = apply_filters('pmpro_add_member_cap', 'edit_users');
	if(current_user_can($cap))
	{
		$new_links = array(
			'<a href="' . admin_url('admin.php?page=pmpro-advancedsettings#LevelCostText') . '" title="' . esc_attr(__('Go to Level Cost Text Advanced Settings', 'pmpro-level-cost-text')) . '">' . __('Settings', 'pmpro-level-cost-text') . '</a>',
		);
	}
	return array_merge($new_links, $links);
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'plct_add_action_links');

/**
 * Add links to the plugin row meta
 */
function pclct_plugin_row_meta($links, $file) {
	if(strpos($file, 'pmpro-level-cost-text.php') !== false)
	{
		$new_links = array(
			'<a href="' . esc_url('http://www.paidmembershipspro.com/add-ons/plugins-on-github/pmpro-custom-level-cost-text/')  . '" title="' . esc_attr( __( 'View Documentation', 'pmpro-level-cost-text' ) ) . '">' . __( 'Docs', 'pmpro-level-cost-text' ) . '</a>',
			'<a href="' . esc_url('http://paidmembershipspro.com/support/') . '" title="' . esc_attr( __( 'Visit Customer Support Forum', 'pmpro' ) ) . '">' . __( 'Support', 'pmpro-level-cost-text' ) . '</a>',
		);
		$links = array_merge($links, $new_links);
	}
	return $links;
}
add_filter('plugin_row_meta', 'pclct_plugin_row_meta', 10, 2);

function pclct_load_textdomain() {
	//get the locale
	$locale = apply_filters("plugin_locale", get_locale(), "pmpro-level-cost-text");
	$mofile = "pclct-" . $locale . ".mo";
	
	//paths to local (plugin) and global (WP) language files
	$mofile_local  = plugin_dir_path(__FILE__)."/languages/" . $mofile;
	$mofile_global = WP_LANG_DIR . '/pmpro/' . $mofile;
	
	//load global first
	load_textdomain("pmpro-level-cost-text", $mofile_global);
	
	//load local second
	load_textdomain("pmpro-level-cost-text", $mofile_local);
}
add_action("init", "pclct_load_textdomain", 1);
