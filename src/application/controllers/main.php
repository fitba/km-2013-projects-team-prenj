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
                $requiredFields = array($this->input->post('title'), $this->input->post('question'), $this->input->post('tags'));

                foreach($requiredFields as $key => $value)
                {
                    if(empty($value))
                    {
                        $errors[] = 'Polja koja su označena sa * su obavezna!';
                        break 1;
                    }
                }
                
                $tags = array();
                $inputTags = $_POST['tags'];
                if(preg_match('/^[A-Za-z ]+$/', $inputTags))
                {
                    $explodeTags = explode(' ', trim($inputTags));
                    foreach ($explodeTags as $key => $value)
                    {
                        if(!empty($value))
                        {
                            $tags[$key] = $value;
                        }
                    }
                }
                else
                {
                    $errors[] = 'Tagove morate odvojiti samo razmakom!';
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
                
                    $dataUpdate = $this->insertdata->dataForInsert('questions', $_POST);

                    if($this->general_m->updateData('questions', $dataUpdate, 'QuestionID', $question_id) == TRUE)
                    {
                        $dataInsertTags['QuestionID'] = $question_id;
                        
                        $logDataInsert = array('UserID' => $sessionData['UserID'],
                                               'LogDate' => date("Y-m-d H:i:s"),
                                               'QuestionID' => $question_id);
                        
                        $this->general_m->addData('logs', $logDataInsert);
                        
                        $this->load->library('zend');
                        $this->load->library('zend', 'Zend/Search/Lucene');
                        $this->zend->load('Zend/Search/Lucene');
                        $appPath = dirname(dirname(dirname(__FILE__))) . '\search\index';
                        $index = '';

                        if(!file_exists($appPath))
                        {
                            $index = Zend_Search_Lucene::create($appPath, true);
                        }
                        else
                        {
                            $index = Zend_Search_Lucene::open($appPath);
                            $index->optimize();
                        }

                        $myid = new Zend_Search_Lucene_Index_Term($question_id, 'myid');
                        $docIds  = $index->termDocs($myid);
                        foreach ($docIds as $id) 
                        {
                            $doc = $index->getDocument($id);
                            $keyword = $doc->keyword;
                            if($keyword == 'question')
                                $index->delete($id);
                        }

                        $doc = new Zend_Search_Lucene_Document();
                        $doc->addField(Zend_Search_Lucene_Field::Text('title', $_POST['title']));
                        $doc->addField(Zend_Search_Lucene_Field::Text('contents', $_POST['question']));
                        $doc->addField(Zend_Search_Lucene_Field::unIndexed('keyword', 'question'));
                        $doc->addField(Zend_Search_Lucene_Field::keyword('myid', $dataInsertTags['QuestionID']));
                        $tagsForSearch = '';

                        foreach ($tags as $value)
                        {
                            $tagsForSearch .= $value . ' ';
                            $tag_id = $this->general_m->selectSomeById('TagID', 'tags', "Name = '".$value."'");
                            $count = count($tag_id);

                            if($count > 0)
                            {
                                $tag = $this->general_m->selectSomeById('TagID', 'question_tags', "TagID = ".$tag_id['TagID']." AND QuestionID = " . $question_id);
                                if(count($tag) == 0)
                                {
                                    $dataInsertTags['TagID'] = $tag_id['TagID'];
                                    $this->general_m->addData('question_tags', $dataInsertTags);
                                }
                            }
                            else
                            {
                                $dataInsertTagsName['Name'] = $value;
                                $this->general_m->addData('tags', $dataInsertTagsName);

                                $dataInsertTags['TagID'] = mysql_insert_id();
                                $this->general_m->addData('question_tags', $dataInsertTags);
                            }
                        }
                        $data['isOk'] = 'Uspješno ste promijenili pitanje.';
                        $doc->addField(Zend_Search_Lucene_Field::text('tags', $tagsForSearch));
                        $index->addDocument($doc);
                        $index->commit();
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
                    $requiredFields = array($this->input->post('answer'));

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

                        $dataUpdate = $this->insertdata->dataForInsert('answers', $_POST);

                        if($this->general_m->updateData('answers', $dataUpdate, 'AnswerID', $answer_id) == TRUE)
                        {
                            $logDataInsert = array('UserID' => $sessionData['UserID'],
                                                   'LogDate' => date("Y-m-d H:i:s"),
                                                   'AnswerID' => $answer_id);
                        
                            $this->general_m->addData('logs', $logDataInsert);
                            
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
            $data['question'] = $this->qawiki_m->getQuestionDataById($question_id);
            $negativeQuestion = $this->general_m->countRows('votes', 'VoteID', "QuestionID = " . $question_id . " AND Positive = '0'");
            $positiveQuestion = $this->general_m->countRows('votes', 'VoteID', "QuestionID = " . $question_id. " AND Positive = '1'");
            
            $data['resultOfVotesForQuestion'] = ($positiveQuestion - $negativeQuestion);
            
            $data['answers'] = $answers = $this->qawiki_m->getAnswersDataById($question_id);
            $data['commentsQuestion'] = $this->qawiki_m->getCommentsDataById($question_id, NULL);
            $data['tags'] = $this->qawiki_m->getTagsForQuestion($question_id);
            
            $joinQuestion = array('questions' => 'questions.QuestionID = logs.QuestionID',
                                                 'users' => 'users.UserID = logs.UserID');
            
            $whereQuestion = 'logs.QuestionID = ' . $question_id;
            $data['lastChangeQuestion'] = $this->logs_m->getLogsBy('*', $joinQuestion, $whereQuestion);
            
            $this->load->view('questions', $data);
        }
        else
        {
            $data['message'] = 'Morate odabrati neko od pitanja da biste dobili njegove informacije! Vratite se na <a href="'.  base_url('index.php/qawiki_c/qa/questions').'">pitanja.</a>';
            $this->load->view('info/info_page', $data);
        }
    }
    
    public function article($article_id = null, $positive = NULL)
    {
        if(isset($article_id))
        {
            $errors = array();
            
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
                        $data['isOk'] = 'Uspješno ste ocijenili članak.';
                    }
                    else
                    {
                        $data['unexpectedError'] = 'Dogodila se nočekivana greška!';
                    }
                }
            }
            
            if(isset($_POST['submitEditArticle']))
            {
                if($sessionData == NULL)
                {
                    $errors[] = 'Morate se prijaviti da biste promijenili članak! Prijavite se <a href="'.  base_url('index.php/login_c/loginUser').'">ovdje</a>';
                }
                
                $requiredFields = array($this->input->post('title'), $this->input->post('content'));
                foreach($requiredFields as $key => $value)
                {
                    if(empty($value))
                    {
                        $errors[] = 'Polja koja su označena sa * su obavezna!';
                        break 1;
                    }
                }
                
                $tags = array();
                $inputTags = $_POST['tags'];
                if(preg_match('/^[A-Za-z ]+$/', $inputTags))
                {
                    $explodeTags = explode(' ', trim($inputTags));
                    foreach ($explodeTags as $key => $value)
                    {
                        if(!empty($value))
                        {
                            $tags[$key] = $value;
                        }
                    }
                }
                else
                {
                    $errors[] = 'Tagove morate odvojiti samo razmakom!';
                }

                if(!empty($errors))
                {
                    $data['errors'] = $this->general_m->displayErrors($errors);
                }
                else
                {
                    $this->load->library('insertdata');

                    $dataUpdate = $this->insertdata->dataForInsert('articles', $_POST);

                    if($this->general_m->updateData('articles', $dataUpdate, 'ArticleID', $article_id) == TRUE)
                    {
                        $dataInsertTags['ArticleID'] = $article_id;
                        $logDataInsert = array('UserID' => $sessionData['UserID'],
                                               'LogDate' => date("Y-m-d H:i:s"),
                                               'ArticleID' => $article_id);
                        
                        $this->general_m->addData('logs', $logDataInsert);
                        
                        $this->load->library('zend');
                        $this->load->library('zend', 'Zend/Search/Lucene');
                        $this->zend->load('Zend/Search/Lucene');
                        $appPath = dirname(dirname(dirname(__FILE__))) . '\search\index';
                        $index = '';

                        if(!file_exists($appPath))
                        {
                            $index = Zend_Search_Lucene::create($appPath, true);
                        }
                        else
                        {
                            $index = Zend_Search_Lucene::open($appPath);
                            $index->optimize();
                        }

                        $myid = new Zend_Search_Lucene_Index_Term($question_id, 'myid');
                        $docIds  = $index->termDocs($myid);
                        foreach ($docIds as $id) 
                        {
                            $doc = $index->getDocument($id);
                            $keyword = $doc->keyword;
                            if($keyword == 'article')
                                $index->delete($id);
                        }
                        
                        $doc = new Zend_Search_Lucene_Document();
                        $doc->addField(Zend_Search_Lucene_Field::Text('title', $_POST['title']));
                        $doc->addField(Zend_Search_Lucene_Field::Text('contents', $_POST['content']));
                        $doc->addField(Zend_Search_Lucene_Field::unIndexed('keyword', 'article'));
                        $doc->addField(Zend_Search_Lucene_Field::keyword('myid', $article_id));
                        $tagsForSearch = '';
                        
                        foreach ($tags as $value)
                        {
                            $tagsForSearch .= $value . ' ';
                            $tag_id = $this->general_m->selectSomeById('TagID', 'tags', "Name = '".$value."'");
                            $count = count($tag_id);

                            if($count > 0)
                            {
                                $tag = $this->general_m->selectSomeById('TagID', 'article_tags', "TagID = ".$tag_id['TagID']." AND ArticleID = " . $article_id);
                                if(count($tag) == 0)
                                {
                                    $dataInsertTags['TagID'] = $tag_id['TagID'];
                                    $this->general_m->addData('article_tags', $dataInsertTags);
                                }
                            }
                            else
                            {
                                $dataInsertTagsName['Name'] = $value;
                                $this->general_m->addData('tags', $dataInsertTagsName);

                                $dataInsertTags['TagID'] = mysql_insert_id();
                                $this->general_m->addData('article_tags', $dataInsertTags);
                            }
                        }
                        $data['isOk'] = 'Uspješno ste promijenili članak.';
                        $doc->addField(Zend_Search_Lucene_Field::text('tags', $tagsForSearch));
                        $index->addDocument($doc);
                        $index->commit();
                    }
                    else
                    {
                        $data['unexpectedError'] = 'Dogodila se nočekivana greška!';
                    }
                } 
            }
            
            $data['article'] = $this->qawiki_m->getArticleDataById($article_id);
            $negativeQuestion = $this->general_m->countRows('votes', 'VoteID', "ArticleID = " . $article_id . " AND Positive = '0'");
            $positiveQuestion = $this->general_m->countRows('votes', 'VoteID', "ArticleID = " . $article_id. " AND Positive = '1'");
            
            $joinArticle = array('articles' => 'articles.ArticleID = logs.ArticleID',
                                               'users' => 'users.UserID = logs.UserID');
            
            $whereArticle = 'logs.ArticleID = ' . $article_id;
            $data['lastChangeArticle'] = $this->logs_m->getLogsBy('*', $joinArticle, $whereArticle);
            $data['tags'] = $this->qawiki_m->getTagsForArticle($article_id);
            
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