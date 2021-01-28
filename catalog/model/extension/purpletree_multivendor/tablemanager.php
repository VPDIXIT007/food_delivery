<?php
class ModelExtensionPurpletreeMultivendorTablemanager extends Model{

		public function addTable($data) {
			$table_no = $data['product_description'][1]['name'];
			$this->db->query("INSERT INTO " . DB_PREFIX . "table_manger SET table_no = '" . (int)$table_no . "', status =1, seller_id = '" . $this->db->escape($data['seller_id']) . "'");
			$table_id = $this->db->getLastId();
			foreach ($data['product_description'] as $language_id => $value) {
			
			$this->db->query("INSERT INTO " . DB_PREFIX . "table_description SET table_id = '" . (int)$table_id . "', name = '" . (int)$value['name'] . "',description = '" . $this->db->escape($value['description']) . "',language = '" . (int)$language_id . "'");
			} 
		}
		
		public function sellerTotalTable($seller_id){
			$query=$this->db->query("SELECT COUNT(*) AS total_table FROM " . DB_PREFIX . "table_manger WHERE seller_id='".(int) $seller_id."'");
			if($query->num_rows){	
				return $query->row;
				} else {
				return NULL;	
			}
		}
			public function getNoOfTableForMultiplePlan($seller_id){
			$query=$this->db->query("SELECT SUM(pvp.no_of_tables) AS no_of_tables FROM " . DB_PREFIX . "purpletree_vendor_plan pvp LEFT JOIN " . DB_PREFIX . "purpletree_vendor_seller_plan pvsp ON (pvp.plan_id=pvsp.plan_id) WHERE pvsp.seller_id='".(int) $seller_id."' AND pvsp.new_status=1");
			if($query->num_rows){	
				return $query->row;
				} else {
				return NULL;	
			}
		}
		
			public function getNoOfTable($seller_id){
			$query=$this->db->query("SELECT pvp.no_of_tables FROM " . DB_PREFIX . "purpletree_vendor_plan pvp LEFT JOIN " . DB_PREFIX . "purpletree_vendor_seller_plan pvsp ON (pvp.plan_id=pvsp.plan_id) WHERE pvsp.seller_id='".(int) $seller_id."' AND pvsp.status=1");
			if($query->num_rows){	
				return $query->row;
				} else {
				return NULL;	
			}
		}
		
		public function getSsellerplanStatus($seller_id) {
			$sql="SELECT pvps.status_id FROM ". DB_PREFIX ."purpletree_vendor_plan pvp  LEFT JOIN ". DB_PREFIX ."purpletree_vendor_plan_description pvpd ON (pvp.plan_id=pvpd.plan_id) LEFT JOIN ". DB_PREFIX ."purpletree_vendor_seller_plan pvsp ON (pvp.plan_id=pvsp.plan_id) LEFT JOIN ". DB_PREFIX ."purpletree_vendor_plan_subscription pvps ON ((pvps.seller_id = pvsp.seller_id) AND (pvps.status_id = pvp.status)) WHERE pvpd.language_id='".(int)$this->config->get('config_language_id') ."' AND pvsp.seller_id='".(int)$seller_id."' AND pvsp.status=1";
			$query = $this->db->query($sql);
			if($query->num_rows){
				return $query->row['status_id'];
				} else { 
				return false;
			}
		}
		public function getInvoiceStatus($seller_id){
			$query=$this->db->query("SELECT pvpi.status_id AS invoice_status FROM " . DB_PREFIX . "purpletree_vendor_plan_invoice pvpi LEFT JOIN " . DB_PREFIX . "purpletree_vendor_seller_plan pvsp ON (pvpi.invoice_id = pvsp.invoice_id) WHERE pvsp.seller_id='".(int) $seller_id."' AND pvsp.status=1");
			if($query->num_rows){	
				return $query->row['invoice_status'];
				} else {
				return NULL;	
			}
		}
		
