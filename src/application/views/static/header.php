<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css"  href="<?php echo base_url('bootstrap/css/bootstrap.css'); ?>"/>
    <link rel="stylesheet" type="text/css"  href="<?php echo base_url('bootstrap/css/bootstrap-responsive.css'); ?>"/>
    <link rel="stylesheet" type="text/css"  href="<?php echo base_url('assets/css/myCssStyle.css'); ?>"/>
    
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
        var editor, html = '';
        function createEditor() 
        {
            if ( editor ) return;
            var config = {};
            config.entities = true;
            editor = CKEDITOR.replace( 'editor', config );
        }
    </script>
    
  </head>
  <body>
<?php 
    $this->load->view('static/main-menu.php');
?>
<div class="container-fluid">
  <div class="row-fluid">
    <?php 
        $this->load->view('static/side-bar-nav.php');
    ?>
        <div class="span9">