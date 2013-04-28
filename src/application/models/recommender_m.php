<?php 
class Recommender_m extends CI_Model
{
    /* Konstruktor klase Qawiki_m. On u sebi nasleđuje konstruktor iz klase CI_Model, 
     * poziva funkciju database() koja će se koristiti kroz ovu klasu. */
    public function __construct() 
    {
        parent::__construct ();
        $this->load->database();
    }
    
    public function getSomethingByUser($table, $where, $join = array(), $order_by = null)
    {
        $this->db->select('*');
        $this->db->from($table);
        
        if(isset($join))
        {
            foreach($join as $key => $value)
            {
                $this->db->join($key, $value, 'left');
            }
        }
        
        $this->db->where($where);
        
        if(isset($order_by))
        {
            $this->db->order_by($order_by);
        }
        
        $query = $this->db->get();
        
        if($this->db->_error_number() > 0)
        {
            return FALSE;
        }
        else
        {
            return $query->result_array();
        }
    }
    
    public function getAverageEvaluateForUser($table_id, $where)
    {
        /* 
         *  SELECT SUM( Evaluate ) as Sum , COUNT( * ) as Count
            FROM evaluation
            WHERE UserID = 3 AND ArticleID IS NOT NULL
        */
        $this->db->select('SUM( Evaluate ) as Sum , COUNT( * ) as Count');
        $this->db->from('evaluation');
        
        $this->db->where($where);
        
        $query = $this->db->get();
        
        if($this->db->_error_number() > 0)
        {
            return FALSE;
        }
        else
        {
            return $query->row_array();
        }
    }
    
    public function getTopRated($table, $id, $join)
    {
        /* SELECT a.ArticleID, a.Title, (SUM( e.Evaluate ) / COUNT(e.Evaluate)) as Average
            FROM articles a
            JOIN evaluation e ON a.ArticleID = e.ArticleID
            GROUP BY a.ArticleID, a.Title
            ORDER BY (SUM( e.Evaluate ) / COUNT(e.Evaluate)) DESC */
        $this->db->select($table. '.' . $id . ' as ID, ' . $table . '.Title as Title, (SUM(evaluation.Evaluate) / COUNT(evaluation.Evaluate)) as Average');
        $this->db->from($table);
        
        if(isset($join))
        {
            foreach($join as $key => $value)
            {
                $this->db->join($key, $value, 'left');
            }
        }
        $this->db->group_by($table. '.' . $id . ', ' . $table . '.Title');
        $this->db->order_by('(SUM(evaluation.Evaluate) / COUNT(evaluation.Evaluate))', 'DESC');
        $this->db->limit(5);
        $query = $this->db->get();
        
        if($this->db->_error_number() > 0)
        {
            return FALSE;
        }
        else
        {
            return $query->result_array();
        }
    }
    
    public function getMostViewed($table, $id, $join)
    {
        /* 
         * SELECT a.Title, a.ArticleID, COUNT( v.ViewID ) 
            FROM views v
            JOIN articles a ON a.ArticleID = v.ArticleID
            GROUP BY v.ArticleID
            ORDER BY COUNT( v.ViewID ) DESC
         */
        
        $this->db->select($table. '.' . $id . ' as ID, ' . $table . '.Title as Title, COUNT( views.ViewID ) as Count');
        $this->db->from('views');
        
        if(isset($join))
        {
            foreach($join as $key => $value)
            {
                $this->db->join($key, $value, 'left');
            }
        }
        $this->db->group_by('views.ArticleID');
        $this->db->order_by('COUNT( views.ViewID )', 'DESC');
        $this->db->limit(5);
        $query = $this->db->get();
        
        if($this->db->_error_number() > 0)
        {
            return FALSE;
        }
        else
        {
            return $query->result_array();
        }
    }
    
    public function topRatedTags()
    {
        /* SELECT t.Name, ft.TagID, COUNT( * ) 
            FROM tags t
            JOIN follow_tags ft ON ft.TagID = t.TagID
            JOIN users u ON u.UserID = ft.UserID
            GROUP BY ft.TagID
            ORDER BY COUNT( * ) DESC */
        
        $this->db->select('t.Name, ft.TagID, COUNT(*)');
        $this->db->from('tags t');
        $this->db->join('follow_tags ft', 'ft.TagID = t.TagID');
        $this->db->join('users u', 'u.UserID = ft.UserID');
        $this->db->group_by('ft.TagID');
        $this->db->order_by('COUNT(*)', 'DESC');
        $this->db->limit(10);
        $query = $this->db->get();
        
        if($this->db->_error_number() > 0)
        {
            return FALSE;
        }
        else
        {
            return $query->result_array();
        }
    }
    
    public function getSomethingByTag($table, $table_id, $table_tag, $tag_id, $order_by = null)
    {
        $this->db->select($table . '.' . $table_id . ', ' . $table . '.Title');
        $this->db->from($table);
        $this->db->join($table_tag, $table_tag . '.' . $table_id . ' = ' . $table . '.' . $table_id);
        $this->db->where($table_tag . '.TagID', $tag_id);
        if(isset($order_by))
        {
            $this->db->order_by($order_by);
        }
        $this->db->limit(5);
        $query = $this->db->get();
        
        if($this->db->_error_number() > 0)
        {
            return FALSE;
        }
        else
        {
            return $query->result_array();
        }
    }
}
?>