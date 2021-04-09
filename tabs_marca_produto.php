<?php 
/**
 * Plugin Name: Tab  Produto
 * Description: Customizacao Tab Produto
 * Version: 1.0
 * Author: Vittoria Thomasini
 */

/**
* Displays the custom text field input field in the WooCommerce product data meta box
*/
function create_custom_field() {
    $args = array(
    'id' => 'custom_field_brand',
    'label' => __( 'Detalhes da Marca'),
    'class' => 'brand-custom-field',
    'desc_tip' => true,
    'description' => __( 'Enter the brand details.'),
    );
    woocommerce_wp_textarea_input( $args );
}
add_action( 'woocommerce_product_options_general_product_data', 'create_custom_field' );

function create_custom_field_product() {
    $args = array(
    'id' => 'custom_field_product',
    'label' => __( 'Detalhes do Produto'),
    'class' => 'product-custom-field',
    'desc_tip' => true,
    'description' => __( 'Enter the product details.' ),
    );
    woocommerce_wp_textarea_input( $args );
}
add_action( 'woocommerce_product_options_general_product_data', 'create_custom_field_product' );

/**
* Saves the custom field data to product meta data
*/
function save_custom_field_brand( $post_id ) {
    $product = wc_get_product( $post_id );
    $details_brand = isset( $_POST['custom_field_brand'] ) ? $_POST['custom_field_brand'] : '';
    $product->update_meta_data( 'custom_field_brand', sanitize_textarea_field( $details_brand ) );
    $product->save();
}
add_action( 'woocommerce_process_product_meta', 'save_custom_field_brand' );

function save_custom_field_product( $post_id ) {
    $product = wc_get_product( $post_id );
    $details_product = isset( $_POST['custom_field_product'] ) ? $_POST['custom_field_product'] : '';
    $product->update_meta_data( 'custom_field_product', sanitize_textarea_field( $details_product ) );
    $product->save();
}
add_action( 'woocommerce_process_product_meta', 'save_custom_field_product' );

/**
 * Rename product data tabs
 */
add_filter( 'woocommerce_product_tabs', 'rename_tab', 98 );
function rename_tab( $tabs ) 
{   // Rename the additional information tab
	$tabs['additional_information']['title'] = __( 'O Produto' );
	return $tabs;
}
/**
 * Reorder priority tabs
 */
function reorder_tabs( $tabs ) {
    $tabs['description']['priority'] = 20; //default priority: 10
 	$tabs['additional_information']['priority'] = 10;	//default priority: 20
 	$tabs['reviews']['priority'] = 50;			// default priority: 30
 
 	return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'reorder_tabs', 20 );
/**
 * Create custom tab
 */
function custom_tab_brand($tabs_brand){
    $tabs_brand['tab'] = array(
        'title' => 'A Marca',
        'priority' => 30,
        'callback' => 'content_tab_brand'
        );
return $tabs_brand;
}
add_filter('woocommerce_product_tabs', 'custom_tab_brand');
/**
 * Customize tab additional information with product data 
 */
add_filter( 'woocommerce_product_tabs', 'custom_additional_information_tab', 98 );
function custom_additional_information_tab( $tabs ) {
	$tabs['additional_information']['callback'] = 'custom_additional_information_tab_content';	
	return $tabs;
}

function custom_additional_information_tab_content() {
	global $product;
	global $post;
// Check for the custom field value
    $product = wc_get_product( $post->ID );
    $details_product = $product->get_meta( 'custom_field_product' );
    if( $details_product ) {
        echo get_post_meta($post->ID, 'custom_field_product', true);
        return $tabs;
    }
}
add_action( 'rest_api_init', 'create_api_posts_meta_field' );

function content_tab_brand() {
    global $product;
	global $post;
// Check for the custom field value
    $product = wc_get_product( $post->ID );
    $details_brand = $product->get_meta( 'custom_field_brand' );
    if( $details_brand ) {
        echo get_post_meta($post->ID, 'custom_field_brand', true);
        return $tabs;
    }
}
add_action('custom_tab_brand','content_tab_brand');
 ?>
