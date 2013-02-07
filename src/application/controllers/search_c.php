<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Search_c extends CI_Controller 
{
    function __construct() 
    {
        parent::__construct();
        $this->load->library('zend', 'Zend/Feed');   
        $this->load->library('zend', 'Zend/Search/Lucene');   
        $this->load->library('zend');   
        $this->zend->load('Zend/Feed');   
        $this->zend->load('Zend/Search/Lucene');
    }
    
    public function search()
    {
        $dirForSearch = dirname(dirname(__FILE__));
        $dirForSearch = str_replace('\application', '', $dirForSearch);
        $index = Zend_Search_Lucene($dirForSearch . '\search', true);
 
        /* Ubacivanje podataka u taj dokument */
        
        $doc = new Zend_Search_Lucene_Document();
        $doc->addField(Zend_Search_Lucene_Field::unIndexed('title', 'Item number 1'));
        $doc->addField(Zend_Search_Lucene_Field::text('contents', 'cow elephant dog hamster'));
        $index->addDocument($doc);

        $doc->addField(Zend_Search_Lucene_Field::unIndexed('title', 'Item number 2'));
        $doc->addField(Zend_Search_Lucene_Field::text('contents', 'cow aardvark dog hamster'));
        $index->addDocument($doc);

        $doc->addField(Zend_Search_Lucene_Field::unIndexed('title', 'Item number 3'));
        $doc->addField(Zend_Search_Lucene_Field::text('contents', 'cow elephant dog esquilax elephant'));
        $index->addDocument($doc);

        $index->commit();
        /* Ubacivanje podataka u taj dokument */
        
        
        // Pretraga podataka iz dokumenta
        /*$index   = Zend_Search_Lucene::open('D:\xampp\htdocs\ZendLuceneSearch\search');
        $results = $index->find('contents:elephant');

        foreach ( $results as $result ) 
        {
            echo $result->score, ' :: ', $result->title . '<br/>';
        }*/
    }
}