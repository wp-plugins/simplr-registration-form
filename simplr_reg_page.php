<?php
/*
Plugin Name: Simplr User Registration Form	- BETA
Version: 0.1.5
Description: This a simple plugin for adding a custom user registration form to any post or page using shortcode.
Author: Mike Van Winkle
Author URI: http://www.mikevanwinkle.com
Plugin URI: http://www.mikevanwinkle.com/wordpress/how-to/custom-wordpress-registration-page/
License: GPL
*/
?>
<?php
/*Version Check*/
global $wp_version;
$exit_msg = "Dude, upgrade your stinkin Wordpress Installation.";
if(version_compare($wp_version, "2.8", "<")) { exit($exit_msg); }

define("SIMPLR_DIR", WP_PLUGIN_URL . '/simplr-registration-form/' );

function simplr_reg_set() {
$profile_fields = get_option('simplr_profile_fields');
?>
	<div class="wrap">
		  <h2>Registration Form Settings</h2>
			  <form method="post" action="options.php" id="simplr-settings">
				  <table class="form-table">
				  <tr valign="top">
				  <th scope="row">Set Default FROM Email:</th>
				  <td><input type="text" name="sreg_admin_email" value="<?php echo get_option('sreg_admin_email'); ?>" class="field text wide"/></td>
				  </tr>
				  <tr valign="top">
				  <th scope="row">Set Defult Confirmation Message:<br>
				  <small></small>
				  </th>
				  <td>
				  <textarea id="sreg_email" name="sreg_email" style="width:500px;height:200px; padding:3px;" class="sreg_email"><?php echo get_option('sreg_email'); ?></textarea></td>
				  </tr>
				  <tr valign="top">
				  <th scope="row">Stylesheet Override</th>
				  <td>
				  <input type="text" name="sreg_style" value="<?php echo get_option('sreg_style'); ?>" class="field text wide" />
				  <p><small>Enter the URL of the stylesheet you would prefer to use. Leave blank to stick with default.</small></p>
				  </td>
				  </tr>
				   <tr valign="top">
				  <th scope="row">Profile Fields<br />
				    <small>Here you can setup default fields to include in your registration form. These can be overwritten on a form by form basis. </small>
				  </th>
						<td>
						<div class="left"><label for="aim">AIM</label></div>
						<div class="right">
						<input type="checkbox" name="simplr_profile_fields[aim][name]" value="aim" class="field checkbox" <?php $aim = $profile_fields[aim]; if($aim[name] == true) { echo "checked";} ?>>  
						Label: <input type="text" name="simplr_profile_fields[aim][label]" value="<?php echo $aim[label]; ?>" /><br/></div>
						<div class="left"><label for="aim">Yahoo ID</label></div>
						<div class="right">
						<input type="checkbox" name="simplr_profile_fields[yim][name]" value="yim" class="field checkbox" <?php $yim = $profile_fields[yim]; if($yim[name] == true) { echo "checked";} ?>>  
						Label: <input type="text" name="simplr_profile_fields[yim][label]" value="<?php echo $yim[label]; ?>" /><br/></div>
						<div class="left"><label for="aim">Website</label></div>
						<div class="right">
						<input type="checkbox" name="simplr_profile_fields[url][name]" value="url" class="field checkbox" <?php $url = $profile_fields[url]; if($url[name] == true) { echo "checked";} ?>>  
						Label: <input type="text" name="simplr_profile_fields[url][label]" value="<?php echo $url[label]; ?>" /><br/></div>
						<div class="left"><label for="aim">Nickname</label></div>
						<div class="right">
						<input type="checkbox" name="simplr_profile_fields[nickname][name]" value="nickname" class="field checkbox" <?php $nickname = $profile_fields[nickname]; if($nickname[name] == true) { echo "checked";} ?>>  
						Label: <input type="text" name="simplr_profile_fields[nickname][label]" value="<?php echo $nickname[label]; ?>" /><br/></div>
						</td>
				  </tr>
				  </table>
			  <?php settings_fields('simplr_reg_options'); ?>
			  <p class="submit">
			  <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			  </p>
			  </form>
		<div id="instructions">
			<h2>How to use</h2>
			<p>The goal of this plugin is to give developers and advanced Wordpress users a simple way to create role-specific registration forms for their wordpress website. For instance, you might be running an education based site in which you wanted both teachers and students to particape. This plugin enables you to create distinct registration forms for each type of registrant.</p>
			<p>Because the focus is on seperating registrants, I have not focused on creating a highly customizable form like <a href="http://wordpress.org/extend/plugins/register-plus/" title="Register Plus">Register Plus</a>. </p>
			<h3>Usage</h3>
			<p>To use this plugin simplr employ the shortcode <code>[Register]</code> on any Wordpress post or page. The default role is "subscriber". To apply another role to the registration simply use the the role parameter, for instance: <code>[Regsiter role="editor"]</code>. If you have created custom roles you may use them as well. </p>
			<p>You can also use shortcode so specify a custom confirmation message for each form: <br>
			<code>[Register role="teacher" <b>message="Thank you for registering for my site. If you would like to encourage your students to register, please direct them to http://www.domain.com/students"</b>]</code></p>
			<p>Finally, you can specify emails to be notified when a new user is registered. By defualt site admins will receive notice but to notify other simply use the notify parameter:
			<code>[Register role="teacher" message="Thank you for registering for my site. If you would like to encourage your students to register, please direct them to http://www.domain.com/students" <b>notify="email1@email.com,email2@email.com"</b>]</code>
		<p>
		</div>
	  </div>
  <?php
}//End Function

