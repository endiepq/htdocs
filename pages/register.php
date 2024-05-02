<?php

/// used to register users
/// also used to activate accounts, when the user clicks on the registration code to activate the account

// SETUP THE VARIABLES 
$msgs = array(); // variable to store the messages requred by msgs()

// PROCESS THE ACCOUNT ACTIVATION 
if(isset($_GET['code']) && isset($_GET['email'])){
  $code = filter_var($_GET['code'], FILTER_SANITIZE_STRING);
  $email = filter_var($_GET['email'], FILTER_SANITIZE_EMAIL);
  
  if(!empty($email) && !empty($code)){
    // activate the account with the code
    $verify = $user->verifyAccount($email, $code);
    if(isset($verify['success'])){
      $msgs[] = array('success'=>"Your Account was Successfully Activated!");
    }if(isset($verify['error'])){
      $msgs[] = array('danger'=>"There was an error activating your account");
    }
  }       
}

// fname, lname, username, email, password
// PROCESS THE REGISTRATION


if(isset($_POST['submit']) && isset($_POST['fname']) && isset($_POST['lname']) && isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password'])){

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
      $fname = filter_var($_POST['fname'], FILTER_SANITIZE_STRING);
      $lname = filter_var($_POST['lname'], FILTER_SANITIZE_STRING);
      $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
      $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
      $pass = $_POST['password'];
      $confPass = $_POST['confirm-password'];

      if(empty($fname)){
        $msgs[] = array('danger'=>"Empty First Name");
      }elseif(empty($lname)){
        $msgs[] = array('danger'=>"Empty Last Name");
      }elseif(empty($username)){
        $msgs[] = array('danger'=>"Empty Username");
      }elseif(empty($email)){
        $msgs[] = array('danger'=>"Empty Email");
      }elseif(empty($pass)){
        $msgs[] = array('danger'=>"Empty Password");
      }elseif($pass != $confPass){
        $msgs[] = array('danger'=>"Password is not the Same");
      }else{
        // CONTINUE WITH REGISTRATION
        $msgs[] = $user->addAccount($fname, $lname, $username, $email, $pass);
        /*
        if(isset($messages['success'])){
          //redirect to login with successfull message.
          $msgs[] = array('success'=>$messages['success']);        
        }else{
          $msgs[] = array('danger'=>$messages['danger']); 
        }*/
      }
    }else{
      $msgs[] = array('danger'=>"Failed Captcha Verification");
    }

  }
  ?>
  
  <?php 
  
  // setup var for the header
  $title = "Register | ".$WebsiteName;
  include ($directory.'/include/header.php');
  ?>
  
  <div class="container">
  
  <div class="row justify-content-center my-5">
    <?php msgs($msgs) ?>
      <div class="col-lg-4 col-sm-12 col-md-6 text-center">
        <h3>Register</h3>
        <br/>
        <?php /*
        if(isset($messages) && !empty($messages)){
          foreach ($messages as $key=>$message ){
            echo "<div class='alert alert-$key' role='alert'>".$message."</div>";         
          }
        } */
    ?>
        
        <form method="POST" action="register">
          <div class="form-group">
            <label for="fname">Имя</label>
            <input type="fname" class="form-control" name="fname" id="fname" placeholder="First Name">
          </div>
          <div class="form-group">
            <label for="lname">Фамилия</label>
            <input type="lname" class="form-control" name="lname" id="lname" placeholder="Last Name">
          </div>

          <div class="form-group">
            <label for="username">Ник</label>
            <input type="username" class="form-control" name="username" id="username" placeholder="Username">
          </div>

          <div class="form-group">
            <label for="email">Почта</label>
            <input type="email" class="form-control" name="email" id="email" placeholder="Email">
          </div>

          <div class="form-group">
            <label for="password">Пароль</label>
            <input type="password" class="form-control" name="password" id="password" placeholder="Password">
          </div>

          <div class="form-group">
            <label for="password">Подтвердите пароль</label>
            <input type="password" class="form-control" name="confirm-password" id="confirm-password" placeholder="Confirm Password">
          </div>

          <br/>
          <button id="submit" name="submit" type="submit" class="btn btn-primary">Зарегистрироваться</button>
          <a name="login" class="btn btn-primary" href="login">Вернуться к входу</a>
          <br/>
          <hr />
          
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
  
  <?php include($directory.'/include/footer.php'); ?>