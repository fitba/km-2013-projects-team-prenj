<?php 
class Login_m extends CI_Model
{
	public function __construct() 
	{
            parent::__construct ();
            $this->load->database();
	}
	
	public function loginCheck($data = array ()) 
	{
            $this->db->select('UserID');
            $where = "(Username = '".$data['email_username']."' OR Email='".$data['email_username']."') AND Password='".$data['password']."' AND ConfirmAccount = 1";
            $this->db->where($where);
            $upit = $this->db->get('users');
            $red = $upit->row_array ();
            return count ($red) > 0 ?  $red['UserID'] : FALSE;
	}
	
	public function getDataOfUserById($id)
	{
            $this->db->where('UserID', $id);
            $upit = $this->db->get('users');
            return $upit->row_array();
	}
	
	public function decodeUserData($column = array())
	{
            $this->load->library('encrypt');

            $session_data = array();

            if(is_array($column))
            {
                foreach($column as $key => $value)
                {
                    $session_data[$key] = $this->encrypt->decode($value);
                }
            }

            return $session_data;
	}
	
	public function dataForSession($data = array())
	{
            $this->load->library('encrypt');

            $session_data = array();

            if(is_array($data))
            {
                foreach($data as $key => $value)
                {
                    $session_data[$key] = $this->encrypt->encode($value);
                }
            }

            return $session_data;
	}
	
	public function isLoggedIn()
	{
            if(!isset($_SESSION)) 
            { 
                session_start(); 
            } 
		
            if(isset($_SESSION['logged_in']))
            {
                if($_SESSION['logged_in'] == true)
                {
                    $data = array('user_id' => $_SESSION['user_id'],
                                  'email_username' => $_SESSION['email'],
                                  'password' => $_SESSION['password'],
                                  'email_username' => $_SESSION['username']);

                    $data_decode = $this->decodeUserData($data);

                    if($this->loginCheck($data_decode))
                    {
                        return $this->getDataOfUserById($data_decode['user_id']);
                    }
                    else
                    {
                        return false;
                    }
                }
            }
            if(!isset($_SESSION)) 
            { 
                session_write_close(); 
            }
	}
}
?>