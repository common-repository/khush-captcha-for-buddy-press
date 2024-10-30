<?php
/*
Plugin Name: Khush Captcha For Buddy Press
Plugin URI: http://khushapps.com
Description: This plugin adds a new form of captcha to your registration and comment forms. 
Use this plugin to provide an "are you human" check on your site. 
Version: 0.11
Author: Deric Johnson
Author URI: http://khushapps.com
License: GPL2

  Copyright 2013  khushapps.com : khushapps@gmail.com

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.q

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
if(!session_id()) {
        session_start();
}

		// call check_validation class
		//add_action( 'bp_after_signup_profile_fields', array( __CLASS__, 'khushcap_register_form' ) );
		add_action( 'bp_after_signup_profile_fields', 'khush_captcha_register_form' );
		
		// check_validation will compare the users captcha anaswer to the current session captch value
		// if fail, sets error in BuddyPress, if success, continue
		// Add Captcha To Registration Form
		// add_action( 'bp_signup_validate', array( __CLASS__, 'khush_captcha_register_post' ) );
		add_action( 'bp_signup_validate', 'khush_captcha_register_post', 10, 3 );

		// add_action( 'register_form', 'khushcap_register_form' );
		// add_action( 'register_post', 'khushcap_register_post', 10, 3 );
		// Add Captcha To Comments Form
		//add_action('comment_form_after_fields', 'khush_captcha_comments', 1);
		//add_filter('preprocess_comment', 'khush_captcha_check_comment');

	//edit this file to change error messages
	include_once 'khush-captcha-core/khush-captcha-config.php';
	include_once 'khush-captcha-core/khush-captcha-class.php';
	$khushCaptchaClass = new khushCaptcha();
	
 /**
 * Action: init
 */
	function ap_action_init()
	{
		// Localization
		load_plugin_textdomain('khush-captcha', false, dirname(plugin_basename(__FILE__)) . '/languages');
	}
	
	// Add actions
	add_action('init', 'ap_action_init');

	$random_color_num = rand(0,3);
	switch ($random_color_num)
	{
		case 1:
			$_SESSION['color_text'] = 'red';
			break;
		case 2:
			$_SESSION['color_text'] = 'blue';
			break;
		case 3:
			$_SESSION['color_text'] = 'green';
			break;
		default:
			$_SESSION['color_text'] = 'red';
	}

	// this function adds the captcha to the register form
	function khush_captcha_register_form() {
		global $khushCaptchaClass;
		
		if (isset($_POST['captcha_answer']) && $_SESSION['local_error'] == 'error') {
			echo "<b style='color:red'>Captcha In-Correct: Please Try Again</b>";
		} else {
			$_SESSION['local_error'] = '';
		}
		echo '<img src="' . WP_PLUGIN_URL . '/khush-captcha-for-buddy-press/khush-captcha-core/captcha-gen.php" ><br />';
		$color_str = ucfirst($_SESSION['color_text']);
		printf(__('Please Type The %s Captcha Characters Below.', 'khush-captcha'), $color_str);
	?>	
		        <br />
		        <input type="text" autocomplete="off" name="captcha_answer" value=""  style="margin-bottom:0;display:inline;font-size: 14px; width:200px; margin-top:3px;" required />
	<?php
		return true;
	}// end khushcap_register_form

	function khush_captcha_register_post(){
		global $bp;
		global $khushCaptchaClass;
	
		$captcha_blank = get_option('khush_captcha_blank');
		$captcha_error = get_option('khush_captcha_error');

		$answer = $_REQUEST['captcha_answer'];
		$_SESSION['local_error'] = '';

		if (isset($answer) && $answer == ""){
			
			$bp->signup->errors['security_check'] = __('This is a required field','buddypress');
			
		} elseif ( !empty( $answer ) ) {
			
			if ($khushCaptchaClass -> checkKhushCaptcha($answer)) {
				// captcha was matched
				$_SESSION['local_error'] = '';
			} else {
				/* The submitted answer was incorrect */
				$bp->signup->errors['security_check'] = __('Sorry, please answer the question again','buddypress');
				$_SESSION['local_error'] = 'error';
			}
			
		}
	}
	
	//add comment form to the comment form
	function khush_captcha_comments() {
		global $KhushCaptchaClass;
		if (is_user_logged_in()) {
			return true;
		}
		
		if ($local_error = 'error') {
			echo "Captcha In-Correct, Please Try Again";
			exit();
		}
	
		echo '<img src="' . WP_PLUGIN_URL . '/khush-captcha-for-buddy-press/khush-captcha-core/captcha-gen.php" ><br />';
		$color_str = ucfirst($_SESSION['color_text']);
		printf(__('Type The %s Captcha Characters Below.', 'khush-captcha'), $color_str);
	?>	
		        <br />
		        <input type="text" autocomplete="off" name="captcha_answer" value=""  style="margin-bottom:0;display:inline;font-size: 14px; width:200px; margin-top:3px;" required />
	<?php
		return true;
	}
	
	// this function checks comments captcha
	function khush_captcha_check_comment($comment) {

		global $khushCaptchaClass;
		
		$captcha_blank = get_option('khush_captcha_blank');
		$captcha_error = get_option('khush_captcha_error');
		
		if (is_user_logged_in()) {
			return $comment;
		}
		
		// for compatibility with WP Wall plugin(no support for WP Wall plugin)
		// it prevents errors when submitting a WP Wall comment
		if (function_exists('WPWall_Widget') && isset($_REQUEST['wpwall_comment'])) {
			return $comment;
		}
	
		// skip captcha for pingback and trackback
		if ($comment['comment_type'] != '' && $comment['comment_type'] != 'comment') {
			return $comment;
		}
	
		// skip captcha for comment replies from the admin menu
		if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'replyto-comment' && (check_ajax_referer('replyto-comment', '_ajax_nonce', false) || check_ajax_referer('replyto-comment', '_ajax_nonce-replyto-comment', false))) {
			return $comment;
		}
		
		// If captcha is empty
		if (isset($_REQUEST['captcha_answer']) && "" == $_REQUEST['captcha_answer'])
			wp_die(__('<strong>' . $captcha_blank . '</strong> ', 'captcha'));
	
		if ($khushCaptchaClass -> checkKhushCaptcha($_REQUEST['captcha_answer'])) {
			return ($comment);
		} else {
			wp_die(__('<strong>' . $captcha_error . '</strong> ', 'captcha'));
		}
		
	}	

	/* Runs when plugin is activated */
	register_activation_hook(__FILE__,'khush_captcha_blank_install');
	/* Runs on plugin deactivation*/
	register_deactivation_hook( __FILE__, 'khush_captcha_blank_remove' );
	/* Runs when plugin is activated */
	register_activation_hook(__FILE__,'khush_captcha_error_install');
	/* Runs on plugin deactivation*/
	register_deactivation_hook( __FILE__, 'khush_captcha_error_remove' );

	function khush_captcha_blank_install() {
		/* Creates new database field */
		add_option("khush_captcha_blank", 'Please Answer Captcha Question To Register.', '', 'yes');
	}
	function khush_captcha_blank_remove() {
		/* Deletes the database field */
		delete_option('khush_captcha_blank');
	}
	function khush_captcha_error_install() {
		/* Creates new database field */
		add_option("khush_captcha_error", 'Captcha answer incorrect, please try again.', '', 'yes');
	}
	function khush_captcha_error_remove() {
		/* Deletes the database field */
		delete_option('khush_captcha_error');
	}

	if ( is_admin() ){
	
		/* Call the html code */
		add_action('admin_menu', 'khush_captcha_admin_menu');
	
		function khush_captcha_admin_menu() {
			add_options_page('Khush Captcha', 'Khush Captcha', 'administrator',
					'khush-captcha', 'khush_captcha_admin_html');
		}
	}

