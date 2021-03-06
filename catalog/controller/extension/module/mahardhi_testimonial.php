<?php
class ControllerExtensionModuleMahardhiTestimonial extends Controller {
    public function index($setting) {
        $this->language->load('extension/module/mahardhi_testimonial');
        $this->load->model('catalog/mahardhi_testimonial');
        $this->load->model('tool/image');

        $data = array();

        $data['heading_title'] = $this->language->get('heading_title');

        $lang_code = $this->session->data['language'];

        if(isset($setting['title']) && $setting['title']) {
            $data['title'] = $setting['title'][$lang_code]['title'];
        } else {
            $data['title'] = $this->language->get('heading_title');
        }

       $testimonial_total = $this->model_catalog_mahardhi_testimonial->getTotalTestimonials();

        if(isset($setting['limit'])) {
            $limit = (int) $setting['limit'];
        } else {
            $limit = 10;
        }

        if($limit > (int) $testimonial_total) {
            $limit = (int) $testimonial_total;
        }

        if (isset($setting['rows'])) {
            $rows = $setting['rows'];
        } else {
            $rows = 1;
        }

        if (isset($setting['items'])) {
            $items = $setting['items'];
        } else {
            $items = 4;
        }

        if (isset($setting['speed'])) {
            $speed = $setting['speed'];
        } else {
            $speed = 3000;
        }

        if (isset($setting['auto']) && $setting['auto']) {
            $auto = true;
        } else {
            $auto = false;
        }

        if (isset($setting['navigation']) && $setting['navigation']) {
            $navigation = true;
        } else {
            $navigation = false;
        }

        if (isset($setting['pagination']) && $setting['pagination']) {
            $pagination = true;
        } else {
            $pagination = false;
        }

        $data['limit'] = $limit;
        $data['testimonials'] = array();

        $results = $this->model_catalog_mahardhi_testimonial->getTestimonials(0, $limit);
        
        foreach($results as $result){

            if($result['image']) {
                $image = $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']);
            }else{
                $image = $this->model_tool_image->resize('no_image.png', $setting['width'], $setting['height']);            
            }

            $data['testimonials'][] = array(
                'customer_name'    	=> $result['customer_name'],
                'image'             => $image,
                'content'   		=> html_entity_decode($result['content'], ENT_QUOTES, 'UTF-8') . "\n",					
            );
        }
        

        $data['slide'] = array(
            'auto' => $auto,
            'rows' => $rows,
            'navigation' => $navigation,
            'pagination' => $pagination,
            'speed' => $speed,
            'items' => $items
        );

		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_readmore'] = $this->language->get('text_readmore');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_theme') . '/template/extension/module/mahardhi_testimonial.twig')) {
            return $this->load->view('extension/module/mahardhi_testimonial', $data);
        } else {
            return;
        }
        
    }

}