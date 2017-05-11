<?php
/*
 * Plugin Name: rest api extensions
 * Plugin URI: http://wordpress.zliu.org/rest-ext.html
 * Description: funciton extensions for rest api.
 * Version: 1.0
 * Author: Zhanliang Liu
 * Author URI: http://zliu.org
*/

// Register REST API endpoints
class Liang_API_Endpoints {
    private static $base_url = "zliu/v1";

    /**
     * Register the routes for the objects of the controller.
     */
    public static function register_endpoints() {
        // endpoints will be registered here
        register_rest_route( Liang_API_Endpoints::$base_url, '/index', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( 'Liang_API_Endpoints', 'get_index' ),
        ) );
    }

    private static function _get_posts_by_tag($tag, $number = 4) {
        $data = get_posts(array(
            'tag' => $tag,
            'posts_per_page' => $number,
            'offset' => 0,
        ));
        $unset_fields = array('post_content', 'post_date_gmt', 'post_status', 'post_excerpt',
            'post_name', 'to_ping', 'pinged', 'post_modified_gmt', 'post_content_filtered',
            'post_password', 'post_parent', 'comment_count', 'comment_status', 'ping_status',
            'post_mime_type', 'filter', 'menu_order', 'post_type', 
        );
        foreach ($data as $post) {
            foreach ($unset_fields as $field) {
                unset($post->$field);
            }
            // $post->thumbnail = get_the_post_thumbnail($post->ID);
            $image_id = get_post_thumbnail_id($post->ID);
            $post->image = wp_get_attachment_image_src($image_id, 'large');
        }
        return $data;
    }

    /**
     * Get all the candies
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Request
     */
    public static function get_index($request) {
        $keys = array(
            array('hot', 'hot description'),
            array('new', 'new description'),
        );
        $data = array();

        foreach ($keys as $item) {
            $data[] = array(
                'tag' => $item[0],
                'description' => $item[1],
                'data' => Liang_API_Endpoints::_get_posts_by_tag($item[0]),
            );
        }

        return new WP_REST_Response($data, 200);
    }
}

function zliu_rest_prepare_user( WP_REST_Response $response, WP_User $user, WP_REST_Request $request ) {
    $userinfo = get_user_meta($user->ID);
    if ($userinfo && isset($userinfo['simple_local_avatar'])) {
        $u = unserialize($userinfo['simple_local_avatar'][0]);
        $data = $response->get_data();
        $data['avatar'] = $u;
        $response->set_data($data);
    }
    return $response;
}

function billing_link_function() {
    global $user_login;
    get_currentuserinfo();
    $return_string = '';
    if ($user_login) {
        $return_string .= "<h3>Pilih Paket</h3><hr /><p>Some Plan... </p>";
        $trxid = date('YmdHis') . "_" . $user_login . "_REG_";
        $return_string .= '<a class="btn-default" href="http://202.43.169.33/app/wap/fyeo/isat?trxid='.$trxid.'isat&type=REG">isat REG</a>&nbsp;';
        $return_string .= '<a class="btn-default" href="http://202.43.169.33/app/wap/fyeo/xl?trxid='.$trxid.'xl&type=REG">xl REG</a>';
    } else {
        $return_string = 'You need <a href="/login">login</a> to access.';
    }
    $return_string .= "<hr />";
    return $return_string;
}

function register_shortcodes() {
    add_shortcode('billing_link', 'billing_link_function');
}

add_action( 'init', 'register_shortcodes');
add_action( 'rest_api_init', array( 'Liang_API_Endpoints', 'register_endpoints' ) );
add_filter( 'rest_prepare_user', 'zliu_rest_prepare_user', 10, 3 );
