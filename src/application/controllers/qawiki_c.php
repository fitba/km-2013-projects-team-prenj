<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Qawiki_c extends CI_Controller 
{
    var $sessionData;
    /* Konstruktor klase Qawiki_c. On u sebi nasleđuje konstruktor iz klase CI_Controller, 
     * poziva model login_m i general_m i globalnoj varijabli */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('general_m');
        $this->load->model('qawiki_m');
        $this->load->library('formatdate');
        $this->sessionData = $this->login_m->isLoggedIn();
    }
    
    /* askQuestion() funkcija nam omogućava unos pitanja. Pošto smo u question/answer sekciji i još u sekciji question, tj
     * postavljamo pitanje, stranici moramo proslijediti podatak $ask da ne bi došlo do greške. */
    public function askQuestion($key, $ask = null)
    {
        $data['key'] = $key;
        $data['ask'] = $ask;

        $data['sessionData'] = $this->sessionData;
        
        if(isset($ask))
        {
            if($ask == 'ask')
            {
                if($this->sessionData == false)
                {
                    $errors[] = 'Morate se prijaviti da biste postavili pitanje! Prijavite se <a href="'.  base_url('index.php/login_c/loginUser').'">ovdje</a>';
                    $data['errors'] = $this->general_m->displayErrors($errors);
                }
                else
                {
                    if(isset($_POST['askQuestion']))
                    {
                        $errors = array();
                        $requiredFields = array($this->input->post('title'), $this->input->post('question'), $this->input->post('tags'));

                        foreach($requiredFields as $key => $value)
                        {
                            if(empty($value))
                            {
                                $errors[] = 'Polja koja su označena sa * su obavezna!';
                                break 1;
                            }
                        }
                        $data['ask'] = 'ask';

                        if(!empty($errors))
                        {
                            $data['errors'] = $this->general_m->displayErrors($errors);
                        }
                        else
                        {
                            $data['sessionData'] = $this->sessionData;

                            $this->load->library('insertdata');

                            $dataInsert = $this->insertdata->dataForInsert('questions', $_POST);

                            if($this->general_m->addData('questions', $dataInsert) == TRUE)
                            {
                                $data['isOk'] = 'Uspješno ste postavili pitanje.';
                            }
                            else
                            {
                                $data['unexpectedError'] = 'Dogodila se nočekivana greška!';
                            }
                        }
                    }
                }
            }
            else if($ask == 'questions')
            {
                $data['questions'] = $this->general_m->getAll('questions', NULL);
            }
            else if($ask == 'tags')
            {
                $data['tags'] = $this->general_m->getAll('tags', 'Name');
            }
            else if($ask == 'users')
            {
                $data['users'] = $this->general_m->getAll('users', 'FirstName');
            }
            $this->load->view('qa', $data);
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
    
    public function tags()
    {
        
    }
}
?>