function simplr_reg_menu() {
	$page = add_submenu_page('options-general.php','Registration Forms', 'Registration Forms','manage_options','simplr_reg_set', 'simplr_reg_set');
	add_action('admin_print_styles-' . $page, 'simplr_admin_style');
	register_setting ('simplr_reg_options', 'sreg_admin_email', '');
	register_setting ('simplr_reg_options', 'sreg_email', '');
	register_setting ('simplr_reg_options', 'sreg_style', '');
	register_setting ('simplr_reg_options', 'simplr_profile_fields', 'simplr_fields_settings_process');
}

function simplr_fields_settings_process($input) {
	if($input[aim][name] && $input[aim][label] == '') {$input[aim][label] = 'AIM';}
	if($input[yim][name] && $input[yim][label] == '') {$input[yim][label] = 'YIM';}
	if($input[website][name] && $input[website][label] == '') {$input[website][label] = 'Website';}	
	if($input[nickname][name] && $input[nickname][label] == '') {$input[nickname][label] = 'Nickname';}
	return $input;
}

function simplr_reg_styles() {
	global $options;
	$style = get_option('sreg_style');
	if(!$style) {
		$src = SIMPLR_DIR .'simplr_reg.css';
		wp_register_style('simplr-forms-style',$src);
		wp_enqueue_style('simplr-forms-style');
	} else {
		$src = $style;
		wp_register_style('simplr-forms-custom-style',$src);
		wp_enqueue_style('simplr-forms-custom-style');
	}
//End Function
}

function simplr_admin_style() {
	$src = SIMPLR_DIR . 'simplr_admin.css';
	wp_register_style('simplr-admin-style',$src); 
	wp_enqueue_style('simplr-admin-style');
}

//Register Menu Item for Admin Page
function simplr_reg_admin_page() {
	add_submenu_page('options-general.php','Registration Page Settings', 'Registration Page','manage_options','simplr_reg_page', 'simplr_reg_admin');
	}	


