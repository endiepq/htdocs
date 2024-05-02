<?php
session_start();

ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

//echo "<pre>"; print_r($_SERVER); echo "</pre>";

/*
Start session
Define Variables 
generate the $page var
Load functions
Load User class
Try to login with sessionLogin() and set $auth (true/false)
check if the selected page exists
load the page

DEFINE SOME VARIABLES 
*/
$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
//directory is used to include scripts or files
$directory = $_SERVER['DOCUMENT_ROOT'];
// currentUrl gives the current url of the loaded page, for displaying the url in links
$currentUrl = $url.$_SERVER['REQUEST_URI'];



/*
The REQUEST_URI is split by the ?
then the path section is split by the / 
creating an array of path's 
array("Folder", "Sample-Page-Title")
*/
$request_uri = explode( "?", $_SERVER['REQUEST_URI']); // split the path from the query 
$url_path = array_values(array_filter(explode("/", $request_uri['0']))); // array of different paths with removed empty arrays

//var_dump($url_path);

// process the url_path to figure out what page to display
if(empty($url_path)){
    // load the home page, empty uri
    $page = "main";
}else{
    // figure out the sections before setting the page var /section/section/page_name
    $page = filter_var($url_path['0'], FILTER_SANITIZE_STRING);  
}

// LOAD THE FUNCTIONS FILE
require_once $directory.'/functions.php';

// LOAD REQUIRED CLASSES TO LOGIN
require_once $directory.'/include/User.php';
$user = new User();
//echo "User Class<br/>";

// FIGURE OUT WHEATER A USER IS LOGED IN.
if($user->sessionLogin()){
  $auth = true;
  $userInfo = $user->getUserInfoById();
}else{
  $auth = false;
}

// FIND AND LOAD THE PAGE
// find the page on the pages directory
if(file_exists($directory."/pages/".$page.".php")){
  require_once $directory."/pages/".$page.".php";
}else{
  // FILE DOESNT EXIST, THERE FOR IS AN INVALID PAGE, INCLUDE 404 PAGE
  include $directory."/pages/404.php";
}
?>


