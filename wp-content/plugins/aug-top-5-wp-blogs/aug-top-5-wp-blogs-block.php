<?php
/**
 * Plugin Name: August top 5 wordpress blogs
 * Description: This plugin is created gutenberg top 5 blogs block
 * Author: Mayur Uttekar
 * Author URI: https://www.linkedin.com/in/mayur-uttekar-b96a4773/
 * Version: 1.0
 */

 // This action is use to initialize the register block
 add_action( 'init', 'top5blogs_register_block' );
 function top5blogs_register_block() {

    // Auto load built assets
    wp_register_script(
        'top5blogs-block-editor',
        plugins_url( 'build/index.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-data' ),
        filemtime( plugin_dir_path( __FILE__ ) . 'build/index.js' )
    );

    // Register your gutenberg block here
    register_block_type( 'top5blogs/block', array(
        'editor_script' => 'top5blogs-block-editor',
        'render_callback' => 'top5blogs_render_callback',
        'attributes' => array(
            'order' => array(
                'type' => 'string',
                'default' => 'DESC'
            ),
            'orderBy' => array(
                'type' => 'string',
                'default' => 'date'
            ),
            'numberOfPosts' => array(
                'type' => 'number',
                'default' => 5
            ),
        ),
    ));
}

// This is the render function of register block
function top5blogs_render_callback( $attributes ) {
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => $attributes['numberOfPosts'],
        'order' => $attributes['order'],
        'orderby' => $attributes['orderBy'] === 'name' ? 'title' : 'date',
    );

    $query = new WP_Query( $args );

    if ( ! $query->have_posts() ) {
        return '<p>No posts found.</p>';
    }

    $output = '<div class="top5blogs-grid">';

    while ( $query->have_posts() ) {
        $query->the_post();
        // We pass 300x240 size image as is in requirement
        $image = get_the_post_thumbnail( get_the_ID(), array(300, 240) );
        $title = get_the_title();
        $link = get_permalink();
        $excerpt = get_the_excerpt();

        $output .= '<div class="top5blogs-item">';
        $output .= '<a href="' . $link . '">' . $image . '</a>';
        $output .= '<h3><a href="' . $link . '">' . $title . '</a></h3>';
        $output .= '<p>' . $excerpt . '</p>';
        $output .= '</div>';
    }

    $output .= '</div>';

    wp_reset_postdata();

    return $output;
}

// Enqueue your custom styles
add_action( 'enqueue_block_assets', function() {
    wp_enqueue_style( 'top5blogs-style', plugins_url( 'style.css', __FILE__ ), array(), filemtime( plugin_dir_path( __FILE__ ) . 'style.css' ));
});