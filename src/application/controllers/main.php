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
    
    /* 
     * question() funkcija predstavlja stranicu gdje se prikazuje određeno pitanje
     * za to određeno pitanje se daju odgovori. Svak može da odgovori na pitanje. Takođe i korisnik koji je postavio pitanje
     * može odgovoriti na nečiji odgovor
     */
    public function question($question_id = null)
    {
        if(isset($_SESSION['redirect']))
        {
            $this->redirectpage->unsetRedirectData();
        }
        if(isset($question_id))
        {
            $data['question'] = $this->qawiki_m->getQuestionDataById($question_id);
            $data['question_id'] = $question_id;
            
            $sessionData = $this->sessionData;
            
            if(isset($_POST['submitAnswer']))
            {
                $errors = array();
                $requiredFields = array($this->input->post('answer'));

                foreach($requiredFields as $key => $value)
                {
                    if(empty($value))
                    {
                        $errors[] = 'Polje za odgovor je obavezno polje!';
                        break 1;
                    }
                }

                if($sessionData == NULL)
                {
                    $this->redirectpage->setRedirectToPage('main/question/' . $question_id);
                    $errors[] = 'Morate se prijaviti da biste odgovorili na pitanje! Prijavite se <a href="'.  base_url('index.php/main/login').'">ovdje</a>';
                }

                if(!empty($errors))
                {
                    $data['errors'] = $this->general_m->displayErrors($errors);
                    $data['question_id'] = $question_id;
                }
                else
                {
                    $dataInsert = array( 'Answer' => $_POST['answer'],
                                   'UserID' => $sessionData['UserID'],
                                   'QuestionID' => $question_id,
                                   'AnswerDate' => date("Y-m-d H:i:s")
                                   );

                    if($this->general_m->addData('answers', $dataInsert) == TRUE)
                    {
                        $data['isOk'] = 'Uspješno ste odgovorili na pitanje.';
                    }
                    else
                    {
                        $data['unexpectedError'] = 'Dogodila se nočekivana greška!';
                    }
                }
            }
            
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
                    $data['errorsComment'] = $this->general_m->displayErrors($errors);
                    $data['question_id'] = $question_id;
                }
                else
                {
                    $lastOrdinal = $this->general_m->selectMax('Ordinal', 'comments', 'QuestionID = ' . $question_id);
                    
                    if($lastOrdinal['Last'] == null)
                        $lastOrdinal['Last'] = 0;
                    
                    $dataInsert = array( 'Comment' => $_POST['comment'],
                                   'UserID' => $sessionData['UserID'],
                                   'QuestionID' => $question_id,
                                   'CommentDate' => date("Y-m-d H:i:s"),
                                   'Ordinal' => $lastOrdinal['Last'] + 1
                                   );

                    if($this->general_m->addData('comments', $dataInsert) == TRUE)
                    {
                        $data['isOkComment'] = 'Uspješno ste odgovorili na pitanje.';
                    }
                    else
                    {
                        $data['unexpectedErrorComment'] = 'Dogodila se nočekivana greška!';
                    }
                }
            }
            $data['answers'] = $this->qawiki_m->getAnswersDataById($question_id);
            $data['comments'] = $this->qawiki_m->getCommentsDataById($question_id, NULL);
            $this->load->view('questions', $data);
        }
        else
        {
            $data['message'] = 'Morate odabrati neko od pitanja da biste dobili njegove informacije! Vratite se na <a href="'.  base_url('index.php/main/qa_wiki/qa').'">pitanja.</a>';
            $this->load->view('info/info_page', $data);
        }
    }
}