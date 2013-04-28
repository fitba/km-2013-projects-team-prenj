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
            $data['u_evaluation'] = $CI->recommender_m->getSomethingByUser('evaluation', 'users.UserID = ' . $CI->sessionData['UserID'], $join_evaluation);
            if(count($data['u_evaluation']) == 0)
            {
                $join_votes = array('users' => 'votes.UserID = users.UserID');
                $data['u_votes'] = $CI->recommender_m->getSomethingByUser('votes', 'users.UserID = ' . $CI->sessionData['UserID'], $join_votes);
                if(count($data['u_votes']) == 0)
                {
                    $join_comments = array('users' => 'comments.UserID = users.UserID');
                    $data['u_comments'] = $CI->recommender_m->getSomethingByUser('comments', 'users.UserID = ' . $CI->sessionData['UserID'], $join_comments);
                    if(count($data['u_comments']) == 0)
                    {
                        $join_views = array('users' => 'views.UserID = users.UserID');
                        $data['u_views'] = $CI->recommender_m->getSomethingByUser('views', 'users.UserID = ' . $CI->sessionData['UserID'], $join_views);
                        if(count($data['u_views']) == 0)
                        {
                            $join_top_rated_articles = array('evaluation' => 'evaluation.ArticleID = articles.ArticleID');
                            $data['top_rated_articles'] = $CI->recommender_m->getTopRated('articles', 'ArticleID', $join_top_rated_articles);
                            
                            $join_top_rated_questions = array('evaluation' => 'evaluation.QuestionID = questions.QuestionID');
                            $data['top_rated_questions'] = $CI->recommender_m->getTopRated('questions', 'QuestionID', $join_top_rated_questions);
                            
                            if(count($data['top_rated_articles']) == 0)
                            {
                                $join_most_viewed_articles = array('articles' => 'articles.ArticleID = views.ArticleID');
                                $data['most_viewed_articles'] = $CI->recommender_m->getMostViewed('articles', 'ArticleID', $join_most_viewed_articles);
                            }
                            
                            if(count($data['top_rated_questions']) == 0)
                            {
                                $join_most_viewed_question = array('questions' => 'questions.QuestionID = views.ArticleID');
                                $data['most_viewed_questions'] = $CI->recommender_m->getMostViewed('questions', 'QuestionID', $join_most_viewed_question);
                            }
                            
                            $data['top_rated_tags'] = $CI->recommender_m->topRatedTags();
                        }
                        else
                        {
                            $CI->load->model('qawiki_m');
                            $articleTags = '';
                            $questionTags = '';
                            for ($i = 0; $i < count($data['u_views']); $i++)
                            {
                                $i = count($data['u_views']) - 1;
                                if($data['u_views'][$i]['ArticleID'] != null)
                                {
                                    $tagsForArticle = $CI->qawiki_m->getTagsForArticle($data['u_views'][$i]['ArticleID']);
                                    foreach ($tagsForArticle as $ta)
                                    {
                                        $articleTags .= $ta['TagID'] . ',';
                                    }
                                }
                                else if($data['u_views'][$i]['QuestionID'] != null)
                                {
                                    $tagsForQuestion = $CI->qawiki_m->getTagsForQuestion($data['u_views'][$i]['QuestionID']);
                                    foreach ($tagsForQuestion as $tq)
                                    {
                                        $questionTags .= $tq['TagID'] . ',';
                                    }
                                }
                            }
                            
                            foreach(explode(',', $articleTags) as $keyArticle => $value)
                            {
                                $test = $CI->recommender_m->getSomethingByTag('articles', 'ArticleID', 'article_tags', $value);
                                $test1 = $CI->recommender_m->getSomethingByTag('questions', 'QuestionID', 'question_tags', $value);
                                $c = count(explode(',', $articleTags));
                                if($keyArticle === ($c - 1))
                                {
                                    break;
                                }
                            }
                            
                            foreach(explode(',', $questionTags) as $keyArticle => $value)
                            {
                                $test = $CI->recommender_m->getSomethingByTag('articles', 'ArticleID', 'article_tags', $value);
                                $test1 = $CI->recommender_m->getSomethingByTag('questions', 'QuestionID', 'question_tags', $value);
                                $c = count(explode(',', $questionTags));
                                if($keyArticle === ($c - 1))
                                {
                                    break;
                                }
                            }
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
                $userSumAndCountEvalArticle = $CI->recommender_m->getAverageEvaluateForUser('ArticleID', 'UserID = ' . $CI->sessionData['UserID'] . ' AND ArticleID IS NOT NULL');
                $userSumAndCountEvalQuestion = $CI->recommender_m->getAverageEvaluateForUser('QuestionID', 'UserID = ' . $CI->sessionData['UserID'] . ' AND QuestionID IS NOT NULL');
                
                $userAverageEvalArticle = ($userSumAndCountEvalArticle['Sum'] / $userSumAndCountEvalArticle['Count']);
                $userAverageEvalQuestion = ($userSumAndCountEvalQuestion['Sum'] / $userSumAndCountEvalQuestion['Count']);
                
                
                
                $valuesForCurrUserArticle = array();
                $articlesWhichCurrUserEvaluate = array();
                $sumEvalCurrUserArticle = 0;
                
                $valuesForCurrUserQuestion = array();
                $questionsWhichCurrUserEvaluate = array();
                $sumEvalCurrUserQuestion = 0;
                
                foreach ($data['u_evaluation'] as $value)
                {
                    if($value['ArticleID'] != null)
                    {
                        $u_curr_eval = $value['Evaluate'];
                        array_push($valuesForCurrUserArticle, ($u_curr_eval - $userAverageEvalArticle));
                        array_push($articlesWhichCurrUserEvaluate, $value['ArticleID']);
                        
                        $sumEvalCurrUserArticle += pow(($u_curr_eval - $userAverageEvalArticle), 2);
                    }
                    else if($value['QuestionID'] != null)
                    {
                        $u_curr_eval = $value['Evaluate'];
                        array_push($valuesForCurrUserQuestion, ($u_curr_eval - $userAverageEvalQuestion));
                        array_push($questionsWhichCurrUserEvaluate, $value['QuestionID']);
                        
                        $sumEvalCurrUserQuestion += pow(($u_curr_eval - $userAverageEvalQuestion), 2);
                    }
                }
                
                $join_evaluation = array('users' => 'evaluation.UserID = users.UserID');
                $usersBesidesCurrentUserArticle = $CI->recommender_m->getSomethingByUser('evaluation', 'users.UserID != ' . $CI->sessionData['UserID'] . ' AND QuestionID IS NULL AND ArticleID IN ('. implode(',', $articlesWhichCurrUserEvaluate) .')', $join_evaluation, 'evaluation.UserID');
                
                $sumEvalOtherUser = 0;
                $keyArticle = 0;
                $sumArticle = 0;
                $users_ids = array();
                $data['totalArticle'] = array();
                
                
                for ($i = 0; $i < count($usersBesidesCurrentUserArticle); $i++)
                {
                    if($i != 0)
                    {
                        if($usersBesidesCurrentUserArticle[$i]['UserID'] != $usersBesidesCurrentUserArticle[$i-1]['UserID'])
                        {
                            error_reporting(0);
                            $total = ($sumArticle / (sqrt($sumEvalCurrUserArticle) * sqrt($sumEvalOtherUser)));
                            array_push($data['totalArticle'], $total);
                            $data['totalArticle'] = array_filter(array_map('trim', $data['totalArticle']));
                            
                            $keyArticle = 0;
                            $sumEvalOtherUser = 0;
                            $sumArticle = 0;
                        }
                    }
                    
                    $otherUserSumAndCountEvalArticle = $CI->recommender_m->getAverageEvaluateForUser('ArticleID', 'UserID = ' . $usersBesidesCurrentUserArticle[$i]['UserID'] . ' AND ArticleID IN ('. implode(',', $articlesWhichCurrUserEvaluate) .')');
                    $otherUserAverageEvalArticle = ($otherUserSumAndCountEvalArticle['Sum'] / $otherUserSumAndCountEvalArticle['Count']);
                    
                    if($otherUserSumAndCountEvalArticle['Count'] == count($articlesWhichCurrUserEvaluate))
                    {
                        $u_other_eval = $usersBesidesCurrentUserArticle[$i]['Evaluate'];

                        $sumArticle += ($valuesForCurrUserArticle[$keyArticle] * ($u_other_eval - $otherUserAverageEvalArticle));
                        $sumEvalOtherUser += pow(($u_other_eval - $otherUserAverageEvalArticle), 2);
                        
                        if(($sumArticle / (sqrt($sumEvalCurrUserArticle) * sqrt($sumEvalOtherUser))) > 0.7)
                            array_push($users_ids, $usersBesidesCurrentUserArticle[$i]['UserID']);
                        
                        $keyArticle++;
                    }
                }
                
                $join_evaluation_question = array('users' => 'evaluation.UserID = users.UserID');
                $usersBesidesCurrentUserQuestion = $CI->recommender_m->getSomethingByUser('evaluation', 'users.UserID != ' . $CI->sessionData['UserID'] . ' AND ArticleID IS NULL AND QuestionID IN ('. implode(',', $questionsWhichCurrUserEvaluate) .')', $join_evaluation_question, 'evaluation.UserID');
                
                $sumEvalOtherUserQuestion = 0;
                $keyQuestion = 0;
                $sumQuestion = 0;
                $users_ids_question = array();
                $data['totalQuestion'] = array();
                
                for ($i = 0; $i < count($usersBesidesCurrentUserQuestion); $i++)
                {
                    if($i != 0)
                    {
                        if($usersBesidesCurrentUserQuestion[$i]['UserID'] != $usersBesidesCurrentUserQuestion[$i-1]['UserID'])
                        {
                            error_reporting(0);
                            $total = ($sumQuestion / (sqrt($sumEvalCurrUserQuestion) * sqrt($sumEvalOtherUserQuestion)));
                            array_push($data['totalQuestion'], $total);
                            $data['totalQuestion'] = array_filter(array_map('trim', $data['totalQuestion']));
                            
                            $keyQuestion = 0;
                            $sumEvalOtherUserQuestion = 0;
                            $sumQuestion = 0;
                        }
                    }
                    
                    $otherUserSumAndCountEvalQuestion = $CI->recommender_m->getAverageEvaluateForUser('QuestionID', 'UserID = ' . $usersBesidesCurrentUserQuestion[$i]['UserID'] . ' AND QuestionID IN ('. implode(',', $questionsWhichCurrUserEvaluate) .')');
                    $otherUserAverageEvalQuestion = ($otherUserSumAndCountEvalQuestion['Sum'] / $otherUserSumAndCountEvalQuestion['Count']);
                    
                    if($otherUserSumAndCountEvalQuestion['Count'] == count($questionsWhichCurrUserEvaluate))
                    {
                        $u_other_eval = $usersBesidesCurrentUserQuestion[$i]['Evaluate'];

                        $sumQuestion += ($valuesForCurrUserQuestion[$keyQuestion] * ($u_other_eval - $otherUserAverageEvalQuestion));
                        $sumEvalOtherUserQuestion += pow(($u_other_eval - $otherUserAverageEvalQuestion), 2);
                        
                        if(($sumQuestion / (sqrt($sumEvalCurrUserQuestion) * sqrt($sumEvalOtherUserQuestion))) > 0.7)
                            array_push($users_ids_question, $usersBesidesCurrentUserQuestion[$i]['UserID']);
                        
                        $keyQuestion++;
                    }
                }
                
                $data['userIDArticle'] = array_unique($users_ids);
                $data['userIDArticle'] = array_filter(array_map('trim', $data['userIDArticle']));
                
                $data['userIDQuestion'] = array_unique($users_ids_question);
                $data['userIDQuestion'] = array_filter(array_map('trim', $data['userIDQuestion']));
            }
        }
        else
        {
            $join_top_rated_articles = array('evaluation' => 'evaluation.ArticleID = articles.ArticleID');
            $data['top_rated_articles'] = $CI->recommender_m->getTopRated('articles', 'ArticleID', $join_top_rated_articles);

            $join_top_rated_questions = array('evaluation' => 'evaluation.QuestionID = questions.QuestionID');
            $data['top_rated_questions'] = $CI->recommender_m->getTopRated('questions', 'QuestionID', $join_top_rated_questions);
            
            if(count($data['top_rated_articles']) == 0)
            {
                $join_most_viewed_articles = array('articles' => 'articles.ArticleID = views.ArticleID');
                $data['most_viewed_articles'] = $CI->recommender_m->getMostViewed('articles', 'ArticleID', $join_most_viewed_articles);
            }

            if(count($data['top_rated_questions']) == 0)
            {
                $join_most_viewed_question = array('questions' => 'questions.QuestionID = views.ArticleID');
                $data['most_viewed_questions'] = $CI->recommender_m->getMostViewed('questions', 'QuestionID', $join_most_viewed_question);
            }
            
            $data['top_rated_tags'] = $CI->recommender_m->topRatedTags();
        }
        
        return $data;
    }
}
?>