			public function getTotalSellerTables($data = array()) {
			$sql = "SELECT COUNT(DISTINCT t.id) AS total FROM " . DB_PREFIX . "table_manger t LEFT JOIN " . DB_PREFIX . "table_description td ON (t.id = td.table_id) JOIN " .DB_PREFIX. "customer c ON(c.customer_id=t.seller_id)";
			
			$sql .= " WHERE td.language = '" . (int)$this->config->get('config_language_id') . "'";
			if(!empty($data['seller_id'])){
				$sql .= " AND t.seller_id ='".(int)$data['seller_id']."'";
			}
			
			$query = $this->db->query($sql);
			
			return $query->row['total'];
		}
	
			
			public function getSellerTables($data=array()){
			
			$sql = "SELECT td.*,t.* FROM " . DB_PREFIX . "table_manger t LEFT JOIN " . DB_PREFIX . "table_description td ON (t.id = td.table_id) JOIN " .DB_PREFIX. "customer c ON(c.customer_id=t.seller_id) ";
			
			if(!empty($data['seller_id'])){
				
				$sql .= " AND td.language = '" . (int)$this->config->get('config_language_id') . "' AND t.seller_id ='".(int)$data['seller_id']."'";
				} else {
				$sql .= " AND td.language = '" . (int)$this->config->get('config_language_id') . "'";
			}	
			//
			$sql .= " GROUP BY t.id";
			
			$sort_data = array(
			'td.name',
			
			);
			
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
				} else {
				$sql .= " ORDER BY td.name";
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
					$data['limit'] = 5;
				}
				
