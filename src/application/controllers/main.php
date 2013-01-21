<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller 
{
    var $data; // globalna varijabla - definisana je na nivou klase.
    
    /* Konstruktor klase Main. On u sebi nasleđuje konstruktor iz klase CI_Controller, 
     * poziva model login_m i globalnoj varijabli $data prosleđuje funkciju isLoggedIn()
     * koja je definisana u modelu login_m */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('login_m');
        $this->data = $this->login_m->isLoggedIn();
    }
    
    /* index() funkcija predstavlja index stranicu na web prikazu */
    public function index()
    {
        $data['sessionData'] = $this->data;
        $this->load->view('main', $data);
    }

    /* register() funkcija predstavlja register stranicu na web prikazu */
    public function register()
    {
        if($this->data)
        {
            redirect('main/index');
        }
        $data['sessionData'] = $this->data;
        $this->load->view('register', $data);
    }
    
    /* login() funkcija predstavlja login stranicu na web prikazu */
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