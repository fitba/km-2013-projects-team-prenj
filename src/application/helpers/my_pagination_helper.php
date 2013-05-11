<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
if (!function_exists ('generate_pagination')) {
	function generate_pagination ($path, $total, $url_segment, $per_page) {
		$CI =& get_instance();
		$CI->load->library ('pagination');
		
		$config['base_url'] = site_url($path);
		$config['total_rows'] = $total;
		$config['per_page'] = $per_page;
		$config['uri_segment'] = $url_segment;

                $config['full_tag_open'] = "<div class='pagination pull-left no-margin-top'><ul>";
                $config['full_tag_close'] = "</ul></div>";

                $config['num_tag_open'] = "<li>";
                $config['num_tag_close'] = "</li>";

                $config['next_link'] = "&rsaquo;&rsaquo;";
                $config['next_tag_open'] = "<li>";
                $config['next_tag_close'] = "</li>";

                $config['prev_link'] = "&lsaquo;&lsaquo;";
                $config['prev_tag_open'] = "<li>";
                $config['prev_tag_close'] = "</li>";

                $config['last_link'] = 'Posljednja stranica &rsaquo;';
                $config['last_tag_open'] = '<li>';
                $config['last_tag_close'] = '</li>';

                $config['first_link'] = '&lsaquo; Prva stranica';
                $config['first_tag_open'] = '<li>';
                $config['first_tag_close'] = '</li>';

                $config['cur_tag_open'] = "<li class=\"active\"><a href=\"#\">";
                $config['cur_tag_close'] = "</a></li>";
			
		$CI->pagination->initialize($config);
		$paginacija = $CI->pagination->create_links();
		return $paginacija != "" ? $paginacija : "";
	}	
}
?>