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
class GenerateWP_Custom_REST_API_Endpoints {

    /**
     * Register the routes for the objects of the controller.
     */
    public static function register_endpoints() {
        // endpoints will be registered here
        register_rest_route( 'zliu/v1', '/index', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( 'GenerateWP_Custom_REST_API_Endpoints', 'get_index' ),
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
        $hot = GenerateWP_Custom_REST_API_Endpoints::_get_posts_by_tag('hot');
        $new = GenerateWP_Custom_REST_API_Endpoints::_get_posts_by_tag('new');
        // @TODO do your magic here
        return new WP_REST_Response( array('hot' => $hot, 'new' => $new), 200 );
    }
}
add_action( 'rest_api_init', array( 'GenerateWP_Custom_REST_API_Endpoints', 'register_endpoints' ) );
