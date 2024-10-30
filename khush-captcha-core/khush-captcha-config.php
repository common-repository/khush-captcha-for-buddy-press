<?php
if (!defined('KHUSH_CAPTCHA_THEME_DIR'))
	define('KHUSH_CAPTCHA_THEME_DIR', ABSPATH . 'wp-content/themes/' . get_template());

if (!defined('KHUSH_CAPTCHA_PLUGIN_NAME'))
	define('KHUSH_CAPTCHA_PLUGIN_NAME', trim(dirname(plugin_basename(__FILE__)), '/'));

if (!defined('KHUSH_CAPTCHA_PLUGIN_DIR'))
	define('KHUSH_CAPTCHA_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . KHUSH_CAPTCHA_PLUGIN_NAME);

if (!defined('KHUSH_CAPTCHA_PLUGIN_URL'))
	define('KHUSH_CAPTCHA_PLUGIN_URL', WP_PLUGIN_URL . '/' . KHUSH_CAPTCHA_PLUGIN_NAME);
?>