<?php

$msgs = array();

if(isset($_GET['msg'])){
  if($_GET['msg'] == "logout"){
    $msgs[] = array('danger'=>"Logout Successful");
  }
  if($_GET['msg'] == "register"){
    $msgs[] = array('success'=>"You account was created successfully,<br/> check your email for an activation link to activate your account.");
  }
}


if($login = $user->sessionLogin()){

  header("Location: /home");
  die();
}

if(isset($_GET['activate'])){
  $msgs[] = array('info'=>$_GET['activate']);
}

// PROCESS THE LOGIN WITH USERNAME AND PASSSWORD
if(isset($_POST['submit']) && isset($_POST['email']) && isset($_POST['password'])){

  // check if recaptcha is enabled
  if($enable_recaptcha){
    if(isset($_POST['token']) && isset($_POST['action'])){
      $token = $_POST['token'];
      $action = $_POST['action'];
      
      // call curl to POST request
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL,"https://www.google.com/recaptcha/api/siteverify");
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('secret' => $recaptcha_secret_key, 'response' => $token)));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $response = curl_exec($ch);
      curl_close($ch);
      $arrResponse = json_decode($response, true);
      // verify the response
      if($arrResponse["success"] == '1' && $arrResponse["action"] == $action && $arrResponse["score"] >= 0.7) {
        $recaptcha = true;
      }else{
        $recaptcha = false;
      }
    }else{
      $recaptcha = false;
    }
  }else{
    $recaptcha = true;
  }


  if($recaptcha){
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $pass = $_POST['password'];

    if(empty($email)){
      $msgs[] = array('danger'=>"Please enter an email address");
    }elseif(empty($pass)){
      $msgs[] = array('danger'=>"Please enter a Password");
    }else{
      $login = $user->login($email, $pass);
      if(isset($login['success'])){
        //$msgs[] = $login;
        //redirect to home page. 
        header("Location: /home");
      }else{
        $msgs[] = $login;      
      }
    }
  }else{
    $msgs[] = array('danger'=>"Failed Captcha Verification");
  }
}


// setup var for the header
$title = "Login | ".$WebsiteName;
include (__DIR__.'/../include/header.php'); 
?>

<div class="container">
  <div class="row justify-content-center my-5">
  <?php msgs($msgs) ?>
    <div class="col-lg-4 col-sm-12 col-md-6 text-center">
      <h3>Login</h3>
      <br/>

      <?php /* old code replaced by function 
      if(isset($messages) && !empty($messages)){
        foreach ($messages as $key=>$message ){
          echo "<div class='alert alert-$key' role='alert'>".$message."</div>";         
        }
      } */
  ?>
      <form method="POST" action="/login">
        <input type="hidden" id="token" name="token">
        <input type="hidden" name="action" value="validate_captcha">
        <div class="form-group">
          <label for="email">Почта</label>
          <input type="email" class="form-control" name="email" id="email" placeholder="Email">
        </div>
        <div class="form-group">
          <label for="password">Пароль</label>
          <input type="password" class="form-control" name="password" id="password" placeholder="Password">
        </div>
        <br/>
        <button id="submit" name="submit" type="submit" class="btn btn-primary">Войти</button>
        <br/>
        <hr />
        <a href="register" role="button" class="btn btn-link" >Зарегистрироваться</a>
        
      </form>
    </div>
</div>
</div> 

<?php if($enable_recaptcha){ ?>
<script src="https://www.google.com/recaptcha/api.js?render=<?php echo $recaptcha_site_key ?>"></script>
<script>
    grecaptcha.ready(function() {
    // do request for recaptcha token
    // response is promise with passed token
        grecaptcha.execute('<?php echo $recaptcha_site_key ?>', {action:'validate_captcha'})
                  .then(function(token) {
            // add token value to form
            document.getElementById('token').value = token;
        });
    });
</script>
<?php } ?>



<?php include(__DIR__.'/../include/footer.php'); ?>