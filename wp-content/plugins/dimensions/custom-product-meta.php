<?php
/**
 * Plugin Name: Custom Product Meta
 * Description: Adds a custom meta field to WooCommerce products.
 * Version: 1.0
 * Author: Your Name
 * Author URI: http://yourwebsite.com
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Add custom field to product edit page
function cpm_add_custom_product_fields() {
    add_meta_box(
        'cpm_custom_product_fields',
        __('Custom Dimensions Fields', 'woocommerce'),
        'cpm_custom_product_fields_callback',
        'product',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'cpm_add_custom_product_fields');

function cpm_custom_product_fields_callback($post) {
    $custom_field_value = get_post_meta($post->ID, '_cpm_custom_field_key', true);
    echo '<label for="cpm_custom_field">' . __('Custom Dimensions Fields', 'woocommerce') . ':</label>';
    echo '<input type="text" id="cpm_custom_field" name="cpm_custom_field" value="' . esc_attr($custom_field_value) . '" />';
}

// Save custom field value
function cpm_save_custom_product_fields($post_id) {
    if (isset($_POST['cpm_custom_field'])) {
        update_post_meta($post_id, '_cpm_custom_field_key', sanitize_text_field($_POST['cpm_custom_field']));
    }
}
add_action('save_post', 'cpm_save_custom_product_fields');

// Add custom field to REST API response
function cpm_add_custom_field_to_rest_api($response, $object, $request) {
    $custom_field_value = get_post_meta($object->get_id(), '_cpm_custom_field_key', true);
    if (!empty($custom_field_value)) {
        $response->data['custom_field'] = $custom_field_value;
    }
    return $response;
}
add_filter('woocommerce_rest_prepare_product', 'cpm_add_custom_field_to_rest_api', 10, 3);

// Update custom field via REST API
function cpm_update_custom_field_via_rest_api($product, $request) {
    if (isset($request['custom_field'])) {
        update_post_meta($product->get_id(), '_cpm_custom_field_key', sanitize_text_field($request['custom_field']));
    }
}
add_action('woocommerce_rest_insert_product', 'cpm_update_custom_field_via_rest_api', 10, 2);
?>
