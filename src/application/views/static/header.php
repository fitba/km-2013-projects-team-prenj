<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css"  href="<?php echo base_url('bootstrap/css/bootstrap.css'); ?>"/>
    <link rel="stylesheet" type="text/css"  href="<?php echo base_url('bootstrap/css/bootstrap-responsive.css'); ?>"/>
    <link rel="stylesheet" type="text/css"  href="<?php echo base_url('assets/css/myCssStyle.css'); ?>"/>
    <link rel="stylesheet" type="text/css"  href="<?php echo base_url('assets/css/demo.css'); ?>"/>
    <link rel="stylesheet" type="text/css"  href="<?php echo base_url('assets/css/basic.css'); ?>"/>
    <title>
        <?php 
	if(isset($title))
            echo $title;
	?>
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Knowledge Management System Project">
    <meta name="author" content="Zajim Kujović">

    <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 20px;
      }
      .sidebar-nav {
        padding: 9px 0;
      }
    </style>
    <script type="text/javascript"> 
        var CI_ROOT = '<?php echo base_url(); ?>';
    </script>
    <script type="text/javascript" src="<?php echo base_url("ckeditor/ckeditor.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url("assets/javascript/jquery.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url("assets/javascript/main.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url("bootstrap/js/bootstrap.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url("assets/javascript/jquery.simplemodal.js"); ?>"></script>
    <script type="text/javascript"> 
        var editor, html = '';
        function createEditor() 
        {
            if ( editor ) return;
            var config = {};
            config.entities = true;
            editor = CKEDITOR.replace( 'editor', config );
        }
        /* Skripta za pamćenje pozicije scroll-a. Ovo je mnogo korisno u sledećem slučaju: 
         * Kada pritisnemo dugme submit, a negdje smo na dno stranice, stranica se refrešuje 
         * i scroll se podesi na vrh stranice. Da se to ne bi dešavalo, sa ovom skriptom pamtimo scroll poziciju
         * i stavićemo tu poziciju da nam bude i posle refrešovanaj stranice.
         * */
        function saveScrollPositions(theForm) 
        {
            if(theForm) 
            {
                var scrolly = typeof window.pageYOffset != 'undefined' ? window.pageYOffset : document.documentElement.scrollTop;
                var scrollx = typeof window.pageXOffset != 'undefined' ? window.pageXOffset : document.documentElement.scrollLeft;
                theForm.scrollx.value = scrollx;
                theForm.scrolly.value = scrolly;
            }
        }
        
        var Tooltip =
        {
            Text: "",
            X: 0,
            Y: 0,
            Show: function () {                  
                $('#refreshHover').show();
                $('#refreshHover').css('left', Tooltip.X + 10);
                $('#refreshHover').css('top', Tooltip.Y + 10);
                $('#refreshHover').html(Tooltip.Text);
            },
            Hide: function () {
                $('#refreshHover').hide();
            }
        }
        
        $(document).ready(function (){
            $(".showsTooltip").mousemove(function (e) {
                Tooltip.X = e.pageX;
                Tooltip.Y = e.pageY;
                Tooltip.Show();
            }).mouseleave(function (e) {
                Tooltip.Hide();
            });
        });
    </script>
  </head>
  <body onload="createEditor()">
      <div id="refreshHover"></div>
<?php 
    $this->load->view('static/main-menu.php');
?>
<div class="container-fluid">
    <?php 
        /*$this->uri->uri_string();
        
        error_reporting(0);
        if(!empty($_SESSION['home']))
        {
            $_SESSION['links'] .= $this->uri->uri_string() . '.';
        }
        $_SESSION['home'] = 'main/index';
        
        $links = array_unique(explode('.', $_SESSION['home'] . '.' . $_SESSION['links']));
        
        foreach ($links as $value)
        {
            echo '<a href="'.base_url('index.php/' . $value).'">'.$title.'</a> >';
        }
        echo '<hr style="margin:5px"/>';
         */
    ?>
    
  <div class="row-fluid">
    <?php
        if($this->uri->segment(2) !== 'loginUser' && $this->uri->segment(2) !== 'register'
        && $this->uri->segment(3) !== 'postArticles' && $this->uri->segment(3) !== 'ask')
        {
           $this->load->view('static/side-bar-nav.php');
           echo '<div class="span9">';
        }
        else
        {
            echo '<div class="span12">';
        }
        
    ?>
<?php 
   if(isset($errors))
   {
       echo '<div class="alert alert-error">
               <button type="button" class="close" data-dismiss="alert">&times;</button>
               <h4>Upozorenje!</h4>
               '.$errors.'
             </div>';
   }
   if(isset($isOk))
   {
        echo '<div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <h4>Informacija!</h4>
                '.$isOk.'
              </div>';
   }
   if(isset($unexpectedError))
   {
        echo '<div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <h4>Upozorenje!</h4>
                '.$unexpectedError.'
              </div>';
   }
   
   $phpSelfFile = $_SERVER['PHP_SELF'];

   $link = explode('/', $phpSelfFile);
   $linkForRedirect = '';
   
   if(!isset($_SESSION['nameOfFunction']))
   {
        foreach ($link as $key => $value) 
        {
            if($key != 0 && $key != 1 && $key != 2 && $key != 3)
            {
                if(($key + 1) == count($link))
                {
                    $linkForRedirect .= $value;
                }
                else
                {
                    $linkForRedirect .= $value . '/';
                }
            }
        }
        $_SESSION['redirect'] = $linkForRedirect;
   }
   else
   {
       if($link[count($link) - 1] != $_SESSION['nameOfFunction'])
       {
            foreach ($link as $key => $value) 
             {
                 if($key != 0 && $key != 1 && $key != 2 && $key != 3)
                 {
                     if(($key + 1) == count($link))
                     {
                         $linkForRedirect .= $value;
                     }
                     else
                     {
                         $linkForRedirect .= $value . '/';
                     }
                 }
             }
             $_SESSION['redirect'] = $linkForRedirect;
       }
   }
?>
<!-- modal content -->
<div id="basic-modal-content">
        
</div>