function simplr_validate($data) {
	require_once(ABSPATH . WPINC . '/registration.php' );
	require_once(ABSPATH . WPINC . '/pluggable.php' );
	$errors = '';
	if(!$data['username']) { $errors = "You must enter a username."; } 
		else {
			// check whether username is valid
			$user_test = validate_username($data['username']);
				if($user_test != true) {
						$errors .= 'Invalid Username';
					}
			// check whether username already exists
			$user_id = username_exists( $data['username'] );
				if($user_id) {
						$errors .= 'This username already exists';
					}
		}	//end user validation
	if(!$data['email']) { $errors = "You must enter an email."; } 	
	else {
		$email_test = email_exists($data['email']);
		if($email_test != false) {
				$errors .= 'An account with this email has already been registered';
			}	
		} // end email validation
	$errors = apply_filters('simplr_validate_form', $errors); 
	return $errors;
}

function sreg_process_form($atts) {
	//security check
	
	if (!wp_verify_nonce($_POST['simplr_nonce'], 'simplr_nonce') ) { die('Security check'); }

	$errors = simplr_validate($_POST);
	if( $errors ==  true ) :
		 $message = $errors;
	endif; 
	
	if (!$message) {
				
			//check options
				global $options;
				$admin_email = $atts['from'];
				$emessage = $atts['message'];
				$role = $atts['role']; 
					if($role == '') { $role = 'subscriber'; }
				require_once(ABSPATH . WPINC . '/registration.php' );
				require_once(ABSPATH . WPINC . '/pluggable.php' );
				
			//Assign POST variables
				$user_name = $_POST['username'];
				$fname = $_POST['fname'];
				$lname = $_POST['lname'];
				$user_name = sanitize_user($user_name, true);
				$email = $_POST['email'];
				$user_url = $_POST['url'];
		
		
			//This part actually generates the account
				$random_password = wp_generate_password( 12, false );
				$userdata = array(
					'user_login' => $user_name,
					'first_name' => $fname,
					'last_name' => $lname,
					'user_pass' => $random_password,
					'user_email' => $email,
					'user_url' => $user_url,
					'role' => $role
					);
				$user_id = wp_insert_user( $userdata );
					if(WP_MULTISITE === true) { 
						global $wpdb;
						$ip = getenv('REMOTE_ADDR');
						$site = get_current_site();
						$sid = $site->id;
						$query = $wpdb->prepare("
							INSERT INTO $wpdb->registration_log
							(ID, email, IP, blog_ID, date_registered)
							VALUES ($user_id, $email, $ip, $sid, NOW() )
							");
						$results = $wpdb->query($query);
					}
				
			//Process additional fields
			$pro_fields = get_option('simplr_profile_fields');
			if($pro_fields) {
					foreach($pro_fields as $field) {
					$key = $field['name'];
					$val = $_POST[$key];
						if(isset( $val )) { add_user_meta($user_id,$key,$val); }
					}
			}
			
			//Do Meta Hook
				do_action('simplr_profile_save_meta', $user_id);

			//Set Message
				$message = 'Registration Successful. A password was sent to you via email.';
				$message = '<div class="simplr-message">'.$message .'</div>';
				
			//add flag for the user to change their auto-generated password
				$update = update_user_option($user_id, 'default_password_nag', true, true);
				
			//notify admin of new user
				$notification = wp_new_user_notification($user_id, $random_pass); 
				$site = get_option('siteurl');
				$name = get_option('blogname');
				$notify = $atts['notify'];
				mail($notify, "New User Registered for $name", "A new user has registered for $name.\rUsername: $user_name\r Email: $email \r");
				$emessage = $emessage . "\r\r---\rYou should login and change your password as soon as possible.\r\rUsername: $user_name\rPassword: $random_password\rLogin: $site/wp-login.php";
				$mail_message = wp_mail($email, "$name - Registration Confirmaion", $emessage);
				$confirm = '<div class="simplr-message">Your Registration was successful, please check your email for confirmation</div>';
				return $confirm;
			} else { 
			
			
		//Print the appropriate message
		$message = '<div class="simplr-message">'.$message .'</div>';
		$form = simplr_build_form($_POST);
		$output = $message . $form;
		return $output;
	}
//END FUNCTION
}

function simplr_build_form($data) {
			
			$label_first = apply_filters('simplr_label_fname', 'First Name:' );
			$label_last = apply_filters('simplr_label_lname', 'Last Name:' );
			$label_email = apply_filters('simplr_label_email', 'Email Address:' );
			$label_username = apply_filters('simplr_label_username', 'Your Username:' );
			
			//POST FORM
			$form = '';
			$form .=  '<div id="simplr-form">';
			$form .=  '<form method="post" action="" id="simplr-reg">';
			$form .=  '<div class="simplr-field">';
			$form .=  '<label for="username" class="left">' .$label_username .' <span class="required">*</span></label>';
			$form .=  '<input type="text" name="username" class="right" value="'.$data['username'] .'" /><br/>';
			$form .=  '</div>';
			$form .=  '<div class="simplr-field">';
			$form .=  '<label for="email" class="left">' .$label_email .' <span class="required">*</span></label>';
			$form .=  '<input type="text" name="email" class="right" value="'.$data['email'] .'" /><br/>';
			$form .=  '</div>';
			$form .=  '<div class="simplr-field">';
			$form .=  '<label for="fname" class="left">'.$label_first .'</label>';
			$form .=  '<input type="text" name="fname" class="right" value="'.$data['fname'] .'" /><br/>';
			$form .=  '</div>';
			$form .=  '<div class="simplr-field">';
			$form .=  '<label for="lname" class="left">' .$label_last .'</label>';
			$form .=  '<input type="text" name="lname" class="right" value="'.$data['lname'] .'"/><br/>';
			$form .=  '</div>';

			//hook for adding profile fields
			$form = apply_filters('simplr_add_form_fields', $form);
													
			//optional profile fields
			$pro_fields = get_option('simplr_profile_fields');
			if($pro_fields) {
					foreach($pro_fields as $field) {
							if($field[name] != '') {
						$form .= '<div class="simplr-field"><label for="' .$field[name] .'" class="left">'.$field[label] .'</label><input type="text" name="'.$field[name] .'" value="'.$data[$field[name]] .'" class="text" /></div>';
						}
					}
				}
			
				
				 
			//submission field
			$form .=  '<input type="submit" name="submit-reg" value="Register" class="submit">';
			
			//wordress nonce for security
			$nonce = wp_create_nonce('simplr_nonce');
			$form .= '<input type="hidden" name="simplr_nonce" value="' .$nonce .'" />';
			$form .=  '</form>';
			$form .=  '</div>';
			return $form;
}

function sreg_basic($atts) {
	//Check if the user is logged in, if so he doesn't need the registration page
		if ( is_user_logged_in() ) {
			echo "You are already registered for this site!!!";
		} else {
		//Then check to see whether a form has been submitted, if so, I deal with it.
		if(isset($_POST['submit-reg'])) {
			$output = sreg_process_form($atts);	
			return $output;
		} else {
			$data = array();
			$form = simplr_build_form($data);		
		return $form;				
	} //Close POST Condiditonal
} //Close LOGIN Conditional

} //END FUNCTION


//this function determines which version of the registration to call
function sreg_figure($atts) {
	global $options;
	extract(shortcode_atts(array(
	'role' => 'subscriber',
	'from' => get_option('sreg_admin_email'),
	'message' => get_option('sreg_email'),
	'notify' => get_option('sreg_email'),
	'fb' => false,
	), $atts));
		if($role != 'admin') {
			$function = sreg_basic($atts);
		} else { 
			$function = 'You should not register admin users via a public form';
		}
	return $function;
}//End Function


add_action('wp_print_styles','simplr_reg_styles');
add_action('admin_menu','simplr_reg_menu');
add_shortcode('Register', 'sreg_figure');

?>