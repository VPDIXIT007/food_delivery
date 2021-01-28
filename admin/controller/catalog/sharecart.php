<?php
####################################################
#    ShareCart 1.05 for Opencart 230x by AlexDW    #
####################################################
class ControllerCatalogShareCart extends Controller {
	private $error = array();

	private function clear() {
		$this->cktimeg = $this->config->get('sharecart_share_timeg') ? $this->config->get('sharecart_share_timeg') : '3';
		$this->cktimer = $this->config->get('sharecart_share_timer') ? $this->config->get('sharecart_share_timer') : '14';
		$q1 = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "cart_share'");
		if ($q1->num_rows) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "cart_share WHERE customer_id = '0' AND date_added < DATE_SUB(NOW(), INTERVAL '" . (int)$this->cktimeg . "' DAY)");
			$this->db->query("DELETE FROM " . DB_PREFIX . "cart_share WHERE customer_id != '0' AND date_added < DATE_SUB(NOW(), INTERVAL '" . (int)$this->cktimer . "' DAY)");
		}
	}

	public function index() {
		$this->clear();
		$this->load->language('extension/module/sharecart');
		$this->document->setTitle(strip_tags($this->language->get('heading_title')));
		$this->load->model('extension/module/sharecart');
		$this->getList();
	}

	public function refresh() {
		$this->load->language('extension/module/sharecart');
		$this->document->setTitle(strip_tags($this->language->get('heading_title')));
		$this->load->model('extension/module/sharecart');
		$xx = (VERSION >= '2.2') ? true : 'SSL';

		if (isset($this->request->post['selected']) && $this->validate()) {
			foreach ($this->request->post['selected'] as $session_id) {
				$this->model_extension_module_sharecart->refreshShare($session_id);
			}

			$this->session->data['success'] = $this->language->get('success_list');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . urlencode(html_entity_decode($this->request->get['filter_quantity'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_ip'])) {
				$url .= '&filter_ip=' . urlencode(html_entity_decode($this->request->get['filter_ip'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_user_agent'])) {
				$url .= '&filter_user_agent=' . urlencode(html_entity_decode($this->request->get['filter_user_agent'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_session'])) {
				$url .= '&filter_session=' . urlencode(html_entity_decode($this->request->get['filter_session'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}

			if (isset($this->request->get['filter_date_added_end'])) {
				$url .= '&filter_date_added_end=' . $this->request->get['filter_date_added_end'];
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

			$this->response->redirect($this->url->link('catalog/sharecart','user_token=' . $this->session->data['user_token']  . $url, $xx));
		}

		$this->getList();
	}

	public function delete() {
		$this->load->language('extension/module/sharecart');
		$this->document->setTitle(strip_tags($this->language->get('heading_title')));
		$this->load->model('extension/module/sharecart');
		$xx = (VERSION >= '2.2') ? true : 'SSL';

		if (isset($this->request->post['selected']) && $this->validate()) {
			foreach ($this->request->post['selected'] as $session_id) {
				$this->model_extension_module_sharecart->deleteShare($session_id);
			}

			$this->session->data['success'] = $this->language->get('success_list');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . urlencode(html_entity_decode($this->request->get['filter_quantity'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_ip'])) {
				$url .= '&filter_ip=' . urlencode(html_entity_decode($this->request->get['filter_ip'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_user_agent'])) {
				$url .= '&filter_user_agent=' . urlencode(html_entity_decode($this->request->get['filter_user_agent'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_session'])) {
				$url .= '&filter_session=' . urlencode(html_entity_decode($this->request->get['filter_session'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}

			if (isset($this->request->get['filter_date_added_end'])) {
				$url .= '&filter_date_added_end=' . $this->request->get['filter_date_added_end'];
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

			$this->response->redirect($this->url->link('catalog/sharecart', 'user_token=' . $this->session->data['user_token']  . $url, $xx));
		}

		$this->getList();
	}

	protected function getList() {
		$xx = (VERSION >= '2.2') ? true : 'SSL';

		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}

		if (isset($this->request->get['filter_quantity'])) {
			$filter_quantity = $this->request->get['filter_quantity'];
		} else {
			$filter_quantity = null;
		}

		if (isset($this->request->get['filter_ip'])) {
			$filter_ip = $this->request->get['filter_ip'];
		} else {
			$filter_ip = null;
		}

		if (isset($this->request->get['filter_user_agent'])) {
			$filter_user_agent = $this->request->get['filter_user_agent'];
		} else {
			$filter_user_agent = null;
		}

		if (isset($this->request->get['filter_session'])) {
			$filter_session = $this->request->get['filter_session'];
		} else {
			$filter_session = null;
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}

		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = null;
		}

		if (isset($this->request->get['filter_date_added_end'])) {
			$filter_date_added_end = $this->request->get['filter_date_added_end'];
		} else {
			$filter_date_added_end = null;
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'r.date_added';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . urlencode(html_entity_decode($this->request->get['filter_quantity'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_ip'])) {
			$url .= '&filter_ip=' . urlencode(html_entity_decode($this->request->get['filter_ip'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_user_agent'])) {
			$url .= '&filter_user_agent=' . urlencode(html_entity_decode($this->request->get['filter_user_agent'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_session'])) {
			$url .= '&filter_session=' . urlencode(html_entity_decode($this->request->get['filter_session'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_added_end'])) {
			$url .= '&filter_date_added_end=' . $this->request->get['filter_date_added_end'];
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
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'] , $xx)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/sharecart', 'user_token=' . $this->session->data['user_token']  . $url, $xx)
		);

		$data['refresh'] = $this->url->link('catalog/sharecart/refresh', 'user_token=' . $this->session->data['user_token']  . $url, $xx);
		$data['delete'] = $this->url->link('catalog/sharecart/delete', 'user_token=' . $this->session->data['user_token'] . $url, $xx);
		$data['stng'] = $this->url->link('extension/module/sharecart', 'user_token=' . $this->session->data['user_token'] , $xx);

		$data['carts'] = array();

		$filter_data = array(
			'filter_name'		=> $filter_name,
			'filter_quantity'	=> $filter_quantity,
			'filter_ip'			=> $filter_ip,
			'filter_user_agent'	=> $filter_user_agent,
			'filter_session'	=> $filter_session,
			'filter_status'     => $filter_status,
			'filter_date_added' => $filter_date_added,
			'filter_date_added_end' => $filter_date_added_end,
			'sort'              => $sort,
			'order'             => $order,
			'start'             => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'             => $this->config->get('config_limit_admin')
		);

		$results = $this->model_extension_module_sharecart->getShare($filter_data);
		$cart_total = $this->model_extension_module_sharecart->getFoundShares();

		foreach ($results as $result) {
			$data['carts'][] = array(
				'share_id'		=> $result['share_id'],
				'share_link'	=> HTTP_CATALOG.'index.php?route=extension/module/sharecart/share&cart=' . $result['share_id'],
				'customer_id'	=> $result['customer_id'],
				'customer_name'	=> $result['customer_name'] ? $result['customer_name'] : $this->language->get('guest'),
				'customer_link'	=> $result['customer_name'] ? $this->url->link('customer/customer/edit', 'user_token=' . $this->session->data['user_token']  . '&customer_id=' . $result['customer_id'], $xx) : '',
				
				'date_added'	=> date('d.m.Y H:i:s', strtotime($result['date_added'])),
				'quantity'		=> $result['quantity'],
				'ip'			=> (!empty($result['ip']) || !empty($result['forwarded_ip'])) ? $result['ip'] . ' / ' . $result['forwarded_ip'] : false,
				'user_agent'	=> $result['user_agent'],
				'pro' => $this->model_extension_module_sharecart->getShareProducts($result['share_id'])

			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_sharelist'] = $this->language->get('text_sharelist');
		$data['text_clear'] = $this->language->get('text_clear');
		$data['text_status'] = $this->language->get('text_status');
		$data['guest'] = $this->language->get('guest');
		$data['registered'] = $this->language->get('registered');
		$data['help_pro'] = $this->language->get('help_pro');
		$data['column_pro'] = $this->language->get('column_pro');

		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_edit'] = $this->language->get('text_edit');

		$data['customer'] = $this->language->get('customer');
		$data['session'] = $this->language->get('session');
		$data['uid'] = $this->language->get('uid');
		$data['ip'] = $this->language->get('ip');
		$data['user_agent'] = $this->language->get('user_agent');
		$data['total_goods'] = $this->language->get('total_goods');
		$data['column_status'] = $this->language->get('column_status');
		$data['date_added'] = $this->language->get('date_added');
		$data['text_date_beg'] = $this->language->get('text_date_beg');
		$data['text_date_end'] = $this->language->get('text_date_end');
		$data['column_action'] = $this->language->get('column_action');

		$data['entry_product'] = $this->language->get('entry_product');
		$data['entry_author'] = $this->language->get('entry_author');
		$data['entry_rating'] = $this->language->get('entry_rating');
		$data['entry_status'] = $this->language->get('entry_status');

		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_refresh'] = $this->language->get('button_refresh');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_filter'] = $this->language->get('button_filter');

		$data['confirm_refresh'] = $this->language->get('confirm_refresh');
		$data['confirm_delete'] = $this->language->get('confirm_delete');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');

		$data['token'] =  $this->session->data['user_token']; 

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

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . urlencode(html_entity_decode($this->request->get['filter_quantity'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_ip'])) {
			$url .= '&filter_ip=' . urlencode(html_entity_decode($this->request->get['filter_ip'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_user_agent'])) {
			$url .= '&filter_user_agent=' . urlencode(html_entity_decode($this->request->get['filter_user_agent'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_session'])) {
			$url .= '&filter_session=' . urlencode(html_entity_decode($this->request->get['filter_session'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_added_end'])) {
			$url .= '&filter_date_added_end=' . $this->request->get['filter_date_added_end'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_customer_name'] = $this->url->link('catalog/sharecart', 'user_token=' . $this->session->data['user_token']  . '&sort=customer_name' . $url, $xx);
		$data['sort_share_id'] = $this->url->link('catalog/sharecart', 'user_token=' . $this->session->data['user_token']  . '&sort=share_id' . $url, $xx);
		$data['sort_quantity'] = $this->url->link('catalog/sharecart', 'user_token=' . $this->session->data['user_token']  . '&sort=quantity' . $url, $xx);
		$data['sort_date_added'] = $this->url->link('catalog/sharecart', 'user_token=' . $this->session->data['user_token']  . '&sort=date_added' . $url, $xx);

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . urlencode(html_entity_decode($this->request->get['filter_quantity'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_ip'])) {
			$url .= '&filter_ip=' . urlencode(html_entity_decode($this->request->get['filter_ip'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_user_agent'])) {
			$url .= '&filter_user_agent=' . urlencode(html_entity_decode($this->request->get['filter_user_agent'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_session'])) {
			$url .= '&filter_session=' . urlencode(html_entity_decode($this->request->get['filter_session'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_added_end'])) {
			$url .= '&filter_date_added_end=' . $this->request->get['filter_date_added_end'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $cart_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/sharecart', 'user_token=' . $this->session->data['user_token']  . $url . '&page={page}', $xx);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($cart_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($cart_total - $this->config->get('config_limit_admin'))) ? $cart_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $cart_total, ceil($cart_total / $this->config->get('config_limit_admin')));

		$data['filter_name'] = $filter_name;
		$data['filter_quantity'] = $filter_quantity;
		$data['filter_ip'] = $filter_ip;
		$data['filter_user_agent'] = $filter_user_agent;
		$data['filter_session'] = $filter_session;
		$data['filter_status'] = $filter_status;
		$data['filter_date_added'] = $filter_date_added;
		$data['filter_date_added_end'] = $filter_date_added_end;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/sharecart_list', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'catalog/sharecart')) {
			$this->error['warning'] = $this->language->get('error_permission_list');
		}

		return !$this->error;
	}
}