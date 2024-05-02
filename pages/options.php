<?php
if($user->isAdmin()){
    require __DIR__.'/../include/Admin.php';
    $admin = new Admin();

    if(isset($_POST['update'])){
        
        if(!empty($_POST['newName']) && !empty($_POST['newValue'])){
            $newName = filter_var($_POST['newName'], FILTER_SANITIZE_STRING);
            $newValue = filter_var($_POST['newValue'], FILTER_SANITIZE_STRING);




        }

    }

?>
<?php
$title = "Options | PHP Login System";
include (__DIR__.'/../include/header.php');
include(__DIR__.'/../include/nav.php');
?>

<div class="container">
    <div class="row">
      <div class="col">
        <h2>Website Options</h2>
      </div>
    </div>

    <div class="row">
      <div class="col">

      <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>?p=options">
      <table class="table table-sm">
      <tr>
      <th>Option Id</th><th>Option Name</th><th>Option Value</th><th>Options</th></tr>
      <?php
      // pull the options array
      $options = $admin->getOptions($userInfo['id']);
      echo "<pre>";
      print_r($options);
      echo "</pre><hr/>";

      foreach($options as $k=>$v){ ?>
      <tr>
      <td><?php echo $v['option_id'] ?></td>
      <td><?php echo $v['option_name'] ?></td>
      <td><?php echo $v['option_value'] ?></td>
      <td><a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>?p=options&option=<?php echo $v['option_id'] ?>" type="submit" name="update" class="btn btn-primary mb-3">Edit</button></td>
      </tr>
      <?php } ?>

      <tr>
      <td>Add New Option: </td>
      <td><input type="text" class="form-control" id="option_name" name="newName" placeholder="Option Name" /></td>
      <td><input type="text" class="form-control" id="option_value" name="newValue" placeholder="Option Value" /></td>
      </tr>

      <tr class="text-center">
      <td colspan="3"><button type="submit" name="update" class="btn btn-primary mb-3">Update</button></td>
      <tr>

      </table>
      </form>


      </div>
    </div>

</div>

<?php
} // end of isAdmin Check
?>

<?php
// FOOTER 
include(__DIR__.'/../include/footer.php'); 
?>