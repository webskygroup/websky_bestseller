<?php
namespace Opencart\Admin\Controller\Extension\WebskyBestseller\Module;
use \Opencart\System\Helper AS Helper;
class WebskyBestseller extends \Opencart\System\Engine\Controller {
	public function index(): void {
		
		$this->load->language('extension/websky_bestseller/module/websky_bestseller');
				$this->document->setTitle(strip_tags($this->language->get('heading_title')));


	    $data['user_token']=$this->session->data['user_token'];

		

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module')
		];

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = [
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/opencart/module/websky_bestseller', 'user_token=' . $this->session->data['user_token'])
			];
		} else {
			$data['breadcrumbs'][] = [
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/opencart/module/websky_bestseller', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'])
			];
		}

		if (!isset($this->request->get['module_id'])) {
			$data['save'] = $this->url->link('extension/websky_bestseller/module/websky_bestseller.save', 'user_token=' . $this->session->data['user_token']);
		} else {
			$data['save'] = $this->url->link('extension/websky_bestseller/module/websky_bestseller.save', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id']);
		}

		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module');

		if (isset($this->request->get['module_id'])) {
			$this->load->model('setting/module');

			$module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
		}

		
		if (isset($module_info['name'])) {
			$data['name'] = $module_info['name'];
		} else {
			$data['name'] = '';
		}

	
		if (isset($module_info['heading'])) {
			$data['heading'] = $module_info['heading'];
		} else {
			$data['heading'] = '';
		}
		
		if (isset($module_info['limit'])) {
			$data['limit'] = $module_info['limit'];
		} else {
			$data['limit'] = '';
		}	
			if (isset($module_info['width'])) {
			$data['width'] = $module_info['width'];
		} else {
			$data['width'] = '';
		}
		
			if (isset($module_info['height'])) {
			$data['height'] = $module_info['height'];
		} else {
			$data['height'] = '';
		}
		
	
		if (isset($module_info['status'])) {
			$data['status'] = $module_info['status'];
		} else {
			$data['status'] = '';
		}
				
		$this->load->model('catalog/product');

		$data['products'] = [];

		if (!empty($module_info['product'])) {
			$products = $module_info['product'];
		} else {
			$products = [];
		}

		foreach ($products as $product_id) {
			$product_info = $this->model_catalog_product->getProduct($product_id);

			if ($product_info) {
				$data['products'][] = [
					'product_id' => $product_info['product_id'],
					'name'       => $product_info['name']
				];
			}
		}
		
		$data['user_token']	 = $this->session->data['user_token'];
		$data['current_version'] = "1.0.6";
		$data['upgrade'] = false;

	  $url = 'https://opencart-ir.com/version/index.php?route=extension/websky_lastversion/module/websky_lastversion';
       $feilds=array(
            'extension_name'=>'websky_bestseller'
           );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $feilds);
        // Execute post
        $json = curl_exec($ch);
     //   print_r($json);
        if ($json === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        // Close connection
        curl_close($ch);
        $response_info=json_decode($json, true);
		if ($response_info) {
			$data['latest_version'] = $response_info['version_ext'];
			$data['date_added'] =jdate($this->config->get('language_traditional_persian_shamsidate_format'), strtotime($response_info["date_added"]));
			if (!version_compare($data['current_version'], $response_info['version_ext'], '>=')) {
				$data['upgrade'] = true;
			}
		} else {
			$data['latest_version'] = '';
			$data['date_added'] = '';
			$data['log'] = '';
		}
		
		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/websky_bestseller/module/websky_bestseller', $data));

	}

	

	
	public function save(): void {

		$this->load->language('extension/websky_bestseller/module/websky_bestseller');

		$json = [];

		if (!$this->user->hasPermission('modify', 'extension/websky_bestseller/module/websky_bestseller')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json) {
			$this->load->model('setting/module');

			if (!isset($this->request->get['module_id'])) {
				$this->model_setting_module->addModule('websky_bestseller.websky_bestseller', $this->request->post);
			} else {
				$this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
			}

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
		public function download(): void {
		$this->load->language('marketplace/marketplace');

		$json = [];

			if (isset($this->request->get['extension_name'])) {
			$extension_name = $this->request->get['extension_name'];
		} else {
			$json['error']= 'extension name null';
		}
		

	
		if (!$this->user->hasPermission('modify', 'marketplace/marketplace')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json) {
		    


		    	$handle = fopen(DIR_STORAGE . 'marketplace/'.$extension_name.'.ocmod.zip' , 'w');

					$download = $this->get_data('https://opencart-ir.com/dl/'.$extension_name.'.ocmod.zip');

					fwrite($handle, $download);

					fclose($handle);
					
					$this->load->language('marketplace/installer');

	     	$json = [];

		if (!$this->user->hasPermission('modify', 'marketplace/installer')) {
			$json['error'] = $this->language->get('error_permission');
		}

		$this->load->model('setting/extension');

			$file = DIR_STORAGE . 'marketplace/' . $extension_name . '.ocmod.zip';

			if (!is_file($file)) {
				$json['error'] = sprintf($this->language->get('error_file'), $extension_name . '.ocmod.zip');
			}


		if (!$json) {
		  
			// Unzip the files
			 $path = $extension_name;
		    $base = DIR_EXTENSION;
		    $this->unzip_file($file, $base . $path);
		}

	
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
		public function update(): void {
		$this->load->language('marketplace/marketplace');

		$json = [];

			if (isset($this->request->get['extension_name'])) {
			$extension_name = $this->request->get['extension_name'];
		} else {
			$json['error']= 'extension name null';
		}
		

	
		if (!$this->user->hasPermission('modify', 'marketplace/marketplace')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json) {
		    
	     $json['success']= 'extension name null';
		
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	private	function get_data($url)
{
  $ch = curl_init();
  $timeout = 15;
  curl_setopt($ch,CURLOPT_URL,$url);
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
  $data = curl_exec($ch);
  curl_close($ch);
  return $data;
}

public function unzip_file($file, $destination){
		// create object
		// Unzip the files
			$zip = new \ZipArchive();

		// open archive
		if ($zip->open($file) !== TRUE) {
			return false;
		}
		$zip->extractTo($destination);
		// close archive
		$zip->close();
			return true;
	}
	

	 
	
}
