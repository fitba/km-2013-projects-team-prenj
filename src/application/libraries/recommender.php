<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Recommender
{
    var $sessionData;
    public function recommenderSystem($sessionData)
    {
        error_reporting(0);
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
                    $config_views['join'] = array('users' => 'views.UserID = users.UserID');
                    $config_views['wheree'] = "users.UserID = " . $CI->sessionData['UserID'];
                    $data['u_views'] = $CI->recommender_m->getSomethingByUser('views', $config_views);
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
                        $tags = array();
                        $CI->load->model('qawiki_m');
                        for ($i = 0; $i < count($data['u_views']); $i++)
                        {
                            if($data['u_views'][$i]['ArticleID'] != null)
                            {
                                $tagsForArticle = $CI->qawiki_m->getTagsForArticle($data['u_views'][$i]['ArticleID']);
                                foreach ($tagsForArticle as $key => $ta)
                                {
                                    array_push($tags, $ta['TagID']);
                                }
                            }
                            else if($data['u_views'][$i]['QuestionID'] != null)
                            {
                                $tagsForQuestion = $CI->qawiki_m->getTagsForQuestion($data['u_views'][$i]['QuestionID']);
                                foreach ($tagsForQuestion as $key => $tq)
                                {
                                    array_push($tags, $tq['TagID']);
                                }
                            }
                        }
                        $whereArticle = '';
                        foreach($tags as $k => $t)
                        {
                            if($k == 0)
                            {
                                $whereArticle = 'article_tags.TagID = ' . $t;
                            }
                            else
                            {
                                $whereArticle .= ' OR article_tags.TagID = ' . $t;
                            }

                        }
                        if(!empty($whereArticle))
                        {
                            $config_article_tags['distinct'] = 'ok';
                            $config_article_tags['select'] = "articles.ArticleID AS ArticleID, articles.Title AS ArticleTitle";
                            $config_article_tags['wheree'] = $whereArticle;
                            $config_article_tags['join'] = array(
                                'article_tags' => 'article_tags.ArticleID = articles.ArticleID',
                                'tags' => 'tags.TagID = article_tags.TagID');
                            $config_article_tags['table'] = 'articles';
                            $config_article_tags['limit'] = 10;
                            $data['articles_by_tags'] = $CI->recommender_m->getSomethingByTag($config_article_tags); 
                        }

                        $whereQuestion = '';
                        foreach($tags as $k => $t)
                        {
                            if($k == 0)
                            {
                                $whereQuestion = 'question_tags.TagID = ' . $t;
                            }
                            else
                            {
                                $whereQuestion .= ' OR question_tags.TagID = ' . $t;
                            }
                        }

                        if(!empty($whereQuestion))
                        {
                            $config_question_tags['distinct'] = 'ok';
                            $config_question_tags['select'] = "questions.QuestionID AS QuestionID, questions.Title AS QuestionTitle";
                            $config_question_tags['wheree'] = $whereQuestion;
                            $config_question_tags['join'] = array(
                                'question_tags' => 'question_tags.QuestionID = questions.QuestionID',
                                'tags' => 'tags.TagID = question_tags.TagID');
                            $config_question_tags['table'] = 'questions';
                            $config_question_tags['limit'] = 10;
                            $data['questions_by_tags'] = $CI->recommender_m->getSomethingByTag($config_question_tags);
                        }
                        
                        $whereUser = '';
                        foreach($tags as $k => $t)
                        {
                            if($k == 0)
                            {
                                $whereUser = 'follow_tags.TagID = ' . $t;
                            }
                            else
                            {
                                $whereUser .= ' OR follow_tags.TagID = ' . $t;
                            }
                        }

                        if(!empty($whereUser))
                        {
                            $config_users_tags['distinct'] = 'ok';
                            $config_users_tags['select'] = 'users.UserID AS UserID, users.FirstName, users.LastName';
                            $config_users_tags['wheree'] = $whereUser;
                            $config_users_tags['join'] = array(
                                'follow_tags' => 'follow_tags.UserID = users.UserID',
                                'tags' => 'tags.TagID = follow_tags.TagID');
                            $config_users_tags['table'] = 'users';
                            $config_users_tags['limit'] = 10;
                            $data['users_by_tags'] = $CI->recommender_m->getSomethingByTag($config_users_tags);
                        }
                    }
                }
                else 
                {
                    $userSumAndCountEval = $CI->recommender_m->getAverageVotesForUser('UserID = ' . $CI->sessionData['UserID']);
                    
                    $configAllQuestions['distinct'] = 'ok';
                    $configAllQuestions['select'] = 'QuestionID';
                    $configAllQuestions['wheree'] = 'UserID = ' . $CI->sessionData['UserID'];
                    
                    $allQuestions = $CI->recommender_m->getSomethingByUser('votes', $configAllQuestions);
                    
                    $configAllArticles['distinct'] = 'ok';
                    $configAllArticles['select'] = 'ArticleID';
                    $configAllArticles['wheree'] = 'UserID = ' . $CI->sessionData['UserID'];
                    
                    $allArticles = $CI->recommender_m->getSomethingByUser('votes', $configAllArticles);

                    $counter = 0;
                    $CI->load->model('general_m');
                    
                    foreach ($allQuestions as $value) 
                    {
                        if($value['QuestionID'] != null)
                        {
                            $where = 'QuestionID = ' . $value['QuestionID'] . ' AND UserID = ' . $CI->sessionData['UserID'];
                            if($CI->general_m->exists('votes', 'VoteID', $where) > 1)
                            {
                                $counter++;
                            }
                        }
                    }
                    
                    foreach ($allArticles as $value) 
                    {
                        if($value['ArticleID'] != null)
                        {
                            $where = 'ArticleID = ' . $value['ArticleID'] . ' AND UserID = ' . $CI->sessionData['UserID'];
                            if($CI->general_m->exists('votes', 'VoteID', $where) > 1)
                            {
                                $counter++;
                            }
                        }
                    }
                    $userSumAndCountEval['Count'] = $userSumAndCountEval['Count'] - $counter;
                    
                    $userAverageEval = ($userSumAndCountEval['Sum'] / $userSumAndCountEval['Count']);

                    $valuesForCurrUser = array();
                    $whichCurrUserEvaluateA = array();
                    $whichCurrUserEvaluateQ = array();
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
                            array_push($whichCurrUserEvaluateA, $data['u_votes'][$j]['ArticleID']);

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
                            array_push($whichCurrUserEvaluateQ, $data['u_votes'][$j]['QuestionID']);

                            $sumEvalCurrUser += pow(($u_curr_eval - $userAverageEval), 2);
                        }
                    }

                    $config_vote['join'] = array('users' => 'votes.UserID = users.UserID');
                    $config_vote['wheree'] = 'users.UserID != ' . $CI->sessionData['UserID'] . ' AND (QuestionID IN ('. implode(',', $whichCurrUserEvaluateQ) .') OR ArticleID IN ('. implode(',', $whichCurrUserEvaluateA) .'))';
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
                        $otherUserSumAndCountEval = $CI->recommender_m->getAverageVotesForUser('UserID = ' . $usersBesidesCurrentUser[$i]['UserID'] . ' AND (ArticleID IN ('. implode(',', $whichCurrUserEvaluateA) .') OR QuestionID IN ('. implode(',', $whichCurrUserEvaluateQ) .'))');
                        
                        $configAllQuestions['distinct'] = 'ok';
                        $configAllQuestions['select'] = 'QuestionID';
                        $configAllQuestions['wheree'] = 'UserID = ' . $usersBesidesCurrentUser[$i]['UserID'];

                        $allQuestions = $CI->recommender_m->getSomethingByUser('votes', $configAllQuestions);

                        $configAllArticles['distinct'] = 'ok';
                        $configAllArticles['select'] = 'ArticleID';
                        $configAllArticles['wheree'] = 'UserID = ' . $usersBesidesCurrentUser[$i]['UserID'];

                        $allArticles = $CI->recommender_m->getSomethingByUser('votes', $configAllArticles);

                        $counter = 0;
                        $CI->load->model('general_m');

                        foreach ($allQuestions as $value) 
                        {
                            if($value['QuestionID'] != null)
                            {
                                $where = 'QuestionID = ' . $value['QuestionID'] . ' AND UserID = ' . $usersBesidesCurrentUser[$i]['UserID'];
                                if($CI->general_m->exists('votes', 'VoteID', $where) > 1)
                                {
                                    $counter++;
                                }
                            }
                        }

                        foreach ($allArticles as $value) 
                        {
                            if($value['ArticleID'] != null)
                            {
                                $where = 'ArticleID = ' . $value['ArticleID'] . ' AND UserID = ' . $usersBesidesCurrentUser[$i]['UserID'];
                                if($CI->general_m->exists('votes', 'VoteID', $where) > 1)
                                {
                                    $counter++;
                                }
                            }
                        }
                        $otherUserSumAndCountEval['Count'] = $otherUserSumAndCountEval['Count'] - $counter;
                        
                        $otherUserAverageEval = ($otherUserSumAndCountEval['Sum'] / $otherUserSumAndCountEval['Count']);

                        if($otherUserSumAndCountEval['Count'] == (count($whichCurrUserEvaluateA) + count($whichCurrUserEvaluateQ)))
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
                $whichCurrUserEvaluateA = array();
                $whichCurrUserEvaluateQ = array();
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
                        array_push($whichCurrUserEvaluateA, $data['u_evaluation'][$j]['ArticleID']);
                        
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
                        array_push($whichCurrUserEvaluateQ, $data['u_evaluation'][$j]['QuestionID']);
                        
                        $sumEvalCurrUser += pow(($u_curr_eval - $userAverageEval), 2);
                    }
                }

                $config_eval['join'] = array('users' => 'evaluation.UserID = users.UserID');
                $config_eval['wheree'] = 'users.UserID != ' . $CI->sessionData['UserID'] . ' AND (QuestionID IN ('. implode(',', $whichCurrUserEvaluateQ) .') OR ArticleID IN ('. implode(',', $whichCurrUserEvaluateA) .'))';
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
                    
                    $otherUserSumAndCountEval = $CI->recommender_m->getAverageEvaluateForUser('UserID = ' . $usersBesidesCurrentUser[$i]['UserID'] . ' AND (ArticleID IN ('. implode(',', $whichCurrUserEvaluateA) .') OR QuestionID IN ('. implode(',', $whichCurrUserEvaluateQ) .'))');
                    $otherUserAverageEval = $otherUserSumAndCountEval['Sum'] / $otherUserSumAndCountEval['Count'];
                    
                    if($otherUserSumAndCountEval['Count'] == (count($whichCurrUserEvaluateA) + count($whichCurrUserEvaluateQ)))
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
            $ipAddress = $_SERVER['REMOTE_ADDR'];
            $config_v['join'] = array('users' => 'views.UserID = users.UserID');
            $config_v['wheree'] = "views.IPAddress = '".$ipAddress."'";
            $data['u_views'] = $CI->recommender_m->getSomethingByUser('views', $config_v);
            
            if(count($data['u_views']) > 0)
            {
                $tags = array();
                
                $CI->load->model('qawiki_m');
                for ($i = 0; $i < count($data['u_views']); $i++)
                {
                    if($data['u_views'][$i]['ArticleID'] != null)
                    {
                        $tagsForArticle = $CI->qawiki_m->getTagsForArticle($data['u_views'][$i]['ArticleID']);
                        foreach ($tagsForArticle as $key => $ta)
                        {
                            array_push($tags, $ta['TagID']);
                        }
                    }
                    else if($data['u_views'][$i]['QuestionID'] != null)
                    {
                        $tagsForQuestion = $CI->qawiki_m->getTagsForQuestion($data['u_views'][$i]['QuestionID']);
                        foreach ($tagsForQuestion as $key => $tq)
                        {
                            array_push($tags, $tq['TagID']);
                        }
                    }
                }
                $whereArticle = '';
                foreach($tags as $k => $t)
                {
                    if($k == 0)
                    {
                        $whereArticle = 'article_tags.TagID = ' . $t;
                    }
                    else
                    {
                        $whereArticle .= ' OR article_tags.TagID = ' . $t;
                    }
                    
                }
                if(!empty($whereArticle))
                {
                    $config_article_tags['distinct'] = 'ok';
                    $config_article_tags['select'] = "articles.ArticleID AS ArticleID, articles.Title AS ArticleTitle";
                    $config_article_tags['wheree'] = $whereArticle;
                    $config_article_tags['join'] = array(
                        'article_tags' => 'article_tags.ArticleID = articles.ArticleID',
                        'tags' => 'tags.TagID = article_tags.TagID');
                    $config_article_tags['table'] = 'articles';
                    $config_article_tags['limit'] = 10;
                    $data['articles_by_tags'] = $CI->recommender_m->getSomethingByTag($config_article_tags); 
                }
                
                $whereQuestion = '';
                foreach($tags as $k => $t)
                {
                    if($k == 0)
                    {
                        $whereQuestion = 'question_tags.TagID = ' . $t;
                    }
                    else
                    {
                        $whereQuestion .= ' OR question_tags.TagID = ' . $t;
                    }
                }
                
                if(!empty($whereQuestion))
                {
                    $config_question_tags['distinct'] = 'ok';
                    $config_question_tags['select'] = "questions.QuestionID AS QuestionID, questions.Title AS QuestionTitle";
                    $config_question_tags['wheree'] = $whereQuestion;
                    $config_question_tags['join'] = array(
                        'question_tags' => 'question_tags.QuestionID = questions.QuestionID',
                        'tags' => 'tags.TagID = question_tags.TagID');
                    $config_question_tags['table'] = 'questions';
                    $config_question_tags['limit'] = 10;
                    $data['questions_by_tags'] = $CI->recommender_m->getSomethingByTag($config_question_tags);
                }
                
                $whereUser = '';
                foreach($tags as $k => $t)
                {
                    if($k == 0)
                    {
                        $whereUser = 'follow_tags.TagID = ' . $t;
                    }
                    else
                    {
                        $whereUser .= ' OR follow_tags.TagID = ' . $t;
                    }
                }

                if(!empty($whereUser))
                {
                    $config_users_tags['distinct'] = 'ok';
                    $config_users_tags['select'] = 'users.UserID AS UserID, users.FirstName, users.LastName';
                    $config_users_tags['wheree'] = $whereUser;
                    $config_users_tags['join'] = array(
                        'follow_tags' => 'follow_tags.UserID = users.UserID',
                        'tags' => 'tags.TagID = follow_tags.TagID');
                    $config_users_tags['table'] = 'users';
                    $config_users_tags['limit'] = 10;
                    $data['users_by_tags'] = $CI->recommender_m->getSomethingByTag($config_users_tags);
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
        }
        
        return $data;
    }
}
?>
