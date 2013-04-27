<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Recommender
{
    var $sessionData;
    public function recommenderSystem($sessionData)
    {
        $CI =& get_instance();
        $CI->load->model('recommender_m');
        $CI->sessionData = $sessionData;
        
        if(isset($CI->sessionData))
        {
            $join_evaluation = array('users' => 'evaluation.UserID = users.UserID');
            $data['u_evaluation'] = $CI->recommender_m->getSomethingByUser('evaluation', $CI->sessionData['UserID'], $join_evaluation);
            if(count($data['u_evaluation']) == 0)
            {
                $join_votes = array('users' => 'votes.UserID = users.UserID');
                $data['u_votes'] = $CI->recommender_m->getSomethingByUser('votes', $CI->sessionData['UserID'], $join_votes);
                if(count($data['u_votes']) == 0)
                {
                    $join_comments = array('users' => 'comments.UserID = users.UserID');
                    $data['u_comments'] = $CI->recommender_m->getSomethingByUser('comments', $CI->sessionData['UserID'], $join_comments);
                    if(count($data['u_comments']) == 0)
                    {
                        $join_views = array('users' => 'views.UserID = users.UserID');
                        $data['u_views'] = $CI->recommender_m->getSomethingByUser('views', $CI->sessionData['UserID'], $join_views);
                        if(count($data['u_views']) == 0)
                        {
                            $join_top_rated_articles = array('evaluation' => 'evaluation.ArticleID = articles.ArticleID');
                            $data['top_rated_articles'] = $CI->recommender_m->getTopRated('articles', 'ArticleID', $join_top_rated_articles);
                            
                            $join_top_rated_questions = array('evaluation' => 'evaluation.QuestionID = questions.QuestionID');
                            $data['top_rated_questions'] = $CI->recommender_m->getTopRated('questions', 'QuestionID', $join_top_rated_questions);
                            
                            
                            $data['top_rated_tags'] = $CI->recommender_m->topRatedTags();
                        }
                        else
                        {
                            
                        }
                    }
                    else
                    {
                        
                    }
                }
                else 
                {
                    
                }
            }
            else
            {
                
            }
        }
        else
        {
            $join_top_rated_articles = array('evaluation' => 'evaluation.ArticleID = articles.ArticleID');
            $data['top_rated_articles'] = $CI->recommender_m->getTopRated('articles', 'ArticleID', $join_top_rated_articles);

            $join_top_rated_questions = array('evaluation' => 'evaluation.QuestionID = questions.QuestionID');
            $data['top_rated_questions'] = $CI->recommender_m->getTopRated('questions', 'QuestionID', $join_top_rated_questions);
            
            $data['top_rated_tags'] = $CI->recommender_m->topRatedTags();
        }
        
        return $data;
    }
}
?>
