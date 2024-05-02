<?php
// GET THE USER INFO 
$userInfo = $user->getUserInfoById();
if(!$userInfo){
  //failed to get the user info, so user is loged out.
  header("Location: /logout");
  die();
}
// SETUP THE VARIABLES 
$url_path; // holds the array with paths 


$title = "Services | ".$WebsiteName; // SETUP THE TITLE VAR FOR THE title tag
include ($directory.'/include/header.php');
include ($directory.'/include/nav.php');
?>

<div class="container-fluid">
  <div class="row">
    <link href="<?php echo $url ?>/include/sidebars.css" rel="stylesheet">
      <div class="d-flex justify-content-center flex-row flex-shrink-0 text-white bg-dark" >
        
        <ul class="nav nav-pills flex-row mb-auto">
          <li class="nav-item">
            <a href="<?php echo $url ?>/subpage/main" class="nav-link text-white <?php echo ($url_path[1] == 'main' || !isset($url_path[1])) ?'active':'' ?>" aria-current="page">
              Home
            </a>
          </li>

         
          <li>
            <a href="<?php echo $url ?>/subpage/other" class="nav-link text-white <?php echo ($url_path[1] == 'other'?'active':'')?>">
              Other
            </a>
          </li>
          <li>
            <a href="<?php echo $url ?>/subpage/second" class="nav-link text-white <?php echo ($url_path[1] == 'second'?'active':'')?>">
              Second
            </a>
          </li>
          <li>
            <a href="<?php echo $url ?>/subpage/third" class="nav-link text-white <?php echo ($url_path[1] == 'third'?'active':'')?>">
              Third
            </a>
          </li>
          <li>
            <a href="<?php echo $url ?>/subpage/last" class="nav-link text-white <?php echo ($url_path[1] == 'last'?'active':'')?>">
              Last
            </a>
          </li>
          
        </ul>
      </div>
  </div>
  <div class="row my-4">
    <div class="col">
      <?php // include the file for the selected subpage
  if(isset($url_path['1'])){
    $subPage = $url_path['1'];
    if(file_exists($directory."/pages/subpage/".$subPage.".php")){
      require_once $directory."/pages/subpage/".$subPage.".php";
    }else{
      echo "Page Does Not Exists";
    }
  }else{
    if(file_exists($directory."/pages/subpage/main.php")){
      require_once $directory."/pages/subpage/main.php";
    }else{
      echo "Page Does Not Exists";
    }
  }
  ?>
      
      
      
    </div>
  </div>
  
</div>

<?php /* 
<script type="text/javascript" >
/* global bootstrap: false 
(function () {
  'use strict'
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  tooltipTriggerList.forEach(function (tooltipTriggerEl) {
    new bootstrap.Tooltip(tooltipTriggerEl)
  })
})()
</script> */ ?>
<?php
// FOOTER 
include($directory.'/include/footer.php'); 
?>