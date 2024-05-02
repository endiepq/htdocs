<?php
// INCLUDE THE HEADER TO INCLUDE THE NAV AND MENUS.
$title = "Page Not Found | PHP Login System"; // SETUP THE TITLE VAR FOR THE title tag

include ($directory.'/include/header.php');
include ($directory.'/include/nav.php');
?>

<div class="container">

<h1>404</h1>
<p><strong>Page not found</strong></p>

<p>
  The page you are looking for was not found.
</p>

</div>


<?php include(__DIR__.'/../include/footer.php'); ?>