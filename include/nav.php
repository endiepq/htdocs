<?php
// REDIRECT IF THE NAV IS LOADED WITHOUT A TITLE
if(!isset($title)){
  //redirect to main page
  header("Location: /");
  die();
} ?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
  
    <a class="navbar-brand" href="/">Logo</a>
    
    <?php // mobile menu icon ?>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>


    <?php 
    if($auth){ 
      // guest visitor menu options 
      ?>
    <?php if(isset($user)){ 
      // authenticated user visitor menu options
      ?>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">

        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="/home">Home</a>
        </li>

        

        <?php /* For Reference 
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Menu
          </a>

          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="#">Option 2</a></li>
            <li><a class="dropdown-item" href="#">Option 1</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#">Another Option</a></li>
          </ul>
        </li> */ ?>


        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Account
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="/profile">Profile</a></li>
            <li><a class="dropdown-item" href="/account">Account</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="/logout">Logout</a></li>
          </ul>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="/subpage">Sub Page</a>
          </li>

          

            <?php // DISPLAY MENU FOR ADMINS 
          if($user->isAdmin()){  ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Admin</a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
              <li><a class="dropdown-item" href="/admin/users">Users</a></li>
              <li><a class="dropdown-item" href="/admin/options">Options</a></li>
              <li><a class="dropdown-item" href="/admin/pages">Pages</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="#">Something else here</a></li>
            </ul>
          </li>
          <?php } ?>

          <?php /* // DISPLAY MENU FOR OTHER 
          if($user->isOther()){  ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Admin</a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
              <li><a class="dropdown-item" href="/admin/users">Users</a></li>
              <li><a class="dropdown-item" href="/admin/options">Options</a></li>
              <li><a class="dropdown-item" href="/admin/pages">Pages</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="#">Something else here</a></li>
            </ul>
          </li>
          <?php } */ ?>
          
       
      </ul>

      
      
				

  </div>

  <?php } ?>

  <?php } ?>
</nav>

