<?php
set_time_limit(0);

$autoloader = dirname(__FILE__) . '/vendor/autoload.php';
if (is_readable($autoloader)) {
	require_once $autoloader;
}

use Automattic\WooCommerce\Client;

require 'db.php';



class devloment_server{

	public $woocommerce = '';
	public $get_products = '';
	function __construct(){

		require __DIR__ . '/vendor/autoload.php';

		$this->woocommerce = new Client(
			'http://192.168.1.128/botiga/',
			'ck_604627d4236d04885abd6d5ad959fed0ba312854',
			'cs_c2ee55c4c7782d0a947543537822310baba42078',
			[
				'wp_api' => true,
				'version' => 'wc/v3'
			]
		);
	}




	public function get_product_list()
	{
		$get_products = $this->woocommerce->get('products');

		if (!empty($get_products)) {

			$res_arr = array();
			foreach ($get_products as $product) {
				// Retrieve meta data
				$meta_data = $product->meta_data;
				$custom_length = '';
				$custom_width = '';
				$custom_height = '';

				// Loop through meta data to find specific fields
				foreach ($meta_data as $meta) {
					if ($meta->key == '_custom_length') {
						$custom_length = $meta->value;
					} elseif ($meta->key == '_custom_width') {
						$custom_width = $meta->value;
					} elseif ($meta->key == '_custom_height') {
						$custom_height = $meta->value;
					}
				}

				$res_arr[] = array(
					'id' => $product->id,
					'name' => $product->name,
					'custom_length' => $custom_length,
					'custom_width' => $custom_width,
					'custom_height' => $custom_height,
				);
			}

			if (!empty($res_arr)) {
				$result = array('status' => 200, 'msg' => 'Products display successfully.', 'response' => $res_arr);
			} else {
				$result = array('status' => 404, 'msg' => 'Oops..! something went wrong.', 'response' => $res_arr);
			}
		} else {
			$result = array('status' => 404, 'msg' => 'Oops..! products not found.', 'response' => []);
		}

		echo json_encode($result);
	}
}
$data = new devloment_server();

$data->get_product_list();
