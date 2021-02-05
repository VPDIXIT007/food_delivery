<?php
class ControllerCatalogProduct extends Controller {
public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_model'])) {
			$this->load->model('catalog/product');
			$this->load->model('catalog/option');

			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			if (isset($this->request->get['filter_model'])) {
				$filter_model = $this->request->get['filter_model'];
			} else {
				$filter_model = '';
			}

			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 5;
			}
				//$store_detail = $this->customer->isSeller();
					//$seller_id = $this->customer->getId();
						$store_detail = $this->customer->isSeller();
							$seller_id = $store_detail['id'];
			$filter_data = array(
				'filter_name'  => $filter_name,
				'filter_model' => $filter_model,
				'start'        => 0,
				'limit'        => $limit,
				//'seller_id'			  => $seller_id
			);
		
			//$results = $this->model_catalog_product->getTotalSellerProducts($filter_data);
			$results = $this->model_catalog_product->getProducts($filter_data,$seller_id);
          //  trigger_error(print_r($results,true));
			foreach ($results as $result) {
				$option_data = array();

				$product_options = $this->model_catalog_product->getProductOptions($result['product_id']);
   
				foreach ($product_options as $product_option) {
					$option_info = $this->model_catalog_option->getOption($product_option['option_id']);

					if ($option_info) {
						$product_option_value_data = array();

						foreach ($product_option['product_option_value'] as $product_option_value) {
							$option_value_info = $this->model_catalog_option->getOptionValue($product_option_value['option_value_id']);

							if ($option_value_info) {
								$product_option_value_data[] = array(
									'product_option_value_id' => $product_option_value['product_option_value_id'],
									'option_value_id'         => $product_option_value['option_value_id'],
									'name'                    => $option_value_info['name'],
									'price'                   => (float)$product_option_value['price'] ? $this->currency->format($product_option_value['price'], $this->config->get('config_currency')) : false,
									'price_prefix'            => $product_option_value['price_prefix']
								);
							}
						}

						$option_data[] = array(
							'product_option_id'    => $product_option['product_option_id'],
							'product_option_value' => $product_option_value_data,
							'option_id'            => $product_option['option_id'],
							'name'                 => $option_info['name'],
							'type'                 => $option_info['type'],
							'value'                => $product_option['value'],
							'required'             => $product_option['required']
						);
					}
				}
 
				$json[] = array(
					'product_id' => $result['product_id'],
					'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'model'      => $result['model'],
					'option'     => $option_data,
					'price'      => $result['price']
				);
			}
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	public function addproduct() {
		$order_id = $this->request->get['order_id'];
		if (isset($this->request->post['product'])) {
				$this->cart->clear();

				foreach ($this->request->post['product'] as $product) {
					if (isset($product['option'])) {
						$option = $product['option'];
					} else {
						$option = array();
					}

					$this->cart->add($product['product_id'], $product['quantity'], $option);
				}

				$json['success'] = $this->language->get('text_success');

				unset($this->session->data['shipping_method']);
				unset($this->session->data['shipping_methods']);
				unset($this->session->data['payment_method']);
				unset($this->session->data['payment_methods']);
			} elseif (isset($this->request->post['product_id'])) {
				$this->load->model('catalog/product');

				$product_info = $this->model_catalog_product->getProduct($this->request->post['product_id']);

				if ($product_info) {
					if (isset($this->request->post['quantity'])) {
						$quantity = $this->request->post['quantity'];
					} else {
						$quantity = 1;
					}

					if (isset($this->request->post['option'])) {
						$option = array_filter($this->request->post['option']);
					} else {
						$option = array();
					}

					$product_options = $this->model_catalog_product->getProductOptions($this->request->post['product_id']);

					foreach ($product_options as $product_option) {
						if ($product_option['required'] && empty($option[$product_option['product_option_id']])) {
							$json['error']['option'][$product_option['product_option_id']] = sprintf($this->language->get('error_required'), $product_option['name']);
						}
					}

					if (!isset($json['error']['option'])) {
						$this->cart->add($this->request->post['product_id'], $quantity, $option);

						$json['success'] = $this->language->get('text_success');

						unset($this->session->data['shipping_method']);
						unset($this->session->data['shipping_methods']);
						unset($this->session->data['payment_method']);
						unset($this->session->data['payment_methods']);
					}
				} else {
					$json['error']['store'] = $this->language->get('error_store');
				}
			}
			// Products
				$order_data['products'] = array();

				foreach ($this->cart->getProducts() as $product) {
					$option_data = array();

					foreach ($product['option'] as $option) {
						$option_data[] = array(
							'product_option_id'       => $option['product_option_id'],
							'product_option_value_id' => $option['product_option_value_id'],
							'option_id'               => $option['option_id'],
							'option_value_id'         => $option['option_value_id'],
							'name'                    => $option['name'],
							'value'                   => $option['value'],
							'type'                    => $option['type']
						);
					}

					$order_data['products'][] = array(
						'product_id' => $product['product_id'],
						'name'       => $product['name'],
						'model'      => $product['model'],
						'option'     => $option_data,
						'download'   => $product['download'],
						'quantity'   => $product['quantity'],
						'subtract'   => $product['subtract'],
						'price'      => $product['price'],
						'total'      => $product['total'],
						'tax'        => $this->tax->getTax($product['price'], $product['tax_class_id']),
						'reward'     => $product['reward']
					);
				}
				// Products
				$data['products'] = $order_data['products'];
		if (isset($data['products'])) {

			$store_shipping_type = array();
			
			foreach ($data['products'] as $product) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "order_product SET order_id = '" . (int)$order_id . "', product_id = '" . (int)$product['product_id'] . "', name = '" . $this->db->escape($product['name']) . "', model = '" . $this->db->escape($product['model']) . "', quantity = '" . (int)$product['quantity'] . "', price = '" . (float)$product['price'] . "', total = '" . (float)$product['total'] . "', tax = '" . (float)$product['tax'] . "', reward = '" . (int)$product['reward'] . "'");

				$order_product_id = $this->db->getLastId();

		 /*** insert into seller orders ****/
			if ($this->config->get('module_purpletree_multivendor_status')) {	
					
					$seller_id = $this->db->query("SELECT pvp.seller_id, pvs.store_shipping_charge,pvs.store_shipping_order_type,pvs.store_shipping_type,pvs.store_commission, p.tax_class_id FROM " . DB_PREFIX . "purpletree_vendor_products pvp JOIN " . DB_PREFIX . "purpletree_vendor_stores pvs ON(pvs.seller_id=pvp.seller_id) JOIN " . DB_PREFIX . "product p ON(p.product_id=pvp.product_id) WHERE pvp.product_id='".(int)$product['product_id']."' AND pvp.is_approved=1")->row;
					if($this->config->get('module_purpletree_multivendor_seller_product_template')){
					if(empty($seller_id['seller_id'])) {
						$sseller_id = $product['seller_id'];
						$seller_id = $this->db->query("SELECT pvs.seller_id, pvs.store_shipping_charge,pvs.store_shipping_order_type,pvs.store_shipping_type,pvs.store_commission, p.tax_class_id FROM " . DB_PREFIX . "purpletree_vendor_template_products pvtp JOIN " . DB_PREFIX . "purpletree_vendor_stores pvs ON(pvs.seller_id=pvtp.seller_id) JOIN " . DB_PREFIX . "purpletree_vendor_template pvt ON(pvt.id=pvtp.template_id) JOIN " . DB_PREFIX . "product p ON(p.product_id=pvt.product_id) WHERE pvt.product_id='".(int)$product['product_id']."' AND pvs.seller_id='".$sseller_id."'")->row;
					}
					}
					if(!empty($seller_id['seller_id'])) {
						//if(isset($this->session->data['table_id'])) {$tableid = $this->session->data['table_id'];} else {$tableid = '';}
						//if(isset($this->session->data['ordertype'])) {$ordertype = $this->session->data['ordertype'];} else {$ordertype = '';}
						$this->db->query("INSERT INTO " . DB_PREFIX . "purpletree_vendor_orders SET order_id ='".(int)$order_id."', seller_id = '".(int)$seller_id['seller_id']."', product_id ='".(int)$product['product_id']."', shipping = '".(float)$seller_id['store_shipping_charge']."', quantity = '" . (int)$product['quantity'] . "', unit_price = '" . (float)$product['price'] . "', total_price = '" . (float)$product['total'] . "',table_id='". (int)$tableid ."',ordertype='". $ordertype ."', created_at =NOW(), updated_at = NOW()");					
						$vendor_or_teb_id = $this->db->getLastId();
						$this->db->query("INSERT INTO " . DB_PREFIX . "purpletree_vendor_commissions SET order_id = '" . (int)$order_id . "', product_id ='".(int)$product['product_id']."', seller_id = '" . (int)$seller_id['seller_id'] . "',vendor_order_table_id = '" . (int)$vendor_or_teb_id . "',commission_shipping = '0', commission_fixed = '0', commission_percent = '0', commission = '0', status = 'Pending', created_at = NOW(), updated_at = NOW()");
						
						if(!isset($seller_sub_total[$seller_id['seller_id']])){
						$seller_sub_total[$seller_id['seller_id']] = $product['total'];
						} else {
							$seller_sub_total[$seller_id['seller_id']] += $product['total'];
						}
						
						if(!isset($seller_final_total[$seller_id['seller_id']])){
							$seller_final_total[$seller_id['seller_id']] = $this->tax->calculate($product['price'], $seller_id['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'];
						} else {
							$seller_final_total[$seller_id['seller_id']] += $this->tax->calculate($product['price'], $seller_id['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'];
						}
						
						$tax_rates = $this->tax->getRates($product['price'], $seller_id['tax_class_id']);
			
						foreach ($tax_rates as $tax_rate) {
							if (!isset($seller_tax_data[$seller_id['seller_id']][$tax_rate['tax_rate_id']])) {
								$seller_tax_data[$seller_id['seller_id']][$tax_rate['tax_rate_id']] = ($tax_rate['amount'] * $product['quantity']);
							} else {
								$seller_tax_data[$seller_id['seller_id']][$tax_rate['tax_rate_id']] += ($tax_rate['amount'] * $product['quantity']);
							}
						}
			
					} 
				}
			

				foreach ($product['option'] as $option) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "order_option SET order_id = '" . (int)$order_id . "', order_product_id = '" . (int)$order_product_id . "', product_option_id = '" . (int)$option['product_option_id'] . "', product_option_value_id = '" . (int)$option['product_option_value_id'] . "', name = '" . $this->db->escape($option['name']) . "', `value` = '" . $this->db->escape($option['value']) . "', `type` = '" . $this->db->escape($option['type']) . "'");
				}
			}
		}
