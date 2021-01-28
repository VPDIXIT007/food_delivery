<?php
####################################################
#    ShareCart 1.05 for Opencart 230x by AlexDW    #
####################################################
class ModelExtensionModuleShareCart extends Model {

	private $stotal;

	public function getFoundShares() {
	global $stotal;
	return $stotal;
	}

	public function deleteShare($session_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "cart_share` WHERE share_id = '" . $this->db->escape($session_id) . "'");
	}

	public function refreshShare($session_id) {
		$this->db->query("UPDATE `" . DB_PREFIX . "cart_share` SET date_added = NOW() WHERE share_id = '" . $this->db->escape($session_id) . "'");
	}

	public function getShare($data = array()) {
		$sql = "SELECT SQL_CALC_FOUND_ROWS adw.*, CONCAT(cu.firstname, ' ', cu.lastname) AS customer_name FROM (SELECT share_id, SUM(quantity) as quantity, MAX(customer_id) AS customer_id, MAX(date_added) AS date_added, `ip`, `forwarded_ip`, `user_agent` FROM `" . DB_PREFIX . "cart_share` GROUP BY share_id) adw LEFT JOIN `" . DB_PREFIX . "customer` cu ON (cu.customer_id = adw.customer_id) WHERE 1 ";

		if (!empty($data['filter_name'])) {
			$sql .= " AND LCASE(CONCAT(cu.firstname, ' ', cu.lastname)) LIKE LCASE('%" . $this->db->escape($data['filter_name']) . "%') ";
		}

		if (isset($data['filter_status'])) {
			if ((int)$data['filter_status'] > 0) {
				$sql .= " AND adw.customer_id <> 0 AND CONCAT(cu.firstname, ' ', cu.lastname) IS NOT NULL ";
			} else {
				$sql .= " AND (adw.customer_id = 0 OR CONCAT(cu.firstname, ' ', cu.lastname) IS NULL) ";
			}
		}

		if (!empty($data['filter_quantity'])) {
			$sql .= " AND adw.quantity >= '" . (int)($data['filter_quantity']) . "' ";
		}

		if (!empty($data['filter_ip'])) {
			$sql .= " AND LCASE(CONCAT(adw.ip, ' ', adw.forwarded_ip)) LIKE '%" . $this->db->escape($data['filter_ip']) . "%'";
		}

		if (!empty($data['filter_user_agent'])) {
			$sql .= " AND LCASE(adw.user_agent) LIKE LCASE('%" . $this->db->escape($data['filter_user_agent']) . "%')";
		}

		if (!empty($data['filter_session'])) {
			$sql .= " AND LCASE(adw.share_id) LIKE LCASE('%" . $this->db->escape($data['filter_session']) . "%')";
		}

		if ((!empty($data['filter_date_added'])) XOR (!empty($data['filter_date_added_end']))) {
			$sql .= " AND (DATE(adw.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "') OR DATE(adw.date_added) = DATE('" . $this->db->escape($data['filter_date_added_end']) . "'))";
		}

		if ((!empty($data['filter_date_added'])) AND (!empty($data['filter_date_added_end']))) {
			$sql .= " AND (DATE(adw.date_added) >= DATE('" . $this->db->escape($data['filter_date_added']) . "') AND DATE(adw.date_added) <= DATE('" . $this->db->escape($data['filter_date_added_end']) . "'))";
		}

		$sort_data = array(
			'share_id',
			'quantity',
			'date_added',
			'customer_name'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY adw.date_added";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		$num_query = $this->db->query("SELECT FOUND_ROWS() AS `found_rows`");

		global $stotal;
		$stotal = intval($num_query->row['found_rows']);

		return $query->rows;
	}

	public function getShareProducts($ckeeper) {
		$this->load->model('tool/image');
		$this->load->language('extension/module/sharecart');
		$product_data = array();

		$cart_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cart_share WHERE share_id = '" . $this->db->escape($ckeeper) . "'");

		foreach ($cart_query->rows as $cart) {
			$stock = true;

			$product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_store p2s LEFT JOIN " . DB_PREFIX . "product p ON (p2s.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND p2s.product_id = '" . (int)$cart['product_id'] . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.date_available <= NOW() AND p.status = '1'");

			if ($product_query->num_rows && ($cart['quantity'] > 0)) {
				$option_price = 0;
				$option_points = 0;
				$option_weight = 0;

				$option_data = array();

				foreach (json_decode($cart['option']) as $product_option_id => $value) {
					$option_query = $this->db->query("SELECT po.product_option_id, po.option_id, od.name, o.type FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_option_id = '" . (int)$product_option_id . "' AND po.product_id = '" . (int)$cart['product_id'] . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");

					if ($option_query->num_rows) {
						if ($option_query->row['type'] == 'select' || $option_query->row['type'] == 'radio') {
							$option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$value . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

							if ($option_value_query->num_rows) {
								if ($option_value_query->row['price_prefix'] == '+') {
									$option_price += $option_value_query->row['price'];
								} elseif ($option_value_query->row['price_prefix'] == '-') {
									$option_price -= $option_value_query->row['price'];
								}

								if ($option_value_query->row['points_prefix'] == '+') {
									$option_points += $option_value_query->row['points'];
								} elseif ($option_value_query->row['points_prefix'] == '-') {
									$option_points -= $option_value_query->row['points'];
								}

								if ($option_value_query->row['weight_prefix'] == '+') {
									$option_weight += $option_value_query->row['weight'];
								} elseif ($option_value_query->row['weight_prefix'] == '-') {
									$option_weight -= $option_value_query->row['weight'];
								}

								if ($option_value_query->row['subtract'] && (!$option_value_query->row['quantity'] || ($option_value_query->row['quantity'] < $cart['quantity']))) {
									$stock = false;
								}

								$option_data[] = array(
									'product_option_id'       => $product_option_id,
									'product_option_value_id' => $value,
									'option_id'               => $option_query->row['option_id'],
									'option_value_id'         => $option_value_query->row['option_value_id'],
									'name'                    => $option_query->row['name'],
									'value'                   => $option_value_query->row['name'],
									'type'                    => $option_query->row['type'],
									'quantity'                => $option_value_query->row['quantity'],
									'subtract'                => $option_value_query->row['subtract'],
									'price'                   => $option_value_query->row['price'],
									'price_prefix'            => $option_value_query->row['price_prefix'],
									'points'                  => $option_value_query->row['points'],
									'points_prefix'           => $option_value_query->row['points_prefix'],
									'weight'                  => $option_value_query->row['weight'],
									'weight_prefix'           => $option_value_query->row['weight_prefix']
								);
							}
						} elseif ($option_query->row['type'] == 'checkbox' && is_array($value)) {
							foreach ($value as $product_option_value_id) {
								$option_value_query = $this->db->query("SELECT pov.option_value_id, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix, ovd.name FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (pov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$product_option_value_id . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

								if ($option_value_query->num_rows) {
									if ($option_value_query->row['price_prefix'] == '+') {
										$option_price += $option_value_query->row['price'];
									} elseif ($option_value_query->row['price_prefix'] == '-') {
										$option_price -= $option_value_query->row['price'];
									}

									if ($option_value_query->row['points_prefix'] == '+') {
										$option_points += $option_value_query->row['points'];
									} elseif ($option_value_query->row['points_prefix'] == '-') {
										$option_points -= $option_value_query->row['points'];
									}

									if ($option_value_query->row['weight_prefix'] == '+') {
										$option_weight += $option_value_query->row['weight'];
									} elseif ($option_value_query->row['weight_prefix'] == '-') {
										$option_weight -= $option_value_query->row['weight'];
									}

									if ($option_value_query->row['subtract'] && (!$option_value_query->row['quantity'] || ($option_value_query->row['quantity'] < $cart['quantity']))) {
										$stock = false;
									}

									$option_data[] = array(
										'product_option_id'       => $product_option_id,
										'product_option_value_id' => $product_option_value_id,
										'option_id'               => $option_query->row['option_id'],
										'option_value_id'         => $option_value_query->row['option_value_id'],
										'name'                    => $option_query->row['name'],
										'value'                   => $option_value_query->row['name'],
										'type'                    => $option_query->row['type'],
										'quantity'                => $option_value_query->row['quantity'],
										'subtract'                => $option_value_query->row['subtract'],
										'price'                   => $option_value_query->row['price'],
										'price_prefix'            => $option_value_query->row['price_prefix'],
										'points'                  => $option_value_query->row['points'],
										'points_prefix'           => $option_value_query->row['points_prefix'],
										'weight'                  => $option_value_query->row['weight'],
										'weight_prefix'           => $option_value_query->row['weight_prefix']
									);
								}
							}
						} elseif ($option_query->row['type'] == 'text' || $option_query->row['type'] == 'textarea' || $option_query->row['type'] == 'file' || $option_query->row['type'] == 'date' || $option_query->row['type'] == 'datetime' || $option_query->row['type'] == 'time') {
							$option_data[] = array(
								'product_option_id'       => $product_option_id,
								'product_option_value_id' => '',
								'option_id'               => $option_query->row['option_id'],
								'option_value_id'         => '',
								'name'                    => $option_query->row['name'],
								'value'                   => $value,
								'type'                    => $option_query->row['type'],
								'quantity'                => '',
								'subtract'                => '',
								'price'                   => '',
								'price_prefix'            => '',
								'points'                  => '',
								'points_prefix'           => '',
								'weight'                  => '',
								'weight_prefix'           => ''
							);
						}
					}
				}

				$product_data[] = array(
					'cart_id'         => $cart['uid'],
					'product_id'      => $product_query->row['product_id'],
					'view'			=> HTTP_CATALOG.'index.php?route=product/product&product_id=' . $product_query->row['product_id'],
					'edit'			=> $this->url->link('catalog/product/edit', 'token=' . $this->session->data['token'] . '&product_id=' . $product_query->row['product_id'], true),
					'name'            => $product_query->row['name'],
					'model'           => $product_query->row['model'],
					'shipping'        => $product_query->row['shipping'],
					'image'           => (isset($product_query->row['image']) && $product_query->row['image'] !='' && is_file(DIR_IMAGE . $product_query->row['image'])) ? $this->model_tool_image->resize($product_query->row['image'], 40,40) : $this->model_tool_image->resize('no_image.png', 40,40),
					'option'          => $option_data,
					'quantity'        => $cart['quantity']
				);
			} else {
				$product_data[] = array(
					'name'			=> $this->language->get('unknown_product'), //'deleted position',
					'model'			=> 'product_id=' . $cart['product_id'],
					'image'			=> $this->model_tool_image->resize('no_image.png', 40,40),
					'option'		=> array(),
					'cart_id'		=> $cart['uid'],
					'quantity'		=> $cart['quantity']
				);
			}
		}

		return $product_data;
	}

}