<?php
/*
Plugin Name: Simplr User Registration Form	
Version: 0.1
Description: This a simple plugin for adding a custom user registration form to any post or page using shortcode.
Author: Mike Van Winkle
Author URI: http://www.mikevanwinkle.com
Plugin URI: http://www.mikevanwinkle.com/plugins/simplr-user-registration-page/
License: GPL
*/
?>
<?php
/*Version Check*/
global $wp_version;
$exit_msg = "Dude, upgrade your stinkin Wordpress Installation.";
if(version_compare($wp_version, "2.8", "<")) { exit($exit_msg); }

/*create admin page*/
function simplr_reg_admin() {
//Add posted options
if(isset($_POST['submit'])) {
	if(check_admin_referer('-1','sreg-nonce-test')) {
		//Add Options
		$options = array(
			"sreg_admin_email" => $_POST['sreg_admin_email'],
			"sreg_email" => $_POST['sreg_email'],
			"sreg_style" => $_POST['sreg_style']
			);
			foreach($options as $key => $value) {
					update_option($key,$value); 
			}//end foreach
		}//Close nonce check 
	}//Close POST conditional

//setup options form
global $options;
?>
<div class="wrap">
    <link href="simplr_reg_admin.css" rel="stylesheet" type="css/text">
	<?php screen_icon(); ?>
	<h2>Registration Form Settings</h2>
	<p>Use this page to configure your registration page settings. To deploy registration page simply use the shortcode [Register] in any page. You can also override the role of the registrant by using the parameter "role=". For instance [Register role="contributor"].</p>
	<form action="" method="post" id="simplr_reg_settings">
	<h3><label for="sreg_admin_email">FROM address for notification email</label></h3>
	<input type="text" name="sreg_admin_email" id="sreg_admin_email" value="<?php echo esc_attr(get_option('sreg_admin_email')); ?>" style="width:300px;"></input>
	<h3><label for="sreg_email">Confirmation Message</label></h3>
	<p><textarea name="sreg_email" id="sreg_email" style="width:300px;height:200px;"><?php echo esc_attr(get_option('sreg_email')); ?></textarea></p>
	<h3><label for="sreg_style">Stylesheet Override</label></h3>
	<small>Enter the URL of the stylesheet you would prefer to use. Leave blank to stick with default.</small>
	<p><input type="text" name="sreg_style" id="sreg_style" value="<?php echo esc_attr(get_option('sreg_style')); ?>" style="width:300px;"></input></p>
	<p class="submit"><input type="submit" name="submit" value="Update Options &raquo;"></p>
	<?php wp_nonce_field('-1','sreg-nonce-test'); ?>
	</form>
	</div>
<?php 

// End admin page function
}

function sreg_styles() {
global $options;
$src = get_bloginfo('siteurl').'/wp-content/plugins/simplr_reg_page/simplr_reg.css';
$style = '<link rel="stylesheet" type="text/css" media="screen,projection" href="' .$src .'" />';
return $style;
//End Function
}


//Register Menu Item for Admin Page
function simplr_reg_admin_page() {
	add_submenu_page('options-general.php','Registration Page Settings', 'Registration Page','manage_options','simplr_reg_page', 'simplr_reg_admin');
	}	

function sreg_process_form($role) {
	global $options;
	$admin_email = get_option('sreg_admin_email');
	$emessage = get_option('sreg_email');
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
				'role' => $role
				);
			$user_id = wp_insert_user( $userdata );
			$message = 'Registration Successful. A password was sent to you via email.';
			$message = '<div class="simplr-message">'.$message .'</div>';
			//add flag for the user to change their auto-generated password
			$update = update_user_option($user_id, 'default_password_nag', true, true);
			//notify admin of new user
			$notify = wp_new_user_notification($user_id, $random_pass);
			$site = get_option('siteurl');
			$name = get_option('blogname');
				if(empty($emessage)) {$emessage = "Thanks for signing up for $name. Here is your password. You should login and change it as soon as possible.\r\rUsername: $user_name\rPassword: $random_password\rLogin: $site";}
					$emessage = $emessage . "\r\r---\rYou should login and change it as soon as possible.\r\rUsername: $user_name\rPassword: $random_password\rLogin: $site";
					$mail_message = wp_mail($email, 'Registration Confirmaion', $emessage);
					echo "$message";
				} else { 
				//Print the appropriate message
				$message = '<div class="simplr-message">'.$message .'</div>';
				echo $message;
		}
//END FUNCTION
}

function sreg_basic($role) {
	//Check if the user is logged in, if so he doesn't need the registration page
	global $options;
	echo sreg_styles();
	$admin_email = get_option('admin_email');
	$emessage = get_option('sreg_email');
	if ( is_user_logged_in() ) {
		echo "You are already registered for this site!!!";
	} else {
		//Then I check to see whether a form has been submitted, if so, I deal with it.
		if(isset($_POST['submit'])) {
			sreg_process_form($role);	
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
			echo '<input type="submit" name="submit" value="Register" class="submit">';
			wp_nonce_field('-1','simplr-nonce' );
			echo '</form>';
			echo '</div>';
	} //Close POST Condiditonal
} //Close LOGIN Conditional

} //END FUNCTION


//this function determines which version of the registration to call
function sreg_figure($atts) {
	extract(shortcode_atts(array(
	'role' => 'subscriber',
	'advanced' => 'false',
	'fb' => 'false',
	), $atts));
if($role != 'admin') {
$function = sreg_basic($role);
} else { 
$function = 'You should not register admin users via a public form';
}
//End Function
}


add_action('admin_menu','simplr_reg_admin_page');
add_shortcode('Register', 'sreg_figure');

?>