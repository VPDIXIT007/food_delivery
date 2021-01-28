<?php
class ControllerExtensionAccountPurpletreeMultivendorCommonHeader extends Controller {
	public function index() {
		$data = array();
		$this->load->language('extension/module/purpletree_sellerpanel');  
		$this->load->language('purpletree_multivendor/header');  
		$this->load->language('account/ptsregister');  
		$data['seller_logo'] = '/admin/view/image/logo.png';
		if($this->customer->isLogged() && $seller_store = $this->customer->isSeller()) {
			$this->load->model('extension/purpletree_multivendor/vendor');
			$data['logged'] = 1;
			$seller = $this->model_extension_purpletree_multivendor_vendor->getsellerInfo();
				$data['firstname'] = '';
					$data['lastname'] = '';
			if($seller) {
				if(isset($seller['store_logo']) && $seller['store_logo'] != '') {
					//$data['seller_logo'] = 'image/'.$seller['store_logo'];
				}
				if(isset($seller['firstname'])) {
					$data['firstname'] = $seller['firstname'];
				} 
			
				if(isset($seller['lastname'])) {
					$data['lastname']  = $seller['lastname'];
				}
				}
				$data['profile'] 			= $this->url->link('extension/account/purpletree_multivendor/sellerstore', '', true);
				$data['storename'] 				= '';
			if(isset($seller['store_name'])) {
				$data['storename'] 				= $seller['store_name'];
			}
			$data['storeurl'] 				= 	'';
			if(isset($seller["id"])) {
				$data['storeurl'] 				= $this->url->link('extension/account/purpletree_multivendor/sellerstore/storeview&seller_store_id='.$seller["id"], '', true);
			}
				$data['currency'] = $this->load->controller('extension/account/purpletree_multivendor/common/currency');
			}
			$data['dashboardpageurl'] 			= $this->url->link('extension/account/purpletree_multivendor/dashboardicons', '', true);
			$data['logout'] 				= $this->url->link('account/logout', '', true);
			
			$this->load->model('tool/image');
			$data['image'] = $this->model_tool_image->resize('catalog/no_image_seller.png', 40, 40);
				$data['sellerprofile'] 			= $this->url->link('account/edit', '', true);
			$data['direction'] = $this->language->get('direction');
		
			$data['lang'] = $this->language->get('code');
			$data['language'] = $this->load->controller('extension/account/purpletree_multivendor/common/language');
			
			$data['stylespts'] = $this->document->getStylespts();
			$data['scriptspts'] = $this->document->getScriptspts('header');
			$data['heading_title1'] = $this->document->getTitle();
		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}
		$data['base'] = $server;
		return $this->load->view('account/purpletree_multivendor/header', $data);
	}
}