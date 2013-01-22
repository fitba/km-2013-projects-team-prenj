<?php defined('BASEPATH') OR exit('No direct script access allowed');

class RedirectPage
{
    /* globalna klasa koja definiše na koju stranicu smo htjeli da uđemo a nismo bili prijavljeni
     *  na sistem. Posle će se ona iskoristiti za redirektanje na tu stranicu, kada se prijavimo na sistem. */
    var $redirectToPage;
    
    public function setRedirectToPage($redirectToPage)
    {
        $_SESSION['redirect'] = $redirectToPage;
    }
    
    public function getRedirectToPage()
    {
        return $_SESSION['redirect'];
    }
    
    public function unsetUserdata()
    {
        $_SESSION['redirect'] = NULL;
    }
}
?>