<?php
class ControllerExtensionAccountPurpletreeMultivendorTablemanager extends Controller{
		private $error = array();
		
		public function index(){
			if (!$this->customer->isLogged()) {
				$this->session->data['redirect'] = $this->url->link('extension/account/purpletree_multivendor/tablemanager', '', true);
				
				$this->response->redirect($this->url->link('account/login', '', true));
			}
			
			$this->load->model('extension/purpletree_multivendor/dashboard');
		
			$this->model_extension_purpletree_multivendor_dashboard->checkSellerApproval();
			
			$store_detail = $this->customer->isSeller();
			
			if(!isset($store_detail['store_status'])){
				$this->response->redirect($this->url->link('account/account', '', true));
				}else{
				if(isset($store_detail['store_status']) && $store_detail['multi_store_id'] != $this->config->get('config_store_id')){	
					$this->response->redirect($this->url->link('account/account','', true));
				}
			}
			
			$this->load->language('purpletree_multivendor/tablemanager');
			
			$this->document->setTitle($this->language->get('heading_title'));
			
			$this->load->model('extension/purpletree_multivendor/tablemanager');
			
			$this->getList();
		}	
	public function qrdownload2(){
		
			include_once(DIR_SYSTEM . 'library/phpqrcode/qrlib.php');
		  // how to save PNG codes to server
    	if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}
    $tempDir = DIR_IMAGE.'qrcodes/';
    $store_detail = $this->customer->isSeller();
		// Store the cipher method 
		//$ciphering = "AES-128-CTR"; 
  
		// Use OpenSSl Encryption method 
		//$iv_length = openssl_cipher_iv_length($ciphering); 
		//$options = 0; 
  
		// Non-NULL Initialization Vector for encryption 
		//$encryption_iv = '1234567891011121'; 
  
		// Store the encryption key 
	//	$encryption_key = "encryptedtableid"; 
		
	//$encrypted_table = openssl_encrypt($this->request->get['table_id'], $ciphering,  $encryption_key, $options, $encryption_iv); 
    $codeContents = HTTPS_SERVER.'index.php?route=extension/account/purpletree_multivendor/sellerstore/storeview&seller_store_id='.$store_detail['id'];
    
    // we need to generate filename somehow, 
    // with md5 or with database ID used to obtains $codeContents...
    $fileName = '249_file_'.md5($store_detail['id']).'.png';
    
    $pngAbsoluteFilePath = $tempDir.$fileName;
	$this->load->model('extension/purpletree_multivendor/vendor');
		$seller_info = $this->model_extension_purpletree_multivendor_vendor->getStore($store_detail['id']);
	//trigger_error(print_r($seller_info,true));

		
	   ob_start();
    $urlRelativeFilePath = '/image/qrcodes/'.$fileName;
      // end of processing here
        $debugLog = ob_get_contents();
    // generating
     
        QRcode::png($codeContents, $pngAbsoluteFilePath,QR_ECLEVEL_M,10);
		 $QR = imagecreatefrompng($pngAbsoluteFilePath);

       
    
   
    // displaying
	 $logotmp = imagecreatefrompng(DIR_IMAGE.'logotmp.png');
	 $logo = imagecreatefrompng(DIR_IMAGE.$seller_info['store_logo']);
	 list($width1, $height1) = getimagesize(DIR_IMAGE.'logotmp.png');
	  list($widthlogo, $heightlogo) = getimagesize(DIR_IMAGE.$seller_info['store_logo']);
	 $out2 = imagecreatetruecolor($width1, $height1);
	 $whity = imagecolorallocate($out2, 255, 255, 255);
	 imagefill($out2, 0, 0, $whity);
	 imagecopyresampled($out2, $logotmp, 0, 0, 0, 0, $width1, $height1, $width1, $height1);
	 imagecopyresampled($out2, $logo, 0, $height1/$heightlogo, 0, 0, $width1, $height1, $widthlogo, $heightlogo);
   // echo '<img src="'.$urlRelativeFilePath.'" />';	
   //header('Content-Type: image/jpeg');

		//$png = imagecreatefrompng($pngAbsoluteFilePath);
		$bg = imagecreatefromjpeg(DIR_IMAGE.'qrtemp2.jpg');

	list($width, $height) = getimagesize(DIR_IMAGE.'qrtemp2.jpg');
	list($newwidth, $newheight) = getimagesize($pngAbsoluteFilePath);
	$out = imagecreatetruecolor($width, $height);
	
	imagecopyresampled($out, $bg, 0, 0, 0, 0, $width, $height, $width, $height);
	imagecopyresampled($out, $QR, $width/5 - 100, $height/4 - 85, 0, 0, $newwidth, $newheight, $newwidth, $newheight);
	imagecopyresampled($out, $out2, $width/4 + 60, $height/3 + 40, 0, 0, $width1, $height1, $width1, $height1);


	imagejpeg($out, DIR_IMAGE.'qrcodes/'.$fileName, 100);
	
	 
	$json= array();
	$json['filename'] = $fileName;
	$json['filepath'] = '/image/qrcodes/';
	echo json_encode($json);
	 
		}		
	public function qrdownload(){
		
			include_once(DIR_SYSTEM . 'library/phpqrcode/qrlib.php');
		  // how to save PNG codes to server
    	if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}
    $tempDir = DIR_IMAGE.'qrcodes/';
    $store_detail = $this->customer->isSeller();
		// Store the cipher method 
		$ciphering = "AES-128-CTR"; 
  
		// Use OpenSSl Encryption method 
		$iv_length = openssl_cipher_iv_length($ciphering); 
		$options = 0; 
  
		// Non-NULL Initialization Vector for encryption 
		$encryption_iv = '1234567891011121'; 
  
		// Store the encryption key 
		$encryption_key = "encryptedtableid"; 
		
	$encrypted_table = openssl_encrypt($this->request->get['table_id'], $ciphering,  $encryption_key, $options, $encryption_iv); 
    $codeContents = HTTPS_SERVER.'index.php?route=extension/account/purpletree_multivendor/sellerstore/storeview&seller_store_id='.$store_detail['id'].'&table_id='.$encrypted_table;
    
    // we need to generate filename somehow, 
    // with md5 or with database ID used to obtains $codeContents...
    $fileName = '249_file_'.md5($this->request->get['table_id']).'.png';
    
    $pngAbsoluteFilePath = $tempDir.$fileName;
	$this->load->model('extension/purpletree_multivendor/vendor');
		$seller_info = $this->model_extension_purpletree_multivendor_vendor->getStore($store_detail['id']);
	//trigger_error(print_r($seller_info,true));

		
	   ob_start();
    $urlRelativeFilePath = '/image/qrcodes/'.$fileName;
      // end of processing here
        $debugLog = ob_get_contents();
    // generating
     
        QRcode::png($codeContents, $pngAbsoluteFilePath,QR_ECLEVEL_M,9);
		 $QR = imagecreatefrompng($pngAbsoluteFilePath);

       
    
   
    // displaying
	 $logotmp = imagecreatefrompng(DIR_IMAGE.'logotmp.png');
	 $logo = imagecreatefrompng(DIR_IMAGE.$seller_info['store_logo']);
	 list($width1, $height1) = getimagesize(DIR_IMAGE.'logotmp.png');
	  list($widthlogo, $heightlogo) = getimagesize(DIR_IMAGE.$seller_info['store_logo']);
	 $out2 = imagecreatetruecolor($width1, $height1);
	 $whity = imagecolorallocate($out2, 255, 255, 255);
	 imagefill($out2, 0, 0, $whity);
	 imagecopyresampled($out2, $logotmp, 0, 0, 0, 0, $width1, $height1, $width1, $height1);
	 imagecopyresampled($out2, $logo, 0, $height1/$heightlogo, 0, 0, $width1, $height1, $widthlogo, $heightlogo);
   // echo '<img src="'.$urlRelativeFilePath.'" />';	
   //header('Content-Type: image/jpeg');

		//$png = imagecreatefrompng($pngAbsoluteFilePath);
		$bg = imagecreatefromjpeg(DIR_IMAGE.'qrtemp.jpg');

	list($width, $height) = getimagesize(DIR_IMAGE.'qrtemp.jpg');
	list($newwidth, $newheight) = getimagesize($pngAbsoluteFilePath);
	$out = imagecreatetruecolor($width, $height);
	
	imagecopyresampled($out, $bg, 0, 0, 0, 0, $width, $height, $width, $height);
	imagecopyresampled($out, $QR, $width/5 - 94, $height/4 - 75, 0, 0, $newwidth, $newheight, $newwidth, $newheight);
	imagecopyresampled($out, $out2, $width/4 + 60, $height/3 + 30, 0, 0, $width1, $height1, $width1, $height1);

	// Allocate A Color For The Text
	$white = imagecolorallocate($out, 0, 0, 0);

// Set Path to Font File
		$font_path = DIR_TEMPLATE.'arial.ttf';

			$table_no = $this->db->query("SELECT table_no FROM ". DB_PREFIX."table_manger WHERE id = ".$this->request->get['table_id']."")->row;
