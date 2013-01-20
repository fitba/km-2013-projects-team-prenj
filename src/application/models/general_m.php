<?php 
class General_m extends CI_Model
{
	public function __construct() 
	{
		parent::__construct ();
		$this->load->database();
	}
	
	public function addData($table, $data = array())
	{
		$dataInsert = array();
		foreach($data as $key => $value)
		{
                    $dataInsert[$key] = htmlentities(strip_tags(mysql_real_escape_string($value)));
		}
		$this->db->insert($table, $dataInsert);
		
		if($this->db->_error_number() == 1452)
			return FALSE;
		else
			return TRUE;
	}
	
	public function selectMax($column, $table, $where = NULL)
	{
		$this->db->select('MAX('.$column.') AS Last');
		
		if(isset($where))
		{
			$this->db->where($where);
		}
		
		$query = $this->db->get($table);
		
		return $query->result_array();
	}
	
	public function selectSomeById($column, $table, $where, $id)
	{
		$this->db->select($column);
		$this->db->where($where, $id);
		$query = $this->db->get($table);
		
		return $query->result_array();
	}
	
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
	
	public function updateData($table, $data = array(), $where, $id)
	{
		$dataUpdate = array();
		foreach($data as $key => $value)
		{
			$dataUpdate[$key] = htmlentities(strip_tags(mysql_real_escape_string($value)));
		}
		$this->db->where($where, $id);
		$this->db->update($table, $dataUpdate);
		
		if($this->db->_error_number() == 1452)
			return FALSE;
		else
			return TRUE;
	}
	
	public function exists($table, $column, $where)
	{
		$this->db->select('COUNT('.$column.') AS Count');
		$this->db->where($where);
		$query = $this->db->get($table);
		
		$count = $query->result_array();
		return $count[0]['Count'];
	}
	
	public function getAll($table, $orderBy, $config = array())
	{
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
	
	function displayErrors($errors)
	{
		$output = array();
		foreach($errors as $error)
		{
			$output[] = '<b style="color:red">'.$error.'</b><br/>';
		}
		
		return implode('', $output);
	}
	
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