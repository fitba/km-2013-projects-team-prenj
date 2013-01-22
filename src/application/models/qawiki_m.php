<?php 
class Qawiki_m extends CI_Model
{
    /* Konstruktor klase Qawiki_m. On u sebi nasleđuje konstruktor iz klase CI_Model, 
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
}
?>