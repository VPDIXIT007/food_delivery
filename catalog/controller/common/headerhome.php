<?php
class ControllerCommonHeaderhome extends Controller {
	public function index() {
		// Analytics
		$this->load->model('setting/extension');
		$this->load->model('extension/purpletree_multivendor/vendor');
		$lang = $this->language->get('code'); 
		$data['analytics'] = array();

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
	
		
		
		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = $server . 'image/' . $this->config->get('config_logo');
		} else {
			$data['logo'] = '';
		}
		
		
		
		
		
		$this->load->language('common/header');
		$this->load->language('common/headerhome');
		// Wishlist
		if ($this->customer->isLogged()) {
			$this->load->model('account/wishlist');

			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), $this->model_account_wishlist->getTotalWishlist());
		} else {
			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
		}

		$data['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', true), $this->customer->getFirstName(), $this->url->link('account/logout', '', true));

		$data['home'] = $this->url->link("common/home");
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
		
		return $this->load->view('common/headerhome', $data);
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
