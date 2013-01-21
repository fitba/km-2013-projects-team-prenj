<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Qawiki_c extends CI_Controller 
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('general_m');
        $this->load->model('login_m');
    }
    
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
