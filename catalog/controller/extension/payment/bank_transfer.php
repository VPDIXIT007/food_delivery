<?php
class ControllerExtensionPaymentBankTransfer extends Controller {
	public function index() {
			if (isset($this->request->get['seller_store_id'])){
			
			$data['seller_id']=$this->request->get['seller_store_id'];
			$seller_id =$this->request->get['seller_store_id'];
		} else {$seller_id = '';}
		$this->load->language('extension/payment/bank_transfer');

		$data['bank'] = nl2br($this->config->get('payment_bank_transfer_bank' . $this->config->get('config_language_id')));

		return $this->load->view('extension/payment/bank_transfer', $data);
	}

	public function confirm() {
			if (isset($this->request->get['seller_store_id'])){
			$data['seller_id']=$this->request->get['seller_store_id'];
			$seller_id =$this->request->get['seller_store_id'];
		} else {$seller_id = '';}
		$json = array();
		$this->session->data['comment'] = strip_tags($this->request->post['comment']);
		
			$this->load->language('extension/payment/bank_transfer');
			
			$this->load->model('checkout/order');

			//ac handle reward point // Order number
			$this->model_checkout_order->handleOrderNumber($this->session->data['order_id'], $seller_id);
			
			$reward_status = $this->model_checkout_order->handleRewardPoint();
			if(!$reward_status['success']){
				$json['redirect'] = $this->url->link('checkout/checkout&seller_store_id='. $seller_id );
				$this->session->data['error'] = "You don't have enough point, please try again.";
				
				$this->response->addHeader('Content-Type: application/json');
				$this->response->setOutput(json_encode($json));
			}

			if(isset($this->session->data['table_id'])) {
			$tablenum = $this->db->query("SELECT * FROM ". DB_PREFIX ."table_manger WHERE id = '". $this->session->data['table_id'] ."'")->row;
			$comment  = "TABLE NUMBER : ".$tablenum['table_no'];
			} else { $comment = '';} 
		
			//trigger_error(print_r($this->request->post['prodcomments'],true));
			foreach($this->request->post['prodcomments'] as $prodcomment) {
				$prods = explode(',',$prodcomment);
				$productid = explode(':',$prods[0]);
				$comment = explode (':',$prods[1]);
				
				$this->db->query("UPDATE ". DB_PREFIX ."purpletree_vendor_orders SET product_comments ='". $comment[1] ."' WHERE product_id = '". (int)$productid[1] ."' AND order_id = '". $this->session->data['order_id'] ."' ");
			}
			$this->model_checkout_order->addOrderHistorydinein($this->session->data['order_id'], '1', $comment, true);
			$push = $this->sendNotification($this->session->data['order_id'],$seller_id);
			$json['redirect'] = $this->url->link('checkout/success&seller_store_id='. $seller_id );
		
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));		
	}
	function sendNotification($order_id,$seller_id){
			// GET SESSION ID AND TOKEN
			trigger_error($seller_id);
			$customer_id = $this->db->query("SELECT seller_id FROM ". DB_PREFIX ."purpletree_vendor_stores WHERE id = '". $seller_id ."'")->row;
			
			$sessionorder = $this->db->query("SELECT session_id from ". DB_PREFIX ."session WHERE data LIKE '%\"customer_id\":\"". $customer_id['seller_id'] ."%' ORDER BY expire DESC ")->row;
		
			$firetoken = $this->db->query("SELECT * FROM ". DB_PREFIX ."pushnotifications WHERE session_id = '". $sessionorder['session_id'] ."'")->row;
				trigger_error(print_r($firetoken,true));
		//	$statusname = $this->db->query("SELECT * FROM ". DB_PREFIX ."order_status WHERE order_status_id = '". $order_status_id ."' and language_id = '". (int)$this->config->get('config_language_id') ."'")->row;
		      
			//save in notification 
			$message = "YOU HAVE A NEW ORDER #$order_id PLEASE PROCESS ASAP";
			$notification_sql = "INSERT INTO oc_fcm_notification SET `session_id` = '".$customer_id['seller_id']."', `payload` = '".$message."', `status` = '0' ";
			$this->db->query($notification_sql);
			$notification_id = $this->db->getLastId();

    $url ="https://fcm.googleapis.com/fcm/send";

    $fields=array(
        "to"=> $firetoken['firebasetoken'],//$_REQUEST['token'],
        "notification"=>array(
            "body"=>'YOU HAVE A NEW ORDER # '.$order_id.' PLEASE PROCESS ASAP',//$_REQUEST['message'],
            "title"=>'El-Order Notifications',//$_REQUEST['title'],
            "icon"=>$_REQUEST['icon'],
            "click_action"=>"https://el-order.com"
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
   // print_r($result);
    curl_close($ch);
	
	return $result;
}
}