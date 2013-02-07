<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login_c extends CI_Controller 
{   
    var $data; // globalna varijabla - definisana je na nivou klase.
    /* Konstruktor klase Login_c. On u sebi nasleđuje konstruktor iz klase CI_Controller, 
     * poziva model login_m i globalnoj varijabli $data prosleđuje funkciju isLoggedIn()
     * koja je definisana u modelu login_m */
    public function __construct()
    {
        parent::__construct();
        $this->data = $this->login_m->isLoggedIn();
    }
    
    /* loginUser() ima sledeće funkcionalnosti: Ako je korisnik logiran, ne može se vratiti opet na login stranicu. 
     * Ako je dugme za login pritisnuto, provjeravaju se unijete vrijednosti za username i password.
     * Ako unešene vrijednosti odgovaraju vrijednostima iz baze, kreira se sesija i korisnik je uspješno logiran.
     * Ako vrijednosti ne odgovaraj vrijednostima iz baze, korisniku će se izbaciti greška.
     */
    public function loginUser()
    {
        $_SESSION['nameOfFunction'] = __FUNCTION__;
        if($this->data)
        {
            redirect('main/index');
        }
        $data[''] = '';
        if(isset($_POST['login']))
        {
            $data['email_username'] = $this->input->post('email_username');
            $data['password'] = md5($this->input->post('password'));
            
            if($this->login_m->loginCheck($data) != false)
            {
                $id = $this->login_m->loginCheck($data);

                $dataOfUser = $this->login_m->getDataOfUserById($id);

                $session_data = array (
                            'user_id' => $dataOfUser['UserID'],
                            'email' => $dataOfUser['Email'],
                            'username' => $dataOfUser['Username'],
                            'password' => $dataOfUser['Password'],
                            'logged_in' => true
                        );

                $session_data = $this->login_m->dataForSession($session_data);

                session_start();
                foreach ($session_data as $key => $value) {
                        $_SESSION[$key] = $value;
                }
                session_write_close();
                $redirectPage = $_SESSION['redirect'];
                redirect($redirectPage);
            }
            else
            {
                $data['errors'] = 'Vaši unešeni podaci nisu tačni ili je moguće da niste još prijavljeni! Ako niste prijavili vaš nalog, molimo vas da prijavite vaš nalog klikom na link koji ste dobili putem mail-a.';
            }
        }
        $this->load->view('login', $data);
    }
    
    /* logout() označava prekidanje svih sesija koje se nalaze u sistemu. To znači da korisnika automatski 
     * izlogira iz sistema.
     */
    public function logout()
    {
        session_start ();
        session_destroy ();
        $_SESSION['redirect'] = null;
        //$this->session->sess_destroy();
        /*foreach ($session_data as $key => $value) 
        {
          setcookie ($key, $value, -COOKIE_TIME, "/");
        } */
        redirect('main/index');
    }
}