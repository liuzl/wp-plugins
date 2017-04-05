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

    /**
     * Get all the candies
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Request
     */
    public static function get_index($request) {
        $data = get_posts(array(
            'tag' => 'hot',
            'posts_per_page' => 4,
            'offset' => 0,
        ));
        foreach ($data as $post) {
            unset($post->post_content);
            $post->thumbnail = get_the_post_thumbnail($post->ID);
        }

        // @TODO do your magic here
        return new WP_REST_Response( array('hot' => $data), 200 );
    }
}
add_action( 'rest_api_init', array( 'GenerateWP_Custom_REST_API_Endpoints', 'register_endpoints' ) );
