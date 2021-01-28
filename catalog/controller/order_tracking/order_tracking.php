<?php
class ControllerOrderTrackingOrderTracking extends Controller{
   
    public function track() {
        
        if(isset($this->request->post['order_no'])){	
            $order_no = $this->request->post['order_no'];
            $this->load->model('checkout/order'); 
            $order_status = $this->model_checkout_order->getOrder($order_no);
            $status_id = $this->db->query('SELECT * FROM '. DB_PREFIX .'purpletree_vendor_orders WHERE order_id ="'. $order_status['order_id'] .'"')->row;
             
			$status_name = $this->db->query('SELECT * FROM '. DB_PREFIX .'order_status WHERE order_status_id ="'. $status_id['order_status_id'] .'" AND language_id = "'. $order_status['language_id'] .'"')->row;
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($status_name));
           
        }
    
}
}
