<?php defined('BASEPATH') OR exit('No direct script access allowed');

class ExternalSources
{
    public function getDataFromWikipedia($tag)
    {
        $xml = simplexml_load_file('http://en.wikipedia.org/w/api.php?action=opensearch&search='.$tag.'&limit=5&namespace=0&format=xml');
        return $xml;
    } 
    
    public function getDataFromStackOverflow($tag)
    {
        $url = 'http://api.stackoverflow.com/1.1/search?intitle='.$tag.'&pagesize=5&sort=votes';
        $gzfile = file_get_contents($url);
        $jsonfile = gzdecode($gzfile);
        $jsondata = json_decode($jsonfile);
        return $jsondata;
    } 
}
?>
