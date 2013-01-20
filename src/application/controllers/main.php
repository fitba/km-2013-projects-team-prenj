<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller 
{
    var $data;
    public function __construct()
    {
        parent::__construct();
        $this->load->model('login_m');
        $this->data = $this->login_m->isLoggedIn();
    }
    
    public function index()
    {
        $data['sessionData'] = $this->data;
        $this->load->view('main', $data);
    }

    public function register()
    {
        if($this->data)
        {
            redirect('main/index');
        }
        $data['sessionData'] = $this->data;
        $this->load->view('register', $data);
    }
    
    public function login()
    {
        if($this->data)
        {
            redirect('main/index');
        }
        $data['sessionData'] = $this->data;
        $this->load->view('login', $data);
    }
}