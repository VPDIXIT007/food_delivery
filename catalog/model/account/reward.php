<?php
class ModelAccountReward extends Model {
	public function getRewards($data = array()) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "customer_reward` WHERE customer_id = '" . (int)$this->customer->getId() . "'";

		$sort_data = array(
			'points',
			'description',
			'date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY date_added";
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

		return $query->rows;
	}

	public function getTotalRewards() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "customer_reward` WHERE customer_id = '" . (int)$this->customer->getId() . "'");

		return $query->row['total'];
	}

	public function getTotalPoints() {
		$query = $this->db->query("SELECT SUM(points) AS total FROM `" . DB_PREFIX . "customer_reward` WHERE customer_id = '" . (int)$this->customer->getId() . "' GROUP BY customer_id");

		if ($query->num_rows) {
			return $query->row['total'];
		} else {
			return 0;
		} 
	}

	public function getRewardHistory(){
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_store_reward` s LEFT JOIN `" . DB_PREFIX . "customer_group_description` d ON s.customer_group_id=d.customer_group_id WHERE customer_id = '" . (int)$this->customer->getId() . "' AND d.language_id =  '" . (int)$this->config->get('config_language_id') . "'");	
		return $query->rows;	
	}
}