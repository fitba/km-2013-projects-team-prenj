<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Register_c extends CI_Controller 
{
    /* Konstruktor klase Register_c. On u sebi nasleđuje konstruktor iz klase CI_Controller, 
     * poziva model login_m i general_m. Ako je korisnik već logiran na sistem, on se ne može vratiti na register
     * stranicu sve dok je sesija popunjena. */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('general_m');
        if($this->login_m->isLoggedIn())
        {
            redirect('main/index');
        }
    }
    
    /* confirmAccount() prima jedan parametar, tj ključ. Kada se korisnik registruje na sistem, neophodno je da potvrdi
     * svoj account. Potvrda za account mu se šalje putem maila. Randomly generisan ključ mu se prosleđuje u linku
     * i klikom na taj link potvrđuje mu se account.
     */
    public function confirmAccount($key)
    {
        $this->load->database();
        
        $where = "Key = '" . $key . "'";
        
        if($this->general_m->exists('users', 'UserID', $where) > 0)
        {
            $data = array( 'ConfirmAccount' => 1);
            
            if($this->general_m->updateData('users', $data, 'Key', $key) === TRUE)
            {
                $data['message'] = 'Uspješno ste potvrdili vašu registraciju. Idite na <a href="'. base_url('index.php/login_c/loginUser').'">Log in</a> stranicu kako biste se prijavili na sistem.';
                $this->load->view('info/info_page', $data);
            }
        }
        else
        {
            $data['message'] = 'Ovaj ključ nije isti kao onaj koji vam je poslat email-om!';
            $this->load->view('info/info_page', $data);
        }
    }

    /* registerUser() funkcija ima sledeće funkcionalnosti: Kada se pritisne dugme za registraciju, definišu se 
     * obavezna polja. Ako je jedno polje od obaveznih prazno, funkcija se prekida i izbacuje se upozorenje da svako polje
     * koje je obavetno mora biti popunjeno. Ako je svako takvo polje popunjeno, vrše se dodatne validacije i to:
     * korisničko ime ne smije imati razmak u sebi, lozinka ne smije biti manja od 6 karaktera, lozinke moraju biti iste,
     * i na kraju ne smijemo unijeti username i email koji već postoje u sistemu. Ako smo sve zadovoljili, prelazi se na
     * definisanje podataka za unos. To su oni podaci koje ste unijeli u formi. Ako je sve u redu, prelazi se na slanje
     * e-maila korisniku. Na kraju ostaje samo potvrda accounta koji je objašnjen funkcijom confirmAccount($key)
     */
    public function register()
    {
        $data[''] = '';
        if(isset($_POST['registerUser']))
        {
            $errors = array();
            $requiredFields = array($this->input->post('username'), $this->input->post('password'), $this->input->post('email'), $this->input->post('sex'));
            
            foreach($requiredFields as $key => $value)
	    {
	        if(empty($value) || $value == '0')
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
            }
            else
            {
                $this->load->library('insertdata');
                
                $dataInsert = $this->insertdata->dataForInsert('users', $_POST);
                $dataInsert['ConfirmAccount'] = 1;

                if($this->general_m->addData('users', $dataInsert) === TRUE)
                {
                    $to      = $this->input->post('email');
                    $subject = 'Potvrdite vaš nalog';
                    $message = '<p>Hvala vam za vašu registraciju na naš sistem. Da biste potvrdili vašu registraciju na sistem molimo vas da kliknete 
                                    <a href="'.base_url('register_c/confirmAccount/' . $key).'">Ovde</a>
                                .</p>';
                    $headers  = 'MIME-Version: 1.0' . "\r\n";
                    $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
                    $headers .= 'From:admin@localhost' . "\r\n";
                    $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
                    $headers .="X-Priority: 1\n";
                    $headers .="X-MSMail-Priority: High\n";
                    
                    if(mail($to, $subject, $message, $headers))
                    {
                        $data['isOk'] = 'Uspješno ste se registrovali. Za nekoliko sekudi bićete prebačeni na login stranicu.';
                        header('refresh:2;url=' . base_url('index.php/login_c/loginUser'));
                    }
                    else
                        $data['isOk'] = 'Uspješno ste se registrovali. Nažalost email nije poslat. Molimo vas da provjerite vaš SMTP server.';
                }
                else
                {
                    $data['unexpectedError'] = 'Dogodila se nočekivana greška!';
                }
            }
        }
        $this->load->view('register', $data);
    }
}
