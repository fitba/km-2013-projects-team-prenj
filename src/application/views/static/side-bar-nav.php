<div class="span3">
    <div class="well sidebar-nav">
      <ul class="nav nav-list">
        <?php
        $segment3 = $this->uri->segment(3);
        $segment2 = $this->uri->segment(2);
        if($segment3 == 'qa')
        {
        ?>
            <li class="nav-header">Q/A</li>
            <li><a href="#">Link</a></li>
            <li><a href="#">Link</a></li>
            <li><a href="#">Link</a></li>
            <li><a href="#">Link</a></li>
        <?php 
        }
        else if($segment3 == 'wiki')
        {
        ?>
            <li class="nav-header">Wiki</li>
            <li><a href="#">Link</a></li>
            <li><a href="#">Link</a></li>
            <li><a href="#">Link</a></li>
            <li><a href="#">Link</a></li>
            <li><a href="#">Link</a></li>
            <li><a href="#">Link</a></li>
        <?php 
        }
        else if($segment2 == 'profile')
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
                    ?>
                        <img src="<?php echo base_url('pictures/default_female.gif'); ?>" height="202" width="200"/>
                    <?php 
                    }
                }
                else
                {
                    echo '<img src="'.$baseLocation . '/' . $userData['ProfilePicture'] .'" height="202" width="200"/>';
                }
                ?>
                        <hr/>
                        <form action="<?php echo base_url('index.php/main/profile/' . $user_id) ?>" method="post" enctype="multipart/form-data">
                            <input type="file" name="profilePicture" class="btn"/>
                            <input type="submit" name="uploadPicture" value="Upload" class="btn btn-primary"/>
                        </form>
                </center>
            </li>
        <?php
        }
        else 
        {
        ?>
            <li class="nav-header">Q/A</li>
            <li><a href="#">Link</a></li>
            <li><a href="#">Link</a></li>
            <li><a href="#">Link</a></li>
            <li><a href="#">Link</a></li>
            <li class="nav-header">Wiki</li>
            <li><a href="#">Link</a></li>
            <li><a href="#">Link</a></li>
            <li><a href="#">Link</a></li>
            <li><a href="#">Link</a></li>
            <li><a href="#">Link</a></li>
            <li><a href="#">Link</a></li>
        <?php 
        }
        ?>
      </ul>
    </div><!--/.well -->
</div><!--/span-->
