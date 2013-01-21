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
    /* qa_wiki() funkcija predstavlja stranicu gdje će biti wiki i question/answer stranice 
     * $key parametar nam označava qa ili wiki, tj. da li je stranica question/answer ili wikipedia
     * $ask parametar označava, kada smo u question/answer sekciji i ako je u pitanju ask sekcija, treba da nam se otvori
     * sekcija gdje ćemo postavljati pitanje.
     */
    public function qa_wiki($key, $ask = null)
    {
        $data['sessionData'] = $this->data;
        $data['key'] = $key;
        $data['ask'] = $ask;
        if($key == 'qa')
            $this->load->view('qa', $data);
        if($key == 'wiki')
            $this->load->view('wiki', $data);
        if(isset($ask))
        {
            if($ask == 'ask')
            {
                if($this->data == false)
                {
                    redirect('main/login');
                }
            }
        }
    }
}