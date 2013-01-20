<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login_c extends CI_Controller 
{   
    var $data;
    public function __construct()
    {
        parent::__construct();
        $this->load->model('login_m');
        $this->data = $this->login_m->isLoggedIn();
    }

    public function loginUser()
    {
        if($this->data)
        {
            redirect('main/index');
        }
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
                redirect('main/index');
            }
            else
            {
                $data['error'] = 'Vaši unešeni podaci nisu tačni ili je moguće da niste još prijavljeni! Ako niste prijavili vaš nalog, molimo vas da prijavite vaš nalog klikom na link koji ste dobili putem mail-a.';
                $this->load->view('login', $data);
            }
        }
        else
        {
            $this->load->view('login', $data);
        }
    }

    public function logout() 
    {
        session_start ();
        session_destroy ();
        //$this->session->sess_destroy();
        /*foreach ($session_data as $key => $value) 
        {
          setcookie ($key, $value, -COOKIE_TIME, "/");
        } */
        redirect('main/index');
    }
}