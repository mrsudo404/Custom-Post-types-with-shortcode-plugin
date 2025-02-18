<?php
/**
 * Plugin Name: Custom Post Types - Recipe Roasts & Meal Prep Videos
 * Description: Adds custom post types for Recipe Roasts and Meal Prep Videos with categories, tags, featured images, and Gutenberg support.
 * Version: 1.0.0
 * Author: Waqas Khan
 * Plugin URI: https://waqaskhan.com.pk
 * License: GPL2
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Register Custom Post Types
function custom_recipe_meal_post_types() {
    // Recipe Roasts Post Type
    register_post_type('recipe_roasts',
        array(
            'labels'      => array(
                'name'          => __('Recipe Roasts'),
                'singular_name' => __('Recipe Roast'),
                'add_new'       => __('Add New Recipe Roast'),
                'add_new_item'  => __('Add New Recipe Roast'),
                'edit_item'     => __('Edit Recipe Roast'),
                'new_item'      => __('New Recipe Roast'),
                'view_item'     => __('View Recipe Roast'),
                'search_items'  => __('Search Recipe Roasts'),
                'not_found'     => __('No Recipe Roasts found'),
                'not_found_in_trash' => __('No Recipe Roasts found in Trash'),
            ),
            'public'      => true,
            'has_archive' => true,
            'supports'    => array('title', 'editor', 'thumbnail', 'excerpt', 'comments', 'author', 'custom-fields', 'revisions'),
            'taxonomies'  => array('category', 'post_tag'),
            'show_in_rest' => true,
            'rewrite'     => array('slug' => 'recipe-roasts'),
            'menu_icon'   => 'dashicons-video-alt3'
        )
    );

    // Meal Prep Videos Post Type
    register_post_type('meal_prep_videos',
        array(
            'labels'      => array(
                'name'          => __('Meal Prep Videos'),
                'singular_name' => __('Meal Prep Video'),
                'add_new'       => __('Add New Meal Prep Video'),
                'add_new_item'  => __('Add New Meal Prep Video'),
                'edit_item'     => __('Edit Meal Prep Video'),
                'new_item'      => __('New Meal Prep Video'),
                'view_item'     => __('View Meal Prep Video'),
                'search_items'  => __('Search Meal Prep Videos'),
                'not_found'     => __('No Meal Prep Videos found'),
                'not_found_in_trash' => __('No Meal Prep Videos found in Trash'),
            ),
            'public'      => true,
            'has_archive' => true,
            'supports'    => array('title', 'editor', 'thumbnail', 'excerpt', 'comments', 'author', 'custom-fields', 'revisions'),
            'taxonomies'  => array('category', 'post_tag'),
            'show_in_rest' => true,
            'rewrite'     => array('slug' => 'meal-prep-videos'),
            'menu_icon'   => 'dashicons-video-alt2'
        )
    );
}
add_action('init', 'custom_recipe_meal_post_types');

// Add Meta Boxes
function add_recipe_meal_meta_boxes() {
    add_meta_box('recipe_roast_meta', 'Recipe Roast Details', 'recipe_roast_meta_callback', 'recipe_roasts', 'normal', 'high');
    add_meta_box('meal_prep_meta', 'Meal Prep Video Details', 'meal_prep_meta_callback', 'meal_prep_videos', 'normal', 'high');
}
add_action('add_meta_boxes', 'add_recipe_meal_meta_boxes');

// Meta Box Callbacks
function recipe_roast_meta_callback($post) {
    $video_url = get_post_meta($post->ID, 'video_url', true);
    $external_link = get_post_meta($post->ID, 'external_link', true);
    ?>
    <label for="video_url">Video URL:</label>
    <input type="text" name="video_url" id="video_url" value="<?php echo esc_attr($video_url); ?>" class="widefat">

    <label for="external_link">External Link:</label>
    <input type="text" name="external_link" id="external_link" value="<?php echo esc_attr($external_link); ?>" class="widefat">
    <?php
}

function meal_prep_meta_callback($post) {
    $embed_video = get_post_meta($post->ID, 'embed_video', true);
    ?>
    <label for="embed_video">Embedded Video Code:</label>
    <textarea name="embed_video" id="embed_video" class="widefat"><?php echo esc_textarea($embed_video); ?></textarea>
    <?php
}

// Save Meta Box Data
function save_recipe_meal_meta_data($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    if (isset($_POST['video_url'])) {
        update_post_meta($post_id, 'video_url', esc_url($_POST['video_url']));
    }

    if (isset($_POST['external_link'])) {
        update_post_meta($post_id, 'external_link', esc_url($_POST['external_link']));
    }

    if (isset($_POST['embed_video'])) {
        update_post_meta($post_id, 'embed_video', $_POST['embed_video']);
    }
}
add_action('save_post', 'save_recipe_meal_meta_data');

// Shortcodes
function display_recipe_roasts($atts) {
    $query = new WP_Query(array('post_type' => 'recipe_roasts', 'posts_per_page' => -1));
    $output = '<div class="recipe-roasts-list">';
    while ($query->have_posts()) {
        $query->the_post();
        $video_url = get_post_meta(get_the_ID(), 'video_url', true);
        $output .= '<div class="recipe-roast-item">';
        $output .= '<h2>' . get_the_title() . '</h2>';
        if ($video_url) {
            $output .= '<iframe width="560" height="315" src="' . esc_url($video_url) . '" frameborder="0" allowfullscreen></iframe>';
        }
        $output .= '<p>' . get_the_excerpt() . '</p>';
        $output .= '</div>';
    }
    wp_reset_postdata();
    $output .= '</div>';
    return $output;
}
add_shortcode('recipe_roasts', 'display_recipe_roasts');

function display_meal_prep_videos($atts) {
    $query = new WP_Query(array('post_type' => 'meal_prep_videos', 'posts_per_page' => -1));
    $output = '<div class="meal-prep-videos-list">';
    while ($query->have_posts()) {
        $query->the_post();
        $embed_video = get_post_meta(get_the_ID(), 'embed_video', true);
        $output .= '<div class="meal-prep-video-item">';
        $output .= '<h2>' . get_the_title() . '</h2>';
        if ($embed_video) {
            $output .= '<div class="video-embed">' . $embed_video . '</div>';
        }
        $output .= '<p>' . get_the_excerpt() . '</p>';
        $output .= '</div>';
    }
    wp_reset_postdata();
    $output .= '</div>';
    return $output;
}
add_shortcode('meal_prep_videos', 'display_meal_prep_videos');
