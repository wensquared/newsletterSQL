<?php 
require_once './vendor/autoload.php';
use app\lib\Mysql;

try{
  $db = new Mysql(config('database.mysql'));
  $connect = $db->getCon();
}
catch (Exception $e) {
  if (config('app.debug')) {
      echo $e->getMessage();
  }
  else { 
      $error = 'Database-Connection konnte nicht hergestellt werden';
      goto htmldoc;
  }
}

if (isset($_GET['delete'])) {
  $db->delete('newsletter',$_GET['delete']);
}
if(!isset($_POST['email_search'])){
  $sql = 'SELECT count(id) AS total FROM newsletter;';
  $result = $connect->query($sql);
  $total_data = ($result->fetchObject())->total;
}
htmldoc:
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Anmeldungen</title>
    <?php 
        if (!empty($error)) {
            echo '<div class="alert-danger">'.$error.'</div>';
        }
    ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <style>
        #wrapper{
            max-width: 700px;
            margin:30px auto;
        }
        a,a:hover,.page-link,.page-link:hover{
            color:#17a2b8;
        }
    </style>
</head>
<body>
  <div id="wrapper" class="container-fluid">
    <h1>Newsletter</h1>
    <div class="row">
        <div class="col-md-6">
            <a href="newsletterAnmeldung.php" class="btn btn-info btn-sm">Neuer Eintrag</a>
        </div>
        <div class="col-md-6">
            <form class="form-inline" action="" method="POST">
                <div class="input-group mb-3">
                    <input type="text" name="email" class="form-control" placeholder="E-Mail" aria-label="E-Mail">
                    <div class="input-group-append">
                      <input type="hidden" name="email_search" value="search">
                      <button type="submit" class="input-group-text btn btn-info btn-sm">suchen</button>
                    </div>
                  </div>
            </form>
            </div>
        </div>

    <table class="table table-striped">
    <thead>
        <tr>
        <th scope="col">#</th>
        <th scope="col">Anrede</th>
        <th scope="col">Vorname</th>
        <th scope="col">Nachname</th>
        <th scope="col">E-Mail</th>
        <th scope="col"></th>
        <th scope="col"></th>
        </tr>
    </thead>
    <tbody>    
        <?php
        //Paginierung
        if(empty($_POST)){
          $page = $_GET['page'] ?? 1;
          $firstDS = ($page - 1)*10;
          $data = $db->getAll('newsletter',(string) $firstDS.',10');
          foreach ($data as $user) {
            echo '<tr>';
              echo '<th scope="row">'.$user->id.'</th>';
              echo '<td>'.$user->anrede.'</td>';
              echo '<td>'.$user->vorname.'</td>';
              echo '<td>'.$user->nachname.'</td>';
              echo '<td>'.$user->email.'</td>';
              echo '<td><a href="newsletterAendern.php?aendern='.$user->id.'">ändern</a></td>';
              echo '<td><a href="newsletterAusgabe.php?delete='.$user->id.'">löschen</a></td>';
            echo '</tr>';
          }
        } 
        else{
          if(isset($_POST['email_search']) && $_POST['email_search'] == 'search'){
            $tmp_email = $_POST['email'];
            $pattern = "/$tmp_email/i";
            
            $data = $db->getAll('newsletter');
            foreach ($data as $user) {
              $current_user_email = $user->email;
                if (preg_match($pattern,$current_user_email)) {
                  echo '<tr>';
                  echo '<th scope="row">'.$user->id.'</th>';
                  echo '<td>'.$user->anrede.'</td>';
                  echo '<td>'.$user->vorname.'</td>';
                  echo '<td>'.$user->nachname.'</td>';
                  echo '<td>'.$user->email.'</td>';
                  echo '<td><a href="newsletterAendern.php?aendern='.$user->id.'">ändern</a></td>';
                  echo '<td><a href="newsletterAusgabe.php?delete='.$user->id.'">löschen</a></td>';
                  echo '</tr>';
                }
            }
          }
          else {
            echo 'Oh no somthing went wrong with emailsearch';
          }
        }
        ?>
    </tbody>
    </table>
    <nav aria-label="Page navigation example">
        <ul class="pagination">
          <li class="page-item">
            <a class="page-link" href="#" aria-label="Previous">
              <span aria-hidden="true">&laquo;</span>
              <span class="sr-only">Previous</span>
            </a>
          </li>
          <?php
            //Anzahl der Seitenblätter 
            $data_per_page = 10;
            $rest_page = 0;
            $full_page_numbers = (int) ($total_data / $data_per_page);
            if ($total_data % $data_per_page != 0) {
              $rest_page = 1;
            }
            $total_pages = $full_page_numbers + $rest_page;
            for ($i=0; $i < $total_pages; $i++) { 
              echo '<li class="page-item"><a class="page-link" href="newsletterAusgabe.php?page=';
              echo $i+1;
              echo '">';
              echo $i+1;
              echo '</a></li>'; 
            }
          ?>
          <li class="page-item">
            <a class="page-link" href="#" aria-label="Next">
              <span aria-hidden="true">&raquo;</span>
              <span class="sr-only">Next</span>
            </a>
          </li>
        </ul>
      </nav>
  </div>
</body>
</html>