// Set Text to Be Printed On Image
		$text = "(".$table_no['table_no'].")";
		//$text = "(10)";
		$numbercount = strlen($table_no['table_no']);
		if ($numbercount == 1) {$numtext = + 10 ; }
		if ($numbercount == 2) {$numtext = + 50 ; }
		imagettftext($out, 130, 0,$width/5 - $numtext, $height/1.1 - 30, $white, $font_path, $text);
		
	imagejpeg($out, DIR_IMAGE.'qrcodes/'.$fileName, 100);
	
	 
	$json= array();
	$json['filename'] = $fileName;
	$json['filepath'] = '/image/qrcodes/';
	echo json_encode($json);
	 
		}
		public function add() {
			
			if (!$this->customer->isLogged()) {
				$this->session->data['redirect'] = $this->url->link('extension/account/purpletree_multivendor/tablemanager', '', true);
				
				$this->response->redirect($this->url->link('account/login', '', true));
			}
			$this->load->model('extension/purpletree_multivendor/dashboard');
			
			
			$this->model_extension_purpletree_multivendor_dashboard->checkSellerApproval();
			
			$store_detail = $this->customer->isSeller();
			if(!isset($store_detail['store_status'])){
				$this->response->redirect($this->url->link('account/account', '', true));
			}
			
			if(!$this->customer->validateSeller()) {
				$this->load->language('purpletree_multivendor/ptsmultivendor');
				$this->session->data['error_warning'] = $this->language->get('error_license');			
				$this->response->redirect($this->url->link('extension/account/purpletree_multivendor/tablemanager', '', true));
			}
			$this->load->language('purpletree_multivendor/tablemanager');
			
			$this->document->setTitle($this->language->get('heading_title'));
			
			$this->document->addScriptpts('catalog/view/javascript/purpletree_style.js');
		
			$this->load->model('extension/purpletree_multivendor/tablemanager');
		
			$total_store_Table = $this->model_extension_purpletree_multivendor_tablemanager->sellerTotalTable($this->customer->getId());
				
				if($this->config->get('module_purpletree_multivendor_multiple_subscription_plan_active')){
					$total_plan_Table = $this->model_extension_purpletree_multivendor_tablemanager->getNoOfTableForMultiplePlan($this->customer->getId());
					} else {
					$total_plan_Table = $this->model_extension_purpletree_multivendor_tablemanager->getNoOfTable($this->customer->getId());
					
				}
			
				if($total_store_Table['total_table']>0){
					$store_table=$total_store_Table['total_table'];			
					} else {
					$store_table=0;	
				}
				
				if($total_plan_Table['no_of_tables']>0){
					$plan_table=$total_plan_Table['no_of_tables'];			
					} else {
					$plan_table=0;	
				}
				
				if(isset($plan_table)){
					
					if($plan_table > $store_table){
						
						} else {
						
						$this->session->data['error_warning']=$this->language->get('error_subscription_plan_limit');
						
						$this->response->redirect($this->url->link('extension/account/purpletree_multivendor/tablemanager', '', true));	
					}
					} else {
					
					$this->session->data['error_warning']=$this->language->get('error_subscription_plan');
					$this->response->redirect($this->url->link('extension/account/purpletree_multivendor/tablemanager', '', true));
				}
			
			
			if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
				
				
				if($this->validateForm()) {

					$this->request->post['seller_id'] = $this->customer->getId();
					$this->request->post['product_store'] = array($this->config->get('config_store_id'));  

					$this->model_extension_purpletree_multivendor_tablemanager->addTable($this->request->post);
					
					$this->session->data['success'] = $this->language->get('text_success');
					$this->response->redirect($this->url->link('extension/account/purpletree_multivendor/tablemanager','', true));
			}
			
			}
			$this->getForm();
		
	} 
	
	
	
	public function enabledtable(){
			
			if (!$this->customer->isLogged()) {
				$this->session->data['redirect'] = $this->url->link('extension/account/purpletree_multivendor/tablemanager', '', true);
				
				$this->response->redirect($this->url->link('account/login', '', true));
			}
			$this->load->model('extension/purpletree_multivendor/dashboard');
			
			$this->model_extension_purpletree_multivendor_dashboard->checkSellerApproval();
			
			$store_detail = $this->customer->isSeller();
			if(!isset($store_detail['store_status'])){
				$this->response->redirect($this->url->link('account/account', '', true));
			}
			
			if(!$this->customer->validateSeller()) {
				$this->load->language('purpletree_multivendor/ptsmultivendor');
				$this->session->data['error_warning'] = $this->language->get('error_license');			
				$this->response->redirect($this->url->link('extension/account/purpletree_multivendor/sellerproduct', '', true));
			}
			$this->load->language('purpletree_multivendor/tablemanager');
			
			$this->document->setTitle($this->language->get('heading_title'));
			
			$this->load->model('extension/purpletree_multivendor/tablemanager');
			
			if (isset($this->request->post['selected'])) {
				foreach ($this->request->post['selected'] as $table_id) {
					$this->model_extension_purpletree_multivendor_tablemanager->enabledtable($table_id);
				}
				
				$this->session->data['success'] = $this->language->get('text_success');
				
				$url = '';
				
				
				if (isset($this->request->get['sort'])) {
					$url .= '&sort=' . $this->request->get['sort'];
				}
				
				if (isset($this->request->get['order'])) {
					$url .= '&order=' . $this->request->get['order'];
				}
				
				if (isset($this->request->get['page'])) {
					$url .= '&page=' . $this->request->get['page'];
				}
				
				$this->response->redirect($this->url->link('extension/account/purpletree_multivendor/tablemanager','', true));
			}
			
			$this->getList();
		}
		
		
		
		
		
		public function disabledtable(){
			
			if (!$this->customer->isLogged()) {
				$this->session->data['redirect'] = $this->url->link('extension/account/purpletree_multivendor/tablemanager', '', true);
				
				$this->response->redirect($this->url->link('account/login', '', true));
			}
			$this->load->model('extension/purpletree_multivendor/dashboard');
			
			$this->model_extension_purpletree_multivendor_dashboard->checkSellerApproval();
			
			$store_detail = $this->customer->isSeller();
			if(!isset($store_detail['store_status'])){
				$this->response->redirect($this->url->link('account/account', '', true));
			}
			
			if(!$this->customer->validateSeller()) {
				$this->load->language('purpletree_multivendor/ptsmultivendor');
				$this->session->data['error_warning'] = $this->language->get('error_license');			
				$this->response->redirect($this->url->link('extension/account/purpletree_multivendor/sellerproduct', '', true));
			}
			$this->load->language('purpletree_multivendor/tablemanager');
			
			$this->document->setTitle($this->language->get('heading_title'));
			
			$this->load->model('extension/purpletree_multivendor/tablemanager');
			
			if (isset($this->request->post['selected'])) {
				foreach ($this->request->post['selected'] as $table_id) {
					$this->model_extension_purpletree_multivendor_tablemanager->disabledtable($table_id);
				}
				
				$this->session->data['success'] = $this->language->get('text_success');
				
				$url = '';
				
				
				if (isset($this->request->get['sort'])) {
					$url .= '&sort=' . $this->request->get['sort'];
				}
				
				if (isset($this->request->get['order'])) {
					$url .= '&order=' . $this->request->get['order'];
				}
				
				if (isset($this->request->get['page'])) {
					$url .= '&page=' . $this->request->get['page'];
				}
				
				$this->response->redirect($this->url->link('extension/account/purpletree_multivendor/tablemanager','', true));
			}
			
			$this->getList();
		}
		
		
		
		
	
	
		
		public function edit() {
			if (!$this->customer->isLogged()) {
				$this->session->data['redirect'] = $this->url->link('extension/account/purpletree_multivendor/tablemanager', '', true);
				
				$this->response->redirect($this->url->link('account/login', '', true));
			}
			$this->load->model('extension/purpletree_multivendor/dashboard');
			
			$this->model_extension_purpletree_multivendor_dashboard->checkSellerApproval();
			
			$store_detail = $this->customer->isSeller();
			if(!isset($store_detail['store_status'])){
				$this->response->redirect($this->url->link('account/account', '', true));
			}
			
			if(!$this->customer->validateSeller()) {
				$this->load->language('purpletree_multivendor/ptsmultivendor');
				$this->session->data['error_warning'] = $this->language->get('error_license');			
				$this->response->redirect($this->url->link('extension/account/purpletree_multivendor/tablemanager', '', true));
			}
			
			$this->load->language('purpletree_multivendor/tablemanager');
			
			$this->document->setTitle($this->language->get('heading_title'));
			
			$this->document->addScriptpts('catalog/view/javascript/purpletree_style.js');
			$this->load->model('extension/purpletree_multivendor/tablemanager');
			$plan_status=array();
			$plan_status = $this->model_extension_purpletree_multivendor_tablemanager->sellerTotalPlanStatus($this->customer->getId());
			
			if($this->config->get('module_purpletree_multivendor_subscription_plans')){
				if($plan_status['status_id']==0){
					
					$this->session->data['error_warning']= $this->language->get('error_subscription_plan_status');
					
					$this->response->redirect($this->url->link('extension/account/purpletree_multivendor/tablemanager', '', true));			
				} 
			}
			if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
				
				
				if($this->validateForm()) {
					
					$this->request->post['seller_id'] = $this->customer->getId();
					$product_infoq = $this->model_extension_purpletree_multivendor_tablemanager->getTable($this->request->get['table_id'],$this->request->post['seller_id']);
					
					if($this->config->get('module_purpletree_multivendor_subscription_plans')){
						$plans=array();
						$plans= $this->model_extension_purpletree_multivendor_tablemanager->sellerActiveTable($this->request->post['seller_id'],$this->request->get['table_id']);
						
						if($plans){
							$this->model_extension_purpletree_multivendor_tablemanager->editTable($this->request->get['table_id'],$this->request->post);
							$this->session->data['success'] = $this->language->get('text_success');
							$url = '';
							
							
							if (isset($this->request->get['sort'])) {
								$url .= '&sort=' . $this->request->get['sort'];
							}
							
							if (isset($this->request->get['order'])) {
								$url .= '&order=' . $this->request->get['order'];
							}
							
							if (isset($this->request->get['page'])) {
								$url .= '&page=' . $this->request->get['page'];
							}
							$this->response->redirect($this->url->link('extension/account/purpletree_multivendor/tablemanager', $url, true));
							} else {
							$this->session->data['error_warning']= 'Product not Allowed';
							$this->response->redirect($this->url->link('extension/account/purpletree_multivendor/tablemanager/edit','&table_id=' .$this->request->get['table_id'], true));			
							
						}		
						}else{
						$this->model_extension_purpletree_multivendor_tablemanager->ediTable($this->request->get['table_id'],$this->request->post);
						$this->session->data['success'] = $this->language->get('text_success');
						$url = '';
						
						if (isset($this->request->get['sort'])) {
							$url .= '&sort=' . $this->request->get['sort'];
						}
						
						if (isset($this->request->get['order'])) {
							$url .= '&order=' . $this->request->get['order'];
						}
						
						if (isset($this->request->get['page'])) {
							$url .= '&page=' . $this->request->get['page'];
						}
						$this->response->redirect($this->url->link('extension/account/purpletree_multivendor/tablemanager',$url, true));
					}
				}
			}
			$this->getForm();
		}
		
		
		protected function getForm() {
			
			$this->load->model('extension/purpletree_multivendor/dashboard');
			
			$this->model_extension_purpletree_multivendor_dashboard->checkSellerApproval();
			
			$data['heading_title'] = $this->language->get('heading_title');
			$data['title_quick_edit'] = $this->language->get('title_quick_edit');
			$data['text_form'] = !isset($this->request->get['product_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
			$data['text_enabled'] = $this->language->get('text_enabled');
			$data['text_disabled'] = $this->language->get('text_disabled');
			$data['text_none'] = $this->language->get('text_none');
			$data['text_yes'] = $this->language->get('text_yes');
			$data['text_no'] = $this->language->get('text_no');
			$data['text_plus'] = $this->language->get('text_plus');
			$data['text_minus'] = $this->language->get('text_minus');
			$data['text_default'] = $this->language->get('text_default');
			$data['text_option'] = $this->language->get('text_option');
			$data['text_option_value'] = $this->language->get('text_option_value');
			$data['text_select'] = $this->language->get('text_select');
			$data['text_percent'] = $this->language->get('text_percent');
			$data['text_amount'] = $this->language->get('text_amount');
			$data['text_enabled'] = $this->language->get('text_enabled');
			$data['text_disabled'] = $this->language->get('text_disabled');
			$data['text_fixed'] = $this->language->get('text_fixed');
			$data['text_percentage'] = $this->language->get('text_percentage');
			
			$data['entry_name'] = $this->language->get('entry_name');
			$data['entry_description'] = $this->language->get('entry_description');
			
			
			$data['button_save'] = $this->language->get('button_save');
			$data['button_cancel'] = $this->language->get('button_cancel');
			
			$data['button_remove'] = $this->language->get('button_remove');
			$data['button_continue'] = $this->language->get('button_continue');
			$data['button_back'] = $this->language->get('button_back');
			
			$data['tab_general'] = $this->language->get('tab_general');
			
			$data['text_confirm'] = $this->language->get('text_confirm');
			$data['text_not_applicable'] = $this->language->get('text_not_applicable');
			$data['entry_subscription_featured_product'] = $this->language->get('entry_subscription_featured_product');
			$data['entry_subscription_category_featured_product'] = $this->language->get('entry_subscription_category_featured_product');
			$data['tab_quick_order'] = $this->language->get('tab_quick_order');
			$data['entry_quick_order'] = $this->language->get('entry_quick_order');
			$data['entry_delivery_address'] = $this->language->get('entry_delivery_address');
			
			
			if (isset($this->error['warning'])) {
				$data['error_warning'] = $this->error['warning'];
			}
			elseif (isset($this->session->data['error_warning'])) {
				$data['error_warning'] = $this->session->data['error_warning'];
				unset($this->session->data['error_warning']);
				} else {
				$data['error_warning'] = '';
			}
			
			if (isset($this->error['name'])) {
				$data['error_name'] = $this->error['name'];
				} else {
				$data['error_name'] = array();
			}
		
			
			
			$url = '';
			if($this->config->get('module_purpletree_multivendor_subscription_plans')) {
				if (isset($this->error['error_category_featured_product_plan_id'])) {
					$data['error_category_featured_product_plan_id'] = $this->error['error_category_featured_product_plan_id'];
					} else {
					$data['error_category_featured_product_plan_id'] = '';
				}
				if (isset($this->error['error_featured_product_plan_id'])) {
					
					$data['error_featured_product_plan_id'] = $this->error['error_featured_product_plan_id'];
					} else {
					
					$data['error_featured_product_plan_id'] = '';
				}
			}
		
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}
			
			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}
			
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			
			$data['breadcrumbs'] = array();
			
			$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home','',true)
			);
			
			$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/account/purpletree_multivendor/tablemanager',  $url, true)
			);
			$seller_id = $this->customer->getId();
			$data['module_purpletree_multivendor_subscription_plans'] = $this->config->get('module_purpletree_multivendor_subscription_plans');
			
		
		
			if (!isset($this->request->get['table_id'])) {
				$data['action'] = $this->url->link('extension/account/purpletree_multivendor/tablemanager/add',$url,true);
				} else {
				$data['action'] = $this->url->link('extension/account/purpletree_multivendor/tablemanager/edit','&table_id=' . $this->request->get['table_id'] .$url, true);
			}
			$seller_id = $this->customer->getId();
			$data['cancel'] = $this->url->link('extension/account/purpletree_multivendor/tablemanager',$url, true);
			
			if (isset($this->request->get['table_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
				$product_info = $this->model_extension_purpletree_multivendor_tablemanager->getTable($this->request->get['table_id'],$seller_id); 
			}
			
			$this->load->model('localisation/language');
			
			$data['languages'] = $this->model_localisation_language->getLanguages();
			foreach($data['languages'] as $key => $value) {
				$data['languages'][$key]['activetab'] = '';
			}
			foreach($data['languages'] as $key => $value) {
				$data['languages'][$key]['activetab'] = 'active';
				break;
			}
			
			
			$data['related_approval'] = $this->config->get('module_purpletree_multivendor_allow_related_product');
			$data['limit_approval'] = $this->config->get('module_purpletree_multivendor_product_limit');
			
			$data['seller_id'] = $seller_id;
			$data['seller_name'] = $this->customer->getFirstName()." ".$this->customer->getLastName();
			
			
			if (isset($this->request->post['product_description'])) {
				$data['product_description'] = $this->request->post['product_description'];
				} elseif (isset($this->request->get['table_id'])) {
				$data['product_description'] = $this->model_extension_purpletree_multivendor_tablemanager->getTableDescriptions($this->request->get['table_id']);
				} else {
				$data['product_description'] = array();
			}
			
		
			$this->load->model('setting/store');
			
		
			$data['back'] = $this->url->link('extension/account/purpletree_multivendor/tablemanager', '', true);
			
			$data['ver']=VERSION;
			if($data['ver']=='3.1.0.0_b'){
				$this->document->addScriptpts('admin/view/javascript/ckeditor/ckeditor.js');
				$this->document->addScriptpts('admin/view/javascript/ckeditor/adapters/jquery.js');
			}
			$this->document->addScriptpts('catalog/view/javascript/purpletree/jquery/datetimepicker/moment/moment.min.js'); 
			$this->document->addScriptpts('catalog/view/javascript/purpletree/jquery/datetimepicker/moment/moment-with-locales.min.js'); 
			$this->document->addScriptpts('catalog/view/javascript/purpletree/jquery/datetimepicker/bootstrap-datetimepicker.min.js'); 
			$this->document->addStylepts('catalog/view/javascript/purpletree/jquery/datetimepicker/bootstrap-datetimepicker.min.css'); 
			$this->document->addStylepts('catalog/view/javascript/purpletree/codemirror/lib/codemirror.css'); 
			$this->document->addStylepts('catalog/view/javascript/purpletree/codemirror/theme/monokai.css'); 
			$this->document->addScriptpts('catalog/view/javascript/purpletree/codemirror/lib/codemirror.js'); 
			$this->document->addScriptpts('catalog/view/javascript/purpletree/codemirror/lib/xml.js'); 
			$this->document->addScriptpts('catalog/view/javascript/purpletree/codemirror/lib/formatting.js'); 
			if($data['ver'] =='3.1.0.0_b') { } else {	  
				$this->document->addScriptpts('catalog/view/javascript/purpletree/summernote/summernote.js'); 
				$this->document->addStylepts('catalog/view/javascript/purpletree/summernote/summernote.css'); 
				$this->document->addScriptpts('catalog/view/javascript/purpletree/summernote/summernote-image-attributes.js'); 
				$this->document->addScriptpts('catalog/view/javascript/purpletree/summernote/opencart.js'); 
			}		
			$data['column_left'] = $this->load->controller('extension/account/purpletree_multivendor/common/column_left');
			$data['footer'] = $this->load->controller('extension/account/purpletree_multivendor/common/footer');
			$data['header'] = $this->load->controller('extension/account/purpletree_multivendor/common/header');
			
			if($this->config->get('module_purpletree_multivendor_hide_seller_product_tab')) {
				$this->response->setOutput($this->load->view('account/purpletree_multivendor/product_form_hideninfo', $data));
				}else{
				$this->response->setOutput($this->load->view('account/purpletree_multivendor/tablemanager_form', $data));
			}
		}
		
		public function delete() {
			if (!$this->customer->isLogged()) {
				$this->session->data['redirect'] = $this->url->link('extension/account/purpletree_multivendor/tablemanager', '', true);
				
				$this->response->redirect($this->url->link('account/login', '', true));
			}
			$this->load->model('extension/purpletree_multivendor/dashboard');
			
			$this->model_extension_purpletree_multivendor_dashboard->checkSellerApproval();
			
			$store_detail = $this->customer->isSeller();
			if(!isset($store_detail['store_status'])){
				$this->response->redirect($this->url->link('account/account', '', true));
			}
			
			if(!$this->customer->validateSeller()) {
				$this->load->language('purpletree_multivendor/ptsmultivendor');
				$this->session->data['error_warning'] = $this->language->get('error_license');			
				$this->response->redirect($this->url->link('extension/account/purpletree_multivendor/sellerproduct', '', true));
			}
			$this->load->language('purpletree_multivendor/tablemanager');
			
			$this->document->setTitle($this->language->get('heading_title'));
			
			$this->load->model('extension/purpletree_multivendor/tablemanager');
			
			if (isset($this->request->post['selected'])) {
				foreach ($this->request->post['selected'] as $table_id) {
					$this->model_extension_purpletree_multivendor_tablemanager->deleteTable($table_id);
				}
				
				$this->session->data['success'] = $this->language->get('text_success');
				
				$url = '';
				
				
				if (isset($this->request->get['sort'])) {
					$url .= '&sort=' . $this->request->get['sort'];
				}
				
				if (isset($this->request->get['order'])) {
					$url .= '&order=' . $this->request->get['order'];
				}
				
				if (isset($this->request->get['page'])) {
					$url .= '&page=' . $this->request->get['page'];
				}
				
				$this->response->redirect($this->url->link('extension/account/purpletree_multivendor/tablemanager','', true));
			}
			
			$this->getList();
		}
		
		protected function getList(){
			
			$this->load->model('extension/purpletree_multivendor/dashboard');
			
			$this->model_extension_purpletree_multivendor_dashboard->checkSellerApproval();
			
			$data['heading_title'] = $this->language->get('heading_title');
			$data['text_all'] = $this->language->get('text_all');
			
			if (isset($this->request->get['order'])) {
				$order = $this->request->get['order'];
				} else {
				$order = 'ASC';
			}
			
			if (isset($this->request->get['page'])) {
				$page = $this->request->get['page'];
				} else {
				$page = 1;
			}
			
			$url = '';
			$data['sorts_order'] = array();
			
			$data['sorts_order'][] = array(
			'text'  => $this->language->get('text_name_asc'),
			'value' => 'ASC',
			'href'  => $this->url->link('extension/account/purpletree_multivendor/tablemanager', '&order=ASC' . $url,true)
			);
			
			$data['sorts_order'][] = array(
			'text'  => $this->language->get('text_name_desc'),
			'value' => 'DESC',
			'href'  => $this->url->link('extension/account/purpletree_multivendor/tablemanager','&order=DESC' . $url,true)
			);
			
			
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}
			
			
			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}
			
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			if (isset($this->request->get['filter_status'])) {
				$filter_status = $this->request->get['filter_status'];
				} else {
				$filter_status = null;
			}
			
			$data['breadcrumbs'] = array();
			
			$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home','',true)
			);
			
			$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/account/purpletree_multivendor/tablemanager', $url, true)
			);
			
		
			if (isset($this->error['warning'])) {
				$data['error_warning'] = $this->error['warning'];
				} else {
				$data['error_warning'] = '';
			}
			
			if (isset($this->session->data['success'])) {
				$data['success'] = $this->session->data['success'];
				
				unset($this->session->data['success']);
				} else {
				$data['success'] = '';
			}
			
			if (isset($this->session->data['error_warning'])) {
				$data['error_warning'] = $this->session->data['error_warning'];
				
				unset($this->session->data['error_warning']);
				} else {
				$data['error_warning'] = '';
			}
			$seller_plan_sataus = 0;
			if($this->config->get('module_purpletree_multivendor_subscription_plans')){
				$total_store_table=array();
				$total_plan_table=array();
				
				$total_store_table = $this->model_extension_purpletree_multivendor_tablemanager->sellerTotalTable($this->customer->getId());
				
				
				if($this->config->get('module_purpletree_multivendor_multiple_subscription_plan_active')){
					$total_plan_table = $this->model_extension_purpletree_multivendor_tablemanager->getNoOfTableForMultiplePlan($this->customer->getId());
					} else {
					$total_plan_table = $this->model_extension_purpletree_multivendor_tablemanager->getNoOfTable($this->customer->getId());
					
				}
				
				if($total_store_table['total_table']>0){
					$store_table=$total_store_table['total_table'];			
					} else {
					$store_table=0;	
				}
				
				if($total_plan_table['no_of_tables']>0){
					$plan_table=$total_plan_table['no_of_tables'];			
					} else {
					$plan_table=0;	
				}
				
				
						
						$data['add'] = $this->url->link('extension/account/purpletree_multivendor/tablemanager/add', $url, true);
							
						
					
					
				}
				
				$getSsellerplanStatus = $this->model_extension_purpletree_multivendor_tablemanager->getSsellerplanStatus($this->customer->isLogged());
				$invoiceStatus = $this->model_extension_purpletree_multivendor_tablemanager->getInvoiceStatus($this->customer->getId());
				
				if(!$getSsellerplanStatus || ($invoiceStatus==NULL || $invoiceStatus!=2) ) {
					$this->session->data['error_warning']=$this->language->get('error_subscription_plan');
					$data['add'] = $this->url->link('extension/account/purpletree_multivendor/tablemanager', $url, true);
			
					$seller_plan_sataus = 1;
        			
				}	
				
			
			$data['delete'] = $this->url->link('extension/account/purpletree_multivendor/tablemanager/delete', $url, true);
			$data['enabledtable'] = $this->url->link('extension/account/purpletree_multivendor/tablemanager/enabledtable', '', true);
			
			$data['disabledtable'] = $this->url->link('extension/account/purpletree_multivendor/tablemanager/disabledtable', '', true);
			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}
			$data['tables'] = array();
			
			$sort = ''; 
			$filter_data = array(
			'sort'            => $sort,
			'order'           => $order,
			'filter_status'   => $filter_status,
			'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'           => $this->config->get('config_limit_admin'),
			'seller_id'		  => $this->customer->getId()	
			);
			$this->load->model('localisation/language');
           $data['languages'] = $this->model_localisation_language->getLanguages();
		  
			foreach($data['languages'] as $key => $value) {
				$data['languages'][$key]['activetab'] = '';
			}
			foreach($data['languages'] as $key => $value) {
				$data['languages'][$key]['activetab'] = 'active';
				break;
			}
			if($this->config->get('module_purpletree_multivendor_products_view')== !null){
			      $data['product_view'] = $this->config->get('module_purpletree_multivendor_products_view');
				}else{
				  $data['product_view'] = 0;
				}
			$product_total = $this->model_extension_purpletree_multivendor_tablemanager->getTotalSellerTables($filter_data);
			$seller_id = $this->customer->getId();	
			$results = $this->model_extension_purpletree_multivendor_tablemanager->getSellerTables($filter_data);
			
			
			foreach ($results as $result) {
				
				
			
				$data['tables'][] = array(
				'table_id' => $result['id'],
				'table_no'       => $result['table_no'],
				'status'     => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'description'      => $result['description'],
				'seller_plan_sataus'      => $seller_plan_sataus,
				'edit'       => $this->url->link('extension/account/purpletree_multivendor/tablemanager/edit', '&table_id=' . $result['id'].$url, true),
				'qrdownload'       => $this->url->link('extension/account/purpletree_multivendor/tablemanager/qrdownload', '&table_id=' . $result['id'].$url, true)
				);
			}
				$data['qrdownload2'] = $this->url->link('extension/account/purpletree_multivendor/tablemanager/qrdownload2', '', true);
			if (isset($this->request->post['selected'])) {
				$data['selected'] = (array)$this->request->post['selected'];
				} else {
				$data['selected'] = array();
			}
			
			$url = '';
			$data['order'] = $order;
			
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}
			
			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}
			
			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}
			$this->load->model('extension/purpletree_multivendor/customer_group');
			$data['customer_groups'] = $this->model_extension_purpletree_multivendor_customer_group->getCustomerGroups();
			$seller_id = $this->customer->getId();
			$data['module_purpletree_multivendor_subscription_plans'] = $this->config->get('module_purpletree_multivendor_subscription_plans');
			if($this->config->get('module_purpletree_multivendor_subscription_plans')){		
				$data['product_plan_info'] = $this->model_extension_purpletree_multivendor_tablemanager->tablePlanInfo($seller_id);
			}
			$pagination = new Pagination();
			$pagination->total = $product_total;
			$pagination->page = $page;
			$pagination->limit = $this->config->get('config_limit_admin');
			$pagination->url = $this->url->link('extension/account/purpletree_multivendor/tablemanager', $url . '&page={page}', true);
			
			$data['pagination'] = $pagination->render();
			
			$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($product_total - $this->config->get('config_limit_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $product_total, ceil($product_total / $this->config->get('config_limit_admin')));
			
			$data['filter_status'] = $filter_status;
			$data['sort'] = $sort;
			$data['order'] = $order;
			$this->document->addScriptpts('catalog/view/javascript/purpletree/jquery/datetimepicker/moment/moment.min.js'); 
			$this->document->addScriptpts('catalog/view/javascript/purpletree/jquery/datetimepicker/moment/moment-with-locales.min.js'); 
			$this->document->addScriptpts('catalog/view/javascript/purpletree/jquery/datetimepicker/bootstrap-datetimepicker.min.js'); 
			$this->document->addStylepts('catalog/view/javascript/purpletree/jquery/datetimepicker/bootstrap-datetimepicker.min.css'); 
			$this->load->language('purpletree_multivendor/tablemanager');
			$data['text_product_enable']=$this->language->get('text_product_enable');
			$data['text_product_disable']=$this->language->get('text_product_disable');
			$data['p_edit']=$this->url->link('extension/account/purpletree_multivendor/tablemanager/edit',$url, true);
			$data['column_left'] = $this->load->controller('extension/account/purpletree_multivendor/common/column_left');
			$data['footer'] = $this->load->controller('extension/account/purpletree_multivendor/common/footer');
			$data['header'] = $this->load->controller('extension/account/purpletree_multivendor/common/header');
			$data['text_confirm'] = $this->language->get('text_confirm');
			$data['module_purpletree_multivendor_featured_enabled_hide_edit']=$this->config->get('module_purpletree_multivendor_featured_enabled_hide_edit');
			$this->response->setOutput($this->load->view('account/purpletree_multivendor/table_list', $data));
			
		}
		
		protected function validateForm() {
			
		 
			foreach ($this->request->post['product_description'] as $language_id => $value) {
				if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 10)) {
					$this->error['name'][$language_id] = $this->language->get('error_name');
				}
			
				
			}
		
			
			
			if ($this->error && !isset($this->error['warning'])) {
				$this->error['warning'] = $this->language->get('error_warning');
			}
			
			return !$this->error;
		}
		/////// category featured and featured product /////////
		public function change_is_featured() {
			if (!$this->customer->isLogged()) {
				
				$json['status'] = 'error'; 
				$json['message'] = 'NO login';
				
			}
			$this->load->model('extension/purpletree_multivendor/dashboard');
			
			$this->model_extension_purpletree_multivendor_dashboard->checkSellerApproval();
			
			$store_detail = $this->customer->isSeller();
			if(!isset($store_detail['store_status'])){
				$json['status'] = 'error'; 
				$json['message'] = 'Not seller';
			}
			
			if(!$this->customer->validateSeller()) {
				$this->load->language('purpletree_multivendor/ptsmultivendor');
				$json['status'] = 'error'; 
				$json['message'] = $this->language->get('error_license');
				} else {
				$this->load->language('purpletree_multivendor/sellerproduct');
				$json['status'] = 'error'; 
				$json['message'] = 'Something went wrong'; 
				if (isset($this->request->get['product_id']) && $this->request->get['product_id'] != '') {
					if ($this->request->get['value'] == 'true') {
						$value = 1;
						} else {
						$value = 0;
					}
					$this->load->model('extension/purpletree_multivendor/sellerproduct');
					
					if($this->config->get('module_purpletree_multivendor_subscription_plans')){
						if($value == 1) {
							$total_featured_product = $this->model_extension_purpletree_multivendor_tablemanager->sellerTotalFeaturedProduct($this->customer->getId(),$this->request->get['product_id']);
							if($total_featured_product==NULL){
								$total_featured_product =0;	
							}
							if($this->config->get('module_purpletree_multivendor_multiple_subscription_plan_active')){
								$allowed_featured_product = $this->model_extension_purpletree_multivendor_tablemanager->sellerAllowedFeaturedProductForMultiplePlan($this->customer->getId());
								} else {
								$allowed_featured_product = $this->model_extension_purpletree_multivendor_tablemanager->sellerAllowedFeaturedProduct($this->customer->getId());	
							}
							
							if($allowed_featured_product==NULL){
								$allowed_featured_product=0;			
							}
							
							
							if( $allowed_featured_product > $total_featured_product){
								$this->model_extension_purpletree_multivendor_tablemanager->change_is_featured($this->request->get['product_id'],$value);
								$json['status'] = 'success'; 	
								$json['message'] = ' successfully Assigned'; 
								} else {
								$json['status'] = 'error'; 	
								$json['message'] = $this->language->get('error_featured_product');
							}
							} else {
							$this->model_extension_purpletree_multivendor_tablemanager->change_is_featured($this->request->get['product_id'],$value); 
							$json['message'] = ' successfully unAssigned'; 
							$json['status'] = 'success'; 
						}			
						} else {
						
						$this->model_extension_purpletree_multivendor_tablemanager->change_is_featured($this->request->get['product_id'],$value);
						if($value == 1) {
							$json['message'] = ' successfully Assigned'; 
							} else {
							$json['message'] = ' successfully unAssigned'; 
							
						}
						$json['status'] = 'success'; 
						$json['value'] = $value; 
						$product_id='';
						if(isset($this->request->get['product_id'])){
							$product_id=$this->request->get['product_id'];
						}
						$json['product_id'] = $product_id; 
					}
					
				}
			}
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
		
		public function change_is_category_featured() {
			if (!$this->customer->isLogged()) {
				
				$json['status'] = 'error'; 
				$json['message'] = 'Error Login';
			}
			$this->load->model('extension/purpletree_multivendor/dashboard');
			
			$this->model_extension_purpletree_multivendor_dashboard->checkSellerApproval();
			
			$store_detail = $this->customer->isSeller();
			if(!isset($store_detail['store_status'])){
				
				$json['status'] = 'error'; 
				$json['message'] = 'Not seller';
			}
			
			if(!$this->customer->validateSeller()) {
				$this->load->language('purpletree_multivendor/ptsmultivendor');
				$json['status'] = 'error'; 
				$json['message'] = $this->language->get('error_license');
				} else {
				$this->load->language('purpletree_multivendor/sellerproduct');
				$json['status'] = 'error'; 
				$json['message'] = 'Something went wrong'; 
				if (isset($this->request->get['product_id']) && $this->request->get['product_id'] != '') {
					if ($this->request->get['value'] == 'true') {
						$value = 1;
						} else {
						$value = 0;
					}
					$this->load->model('extension/purpletree_multivendor/sellerproduct');
					
					if($this->config->get('module_purpletree_multivendor_subscription_plans')){
						if($value == 1) {
							
							$total_category_featured_product = $this->model_extension_purpletree_multivendor_tablemanager->sellerTotalCategpryFeaturedProduct($this->customer->getId(),$this->request->get['product_id']);
							if($total_category_featured_product==NULL){
								$total_category_featured_product =0;	
							}
							
							if($this->config->get('module_purpletree_multivendor_multiple_subscription_plan_active')){
								$allowed_category_featured_product = $this->model_extension_purpletree_multivendor_tablemanager->sellerAllowedCategoryFeaturedProductForMultiplePlan($this->customer->getId());
								} else {
								$allowed_category_featured_product = $this->model_extension_purpletree_multivendor_tablemanager->sellerAllowedCategoryFeaturedProduct($this->customer->getId());
							}
							if($allowed_category_featured_product==NULL){
								$allowed_category_featured_product=0; 			
							} 
							if( $allowed_category_featured_product > $total_category_featured_product){
								$this->model_extension_purpletree_multivendor_tablemanager->change_is_category_featured($this->request->get['product_id'],$value);
								$json['status'] = 'success'; 	
								$json['message'] = ' successfully Assigned'; 
								} else {
								$json['status'] = 'error'; 	
								$json['message'] = $this->language->get('error_category_featured_product');
							}
							} else {
							$this->model_extension_purpletree_multivendor_tablemanager->change_is_category_featured($this->request->get['product_id'],$value);
							$json['message'] = ' successfully unAssigned'; 
							$json['status'] = 'success'; 
						}			
						} else {
						
						$this->model_extension_purpletree_multivendor_tablemanager->change_is_category_featured($this->request->get['product_id'],$value);
						if($value == 1) {
							$json['message'] = ' successfully Assigned'; 
							} else {
							$json['message'] = ' successfully unAssigned'; 
							
						}
						$json['status'] = 'success'; 
					}
					$json['value'] = $value;
				}
			}
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
		/////// End category featured and featured product /////////
		public function autocomplete() {
			$json = array();
			
			$this->load->model('extension/purpletree_multivendor/dashboard');
			
			$this->model_extension_purpletree_multivendor_dashboard->checkSellerApproval();
			
			if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_model'])) {
				
				$this->load->model('extension/purpletree_multivendor/sellerproduct');
				
				if (isset($this->request->get['filter_name'])) {
					$filter_name = $this->request->get['filter_name'];
					} else {
					$filter_name = '';
				}
				
				if (isset($this->request->get['filter_model'])) {
					$filter_model = $this->request->get['filter_model'];
					} else {
					$filter_model = '';
				}
				
				if (isset($this->request->get['limit'])) {
					$limit = $this->request->get['limit'];
					} else {
					$limit = 5;
				}
				
				$seller_id = $this->customer->getId();
				
				$filter_data = array(
				'filter_name'  => $filter_name,
				'filter_model' => $filter_model,
				'start'        => 0,
				'limit'        => $limit,
				'seller_id' => $seller_id
				);
				
				$results = $this->model_extension_purpletree_multivendor_tablemanager->getProducts($filter_data);
				
				foreach ($results as $result) {
					$option_data = array();
					
					$product_options = $this->model_extension_purpletree_multivendor_tablemanager->getProductOptions($result['product_id']);
					
					foreach ($product_options as $product_option) {
						$option_info = $this->model_extension_purpletree_multivendor_tablemanager->getOptions($product_option['option_id']);
						
						if ($option_info) {
							$product_option_value_data = array();
							
							foreach ($product_option['product_option_value'] as $product_option_value) {
								$option_value_info = $this->model_extension_purpletree_multivendor_tablemanager->getOptionValue($product_option_value['option_value_id']);
								
								if ($option_value_info) {
									$product_option_value_data[] = array(
									'product_option_value_id' => $product_option_value['product_option_value_id'],
									'option_value_id'         => $product_option_value['option_value_id'],
									'name'                    => $option_value_info['name'],
									'price'                   => (float)$product_option_value['price'] ? $this->currency->format($product_option_value['price'], $this->session->data['currency']) : false,
									'price_prefix'            => $product_option_value['price_prefix']
									);
								}
							}
							
							$option_data[] = array(
							'product_option_id'    => $product_option['product_option_id'],
							'product_option_value' => $product_option_value_data,
							'option_id'            => $product_option['option_id'],
							//'name'                 => $option_info['name'],
							//'type'                 => $option_info['type'],
							'value'                => $product_option['value'],
							'required'             => $product_option['required']
							);
						}
					}
					
					$json[] = array(
					'product_id' => $result['product_id'],
					'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'model'      => $result['model'],
					'option'     => $option_data,
					'price'      => $result['price']
					);
				}
			}
			
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
		
		public function manufacturer() {
			$json = array();
			
			$this->load->model('extension/purpletree_multivendor/dashboard');
			
			$this->model_extension_purpletree_multivendor_dashboard->checkSellerApproval();
			
			if (isset($this->request->get['filter_name'])) {
				$this->load->model('extension/purpletree_multivendor/sellerproduct');
				
				$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start'       => 0,
				'limit'       => 5
				);
				
				$results = $this->model_extension_purpletree_multivendor_tablemanager->getManufacturers($filter_data);
				
				foreach ($results as $result) {
					$json[] = array(
					'manufacturer_id' => $result['manufacturer_id'],
					'name'            => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
					);
				}
			}
			
			$sort_order = array();
			
			foreach ($json as $key => $value) {
				$sort_order[$key] = $value['name'];
			}
			
			array_multisort($sort_order, SORT_ASC, $json);
			
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
		
		public function category() {
			$json = array();
			
			$this->load->model('extension/purpletree_multivendor/dashboard');
			
			$this->model_extension_purpletree_multivendor_dashboard->checkSellerApproval();
			
			if (isset($this->request->get['filter_name'])) {
				$this->load->model('extension/purpletree_multivendor/sellerproduct');
				$allowed=array();
				if($this->config->get('module_purpletree_multivendor_allow_categorytype')) {
					$this->load->model('catalog/category');
					$results = $this->model_catalog_category->getCategories();
					foreach ($results as $result) {
						$allowed[] = $result['category_id'];
					}
					} else {
					$allowed = $this->config->get('module_purpletree_multivendor_allow_category');
				}
				$allowddd = '';
				if(!empty($allowed)) {
					$allowddd = (implode(',',$allowed));
				}
				$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'sort'        => 'name',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => 5,
				'category_type' => ($this->config->get('module_purpletree_multivendor_allow_categorytype')),
				'category_allow' => $allowddd
				);
				
				$results = $this->model_extension_purpletree_multivendor_tablemanager->getCategories($filter_data);
				
				foreach ($results as $result) {
					$json[] = array(
					'category_id' => $result['category_id'],
					'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
					);
				}
			}
			
			$sort_order = array();
			
			foreach ($json as $key => $value) {
				$sort_order[$key] = $value['name'];
			}
			
			array_multisort($sort_order, SORT_ASC, $json);
			
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
		
		public function filter() {
			$json = array();
			
			$this->load->model('extension/purpletree_multivendor/dashboard');
			
			$this->model_extension_purpletree_multivendor_dashboard->checkSellerApproval();
			
			if (isset($this->request->get['filter_name'])) {
				$this->load->model('extension/purpletree_multivendor/sellerproduct');
				
				$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start'       => 0,
				'limit'       => 5
				);
				
				$filters = $this->model_extension_purpletree_multivendor_tablemanager->getFilters($filter_data);
				
				foreach ($filters as $filter) {
					$json[] = array(
					'filter_id' => $filter['filter_id'],
					'name'      => strip_tags(html_entity_decode($filter['group'] . ' &gt; ' . $filter['name'], ENT_QUOTES, 'UTF-8'))
					);
				}
			}
			
			$sort_order = array();
			
			foreach ($json as $key => $value) {
				$sort_order[$key] = $value['name'];
			}
			
			array_multisort($sort_order, SORT_ASC, $json);
			
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
		
		public function download() {
			$json = array();
			
			$this->load->model('extension/purpletree_multivendor/dashboard');
			
			$this->model_extension_purpletree_multivendor_dashboard->checkSellerApproval();
			
			if (isset($this->request->get['filter_name'])) {
				$this->load->model('extension/purpletree_multivendor/sellerproduct');
				
				$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start'       => 0,
				'limit'       => 5,
				'seller_id'		  => $this->customer->getId()
				);
				
				$results = $this->model_extension_purpletree_multivendor_tablemanager->getDownloads($filter_data);
				
				foreach ($results as $result) {
					$json[] = array(
					'download_id' => $result['download_id'],
					'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
					);
				}
			}
			
			$sort_order = array();
			
			foreach ($json as $key => $value) {
				$sort_order[$key] = $value['name'];
			}
			
			array_multisort($sort_order, SORT_ASC, $json);
			
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
		
		public function product() {
			$json = array();
			
			$this->load->model('extension/purpletree_multivendor/dashboard');
			
			$this->model_extension_purpletree_multivendor_dashboard->checkSellerApproval();
			
			if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_model'])) {
				$this->load->model('catalog/product');
				$this->load->model('extension/catalog/option');
				
				if (isset($this->request->get['filter_name'])) {
					$filter_name = $this->request->get['filter_name'];
					} else {
					$filter_name = '';
				}
				
				if (isset($this->request->get['filter_model'])) {
					$filter_model = $this->request->get['filter_model'];
					} else {
					$filter_model = '';
				}
				
				if (isset($this->request->get['limit'])) {
					$limit = $this->request->get['limit'];
					} else {
					$limit = 5;
				}
				
				
				$seller_id = $this->customer->getId();
				
				$filter_data = array(
				'filter_name'  => $filter_name,
				'filter_model' => $filter_model,
				'start'        => 0,
				'limit'        => $limit,
				'seller_id'        => $seller_id
				);
				
				$this->load->model('extension/purpletree_multivendor/sellerproduct');
				$results = $this->model_extension_purpletree_multivendor_tablemanager->getProducts($filter_data);
				
				foreach ($results as $result) {
					$option_data = array();
					
					$product_options = $this->model_catalog_product->getProductOptions($result['product_id']);
					
					foreach ($product_options as $product_option) {
						$option_info = $this->model_extension_catalog_option->getOption($product_option['option_id']);
						
						if ($option_info) {
							$product_option_value_data = array();
							
							foreach ($product_option['product_option_value'] as $product_option_value) {
								$option_value_info = $this->model_extension_catalog_option->getOptionValue($product_option_value['option_value_id']);
								
								if ($option_value_info) {
									$product_option_value_data[] = array(
									'product_option_value_id' => $product_option_value['product_option_value_id'],
									'option_value_id'         => $product_option_value['option_value_id'],
									'name'                    => $option_value_info['name'],
									'price'                   => (float)$product_option_value['price'] ? $this->currency->format($product_option_value['price'], $this->session->data['currency']) : false,
									'price_prefix'            => $product_option_value['price_prefix']
									);
								}
							}
							
							$option_data[] = array(
							'product_option_id'    => $product_option['product_option_id'],
							'product_option_value' => $product_option_value_data,
							'option_id'            => $product_option['option_id'],
							'name'                 => $option_info['name'],
							'type'                 => $option_info['type'],
							'value'                => $product_option['value'],
							'required'             => $product_option['required']
							);
						}
					}
					
					$json[] = array(
					'product_id' => $result['product_id'],
					'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'model'      => $result['model'],
					'option'     => $option_data,
					'price'      => $result['price']
					);
				}
			}
			
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
		
		public function attribute() {
			$json = array();
			
			$this->load->model('extension/purpletree_multivendor/dashboard');
			
			$this->model_extension_purpletree_multivendor_dashboard->checkSellerApproval();
			
			if (isset($this->request->get['filter_name'])) {
				$this->load->model('extension/purpletree_multivendor/sellerproduct');
				
				$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start'       => 0,
				'limit'       => 5
				);
				$this->load->model('extension/purpletree_multivendor/sellerattribute');
				// attribute sets
				$datag = $this->model_extension_purpletree_multivendor_sellerattribute->getOtherSellerAttributeGroups();
				$attributearray =array();
				if(!empty($datag)){
					foreach($datag as $datagg){
						$attributearray[] = $datagg['attribute_group_id'];
					}
				}
				$stringgattrsets = '';
				if(!empty($attributearray)) {
					$stringgattrsets = implode(',',$attributearray);
				}
				// attribute sets
				// attributes
				$dataga = $this->model_extension_purpletree_multivendor_sellerattribute->getOtherSellerAttributes();
				$attributearraya =array();
				if(!empty($dataga)){
					foreach($dataga as $datagga){
						$attributearraya[] = $datagga['attribute_id'];
					}
				}
				$stringgattrs = '';
				if(!empty($attributearraya)) {
					$stringgattrs = implode(',',$attributearraya);
				}
				// attributes
				$results = $this->model_extension_purpletree_multivendor_tablemanager->getAttributes($filter_data,$stringgattrsets,$stringgattrs);
				
				foreach ($results as $result) {
					$json[] = array(
					'attribute_id'    => $result['attribute_id'],
					'name'            => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'attribute_group' => $result['attribute_group']
					);
				}
			}
			
			$sort_order = array();
			
			foreach ($json as $key => $value) {
				$sort_order[$key] = $value['name'];
			}
			
			array_multisort($sort_order, SORT_ASC, $json);
			
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
		
		public function option() {
			$json = array();
			
			$this->load->model('extension/purpletree_multivendor/dashboard');
			
			$this->model_extension_purpletree_multivendor_dashboard->checkSellerApproval();
			
			if (isset($this->request->get['filter_name'])) {
				$this->load->language('catalog/option');
				
				$this->load->model('extension/catalog/option');
				
				$this->load->model('tool/image');
				
				$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start'       => 0,
				'limit'       => 5
				);
				// options
				$this->load->model('extension/purpletree_multivendor/sellerattribute');
				$datag = $this->model_extension_purpletree_multivendor_sellerattribute->getOtherSellerOptions();
				$attributearray =array();
				if(!empty($datag)){
					foreach($datag as $datagg){
						$attributearray[] = $datagg['option_id'];
					}
				}
				$stringgattrsets = '';
				if(!empty($attributearray)) {
					$stringgattrsets = implode(',',$attributearray);
				}
				// attribute sets
				$options = $this->model_extension_catalog_option->getOptions($filter_data,$stringgattrsets);
				
				foreach ($options as $option) {
					$option_value_data = array();
					
					if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'image') {
						$option_values = $this->model_extension_catalog_option->getOptionValues($option['option_id']);
						
						foreach ($option_values as $option_value) {
							if (is_file(DIR_IMAGE . $option_value['image'])) {
								$image = $this->model_tool_image->resize($option_value['image'], 50, 50);
								} else {
								$image = $this->model_tool_image->resize('no_image.png', 50, 50);
							}
							 
							$option_value_data[] = array(
							'option_value_id' => $option_value['option_value_id'],
							'name'            => strip_tags(html_entity_decode($option_value['name'], ENT_QUOTES, 'UTF-8')),
							'image'           => $image
							);
						}
						
						$sort_order = array();
						
						foreach ($option_value_data as $key => $value) {
							$sort_order[$key] = $value['name'];
						}
						
						array_multisort($sort_order, SORT_ASC, $option_value_data);
					}
					
					$type = '';
					
					if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox') {
						$type = $this->language->get('text_choose');
					}
					
					if ($option['type'] == 'text' || $option['type'] == 'textarea') {
						$type = $this->language->get('text_input');
					}
					
					if ($option['type'] == 'file') {
						$type = $this->language->get('text_file');
					}
					
					if ($option['type'] == 'date' || $option['type'] == 'datetime' || $option['type'] == 'time') {
						$type = $this->language->get('text_date');
					}
					
					$json[] = array(
					'option_id'    => $option['option_id'],
					'name'         => strip_tags(html_entity_decode($option['name'], ENT_QUOTES, 'UTF-8')),
					'category'     => $type,
					'type'         => $option['type'],
					'option_value' => $option_value_data
					);
				}
			}
			
			$sort_order = array();
			
			foreach ($json as $key => $value) {
				$sort_order[$key] = $value['name'];
			}
			
			array_multisort($sort_order, SORT_ASC, $json);
			
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
		
		
		////////////// For sub category //////////////////
		public function autosubcategory() {
			$json = array();
			
			if (isset($this->request->get['category_id'])) {
				$category_id = $this->request->get['category_id'];
				
				} else {
				$category_id = '';
			}
			$this->load->model('extension/purpletree_multivendor/sellerproduct');	
			$this->load->model('extension/purpletree_multivendor/dashboard');
			$this->model_extension_purpletree_multivendor_dashboard->checkSellerApproval();			
			$results = $this->model_extension_purpletree_multivendor_tablemanager->getSubcategory($category_id);
			if(empty($results)) {
				$json[] = array(
				'subcategory_id'       => $category_id,
				'name'              => 'None'	
				);
				} else {
				foreach ($results as $result) {
					$json[] = array(
					'subcategory_id'       => $result['category_id'],
					'name'              => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))				
					);
				}
			}
			
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
		///////////////// End sub category /////////////////////
		///// check product subscription plan ///////
		public function check_featured_product_subscription_plan() {
			if (!$this->customer->isLogged()) {
				$this->session->data['redirect'] = $this->url->link('extension/account/purpletree_multivendor/sellerproduct', '', true);
				
				$this->response->redirect($this->url->link('account/login', '', true));
			}
			$this->load->model('extension/purpletree_multivendor/dashboard');
			
			$this->model_extension_purpletree_multivendor_dashboard->checkSellerApproval();
			
			$store_detail = $this->customer->isSeller();
			if(!isset($store_detail['store_status'])){
				$this->response->redirect($this->url->link('account/account', '', true));
			}
			
			if(!$this->customer->validateSeller()) {
				$this->load->language('purpletree_multivendor/ptsmultivendor');
				$this->session->data['error_warning'] = $this->language->get('error_license');			
				$this->response->redirect($this->url->link('extension/account/purpletree_multivendor/sellerproduct', '', true));
			}
			
			$this->load->language('purpletree_multivendor/sellerproduct');
			$json['status'] = 'error'; 
			$json['message'] = 'Something went wrong'; 
			if (isset($this->request->get['product_id']) && $this->request->get['product_id'] != '') {
				if ($this->request->get['value'] == 'true') {
					$value = 1;
					} else {
					$value = 0;
				}
				$this->load->model('extension/purpletree_multivendor/sellerproduct');
				if($this->config->get('module_purpletree_multivendor_subscription_plans')){
					if($value == 1) {
						if (isset($this->request->get['product_id'])) {
							$product_id = $this->request->get['product_id'];
							
							} else {
							$product_id = '';
						}
						$this->load->model('extension/purpletree_multivendor/sellerproduct');	
						$results = $this->model_extension_purpletree_multivendor_tablemanager->featuredProductPlanName($this->request->get['product_id']);		
						if(empty($results)) {
							$json['plan_id'] = $results;
							$json['status'] = 'success';
							$json['message'] = ''; 
							} else {
							$json['plan_id'] = $results;
							$json['status'] = 'success';
							$json['message'] = ''; 
						}		
						
					} 		
				}	
			}
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
		public function add_featured_product_By_Popup(){		
			if (!$this->customer->isLogged()) {
				$this->session->data['redirect'] = $this->url->link('extension/account/purpletree_multivendor/sellerproduct', '', true);
				
				$this->response->redirect($this->url->link('account/login', '', true));
			}
			$this->load->model('extension/purpletree_multivendor/dashboard');
			
			$this->model_extension_purpletree_multivendor_dashboard->checkSellerApproval();
			
			$store_detail = $this->customer->isSeller();
			if(!isset($store_detail['store_status'])){
				$this->response->redirect($this->url->link('account/account', '', true));
			}
			
			if(!$this->customer->validateSeller()) {
				$this->load->language('purpletree_multivendor/ptsmultivendor');
				$this->session->data['error_warning'] = $this->language->get('error_license');			
				$this->response->redirect($this->url->link('extension/account/purpletree_multivendor/sellerproduct', '', true));
			}
			
			$this->load->language('purpletree_multivendor/sellerproduct');
			$json['status'] = 'error'; 
			$json['message'] = 'Something went wrong'; 
			$featuredhidden  = $this->request->post['featuredhidden'];
			if(isset($featuredhidden) && $featuredhidden == 1){
				if (isset($this->request->post['productidinform']) && $this->request->post['popup_product_plan_id'] != '') {
					//// feature product plan validation ////////
					if($this->config->get('module_purpletree_multivendor_subscription_plans')) {
						$this->load->model('extension/purpletree_multivendor/sellerproduct');
						$seller_id = $this->customer->getId();			 
						$featured_plan_product = array();
						$featured_total_product = array();		
						if(isset($this->request->post['productidinform']) && $this->request->post['popup_product_plan_id'] != 0 ) {
							$featured_plan_product = $this->model_extension_purpletree_multivendor_tablemanager->getFeaturedPlanProduct($this->request->post['popup_product_plan_id']);
							$featured_total_product = $this->model_extension_purpletree_multivendor_tablemanager->getFeaturedTotalProduct($this->request->post['popup_product_plan_id'], $seller_id);	
							if($featured_total_product >= $featured_plan_product){
								$json['status'] = 'error'; 
								$json['message'] = $this->language->get('error_featured_product_plan_id');				
								}else{
								$this->model_extension_purpletree_multivendor_tablemanager->addFeaturedProductByPopup($this->request->post['productidinform'],$this->request->post['popup_product_plan_id']);
								$json['status'] = 'success'; 	
								$json['message'] = $this->language->get('text_assigned'); 
								$json['product_id'] = $this->request->post['productidinform'];
								$json['featuredhidden']  = $this->request->post['featuredhidden'];				
							}  
							
						}
					}
					//// End feature product plan validation ////////
					
				}
				}else{
				if (isset($this->request->post['productidinform']) && $this->request->post['popup_product_plan_id'] != '') {
					//// feature product plan validation ////////
					if($this->config->get('module_purpletree_multivendor_subscription_plans')) {
						$this->load->model('extension/purpletree_multivendor/sellerproduct');
						$seller_id = $this->customer->getId();			 
						$category_featured_plan_product = array();
						$category_featured_total_product = array();		
						if(isset($this->request->post['productidinform']) && $this->request->post['popup_product_plan_id'] != 0 ) {
							$category_featured_plan_product = $this->model_extension_purpletree_multivendor_tablemanager->getCatgoryFeaturedPlanProduct($this->request->post['popup_product_plan_id']);
							$category_featured_total_product = $this->model_extension_purpletree_multivendor_tablemanager->getCatgoryFeaturedTotalProduct($this->request->post['popup_product_plan_id'], $seller_id);	
							if($category_featured_total_product >= $category_featured_plan_product){
								$json['status'] = 'error'; 
								$json['message'] = $this->language->get('error_featured_product_plan_id');				
								}else{
								$this->model_extension_purpletree_multivendor_tablemanager->addCategoryFeaturedProductByPopup($this->request->post['productidinform'],$this->request->post['popup_product_plan_id']);
								$json['status'] = 'success'; 	
								$json['message'] = $this->language->get('text_assigned');
								$json['product_id'] = $this->request->post['productidinform'];
								$json['featuredhidden']  = $this->request->post['featuredhidden'];				
							}  
							
						}
					}
					//// End feature product plan validation ////////
					
				}
			}
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
		
		public function check_category_featured_product_subscription_plan() {
			if (!$this->customer->isLogged()) {
				$this->session->data['redirect'] = $this->url->link('extension/account/purpletree_multivendor/sellerproduct', '', true);
				$this->response->redirect($this->url->link('account/login', '', true));
			}
			$this->load->model('extension/purpletree_multivendor/dashboard');
			
			$this->model_extension_purpletree_multivendor_dashboard->checkSellerApproval();
			
			$store_detail = $this->customer->isSeller();
			if(!isset($store_detail['store_status'])){
				$this->response->redirect($this->url->link('account/account', '', true));
			}
			
			if(!$this->customer->validateSeller()) {
				$this->load->language('purpletree_multivendor/ptsmultivendor');
				$this->session->data['error_warning'] = $this->language->get('error_license');			
				$this->response->redirect($this->url->link('extension/account/purpletree_multivendor/sellerproduct', '', true));
			}
			
			$this->load->language('purpletree_multivendor/sellerproduct');
			$json['status'] = 'error'; 
			$json['message'] = 'Something went wrong'; 
			if (isset($this->request->get['product_id']) && $this->request->get['product_id'] != '') {
				if ($this->request->get['value'] == 'true') {
					$value = 1;
					} else {
					$value = 0;
				}
				$this->load->model('extension/purpletree_multivendor/sellerproduct');
				if($this->config->get('module_purpletree_multivendor_subscription_plans')){
					if($value == 1) {
						if (isset($this->request->get['product_id'])) {
							$product_id = $this->request->get['product_id'];
							
							} else {
							$product_id = '';
						}
						$this->load->language('purpletree_multivendor/sellerproduct');
						$this->load->model('extension/purpletree_multivendor/sellerproduct');	
						$results = $this->model_extension_purpletree_multivendor_tablemanager->categoryFeaturedProductPlanName($this->request->get['product_id']);		
						if(empty($results)) {
							$json['plan_id'] = $results;
							$json['status'] = 'success';
							$json['message'] = ''; 
							} else {
							$json['plan_id'] = $results;
							$json['status'] = 'success';
							$json['message'] = ''; 
						}		
						
					} 		
				}	
			}
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
		public function remove_category_featured_product_subscription_plan() {
			if (!$this->customer->isLogged()) {
				$this->session->data['redirect'] = $this->url->link('extension/account/purpletree_multivendor/sellerproduct', '', true);
				$this->response->redirect($this->url->link('account/login', '', true));
			}
			$this->load->model('extension/purpletree_multivendor/dashboard');
			
			$this->model_extension_purpletree_multivendor_dashboard->checkSellerApproval();
			
			$store_detail = $this->customer->isSeller();
			if(!isset($store_detail['store_status'])){
				$this->response->redirect($this->url->link('account/account', '', true));
			}
			
			if(!$this->customer->validateSeller()) {
				$this->load->language('purpletree_multivendor/ptsmultivendor');
				$this->session->data['error_warning'] = $this->language->get('error_license');			
				$this->response->redirect($this->url->link('extension/account/purpletree_multivendor/sellerproduct', '', true));
			}
			
			$this->load->language('purpletree_multivendor/sellerproduct');
			$json['status'] = 'error'; 
			$json['message'] = 'Something went wrong'; 
			if (isset($this->request->get['product_id']) && $this->request->get['product_id'] != '') {	  $this->load->language('purpletree_multivendor/sellerproduct');
				$this->load->model('extension/purpletree_multivendor/sellerproduct');
				$this->model_extension_purpletree_multivendor_tablemanager->removeCategoryFeaturedProduct($this->request->get['product_id']);
				if($this->config->get('module_purpletree_multivendor_subscription_plans')){	
					$json['status'] = 'success';
					$json['message'] = $this->language->get('text_unAssigned');				
				} 		
			}	
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
		public function remove_featured_product_subscription_plan() {
			if (!$this->customer->isLogged()) {
				$this->session->data['redirect'] = $this->url->link('extension/account/purpletree_multivendor/sellerproduct', '', true);
				$this->response->redirect($this->url->link('account/login', '', true));
			}
			
			$this->load->model('extension/purpletree_multivendor/dashboard');
			
			$this->model_extension_purpletree_multivendor_dashboard->checkSellerApproval();
			
			$store_detail = $this->customer->isSeller();
			if(!isset($store_detail['store_status'])){
				$this->response->redirect($this->url->link('account/account', '', true));
			}
			
			if(!$this->customer->validateSeller()) {
				$this->load->language('purpletree_multivendor/ptsmultivendor');
				$this->session->data['error_warning'] = $this->language->get('error_license');			
				$this->response->redirect($this->url->link('extension/account/purpletree_multivendor/sellerproduct', '', true));
			}
			
			$this->load->language('purpletree_multivendor/sellerproduct');
			$json['status'] = 'error'; 
			$json['message'] = 'Something went wrong'; 
			if (isset($this->request->get['product_id']) && $this->request->get['product_id'] != '') {	  $this->load->language('purpletree_multivendor/sellerproduct');
				$this->load->model('extension/purpletree_multivendor/sellerproduct');
				if($this->config->get('module_purpletree_multivendor_subscription_plans')){	
					$this->model_extension_purpletree_multivendor_tablemanager->removeFeaturedProduct($this->request->get['product_id']);
					$json['product_id'] = $this->request->get['product_id'];
					$json['status'] = 'success';
					$json['message'] = $this->language->get('text_unAssigned');				
				} 		 
			}	
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
		//// End check product subscription plan///
		//// ***** Start Quick Edit  Product ********/////
		public function quickEdit() {
			$json = array();
			//echo "<pre>"; print_r($this->request->get['product_id']); die;
			$this->load->model('extension/purpletree_multivendor/dashboard');
			$this->load->model('extension/catalog/option');
			$this->model_extension_purpletree_multivendor_dashboard->checkSellerApproval();
			
			if (isset($this->request->get['product_id'])) {
				
				$this->load->model('extension/purpletree_multivendor/sellerproduct');
				//$results = $this->model_extension_purpletree_multivendor_tablemanager->getProducts($filter_data);
				$seller_id = $this->customer->getId();
				$result = $this->model_extension_purpletree_multivendor_tablemanager->getProduct($this->request->get['product_id'],$seller_id);
				
				$this->load->model('localisation/language');
           $languages = $this->model_localisation_language->getLanguages();
		  
			foreach($languages as $key => $value) {
				$languages[$key]['activetab'] = '';
			}
			foreach($languages as $key => $value) {
				$languages[$key]['activetab'] = 'active';
				break;
			}
			
			
			$product_options = $this->model_extension_purpletree_multivendor_tablemanager->getProductOptions($this->request->get['product_id']);
			
			$product_options1 = array();
			
			foreach ($product_options as $product_option) {
				$product_option_value_data = array();
				
				if (isset($product_option['product_option_value'])) {
					foreach ($product_option['product_option_value'] as $product_option_value) {
						$product_option_value_data[] = array(
						'product_option_value_id' => $product_option_value['product_option_value_id'],
						'option_value_id'         => $product_option_value['option_value_id'],
						'quantity'                => $product_option_value['quantity'],
						'subtract'                => $product_option_value['subtract'],
						'price'                   => $product_option_value['price'],
						'price_prefix'            => $product_option_value['price_prefix'],
						'points'                  => $product_option_value['points'],
						'points_prefix'           => $product_option_value['points_prefix'],
						'weight'                  => $product_option_value['weight'],
						'weight_prefix'           => $product_option_value['weight_prefix']
						);
					}
				}
				
				
				
				$product_options1[] = array(
				'product_option_id'    => $product_option['product_option_id'],
				'product_option_value' => $product_option_value_data,
				'option_id'            => $product_option['option_id'],
				'name'                 => $product_option['name'],
				'type'                 => $product_option['type'],
				'value'                => isset($product_option['value']) ? $product_option['value'] : '',
				'required'             => $product_option['required']
				);
			}
			$product_option_values_data = array();
			foreach ($product_options1 as $product_option2) {
				if ($product_option2['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
					
					//echo"<pre>"; print_r($product_option2['option_id']);
						$product_option_values_data[$product_option2['option_id']] = $this->model_extension_catalog_option->getOptionValues($product_option2['option_id']);
					
				}
			}
			//echo"<pre>"; print_r($product_option_values_data);die;
			
         $product_specials = array();			
         $product_specials = $this->model_extension_purpletree_multivendor_tablemanager->getProductSpecials($this->request->get['product_id']);
		 $product_specials1[] = array();
      foreach ($product_specials as $product_special) {
				$product_specials1[] = array(
				'customer_group_id' => $product_special['customer_group_id'],
				'priority'          => $product_special['priority'],
				'price'             => $product_special['price'],
				'date_start'        => ($product_special['date_start'] != '0000-00-00') ? $product_special['date_start'] : '',
				'date_end'          => ($product_special['date_end'] != '0000-00-00') ? $product_special['date_end'] :  ''
				);
			}
			$product_description = $this->model_extension_purpletree_multivendor_tablemanager->getProductDescriptions($this->request->get['product_id']);
			
			 foreach ($languages as $key => $value) {
			    $product_name[] = array(
				'product_lang' => $languages[$key]['language_id'],
				'product_name' => $product_description[$languages[$key]['language_id']]['name']
				);
				 }
			
			//echo"<pre>"; print_r($product_options1); die;
					
					$json = array(
					'product_id' => $this->request->get['product_id'],
					'name'       => $product_name,
					'product_option'     => $product_options1,
					'price'      => $result['price'],
					'quantity'      => $result['quantity'],
					'special_price'      => $product_specials1,
					'status'      => $result['status'],
					'product_option_values_data'      => $product_option_values_data,
					'language'      => $languages
					);
			}
			//echo "<pre>"; print_r($json); die;
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
		public function quickSave() {
		  if (!$this->customer->isLogged()) {
				$this->session->data['redirect'] = $this->url->link('extension/account/purpletree_multivendor/sellerproduct', '', true);
				
				$this->response->redirect($this->url->link('account/login', '', true));
			}
			$this->load->model('extension/purpletree_multivendor/dashboard');
			$this->load->model('extension/purpletree_multivendor/sellerproduct');
			
			$this->model_extension_purpletree_multivendor_dashboard->checkSellerApproval();
			
			$store_detail = $this->customer->isSeller();
			if(!isset($store_detail['store_status'])){
				$this->response->redirect($this->url->link('account/account', '', true));
			}
			
			if(!$this->customer->validateSeller()) {
				$this->load->language('purpletree_multivendor/ptsmultivendor');
				$this->session->data['error_warning'] = $this->language->get('error_license');			
				$this->response->redirect($this->url->link('extension/account/purpletree_multivendor/sellerproduct', '', true));
			}
			
			$this->load->language('purpletree_multivendor/sellerproduct');
			
			//echo"<pre>"; print_r($this->request->post); die;
			if($this->request->post['quick_product_id']){
			    $this->model_extension_purpletree_multivendor_tablemanager->qucikEditProduct($this->request->post['quick_product_id'],$this->request->post);
			    $currentproductname = $this->model_extension_purpletree_multivendor_tablemanager->getproductname($this->request->post['quick_product_id']);
				$json['productname'] = $currentproductname; 
				$json['pts_price'] =  $this->request->post['quick_product_price'];  
				$json['pts_quentity'] = $this->request->post['quick_product_quantity']; 
				$json['pts_status'] = $this->request->post['quick_product_status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled');
				$json['pts_status_id'] = $this->request->post['quick_product_status'];
				$json['product_id'] = $this->request->post['quick_product_id']; 
				$json['status'] = 'success'; 
			    $json['message'] = 'Product information successfully update';
				}else{
				$json['status'] = 'error'; 
			    $json['message'] = 'Something went wrong'; 
				}
					
				
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
		
		//// ***** End  Quick Edit  Product ********/////
		
	
	
}
?>