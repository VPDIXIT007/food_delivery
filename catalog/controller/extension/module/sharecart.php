<?php
####################################################
#    ShareCart 1.05 for Opencart 230x by AlexDW    #
####################################################
class ControllerExtensionModuleShareCart extends Controller {

	private function clear() {
		$this->cktimeg = $this->config->get('sharecart_share_timeg') ? $this->config->get('sharecart_share_timeg') : '3';
		$this->cktimer = $this->config->get('sharecart_share_timer') ? $this->config->get('sharecart_share_timer') : '14';
		$q1 = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "cart_share'");
		if ($q1->num_rows) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "cart_share WHERE customer_id = '0' AND date_added < DATE_SUB('".date('Y-m-d 00:00:00', time())."', INTERVAL '" . (int)$this->cktimeg . "' DAY)");
			$this->db->query("DELETE FROM " . DB_PREFIX . "cart_share WHERE customer_id != '0' AND date_added < DATE_SUB('".date('Y-m-d 00:00:00', time())."', INTERVAL '" . (int)$this->cktimer . "' DAY)");
		}
	}

	private function getMorph($total_pro) {
			$morf = strlen($total_pro) - 1;
			$total_pro = (string)$total_pro;
			if (($total_pro[$morf] > 0 && $total_pro[$morf] < 5)  )	{
				if (($total_pro < 10) or (strlen($total_pro)!=1 && $total_pro[strlen($total_pro)-2]!=1)) {
				if ($total_pro[$morf] == 1)	{
					$total_pro = 1;
				}	else	{
					$total_pro = 2;
				}
				} else	{
					$total_pro = 3;
				}
			}	else	{
				$total_pro = 3;
			}	
		return $total_pro;
	}

	public function index() {
		if ($this->config->get('sharecart_status')) {

		$this->clear();
		$this->load->language('extension/module/sharecart');
		$data['copy_link'] = $this->language->get('copy_link');
		$data['copy_link_success'] = $this->language->get('copy_link_success');
		$data['sharecart_share'] = $this->language->get('sharecart_share');
		$data['clear_cart'] = $this->language->get('clear_cart');
			$data['warning_text'] = $this->language->get('warning_text');
		$data['share_create_text'] = $this->language->get('share_create_text');
		$data['btn_ckshare_create'] = $this->language->get('btn_ckshare_create');
		$data['button_cancel'] = $this->language->get('btn_ckshare_cancel');
		$data['print_pdf'] = $this->language->get('print_pdf');
		$data['logged'] = $this->config->get('sharecart_share_onlyreg') ? $this->customer->isLogged() : true;
		$data['route'] = $this->request->get['route'];
		return $this->load->view('extension/module/sharecart', $data);
		}
	}

	public function ckshare() {

	if ($this->config->get('sharecart_status')) {
	$this->load->language('extension/module/sharecart');

	$customer = $this->customer->isLogged();
	$logged = $this->config->get('sharecart_share_onlyreg') ? $customer : true;
	$limit_reg = $this->config->get('sharecart_share_limitr');
	$limit_guest = $this->config->get('sharecart_share_limitg');
	$limit = $customer ? (int)$limit_reg : (int)$limit_guest;

	$limit_now = $limit . ' ' . $this->language->get('limit_pos'.$this->getMorph($limit));
	$limit_reg = $limit_reg . ' ' . $this->language->get('limit_pos'.$this->getMorph($limit_reg));
	$share_day = $customer ? (int)$this->config->get('sharecart_share_timer') : (int)$this->config->get('sharecart_share_timeg');
	$share_day = $share_day . ' ' . $this->language->get('share_day'.$this->getMorph($share_day));

	if ($logged) {

		$json = array();
		$hasProducts = count($this->cart->getProducts());

		if (!$hasProducts) {
			$json['nop'] = $this->language->get('share_create_empty');
			$this->response->addHeader('Content-Type: application/json; charset=utf-8');
			$this->response->setOutput(json_encode($json));
			return;
		}

		if ($hasProducts > $limit) {
			$json['nop'] = sprintf($this->language->get('share_create_limit'), $limit_now);
				if (!$customer) {
				$json['nop'] .= sprintf($this->language->get('share_create_reg'), $limit_reg);
				}
			$this->response->addHeader('Content-Type: application/json; charset=utf-8');
			$this->response->setOutput(json_encode($json));
			return;
		}

		$json['share_day'] = sprintf($this->language->get('share_create_day'), $share_day);
		$ip = isset($this->request->server['REMOTE_ADDR']) ? $this->request->server['REMOTE_ADDR'] : '';

		if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
			$forwarded_ip = $this->request->server['HTTP_X_FORWARDED_FOR'];
		} elseif (!empty($this->request->server['HTTP_CLIENT_IP'])) {
			$forwarded_ip = $this->request->server['HTTP_CLIENT_IP'];
		} else {
			$forwarded_ip = '';
		}

		$user_agent = isset($this->request->server['HTTP_USER_AGENT']) ? $this->request->server['HTTP_USER_AGENT'] : '';
		$ckeeper = $this->session->getId();

		$query = $this->db->query("SELECT concat(md5(concat(`product_id`, `option`, `quantity`))) as `uid` FROM `" . DB_PREFIX . "cart` WHERE `session_id` = '" . $this->db->escape($ckeeper) . "' AND customer_id = '" . (int)$customer . "' ORDER BY `product_id` ASC ");

		$data = '';
			foreach ($query->rows as $result) {
				$data = md5($data.$result['uid']);
			}

		$query = $this->db->query("SELECT `share_id` FROM `" . DB_PREFIX . "cart_share` WHERE `share_id` = '" . $this->db->escape($data) . "' LIMIT 1 ");

		if (!$query->num_rows) {
		$query = $this->db->query("INSERT IGNORE INTO `" . DB_PREFIX . "cart_share` (`share_id`, `customer_id`, `product_id`, `option`, `quantity`, `date_added`, `ip`, `forwarded_ip`, `user_agent` ) SELECT '" . $this->db->escape($data) . "' as `share_id`, `customer_id`, `product_id`, `option`, `quantity`, '".date('Y-m-d 00:00:00', time())."' AS `date_added`, '" . $this->db->escape($ip) . "' as `ip`, '" . $this->db->escape($forwarded_ip) . "' as `forwarded_ip`, '" . $this->db->escape($user_agent) . "' as `user_agent` FROM `" . DB_PREFIX . "cart` WHERE `session_id` = '" . $this->db->escape($ckeeper) . "' AND customer_id = '" . (int)$customer . "' ");

		$json['success'] = $this->url->link('extension/module/sharecart/share', 'cart=' . $this->db->escape($data));
		} else {
			$this->db->query("UPDATE `" . DB_PREFIX . "cart_share` SET `date_added` = '".date('Y-m-d 00:00:00', time())."' WHERE `share_id` = '" . $this->db->escape($data) . "' ");
			$json['success'] = $this->url->link('extension/module/sharecart/share', 'cart=' . $this->db->escape($data));
		}

		$this->response->addHeader('Content-Type: application/json; charset=utf-8');
		$this->response->setOutput(json_encode($json));
	}

	}
	}

	public function share() {

		$xx = (VERSION >= '2.2') ? true : 'SSL';
		if ($this->config->get('sharecart_status')) {

		$this->clear();
		$this->load->language('extension/module/sharecart');
		$link = $this->config->get('sharecart_share_redirect');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		$data['continue'] = $this->url->link('common/home');
		$data['success_url'] = !empty($link) ? $this->url->link($link) : $this->url->link('checkout/cart');
		$data['cart_count'] = $cart_cnt = count($this->cart->getProducts());
		$data['cart_total'] = $cart_total = $this->cart->countProducts();
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title2'),
			'href' => $this->url->link('information/contact')
		);

		$data['heading_title'] = $this->language->get('heading_title2');

		$limit_reg = $this->config->get('sharecart_share_elimitr');
		$limit_guest = $this->config->get('sharecart_share_elimitg');
		$limit_guest = $limit_guest . ' ' . $this->language->get('limit_pos'.$this->getMorph($limit_guest));
		$limit_reg = $limit_reg . ' ' . $this->language->get('limit_pos'.$this->getMorph($limit_reg));
		$data['share_add_limit'] = sprintf($this->language->get('share_add_limit'), $limit_guest, $limit_reg);
		$data['share_id'] = $share_id = $this->request->get['cart'];
		$data['text_share_add'] = $this->language->get('text_share_add');
		$data['share_available'] = $this->language->get('share_available');
		$data['share_unavailable'] = $this->language->get('share_unavailable');
		$data['btn_ckshare_add'] = $this->language->get('btn_ckshare_add');
		$data['btn_ckshare_replace'] = $this->language->get('btn_ckshare_replace');
		$data['btn_ckshare_cancel'] = $this->language->get('btn_ckshare_cancel');
		$data['button_continue'] = $this->language->get('button_continue');
		$data['share_updated'] = $this->language->get('share_updated');
		$data['share_updated_nop'] = $this->language->get('share_updated_nop');

		$query = $this->db->query("SELECT count(`share_id`) as cnt, SUM(`quantity`) as quant FROM `" . DB_PREFIX . "cart_share` WHERE `share_id` = '" . $this->db->escape($share_id) . "' ");
		if (!empty($query->row['cnt'])) {
			$data['share_cnt'] = $cnt = $query->row['cnt'];
			$data['share_quantity'] = $quant = $query->row['quant'];
			$cnt = $cnt . ' ' . $this->language->get('share_pos'.$this->getMorph($cnt));
			$quant = $quant . ' ' . $this->language->get('share_pro'.$this->getMorph($quant));
			$data['text_share_info'] = sprintf($this->language->get('text_share_info'), $cnt, $quant);
			$this->db->query("UPDATE " . DB_PREFIX . "cart_share SET date_added = '".date('Y-m-d 00:00:00', time())."' WHERE `share_id` = '" . $this->db->escape($share_id) . "' ");
		} else {
			$data['share_cnt'] = false;
		}

		if ($cart_cnt) {
			$cart_cnt = $cart_cnt . ' ' . $this->language->get('share_pos'.$this->getMorph($cart_cnt));
			$cart_total = $cart_total . ' ' . $this->language->get('share_pro'.$this->getMorph($cart_total));
			$data['text_ckeeper_info'] = sprintf($this->language->get('text_ckeeper_info'), $cart_cnt, $cart_total);
		} else {
			$data['text_ckeeper_info'] = $this->language->get('text_ckeeper_empty');
		}

		$this->response->addHeader('X-Robots-Tag: noindex');
		$this->response->setOutput($this->load->view('extension/module/sharecart_link', $data));

		} else {
			$this->session->data['redirect'] = $this->url->link('error/not_found', '', true);
			$this->response->redirect($this->url->link('error/not_found', '', true));
		}
	}

	public function share_add() {
		if ($this->config->get('sharecart_status')) {
		$json = array();
		$this->load->language('extension/module/sharecart');

		if (isset($this->request->get['cart'])) {

		$limit_reg = $this->config->get('sharecart_share_elimitr');
		$limit_guest = $this->config->get('sharecart_share_elimitg');
		$limit = $this->customer->isLogged() ? (int)$limit_reg : (int)$limit_guest;
		$share_id = $this->request->get['cart'];
		$flag = isset($this->request->get['flag']) && (int)$this->request->get['flag'] == 1 ? !0 : !1;
		$cart_pro = $flag ? count($this->cart->getProducts()) : 0;

		$query = $this->db->query("SELECT count(`share_id`) as cnt, SUM(`quantity`) as quant FROM `" . DB_PREFIX . "cart_share` WHERE `share_id` = '" . $this->db->escape($share_id) . "' ");
		if (empty($query->row['cnt'])) {
			$this->response->addHeader('Content-Type: application/json; charset=utf-8');
			$this->response->setOutput(json_encode($json));
			return;
		} elseif ($cart_pro + (int)$query->row['cnt'] > $limit ) {
			$json['nop'] = $this->language->get('share_wrong');
			$this->response->addHeader('Content-Type: application/json; charset=utf-8');
			$this->response->setOutput(json_encode($json));
			return;
		}

		$query = $this->db->query("SELECT `product_id`, `option`, `quantity` FROM `" . DB_PREFIX . "cart_share` WHERE `share_id` = '" . $this->db->escape($share_id) . "' ");
		if ($query->num_rows && !$flag) {
			$this->cart->clear();
		}
			foreach ($query->rows as $result) {
				$this->cart->add($result['product_id'], $result['quantity'], (array)json_decode($result['option']) );
			}
			$json['success'] = $this->language->get('share_success');
		}

		$this->response->addHeader('Content-Type: application/json; charset=utf-8');
		$this->response->setOutput(json_encode($json));
	}
	}
}
?>