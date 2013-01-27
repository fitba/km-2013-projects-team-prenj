<?php defined('BASEPATH') OR exit('No direct script access allowed');

class InsertData
{
    /* Globlna klasa koja nam omogućava unos podataka. Funkcija dataForInsert prihvata dva parametra $table i $post podatke.
     * Ono što ova funkcija radi je sledeće: Ona uzima imena kolona iz baze za određenu tabelu i prihvata podatke za unos
     * kroz neku formu. Suština funkcije jeste da unese podatke u bazu, i to dinamički. Funkcija uzme imena kolona iz tabele
     * koje predstavljaju definisanje kolona u koje će se podaci smjestiti i prosleđuje tim kolonama vrijednosti iz post
     * podataka koje smo unijeli kroz neku formu.
     */
    public function dataForInsert($table, $post = array())
    {
        $CI =& get_instance();
        $CI->load->model('general_m');
        
        $columnNames = $CI->general_m->getColumnNames($table);
        $insertData = array();

        foreach ($columnNames as $valueName) {
            $name = $valueName['COLUMN_NAME'];
            foreach ($post as $key => $valuePost) {
                if(strtolower($name) == strtolower($key))
                {
                    if(strtolower($name) == 'password')
                        $valuePost = md5($valuePost);
                    if(strtolower($name) == 'userid')
                        $valuePost = base64_decode($valuePost);
                    if(strtolower($name) == 'answerid')
                        $valuePost = base64_decode($valuePost);
                    if(strtolower($name) == 'questionid')
                        $valuePost = base64_decode($valuePost);
                    $insertData[$name] = $valuePost;
                    break;
                }
            }

        }
        return $insertData;
    }
}
?>
