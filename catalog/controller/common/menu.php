<?php
class ControllerCommonMenu extends Controller {
	public function index() {
		$this->load->language('common/menu');

		// Blog Enable
		$results = $this->model_setting_extension->getExtensions('module');
		foreach($results as $result){
			if ($result['code'] === 'blog') {
				$data['enableBlog'] = true;
			} 
		}
		$data['blogs_link'] = $this->url->link('blog/blog');
		
		
		
		//seller menu
		
		
		if(isset($this->request->get['seller_store_id'])) {
			$this->load->model('extension/purpletree_multivendor/vendor');
			// Menu
			$store_info = $this->model_extension_purpletree_multivendor_vendor->getStore($this->request->get['seller_store_id']);
			$seller_id = $store_info['seller_id']; 
		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		$data['categories'] = array();

		$categories = $this->model_catalog_category->getCategories(0);

		foreach ($categories as $category) {
			
			$filter_data_1 = array(
							'filter_category_id'  => $category['category_id'],
							'filter_sub_category' => true,
							'seller_id'			  => $seller_id
						);
							$sellerproduct = $this->model_catalog_product->getTotalSellerProducts($filter_data_1);
								if ($sellerproduct > 0 ) {
			if ($category) {
				// Level 2
				// print_r($category);
				$children_data = array();

				$children = $this->model_catalog_category->getCategories($category['category_id']);
				 
				foreach ($children as $child) {
					//level 3
					$children_data_3 = array();

					$children_3 = $this->model_catalog_category->getCategories($child['category_id']);
					foreach ($children_3 as $child_3) {
						$filter_data_3 = array(
							'filter_category_id'  => $child_3['category_id'],
							'filter_sub_category' => true,
							'seller_id'			  => $seller_id
						);
						$sellerproduct = $this->model_catalog_product->getTotalSellerProducts($filter_data_3);
						
						if ($sellerproduct > 0 ) {
						$children_data_3[] = array(
							'name'  => $child_3['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data_3) . ')' : ''),
							'href'         => $this->url->link('extension/account/purpletree_multivendor/sellerstore/storeview', 'seller_store_id='. $this->request->get['seller_store_id'] .'&p_url='. $child['category_id']),
						);
						}
					}
					// end lavel 3
					$filter_data = array(
						'filter_category_id'  => $child['category_id'],
						'filter_sub_category' => true,
						'seller_id'			  => $seller_id
					);
					$sellerproduct = $this->model_catalog_product->getTotalSellerProducts($filter_data);
						
						if ($sellerproduct > 0 ) {
					$children_data[] = array(
						'name'         => $child['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
						'href'         => $this->url->link('extension/account/purpletree_multivendor/sellerstore/storeview', 'seller_store_id='. $this->request->get['seller_store_id'] .'&p_url=' . $category['category_id'] . '_' . $child['category_id']),
						'grand_childs' => $children_data_3
					);
						}
				}

				// Category Image
				$thumb='';
				if (isset($category['menuimage'])) {
					$children = $this->model_catalog_category->getCategories($category['category_id']);
					foreach ($children as $child) {
						$filter_data = array(
							'filter_category_id'  => $child['category_id']
						);
						$this->load->model('tool/image');
						$image = empty($category['image']) ? 'no_image.jpg' : $category['image'];
						$thumb = $this->model_tool_image->resize($image, 848, 160, $filter_data);
					}
				}

				$data['categories'][] = array(
					'name'     => $category['name'],
					'children' => $children_data,
					'column'   => $category['column'] ? $category['column'] : 1,
					'thumb'    => $thumb,
					'href'         => $this->url->link('extension/account/purpletree_multivendor/sellerstore/storeview', 'seller_store_id='. $this->request->get['seller_store_id'] .'&p_url=' . $category['category_id'])
						
				);
				

		}}
		}
		} else {
		//end seller menu	
	
		// Menu
		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		$data['categories'] = array();

		$categories = $this->model_catalog_category->getCategories(0);

		foreach ($categories as $category) {
			if ($category) {
				// Level 2
				$children_data = array();

				$children = $this->model_catalog_category->getCategories($category['category_id']);

				foreach ($children as $child) {
					//level 3
					$children_data_3 = array();

					$children_3 = $this->model_catalog_category->getCategories($child['category_id']);
					foreach ($children_3 as $child_3) {
						$filter_data_3 = array(
							'filter_category_id'  => $child_3['category_id'],
							'filter_sub_category' => true
						);
						$children_data_3[] = array(
							'name'  => $child_3['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data_3) . ')' : ''),
							'href'  => $this->url->link('product/category', 'path=' . $child['category_id'] . '_' . $child_3['category_id'])
						);
					}
					// end lavel 3
					$filter_data = array(
						'filter_category_id'  => $child['category_id'],
						'filter_sub_category' => true
					);
					$children_data[] = array(
						'name'         => $child['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
						'href'         => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id']),
						'grand_childs' => $children_data_3
					);
				}

				// Category Image
				$thumb='';
				if (isset($category['menuimage'])) {
					$children = $this->model_catalog_category->getCategories($category['category_id']);
					foreach ($children as $child) {
						$filter_data = array(
							'filter_category_id'  => $child['category_id']
						);
						$this->load->model('tool/image');
						$image = empty($category['image']) ? 'no_image.jpg' : $category['image'];
						$thumb = $this->model_tool_image->resize($image, 848, 160, $filter_data);
					}
				}

				$data['categories'][] = array(
					'name'     => $category['name'],
					'children' => $children_data,
					'column'   => $category['column'] ? $category['column'] : 1,
					'thumb'    => $thumb,
					'href'     => $this->url->link('product/category', 'path=' . $category['category_id'])
				);
			}
		}
		}
		return $this->load->view('common/menu', $data);
	}
}
