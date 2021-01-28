<?php
class ControllerCommonHeader extends Controller {
	public function index() {
		// Analytics
		$this->load->model('setting/extension');
		$this->load->model('extension/purpletree_multivendor/vendor');
		$lang = $this->language->get('code'); 
		$data['analytics'] = array();
		$sellerstore = $this->request->get["seller_store_id"];
		//opining timeing
		$seller_info_social = $this->model_extension_purpletree_multivendor_vendor->getStoreSocial($sellerstore);
		if (!empty($seller_info_social) && isset($seller_info_social['facebook_link'])) {
			$data['facebook_link'] = $seller_info_social['facebook_link'];
		} else {
			$data['facebook_link'] = '';
		}	
		if (!empty($seller_info_social) && isset($seller_info_social['google_link'])) {
			$data['google_link'] = $seller_info_social['google_link'];
		} else {
			$data['google_link'] = '';
		}	
		if (!empty($seller_info_social) && isset($seller_info_social['twitter_link'])) {
			$data['twitter_link'] = $seller_info_social['twitter_link'];
		} else {
			$data['twitter_link'] = '';
		}		
		if (!empty($seller_info_social) && isset($seller_info_social['instagram_link'])) {
			$data['instagram_link'] = $seller_info_social['instagram_link'];
		} else {
			$data['instagram_link'] = '';
		}		
		if (!empty($seller_info_social) && isset($seller_info_social['pinterest_link'])) {
			$data['pinterest_link'] = $seller_info_social['pinterest_link'];
		} else {
			$data['pinterest_link'] = '';
		}		
		if (!empty($seller_info_social) && isset($seller_info_social['wesbsite_link'])) {
			$data['wesbsite_link'] = $seller_info_social['wesbsite_link'];
		} else {
			$data['wesbsite_link'] = '';
		}		
					
			if (!empty($seller_info_social) && isset($seller_info_social['whatsapp_link'])) {
			$whatsapp_no = $seller_info_social['whatsapp_link'];
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
				$data['storestatus'] = 'Open Now';
				foreach($store_timings as $time) {
					$restday = substr($time['day_name'], 0, 3);
					if ($restday == $today) 
					{
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

		$analytics = $this->model_setting_extension->getExtensions('analytics');

		foreach ($analytics as $analytic) {
			if ($this->config->get('analytics_' . $analytic['code'] . '_status')) {
				$data['analytics'][] = $this->load->controller('extension/analytics/' . $analytic['code'], $this->config->get('analytics_' . $analytic['code'] . '_status'));
			}
		}

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
			$this->document->addLink($server . 'image/' . $this->config->get('config_icon'), 'icon');
		}

		$data['title'] = $this->document->getTitle();

		$data['base'] = $server;
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();
		$data['scripts'] = $this->document->getScripts('header');
		$data['lang'] = $this->language->get('code');
		$data['direction'] = $this->language->get('direction');
		if(isset($this->session->data['tracking_order_id'])){
		$data['tracking_order_id'] = $this->session->data['tracking_order_id'];
		}

		$data['name'] = $this->config->get('config_name');
		
		if(isset($this->request->get['seller_store_id'])) {
		$sellerid = $this->request->get['seller_store_id'];
		
		}
		
		
		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = $server . 'image/' . $this->config->get('config_logo');
		} else {
			$data['logo'] = '';
		}
		
		
		
		if(isset($sellerid)) {
				$this->load->model('extension/purpletree_multivendor/vendor');
					$seller_info = $this->model_extension_purpletree_multivendor_vendor->getStore($sellerid);
					if($seller_info['store_logo']) {
			$data['logo'] = $server . 'image/' .$seller_info['store_logo'];
					}
		}
		
		$this->load->language('common/header');

		// Wishlist
		if ($this->customer->isLogged()) {
			$this->load->model('account/wishlist');

			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), $this->model_account_wishlist->getTotalWishlist());
		} else {
			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
		}

