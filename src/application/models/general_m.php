<?php 
class General_m extends CI_Model
{
    /* Konstruktor klase General_m. On u sebi nasleđuje konstruktor iz klase CI_Model, 
     * poziva funkciju database() koja će se koristiti kroz ovu klasu. */
    public function __construct() 
    {
        parent::__construct ();
        $this->load->database();
    }

    /* Globalna funkcija addData() prihvata 2 parametra $table i listu podataka $data. Prije nego što se izvrši unos 
     * podataka u bazu, prethodno se podaci filtriraju posebnim funkcijama koje osiguravaju da ne dođe do neželjenih
     * grešaka koje se mogu desiti prilikom unosa podataka u bazu.
     */
    public function addData($table, $data = array())
    {
        $dataInsert = array();
        foreach($data as $key => $value)
        {
            $dataInsert[$key] = strip_tags(stripslashes($value));
        }
        $this->db->insert($table, $dataInsert);

        if($this->db->_error_number() == 1452)
                return FALSE;
        else
                return TRUE;
    }

    /* Globalna funkcija selectMax() prihvata 3 parametra $column, $table i  $where calusulu koja nije obavezna. 
     * Funkcija vraća red koji u sebi sadrži maksimalnu vrijednost.
     */
    public function selectMax($column, $table, $where = NULL)
    {
        $this->db->select('MAX('.$column.') AS Last');

        if(isset($where))
        {
            $this->db->where($where);
        }

        $query = $this->db->get($table);

        return $query->row_array();
    }
    
    /* Funkcija getColumnNames povlači iz baze imena svih tabela */
    public function getColumnNames($table)
    {
        $this->db->select('*');
        $this->db->from('INFORMATION_SCHEMA.COLUMNS');
        $this->db->where('TABLE_NAME', $table);
        $query = $this->db->get();
        
        return $query->result_array();
    }

    /* Globalna funkcija selectSomeById() prihvata 4 parametra $column, $table, $where calusulu i $id. 
     * Funkcija vraća red koji na osnovu zadatog ID-a.
     */
    public function selectSomeById($column, $table, $where)
    {
        $this->db->select($column);
        $this->db->where($where);
        $query = $this->db->get($table);

        return $query->row_array();
    }

    /* Globalna funkcija deleteData() prihvata 3 parametra $column, $table, i $id. 
     * Funkcija briše red na osnovu zadatog ID-a.
     */
    public function deleteData($table, $column_id, $id)
    {
        $this->db->where($column_id, $id);
        $this->db->delete($table);

        if ($this->db->_error_number() == 1451)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    /* Globalna funkcija updateData() prihvata 4 parametra $table, listu podataka $data, $where i $id. 
     * Funkcija update-uje podatke koji su proslijeđeni listom na osnovu zadatog ID-a. Prije nego se izvrši update
     * vrši se osiguravanje unešenih podataka slično kao i kod insertovanja podataka.
     */
    public function updateData($table, $data = array(), $where, $id)
    {
        $dataUpdate = array();
        foreach($data as $key => $value)
        {
            $dataUpdate[$key] = strip_tags(stripslashes($value));
        }
        $this->db->where($where, $id);
        $this->db->update($table, $dataUpdate);

        if($this->db->_error_number() == 1452)
            return FALSE;
        else
            return TRUE;
    }

    /* Globalna funkcija exists() prihvata 3 parametra $table, $column i $where. 
     * Funkcija provjerava da li postoji proslijeđeni podatak u bazi.
     */
    public function exists($table, $column, $where)
    {
        $this->db->select('COUNT('.$column.') AS Count');
        $this->db->where($where);
        $query = $this->db->get($table);

        $count = $query->result_array();
        return $count[0]['Count'];
    }
    
    /* Globalna funkcija countRows() prihvata 3 parametra $table, $column i $where. 
     * Funkcija broji broj redova u bazi za zadati id.
     */
    public function countRows($table, $column, $where)
    {
        $this->db->select('COUNT('.$column.') AS Count');
        $this->db->where($where);
        $query = $this->db->get($table);

        $count = $query->result_array();
        return $count[0]['Count'];
    }

    /* Globalna funkcija getAll() prihvata 3 parametra $table, $orderBy i $config. 
     * Funkcija vraća sve podatke iz proslijeđene tabele.
     */
    public function getAll($table, $orderBy, $config = array())
    {
        if($orderBy != NULL)
            $this->db->order_by($orderBy, "asc");

        if (isset($config['limit'])) 
        {
            $this->db->limit($config['limit']);	
        }

        if (isset($config['offset'])) 
        {
            $this->db->offset($config['offset']);
        }


        $query = $this->db->get($table);
        return $query->result_array();
    }

    /* Globalna funkcija getAll() prihvata 1 parametar $errors. 
     * Funkcija ispisuje listu grešaka koje je korisnik napravio tokom registracije ili login-a.
     */
    function displayErrors($errors)
    {
        $output = array();
        foreach($errors as $error)
        {
            $output[] = '<b style="color:red">'.$error.'</b><br/>';
        }

        return implode('', $output);
    }

    /* Globalna funkcija search() prihvata 5 parametara $table, $select, $like, $or_like, $join. 
     * Na osnovu select-a koji je proslijeđen, naziva tabele i parametara po kojem je vršena pretraga funkcija će vratiti
     * one podatke koji odgovaraju proslijeđenim parametrima.
     */
    public function search($table, $select, $like = array(), $or_like = array(), $join = array())
    {
        $this->db->select($select);
        $this->db->like($like);

        if(isset($or_like))
        {
            foreach($or_like as $key => $value)
            {
                $this->db->or_like($key, $value);
            }
        }

        $this->db->from($table);

        if(isset($join))
        {
            foreach($join as $key => $value)
            {
                $this->db->join($key, $value, 'left');
            }
        }

        $query = $this->db->get();

        if(count($query) > 0)
            return $query->result_array();
        else
            return FALSE;
    }
}
?>