<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Search_c extends CI_Controller 
{
    var $search_index;
    function __construct() 
    {
        parent::__construct();
        $this->load->library('zend');   
        $this->load->library('zend', 'Zend/Search/Lucene');
        $this->zend->load('Zend/Search/Lucene');
        $appPath = dirname(dirname(dirname(__FILE__)));
        $this->search_index = $appPath . '\search\index';
        //error_reporting(0);
    }
    
    public function index()
    {
        // Create empty array, in case there are no results.
        $data['results'] = array();

        // If a search_query parameter has been posted, search the index.
        if ($_GET['pretraga'])
        {
            if(file_exists($this->search_index))
            {
                $index = Zend_Search_Lucene::open($this->search_index);
                $index->optimize();

                // Get results.
                $data['results'] = $index->find($_GET['pretraga']);
            }
        }

        // Load view, and populate with results.
        $this->load->view('search', $data);
    }
}