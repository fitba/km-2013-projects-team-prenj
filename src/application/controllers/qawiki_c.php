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
    
    /* qa() funkcija nam omogućava unos pitanja. Pošto smo u question/answer sekciji i još u sekciji question, tj
     * postavljamo pitanje, stranici moramo proslijediti podatak $ask da ne bi došlo do greške. */
    public function qa($key = null)
    {
        $data['key'] = $key;

        $data['sessionData'] = $this->sessionData;
        
        if(isset($key))
        {
            if($key == 'ask')
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
                            $data['sessionData'] = $this->sessionData;

                            $this->load->library('insertdata');

                            $dataInsert = $this->insertdata->dataForInsert('questions', $_POST);

                            if($this->general_m->addData('questions', $dataInsert) == TRUE)
                            {
                                $dataInsertTags['QuestionID'] = mysql_insert_id();
                                
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
                                        $dataInsertTags['TagID'] = $tag_id['TagID'];
                                        $this->general_m->addData('question_tags', $dataInsertTags);
                                    }
                                    else
                                    {
                                        $dataInsertTagsName['Name'] = $value;
                                        $this->general_m->addData('tags', $dataInsertTagsName);

                                        $dataInsertTags['TagID'] = mysql_insert_id();
                                        $this->general_m->addData('question_tags', $dataInsertTags);
                                    }
                                }
                                $data['isOk'] = 'Uspješno ste postavili pitanje.';
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
                }
            }
            else if($key == 'questions')
            {
                $data['questions'] = $this->general_m->getAll('questions', NULL);
            }
        }
        $this->load->view('qa', $data);
    }
    
    public function tags()
    {
        $data['tags'] = $this->general_m->getAll('tags', 'Name');
        $this->load->view('tags', $data);
    }
    
    public function users()
    {
        $data['users'] = $this->general_m->getAll('users', 'FirstName');
        $this->load->view('users', $data);
    }
    
    public function wiki($key = null)
    {
        $data[''] = '';
        $data['subtitlesTags'] = '';
        $data['sessionData'] = $this->sessionData;
        
        if(isset($key))
        {
            $data['key'] = $key;
            
            if($key == 'postArticles')
            {
                if($this->sessionData == false)
                {
                    $errors[] = 'Morate se prijaviti da biste postavili wiki članak! Prijavite se <a href="'.  base_url('index.php/login_c/loginUser').'">ovdje</a>';
                    $data['errors'] = $this->general_m->displayErrors($errors);
                }
                else
                {
                    if(isset($_POST['postArticle']))
                    {
                        $errors = array();
                        $requiredFields = array($this->input->post('title'), $this->input->post('content'), $this->input->post('tags'));

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
                            $sessionData = $this->sessionData;

                            $this->load->library('insertdata');
                            $test = $_POST;
                            $dataInsert = $this->insertdata->dataForInsert('articles', $_POST);

                            if($this->general_m->addData('articles', $dataInsert) == TRUE)
                            {
                                $dataInsertSubtitles['ArticleID'] = mysql_insert_id();
                                $dataInsertTags['ArticleID'] = mysql_insert_id();
                                
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
                                    
                                $doc = new Zend_Search_Lucene_Document();
                                $doc->addField(Zend_Search_Lucene_Field::Text('title', $_POST['title']));
                                $doc->addField(Zend_Search_Lucene_Field::Text('contents', $_POST['content']));
                                $doc->addField(Zend_Search_Lucene_Field::unIndexed('keyword', 'article'));
                                $doc->addField(Zend_Search_Lucene_Field::keyword('myid', $dataInsertTags['ArticleID']));
                                $tagsForSearch = '';
                                
                                foreach ($tags as $value)
                                {
                                    $tagsForSearch .= $value . ' ';
                                    $tag_id = $this->general_m->selectSomeById('TagID', 'tags', "Name = '".$value."'");
                                    $count = count($tag_id);
                                    if($count > 0)
                                    {
                                        $dataInsertTags['TagID'] = $tag_id['TagID'];
                                        $this->general_m->addData('article_tags', $dataInsertTags);
                                    }
                                    else
                                    {
                                        $dataInsertTagsName['Name'] = $value;
                                        $this->general_m->addData('tags', $dataInsertTagsName);

                                        $dataInsertTags['TagID'] = mysql_insert_id();
                                        $this->general_m->addData('article_tags', $dataInsertTags);
                                    }
                                }
                                
                                $data['isOk'] = 'Uspješno ste postavili članak.';
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
                }
            }
            else if($key == 'articles')
            {
                $data['articles'] = $this->general_m->getAll('articles', NULL);
            }
        }
        $this->load->view('wiki', $data);
    }
}
?>
