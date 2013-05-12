<div class="span3">
    <div class="well sidebar-nav">
        <?php
        $segment2 = $this->uri->segment(2);
        if($segment2 != 'profile')
        {
            if(($segment2 == 'wiki' || $segment2 == 'qa' || $segment2 == 'index') && !isset($_GET['pretraga']))
            {
                echo '<center><h2 id="preporuka">Tagovi</h2></center>';
            }
            else
            {
                echo '<center><h2 id="preporuka">Preporuka</h2></center>';
            }
        }
        ?>
      <ul class="nav nav-list">
        <?php
        $segment3 = $this->uri->segment(3);
        
        if($segment2 == 'profile')
        {
        ?>
            <li>
                <center>
                <?php
                if($userData['ProfilePicture'] == NULL)
                {
                    if($userData['Sex'] == 'm')
                    {
                    ?>
                        <img src="<?php echo base_url('pictures/default_male.gif'); ?>" height="202" width="200"/>
                    <?php 
                    }
                    ?>
                    <?php 
                    if($userData['Sex'] == 'f')
                    {
                        $locationPicture = base_url('pictures/default_female.gif');
                    ?>
                        <img src="<?php echo base_url('pictures/default_female.gif'); ?>" height="202" width="200"/>
                    <?php 
                    }
                }
                else
                {
                    echo '<img src="'. $userData['ProfilePicture'] .'" height="202" width="200"/>';
                }
                ?>
                        
                    <?php 
                    if($userData['UserID'] === $sessionData['UserID'])
                    {
                    ?>
                        <hr/>
                        <form action="<?php echo base_url('index.php/main/profile/' . $user_id) ?>" method="post" enctype="multipart/form-data">
                            <div>
                                <input type="file" name="profilePicture" class="btn"/>
                                <button type="submit" name="uploadPicture" class="btn btn-primary"><i class="icon-upload icon-white"></i> Upload</button>
                                <button type="submit" name="deletePicture" class="btn btn-primary"><i class="icon-remove icon-white"></i> Obriši</button>
                                </div>
                        </form>
                    <?php 
                    }
                    ?>
                            
                </center>
            </li>
        <?php
        }
        else if(($segment2 == 'wiki' || $segment2 == 'qa' || $segment2 == 'index') && !isset($_GET['pretraga']))
        {
            $iterate = 0;
            for ($i = 0; $i < count($tagsSide); $i++)
            {
                for ($j = 0; $j < 3; $j++)
                {
                    if($iterate != count($tagsSide))
                    {
                        echo '<span class="label"><a style="color:#FFF" href="'.base_url('index.php/search_c/index?pretraga=' . $tagsSide[$iterate]['Name']).'">'.$tagsSide[$iterate]['Name'].'</a></span>' . ' ';
                        $iterate++;
                    }
                }
                $i = $iterate - 1;
            }
        }
        else 
        {
        ?>
            <?php
            if(count($xmlWikiData->Section->Item) > 0)
            {
                echo '<li class="nav-header my-nav-header">Wikipedia</li>';
                for($i = 0; $i < count($xmlWikiData->Section->Item); $i++)
                {
                    echo '<li><a target="_blank" href="'.$xmlWikiData->Section->Item[$i]->Url.'">'. $xmlWikiData->Section->Item[$i]->Text . '</a></li>';
                }
            }
            ?>
            
            <?php
            if(count($jsonStackData->questions) > 0)
            {
                echo '<li class="nav-header my-nav-header">Stack overflow</li>';
                for($i = 0; $i < count($jsonStackData->questions); $i++)
                {
                    echo '<li><a target="_blank" href="http://stackoverflow.com/'.$jsonStackData->questions[$i]->question_timeline_url.'">'. $jsonStackData->questions[$i]->title .'</a></li>';
                }
            }
            ?>
            
            <?php
            if(count($top_rated_questions) > 0)
            {
                echo '<li class="nav-header my-nav-header">Pitanja</li>';
                foreach($top_rated_questions as $question)
                {
            ?>
                <li><a href="<?php echo base_url('index.php/main/question/' . $question['ID']); ?>"><?php echo $question['Title']; ?></a></li>
            <?php
                }
            }
            else if(count($most_viewed_questions) > 0)
            {
                echo '<li class="nav-header my-nav-header">Pitanja</li>';
                foreach($most_viewed_questions as $question)
                {
                ?>
                    <li><a href="<?php echo base_url('index.php/main/question/' . $question['ID']); ?>"><?php echo $question['Title']; ?></a></li>
                <?php
                }
            }
            else if(count($questionIds) > 0)
            {
                echo '<li class="nav-header my-nav-header">Pitanja</li>';
                if(isset($question_id))
                {
                    foreach($questionIds as $id)
                    {
                        $separate = explode('.', $id);
                        if($separate[0] != $question_id)
                        {
                            ?>
                                <li><a href="<?php echo base_url('index.php/main/question/' . $separate[0]); ?>"><?php echo $separate[1]; ?></a></li>
                            <?php
                        }
                    }
                }
                else
                {
                    foreach($questionIds as $id)
                    {
                        $separate = explode('.', $id);
                        ?>
                            <li><a href="<?php echo base_url('index.php/main/question/' . $separate[0]); ?>"><?php echo $separate[1]; ?></a></li>
                        <?php
                    }
                }
            }
            else if(count($questions_by_tags) > 0)
            {
                echo '<li class="nav-header my-nav-header">Pitanja</li>';
                if(isset($question_id))
                {
                    foreach($questions_by_tags as $question)
                    {
                        if($question['QuestionID'] != $question_id)
                        {
                        ?>
                            <li><a href="<?php echo base_url('index.php/main/question/' . $question['QuestionID']); ?>"><?php echo $question['QuestionTitle']; ?></a></li>
                        <?php
                        }
                    }
                }
                else
                {
                    foreach($questions_by_tags as $question)
                    {
                        ?>
                            <li><a href="<?php echo base_url('index.php/main/question/' . $question['QuestionID']); ?>"><?php echo $question['QuestionTitle']; ?></a></li>
                        <?php
                    }
                }
            }
            ?>
            
            <?php
            if(count($top_rated_articles) > 0)
            {
                echo '<li class="nav-header my-nav-header">Članci</li>';
                foreach($top_rated_articles as $article)
                {
            ?>
                <li><a href="<?php echo base_url('index.php/main/article/' . $article['ID']); ?>"><?php echo $article['Title']; ?></a></li>
            <?php
                }
            }
            else if(count($most_viewed_articles) > 0)
            {
                echo '<li class="nav-header my-nav-header">Članci</li>';
                foreach($most_viewed_articles as $article)
                {
                ?>
                    <li><a href="<?php echo base_url('index.php/main/article/' . $article['ID']); ?>"><?php echo $article['Title']; ?></a></li>
                <?php
                }
            }
            else if(count($articleIds) > 0)
            {
                echo '<li class="nav-header my-nav-header">Članci</li>';
                if(isset($article_id))
                {
                    foreach($articleIds as $id)
                    {
                        $separate = explode('.', $id);
                        if($article_id != $separate[0])
                        {
                        ?>
                            <li><a href="<?php echo base_url('index.php/main/article/' . $separate[0]); ?>"><?php echo $separate[1]; ?></a></li>
                        <?php
                        }
                    }
                }
                else
                {
                    foreach($articleIds as $id)
                    {
                        $separate = explode('.', $id);
                    ?>
                        <li><a href="<?php echo base_url('index.php/main/article/' . $separate[0]); ?>"><?php echo $separate[1]; ?></a></li>
                    <?php
                    }
                }
            }
            else if(count($articles_by_tags) > 0)
            {
                echo '<li class="nav-header my-nav-header">Članci</li>';
                if(isset($article_id))
                {
                    foreach($articles_by_tags as $article)
                    {
                        if($article['ArticleID'] != $article_id)
                        {
                    ?>
                        <li><a href="<?php echo base_url('index.php/main/article/' . $article['ArticleID']); ?>"><?php echo $article['ArticleTitle']; ?></a></li>
                    <?php
                        }
                    }
                }
                else
                {
                    foreach($articles_by_tags as $article)
                    {
                     ?>
                        <li><a href="<?php echo base_url('index.php/main/article/' . $article['ArticleID']); ?>"><?php echo $article['ArticleTitle']; ?></a></li>
                    <?php
                    }
                }
            }
            if(count($userID) > 0)
            {
                ?>
                <li class="nav-header my-nav-header">Korisnici</li>
                <?php 
                foreach ($userID as $value)
                {
                    $u = $this->general_m->selectSomeById('*', 'users', 'UserID = ' . $value);
                    if($u != $sessionData['UserID'])
                    echo '<li><a href="'.base_url('index.php/main/profile/' . $u['UserID']).'">'.$u['FirstName'] . ' ' . $u['LastName'] .'</a></li>';
                }
            }
            else if(count($users_by_tags) > 0)
            {
                ?>
                <li class="nav-header my-nav-header">Korisnici</li>
                <?php
                if(isset($sessionData))
                {
                    foreach ($users_by_tags as $value)
                    {
                        $u = $this->general_m->selectSomeById('*', 'users', 'UserID = ' . $value['UserID']);
                        if($u != $sessionData['UserID'])
                            echo '<li><a href="'.base_url('index.php/main/profile/' . $value['UserID']).'">'.$value['FirstName'] . ' ' . $value['LastName'] .'</a></li>';
                    }
                }
                else
                {
                    foreach ($users_by_tags as $value)
                    {
                        echo '<li><a href="'.base_url('index.php/main/profile/' . $value['UserID']).'">'.$value['FirstName'] . ' ' . $value['LastName'] .'</a></li>';
                    }
                }
            }
            ?>
            <?php 
            /*if(isset($total))
            {
                foreach ($total as $value) 
                {
                    if($value >= 0.6)
                        echo '<li><a href="#">'.$value.'</a></li>';
                }
            }*/
            if(count($top_rated_tags) > 0)
            {
                ?>
                <li class="nav-header my-nav-header">Tagovi</li>
                <?php
                foreach($top_rated_tags as $tag)
                {
            ?>
                <li><span class="label"><a style="color:#FFF" href="<?php echo base_url('index.php/search_c/index?pretraga=' . $tag['Name']); ?>"><?php echo $tag['Name']; ?></a></span></li>
            <?php
                }
            }
            ?>
        <?php 
        }
        ?>
      </ul>
    </div><!--/.well -->
</div><!--/span-->
