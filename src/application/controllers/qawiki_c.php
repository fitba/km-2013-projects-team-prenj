<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Qawiki_c extends CI_Controller 
{
    var $dataSession;
    /* Konstruktor klase Qawiki_c. On u sebi nasleđuje konstruktor iz klase CI_Controller, 
     * poziva model login_m i general_m i globalnoj varijabli */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('general_m');
        $this->load->model('qawiki_m');
        $this->load->model('login_m');
        $this->load->library('redirectpage');
        $this->load->library('formatdate');
        $this->dataSession = $this->login_m->isLoggedIn();
    }
    
    /* askQuestion() funkcija nam omogućava unos pitanja. Pošto smo u question/answer sekciji i još u sekciji question, tj
     * postavljamo pitanje, stranici moramo proslijediti podatak $ask da ne bi došlo do greške. */
    public function askQuestion()
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
                $this->load->view('qa', $data);
            }
            else
            {
                $data['sessionData'] = $this->dataSession;
                
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
                $this->load->view('qa', $data);
            }
        }
    }
    
    public function postCommentOnAnswer($question_id = NULL, $answer_id = NULL)
    {
        if(isset($_SESSION['redirect']))
        {
            $this->redirectpage->unsetRedirectData();
        }
        $data['sessionData'] = $sessionData = $this->dataSession;
        if(isset($answer_id, $question_id))
        {
            if(isset($_POST['submitComment']))
            {
                $errors = array();
                $requiredFields = array($this->input->post('comment'));

                foreach($requiredFields as $key => $value)
                {
                    if(empty($value))
                    {
                        $errors[] = 'Polje za komentar je obavezno polje!';
                        break 1;
                    }
                }

                if($sessionData == NULL)
                {
                    $this->redirectpage->setRedirectToPage('main/question/' . $question_id);
                    $errors[] = 'Morate se prijaviti da biste postavili komentar! Prijavite se <a href="'.  base_url('index.php/main/login').'">ovdje</a>';
                }

                if(!empty($errors))
                {
                    $data['errors'] = $this->general_m->displayErrors($errors);
                }
                else
                {
                    $this->load->library('insertdata');
                
                    $dataInsert = $this->insertdata->dataForInsert('comments', $_POST);

                    if($this->general_m->addData('comments', $dataInsert) == TRUE)
                    {
                        $data['isOk'] = 'Uspješno ste postavili komentar.';
                    }
                    else
                    {
                        $data['unexpectedError'] = 'Dogodila se nočekivana greška!';
                    }
                }
            }
            $data['question'] = $this->qawiki_m->getQuestionDataById($question_id);
            $data['question_id'] = $question_id;
            $data['answers'] = $answers = $this->qawiki_m->getAnswersDataById($question_id);
            $data['commentsQuestion'] = $this->qawiki_m->getCommentsDataById($question_id, NULL);
            $this->load->view('questions', $data);
        }
        else
        {
            $data['message'] = 'Za određeno pitanje postavljate komentar! Vratite se na <a href="'.  base_url('index.php/main/question/' . $question_id).'">nazad.</a>';
            $this->load->view('info/info_page', $data);
        }
    }
}
?>
