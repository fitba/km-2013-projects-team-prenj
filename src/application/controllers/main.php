<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller
{
    var $data; // globalna varijabla - definisana je na nivou klase.

    /* Konstruktor klase Main. On u sebi nasleđuje konstruktor iz klase CI_Controller, 
     * poziva model login_m i globalnoj varijabli $data prosleđuje funkciju isLoggedIn()
     * koja je definisana u modelu login_m */
    var $sessionData;
    public function __construct()
    {
        parent::__construct();
        $this->load->library('redirectpage');
        $this->load->model('general_m');
        $this->load->model('qawiki_m');
        $this->sessionData = $this->login_m->isLoggedIn();
    }
    
    /* index() funkcija predstavlja index stranicu na web prikazu */
    public function index()
    {
        $this->load->view('main');
    }

    /* register() funkcija predstavlja register stranicu na web prikazu */
    public function register()
    {
        if($this->sessionData)
        {
            redirect('main/index');
        }
        $this->load->view('register');
    }
    
    /* login() funkcija predstavlja login stranicu na web prikazu */
    public function login()
    {
        if($this->sessionData)
        {
            redirect('main/index');
        }
        $this->load->view('login');
    }
    /* qa_wiki() funkcija predstavlja stranicu gdje će biti wiki i question/answer stranice 
     * $key parametar nam označava qa ili wiki, tj. da li je stranica question/answer ili wikipedia
     * $ask parametar označava, kada smo u question/answer sekciji i ako je u pitanju ask sekcija, treba da nam se otvori
     * sekcija gdje ćemo postavljati pitanje.
     */
    public function qa_wiki($key, $ask = null)
    {
        $data['key'] = $key;
        $data['ask'] = $ask;

        if(isset($ask))
        {
            if($ask == 'ask')
            {
                if($this->sessionData == false)
                {
                    $this->redirectpage->setRedirectToPage('main/qa_wiki/qa/ask');
                    redirect('main/login');
                }
                else
                {
                    $this->load->view('qa', $data);
                }
            }
            else if($ask == 'questions')
            {
                $data['questions'] = $this->general_m->getAll('questions', NULL);
                $this->load->view('qa', $data);
            }
        }
        else if($key == 'qa')
        {
            $this->load->view('qa', $data);
        }
        else if($key == 'wiki')
        {
            $this->load->view('wiki', $data);
        }
    }
    
    public function question($question_id)
    {
        $data['question'] = $this->qawiki_m->getQuestionDataById($question_id);
        $this->load->view('questions', $data);
    }
}