		$data['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', true), $this->customer->getFirstName(), $this->url->link('account/logout', '', true));

		$data['home'] = $this->url->link('extension/account/purpletree_multivendor/sellerstore/storeview&seller_store_id='.$sellerid);
		$data['wishlist'] = $this->url->link('account/wishlist', '', true);
		$data['logged'] = $this->customer->isLogged();
		$data['account'] = $this->url->link('account/account', '', true);
		$data['register'] = $this->url->link('account/register', '', true);
		$data['login'] = $this->url->link('account/login', '', true);
		$data['order'] = $this->url->link('account/order', '', true);
		$data['transaction'] = $this->url->link('account/transaction', '', true);
		$data['download'] = $this->url->link('account/download', '', true);
		$data['logout'] = $this->url->link('account/logout', '', true);
		$data['shopping_cart'] = $this->url->link('checkout/cart');
		$data['checkout'] = $this->url->link('checkout/checkout', '', true);
		$data['contact'] = $this->url->link('information/contact');
		$data['telephone'] = $this->config->get('config_telephone');
		$data['open'] = $this->config->get('config_open');
		
		if(isset($seller_info['store_phone']))
		{
			$data['telephone'] = $seller_info['store_phone'];
		}
		
		$data['language'] = $this->load->controller('common/language');
		$data['currency'] = $this->load->controller('common/currency');
		// Mahardhi Edit
			// search
			if (file_exists(DIR_TEMPLATE . $this->config->get('theme_mahardhi_directory') . '/template/extension/module/mahardhi_search.twig') && $this->config->get('module_mahardhi_search_status')) {
				$data['advanceSearch'] = $this->config->get('module_mahardhi_search_autocomplete');
				$data['search'] = $this->load->controller('extension/module/mahardhi_search');
			} else {
				$data['search'] = $this->load->controller('common/search');
			}

			// colors
		  	$primaryColor = $this->config->has('theme_mahardhi_primary_color') ? $this->config->get('theme_mahardhi_primary_color') : '#222222';
		  	$primaryHoverColor = $this->config->has('theme_mahardhi_primary_hover_color') ? $this->config->get('theme_mahardhi_primary_hover_color') : '#ffffff';
		  	$secondaryColor = $this->config->has('theme_mahardhi_secondary_color') ? $this->config->get('theme_mahardhi_secondary_color') : '#79b530';
		  	$secondaryHoverColor = $this->config->has('theme_mahardhi_secondary_hover_color') ? $this->config->get('theme_mahardhi_secondary_hover_color') : '#e01212';
		  	$secondaryLightColor = $this->config->has('theme_mahardhi_secondary_light_color') ? $this->config->get('theme_mahardhi_secondary_light_color') : '#7d7d7d';
		  	$backgroundColor = $this->config->has('theme_mahardhi_background_color') ? $this->config->get('theme_mahardhi_background_color') : '#f5f5f5';
		  	$borderColor = $this->config->has('theme_mahardhi_border_color') ? $this->config->get('theme_mahardhi_border_color') : '#dddddd';

			$data['inline_style'] = html_entity_decode('<style>
				:root {
					--primary-color: ' . $primaryColor . ';
					--primary-hover-color: ' . $primaryHoverColor . ';
					--secondary-color: ' . $secondaryColor . ';
					--secondary-hover-color: ' . $secondaryHoverColor . ';
					--secondary-light-color: ' . $secondaryLightColor . ';
					--background-color: ' . $backgroundColor . ';
					--border-color: ' . $borderColor . '
				}
			</style>', ENT_QUOTES, 'UTF-8');
		// End
		$data['cart'] = $this->load->controller('common/cart');
		$data['menu'] = $this->load->controller('common/menu');

		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		if (isset($this->request->get['route'])) {
			if (isset($this->request->get['product_id'])) {
				$class = '-' . $this->request->get['product_id'];
			} elseif (isset($this->request->get['path'])) {
				$class = '-' . $this->request->get['path'];
			} elseif (isset($this->request->get['manufacturer_id'])) {
				$class = '-' . $this->request->get['manufacturer_id'];
			} elseif (isset($this->request->get['information_id'])) {
				$class = '-' . $this->request->get['information_id'];
			} else {
				$class = '';
			}
			$data['class'] = str_replace('/', '-', $this->request->get['route']) . $class;
		} else {
			$data['class'] = 'common-home';
		}
		
		$data['header_top'] = $this->load->controller('common/header_top');
		
		return $this->load->view('common/header', $data);
	}
	public function pushnotifications() {

		$firetoken = $this->request->post['token'];
	
		$thissession = $this->session->getId();
		// check if session exists
		$sessiony = $this->db->query("SELECT * FROM ". DB_PREFIX ."pushnotifications WHERE session_id = '". $thissession ."'")->row;
	//	trigger_error(print_r($sessiony,true));
		if($sessiony['session_id'] == $thissession) {
			if ($firetoken == $sessiony['firebasetoken']) {} else {
			$this->db->query("UPDATE ". DB_PREFIX ."pushnotifications SET firebasetoken= '". $firetoken ."', session_id ='". $thissession ."'");
			}} else {
		$this->db->query("INSERT INTO ". DB_PREFIX ."pushnotifications (firebasetoken,session_id) VALUES ('". $firetoken ."', '". $thissession ."')");
		} 
	}
}
