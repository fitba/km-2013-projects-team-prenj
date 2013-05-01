<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Search_c extends CI_Controller 
{
    var $search_index;
    var $sessionData;
    function __construct()
    {
        parent::__construct();
        $this->load->library('zend');   
        $this->load->library('zend', 'Zend/Search/Lucene');
        $this->zend->load('Zend/Search/Lucene');
        $appPath = dirname(dirname(dirname(__FILE__)));
        $this->search_index = $appPath . '\search\index';
        $this->load->model('general_m');
        $this->load->model('qawiki_m');
        error_reporting(0);
        $this->sessionData = $this->login_m->isLoggedIn();
    }
    
    public function index()
    {
        $this->load->library('recommender');
        $data = $this->recommender->recommenderSystem($this->sessionData);
            
        // If a search_query parameter has been posted, search the index.
        if ($_GET['pretraga'])
        {
            
            // Create empty array, in case there are no results.
            $data['results'] = array();
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