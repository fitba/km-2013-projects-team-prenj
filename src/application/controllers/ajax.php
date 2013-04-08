<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends CI_Controller 
{
    var $sessionData;
    /* Konstruktor klase Ajax. On u sebi nasleđuje konstruktor iz klase CI_Controller, 
     * poziva model login_m i general_m i globalnoj varijabli */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('general_m');
        $this->sessionData = $this->login_m->isLoggedIn();
    }
    
    public function likeTag($tag_id)
    {
        $sessionData = $this->sessionData;
        if($sessionData != NULL)
        {
            if(isset($tag_id))
            {
                $exists = $this->general_m->exists('follow_tags', 'TagID', 'TagID = ' . $tag_id . ' AND UserID = ' . $sessionData['UserID']);
                if($exists == 0)
                {
                    $dataInsert = array('TagID' => $tag_id,
                                        'UserID' => $sessionData['UserID']);
                    if($this->general_m->addData('follow_tags', $dataInsert) === TRUE)
                    {
                        $count = $this->general_m->countRows('follow_tags', 'UserID', "TagID = " . $tag_id);
                        echo $count;
                    }
                    else
                    {
                        echo 'false';
                    }
                }
                else
                {
                    echo '<h3>Upozorenje</h3>
                            <hr/><p>Ovaj tag ste već lajkali</p>';
                }
            }
        }
        else
        {
            echo '<h3>Login validacija</h3>
                        <hr/><p style="color:red">Morate se prijaviti da biste lajkali tag! Prijavite se <a href="'.  base_url('index.php/login_c/loginUser').'">ovdje</a></p>';
        }
    }
    
    public function voteQuestion($question_id, $vote)
    {
        $sessionData = $this->sessionData;
        if(isset($question_id))
        {
            if(isset($vote))
            {
                if($sessionData == NULL)
                {
                    $errors[] = '
                        <h3>Login validacija</h3>
                        <hr/>
                        <p>Morate se prijaviti da biste ocijenili pitanje! Prijavite se <a href="'.  base_url('index.php/login_c/loginUser').'">ovdje</a></p>';
                }
                else
                {
                    $where = "UserID = " . $sessionData['UserID'] . " AND QuestionID = " . $question_id . " AND Positive = '".$vote."'";
                    $count = $this->general_m->exists('votes', 'VoteID', $where);

                    if($count > 0)
                    {
                        $errors[] = '
                            <h3>Upozorenje</h3>
                            <hr/>
                            <p>Već ste ocijenili to pitanje!</p>';
                    }
                }

                if(!empty($errors))
                {
                    $displayErrors = $this->general_m->displayErrors($errors);
                    echo $displayErrors;
                }
                else
                {
                    $dataInsert = array('UserID' => $sessionData['UserID'],
                                        'QuestionID' => $question_id,
                                        'Positive' => $vote);

                    if($this->general_m->addData('votes', $dataInsert) === TRUE)
                    {
                        $negative = $this->general_m->countRows('votes', 'VoteID', "QuestionID = " . $question_id . " AND Positive = '0'");
                        $positive = $this->general_m->countRows('votes', 'VoteID', "QuestionID = " . $question_id. " AND Positive = '1'");

                        $resultOfVotes = '';

                        if($negative !== FALSE && $positive !== FALSE)
                        {
                            $resultOfVotes = ($positive - $negative);
                        }

                        echo $resultOfVotes;
                    }
                    else
                    {
                        echo '<h3>Upozorenje</h3>
                        <hr/><p>Dogodila se neočekivana greska!</p>';
                    }
                }
            }
        }
    }
    
    public function voteAnswer($answer_id, $vote)
    {
        $sessionData = $this->sessionData;
        if(isset($answer_id))
        {
            if(isset($vote))
            {
                if($sessionData == NULL)
                {
                    $errors[] = '
                        <h3>Login validacija</h3>
                        <hr/>
                        <p>Morate se prijaviti da biste ocijenili odgovor! Prijavite se <a href="'.  base_url('index.php/login_c/loginUser').'">ovdje</a></p>';
                }
                else
                {
                    $where = "UserID = " . $sessionData['UserID'] . " AND AnswerID = " . $answer_id . " AND Positive = '".$vote."'";
                    $count = $this->general_m->exists('votes', 'VoteID', $where);

                    if($count > 0)
                    {
                        $errors[] = '
                            <h3>Upozorenje</h3>
                            <hr/>
                            <p>Već ste ocijenili taj odgovor!</p>';
                    }
                }

                if(!empty($errors))
                {
                    $displayErrors = $this->general_m->displayErrors($errors);
                    echo $displayErrors;
                }
                else
                {
                    $dataInsert = array('UserID' => $sessionData['UserID'],
                                        'AnswerID' => $answer_id,
                                        'Positive' => $vote);

                    if($this->general_m->addData('votes', $dataInsert) === TRUE)
                    {
                        $negative = $this->general_m->countRows('votes', 'VoteID', "AnswerID = " . $answer_id . " AND Positive = '0'");
                        $positive = $this->general_m->countRows('votes', 'VoteID', "AnswerID = " . $answer_id. " AND Positive = '1'");

                        $resultOfVotes = '';

                        if($negative !== FALSE && $positive !== FALSE)
                        {
                            $resultOfVotes = ($positive - $negative);
                        }

                        echo $resultOfVotes;
                    }
                    else
                    {
                        echo '<h3>Upozorenje</h3>
                        <hr/><p>Dogodila se neočekivana greska!</p>';
                    }
                }
            }
        }
    }
    
    public function bestAnswer($answer_id, $question_id)
    {
        $sessionData = $this->sessionData;
        if(isset($answer_id))
        {
            if($sessionData == NULL)
            {
                $errors[] = '<h3>Upozorenje</h3>
                            <hr/><p>Morate se prijaviti da biste označili pitanje kao najbolje! Prijavite se <a href="'.  base_url('index.php/login_c/loginUser').'">ovdje</a></p>';
            }
            else
            {
                $where = "QuestionID = " . $question_id;
                $user = $this->general_m->selectSomeById('UserID', 'questions', $where);

                if($user['UserID'] != $sessionData['UserID'])
                {
                    $errors[] = '<h3>Upozorenje</h3>
                            <hr/><p>Vi ne možete ocijeniti ovaj odgovor kao najbolji jer niste vlasnik pitanja!</p>';
                }
            }

            if(!empty($errors))
            {
                $displayErrors = $this->general_m->displayErrors($errors);
                echo $displayErrors;
            }
            else
            {
                $where = "AnswerID = " . $answer_id;
                $best = $this->general_m->selectSomeById('Best', 'answers', $where);

                if($best['Best'] == 0)
                {
                    $dataUpdate = array('Best' => 1);

                    if($this->general_m->updateData('answers', $dataUpdate, 'AnswerID', $answer_id) === TRUE)
                    {
                        echo '<img class="showsTooltip" src="'.base_url('assets/images/star1.png').'"
                                   onmousemove="Tooltip.Text = \'Vlasnik pitanja je ocijenio ovaj odgovor kao najbolji (kliknite opet da vratite na početno stanje)\'"
                                   onclick="best('.$answer_id.', \'/index.php/ajax/bestAnswer/\', '.$question_id.');" />';
                    }
                    else
                    {
                        echo '<h3>Upozorenje</h3>
                        <hr/><p>Dogodila se neočekivana greska!</p>';
                    }
                }
                else
                {
                    $dataUpdate = array('Best' => 0);

                    if($this->general_m->updateData('answers', $dataUpdate, 'AnswerID', $answer_id) === TRUE)
                    {
                        echo '';
                    }
                    else
                    {
                        echo '<h3>Upozorenje</h3>
                        <hr/><p>Dogodila se neočekivana greska!</p>';
                    }
                }
            }
        }
    }
    
    public function voteArticle($article_id, $vote)
    {
        $sessionData = $this->sessionData;
        if(isset($article_id))
        {
            if(isset($vote))
            {
                if($sessionData == NULL)
                {
                    $errors[] = '<h3>Login validacija</h3>
                        <hr/><p>Morate se prijaviti da biste ocijenili pitanje! Prijavite se <a href="'.  base_url('index.php/login_c/loginUser').'">ovdje</a></p>';
                }
                else
                {
                    $where = "UserID = " . $sessionData['UserID'] . " AND ArticleID = " . $article_id . " AND Positive = '".$vote."'";
                    $count = $this->general_m->exists('votes', 'VoteID', $where);

                    if($count > 0)
                    {
                        $errors[] = '<h3>Upozorenje</h3>
                        <hr/><p>Već ste ocijenili taj članak!</p>';
                    }
                }

                if(!empty($errors))
                {
                    $displayErrors = $this->general_m->displayErrors($errors);
                    echo $displayErrors;
                }
                else
                {
                    $dataInsert = array('UserID' => $sessionData['UserID'],
                                        'ArticleID' => $article_id,
                                        'Positive' => $vote);

                    if($this->general_m->addData('votes', $dataInsert) === TRUE)
                    {
                        $negative = $this->general_m->countRows('votes', 'VoteID', "ArticleID = " . $article_id . " AND Positive = '0'");
                        $positive = $this->general_m->countRows('votes', 'VoteID', "ArticleID = " . $article_id. " AND Positive = '1'");

                        $resultOfVotes = '';

                        if($negative !== FALSE && $positive !== FALSE)
                        {
                            $resultOfVotes = ($positive - $negative);
                        }

                        echo $resultOfVotes;
                    }
                    else
                    {
                        echo '<h3>Upozorenje</h3>
                        <hr/><p>Dogodila se neočekivana greska!</p>';
                    }
                }
            }
        }
    }
    
    public function getAutoCompleteTags($name)
    {
        if(!empty($name) || isset($name))
        {
            $this->db->like('Name',$name);
            $results = $this->db->get('tags')->result_array();
            
            foreach($results as $result)
            {
                echo '<li>'.$result['Name'].'</li>';
            }
        }  
    }
}