$this->cart->clear();
			$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	Public function removeproduct() {
		
			$order_id = $this->request->get['order_id'];
			$product_id = $this->request->post['key'];
		$this->db->query("DELETE FROM " . DB_PREFIX . "order_product WHERE  order_id = '" . (int)$order_id . "' AND product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "purpletree_vendor_orders WHERE  order_id = '" . (int)$order_id . "' AND product_id = '" . (int)$product_id . "'");
		
		// UPDATE TOTALS 
				//GET ALL PRODUCTS
				$total = 0;
				$subtotal = 0;
				$totals = array();
				$products = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '".$order_id."'")->rows;
				foreach ($products as $product) {
					$product_total = $product['price'] * $product['quantity'] + $product['tax'];
					
					$totals[] = $product_total;
				}
				$subtotal = array_sum($totals);
				// GET SHIPPING 
				$shipping =  $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '".$order_id."' AND code = 'shipping'")->row;
				if($shipping) {
					$total = $subtotal + $shipping['value'];
				} else {$total = $subtotal;}
				// update subtotal
				$this->db->query("UPDATE " . DB_PREFIX . "order_total SET value = '".$subtotal."' WHERE order_id = '".$order_id."' AND code = 'sub_total'");
				$this->db->query("UPDATE " . DB_PREFIX . "purpletree_order_total SET value = '".$subtotal."' WHERE order_id = '".$order_id."' AND code = 'sub_total'");
				// update total
				$this->db->query("UPDATE " . DB_PREFIX . "purpletree_order_total SET value = '".$total."' WHERE order_id = '".$order_id."' AND code = 'total'");
				$this->db->query("UPDATE " . DB_PREFIX . "order_total SET value = '".$total."' WHERE order_id = '".$order_id."' AND code = 'total'");
				$this->db->query("UPDATE " . DB_PREFIX . "order SET total = '".$total."' WHERE order_id = '".$order_id."'");
				
		$json['success'] = $this->language->get('text_success');		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
		Public function refreshquantity() {
				$this->load->model('catalog/product');
				trigger_error(print_r($this->request->post,true));
					$order_id = $this->request->get['order_id'];
			foreach ($this->request->post['product'] as $product) {
		
			$product_id = $product['product_id'];
			$quantity = $product['quantity'];;
	
			$product_info = $this->model_catalog_product->getProduct($product['product_id']);
			$purpletree_info = $this->db->query("SELECT * FROM " . DB_PREFIX . "purpletree_vendor_orders WHERE order_id = '" . (int)$order_id . "'")->row;
				$total = $product_info['price'] * $quantity;
				$total_price = $total;
			$this->db->query("UPDATE " . DB_PREFIX . "purpletree_vendor_orders SET quantity = '" . (int)$quantity . "', unit_price = '" . (float)$product_info['price'] . "', total_price = '" . (float)$total_price . "', order_status_id = '" . (float)$purpletree_info['order_status_id'] . "', table_id = '" . (int)$purpletree_info['table_id'] . "', created_at = '" . $purpletree_info['created_at'] . "', updated_at = '" . $purpletree_info['updated_at'] . "' , ordertype = '" . $purpletree_info['ordertype'] . "',seen = '" . $purpletree_info['seen'] . "' WHERE order_id = '" . (int)$order_id . "' AND product_id = '" . (int)$product_info['product_id'] . "'  ");
			
				$this->db->query("UPDATE " . DB_PREFIX . "order_product SET quantity = '" . (int)$quantity . "', price = '" . (float)$product_info['price'] . "', total = '" . (float)$total . "', tax = '" . (float)$product_info['tax'] . "', reward = '" . (int)$product_info['reward']*$quantity . "' WHERE order_id = '" . (int)$order_id . "' AND product_id = '" . (int)$product_info['product_id'] . "'");
			}
			
			// UPDATE TOTALS 
				//GET ALL PRODUCTS
				$total = 0;
				$subtotal = 0;
				$totals = array();
				$products = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '".$order_id."'")->rows;
				foreach ($products as $product) {
					$product_total = $product['price'] * $product['quantity'] + $product['tax'];
					
					$totals[] = $product_total;
				}
				$subtotal = array_sum($totals);
				// GET SHIPPING 
				$shipping =  $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '".$order_id."' AND code = 'shipping'")->row;
				if($shipping) {
					$total = $subtotal + $shipping['value'];
				} else {$total = $subtotal;}
				// update subtotal
				$this->db->query("UPDATE " . DB_PREFIX . "order_total SET value = '".$subtotal."' WHERE order_id = '".$order_id."' AND code = 'sub_total'");
				$this->db->query("UPDATE " . DB_PREFIX . "purpletree_order_total SET value = '".$subtotal."' WHERE order_id = '".$order_id."' AND code = 'sub_total'");
				// update total
				$this->db->query("UPDATE " . DB_PREFIX . "purpletree_order_total SET value = '".$total."' WHERE order_id = '".$order_id."' AND code = 'total'");
				$this->db->query("UPDATE " . DB_PREFIX . "order_total SET value = '".$total."' WHERE order_id = '".$order_id."' AND code = 'total'");
				$this->db->query("UPDATE " . DB_PREFIX . "order SET total = '".$total."' WHERE order_id = '".$order_id."'");
				
				$json['success'] = $this->language->get('text_success');		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
		}
	}