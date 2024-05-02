<?php

$title = "Home | ".$WebsiteName; 
include (__DIR__.'/../include/header.php');
include(__DIR__.'/../include/nav.php');
?>
<div class="container">
  <div class="row m-4">
    <div class="col text-center">
      <h1>Добро пожаловать!</h1>
    </div>
  </div>

  <div class="row m-4">
    <div class="col text-center">
    <a href="/login">Войти</a>
    </div>
  </div>

</div>


<?php

include(__DIR__.'/../include/footer.php'); 
?>