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
            $config_evaluation['join'] = array('users' => 'evaluation.UserID = users.UserID',
                                               'articles' => 'evaluation.ArticleID = articles.ArticleID',
                                               'questions' => 'evaluation.QuestionID = questions.QuestionID');
            
            $config_evaluation['select'] = '*, articles.Title AS ArticleTitle, questions.Title AS QuestionTitle';
            $config_evaluation['wheree'] = 'users.UserID = ' . $CI->sessionData['UserID'];
            
            $data['u_evaluation'] = $CI->recommender_m->getSomethingByUser('evaluation', $config_evaluation);
            if(count($data['u_evaluation']) == 0)
            {
                $config_votes['join'] = array('users' => 'votes.UserID = users.UserID',
                                              'articles' => 'votes.ArticleID = articles.ArticleID',
                                              'questions' => 'votes.QuestionID = questions.QuestionID');
                
                $config_votes['wheree'] = 'users.UserID = ' . $CI->sessionData['UserID'];
                $config_votes['select'] = '*, articles.Title AS ArticleTitle, questions.Title AS QuestionTitle';
                $data['u_votes'] = $CI->recommender_m->getSomethingByUser('votes', $config_votes);
                if(count($data['u_votes']) == 0)
                {
                    $config_comments['join'] = array('users' => 'comments.UserID = users.UserID');
                    $config_comments['wheree'] = 'users.UserID = ' . $CI->sessionData['UserID'];
                    $data['u_comments'] = $CI->recommender_m->getSomethingByUser('comments', $config_comments);
                    if(count($data['u_comments']) == 0)
                    {
                        $config_views['join'] = array('users' => 'views.UserID = users.UserID');
                        $config_views['wheree'] = 'users.UserID = ' . $CI->sessionData['UserID'];
                        $data['u_views'] = $CI->recommender_m->getSomethingByUser('views', config_views);
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
                    $userSumAndCountEval = $CI->recommender_m->getAverageVotesForUser('UserID = ' . $CI->sessionData['UserID']);

                    $allQuestions = $CI->general_m->getAll('questions', null);
                    $allArticles = $CI->general_m->getAll('articles', null);
                    
                    $counter = 0;
                    $CI->load->model('general_m');
                    
                    foreach ($allQuestions as $value) 
                    {
                        $where = 'QuestionID = ' . $value['QuestionID'] . ' AND UserID = ' . $CI->sessionData['UserID'];
                        if($CI->general_m->exists('votes', 'VoteID', $where) > 1)
                        {
                            $counter++;
                        }
                    }
                    
                    foreach ($allArticles as $value) 
                    {
                        $where = 'ArticleID = ' . $value['ArticleID'] . ' AND UserID = ' . $CI->sessionData['UserID'];
                        if($CI->general_m->exists('votes', 'VoteID', $where) > 1)
                        {
                            $counter++;
                        }
                    }
                    $userSumAndCountEval['Count'] = $userSumAndCountEval['Count'] - $counter;
                    
                    $userAverageEval = ($userSumAndCountEval['Sum'] / $userSumAndCountEval['Count']);

                    $valuesForCurrUser = array();
                    $whichCurrUserEvaluate = array();
                    $sumEvalCurrUser = 0;

                    $data['articleIds'] = array();
                    $data['questionIds'] = array();

                    for ($j = 0; $j < count($data['u_votes']); $j++)
                    {
                        if($data['u_votes'][$j]['ArticleID'] != null)
                        {
                            if($j != 0)
                            {
                                $config_evalsArticle1['wheree'] = 'ArticleID = ' . $data['u_votes'][$j-1]['ArticleID'];
                                $config_evalsArticle1['order_by'] = 'ArticleID';
                                
                                $config_evalsArticle2['wheree'] = 'ArticleID = ' . $data['u_votes'][$j]['ArticleID'];
                                $config_evalsArticle2['order_by'] = 'ArticleID';
                                
                                $evalsArticle1 = $CI->recommender_m->getSomethingByUser('votes', $config_evalsArticle1);
                                $evalsArticle2 = $CI->recommender_m->getSomethingByUser('votes', $config_evalsArticle2);

                                if(count($evalsArticle1) == count($evalsArticle2))
                                {
                                    $sumOfArticleItemsAbove = 0;
                                    $sumOfArticlesBottom1 = 0;
                                    $sumOfArticlesBottom2 = 0;
                                    for($x = 0; $x < count($evalsArticle1); $x++)
                                    {
                                        $sumOfArticleItemsAbove += $evalsArticle1[$x]['Positive'] * $evalsArticle2[$x]['Positive'];
                                        $sumOfArticlesBottom1 += pow($evalsArticle1[$x]['Positive'], 2);
                                        $sumOfArticlesBottom2 += pow($evalsArticle2[$x]['Positive'], 2);
                                    }
                                    $sqrtMultiplication = sqrt($sumOfArticlesBottom1) * sqrt($sumOfArticlesBottom2);
                                    if($sumOfArticleItemsAbove == 0)
                                    {
                                        $sqrtMultiplication = 1;
                                    }
                                    $sumTotal = $sumOfArticleItemsAbove / $sqrtMultiplication;
                                    
                                    if($sumTotal >= 0.5)
                                    {
                                        array_push($data['articleIds'], $data['u_votes'][$j]['ArticleID'] . '.' . $data['u_votes'][$j]['ArticleTitle']);
                                    }
                                }
                            }

                            $u_curr_eval = $data['u_votes'][$j]['Positive'];
                            array_push($valuesForCurrUser, ($u_curr_eval - $userAverageEval));
                            array_push($whichCurrUserEvaluate, $data['u_votes'][$j]['ArticleID']);

                            $sumEvalCurrUser += pow(($u_curr_eval - $userAverageEval), 2);
                        }
                        else if($data['u_votes'][$j]['QuestionID'] != null)
                        {
                            if($j != 0)
                            {
                                $config_evalsQuestion1['wheree'] = 'QuestionID = ' . $data['u_votes'][$j-1]['QuestionID'];
                                $config_evalsQuestion1['order_by'] = 'QuestionID';
                                
                                $config_evalsQuestion2['wheree'] = 'QuestionID = ' . $data['u_votes'][$j]['QuestionID'];
                                $config_evalsQuestion2['order_by'] = 'QuestionID';
                                
                                $evalsQuestion1 = $CI->recommender_m->getSomethingByUser('votes', $config_evalsQuestion1);
                                $evalsQuestion2 = $CI->recommender_m->getSomethingByUser('votes', $config_evalsQuestion2);

                                if(count($evalsQuestion1) == count($evalsQuestion2))
                                {
                                    $sumOfQuestionsItemsAbove = 0;
                                    $sumOfQuestionsBottom1 = 0;
                                    $sumOfQuestionsBottom2 = 0;
                                    for($x = 0; $x < count($evalsQuestion1); $x++)
                                    {
                                        $sumOfQuestionsItemsAbove += $evalsQuestion1[$x]['Positive'] * $evalsQuestion2[$x]['Positive'];
                                        $sumOfQuestionsBottom1 += pow($evalsQuestion1[$x]['Positive'], 2);
                                        $sumOfQuestionsBottom2 += pow($evalsQuestion2[$x]['Positive'], 2);
                                    }
                                    error_reporting(0);
                                    $sqrtMultiplication = sqrt($sumOfQuestionsBottom1) * sqrt($sumOfQuestionsBottom2);
                                    if($sumOfQuestionsItemsAbove == 0)
                                    {
                                        $sqrtMultiplication = 1;
                                    }
                                    $sumTotal = $sumOfQuestionsItemsAbove / $sqrtMultiplication;

                                    if($sumTotal >= 0.5)
                                    {
                                        array_push($data['questionIds'], $data['u_votes'][$j]['QuestionID'] . '.' . $data['u_votes'][$j]['QuestionTitle']);
                                    }
                                }
                            }

                            $u_curr_eval = $data['u_votes'][$j]['Positive'];
                            array_push($valuesForCurrUser, ($u_curr_eval - $userAverageEval));
                            array_push($whichCurrUserEvaluate, $data['u_votes'][$j]['QuestionID']);

                            $sumEvalCurrUser += pow(($u_curr_eval - $userAverageEval), 2);
                        }
                    }

                    $config_vote['join'] = array('users' => 'votes.UserID = users.UserID');
                    $config_vote['wheree'] = 'users.UserID != ' . $CI->sessionData['UserID'] . ' AND (QuestionID IN ('. implode(',', $whichCurrUserEvaluate) .') OR ArticleID IN ('. implode(',', $whichCurrUserEvaluate) .'))';
                    $config_vote['order_by'] = 'votes.UserID';

                    $usersBesidesCurrentUser = $CI->recommender_m->getSomethingByUser('votes', $config_vote);

                    $sumEvalOtherUser = 0;
                    $key = 0;
                    $sum = 0;
                    $users_ids = array();
                    $data['total'] = array();

                    for ($i = 0; $i < count($usersBesidesCurrentUser); $i++)
                    {
                        if($i != 0)
                        {
                            if($usersBesidesCurrentUser[$i]['UserID'] != $usersBesidesCurrentUser[$i-1]['UserID'])
                            {
                                error_reporting(0);
                                $sqrtMultiplication = sqrt($sumEvalCurrUser) * sqrt($sumEvalOtherUser);
                                if($sum == 0)
                                {
                                    $sqrtMultiplication = 1;
                                }
                                
                                $total = $sum / $sqrtMultiplication;
                                array_push($data['total'], $total);
                                $data['total'] = array_filter(array_map('trim', $data['total']));

                                $key = 0;
                                $sumEvalOtherUser = 0;
                                $sum = 0;
                            }
                        }
                        error_reporting(0);
                        $otherUserSumAndCountEval = $CI->recommender_m->getAverageVotesForUser('UserID = ' . $usersBesidesCurrentUser[$i]['UserID'] . ' AND (ArticleID IN ('. implode(',', $whichCurrUserEvaluate) .') OR QuestionID IN ('. implode(',', $whichCurrUserEvaluate) .'))');
                        
                        $allQuestions = $CI->general_m->getAll('questions', null);
                        $allArticles = $CI->general_m->getAll('articles', null);

                        $counter = 0;
                        $CI->load->model('general_m');

                        foreach ($allQuestions as $value) 
                        {
                            $where = 'QuestionID = ' . $value['QuestionID'] . ' AND UserID = ' . $usersBesidesCurrentUser[$i]['UserID'];
                            if($CI->general_m->exists('votes', 'VoteID', $where) > 1)
                            {
                                $counter++;
                            }
                        }

                        foreach ($allArticles as $value) 
                        {
                            $where = 'ArticleID = ' . $value['ArticleID'] . ' AND UserID = ' . $usersBesidesCurrentUser[$i]['UserID'];
                            if($CI->general_m->exists('votes', 'VoteID', $where) > 1)
                            {
                                $counter++;
                            }
                        }
                        $otherUserSumAndCountEval['Count'] = $otherUserSumAndCountEval['Count'] - $counter;
                        
                        $otherUserAverageEval = ($otherUserSumAndCountEval['Sum'] / $otherUserSumAndCountEval['Count']);

                        if($otherUserSumAndCountEval['Count'] == count($whichCurrUserEvaluate))
                        {
                            $u_other_eval = $usersBesidesCurrentUser[$i]['Positive'];

                            $sum += $valuesForCurrUser[$key] * ($u_other_eval - $otherUserAverageEval);
                            $sumEvalOtherUser += pow(($u_other_eval - $otherUserAverageEval), 2);
                            
                            $sqrtMultiplication = sqrt($sumEvalCurrUser) * sqrt($sumEvalOtherUser);
                            if($sum == 0)
                            {
                                $sqrtMultiplication = 1;
                            }
                            if(($sum / $sqrtMultiplication) >= 0.5)
                            {
                                array_push($users_ids, $usersBesidesCurrentUser[$i]['UserID']);
                            }

                            $key++;
                        }
                    }

                    $data['userID'] = array_unique($users_ids);
                    $data['userID'] = array_filter(array_map('trim', $data['userID']));
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
                            $config_evalsArticle1['wheree'] = 'ArticleID = ' . $data['u_evaluation'][$j-1]['ArticleID'];
                            $config_evalsArticle1['order_by'] = 'ArticleID';

                            $config_evalsArticle2['wheree'] = 'ArticleID = ' . $data['u_evaluation'][$j]['ArticleID'];
                            $config_evalsArticle2['order_by'] = 'ArticleID';
                            
                            $evalsArticle1 = $CI->recommender_m->getSomethingByUser('evaluation', $config_evalsArticle1);
                            $evalsArticle2 = $CI->recommender_m->getSomethingByUser('evaluation', $config_evalsArticle2);
                            
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
                                
                                $sqrtMultiplication = sqrt($sumOfArticlesBottom1) * sqrt($sumOfArticlesBottom2);
                                
                                if($sumOfArticleItemsAbove == 0)
                                {
                                    $sqrtMultiplication = 1;
                                }
                                $sumTotal = $sumOfArticleItemsAbove / $sqrtMultiplication;
                                
                                if($sumTotal >= 0.5)
                                {
                                    array_push($data['articleIds'], $data['u_evaluation'][$j]['ArticleID'] . '.' . $data['u_evaluation'][$j]['ArticleTitle']);
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
                            $config_evalsQuestion1['wheree'] = 'QuestionID = ' . $data['u_evaluation'][$j-1]['QuestionID'];
                            $config_evalsQuestion1['order_by'] = 'QuestionID';

                            $config_evalsQuestion2['wheree'] = 'QuestionID = ' . $data['u_evaluation'][$j]['QuestionID'];
                            $config_evalsQuestion2['order_by'] = 'QuestionID';
                            
                            $evalsQuestion1 = $CI->recommender_m->getSomethingByUser('evaluation', $config_evalsQuestion1);
                            $evalsQuestion2 = $CI->recommender_m->getSomethingByUser('evaluation', $config_evalsQuestion2);
                            
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
                                
                                $sqrtMultiplication = sqrt($sumOfQuestionsBottom1) * sqrt($sumOfQuestionsBottom2);
                                if($sumOfQuestionsItemsAbove == 0)
                                {
                                    $sqrtMultiplication = 1;
                                }
                                $sumTotal = $sumOfQuestionsItemsAbove / $sqrtMultiplication;
                                
                                if($sumTotal >= 0.5)
                                {
                                    array_push($data['questionIds'], $data['u_evaluation'][$j]['QuestionID'] . '.' . $data['u_evaluation'][$j]['QuestionTitle']);
                                }
                            }
                        }
                        
                        $u_curr_eval = $data['u_evaluation'][$j]['Evaluate'];
                        array_push($valuesForCurrUser, ($u_curr_eval - $userAverageEval));
                        array_push($whichCurrUserEvaluate, $data['u_evaluation'][$j]['QuestionID']);
                        
                        $sumEvalCurrUser += pow(($u_curr_eval - $userAverageEval), 2);
                    }
                }

                $config_eval['join'] = array('users' => 'evaluation.UserID = users.UserID');
                $config_eval['wheree'] = 'users.UserID != ' . $CI->sessionData['UserID'] . ' AND (QuestionID IN ('. implode(',', $whichCurrUserEvaluate) .') OR ArticleID IN ('. implode(',', $whichCurrUserEvaluate) .'))';
                $config_eval['order_by'] = 'evaluation.UserID';
                
                $usersBesidesCurrentUser = $CI->recommender_m->getSomethingByUser('evaluation', $config_eval);
                
                $sumEvalOtherUser = 0;
                $key = 0;
                $sum = 0;
                $users_ids = array();
                $data['total'] = array();
 
                for ($i = 0; $i < count($usersBesidesCurrentUser); $i++)
                {
                    if($i != 0)
                    {
                        if($usersBesidesCurrentUser[$i]['UserID'] != $usersBesidesCurrentUser[$i-1]['UserID'])
                        {
                            error_reporting(0);
                            $sqrtMultiplication = sqrt($sumEvalCurrUser) * sqrt($sumEvalOtherUser);
                            
                            if($sum == 0)
                            {
                                $sqrtMultiplication = 1;
                            }
                            $total = $sum / $sqrtMultiplication;
                            array_push($data['total'], $total);
                            $data['total'] = array_filter(array_map('trim', $data['total']));
                            
                            $key = 0;
                            $sumEvalOtherUser = 0;
                            $sum = 0;
                        }
                    }
                    
                    $otherUserSumAndCountEval = $CI->recommender_m->getAverageEvaluateForUser('UserID = ' . $usersBesidesCurrentUser[$i]['UserID'] . ' AND (ArticleID IN ('. implode(',', $whichCurrUserEvaluate) .') OR QuestionID IN ('. implode(',', $whichCurrUserEvaluate) .'))');
                    $otherUserAverageEval = $otherUserSumAndCountEval['Sum'] / $otherUserSumAndCountEval['Count'];
                    
                    if($otherUserSumAndCountEval['Count'] == count($whichCurrUserEvaluate))
                    {
                        $u_other_eval = $usersBesidesCurrentUser[$i]['Evaluate'];
                        
                        $sum += $valuesForCurrUser[$key] * ($u_other_eval - $otherUserAverageEval);
                        $sumEvalOtherUser += pow(($u_other_eval - $otherUserAverageEval), 2);
                        
                        $sqrtMultiplication = sqrt($sumEvalCurrUser) * sqrt($sumEvalOtherUser);
                        if($sum == 0)
                        {
                            $sqrtMultiplication = 1;
                        }
                        if(($sum / $sqrtMultiplication) > 0)
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
