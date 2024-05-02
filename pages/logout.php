<?php

if($user->logout()){
    //echo "Successfully Log out<br/>";
    header("Location: /login?msg=logout");
    die("Success logout");
}else{
    die("failed to logout");
}
?>