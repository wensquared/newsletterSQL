<?php
    
    /*
        Erstelle eine Datenbank Tabelle in der die Newsletter Anmeldungen gespeichert werden. Ausserdem sollen alle Newsletter Anmeldungen angezeigt werden. Es soll auch die Möglichkeit geschaffen werden, die Newsletter Anmeldungen zu ändern und aus der DB Tabelle zu löschen.
        Die E-Mail Adresse soll in der DB Tabelle eindeutig sein.

        Das Formular sollte inhaltlich wie folgt überprüft werden: 
        Anrede: Pflichtfeld
        Vorname: Pflichtfeld, mindestens 2 Zeichen, maximal 50 Zeichen
        Nachname: Pflichtfeld, mindestens 2 Zeichen, maximal 50 Zeichen
        E-Mail: Pflichtfeld, E-Mail
        Datenschutz: Pflichtfeld

        Optional könnte eine Paginierung und eine Suche nach E-Mail Adressen erstellt werden.
    */

require_once './vendor/autoload.php';
require_once './app/lib/Session.php';
use app\lib\Mysql;
$sess = app\lib\Session::init();

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

if (isset($_GET['aendern'])) {
    $id = (int) $_GET['aendern'];
    $data = $db->find('newsletter',$id);
    if (empty($data)) {
        header("LOCATION:newsletterAusgabe.php");
        exit;
    }
    $_POST['anrede'] = $data->anrede;
    $_POST['vorname'] = $data->vorname;
    $_POST['nachname'] = $data->nachname;
    $_POST['email'] = $data->email;
    $_POST['id'] = $id;
    $_POST['_token'] = $sess->getCsrf();   
}

if (!empty($_POST) && !isset($_GET['aendern'])) {
    if (!isset($_POST['_token']) ||$_POST['_token'] != $sess->getCsrf()) {
        $error = 'Datenübertragung fehlgeschlagen';
    }
    else {
        $gump = new GUMP('de');
        $rules = [
            'anrede' => 'required|contains_list,Frau;Herr;Divers',
            'vorname' =>'required|min_len,2|max_len,100',
            'nachname' =>'required|min_len,2|max_len,100',
            'email' => 'required|valid_email',
            'datenschutz' => 'required'
        ];

        $gump->validation_rules($rules);
        
        $gump->set_fields_error_messages([
            'datenschutz'      => [
                'required' => 'Bitte Datenschutzerklärung zustimmen.',
            ]
        ]);

        $valid_data = $gump->run($_POST);
        if( $gump->errors()){
            $errors = $gump->get_errors_array();
        }
        else{
            unset($_POST['_token']);

            $fields = [
                'anrede'=>$_POST['anrede'],
                'vorname' =>$_POST['vorname'],
                'nachname' =>$_POST['nachname'],
                'email' =>$_POST['email'],
                'datenschutz' =>$_POST['datenschutz'],
            ];
            $db->update('newsletter',$fields,$_POST['id']);
            
            unset($_POST);
            header('LOCATION:newsletterAusgabe.php');
            exit;
        }
    }
}

htmldoc:
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Formularänderung</title>
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
    <h1>Newsletter Anmeldung</h1>
    <?php
        if (!empty($error)) {
            echo '<div style="color:red">'.$error.'</div>';
        }
        if( isset($success) ){
            echo '<div style="color:green; margin-bottom:10px;">'.$success.'</div>';
        } 
        if (!empty($err_email)) {
            echo '<div style="color:red">'.$err_email.'</div>';
        }
    ?>
    <div class="py-3">
        <a href="newsletterAusgabe.php" class="btn btn-info btn-sm">Alle anzeigen</a>
    </div>
    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
        <div class="form-group">
            <div class="form-check form-check-inline is-invalid">
                <input class="form-check-input" type="radio" name="anrede" id="w" value="Frau" 
                    <?php
                        if(isset($_POST['anrede']) && $_POST['anrede'] == 'Frau') echo 'checked';
                    ?>
                >
                <label class="form-check-label" for="w">Frau</label>
                </div>
                <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="anrede" id="m" value="Herr"
                    <?php
                        if(isset($_POST['anrede']) && $_POST['anrede'] == 'Herr') echo 'checked';
                    ?>
                >
                <label class="form-check-label" for="m">Herr</label>
                </div>
                <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="anrede" id="d" value="Divers"
                    <?php
                        if(isset($_POST['anrede']) && $_POST['anrede'] == 'Divers') echo 'checked';
                    ?>
                >
                <label class="form-check-label" for="d">Divers</label>
            </div>
            <div class="invalid-feedback"><?php if(isset($errors['anrede']) ) echo $errors['anrede']; ?></div>
        </div>
        <div class="form-group">
            <label for="vorname">Vorname*</label>
            <input type="text" name="vorname" id="vorname" class="form-control <?php if(isset($errors['vorname']) ) echo 'is-invalid'; ?>" value="<?php echo $_POST['vorname'] ?? ''; ?>">
            <div class="invalid-feedback"><?php if(isset($errors['vorname']) ) echo $errors['vorname']; ?></div>
        </div>
        <div class="form-group">
            <label for="Nachname">Nachname*</label>
            <input type="text" name="nachname" id="nachname" class="form-control <?php if(isset($errors['nachname']) ) echo 'is-invalid'; ?>" value="<?php echo $_POST['nachname'] ?? ''; ?>">
            <div class="invalid-feedback"><?php if(isset($errors['nachname']) ) echo $errors['nachname']; ?></div>
        </div>
        <div class="form-group">
            <label for="email">E-Mail*</label>
            <input type="text" name="email" id="email" class="form-control 
                <?php if(isset($errors['email']) ) echo 'is-invalid'; ?>" value="<?php echo $_POST['email'] ?? ''; ?>">
            <div class="invalid-feedback"><?php if(isset($errors['email']) ) echo $errors['email']; ?></div>
        </div>
        <div class="form-group">
            <div class="form-check is-invalid">
                <input class="form-check-input" type="checkbox" name="datenschutz" id="ds" value="Datenschutz gelesen">
                <label class="form-check-label is-invalid" for="ds">Ich habe die <a href="#">Datenschutzerklärung</a> gelesen und bin damit einverstanden, dass die von mir eingegebenen personenbezogenen Daten gespeichert werden. Die Anmeldung kann ich jederzeit widerrufen.</label>
                <div class="invalid-feedback"><?php if(isset($errors['datenschutz']) ) echo $errors['datenschutz']; ?></div>
            </div>
        </div>
        <input type="hidden" name="_token" value="<?php echo $sess->setCsrf();?>">
        <input type="hidden" name="id" value="<?php echo $_POST['id'] ?? '' ?>">
       <button type="submit" class="btn btn-primary">Anmelden</button>
    </form>
  </div>
</body>
</html>