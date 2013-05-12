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
        $this->load->library('recommender');
        $this->sessionData = $this->login_m->isLoggedIn();
    }
    
    /* qa() funkcija nam omogućava unos pitanja. Pošto smo u question/answer sekciji i još u sekciji question, tj
     * postavljamo pitanje, stranici moramo proslijediti podatak $ask da ne bi došlo do greške. */
    public function qa($key = null, $per_page = 0)
    {
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

                        $explodeTags = explode(',', trim($inputTags));
                        foreach ($explodeTags as $key => $value)
                        {
                            if(!empty($value))
                            {
                                $tags[$key] = $value;
                            }
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

                            if($this->general_m->addData('questions', $dataInsert) === TRUE)
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
                                        if($this->general_m->addData('question_tags', $dataInsertTags) === FALSE)
                                        {
                                            $data['unexpectedError'] = 'Dogodila se nočekivana greška prilikom unosa tagova!';
                                        }
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
                $data = $this->recommender->recommenderSystem($this->sessionData);
                
                if(isset($per_page))
                    $data['per_page'] = $per_page;
                else
                    $data['per_page'] = 0;
                $config = array (
                        'limit' => LIMIT,
                        'offset' => $per_page
                );

                $data['questions'] = $users = $this->general_m->getAll('questions', 'AskDate', $config);

                $this->load->helper('MY_pagination');

                $data['pagination'] = generate_pagination ('qawiki_c/qa/questions/', 
                count($this->general_m->getAll('questions', 'AskDate')), 4, PER_PAGE);
            }
        }
        
        $data['key'] = $key;
        $data['sessionData'] = $this->sessionData;
        
        $this->load->view('qa', $data);
    }
    
    public function tags($tag_id = NULL, $per_page = 0)
    {
        if(isset($_GET['tag_search']))
        {
            $txtSearch = $_GET['tag_search'];
            
            $like = array('Name' => $txtSearch);
            
            $data = $this->recommender->recommenderSystem($this->sessionData);
            
            $data['tags'] = $this->general_m->search('tags', '*', $like);
            
            if(count($data['tags']) == 0)
            {
                $data['errors'] = 'Nema rezultata pretrage za termin <strong>"' . $txtSearch . '"</strong>';
            }
        }
        else
        {
            $data = $this->recommender->recommenderSystem($this->sessionData);
            
            if(isset($per_page))
                $data['per_page'] = $per_page;
            else
                $data['per_page'] = 0;
            $config = array (
                    'limit' => LIMIT,
                    'offset' => $per_page
            );
            $data['tags'] = $this->general_m->getAll('tags', 'Name', $config);

            $this->load->helper('MY_pagination');

            $data['pagination'] = generate_pagination ('qawiki_c/tags/0/', 
            count($this->general_m->getAll('tags', 'Name')), 4, PER_PAGE);
        }
        
        
        $data['tag'] = '';
        
        if(isset($tag_id))
        {
            $this->load->library('externalsources');
            $data['tag_id'] = $tag_id;
            $tag = $this->general_m->selectSomeById('*', 'tags', "TagID = '$tag_id'");
            $data['xmlWikiData'] = $this->externalsources->getDataFromWikipedia($tag['Name']);
            $data['jsonStackData'] = $this->externalsources->getDataFromStackOverflow($tag['Name']);
            
            if(isset($_POST['submitEditTag']))
            {
                $errors = array();
                $requiredFields = array($this->input->post('description'));
                $sessionData = $this->sessionData;
                
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
                    $errors[] = 'Morate se prijaviti da biste promijenili tag! Prijavite se <a href="'.  base_url('index.php/login_c/loginUser').'">ovdje</a>';
                }
                
                if(!empty($errors))
                {
                    $data['errors'] = $this->general_m->displayErrors($errors);
                }
                else
                {
                    $this->load->library('insertdata');
                    $dataUpdate = $this->insertdata->dataForInsert('tags', $_POST);
                    
                    if($this->general_m->updateData('tags', $dataUpdate, 'TagID', $tag['TagID']) === TRUE)
                    {
                        $logDataInsert = array('UserID' => $sessionData['UserID'],
                                               'LogDate' => date("Y-m-d H:i:s"),
                                               'TagID' => $tag['TagID']);
                        
                        $this->general_m->addData('logs', $logDataInsert);
                        
                        $data['isOk'] = 'Uspješno ste postavili pitanje.';
                    }
                    else
                    {
                        $data['unexpectedError'] = 'Dogodila se nočekivana greška!';
                    }
                }
            }

            if($tag !== FALSE)
            {
                $data['tag'] = $this->general_m->selectSomeById('*', 'tags', "TagID = '$tag_id'");
            }
            else
            {
                $data['unexpectedError'] = 'Došlo je do neočekivane greške prilikom uzimanja tagova iz baze!';
            }
        }
        $this->load->view('tags', $data);
    }

    public function users($per_page = 0)
    {
        if(isset($_GET['user_search']))
        {
            $txtSearch = $_GET['user_search'];
            
            $like = array('CONCAT(FirstName, \' \', LastName)' => $txtSearch);
            $or_like = array('FirstName' => $txtSearch,
                             'LastName' => $txtSearch);
            
            
            $data = $this->recommender->recommenderSystem($this->sessionData);
            
            $data['users'] = $this->general_m->search('users', '*', $like, $or_like);
            
            if(count($data['users']) == 0)
            {
                $data['errors'] = 'Nema rezultata pretrage za termin <strong>"' . $txtSearch . '"</strong>';
            }
        }
        else
        {
            $data = $this->recommender->recommenderSystem($this->sessionData);
            
            if(isset($per_page))
                $data['per_page'] = $per_page;
            else
                $data['per_page'] = 0;
            $config = array (
                    'limit' => LIMIT,
                    'offset' => $per_page
            );
            
            $data['users'] = $users = $this->general_m->getAll('users', 'FirstName', $config);

            $this->load->helper('MY_pagination');

            $data['pagination'] = generate_pagination ('qawiki_c/users/', 
            count($this->general_m->getAll('users', 'FirstName')), 3, PER_PAGE);
        }
        
        $this->load->view('users', $data);
    }
    
    public function wiki($key = null, $per_page = 0)
    {
        $data = $this->recommender->recommenderSystem($this->sessionData);
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
                    $config['order'] = 'Name';
                    $data['categories'] = $this->general_m->getAll('categories', null, $config);
                    if(isset($_POST['postArticle']))
                    {
                        $errors = array();
                        $requiredFields = array($this->input->post('title'), $this->input->post('categoryid'), $this->input->post('content'), $this->input->post('tags'));

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

                        $explodeTags = explode(',', trim($inputTags));
                        foreach ($explodeTags as $key => $value) 
                        {
                            if(!empty($value))
                            {
                                $tags[$key] = $value;
                            }
                        }
                        

                        if(!empty($errors))
                        {
                            $data['errors'] = $this->general_m->displayErrors($errors);
                        }
                        else
                        {
                            $sessionData = $this->sessionData;

                            $this->load->library('insertdata');

                            $dataInsert = $this->insertdata->dataForInsert('articles', $_POST);

                            if($this->general_m->addData('articles', $dataInsert) === TRUE)
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
                                        if($this->general_m->addData('article_tags', $dataInsertTags) === FALSE)
                                        {
                                            $data['unexpectedError'] = 'Dogodila se nočekivana greška prilikom unosa tagova!';
                                        }
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
                if(isset($per_page))
                    $data['per_page'] = $per_page;
                else
                    $data['per_page'] = 0;
                $config = array (
                        'limit' => LIMIT,
                        'offset' => $per_page
                );

                $data['articles'] = $this->general_m->getAll('articles', 'PostDate', $config);

                $this->load->helper('MY_pagination');

                $data['pagination'] = generate_pagination ('qawiki_c/wiki/articles/', 
                count($this->general_m->getAll('articles', 'PostDate')), 4, PER_PAGE);
            }
        }
        $this->load->view('wiki', $data);
    }
}
?>
