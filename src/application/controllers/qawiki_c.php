<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Qawiki_c extends CI_Controller 
{
    /* Konstruktor klase Qawiki_c. On u sebi nasleđuje konstruktor iz klase CI_Controller, 
     * poziva model login_m i general_m i globalnoj varijabli */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('general_m');
        $this->load->model('login_m');
    }
    
    /* askQuestion() funkcija nam omogućava unos pitanja. Pošto smo u question/answer sekciji i još u sekciji question, tj
     * postavljamo pitanje, stranici moramo proslijediti podatak $ask da ne bi došlo do greške. */
    public function askQuestion()
    {
        if(isset($_POST['askQuestion']))
        {
            $errors = array();
            $requiredFields = array($this->input->post('title'), $this->input->post('editor'), $this->input->post('tags'));
            
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
                $this->load->view('qa', $data);
            }
            else
            {
                $sessionData = $this->login_m->isLoggedIn();
                
                $data = array( 'Title' => $_POST['title'],
                               'Question' => $_POST['editor'],
                               'Tags' => $_POST['tags'],
                               'UserID' => $sessionData['UserID']
                               );
                
                if($this->general_m->addData('questions', $data) == TRUE)
                {
                    $data['isOk'] = 'Uspješno ste postavili pitanje.';
                }
                else
                {
                    $data['unexpectedError'] = 'Dogodila se nočekivana greška!';
                }
                $this->load->view('qa', $data);
            }
        }
    }
}
?>
