<?php
class ControllerApiSellers extends Controller {
	public function orders() {
		$this->load->language('api/sellers');

		$json = array();
    
    $this->load->model('common/ac');

    //Validate seller
    if(isset($this->request->get['api_token'])){
      $seller_info = $this->model_common_ac->validateSeller($this->request->get['api_token']);
    }else{
      $seller_info = [];
    }
		

    if(!empty($seller_info)){

      if (isset($this->request->get['seller_id'])) {
				$seller_id = $this->request->get['seller_id'];
				} else {
				$seller_id = $seller_info['seller_id'];
			}

      if (isset($this->request->get['filter_order_status'])) {
				$filter_order_status = $this->request->get['filter_order_status'];
				} else {
				$filter_order_status = null;
			}
			
			if (isset($this->request->get['filter_date_from'])) {
				$filter_date_from = $this->request->get['filter_date_from'];
				} else {
				$end_date = date('Y-m-d', strtotime("-30 days"));
				$filter_date_from = $end_date;
			}
			
			if (isset($this->request->get['filter_date_to'])) {
				$filter_date_to = $this->request->get['filter_date_to'];
				} else {
				$end_date = date('Y-m-d');
				$filter_date_to = $end_date;
			}

			
			if (isset($this->request->get['filter_time_from'])) {
				$filter_time_from = $this->request->get['filter_time_from'];
				}
			
			if (isset($this->request->get['filter_time_to'])) {
				$filter_time_to = $this->request->get['filter_time_to'];
				}
			
			if (isset($this->request->get['filter_table'])) {
				$filter_table = $this->request->get['filter_table'];
			}

			if (isset($this->request->get['order_type'])) {
				$order_type = $this->request->get['order_type'];
			}
			
      if (isset($this->request->get['page'])) {
				$page = $this->request->get['page'];
				} else {
				$page = 1;
			}

      if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
				} else {
				$limit = $this->config->get('config_limit_admin');
			}

      $filter_data = array(
        'filter_order_status'  => $filter_order_status,
        'filter_date_from'    => $filter_date_from,
        'filter_date_to'      => $filter_date_to,
        'start'                => ($page - 1) * $limit,
        'limit'                => $limit,
        'seller_id'            => $seller_id,
        'filter_time_from' 	   => $filter_time_from,
        'filter_time_to'	   => $filter_time_to,
        'filter_table' 		   => $filter_table,
        'order_type'           => $order_type
      );

      $order_total = $this->model_common_ac->getTotalSellerOrders($filter_data);
			
			$results = $this->model_common_ac->getSellerOrders($filter_data);


      $json['store'] = array(
        'seller_id'  => $seller_info['seller_id'],
        'store_name' => $seller_info['store_name'],
        'store_logo' => $seller_info['store_logo']
      );

      $toal_page = ceil($order_total / $limit);
      $next_page = $page+1;

      $json['pagination'] = array(
        'item' => $order_total,
        'per_page' => $limit,
        'total_page' => $toal_page,
        'current_page' => $page,
        'next_page' =>  ($next_page > $toal_page)? 0 : $next_page,
      );

      $json['orders'] = $results;

    }else{
      $json['error']['warning'] = $this->language->get('error_invalid');
    }

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
