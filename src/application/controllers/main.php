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
        $this->load->library('formatdate');
        $this->load->model('general_m');
        $this->load->model('qawiki_m');
        $this->sessionData = $this->login_m->isLoggedIn();
    }
    
    /* index() funkcija predstavlja index stranicu na web prikazu */
    public function index()
    {
        $this->load->view('main');
    }
    
    public function profile($user_id)
    {
        if(isset($user_id))
        {
            $data['user_id'] = $user_id;
            $data['userData'] = $userData = $this->qawiki_m->getUserDataById($user_id);
            $nameOfFolder = 'pictures/' . $userData['UsersUserID'];
            $location = $data['baseLocation'] = 'http://'.$_SERVER['HTTP_HOST'].dirname(dirname(dirname(dirname($_SERVER['PHP_SELF'])))).'/'.$nameOfFolder;
            
            if(isset($_POST['uploadPicture']))
            {
                
                $errors = array();
                
                if(!empty($_FILES['profilePicture']['name']) && isset($_FILES['profilePicture']))
		{
                    $slika = $_FILES['profilePicture']['name'];
                    $slika_tmp = $_FILES["profilePicture"]['tmp_name'];
                    $slika_size = '';

                    if(!empty($slika_tmp))
                    {
                        $slika_size = getimagesize($slika_tmp);
                    }

                    $folder = $_SERVER['DOCUMENT_ROOT'].dirname(dirname(dirname(dirname($_SERVER['PHP_SELF'])))).'/'.$nameOfFolder;
                    if($slika_size == NULL)
                    {	
                        $errors[] = 'Ovo nije slika!';
                    }

                    $where4 = "ProfilePicture = '" . $slika . "'";

                    if($this->general_m->exists('users', 'UserID', $where4) > 0)
                    {
                        $errors[] = 'Slika sa ovim nazivom već se nalazi u bazi!';
                    }
                    
                    if(!empty($errors))
                    {
                        $data['errors'] = $this->general_m->displayErrors($errors);
                    }
                    else
                    {
                        $dataUpdate = array('ProfilePicture' => $slika);

                        if($this->general_m->updateData('users', $dataUpdate, 'UserID', $user_id) == TRUE)
                        {
                            if(!file_exists($folder))
                            {
                                mkdir($folder, 777);
                            }
                            move_uploaded_file($slika_tmp, $folder.'/'.$slika);
                            $data['isOk'] = 'Uspješno ste unijeli profil sliku.';
                        }
                        else
                        {
                            $data['unexpectedError'] = 'Dogodila se nočekivana greška!';
                        }
                        
                    }
		}
            }
            $this->load->view('profile', $data);
        }
    }

    /* 
     * question() funkcija predstavlja stranicu gdje se prikazuje određeno pitanje
     * za to određeno pitanje se daju odgovori. Svak može da odgovori na pitanje. Takođe i korisnik koji je postavio pitanje
     * može odgovoriti na nečiji odgovor. Za pitanje i odgovor korisnici mogu postavljati komentare.
     */
    public function question($question_id = NULL, $answer_id = NULL)
    {
        if(isset($_SESSION['redirect']))
        {
            $this->redirectpage->unsetRedirectData();
        }
        if(isset($question_id))
        {
            $data['question'] = $this->qawiki_m->getQuestionDataById($question_id);
            $data['question_id'] = $question_id;
            
            $data['sessionData'] = $sessionData = $this->sessionData;
            
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
                    $this->load->library('insertdata');
                
                    $dataInsert = $this->insertdata->dataForInsert('answers', $_POST);

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
                    $errors[] = 'Morate se prijaviti da biste postavili komentar! Prijavite se <a href="'.  base_url('index.php/login_c/loginUser').'">ovdje</a>';
                }

                if(!empty($errors))
                {
                    $data['errors'] = $this->general_m->displayErrors($errors);
                    $data['question_id'] = $question_id;
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
            $data['answers'] = $answers = $this->qawiki_m->getAnswersDataById($question_id);
            $data['commentsQuestion'] = $this->qawiki_m->getCommentsDataById($question_id, NULL);
            $this->load->view('questions', $data);
        }
        else
        {
            $data['message'] = 'Morate odabrati neko od pitanja da biste dobili njegove informacije! Vratite se na <a href="'.  base_url('index.php/qawiki_c/askQuestion/qa/questions').'">pitanja.</a>';
            $this->load->view('info/info_page', $data);
        }
    }
}