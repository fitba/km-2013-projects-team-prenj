<?php 
    $data['title'] = 'Pitanja';
    $this->load->view('static/header.php', $data); 
?>
<div class="hero-unit" style="font-size: 16px;">
<h3>Pitanje</h3>
  <table class="table">
        <tbody>
            <tr>
                <td>
                    <div class="votes1">
                        <center>
                            0<br/> votes<br/>
                            0<br/> answers
                        </center>
                        <center>0 views</center>
                    </div>
                    <div class="questions">
                        <p><?php echo $question['Question'] ?></p>
                        <p><?php echo $question['Tags'] ?></p>
                    </div>
                    <div class="textRight">Pitanje postavio/la: <?php echo '<b>' . $question['FirstName'] . ' ' . $question['LastName'] . ' | '. $question['AskDate'] .'</b>'; ?></div>
                </td>
            </tr>
        </tbody>
    </table>
<h5>Komentari</h5>
<table class="table">
    <tbody>
        <tr>
            <td>
                <div class="comments">
                    <div style="float: left">1</div><div style="margin-left: 30px"></div>
                </div>
            </td>
        </tr>
    </tbody>
</table>
</div>
<div class="row-fluid">
  <div class="span12">
      <h3>Odgovori</h3>
      <table class="table">
        <tbody>
            <tr>
                <td>
                    <div class="votes">
                        <center>
                            0<br/> votes<br/>
                            0<br/> answers
                        </center>
                        <center>0 views</center>
                    </div>
                    <div class="questions">
                        <p class="title"><a href="#"><?php echo $question['Title'] ?></a></p>
                        <p><?php echo $question['Question'] ?></p>
                        <p><?php echo $question['Tags'] ?></p>
                    </div>
                    <div class="textRight">Pitanje postavio/la: <?php echo '<b>' . $question['FirstName'] . ' ' . $question['LastName'] . '</b>'; ?></div>
                </td>
            </tr>
        </tbody>
    </table>
      <hr/>
      <form>
          <p><textarea id="editor" name="answer"></textarea></p>
          <p><input type="submit" name="submitAnswer" value="Odgovori" class="btn btn-primary"/></p>
      </form>
  </div>
</div>
<?php 
    $this->load->view('static/footer.php'); 
?>
