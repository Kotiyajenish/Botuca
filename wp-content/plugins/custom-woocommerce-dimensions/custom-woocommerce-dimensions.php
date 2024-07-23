<?php

/**
 * Plugin Name: Custom WooCommerce Dimensions
 * Description: Adds custom dimensions fields to WooCommerce products and makes them available via the REST API.
 * Version: 1.0
 * Author: Jenish kotiya
 * Text Domain: custom-woocommerce-dimensions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}


// Display Custom Fields in Product Edit Page
add_action('woocommerce_product_options_general_product_data', 'add_custom_dimension_fields');

function add_custom_dimension_fields(){

    echo '<div class="options_group">';
    // Height Field
    woocommerce_wp_text_input(
        array(
            'id'          => '_custom_height',
            'label'       => __('Height (cm)', 'woocommerce'),
            'placeholder' => 'Enter height',
            'desc_tip'    => 'true',
            'description' => __('Enter the height of the product in cm.', 'woocommerce')
        )
    );

    // Width Field
    woocommerce_wp_text_input(
        array(
            'id'          => '_custom_width',
            'label'       => __('Width (cm)', 'woocommerce'),
            'placeholder' => 'Enter width',
            'desc_tip'    => 'true',
            'description' => __('Enter the width of the product in cm.', 'woocommerce')
        )
    );

    // Length Field
    woocommerce_wp_text_input(
        array(
            'id'          => '_custom_length',
            'label'       => __('Length (cm)', 'woocommerce'),
            'placeholder' => 'Enter length',
            'desc_tip'    => 'true',
            'description' => __('Enter the length of the product in cm.', 'woocommerce')
        )
    );

    echo '</div>';
}


// Save Custom Fields Data
add_action('woocommerce_process_product_meta', 'save_custom_dimension_fields');

function save_custom_dimension_fields($post_id)
{
    $custom_height = isset($_POST['_custom_height']) ? sanitize_text_field($_POST['_custom_height']) : '';
    $custom_width = isset($_POST['_custom_width']) ? sanitize_text_field($_POST['_custom_width']) : '';
    $custom_length = isset($_POST['_custom_length']) ? sanitize_text_field($_POST['_custom_length']) : '';

    update_post_meta($post_id, '_custom_height', $custom_height);
    update_post_meta($post_id, '_custom_width', $custom_width);
    update_post_meta($post_id, '_custom_length', $custom_length);
}


// Display Custom Fields on the Product Page
add_action('woocommerce_single_product_summary', 'display_custom_dimension_fields', 25);

function display_custom_dimension_fields()
{
    global $post;

    $custom_height = get_post_meta($post->ID, '_custom_height', true);
    $custom_width = get_post_meta($post->ID, '_custom_width', true);
    $custom_length = get_post_meta($post->ID, '_custom_length', true);
    echo '<h4>' . __('Dimensions Fields', 'woocommerce') . ':</h4>';
    if (!empty($custom_height)) {
        echo '<p class="custom-field">' . __('Height:', 'woocommerce') . ' ' . esc_html($custom_height) . ' cm</p>';
    }

    if (!empty($custom_width)) {
        echo '<p class="custom-field">' . __('Width:', 'woocommerce') . ' ' . esc_html($custom_width) . ' cm</p>';
    }

    if (!empty($custom_length)) {
        echo '<p class="custom-field">' . __('Length:', 'woocommerce') . ' ' . esc_html($custom_length) . ' cm</p>';
    }
}