				$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			}
			//echo $sql;
			//	die;
			$query = $this->db->query($sql);
			
			return $query->rows;
		}

	
			
			
			public function tablePlanInfo($seller_id) {
			$query=$this->db->query("SELECT id FROM ". DB_PREFIX . "purpletree_vendor_plan_subscription  WHERE status_id=1 AND seller_id = '" . (int)$seller_id . "'");
			if($query->num_rows){
				if($this->config->get('module_purpletree_multivendor_multiple_subscription_plan_active')){
					$obj_plan_name=$this->db->query("SELECT pvpd.plan_name,pvp.plan_id FROM ". DB_PREFIX . "purpletree_vendor_plan_invoice pvpi LEFT JOIN ". DB_PREFIX . "purpletree_vendor_plan_description pvpd ON (pvpi.plan_id=pvpd.plan_id) LEFT JOIN ". DB_PREFIX . "purpletree_vendor_plan pvp ON (pvp.plan_id=pvpd.plan_id) WHERE pvpi.status_id=2 AND pvpi.invoice_id IN (SELECT invoice_id FROM ". DB_PREFIX . "purpletree_vendor_seller_plan pvsp WHERE pvsp.new_status=1 AND pvpd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND pvsp.seller_id='".(int)$seller_id."' ) AND pvp.status=1");
					} else {
					$obj_plan_name=$this->db->query("SELECT pvpd.plan_name,pvp.plan_id FROM ". DB_PREFIX . "purpletree_vendor_plan_invoice pvpi LEFT JOIN ". DB_PREFIX . "purpletree_vendor_plan_description pvpd ON (pvpi.plan_id=pvpd.plan_id) LEFT JOIN ". DB_PREFIX . "purpletree_vendor_plan pvp ON (pvp.plan_id=pvpd.plan_id) WHERE pvpi.status_id=2 AND pvpi.invoice_id IN (SELECT invoice_id FROM ". DB_PREFIX . "purpletree_vendor_seller_plan pvsp WHERE pvsp.status=1 AND pvpd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND pvsp.seller_id='".(int)$seller_id."' AND pvp.status=1 ) ");	
				}
				if($obj_plan_name->num_rows){
					return $obj_plan_name->rows;
					} else {
					return NULL;
				}
				
				} else {
				return NULL;	
			}
			
		}
		
			public function sellerTotalPlanStatus($seller_id){
			$sql="SELECT pvps.status_id FROM ". DB_PREFIX ."purpletree_vendor_plan pvp  LEFT JOIN ". DB_PREFIX ."purpletree_vendor_plan_description pvpd ON (pvp.plan_id=pvpd.plan_id) LEFT JOIN ". DB_PREFIX ."purpletree_vendor_seller_plan pvsp ON (pvp.plan_id=pvsp.plan_id) LEFT JOIN ". DB_PREFIX ."purpletree_vendor_plan_subscription pvps ON ((pvps.seller_id = pvsp.seller_id) AND (pvps.status_id = pvp.status)) WHERE pvpd.language_id='".(int)$this->config->get('config_language_id') ."' AND pvsp.seller_id='".(int)$seller_id."' AND pvsp.status=1";
			$query = $this->db->query($sql);
			if($query->num_rows){	
				return $query->row;
				} else {
				return NULL;	
			}
		}
		
		
		public function getTable($table_id, $seller_id=NULL) {
			
			$sql = "SELECT DISTINCT * FROM " . DB_PREFIX . "table_manger t LEFT JOIN " . DB_PREFIX . "table_description td ON (t.id = td.table_id) WHERE t.id = '" . (int)$table_id . "' AND td.language = '" . (int)$this->config->get('config_language_id') . "'";
			
			if($seller_id){
				$sql .= " AND t.seller_id='".(int)$seller_id."'";
			}
			
			$query = $this->db->query($sql);
			
			return $query->row;
		}
		
    public function disabledtable($table_id){
			
			$this->db->query("UPDATE " . DB_PREFIX . "table_manger SET status = 0 WHERE id='".(int)$table_id."'");
			return $query->row;
		}
		
    public function enabledtable($table_id){
		
			$this->db->query("UPDATE " . DB_PREFIX . "table_manger SET status = 1 WHERE id='".(int)$table_id."'");
			return $query->row;
			
		}
	
		
		public function getTableDescriptions($table_id) {
			$table_description_data = array();
			
			$seller_id = $this->customer->getId();
			
			$query = $this->db->query("SELECT td.*,t.* FROM " . DB_PREFIX . "table_description td LEFT JOIN " . DB_PREFIX . "table_manger t ON (t.id = td.table_id) WHERE td.table_id = '" . (int)$table_id . "' AND t.seller_id = '".(int)$seller_id."'");
			
			foreach ($query->rows as $result) {
				$table_description_data[$result['language']] = array(
				'name'             => $result['name'],
				'description'      => $result['description'],
				
				);
			}
			
			return $table_description_data;
		}
		
		
		
		public function sellerActiveTable($seller_id,$table_id = NULL){
			$plan_id = $this->db->query("SELECT plan_id FROM  ". DB_PREFIX . "purpletree_vendor_seller_plan WHERE seller_id = '".(int)$seller_id."'")->row;
			$no_of_tables = 0;
			$query11 = $this->db->query("SELECT no_of_tables FROM ". DB_PREFIX . "purpletree_vendor_plan pvp WHERE plan_id='".$plan_id['plan_id']."'");
			if($query11->num_rows){	
				$no_of_tables = $query11->row['no_of_tables'];
			}
			$sql = "SELECT COUNT(*) AS table_count FROM ". DB_PREFIX . "table_manger t WHERE t.seller_id='".(int)$seller_id."'";
			if($table_id) {
				$sql .= " AND t.id !=".(int)$table_id;
			}
			$seller_assign_table= 0;
			$query11= $this->db->query($sql);
			if($query11->num_rows){	
				$seller_assign_table = $query11->row['table_count'];
			}
			if($seller_assign_table<$no_of_tables){
				return true;
				} else {
				return false;	
			}
			
		}
		
		public function editTable($table_id, $data) {
			
	       
			$seller_id = $this->customer->getId();
			
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "table_manger WHERE id='" . (int)$table_id . "' AND seller_id='" . (int)$seller_id."'");
			if($query->num_rows != 1){
				return false;
			}

			
			$this->db->query("DELETE FROM " . DB_PREFIX . "table_description WHERE table_id = '" . (int)$table_id . "'");
			
			foreach ($data['product_description'] as $language_id => $value) {
				
				
				$this->db->query("INSERT INTO " . DB_PREFIX . "table_description SET table_id = '" . (int)$table_id . "', language = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "'");
			}
		
			
		}
		
			public function deleteTable($table_id) {
			///////----RESTRICT PRODUCTS BY CUSTOMER GROUP----//////
			
			///////----END RESTRICT PRODUCTS BY CUSTOMER GROUP----//////
			$this->db->query("DELETE FROM " . DB_PREFIX . "table_manger WHERE id = '" . (int)$table_id . "'");
		
			$this->db->query("DELETE FROM " . DB_PREFIX . "table_description WHERE table_id = '" . (int)$table_id . "'");
		
		}
		
}
?>