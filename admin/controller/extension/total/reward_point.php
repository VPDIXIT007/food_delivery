<?php
class ControllerExtensionTotalRewardPoint extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/total/reward_point');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
	
			$this->model_setting_setting->editSetting('total_reward_point', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=total', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=total', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/total/reward_point', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/total/reward_point', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=total', true);

		if (isset($this->request->post['total_reward_point_total'])) {
			$data['total_reward_point_total'] = $this->request->post['total_reward_point_total'];
		} else {
			$data['total_reward_point_total'] = $this->config->get('total_reward_point_total');
		}

		if (isset($this->request->post['total_reward_point_text'])) {
			$data['total_reward_point_text'] = $this->request->post['total_reward_point_text'];
		} else {
			$data['total_reward_point_text'] = $this->config->get('total_reward_point_text');
		}

		if (isset($this->request->post['total_reward_point_value'])) {
			$data['total_reward_point_value'] = $this->request->post['total_reward_point_value'];
		} else {
			$data['total_reward_point_value'] = $this->config->get('total_reward_point_value');
		}

		if (isset($this->request->post['total_reward_point_status'])) {
			$data['total_reward_point_status'] = $this->request->post['total_reward_point_status'];
		} else {
			$data['total_reward_point_status'] = $this->config->get('total_reward_point_status');
		}

		if (isset($this->request->post['total_reward_point_sort_order'])) {
			$data['total_reward_point_sort_order'] = $this->request->post['total_reward_point_sort_order'];
		} else {
			$data['total_reward_point_sort_order'] = $this->config->get('total_reward_point_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/total/reward_point', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/total/reward_point')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}