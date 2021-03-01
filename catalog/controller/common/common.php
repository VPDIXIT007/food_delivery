<?php
class ControllerCommonCommon extends Controller {

  public function sendCallPushNotification(){
		if (isset($this->request->post['seller_store_id']) && $this->session->data['table_id']){
			
			$type = $this->request->post['call_type'];
			$name = $this->customer->getFirstName();

      $tablenum = $this->db->query("SELECT * FROM ". DB_PREFIX ."table_manger WHERE id = '". $this->session->data['table_id'] ."'")->row;
			$comment  = "TABLE NUMBER : ".$tablenum['table_no'];

			if($type == "cheque"){
				$message = "$name rquested for a cheque. $comment";
			}else{
				$message = "$name called for a waiter. $comment";
			}
			
			$seller_id =$this->request->post['seller_store_id'];

			$customer_id = $this->db->query("SELECT seller_id FROM ". DB_PREFIX ."purpletree_vendor_stores WHERE id = '". $seller_id ."'")->row;
			
			$sessionorder = $this->db->query("SELECT session_id from ". DB_PREFIX ."session WHERE data LIKE '%\"customer_id\":\"". $customer_id['seller_id'] ."%' ORDER BY expire DESC ")->row;
		
			$firetoken = $this->db->query("SELECT * FROM ". DB_PREFIX ."pushnotifications WHERE session_id = '". $sessionorder['session_id'] ."'")->row;
			
      //save in notification 
			$notification_sql = "INSERT INTO oc_fcm_notification SET `session_id` = '".$customer_id['seller_id']."', `payload` = '".$message."', `status` = '0' ";
			$this->db->query($notification_sql);
      $notification_id = $this->db->getLastId();

			$url ="https://fcm.googleapis.com/fcm/send";

			$fields=array(
					"to"	=> $firetoken['firebasetoken'],
					"notification"	=> array(
							"body"	=>	$message,
							"title"	=>	'Call notification',
					),
					"data" => array(
						"type" => "call_waiter",
            "notification_id" => $notification_id
					)
			);

			$headers=array(
					'Authorization: key=AAAAQIa-Tu0:APA91bHZa633Mg2ML9iOEl9D9XL4UI22XO-Cj4jCdlHUXExFcIOYoGps3UecyN_J5q8kIzOtK0ay19FDEiUMaaoJlokihWhwGr6Fz7TCu3lYDHOX0Vzxw_-kvcTNjN1Tu9ZAb8RBqprn',
					'Content-Type:application/json'
			);

			$ch=curl_init();
			curl_setopt($ch,CURLOPT_URL,$url);
			curl_setopt($ch,CURLOPT_POST,true);
			curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
			curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($fields));
			$result=curl_exec($ch);
			curl_close($ch);

      return $result;
		}
	}

  public function clearCallPushNotification(){
    $customer_id = $this->customer->getId();

    $notification_sql = "DELETE FROM `oc_fcm_notification` WHERE `session_id` = '$customer_id' AND `status` = '1'";
		$this->db->query($notification_sql);

    return true;
  }

  public function markCallPushNotification(){
    if(isset($this->request->post['notification_id']) && isset($this->request->post['notification_status'])){
      $notification_id = $this->request->post['notification_id'];
      $status = $this->request->post['notification_status'];

      $notification_sql = "UPDATE `oc_fcm_notification` SET `status` = '$status' WHERE `notification_id` = '$notification_id'";
      $this->db->query($notification_sql);
    }

    return true;
  }

	public function homeV2(){
		
			// Analytics
			$this->load->model('setting/extension');
			$this->load->model('extension/purpletree_multivendor/vendor');
			$lang = $this->language->get('code'); 
			$data['analytics'] = array();
			$sellerstore = $this->request->get["seller_store_id"];
			$data['sellerstore'] = $sellerstore;
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
			$data['tracking_order_no'] = $this->session->data['tracking_order_no'];
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
			$data['reward'] = $this->url->link('account/reward', '', true);
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
				$this->load->model('extension/purpletree_multivendor/subscriptionplan');
				$plan_info = $this->model_extension_purpletree_multivendor_subscriptionplan->getCurrentPlan($seller_info['seller_id']);
				if(isset($plan_info['enable_theme_setting'])){
					$data['enable_theme_setting'] = $plan_info['enable_theme_setting'];
				}else{
					$data['enable_theme_setting'] = 0;
				}
	
				if($data['enable_theme_setting'] && isset($sellerid) && !empty($seller_info['theme_setting_color'])) {
					$arr = json_decode($seller_info['theme_setting_color'], true);
					// colors
					$primaryColor = $arr['theme_mahardhi_primary_color'];
					$primaryHoverColor = $arr['theme_mahardhi_primary_hover_color'];
					$secondaryColor = $arr['theme_mahardhi_secondary_color'];
					$secondaryHoverColor = $arr['theme_mahardhi_secondary_hover_color'];
					$secondaryLightColor =  $arr['theme_mahardhi_secondary_light_color'];
					$backgroundColor = $arr['theme_mahardhi_background_color'];
					$borderColor = $arr['theme_mahardhi_border_color'];
				}
				else{
					$primaryColor = $this->config->has('theme_mahardhi_primary_color') ? $this->config->get('theme_mahardhi_primary_color') : '#222222';
					$primaryHoverColor = $this->config->has('theme_mahardhi_primary_hover_color') ? $this->config->get('theme_mahardhi_primary_hover_color') : '#ffffff';
					$secondaryColor = $this->config->has('theme_mahardhi_secondary_color') ? $this->config->get('theme_mahardhi_secondary_color') : '#79b530';
					$secondaryHoverColor = $this->config->has('theme_mahardhi_secondary_hover_color') ? $this->config->get('theme_mahardhi_secondary_hover_color') : '#e01212';
					$secondaryLightColor = $this->config->has('theme_mahardhi_secondary_light_color') ? $this->config->get('theme_mahardhi_secondary_light_color') : '#7d7d7d';
					$backgroundColor = $this->config->has('theme_mahardhi_background_color') ? $this->config->get('theme_mahardhi_background_color') : '#f5f5f5';
					$borderColor = $this->config->has('theme_mahardhi_border_color') ? $this->config->get('theme_mahardhi_border_color') : '#dddddd';
				}
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
			
			$data['session_table_id'] = 0;
			if(isset($this->session->data['table_id']) && $this->session->data['table_id']){
				$data['session_table_id'] = $this->session->data['table_id'];
			}

								  
		if (isset($this->request->get["seller_store_id"])){
			$this->load->model('extension/purpletree_multivendor/vendor');
				
			 $sellerstore = $this->request->get["seller_store_id"];
			 //trigger_error($sellerstore);
			 $store_timings = $this->model_extension_purpletree_multivendor_vendor->getStoreTime($sellerstore);
				 
			 if (!empty($store_timings)) {
			 $data['store_timings'] = $store_timings;
		 } else {
			 $data['store_timings'] = '';
		 }
	 }	 


	 $this->load->language('common/footer');

	 $this->load->model('catalog/information');

	 $data['informations'] = array();

	 foreach ($this->model_catalog_information->getInformations() as $result) {
		 if ($result['bottom']) {
			 $data['informations'][] = array(
				 'title' => $result['title'],
				 'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
			 );
		 }
	 }
	 $data['lang'] = $this->language->get('code');
	 $data['contact'] = $this->url->link('information/contact');
	 $data['return'] = $this->url->link('account/return/add', '', true);
	 $data['sitemap'] = $this->url->link('information/sitemap');
	 $data['tracking'] = $this->url->link('information/tracking');
	 $data['manufacturer'] = $this->url->link('product/manufacturer');
	 $data['voucher'] = $this->url->link('account/voucher', '', true);
	 $data['affiliate'] = $this->url->link('affiliate/login', '', true);
	 $data['special'] = $this->url->link('product/special');
	 $data['account'] = $this->url->link('account/account', '', true);
	 $data['order'] = $this->url->link('account/order', '', true);
	 $data['wishlist'] = $this->url->link('account/wishlist', '', true);
	 $data['newsletter'] = $this->url->link('account/newsletter', '', true);

	 $data['powered'] = sprintf($this->language->get('text_powered'), $this->config->get('config_name'), date('Y', time()));

	 // Whos Online
	 if ($this->config->get('config_customer_online')) {
		 $this->load->model('tool/online');

		 if (isset($this->request->server['REMOTE_ADDR'])) {
			 $ip = $this->request->server['REMOTE_ADDR'];
		 } else {
			 $ip = '';
		 }

		 if (isset($this->request->server['HTTP_HOST']) && isset($this->request->server['REQUEST_URI'])) {
			 $url = ($this->request->server['HTTPS'] ? 'https://' : 'http://') . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI'];
		 } else {
			 $url = '';
		 }

		 if (isset($this->request->server['HTTP_REFERER'])) {
			 $referer = $this->request->server['HTTP_REFERER'];
		 } else {
			 $referer = '';
		 }

		 $this->model_tool_online->addOnline($ip, $this->customer->getId(), $url, $referer);
	 }

	 if(isset($this->request->get['seller_store_id'])){
		$sellerstore = $this->request->get['seller_store_id'];
		} else if ($this->customer->isSeller()) {
			$sellerstore_d = $this->customer->isSeller();
			$sellerstore = $sellerstore_d['id'];
		}

	 $slider = $this->model_extension_purpletree_multivendor_vendor->getSlider($sellerstore);
	 $data['slider'] = array();

	 if(isset($slider)) {
		 if(!empty($slider)) {
		 foreach ($slider as $result) {
			 $data['slider'][] = array(
				 'image' => $result['image'],
			 );
		 }
	 }
	 }


	 $data['scripts'] = $this->document->getScripts('footer');
	 $data['footer_top'] = $this->load->controller('common/footer_top');
	 $data['footer_left'] = $this->load->controller('common/footer_left');
	 $data['footer_right'] = $this->load->controller('common/footer_right');
	 $data['footer_bottom'] = $this->load->controller('common/footer_bottom');
	 
		$this->response->setOutput($this->load->view('ac_theme/home', $data));
	}
}