<?php
/*
 * Plugin Name: Mobile Login
 * Plugin URI: http://wordpress.zliu.org/mobile-login.html
 * Description: Let user register and login WordPress by mobile phone number.
 * Version: 1.0
 * Author: Zhanliang Liu
 * Author URI: http://zliu.org
*/

add_action('init', 'ugp_textdomain');
function ugp_textdomain() {
    load_plugin_textdomain('ml-domain', false, 'mobile-login');
}
add_action( 'register_form', 'ugp_show_extra_register_fields' );
function ugp_show_extra_register_fields(){
    ?>
    <p>
    <label for="password"><?php _e( 'Password', 'ml-domain' );?><br/>
    <input id="password" class="input" type="password" tabindex="30" size="25" value="" name="password" />
    </label>
    </p>
    <p>
    <label for="repeat_password"><?php _e( 'Repeat password', 'ml-domain' );?><br/>
    <input id="repeat_password" class="input" type="password" tabindex="40" size="25" value="" name="repeat_password" />
    </label>
    </p>
    <?php
}

/*
 * Check the form for errors
 */
add_action( 'register_post', 'ugp_check_extra_register_fields', 10, 3 );
function ugp_check_extra_register_fields($login, $email, $errors) {
    if ( $_POST['password'] !== $_POST['repeat_password'] ) {
        $errors->add( 'passwords_not_matched', __("<strong>ERROR</strong>: Passwords must match", 'ml-domain' ) );
    }
    if ( strlen( $_POST['password'] ) < 8 ) {
        $errors->add( 'password_too_short', __("<strong>ERROR</strong>: Passwords must be at least eight characters long", 'ml-domain' ) );
    }
}

/*
 * Storing WordPress user-selected password into database on registration
 */

add_action( 'user_register', 'ugp_register_extra_fields', 100 );
function ugp_register_extra_fields( $user_id ){
    $userdata = array();
    
    $userdata['ID'] = $user_id;
    if ( $_POST['password'] !== '' ) {
        $userdata['user_pass'] = $_POST['password'];
    }
    $new_user_id = wp_update_user( $userdata );
}

/*
 * Editing WordPress registration confirmation message
 */

add_filter( 'gettext', 'ugp_edit_password_email_text',20, 3 );
function ugp_edit_password_email_text ( $translated_text, $untranslated_text, $domain ) {
    if(in_array($GLOBALS['pagenow'], array('wp-login.php'))){
        if ( $untranslated_text == 'A password will be e-mailed to you.' ) {
            $translated_text = __( 'If you leave password fields empty one will be generated for you. Password must be at least eight characters long.', 'ml-domain' );
        }
        if( $untranslated_text == 'Registration complete. Please check your e-mail.' ) {
            $translated_text = __( 'Registration complete. Please sign in or check your e-mail.', 'ml-domain' );
        }
    }
    return $translated_text;
}
?>
