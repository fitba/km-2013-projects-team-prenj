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
        $this->load->library('formatdate');
        $this->load->model('general_m');
        $this->load->model('qawiki_m');
        $this->load->model('logs_m');
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
    public function question($question_id = NULL, $answer_id = NULL, $positive = NULL)
    {
        if(isset($question_id))
        {
            $data['question'] = $this->qawiki_m->getQuestionDataById($question_id);
            $data['question_id'] = $question_id;
            $data['sessionData'] = $sessionData = $this->sessionData;
            
            if($sessionData == NULL)
            {
                $ipAddress = $_SERVER['REMOTE_ADDR'];
                $ipAddressFromDB = $this->general_m->exists('views', 'ViewID', "IPAddress = '".$ipAddress."' AND QuestionID = " . $question_id);
                
                if($ipAddressFromDB <= 0)
                {
                    $dataInsert = array('IPAddress' => $ipAddress,
                                        'QuestionID' => $question_id);

                    $this->general_m->addData('views', $dataInsert);
                    
                }
            }
            else
            {
                $userIDFromDB = $this->general_m->exists('views', 'ViewID', "UserID = ".$sessionData['UserID']." AND QuestionID = " . $question_id);
                
                if($userIDFromDB <= 0)
                {
                    $dataInsert = array('UserID' => $sessionData['UserID'],
                                        'QuestionID' => $question_id);

                    $this->general_m->addData('views', $dataInsert);
                }
            }
            
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
                    $errors[] = 'Morate se prijaviti da biste odgovorili na pitanje! Prijavite se <a href="'.  base_url('index.php/login_c/loginUser').'">ovdje</a>';
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
            
            if(isset($_POST['submitEditQuestion']))
            {
                $errors = array();
                $requiredFields = array($this->input->post('newtitle'), $this->input->post('newContent'));

                foreach($requiredFields as $key => $value)
                {
                    if(empty($value))
                    {
                        $errors[] = 'Polja koja su označena sa * su obavezna!';
                        break 1;
                    }
                }
                
                if($sessionData == NULL)
                {
                    $errors[] = 'Morate se prijaviti da biste promijenili pitanje! Prijavite se <a href="'.  base_url('index.php/login_c/loginUser').'">ovdje</a>';
                }

                if(!empty($errors))
                {
                    $data['errors'] = $this->general_m->displayErrors($errors);
                    $data['question_id'] = $question_id;
                }
                else
                {
                    $this->load->library('insertdata');
                
                    $dataInsert = $this->insertdata->dataForInsert('logs', $_POST);

                    if($this->general_m->addData('logs', $dataInsert) == TRUE)
                    {
                        $data['isOk'] = 'Uspješno ste promijenili pitanje.';
                    }
                    else
                    {
                        $data['unexpectedError'] = 'Dogodila se nočekivana greška!';
                    }
                }
            }
            
            if(isset($answer_id))
            {
                $data['answer_id'] = $answer_id;
                
                if(isset($_POST['submitEditAnswer']))
                {
                    $errors = array();
                    $requiredFields = array($this->input->post('newContent'));

                    foreach($requiredFields as $key => $value)
                    {
                        if(empty($value))
                        {
                            $errors[] = 'Polja za unos odgovora je obavezno!';
                            break 1;
                        }
                    }

                    if($sessionData == NULL)
                    {
                        $errors[] = 'Morate se prijaviti da biste promijenili odgovor! Prijavite se <a href="'.  base_url('index.php/login_c/loginUser').'">ovdje</a>';
                    }

                    if(!empty($errors))
                    {
                        $data['errors'] = $this->general_m->displayErrors($errors);
                    }
                    else
                    {
                        $this->load->library('insertdata');

                        $dataInsert = $this->insertdata->dataForInsert('logs', $_POST);

                        if($this->general_m->addData('logs', $dataInsert) == TRUE)
                        {
                            $data['isOk'] = 'Uspješno ste promijenili odgovor.';
                        }
                        else
                        {
                            $data['unexpectedError'] = 'Dogodila se nočekivana greška!';
                        }
                    }
                }
                
                if(isset($_POST['submitCommentAnswer']))
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
                        $errors[] = 'Morate se prijaviti da biste postavili komentar! Prijavite se <a href="'.  base_url('index.php/login_c/loginUser').'">ovdje</a>';
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
                else if($answer_id == 0)
                {
                    if(isset($positive))
                    {
                        if($sessionData == NULL)
                        {
                            $errors[] = 'Morate se prijaviti da biste ocijenili pitanje! Prijavite se <a href="'.  base_url('index.php/login_c/loginUser').'">ovdje</a>';
                        }
                        else
                        {
                            $where = "UserID = " . $sessionData['UserID'] . " AND QuestionID = " . $question_id . " AND Positive = '".$positive."'";
                            $count = $this->general_m->exists('votes', 'VoteID', $where);

                            if($count > 0)
                            {
                                $errors[] = 'Već ste ocijenili to pitanje!';
                            }
                        }

                        if(!empty($errors))
                        {
                            $data['errors'] = $this->general_m->displayErrors($errors);
                        }
                        else
                        {
                            $dataInsert = array('UserID' => $sessionData['UserID'],
                                                'QuestionID' => $question_id,
                                                'Positive' => $positive);

                            if($this->general_m->addData('votes', $dataInsert) == TRUE)
                            {
                                $data['isOk'] = 'Uspješno ste ocijenili pitanje.';
                            }
                            else
                            {
                                $data['unexpectedError'] = 'Dogodila se nočekivana greška!';
                            }
                        }
                    }
                }
                else if($answer_id != 0)
                {
                    if(isset($positive))
                    {
                        if($sessionData == NULL)
                        {
                            $errors[] = 'Morate se prijaviti da biste ocijenili odgovor! Prijavite se <a href="'.  base_url('index.php/login_c/loginUser').'">ovdje</a>';
                        }
                        else
                        {
                            $where = "UserID = " . $sessionData['UserID'] . " AND AnswerID = " . $answer_id . " AND Positive = '".$positive."'";
                            $count = $this->general_m->exists('votes', 'VoteID', $where);

                            if($count > 0)
                            {
                                $errors[] = 'Već ste ocijenili taj odgovor!';
                            }
                        }
                        if(!empty($errors))
                        {
                            $data['errors'] = $this->general_m->displayErrors($errors);
                        }
                        else
                        {
                            $dataInsert = array('UserID' => $sessionData['UserID'],
                                                'AnswerID' => $answer_id,
                                                'Positive' => $positive);

                            if($this->general_m->addData('votes', $dataInsert) == TRUE)
                            {
                                $data['isOk'] = 'Uspješno ste ocijenili odgovor.';
                            }
                            else
                            {
                                $data['unexpectedError'] = 'Dogodila se nočekivana greška!';
                            }
                        }
                    }
                }
            }
            
            $negativeQuestion = $this->general_m->countRows('votes', 'VoteID', "QuestionID = " . $question_id . " AND Positive = '0'");
            $positiveQuestion = $this->general_m->countRows('votes', 'VoteID', "QuestionID = " . $question_id. " AND Positive = '1'");
            
            $data['resultOfVotesForQuestion'] = ($positiveQuestion - $negativeQuestion);
            
            $data['answers'] = $answers = $this->qawiki_m->getAnswersDataById($question_id);
            $data['commentsQuestion'] = $this->qawiki_m->getCommentsDataById($question_id, NULL);
            
            $joinQuestion = array('questions' => 'questions.QuestionID = logs.QuestionID',
                                                 'users' => 'users.UserID = logs.UserID');
            
            $whereQuestion = 'logs.QuestionID = ' . $question_id . ' AND logs.LogID = (SELECT MAX(LogID)
                                                                                      FROM logs
                                                                                      WHERE QuestionID = '.$question_id.')';
            $data['lastChangeQuestion'] = $this->logs_m->getLogsBy('*', $joinQuestion, $whereQuestion);
            
            $this->load->view('questions', $data);
        }
        else
        {
            $data['message'] = 'Morate odabrati neko od pitanja da biste dobili njegove informacije! Vratite se na <a href="'.  base_url('index.php/qawiki_c/qa/questions').'">pitanja.</a>';
            $this->load->view('info/info_page', $data);
        }
    }
    
    public function article($article_id = null, $subtitle_id = null, $positive = NULL)
    {
        if(isset($article_id))
        {
            $errors = array();
            $data['article'] = $this->qawiki_m->getArticleDataById($article_id);
            $data['article_id'] = $article_id;
            $data['sessionData'] = $sessionData = $this->sessionData;
            
            if($sessionData == NULL)
            {
                $ipAddress = $_SERVER['REMOTE_ADDR'];
                $ipAddressFromDB = $this->general_m->exists('views', 'ViewID', "IPAddress = '".$ipAddress."' AND ArticleID = " . $article_id);
                
                if($ipAddressFromDB <= 0)
                {
                    $dataInsert = array('IPAddress' => $ipAddress,
                                        'ArticleID' => $article_id);

                    $this->general_m->addData('views', $dataInsert);
                    
                }
            }
            else
            {
                $userIDFromDB = $this->general_m->exists('views', 'ViewID', "UserID = ".$sessionData['UserID']." AND ArticleID = " . $article_id);
                
                if($userIDFromDB <= 0)
                {
                    $dataInsert = array('UserID' => $sessionData['UserID'],
                                        'ArticleID' => $article_id);

                    $this->general_m->addData('views', $dataInsert);
                }
            }
            
            if(isset($subtitle_id))
            {
                if($subtitle_id == 0)
                {
                    if(isset($positive))
                    {
                        if($sessionData == NULL)
                        {
                            $errors[] = 'Morate se prijaviti da biste ocijenili članak! Prijavite se <a href="'.  base_url('index.php/login_c/loginUser').'">ovdje</a>';
                        }
                        else
                        {
                            $where = "UserID = " . $sessionData['UserID'] . " AND ArticleID = " . $article_id . " AND Positive = '".$positive."'";
                            $count = $this->general_m->exists('votes', 'VoteID', $where);

                            if($count > 0)
                            {
                                $errors[] = 'Već ste ocijenili taj članak!';
                            }
                        }
                        if(!empty($errors))
                        {
                            $data['errors'] = $this->general_m->displayErrors($errors);
                        }
                        else
                        {
                            $dataInsert = array('UserID' => $sessionData['UserID'],
                                                'ArticleID' => $article_id,
                                                'Positive' => $positive);

                            if($this->general_m->addData('votes', $dataInsert) == TRUE)
                            {
                                $data['isOk'] = 'Uspješno ste ocijenili odgovor.';
                            }
                            else
                            {
                                $data['unexpectedError'] = 'Dogodila se nočekivana greška!';
                            }
                        }
                    }
                }
                else
                {
                    $data['subtitle_id'] = $subtitle_id;
                }
            }
            
            if(isset($_POST['submitEditArticle']))
            {
                if($sessionData == NULL)
                {
                    $errors[] = 'Morate se prijaviti da biste promijenili članak! Prijavite se <a href="'.  base_url('index.php/login_c/loginUser').'">ovdje</a>';
                }
                
                $requiredFields = array($this->input->post('newtitle'), $this->input->post('newContent'));
                foreach($requiredFields as $key => $value)
                {
                    if(empty($value))
                    {
                        $errors[] = 'Polja koja su označena sa * su obavezna!';
                        break 1;
                    }
                }

                if(!empty($errors))
                {
                    $data['errors'] = $this->general_m->displayErrors($errors);
                }
                else
                {
                    $this->load->library('insertdata');

                    $dataInsert = $this->insertdata->dataForInsert('logs', $_POST);

                    if($this->general_m->addData('logs', $dataInsert) == TRUE)
                    {
                        $data['isOk'] = 'Uspješno ste promijenili članak.';
                    }
                    else
                    {
                        $data['unexpectedError'] = 'Dogodila se nočekivana greška!';
                    }
                } 
            }
            
            if(isset($_POST['submitEditSubtitle']))
            {
                if($sessionData == NULL)
                {
                    $errors[] = 'Morate se prijaviti da biste promijenili oblast članka! Prijavite se <a href="'.  base_url('index.php/login_c/loginUser').'">ovdje</a>';
                }

                $requiredFields = array($this->input->post('newtitle'), $this->input->post('newContent'));
                foreach($requiredFields as $key => $value)
                {
                    if(empty($value))
                    {
                        $errors[] = 'Polja koja su označena sa * su obavezna!';
                        break 1;
                    }
                }

                if(!empty($errors))
                {
                    $data['errors'] = $this->general_m->displayErrors($errors);
                }
                else
                {
                    $this->load->library('insertdata');

                    $dataInsert = $this->insertdata->dataForInsert('logs', $_POST);

                    if($this->general_m->addData('logs', $dataInsert) == TRUE)
                    {
                        $data['isOk'] = 'Uspješno ste promijenili oblast članka.';
                    }
                    else
                    {
                        $data['unexpectedError'] = 'Dogodila se nočekivana greška!';
                    }
                } 
            }
            
            if(isset($_POST['submitAddNewSubtitle']))
            {
                if($sessionData == NULL)
                {
                    $errors[] = 'Morate se prijaviti da biste dodali oblast članka! Prijavite se <a href="'.  base_url('index.php/login_c/loginUser').'">ovdje</a>';
                }

                $requiredFields = array($this->input->post('subtitle'), $this->input->post('subtitleContent'));
                foreach($requiredFields as $key => $value)
                {
                    if(empty($value))
                    {
                        $errors[] = 'Polja koja su označena sa * su obavezna!';
                        break 1;
                    }
                }

                if(!empty($errors))
                {
                    $data['errors'] = $this->general_m->displayErrors($errors);
                }
                else
                {
                    $this->load->library('insertdata');

                    $dataInsert = $this->insertdata->dataForInsert('subtitles', $_POST);

                    if($this->general_m->addData('subtitles', $dataInsert) == TRUE)
                    {
                        $data['isOk'] = 'Uspješno ste dodali oblast članka.';
                    }
                    else
                    {
                        $data['unexpectedError'] = 'Dogodila se nočekivana greška!';
                    }
                } 
            }
            
            $negativeQuestion = $this->general_m->countRows('votes', 'VoteID', "ArticleID = " . $article_id . " AND Positive = '0'");
            $positiveQuestion = $this->general_m->countRows('votes', 'VoteID', "ArticleID = " . $article_id. " AND Positive = '1'");
            
            $joinArticle = array('articles' => 'articles.ArticleID = logs.ArticleID',
                                               'users' => 'users.UserID = logs.UserID');
            
            $whereArticle = 'logs.ArticleID = ' . $article_id . ' AND logs.LogID = (SELECT MAX(LogID)
                                                                                    FROM logs
                                                                                    WHERE ArticleID = '.$article_id.')';
            $data['lastChangeArticle'] = $this->logs_m->getLogsBy('*', $joinArticle, $whereArticle);
            
            $data['subtitles'] = $this->qawiki_m->getPodContentDataByArticleId($article_id);
            $data['resultOfVotesForQuestion'] = ($positiveQuestion - $negativeQuestion);
            $this->load->view('articles', $data);
        }
        else
        {
            $data['message'] = 'Morate odabrati neko od članaka da biste dobili njegove informacije! Vratite se na <a href="'.  base_url('index.php/qawiki_c/wiki/articles').'">članke.</a>';
            $this->load->view('info/info_page', $data);
        }
    }
    
    public function changes()
    {
        $select = '*, questions.Title AS QuestionTitle, articles.Title AS ArticleTitle';
        $joins = array('users' => 'users.UserID = logs.UserID',
                       'answers' => 'answers.AnswerID = logs.AnswerID',
                       'questions' => 'questions.QuestionID = logs.QuestionID',
                       'articles' => 'articles.ArticleID = logs.ArticleID',
                       'subtitles' => 'subtitles.SubtitleID = logs.SubtitleID');
        
        $data['changes'] = $this->logs_m->getLogs($select, $joins);
        
        $this->load->view('changes', $data);
    }
}