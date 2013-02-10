<?php 
class Logs_m extends CI_Model
{
    /* Konstruktor klase Qawiki_m. On u sebi nasleđuje konstruktor iz klase CI_Model, 
     * poziva funkciju database() koja će se koristiti kroz ovu klasu. */
    public function __construct() 
    {
        parent::__construct ();
        $this->load->database();
    }
    
    public function getLogs($select, $join = array())
    {
        $this->db->select($select);
        $this->db->from('logs');
        
        if(isset($join))
        {
            foreach($join as $key => $value)
            {
                $test = $this->db->join($key, $value, 'left');
            }
        }
        
        $query = $this->db->get();
        
        return $query->result_array();
    }
}
?>