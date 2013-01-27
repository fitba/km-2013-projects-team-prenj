<?php defined('BASEPATH') OR exit('No direct script access allowed');

class FormatDate
{
    /* globalna klasa koja omogućava formatiranje datuma. Kada se datum ispisuje u nekom polju, mi definišemo kako će se
     * taj datum ispisati.
     */
    public function getFormatDate($date)
    {
        return date('d.m.Y H:i:s', strtotime($date));
    }
}
?>