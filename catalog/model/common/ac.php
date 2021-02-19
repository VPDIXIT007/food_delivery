<?php
class ModelCommonAc extends Model 
{
	public function validateSeller($api_token) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "purpletree_vendor_stores WHERE api_token = '" . $this->db->escape($api_token) . "'");

		return $query->row;
	}

  public function getTotalSellerOrders($data= array()){
    $sql = "SELECT COUNT(DISTINCT(pvo.order_id)) AS total, pvo.ordertype  FROM `" . DB_PREFIX . "order` o JOIN " . DB_PREFIX . "purpletree_vendor_orders pvo ON(pvo.order_id=o.order_id) LEFT JOIN oc_table_manger tm on tm.id=pvo.table_id";
    
    if (isset($data['filter_order_status'])) {
      $implode = array();
      
      $order_statuses = explode(',', $data['filter_order_status']);
      
      foreach ($order_statuses as $order_status_id) {
        $implode[] = "pvo.order_status_id = '" . (int)$order_status_id . "'";
      }
      
      if ($implode) {
        $sql .= " WHERE (" . implode(" OR ", $implode) . ")";
      }
      } else {
      $sql .= " WHERE pvo.order_status_id > '0'";
    }
    if (isset($data['filter_admin_order_status'])) {
      $implode1 = array();
      
      $order_statuses1 = explode(',', $data['filter_admin_order_status']);
      
      foreach ($order_statuses1 as $order_status_id) {
        $implode1[] = "o.order_status_id = '" . (int)$order_status_id . "'";
      }
      
      if ($implode1) {
        $sql .= " AND (" . implode(" OR ", $implode1) . ")";
      }
      } else {
      $sql .= " AND o.order_status_id > '0'";
    }
    
    if(!empty($data['seller_id'])){
      $sql .= " AND pvo.seller_id ='".(int)$data['seller_id']."'";
    }
    
    if($data['filter_order_no']){
      $sql .= " AND o.order_no LIKE '".$data['filter_order_no']."'";
    }

    if(!empty($data['filter_order_id'])){
      $sql .= " AND o.order_id = '".(int)$data['filter_order_id']."'";
    }
    
    if (!empty($data['filter_date_from'])) {
      $sql .= " AND DATE(o.date_added) >= DATE('" . $this->db->escape($data['filter_date_from']) . "')";
    }
    
    if (!empty($data['filter_date_to'])) {
      $sql .= " AND DATE(o.date_added) <= DATE('" . $this->db->escape($data['filter_date_to']) . "')";
    }

    if (!empty($data['filter_time_from'])) {
      $sql .= " AND TIME(o.date_added) >= TIME('".$data['filter_time_from']."')";
    }
    
    if (!empty($data['filter_time_to'])) {
      $sql .= " AND TIME(o.date_added) <= TIME('".$data['filter_time_to']."')";
    }
    if (!empty($data['order_type'])) {
      $sql .= " AND pvo.ordertype = '".$data['order_type']."'";
    }

    if(!isset($data['filter_date_from']) && !isset($data['filter_date_to'])){
      $end_date = date('Y-m-d', strtotime("-30 days"));
      $sql .= " AND DATE(o.date_added) >= '".$end_date."'";
      $sql .= " AND DATE(o.date_added) <= '".date('Y-m-d')."'";
    }

    if (!empty($data['filter_table'])) {
      $sql .= " AND tm.table_no = '". (int)$data['filter_table'] ."' ";
    }
    $query = $this->db->query($sql);
    
    return $query->row['total'];
  }
  
  public function getSellerOrders($data = array()) {
    $order_cloumn = "o.order_id, o.firstname, o.lastname, o.email, o.telephone, o.payment_firstname, o.payment_lastname, o.payment_address_1, o.payment_address_2, o.payment_city,o.payment_postcode, o.payment_country,o.payment_zone,o.payment_method, o.shipping_firstname, o.shipping_lastname, o.shipping_company, o.shipping_address_1, o.shipping_address_2, o.shipping_city, o.shipping_postcode, o.shipping_country, o.shipping_zone, o.shipping_method ";
    $sql = "SELECT $order_cloumn,pvo.ordertype,pvo.order_status_id AS seller_order_status_idd,pvo.seen,(SELECT os.name FROM " . DB_PREFIX . "order_status os WHERE os.order_status_id = pvo.order_status_id AND os.language_id = '" . (int)$this->config->get('config_language_id') . "') AS order_status, o.shipping_code, o.total, o.currency_code, o.currency_value, o.date_added, o.date_modified, o.order_no FROM `" . DB_PREFIX . "order` o JOIN " . DB_PREFIX . "purpletree_vendor_orders pvo ON(pvo.order_id=o.order_id)";

    if (isset($data['filter_order_status'])) {
      $implode = array();
      
      $order_statuses = explode(',', $data['filter_order_status']);
      
      foreach ($order_statuses as $order_status_id) {
        $implode[] = "pvo.order_status_id = '" . (int)$order_status_id . "'";
      }
      
      if ($implode) {
        $sql .= " WHERE (" . implode(" OR ", $implode) . ")";
      }
      } else {
      $sql .= " WHERE pvo.order_status_id > '0'";
    }
    if (isset($data['filter_admin_order_status'])) {
      $implode1 = array();
      
      $order_statuses1 = explode(',', $data['filter_admin_order_status']);
      
      foreach ($order_statuses1 as $order_status_id) {
        $implode1[] = "o.order_status_id = '" . (int)$order_status_id . "'";
      }
      
      if ($implode1) {
        $sql .= " AND (" . implode(" OR ", $implode1) . ")";
      }
      } else {
      $sql .= " AND o.order_status_id > '0'";
    }
    
    if(!empty($data['seller_id'])){
      $sql .= " AND pvo.seller_id ='".(int)$data['seller_id']."'";
    }
    
    if($data['filter_order_no']){
      $sql .= " AND o.order_no LIKE '".$data['filter_order_no']."'";
    }

    if(!empty($data['filter_order_id'])){
      $sql .= " AND o.order_id = '".(int)$data['filter_order_id']."'";
    }

    if (!empty($data['filter_date_from'])) {
      $sql .= " AND DATE(pvo.created_at) >= DATE('" . $this->db->escape($data['filter_date_from']) . "')";
    }
    
    if (!empty($data['filter_date_to'])) {
      $sql .= " AND DATE(pvo.created_at) <= DATE('" . $this->db->escape($data['filter_date_to']) . "')";
    }

    if (!empty($data['filter_time_from'])) {
      $sql .= " AND TIME(o.date_added) >= TIME('".$data['filter_time_from']."')";
    }
    
    if (!empty($data['filter_time_to'])) {
      $sql .= " AND TIME(o.date_added) <= TIME('".$data['filter_time_to']."')";
    }
    
    if (!empty($data['order_type'])) {
      $sql .= " AND pvo.ordertype = '".$data['order_type']."'";
    }
    if(empty($data['filter_date_from']) && empty($data['filter_date_to'])){
      $end_date = date('Y-m-d', strtotime("-30 days"));
      $sql .= " AND DATE(pvo.created_at) >= '".$end_date."'";
      $sql .= " AND DATE(pvo.created_at) <= '".date('Y-m-d')."'";
    }
    
    $sort_data = array(
    'o.order_id',
    'customer',
    'order_status',
    'o.date_added',
    'o.date_modified',
    'o.total'
    );
    
    $sql .= " group by o.order_id";

    //HAVING CHECK
    if (!empty($data['filter_table'])) {
      $sql .= " HAVING table_num = '". (int)$data['filter_table'] ."' ";
    }
    
    if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
      $sql .= " ORDER BY " . $this->db->escape($data['sort']);
      } else {
      $sql .= " ORDER BY o.order_id";
    }
    
    if (isset($data['order']) && ($data['order'] == 'DESC')) {
      $sql .= " DESC";
      } else {
      $sql .= " DESC";
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

  public function getSellerOrderProducts($order_id,$seller_id){
    $query = $this->db->query("SELECT op.* ,pvo.product_comments,(SELECT CONCAT(c.firstname, ' ', c.lastname) FROM " . DB_PREFIX . "customer c WHERE c.customer_id = pvo.seller_id) as seller_name, pvo.seller_id, pvo.shipping, (SELECT upc FROM " . DB_PREFIX . "product p WHERE p.product_id = op.product_id) as upc FROM " . DB_PREFIX . "order_product op JOIN " . DB_PREFIX . "purpletree_vendor_orders pvo ON(pvo.order_id=op.order_id AND pvo.product_id=op.product_id) WHERE op.order_id = '" . (int)$order_id . "' AND pvo.seller_id = '".(int)$seller_id."' GROUP BY op.order_product_id");
    return $query->rows;
  }
}