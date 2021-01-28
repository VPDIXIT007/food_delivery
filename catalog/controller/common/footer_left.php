<?php
class ControllerCommonFooterLeft extends Controller {
	public function index() {
		$this->load->model('design/layout');

		if (isset($this->request->get['route'])) {
			$route = (string)$this->request->get['route'];
		} else {
			$route = 'common/home';
		}

		$layout_id = 0;
		// seller information	
		if(isset($this->request->get['seller_store_id'])) {
		$seller_Store = $this->model_extension_purpletree_multivendor_vendor->getStore($this->request->get['seller_store_id']);
		if(isset($seller_Store['seller_id'])){
			$sellerstore='';
		if(isset($this->request->get['seller_store_id'])){
			$sellerstore = $this->request->get['seller_store_id'];
		} else if ($this->customer->isSeller()) {
			$sellerstore_d = $this->customer->isSeller();
			$sellerstore = $sellerstore_d['id'];
		}
		$store_detail = $this->model_extension_purpletree_multivendor_vendor->getStore($sellerstore); 
			$data['store_rating'] = $this->model_extension_purpletree_multivendor_vendor->getStoreRating($store_detail['seller_id']);
		 $cus_seller_email  = $this->model_extension_purpletree_multivendor_vendor->getCustomerEmailId($store_detail['seller_id']);
		$data['seller_review_status'] = $this->config->get('module_purpletree_multivendor_seller_review');
		$data['store_review'] = $this->url->link('extension/account/purpletree_multivendor/sellerstore/sellerreview','seller_id=' . $store_detail['seller_id'].'&seller_store_id=' . $this->request->get['seller_store_id'], true);
		
			
		$_seller_info_social = $this->model_extension_purpletree_multivendor_vendor->getStoreSocial($sellerstore);
		if (!empty($_seller_info_social) && isset($_seller_info_social['facebook_link'])) {
			$data['facebook_link'] = $_seller_info_social['facebook_link'];
		} else {
			$data['facebook_link'] = '';
		}	
		if (!empty($_seller_info_social) && isset($_seller_info_social['google_link'])) {
			$data['google_link'] = $_seller_info_social['google_link'];
		} else {
			$data['google_link'] = '';
		}	
		if (!empty($_seller_info_social) && isset($_seller_info_social['twitter_link'])) {
			$data['twitter_link'] = $_seller_info_social['twitter_link'];
		} else {
			$data['twitter_link'] = '';
		}		
		if (!empty($_seller_info_social) && isset($_seller_info_social['instagram_link'])) {
			$data['instagram_link'] = $_seller_info_social['instagram_link'];
		} else {
			$data['instagram_link'] = '';
		}		
		if (!empty($_seller_info_social) && isset($_seller_info_social['pinterest_link'])) {
			$data['pinterest_link'] = $_seller_info_social['pinterest_link'];
		} else {
			$data['pinterest_link'] = '';
		}		
		if (!empty($_seller_info_social) && isset($_seller_info_social['wesbsite_link'])) {
			$data['wesbsite_link'] = $_seller_info_social['wesbsite_link'];
		} else {
			$data['wesbsite_link'] = '';
		}		
					
			if (!empty($_seller_info_social) && isset($_seller_info_social['whatsapp_link'])) {
			$whatsapp_no = $_seller_info_social['whatsapp_link'];
		} else {
			$whatsapp_no = '';
		}	
		if ($tablet_browser > 0) {
		   // do something for tablet devices
		   if($whatsapp_no!=''){
		   $data['whatsapp_link']='https://api.whatsapp.com/send?phone='.$whatsapp_no;
		   }
		}
		else if ($mobile_browser > 0) {
		   // do something for mobile devices
		   if($whatsapp_no!=''){
		   	$data['whatsapp_link']='https://api.whatsapp.com/send?phone='.$whatsapp_no;
		   }
		}
		else {
		   // do something for everything else
			   if($whatsapp_no!=''){
					$data['whatsapp_link']='https://web.whatsapp.com/send?phone='.$whatsapp_no;
			   }
		}   
		 
		$store_timings = $this->model_extension_purpletree_multivendor_vendor->getStoreTime($sellerstore);
		$today = date("D");
	  if($store_timings) {
	  foreach($store_timings as $time) {
		  
			$restday = substr($time['day_name'], 0, 3);
	  
		  if ($restday == $today) {
			  
		  $now =  date("d H:i:s a",strtotime('2 hour'));
	  
		  $openining = date('d H:i:s a', strtotime($time['open_time']));
		  $closing = date('d H:i:s a', strtotime($time['close_time']));
		  $closingampm = date('A', strtotime($time['close_time']));
		  $openinghour =  date('d H:i:s a', strtotime($time['open_time'].' -1 hour '));
		  $closinghour = date('d H:i:s a', strtotime($time['close_time'].' -1 hour '));
		  $closingampm2 = date('A', strtotime($time['close_time'].' -1 hour '));
		  if ($closingampm2 == 'AM') {		$closinghour = date('d H:i:s a', strtotime($time['close_time'].' +23 hour ')); }
	  if ($closingampm == 'AM') {	$closing = date('d H:i:s a', strtotime($time['close_time'].' +1 day')); }
		  if ($now > $openining && $now < $closing)
		  {
			if($lang == 'en') {	$data['storestatus'] = 'Open Now'; } else {$data['storestatus'] = 'مفتوح الان';}
			  if ($now > $closinghour && $now < $closing) {
			   if($lang == 'en') {	$data['storestatus'] = 'Closing Soon'; } else {$data['storestatus'] = 'سيتم الاغلاق قريبا';}
		  }
		  }
		   elseif ($now > $closing) {
				if($lang == 'en') {		$data['storestatus'] = 'Closed'; } else {	$data['storestatus'] = 'مغلق الان';}
		  } elseif ($now > $openinghour && $now < $openining) {
				if($lang == 'en') {		$data['storestatus'] = 'Opening Soon'; } else {	$data['storestatus'] = 'سيتم الفتح قريبا';}
			  
	  }
	  } 
			$restday = '';
	  }}
//end opening time




			$data['store_name'] = $store_detail['store_name'];
			$data['seller_name'] = $store_detail['seller_name'];
			$data['store_email'] = $cus_seller_email;
			$data['store_phone'] = $store_detail['store_phone'];
			$data['module_purpletree_multivendor_store_email'] = $this->config->get('module_purpletree_multivendor_store_email');
			$data['module_purpletree_multivendor_store_phone'] = $this->config->get('module_purpletree_multivendor_store_phone');
			$data['module_purpletree_multivendor_store_address'] = $this->config->get('module_purpletree_multivendor_store_address');
		    $data['module_purpletree_multivendor_store_social_link'] = $this->config->get('module_purpletree_multivendor_store_social_link');///Social links
			$data['module_purpletree_multivendor_seller_name'] = $this->config->get('module_purpletree_multivendor_seller_name');
			$data['store_city'] = html_entity_decode($store_detail['store_city'], ENT_QUOTES, 'UTF-8').',';
			$data['store_state'] = $this->model_extension_purpletree_multivendor_vendor->getStateName($store_detail['store_state'],$store_detail['store_country']);
			$data['store_zipcode'] = $store_detail['store_zipcode'];
			$data['store_country'] = $this->model_extension_purpletree_multivendor_vendor->getCountryName($store_detail['store_country']).',';
			if (!empty($store_detail)) {
			$data['store_address'] = $store_detail['store_address'];
			$data['store_addresslen'] = strlen($data['store_address']);
		} else {
			$data['store_address'] = '';
		}

	

		
		}
		}
		if ($route == 'product/category' && isset($this->request->get['path'])) {
			$this->load->model('catalog/category');

			$path = explode('_', (string)$this->request->get['path']);

			$layout_id = $this->model_catalog_category->getCategoryLayoutId(end($path));
		}

		if ($route == 'product/product' && isset($this->request->get['product_id'])) {
			$this->load->model('catalog/product');

			$layout_id = $this->model_catalog_product->getProductLayoutId($this->request->get['product_id']);
		}

		if ($route == 'information/information' && isset($this->request->get['information_id'])) {
			$this->load->model('catalog/information');

			$layout_id = $this->model_catalog_information->getInformationLayoutId($this->request->get['information_id']);
		}

		if (!$layout_id) {
			$layout_id = $this->model_design_layout->getLayout($route);
		}

		if (!$layout_id) {
			$layout_id = $this->config->get('config_layout_id');
		}

		$this->load->model('setting/module');

		$data['modules'] = array();

		$modules = $this->model_design_layout->getLayoutModules($layout_id, 'footer_left');

		foreach ($modules as $module) {
			$part = explode('.', $module['code']);

			if (isset($part[0]) && $this->config->get('module_' . $part[0] . '_status')) {
				$module_data = $this->load->controller('extension/module/' . $part[0]);

				if ($module_data) {
					$data['modules'][] = $module_data;
				}
			}

			if (isset($part[1])) {
				$setting_info = $this->model_setting_module->getModule($part[1]);

				if ($setting_info && $setting_info['status']) {
					$output = $this->load->controller('extension/module/' . $part[0], $setting_info);

					if ($output) {
						$data['modules'][] = $output;
					}
				}
			}
		}

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_theme') . '/template/common/footer_left.twig')) {
			return $this->load->view('common/footer_left', $data);
	  	} else {
	     	return;
	  	}
	}
}
