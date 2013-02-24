<?php 
class Login_m extends CI_Model
{
    /* Konstruktor klase Login_m. On u sebi nasleđuje konstruktor iz klase CI_Model, 
     * poziva funkciju database() koja će se koristiti kroz ovu klasu. */
    public function __construct() 
    {
        parent::__construct ();
        $this->load->database();
    }

    /* Funkcija loginChech() prima listu podataka. Na osnovu te liste ona provjerava da li username odgovara unešenom username
     * da li email odgovara unešenom emailu, da li lozinka odgovara unešenoj lozinci i da li je account potvrđen.
     * Ako je sve ovo zadovoljeno, funkcija će vratiti UserID, u suprotnom vratiće false.
     */
    public function loginCheck($data = array ()) 
    {
        $this->db->select('UserID');
        $where = "(Username = '".$data['email_username']."' OR Email='".$data['email_username']."') AND Password='".$data['password']."' AND ConfirmAccount = 1";
        $this->db->where($where);
        $upit = $this->db->get('users');
        $red = $upit->row_array ();
        return count ($red) > 0 ?  $red['UserID'] : FALSE;
    }

    /* Funkcija getDataOfUserById() prima $id. Funkcija vraća podatke o korisniku na osnovu unijetog $id-a.
     */
    public function getDataOfUserById($id)
    {
        $this->db->where('UserID', $id);
        $upit = $this->db->get('users');
        
        if($this->db->_error_number() > 0)
        {
            return FALSE;
        }
        else
        {
            return $upit->row_array();
        }
    }

    /* Funkcija decodeUserData() prima listu kolona koje treba da dekodira. 
     * Funkcija poziva library encrypt koja je neophodna za pozivanje decode funkcije. Funkcija vraća sesiju dekodiranih
     * podataka koji su prethodno bili kodirani dataForSession() funkcijom.
     */
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

    /* Funkcija dataForSession() prima listu podataka koje treba da kodira. 
     * Funkcija poziva library encrypt koja je neophodna za pozivanje decode funkcije. Funkcija vraća sesiju kodiranih
     * podataka.
     */
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

    /* Funkcija isLoggedIn() listu podataka o korisniku na osnovu njegovog id-a. To su uglavnom podaci koji se nalaze
     * u sesiji. Ti podaci se kasnije koriste da bi se ispisali na nekim stranicama gdje je to neophodno.
     */
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