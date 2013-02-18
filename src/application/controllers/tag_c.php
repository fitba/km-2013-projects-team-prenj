<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tag_c extends CI_Controller 
{
    
    public function __construct()
    {
        parent::__construct();
    }
    
    public function index($tag = null)
    {
        $tag = (string)$tag;
        $data['tag'] = $tag;
        $this->load->view('tag', $data);
    }
}