function khush_captcha_admin_html() {
?>
	<div>
	<?php echo '<h2>' . __('Khush Captcha Options', 'khush-captcha') . '</h2>'; ?>
	<form method="post" action="options.php">
	<?php wp_nonce_field('update-options'); ?>
	<table width="700">
		<tr valign="top">
		<th width="120" scope="row" style="text-align:left;"><?php _e('Khush Captcha Blank Text Message', 'khush-captcha'); ?></th>
			<td width="400">
			<input name="khush_captcha_blank" type="text" id="khush_captcha_blank" value="<?php echo get_option('khush_captcha_blank'); ?>" style="width:400px;" />
			</td>
		</tr>
		<tr valign="top">
		<th width="120" scope="row" style="text-align:left;"><?php _e('Khush Captcha Error Text Message', 'khush-captcha'); ?></th>
			<td width="200">
			<input name="khush_captcha_error" type="text" id="khush_captcha_error" value="<?php echo get_option('khush_captcha_error'); ?>" style="width:400px;" />
			</td>
		</tr>
	</table>
		<input type="hidden" name="action" value="update" />
		<input type="hidden" name="page_options" value="khush_captcha_blank,khush_captcha_error" />
		<p>
			<input type="submit" value="<?php _e('Save Changes') ?>" />
		</p>
	</form>
	</div>
<?php
}
?>
