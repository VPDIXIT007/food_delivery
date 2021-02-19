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

  public function clearCallPushNotification()
  {
    $customer_id = $this->customer->getId();

    $notification_sql = "DELETE FROM `oc_fcm_notification` WHERE `session_id` = '$customer_id' AND `status` = '1'";
		$this->db->query($notification_sql);

    return true;
  }

  public function markCallPushNotification()
  {
    if(isset($this->request->post['notification_id']) && isset($this->request->post['notification_status'])){
      $notification_id = $this->request->post['notification_id'];
      $status = $this->request->post['notification_status'];

      $notification_sql = "UPDATE `oc_fcm_notification` SET `status` = '$status' WHERE `notification_id` = '$notification_id'";
      $this->db->query($notification_sql);
    }

    return true;
  }
}