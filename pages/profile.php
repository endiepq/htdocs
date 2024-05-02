<?php

// GET THE USER INFO 
$userInfo = $user->getUserInfoById();
if(!$userInfo){
  //failed to get the user info, so user is loged out.
  header("Location: logout.php");
  die("UserInfo");
}

?>




<?php
// INCLUDE THE HEADER TO INCLUDE THE NAV AND MENUS.
$title = "Profile | PHP Login System"; // SETUP THE TITLE VAR FOR THE title tag
include (__DIR__.'/../include/header.php');
include(__DIR__.'/../include/nav.php');
?>




<div class="container">
  <div class="row">
    
    <div class="col">
      <?php echo (isset($userInfo['username']) ? '<h2>'.$userInfo['username'].'</h2>' : '') ?>
      <?php echo (isset($userInfo['fname']) ? '<h4>'.$userInfo['fname'].' '.$userInfo['lname'].'</h4>' : '') ?>
    </div>
  </div>



</div>

<?php
// FOOTER 
include(__DIR__.'/../include/footer.php'); 
?>