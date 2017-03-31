<?php
/*
 * Plugin Name: Disable Annoying Admin Bar
 * Plugin URI: http://wordpress.zliu.org/disable-admin-bar.html
 * Description: Remove the annoying admin bar of login users.
 * Version: 1.0
 * Author: Zhanliang Liu
 * Author URI: http://zliu.org
*/
show_admin_bar(false);
add_action('admin_init', 'no_mo_dashboard');
function no_mo_dashboard() {
  if (!current_user_can('manage_options') && $_SERVER['DOING_AJAX'] != '/wp-admin/admin-ajax.php') {
  wp_redirect(home_url()); exit;
  }
}
