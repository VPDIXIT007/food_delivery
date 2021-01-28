<?php
####################################################
#    ShareCart 1.05 for Opencart 230x by AlexDW    #
####################################################
class ControllerExtensionModuleShareCart extends Controller {

	private $error = array(); 

	public function index() {   
		$this->load->language('extension/module/sharecart');
		$this->ckinstall();

		$this->document->setTitle(strip_tags($this->language->get('heading_title')));
		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('sharecart', $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token']  . '&type=module', true));
		}

		$text_strings = array(
				'heading_title',
				'text_module',
				'text_edit',
				'text_enabled',
				'text_disabled',

				'entry_status',
				'ckday',
				'text_settings',
				'help_share',
				'entry_share_onlyreg',
				'help_share_onlyreg',
				'entry_share_timer',
				'entry_share_timeg',
				'help_share_time',
				'entry_share_limitr',
				'entry_share_limitg',
				'help_share_limit',
				'entry_share_elimitr',
				'entry_share_elimitg',
				'help_share_elimit',
				'entry_share_redirect',
				'help_share_redirect',

				'button_save',
				'button_cancel',
				'button_add_module',
				'button_remove'
		);

		foreach ($text_strings as $text) {
			$data[$text] = $this->language->get($text);
		}

		$config_data = array(
				'status',
				'share_onlyreg',
				'share_timer',
				'share_timeg',
				'share_limitr',
				'share_limitg',
				'share_elimitr',
				'share_elimitg',
				'share_redirect'
		);

		foreach ($config_data as $conf) {
			$conf = 'sharecart_'.$conf;
			if (isset($this->request->post[$conf])) {
				$data[$conf] = $this->request->post[$conf];
			} else {
				$data[$conf] = $this->config->get($conf);
			}
		}

 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'] , true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/sharecart', 'user_token=' . $this->session->data['user_token'] , true)
		);

		$data['action'] = $this->url->link('extension/module/sharecart', 'user_token=' . $this->session->data['user_token'] , true);
		$data['cancel'] = $this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token']  . '&type=module', true);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('extension/module/sharecart', $data));
	}

	public function install() {
		$this->load->model('user/user_group');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'catalog/sharecart');
		$this->ckinstall();
	}

	protected function ckinstall() {

		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX ."cart_share` ( `uid` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, `share_id` VARCHAR(32) NOT NULL, `customer_id` INT(11) NOT NULL, `product_id` INT(11) NOT NULL, `option` TEXT NOT NULL, `quantity` INT(5) NOT NULL, `date_added` DATETIME NOT NULL, `ip` VARCHAR(40) NOT NULL, `forwarded_ip` VARCHAR(40) NOT NULL, `user_agent` VARCHAR(255) NOT NULL, PRIMARY KEY (`uid`), INDEX `uid` (`share_id`, `product_id`, `customer_id`) ) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB";

		$query = $this->db->query($sql);

		$qu = $this->db->query("DESCRIBE `" . DB_PREFIX . "cart_share` `uid`");
		if ($qu->num_rows == 0) {
			$sqladd = $this->db->query("ALTER TABLE `" . DB_PREFIX ."cart_share` ADD `uid` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT CHARACTER SET utf8 COLLATE utf8_general_ci");
		}

		$qu = $this->db->query("DESCRIBE `" . DB_PREFIX . "cart_share` `share_id`");
		if ($qu->num_rows == 0) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX ."cart_share` ADD `share_id` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `uid`");
		}

		$qu = $this->db->query("DESCRIBE `" . DB_PREFIX . "cart_share` `customer_id`");
		if ($qu->num_rows == 0) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX ."cart_share` ADD `customer_id` INT(11) NOT NULL CHARACTER SET utf8 COLLATE utf8_general_ci AFTER `share_id`");
		}

		$qu = $this->db->query("DESCRIBE `" . DB_PREFIX . "cart_share` `product_id`");
		if ($qu->num_rows == 0) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX ."cart_share` ADD `product_id` INT(11) NOT NULL CHARACTER SET utf8 COLLATE utf8_general_ci AFTER `customer_id`");
		}

		$qu = $this->db->query("DESCRIBE `" . DB_PREFIX . "cart_share` `option`");
		if ($qu->num_rows == 0) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX ."cart_share` ADD `option` TEXT NOT NULL CHARACTER SET utf8 COLLATE utf8_general_ci AFTER `product_id`");
		}

		$qu = $this->db->query("DESCRIBE `" . DB_PREFIX . "cart_share` `quantity`");
		if ($qu->num_rows == 0) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX ."cart_share` ADD `quantity` INT(5) NOT NULL CHARACTER SET utf8 COLLATE utf8_general_ci AFTER `option`");
		}

		$qu = $this->db->query("DESCRIBE `" . DB_PREFIX . "cart_share` `ip`");
		if ($qu->num_rows == 0) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX ."cart_share` ADD `ip` VARCHAR(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `share_id`");
		}

		$qu = $this->db->query("DESCRIBE `" . DB_PREFIX . "cart_share` `forwarded_ip`");
		if ($qu->num_rows == 0) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX ."cart_share` ADD `forwarded_ip` VARCHAR(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `ip`");
		}

		$qu = $this->db->query("DESCRIBE `" . DB_PREFIX . "cart_share` `user_agent`");
		if ($qu->num_rows == 0) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX ."cart_share` ADD `user_agent` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `forwarded_ip`");
		}

		$qu = $this->db->query("DESCRIBE `" . DB_PREFIX . "cart_share` `date_added`");
		if ($qu->num_rows == 0) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX ."cart_share` ADD `date_added` DATETIME NOT NULL CHARACTER SET utf8 COLLATE utf8_general_ci AFTER `quantity`");
		}
	}

	protected function validate() { 
		if (!$this->user->hasPermission('modify', 'extension/module/sharecart')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		return !$this->error;
	}
}
?>