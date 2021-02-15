<?php
class ControllerOrderTrackingOrderTracking extends Controller{
   
    public function track() {
        $json = array();
        $json['success'] = false;
        $json['message'] = "Unable find order, please check with store.";
        if(isset($this->request->post['order_no']) &&  isset($this->request->post['sellerstore'])){	
            $order_no = $this->request->post['order_no'];
            $seller_store_id = $this->request->post['sellerstore'];

            if($order_no && $seller_store_id){
                $today = DATE("l");
                $get_seller_time = $this->db->query("SELECT * FROM " . DB_PREFIX . "purpletree_vendor_store_time WHERE store_id = '". (int)$seller_store_id ."' AND day_name = '$today' ");
                if($get_seller_time->num_rows){
                    $open_time = $get_seller_time->row['open_time'];
                    $close_time = $get_seller_time->row['close_time'];
    
                    //Open day
                    $open_day = DATE("Y-m-d $open_time");
                    //Close day
                    if(strtotime($open_time) > strtotime($close_time)){
                        $close_day = DATE("Y-m-d $close_time",strtotime('+1 day'));
                    }else{
                        $close_day = DATE("Y-m-d $close_time");
                    }
    
                    $orders = $this->db->query("SELECT vo.order_status_id, o.language_id FROM `oc_purpletree_vendor_orders` vo LEFT JOIN oc_order o ON o.order_id = vo.order_id WHERE vo.date_added >= '$open_day' AND vo.date_added <= '$close_day' AND vo.seller_id = (SELECT seller_id FROM `oc_purpletree_vendor_stores` WHERE `id` = '". (int)$seller_store_id ."') AND vo.order_status_id > 0 AND vo.order_no = '". $this->db->escape($order_no) ."'");

                    if($orders->num_rows){
                        $order = $orders->row;
                        $status_name = $this->db->query('SELECT * FROM '. DB_PREFIX .'order_status WHERE order_status_id ="'. $order['order_status_id'] .'" AND language_id = "'. $order['language_id'] .'"')->row;

                        $json['success'] = true;
                        $json['message'] = "Order details";
                        $json['status_name'] = $status_name;
                    }
                }
            }

        }
    
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
}
}
