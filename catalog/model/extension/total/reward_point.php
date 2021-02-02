<?php
class ModelExtensionTotalRewardPoint extends Model {
	public function getTotal($total) 
	{

		if ($this->config->get('total_reward_point_status') && isset($this->session->data['customer_point']) && $this->customer->getId()) {
			$error = false;
			
			//Active Plan check
			if(!$this->getPlanRewardStatus()){
				$error = true;
			}

			// seller reard setting
			$seller_group = $this->getSellerCustomerGroup();
			if(isset($seller_group['reward_status']) && !$seller_group['reward_status']){
				$error = true;
			}

			//verify total point / max point
			$total_points = $this->totalRewardPoint($seller_group['customer_group_id']);
			if(($this->session->data['customer_point'] > $seller_group['reward_point_max']) || ($this->session->data['customer_point'] > $total_points)){
				$error = true;
			}

			//min point check
			if($this->session->data['customer_point'] < $seller_group['reward_point_min']){
				$error = true;
			}

			//order value check
			if( !$error && ($this->cart->getSubTotal() > 0) && $this->cart->getSubTotal() >= $seller_group['reward_order_total']){

				//calculate value
				$customer_point = $this->session->data['customer_point'];

				$reward_value = $seller_group['reward_point_value']*$customer_point;

				if($reward_value > $this->cart->getSubTotal()){
					$required_points = $this->cart->getSubTotal()/$seller_group['reward_point_value'];
					$customer_point = $this->session->data['customer_point'] = intval($required_points);
					$reward_value = $this->cart->getSubTotal();
				}

				$total['totals'][] = array(
					'code'       => 'reward_point',
					'title'      => sprintf($seller_group['reward_invoice_text']." (%s)", $customer_point),
					'value'      => -$reward_value,
					'sort_order' => $this->config->get('total_reward_point_sort_order')
				);

				$total['total'] -= $reward_value;
				
				//put in session
				$this->session->data['reward_point_data']  = array(
					'point_used' => $customer_point,
					'reward_value' => $reward_value,
					'seller_group_id' =>  $seller_group['customer_group_id']
				);

			}else{
				$this->session->data['customer_point'] = '';
				$this->session->data['reward_point_data'] = '';
				unset($this->session->data['customer_point']);
				unset($this->session->data['reward_point_data']);
			}
		}else{
			$this->session->data['customer_point'] = '';
			$this->session->data['reward_point_data'] = '';
			unset($this->session->data['customer_point']);
			unset($this->session->data['reward_point_data']);
		}
	}

	public function getSellerCustomerGroup($seller_store_id=0)
	{	
		if(!$seller_store_id){
			$seller_store_id = $this->session->data['seller_store_id'];
		}

		if($seller_store_id){
			$customer_group_query = $this->db->query("SELECT * FROM `oc_customer_group` WHERE customer_group_id = (SELECT customer_group_id FROM oc_customer WHERE customer_id = (SELECT seller_id FROM `oc_purpletree_vendor_stores` WHERE store_status = '1' AND id = '". (int)$seller_store_id ."'))");

			if($customer_group_query->num_rows){
				return $customer_group_query->row;
			}
		}

		return false;
	}

	public function totalRewardPoint($seller_customer_group_id)
	{
		$customer_id = $this->customer->getId();
		$total_reward = $this->db->query("SELECT points FROM " . DB_PREFIX . "customer_store_reward WHERE customer_id = '" . (int)$customer_id . "' AND customer_group_id = $seller_customer_group_id LIMIT 1");

		if($total_reward->num_rows){
			return $total_reward->row['points'];
		}else{
			return 0;
		}

	}
	
	public function getPlanRewardStatus()
	{
		$seller_store_id = $this->session->data['seller_store_id'];
		
		$store_query = $this->db->query("SELECT seller_id FROM `oc_purpletree_vendor_stores` WHERE store_status = '1' AND id = '". (int)$seller_store_id ."'");

		if($store_query->num_rows){
			
			$seller_id = $store_query->row['seller_id'];
			$is_plan_active = $this->db->query("SELECT * FROM `oc_purpletree_vendor_plan_subscription` WHERE seller_id = '". (int)$seller_id ."' AND status_id = '1'");
			
			if($is_plan_active->num_rows){
				$seller_plan = $this->db->query("SELECT * FROM `oc_purpletree_vendor_seller_plan` WHERE seller_id = '". (int)$seller_id ."' AND status = '1'");
				if($seller_plan->num_rows){
					$plan_id = $seller_plan->row['plan_id'];
					$plan_query = $this->db->query("SELECT enable_point_system FROM " . DB_PREFIX . "purpletree_vendor_plan WHERE plan_id = '". (int)$plan_id ."' AND `status` = '1'");
					return ($plan_query->num_rows)? $plan_query->row['enable_point_system'] : 0;
				}
			}
		}
		return 0;
	}

}