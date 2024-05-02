<?php
// this was moved to /pages/sub/users.php
die();
if(!$user->isAdmin()){  
  header("Location: home");
  die();
}
else{
  ?>
<?php
// DECLARE THE ADMIN CLASS
require __DIR__.'/../include/Admin.php';
$admin = new Admin();

// PROCESS RECEIVED MESSAGES TO DISPLAY
if(isset($_GET['msg'])){
  $msg = $_GET['msg'];
}

// PROCESS ACTIVATE OR DISABLE USERS
if(isset($_GET['action']) && isset($_GET['userId'])){

  if($_GET['action'] == "enable" || $_GET['action'] == "disable"){ // PROCESS THE enable & disable button
    $userId = filter_var($_GET['userId'], FILTER_SANITIZE_NUMBER_INT);
    if($admin->changeAccountStatus($userId)){
      $data = array(
        'p' => "users",
        'msg' => array('info'=>"Successfully Changed Account Status")
      );
      $redirect = $url."/home.php?".http_build_query($data, '', '&');
    }
    else{
      $data = array(
        'p' => "users",
        'msg' => array('danger'=>"Failed to update the account!")
      );
      $redirect = $url."/".http_build_query($data, '', '&');
    }
    header("Location: $redirect");
  }
  elseif($_GET['action'] == "update" & is_numeric($_GET['userId'])){ // PROCESS THE edit user FORM
    //echo "<pre>";
    //var_dump($_POST);
    //echo "</pre>";
    $fname = filter_var($_POST['fname'], FILTER_SANITIZE_STRING);
    $lname = filter_var($_POST['lname'], FILTER_SANITIZE_STRING);
    $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $mobile = filter_var($_POST['mobile'], FILTER_SANITIZE_NUMBER_INT);
    $userId = filter_var($_GET['userId'], FILTER_SANITIZE_NUMBER_INT);
    $isActive = isset($_POST['is_active']) ? ($_POST['is_active'] == "on" ? true : false) : false;
    $isAdmin = isset($_POST['is_admin']) ? ($_POST['is_admin'] == "on" ? true : false) : false;
    $isUser = isset($_POST['is_user']) ? ($_POST['is_user'] == "on" ? true : false) : false;
    if(empty($fname) || empty($lname) || empty($username) || empty($email) || empty($userId)){
      $msg['danger'] = "Some Fields are required.";
    }else{
      $result = $admin->updateUser($userId, $fname, $lname, $username, $email, $mobile, $isActive, $isAdmin, $isUser);
      $msg = $result;
    }


    // redirect to home.php?p=users&action=edit&userId=$userId with message 
  }
  
}
?>

<?php
// INCLUDE THE HEADER TO INCLUDE THE NAV AND MENUS.
$title = "Users | PHP Login System"; // SETUP THE TITLE VAR FOR THE title tag
include (__DIR__.'/../include/header.php');
include(__DIR__.'/../include/nav.php');
?>

  <div class="container">

    <?php
    if(isset($msg) && !empty($msg)){
      foreach ($msg as $key=>$message ){
        echo "<div class='alert alert-$key' role='alert'>".$message."</div>";         
      }
    }
    ?>


<?php
    if(isset($_GET['action']) && isset($_GET['userId'])){
      if($_GET['action'] == "edit"){ 
        $userId = filter_var($_GET['userId'], FILTER_SANITIZE_STRING);
        $user = $admin->getUser($userId);

      ?>
<h2>Edit the User:</h2>
 <form action="home.php?p=users&action=update&userId=<?php echo $user['id'] ?>" method="post">
<div class="row">
 
  <div class="col">

    <div class="row mb-3">
    <label for="first_name" class="col-sm-2 col-form-label">First Name</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" id="first_name" name="fname" placeholder="Fist Name" value="<?php echo $user['fname'] ?>">
      </div>
    </div>

    <div class="row mb-3">
    <label for="first_name" class="col-sm-2 col-form-label">Last Name</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" id="last_name" name="lname" placeholder="Last Name" value="<?php echo $user['lname'] ?>">
      </div>
    </div>

    <div class="row mb-3">
      <label for="last_name" class="col-sm-2 col-form-label">Username</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" id="username" name="username" placeholder="Username" value="<?php echo $user['username'] ?>">
      </div>
    </div>

    <div class="row mb-3">
    <label for="email" class="col-sm-2 col-form-label">Email</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" id="email" name="email" placeholder="Email" value="<?php echo $user['email'] ?>">
      </div>
    </div>

    <div class="row mb-3">
    <label for="mobile" class="col-sm-2 col-form-label">Mobile</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" id="mobile" name="mobile" placeholder="Mobile Number" value="<?php echo $user['mobile'] ?>">
      </div>
    </div>

    <button type="submit" name="update" class="btn btn-primary">Update</button>

  </div>

  <div class="col">

  <div class="row mb-3">
      <div class="col-sm-10 offset-sm-2">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="gridCheck1" name="is_active"<?php echo ($user['isActive'] == 1 ? 'checked' : '') ?>>
          <label class="form-check-label" for="gridCheck1">
            Is Active
          </label>
        </div>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-sm-10 offset-sm-2">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="gridCheck1" name="is_admin"<?php echo ($user['isAdmin'] == 1 ? 'checked' : '') ?>>
          <label class="form-check-label" for="gridCheck1">
            Is Admin
          </label>
        </div>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-sm-10 offset-sm-2">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="gridCheck1" name="is_user"<?php echo ($user['isUser'] == 1 ? 'checked' : '') ?>>
          <label class="form-check-label" for="gridCheck1">
            Is User
          </label>
        </div>
      </div>
    </div>

  </div>


 


</div> 
</form>
 <hr/>
        <?php
      }
    }


?>


    <div class="row">
      <div class="col">
        <h2>Пользователи</h2>
      </div>
    </div>

    <div class="row">
      <table class="table">
        <tr>
          <th>Имя</th><th>Ник</th><th>Почта</th><th>Телефон</th><th>Статус</th><th>Админ</th><th>Настройки</th>
        </tr>
        <?php
        // PULL THE USERS LIST
        $users = $admin->getUsers();
        
        if(is_array($users)){
        
          foreach($users as $user){ ?>
            <tr>
            <td><?php echo $user['fname']." ".$user['lname'] ?></td>
            <td><?php echo $user['username'] ?></td>
            <td><?php echo $user['email'] ?></td>
            <td><?php echo $user['mobile'] ?></td>
            <td>
            <?php 
            if($user['isActive'] == 0){ ?>
            Not Active
            <?php }else{ ?>
            Active
            <?php } ?>
            
            </td>
            <td>
            <?php 
            if($user['isAdmin'] == 0){ ?>
            Not Admin
            <?php }else{ ?>
            Admin
            <?php } ?>
            </td>
            <td>
            <a class="btn btn-sm btn-success" href="#" role="button">View</a> 
            <a class="btn btn-sm btn-info" href="/home.php?p=users&action=edit&userId=<?php echo $user['id'] ?>" role="button">Edit</a>
            <a class="btn btn-sm btn-danger" href="#" role="button">Remove</a>
            <?php
            if($user['isActive']){ ?>
              <a class="btn btn-sm btn-warning" href="/home.php?p=users&action=disable&userId=<?php echo $user['id'] ?>" role="button">Disable</a>
            <?php }else{ ?>
              <a class="btn btn-sm btn-success" href="/home.php?p=users&action=enable&userId=<?php echo $user['id'] ?>" role="button">Enable</a>
            <?php } ?>
            
            </td>

            </tr>
            <?php
          }
        }
        

        ?>

      </table>
    </div>


  </div>





<?php }

?>

<?php
// FOOTER 
include(__DIR__.'/../include/footer.php'); 
?>