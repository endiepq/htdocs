<?php

if(!$user->isAdmin()){
  header("Location: /");
  die("Redirect Home from Admin");
}
if(!$page == "admin"){
  header("Location: home");
  die("Redirect Home from Admin");
}

$userInfo = $user->getUserInfoById();
if(!$userInfo){
  header("Location: logout.php");
  die();
}

require $directory.'/include/Admin.php';
$admin = new Admin();

if(isset($_GET['msg'])){
    $msg = $_GET['msg'];
}

$title = "Admin | ".$WebsiteName;
include ($directory.'/include/header.php');
include ($directory.'/include/nav.php');

if(isset($url_path['1'])){
    $subPage = filter_var($url_path['1'], FILTER_SANITIZE_STRING);
    
    if(file_exists($directory."/pages/admin/".$subPage.".php")){
        require_once $directory."/pages/admin/".$subPage.".php";
    }else{
        echo "Страница не найдена";  
    }
}else{
    echo "Страница администратора";
}

  
include($directory.'/include/footer.php'); 
?>