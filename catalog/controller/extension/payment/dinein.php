<?php
class ControllerExtensionPaymentDinein extends Controller {
	public function index() {
			if (isset($this->request->get['seller_store_id'])){
			$data['seller_id']=$this->request->get['seller_store_id'];
			$seller_id =$this->request->get['seller_store_id'];
		} else {$seller_id = '';}
		$this->load->language('extension/payment/bank_transfer');

		$data['bank'] = nl2br($this->config->get('payment_bank_transfer_bank' . $this->config->get('config_language_id')));

		return $this->load->view('extension/payment/dinein', $data);
	}

	public function confirm() {
		$json = array();
			if (isset($this->request->get['seller_store_id'])){
			$data['seller_id']=$this->request->get['seller_store_id'];
			$seller_id =$this->request->get['seller_store_id'];
		} else {$seller_id = '';}
		if ($this->session->data['payment_method']['code'] == 'bank_transfer') {
			$this->load->language('extension/payment/bank_transfer');
		
			$this->load->model('checkout/order');

			$comment  = $this->language->get('text_instruction') . "\n\n";
			$comment .= $this->config->get('payment_bank_transfer_bank' . $this->config->get('config_language_id')) . "\n\n";
			$comment .= $this->language->get('text_payment');

			$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('payment_bank_transfer_order_status_id'), $comment, true);
		
			$json['redirect'] = $this->url->link('checkout/success&seller_store_id='. $seller_id );
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));		
	}
}