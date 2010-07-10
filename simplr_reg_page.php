<?php
/*
Plugin Name: Simplr User Registration Form	
Version: 0.1.2
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

function simplr_reg_set() { ?>
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

function sreg_process_form($atts) {
		global $options;
		$admin_email = $atts['from'];
		$emessage = $atts['message'];
		require_once(ABSPATH . WPINC . '/registration.php' );
		require_once(ABSPATH . WPINC . '/pluggable.php' );
		//Assign POST variables
		$user_name = $_POST['username'];
		$fname = $_POST['fname'];
		$lname = $_POST['lname'];
		$user_name = sanitize_user($user_name, true);
		$email = $_POST['email'];
		//Test variables using built in wordpress functions.
		$user_test = validate_username($user_name);
			if($user_test != true) {
				$message = 'Invalid Username';
				}
			$email_test = email_exists($email);
			if($email_test != false) {
					$message = 'An account with this email has already been registered';
				}
			$user_id = username_exists( $user_name );
			if($user_id) {
				$message = 'This username already exists';
			}
			if (!$message) {
			//This part actually generates the account
				$random_password = wp_generate_password( 12, false );
				$userdata = array(
					'user_login' => $user_name,
					'first_name' => $fname,
					'last_name' => $lname,
					'user_pass' => $random_password,
					'user_email' => $email,
					'role' => $atts['role']
					);
				$user_id = wp_insert_user( $userdata );
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
				$emessage = $emessage . "\r\r---\rYou should login and change your password as soon as possible.\r\rUsername: $user_name\rPassword: $random_password\rLogin: $site";
				$mail_message = wp_mail($email, "$name - Registration Confirmaion", $emessage);
				echo "Your Registration was successful, please check your email for confirmation";
			} else { 
			//Print the appropriate message
			$message = '<div class="simplr-message">'.$message .'</div>';
			echo $message;
			}
//END FUNCTION
}

function sreg_basic($atts) {
	//Check if the user is logged in, if so he doesn't need the registration page
		if ( is_user_logged_in() ) {
			echo "You are already registered for this site!!!";
		} else {
		//Then check to see whether a form has been submitted, if so, I deal with it.
		if(isset($_POST['submit-reg'])) {
			sreg_process_form($atts);	
		} else {
			//POST FORM
			echo '<div id="simplr-form">';
			echo '<form method="post" action="">';
			echo '<div class="simplr-field">';
			echo '<label for="fname" class="left">First Name:</label>';
			echo '<input type="text" name="fname" class="right"><br/>';
			echo '</div>';
			echo '<div class="simplr-field">';
			echo '<label for="lname" class="left">Last Name:</label>';
			echo '<input type="text" name="lname" class="right"><br/>';
			echo '</div>';
			echo '<div class="simplr-field">';
			echo '<label for="username" class="left">Choose a username:</label>';
			echo '<input type="text" name="username" class="right"><br/>';
			echo '</div>';
			echo '<div class="simplr-field">';
			echo '<label for="email" class="left">Enter your email:</label>';
			echo '<input type="text" name="email" class="right"><br/>';
			echo '</div>';
			echo '<input type="submit" name="submit-reg" value="Register" class="submit">';
			wp_nonce_field('-1','simplr-nonce' );
			echo '</form>';
			echo '</div>';
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
}//End Function


add_action('wp_print_styles','simplr_reg_styles');
add_action('admin_menu','simplr_reg_menu');
add_shortcode('Register', 'sreg_figure');

?>