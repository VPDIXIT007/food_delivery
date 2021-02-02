<?php
class ControllerExtensionTotalRewardPoint extends Controller {
	public function index() {
		//reward point
	}

	public function show()
	{
		
		$this->load->model('extension/total/reward_point');
		if ($this->customer->getId() && $this->config->get('total_reward_point_status') && $this->model_extension_total_reward_point->getPlanRewardStatus()) {
				$this->load->language('extension/total/reward');
			
				$seller_store_id = $this->session->data['seller_store_id'];
				$seller_group = $this->model_extension_total_reward_point->getSellerCustomerGroup($seller_store_id);
				if(isset($seller_group['reward_status']) && $seller_group['reward_status'] && $this->cart->getSubTotal() >= $seller_group['reward_order_total']){
					$total_point = $this->model_extension_total_reward_point->totalRewardPoint($seller_group['customer_group_id']);

					$data['total_point'] = $total_point;
					$data['max_point']   = min($total_point, $seller_group['reward_point_max']);
					$data['seller_store_id'] = $seller_store_id;
					$data['point_used'] = $this->session->data['customer_point'];
					$data['min_point'] = min($total_point,$seller_group['reward_point_min']);

					$data['heading_title'] = sprintf($this->language->get('heading_title'), $total_point);
			
					$data['entry_reward'] = sprintf($this->language->get('entry_reward'), $data['max_point'], $data['min_point']);

					return $this->load->view('extension/total/reward_point', $data);
				}
		}
	}

	public function reward() {
		
		$this->load->model('extension/total/reward_point');
		$this->load->language('extension/total/reward');

		$json = array();

		if(isset($this->request->post['reward_point']) && $this->request->post['reward_point'] == 0){
			
			unset($this->session->data['customer_point']);
			$json['success'] = $this->language->get('text_remove_success');

		}else{
			
			if($this->model_extension_total_reward_point->getPlanRewardStatus()){
				$seller_store_id = $this->session->data['seller_store_id'];
				$seller_group = $this->model_extension_total_reward_point->getSellerCustomerGroup($seller_store_id);
		
				if(empty($seller_group)){
					$json['error'] = $this->language->get('error_reward_disable');
				}
		
				if (empty($this->request->post['reward_point'])) {
					$json['error'] = $this->language->get('error_reward');
				}
		
				if(!empty($seller_group)){
					
					if(!$seller_group['reward_status']){
						$json['error'] = $this->language->get('error_reward_disable');
					}
	
					$points = $this->model_extension_total_reward_point->totalRewardPoint($seller_group['customer_group_id']);
		
					if ($this->request->post['reward_point'] > $points) {
						$json['error'] = sprintf($this->language->get('error_points'), $this->request->post['reward']);
					}
			
					if ($this->request->post['reward_point'] > $seller_group['reward_point_max']) {
						$json['error'] = sprintf($this->language->get('error_maximum'), $seller_group['reward_point_max']);
					}

					if ($this->request->post['reward_point'] < $seller_group['reward_point_min']) {
						$json['error'] = sprintf($this->language->get('error_minimum'), $seller_group['reward_point_min']);
					}
		
					if($this->cart->getSubTotal() < $seller_group['reward_order_total']){
						$json['error'] = sprintf($this->language->get('error_reward_order_total'), $seller_group['reward_order_total']);
					}
				}
		
				if (!$json) {
					$this->session->data['customer_point'] = $this->request->post['reward_point'];

					$reward_value = $seller_group['reward_point_value']*$this->request->post['reward_point'];
					if($reward_value > $this->cart->getSubTotal()){
						$required_points = $this->cart->getSubTotal()/$seller_group['reward_point_value'];
						$this->session->data['customer_point'] = intval($required_points);
					}
					
					$json['customer_point'] = $this->session->data['customer_point'];
					$json['success'] = $this->language->get('text_success');
				}
			}else{
				$json['error'] = $this->language->get('error_plan_status');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

}
