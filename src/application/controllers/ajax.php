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
                        echo 'true';
                    }
                    else
                    {
                        echo 'Dogodila se neočekivana greška';
                    }
                }
            }
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
                    $errors[] = 'Morate se prijaviti da biste ocijenili pitanje! Prijavite se <a href="'.  base_url('index.php/login_c/loginUser').'">ovdje</a>';
                }
                else
                {
                    $where = "UserID = " . $sessionData['UserID'] . " AND QuestionID = " . $question_id . " AND Positive = '".$vote."'";
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
                                        'Positive' => $vote);

                    if($this->general_m->addData('votes', $dataInsert) === TRUE)
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
                    $errors[] = 'Morate se prijaviti da biste ocijenili pitanje! Prijavite se <a href="'.  base_url('index.php/login_c/loginUser').'">ovdje</a>';
                }
                else
                {
                    $where = "UserID = " . $sessionData['UserID'] . " AND AnswerID = " . $answer_id . " AND Positive = '".$vote."'";
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
                                        'Positive' => $vote);

                    if($this->general_m->addData('votes', $dataInsert) === TRUE)
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
    
    public function bestAnswer($answer_id, $question_id)
    {
        $sessionData = $this->sessionData;
        if(isset($answer_id))
        {
            if($sessionData == NULL)
            {
                $errors[] = 'Morate se prijaviti da biste ocijenili pitanje! Prijavite se <a href="'.  base_url('index.php/login_c/loginUser').'">ovdje</a>';
            }
            else
            {
                $where = "QuestionID = " . $question_id;
                $user = $this->general_m->selectSomeById('UserID', 'questions', $where);

                if($user['UserID'] != $sessionData['UserID'])
                {
                    $errors[] = 'Vi ne možete ocijeniti ovaj odgovor kao najbolji jer niste vlasnik pitanja!';
                }
            }

            if(!empty($errors))
            {
                $errors = $this->general_m->displayErrors($errors);
                echo $errors;
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
                        echo 'Ocijenili ste odgovor kao najbolji.';
                    }
                    else
                    {
                        echo 'Dogodila se nočekivana greška!';
                    }
                }
                else
                {
                    $dataUpdate = array('Best' => 0);

                    if($this->general_m->updateData('answers', $dataUpdate, 'AnswerID', $answer_id) === TRUE)
                    {
                        echo 'Vratili ste prethodnu vrijednost.';
                    }
                    else
                    {
                        echo 'Dogodila se nočekivana greška!';
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
                    $errors[] = 'Morate se prijaviti da biste ocijenili pitanje! Prijavite se <a href="'.  base_url('index.php/login_c/loginUser').'">ovdje</a>';
                }
                else
                {
                    $where = "UserID = " . $sessionData['UserID'] . " AND ArticleID = " . $article_id . " AND Positive = '".$vote."'";
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
                                        'Positive' => $vote);

                    if($this->general_m->addData('votes', $dataInsert) === TRUE)
                    {
                        $data['isOk'] = 'Uspješno ste ocijenili članak.';
                    }
                    else
                    {
                        $data['unexpectedError'] = 'Dogodila se nočekivana greška!';
                    }
                }
            }
        }
    }
}