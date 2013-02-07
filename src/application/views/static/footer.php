            </div><!--/span-->
        </div><!--/row-->
        <hr>
     <footer>
       <p>&copy; Knowledge Management System Project 2013</p>
     </footer>
   </div><!--/.fluid-container-->
   <?php
    $scrollx = 0;
    $scrolly = 0;
    if(!empty($_REQUEST['scrollx'])) 
    {
        $scrollx = $_REQUEST['scrollx'];
    }
    if(!empty($_REQUEST['scrolly'])) 
    {
        $scrolly = $_REQUEST['scrolly'];
    }
    ?>
    <script type="text/javascript">
        window.scrollTo(<?php echo "$scrollx" ?>, <?php echo "$scrolly" ?>);
    </script>
</body>
</html>
