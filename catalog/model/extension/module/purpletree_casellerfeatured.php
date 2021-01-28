<?php
class ModelExtensionModulePurpletreeCasellerfeatured extends Model {
		public function getFeatured($category_id) {
			$query = $this->db->query("SELECT pvp.product_id FROM " . DB_PREFIX . "purpletree_vendor_products pvp LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id = pvp.product_id) LEFT JOIN " . DB_PREFIX . "product_to_category pc ON (p.product_id = pc.product_id) WHERE pvp.is_category_featured ='1' AND p.status=1 AND pc.category_id = '" . (int)$category_id . "' AND pvp.is_approved = 1 AND p.date_available <= NOW() AND p.quantity >= 1 ORDER BY RAND() DESC" );;
			if($query->num_rows) {
				$products = $query->rows;
				return $products;
				}else{
				return null;
			}
		}
}