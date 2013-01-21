<div class="span3">
    <div class="well sidebar-nav">
      <ul class="nav nav-list">
        <?php
        if(isset($key))
        {
            if($key == 'qa')
            {
            ?>
            <li class="nav-header">Q/A</li>
            <li><a href="#">Link</a></li>
            <li><a href="#">Link</a></li>
            <li><a href="#">Link</a></li>
            <li><a href="#">Link</a></li>
            <?php 
            }
            else if($key == 'wiki')
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
