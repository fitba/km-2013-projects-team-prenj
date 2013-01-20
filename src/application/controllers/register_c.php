<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Register_c extends CI_Controller 
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('general_m');
        $this->load->model('login_m');
        if($this->login_m->isLoggedIn())
        {
            redirect('main/index');
        }
    }
    
    public function confirmAccount($key)
    {
        $this->load->database();
        
        $where = "Key = '" . $key . "'";
        
        if($this->general_m->exists('users', 'UserID', $where) > 0)
        {
            $data = array( 'ConfirmAccount' => 1);
            
            if($this->general_m->updateData('users', $data, 'Key', $key) == TRUE)
            {
                echo '<h3>Uspješno ste potvrdili vašu registraciju. Idite na <a href="'. base_url('index.php/main/login').'">Log in</a> stranicu kako biste se prijavili na sistem.</h3>';
            }
        }
    }

    public function registerUser()
    {
        if(isset($_POST['registerUser']))
        {
            $errors = array();
            $requiredFields = array($this->input->post('username'), $this->input->post('password'), $this->input->post('email'));
            
            foreach($requiredFields as $key => $value)
	    {
	        if(empty($value))
	        {
	            $errors[] = 'Polja koja su označena sa * su obavezna!';
                    break 1;
	        }
	    }
            
            if(preg_match('/\\s/', $_POST['username']) == true)
            {
                $errors[] = 'Korisnicko ime ne smije da sadrži znak razdvajanja.';
            }
            if(strlen($_POST['password']) <= 5)
            {
                $errors[] = 'Lozinka mora da sadrži najmanje 6 karaktera.';
            }
            /*if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false)
            {
                $errors[] = 'Email nije validan!';
            }*/
            if($_POST['password'] !== $_POST['passwordConfirm'])
            {
                $errors[] = 'Lozinke se ne poklapaju!';
            }
            
            $where1 = "Username = '" . $_POST['username'] . "'";
            $where2 = "Email = '" . $_POST['email'] . "'";
            
            if($this->general_m->exists('users', 'UserID', $where1) > 0)
            {
                $errors[] = 'Korisnik sa ovim korisničkim imenom već postoji u bazi!';
            }
            if($this->general_m->exists('users', 'UserID', $where2) > 0)
            {
                $errors[] = 'Korisnik sa ovim email-om već postoji u bazi!';
            }
            
            if(!empty($errors))
            {
                $data['errors'] = $this->general_m->displayErrors($errors);
                $this->load->view('register', $data);
            }
            else
            {
                $key = md5(uniqid());
                
                $data = array( 'FirstName' => $_POST['firstName'],
                               'LastName' => $_POST['lastName'],
                               'Username' => $_POST['username'],
                               'Password' => md5($_POST['password']),
                               'Email' => $_POST['email'],
                               'RegistrationDate' => date("Y-m-d H:i:s"),
                               'Key' => $key,
                               'UserType' => 'user');
                
                if($this->general_m->addData('users', $data) == TRUE)
                {
                    $to      = $this->input->post('email');
                    $subject = 'Potvrdite vaš nalog';
                    $message = '<p>Hvala vam za vašu registraciju na naš sistem. Da biste potvrdili vašu registraciju na sistem molimo vas da kliknete 
                                    <a href="'.base_url('register_c/confirmAccount/' . $key).'">Ovde</a>
                                .</p>';
                    $headers  = 'MIME-Version: 1.0' . "\r\n";
                    $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
                    $headers .= 'From:admin@localhost' . "\r\n";
                    $headers .= "Reply-To: Retroshoes <info@retroshoes.ba>\r\n"; 
                    $headers .= "Return-Path: Retroshoes <info@retroshoes.ba>\r\n"; 
                    $headers .= "Organization: Retroshoes\r\n"; 
                    $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
                    $headers .="X-Priority: 1\n";
                    $headers .="X-MSMail-Priority: High\n";
                    $headers .="X-Mailer:Retroshoes.ba\n"; 
                    
                    if(mail($to, $subject, $message, $headers))
                        $data['isOk'] = 'Uspješno ste se registrovali. Molimo vas da provjerite vaš email.';
                    else
                        $data['isOk'] = 'Uspješno ste se registrovali. Nažalost email nije poslat. Molimo vas da provjerite vaš SMTP server.';
                }
                else
                {
                    $data['unexpectedError'] = 'Dogodila se nočekivana greška!';
                }
                $this->load->view('register', $data);
            }
        }
        else
        {
            redirect('main/register');
        }
    }
}
