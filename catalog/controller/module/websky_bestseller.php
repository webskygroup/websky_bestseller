<?php
namespace Opencart\Catalog\Controller\Extension\WebskyBestseller\Module;
use \Opencart\System\Helper AS Helper;
class WebskyBestseller extends \Opencart\System\Engine\Controller {

	public function index(array $setting): string {
 	$data=array();
 	
		$this->load->model('catalog/product');
		$this->load->model('tool/image');
$this->load->language('extension/websky_bestseller/module/websky_bestseller');
		if($setting['product_type'] == 'new_product'){
		    $results = $this->model_catalog_product->getLatest($setting['limit']);
		}
		elseif($setting['product_type'] == 'best_sales'){
		    		$this->load->model('extension/opencart/module/bestseller');
		    $results = $this->model_extension_opencart_module_bestseller->getBestSeller($setting['limit']);
		}
		elseif($setting['product_type'] == 'feature'){

		$data['products'] = [];
       	if (!empty($setting['product'])) {
			$results = [];
			$product_data = [];

			foreach ($setting['product'] as $product_id) {
				$product_info = $this->model_catalog_product->getProduct($product_id);

				if ($product_info) {
					$results[] = $product_info;
				}
			}

       		}
		    
		}

		if ($results) {
			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize(html_entity_decode($result['image'], ENT_QUOTES, 'UTF-8'), $setting['width'], $setting['height']);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
				}

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if ((float)$result['special']) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$special = false;
				}

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
				} else {
					$tax = false;
				}

				$data['products'][] = [
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'description' => Helper\Utf8\substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('config_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'rating'      => $result['rating'],
					'href'        => $this->url->link('product/product', 'language=' . $this->config->get('config_language') . '&product_id=' . $result['product_id'])
				];

				
			}
		}

	 $data['heading']=$setting['heading'][$this->config->get('config_language_id')];
 		return $this->load->view('extension/websky_bestseller/module/websky_bestseller', $data);
  }
 }