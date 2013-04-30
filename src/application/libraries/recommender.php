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
            $join_evaluation = array('users' => 'evaluation.UserID = users.UserID',
                                     'articles' => 'evaluation.ArticleID = articles.ArticleID');
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
                $userSumAndCountEval = $CI->recommender_m->getAverageEvaluateForUser('UserID = ' . $CI->sessionData['UserID']);
                $userAverageEval = ($userSumAndCountEval['Sum'] / $userSumAndCountEval['Count']);
                
                $valuesForCurrUser = array();
                $whichCurrUserEvaluate = array();
                $sumEvalCurrUser = 0;
                
                $data['articleIds'] = array();
                $data['questionIds'] = array();
                
                for ($j = 0; $j < count($data['u_evaluation']); $j++)
                {
                    if($data['u_evaluation'][$j]['ArticleID'] != null)
                    {
                        if($j != 0)
                        {
                            $evalsArticle1 = $CI->recommender_m->getSomethingByUser('evaluation', 'ArticleID = ' . $data['u_evaluation'][$j-1]['ArticleID'], null, 'ArticleID');
                            $evalsArticle2 = $CI->recommender_m->getSomethingByUser('evaluation', 'ArticleID = ' . $data['u_evaluation'][$j]['ArticleID'], null, 'ArticleID');
                            
                            if(count($evalsArticle1) == count($evalsArticle2))
                            {
                                $sumOfArticleItemsAbove = 0;
                                $sumOfArticlesBottom1 = 0;
                                $sumOfArticlesBottom2 = 0;
                                for($x = 0; $x < count($evalsArticle1); $x++)
                                {
                                    $sumOfArticleItemsAbove += $evalsArticle1[$x]['Evaluate'] * $evalsArticle2[$x]['Evaluate'];
                                    $sumOfArticlesBottom1 += pow($evalsArticle1[$x]['Evaluate'], 2);
                                    $sumOfArticlesBottom2 += pow($evalsArticle2[$x]['Evaluate'], 2);
                                }
                                $sumTotal = $sumOfArticleItemsAbove / (sqrt($sumOfArticlesBottom1) * sqrt($sumOfArticlesBottom2));
                                
                                if($sumTotal >= 0.6)
                                {
                                    array_push($data['articleIds'], $data['u_evaluation'][$j]['ArticleID'] . '.' . $data['u_evaluation'][$j]['Title']);
                                }
                            }
                        }
                        
                        $u_curr_eval = $data['u_evaluation'][$j]['Evaluate'];
                        array_push($valuesForCurrUser, ($u_curr_eval - $userAverageEval));
                        array_push($whichCurrUserEvaluate, $data['u_evaluation'][$j]['ArticleID']);
                        
                        $sumEvalCurrUser += pow(($u_curr_eval - $userAverageEval), 2);
                    }
                    else if($data['u_evaluation'][$j]['QuestionID'] != null)
                    {
                        if($j != 0)
                        {
                            $evalsQuestion1 = $CI->recommender_m->getSomethingByUser('evaluation', 'QuestionID = ' . $data['u_evaluation'][$j-1]['QuestionID'], null, 'QuestionID');
                            $evalsQuestion2 = $CI->recommender_m->getSomethingByUser('evaluation', 'QuestionID = ' . $data['u_evaluation'][$j]['QuestionID'], null, 'QuestionID');
                            
                            if(count($evalsQuestion1) == count($evalsQuestion2))
                            {
                                $sumOfQuestionsItemsAbove = 0;
                                $sumOfQuestionsBottom1 = 0;
                                $sumOfQuestionsBottom2 = 0;
                                for($x = 0; $x < count($evalsQuestion1); $x++)
                                {
                                    $sumOfQuestionsItemsAbove += $evalsQuestion1[$x]['Evaluate'] * $evalsQuestion2[$x]['Evaluate'];
                                    $sumOfQuestionsBottom1 += pow($evalsQuestion1[$x]['Evaluate'], 2);
                                    $sumOfQuestionsBottom2 += pow($evalsQuestion2[$x]['Evaluate'], 2);
                                }
                                $sumTotal = $sumOfQuestionsItemsAbove / (sqrt($sumOfQuestionsBottom1) * sqrt($sumOfQuestionsBottom2));
                                
                                if($sumTotal >= 0.6)
                                {
                                    array_push($data['questionIds'], $data['u_evaluation'][$j]['QuestionID'] . '.' . $data['u_evaluation'][$j]['Title']);
                                }
                            }
                        }
                        
                        $u_curr_eval = $data['u_evaluation'][$j]['Evaluate'];
                        array_push($valuesForCurrUser, ($u_curr_eval - $userAverageEval));
                        array_push($whichCurrUserEvaluate, $data['u_evaluation'][$j]['QuestionID']);
                        
                        $sumEvalCurrUser += pow(($u_curr_eval - $userAverageEval), 2);
                    }
                }
                
                $join_evaluation = array('users' => 'evaluation.UserID = users.UserID');
                
                $usersBesidesCurrentUser = $CI->recommender_m->getSomethingByUser('evaluation', 'users.UserID != ' . $CI->sessionData['UserID'] . ' AND (QuestionID IN ('. implode(',', $whichCurrUserEvaluate) .') OR ArticleID IN ('. implode(',', $whichCurrUserEvaluate) .'))', $join_evaluation, 'evaluation.UserID');
                
                $sumEvalOtherUser = 0;
                $key = 0;
                $sum = 0;
                $users_ids = array();
                $data['total'] = array();
 
                for ($i = 0; $i < count($usersBesidesCurrentUser); $i++)
                {
                    /*$testiranje = $usersBesidesCurrentUser[$i]['EvaluationID'];
                    if($usersBesidesCurrentUser[$i]['ArticleID'] != null)
                    {
                        $countOfArticleEvals = $CI->recommender_m->getAverageEvaluateForUser('UserID = ' . $usersBesidesCurrentUser[$i]['UserID'] . ' AND ArticleID IS NOT NULL');
                        if(count($evalsForArticles) == $countOfArticleEvals['Count'])
                        {
                            for($k = 0; $k < count($evalsForArticles); $k++)
                            {
                                $test1 = $evalsForArticles[$k];
                                $test2 = $usersBesidesCurrentUser[$k]['Evaluate'];
                                $sumOfArticleItems += $evalsForArticles[$k] * $usersBesidesCurrentUser[$k]['Evaluate'];
                            }
                        }
                    }
                    else if($usersBesidesCurrentUser[$i]['QuestionID'] != null)
                    {
                        $countOfQuestionEvals = $CI->recommender_m->getAverageEvaluateForUser('UserID = ' . $usersBesidesCurrentUser[$i]['UserID'] . ' AND QuestionID IS NOT NULL');
                        if(count($evalsForQuestions) == $countOfQuestionEvals['Count'])
                        {
                            for($k = 0; $k < count($evalsForQuestions); $k++)
                            {
                                $test1 = $evalsForQuestions[$k];
                                $test2 = $usersBesidesCurrentUser[$k]['Evaluate'];
                                $sumOfQuestionItems += $evalsForQuestions[$k] * $usersBesidesCurrentUser[$k]['Evaluate'];
                            }
                        }
                    }*/
                    
                    if($i != 0)
                    {
                        if($usersBesidesCurrentUser[$i]['UserID'] != $usersBesidesCurrentUser[$i-1]['UserID'])
                        {
                            error_reporting(0);
                            $total = $sum / (sqrt($sumEvalCurrUser) * sqrt($sumEvalOtherUser));
                            array_push($data['total'], $total);
                            $data['total'] = array_filter(array_map('trim', $data['total']));
                            
                            $key = 0;
                            $sumEvalOtherUser = 0;
                            $sum = 0;
                        }
                    }
                    
                    $otherUserSumAndCountEval = $CI->recommender_m->getAverageEvaluateForUser('UserID = ' . $usersBesidesCurrentUser[$i]['UserID'] . ' AND (ArticleID IN ('. implode(',', $whichCurrUserEvaluate) .') OR QuestionID IN ('. implode(',', $whichCurrUserEvaluate) .'))');
                    $otherUserAverageEval = ($otherUserSumAndCountEval['Sum'] / $otherUserSumAndCountEval['Count']);
                    
                    if($otherUserSumAndCountEval['Count'] == count($whichCurrUserEvaluate))
                    {
                        $u_other_eval = $usersBesidesCurrentUser[$i]['Evaluate'];

                        $sum += $valuesForCurrUser[$key] * ($u_other_eval - $otherUserAverageEval);
                        $sumEvalOtherUser += pow(($u_other_eval - $otherUserAverageEval), 2);
                        
                        if($sum / (sqrt($sumEvalCurrUser) * sqrt($sumEvalOtherUser)) >= 0.6)
                            array_push($users_ids, $usersBesidesCurrentUser[$i]['UserID']);
                        
                        $key++;
                    }
                }
                
                $data['userID'] = array_unique($users_ids);
                $data['userID'] = array_filter(array_map('trim', $data['userID']));
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
