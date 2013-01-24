<?php 
class Qawiki_m extends CI_Model
{
    /* Konstruktor klase Qawiki_m. On u sebi nasleđuje konstruktor iz klase CI_Model, 
     * poziva funkciju database() koja će se koristiti kroz ovu klasu. */
    public function __construct() 
    {
        parent::__construct ();
        $this->load->database();
    }

    /* Funkcija getQuestionDataById() vraća sve podatke o određenom pitanju na osnovu njegovog id-a.
     */
    public function getQuestionDataById($question_id)
    {
        $question_id = (int)$question_id;
        $this->db->select('*');
        $this->db->from('questions');
        $this->db->join('users', 'questions.UserID = users.UserID');
        $this->db->where('QuestionID', $question_id);
        
        $query = $this->db->get();
        
        return $query->row_array();
    }
    
    /* Funkcija getAnswersDataById() vraća sve podatke o odgovorima na određeno pitanje.
     */
    public function getAnswersDataById($question_id)
    {
        $question_id = (int)$question_id;
        $this->db->select('*');
        $this->db->from('answers');
        $this->db->join('users', 'answers.UserID = users.UserID');
        $this->db->where('QuestionID', $question_id);
        
        $query = $this->db->get();
        
        return $query->result_array();
    }
    
    public function getCommentsDataById($question_id = NULL, $answer_id = NULL)
    {
        $question_id = (int)$question_id;
        $answer_id = (int)$answer_id;
        
        $this->db->select('*');
        $this->db->from('comments');
        $this->db->join('users', 'comments.UserID = users.UserID');
        
        if($question_id != null)
        {
            $this->db->join('questions', 'comments.QuestionID = questions.QuestionID');
            $this->db->where('comments.QuestionID', $question_id);
        }
        
        if($answer_id != null)
        {
            $this->db->join('answers', 'comments.AnswerID = answers.AnswerID');
            $this->db->where('comments.AnswerID', $answer_id);
        }
        
        $query = $this->db->get();
        
        return $query->result_array();
    }
}
?>