<?php 
    $data['title'] = 'Profile';
    $this->load->view('static/header.php', $data);
?>
<div class="row-fluid">
<h3>
    <?php echo $userData['FirstName'] . ' ' . $userData['LastName']; ?>
    <?php 
    if($user_id === $sessionData['UserID']) 
    {
    ?>
    <a style="float: right; font-size: 13px;"  href="<?php if($user_id === $sessionData['UserID']) echo base_url('index.php/main/profile/' . $userData['UserID'] . '?editUser=true'); ?>">[promijeni]</a>
    <?php 
    }
    ?>
</h3>
<hr/>
<?php
    if(!isset($_GET['editUser']))
    {
?>
        
        <div class="span5">
            <p><b>Email:</b> <?php echo $userData['Email']; ?></p>
            <p><b>Pravo ime:</b> <?php echo $userData['FirstName'] . ' ' . $userData['LastName']; ?></p>
            <p><b>Korisničko ime:</b> <?php echo $userData['Username']; ?></p>
            <p><b>Datum registracije:</b> <?php echo $this->formatdate->getFormatDate($userData['RegistrationDate']); ?></p>
        </div>
        <div class="span6">
            <?php
            if(isset($userData['Location']))
            {
                echo '<p><b>Lokacija:</b> '.$userData['Location'].'</p>';
            }  
            if(isset($userData['WebSite']))
            {
                echo '<p><b>Web stranica:</b> '.$userData['WebSite'].'</p>';
            }
            if(isset($userData['DateOfBirth']))
            {
                $dateOfBirth = str_replace('00:00:00', '', $this->formatdate->getFormatDate($userData['DateOfBirth']));
                echo '<p><b>Datum rođenja:</b> '.$dateOfBirth.'</p>';
            }
            if(isset($userData['AboutSelf']))
            {
                echo '<b>O sebi:</b> '.html_entity_decode($userData['AboutSelf']);
            }
            ?>
        </div>
<?php 
    }
    else
    {
        if($_GET['editUser'] === 'true')
        {
?>
<form action="<?php echo base_url('index.php/main/profile/' . $userData['UserID']); ?>" method="POST">
    <div class="span4">
        <p><label>Email: *</label> <input type="text" name="email" value="<?php echo $userData['Email']; ?>"/></p>
        <p><label>Ime: *</label> <input type="text" name="firstName" value="<?php echo $userData['FirstName']; ?>"/></p>
        <p><label>Prezime: *</label> <input type="text" name="lastName" value="<?php echo $userData['LastName']; ?>"/></p>
        <p><label>Korisničko ime: *</label> <input type="text" name="username" value="<?php echo $userData['Username']; ?>"/></p>
    </div>
    <div class="span7">
        <p><label>Lokacija:</label> <input type="text" name="location" value="<?php echo $userData['Location']; ?>"/></p>
        <p><label>Web stranica:</label> <input type="text" name="website" value="<?php echo $userData['WebSite']; ?>"/></p>
        <p><label>Datum rođenja:</label> <input type="text" name="dateOfBirth" value="<?php echo $userData['DateOfBirth']; ?>" placeholder="YYYY-MM-DD"/></p>
        <p><label>O sebi:</label> <textarea id="editor" name="aboutSelf"><?php echo $userData['AboutSelf']; ?></textarea></p>
        <p><input type="submit" name="submitEditUser" value="Snimi promjene" class="btn btn-primary pull-right"/></p>
    </div>
</form>
<?php
        }
    }
?>
</div>
<hr/>
<div class="row-fluid">
    <div class="span12">
        <div class="span5" style="margin-left: 25px;">
            <?php 
            if(isset($questions))
            {
                echo '<b>('.count($questions).') Pitanja</b><hr style="margin:0;"/>';
                foreach ($questions as $question) 
                {
                    echo '<a href="'.base_url('index.php/main/question/' . $question['QuestionID']).'">'.$question['Title'].'</a><br/>';
                }
            }
            ?>

        </div>
        <div class="span5">
            <?php 
            if(isset($answers))
            {
                echo '<b>('.count($answers).') Odgovori</b><hr style="margin:0;"/>';
                foreach ($answers as $answer) 
                {
                    if(strlen($answer['Answer']) > 100)
                    {
                        echo '<a href="'.base_url('index.php/main/question/' . $answer['QuestionID'] . '#ans' . $answer['AnswerID']).'">'.substr(html_entity_decode($answer['Answer']), 0, 100) . '...</a><br/>';
                    }
                    else
                    {
                        echo '<a href="'.base_url('index.php/main/question/' . $answer['QuestionID'] . '#ans' . $answer['AnswerID']).'">'.html_entity_decode($answer['Answer']).'</a><br/>';
                    }
                }
            }
            ?>
        </div>
    </div>
    <div class="span12">
        <div class="span5">
            <?php 
            if(isset($articles))
            {
                echo '<b>('.count($articles).') Članci</b><hr style="margin:0;"/>';
                foreach ($articles as $article) 
                {
                    echo '<a href="'.base_url('index.php/main/article/' . $article['ArticleID']).'">'.$article['Title'].'</a><br/>';
                }
            }
            ?>
        </div>
        <div class="span5">
            <?php 
            if(isset($tags))
            {
                echo '<b>('.count($tags).') Tagovi</b><hr style="margin:0;"/>';
                foreach ($tags as $tag) 
                {
                    echo '<span class="label"><a style="color:#FFF" href="'.base_url('index.php/qawiki_c/tags/' . $tag['TagID']).'">'.$tag['Name'].'</a></span>' . ' ';
                }
            }
            ?>
        </div>
    </div>
</div>
<?php 
    $this->load->view('static/footer.php');
?>