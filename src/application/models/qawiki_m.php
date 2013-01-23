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
}
?>