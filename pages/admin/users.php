<?php

if(!$user->isAdmin()){
  // redirect Home
  header("Location: /");
  die("Redirect Home from Admin");
}
if(!$page == "admin"){
  header("Location: home");
  die("Redirect Home from Admin");
}


// SETUP THE VARIABLES 
$msgs = array(); // variable to store the messages requred by msgs()

//echo "<hr/><pre>"; var_dump($_POST); echo "</pre><hr/>";

///////////////// PROCESS ADMIN USER REMOVE /////////////////
if(isset($_POST['remove']) && isset($_POST['userId']) && isset($_POST['email'])){
  $userId = filter_var($_POST['userId'], FILTER_SANITIZE_NUMBER_INT);
  $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
  if(!empty($userId) && !empty($email)){
    $msgs[] = $admin->removeAccount($userId, $email);
  }else{
    $msgs[] = array('danger'=>"Some more information is required!");
  }
}

///////////////// PROCESS ADMIN USER ENABLE/DISABLE /////////////////
if(isset($_POST['status']) && isset($_POST['userId']) && isset($_POST['email'])){
  $userId = filter_var($_POST['userId'], FILTER_SANITIZE_NUMBER_INT);
  $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

  if($admin->changeAccountStatus($userId, $email)){
    $msgs[] = array('success'=>"Successfully Changed Account Status");
  }else{
    $msgs[] = array('success'=>"Failed to Changed Account Status");
  }
}

///////////////// ENABLE ADMIN EDIT USER FORM /////////////////
if(isset($_GET['edit']) && isset($_GET['userId'])){
  $userId = filter_var($_GET['userId'], FILTER_SANITIZE_STRING);
  $editUser = $admin->getUser($userId);
}
///////////////// PROCESS ADMIN EDIT USER FORM /////////////////
if(isset($_POST['update']) && isset($_POST['userId'])){
  $fname = filter_var($_POST['fname'], FILTER_SANITIZE_STRING);
  $lname = filter_var($_POST['lname'], FILTER_SANITIZE_STRING);
  $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
  $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
  $mobile = filter_var($_POST['mobile'], FILTER_SANITIZE_NUMBER_INT);
  $userId = filter_var($_POST['userId'], FILTER_SANITIZE_NUMBER_INT);
  $isActive = isset($_POST['is_active']) ? ($_POST['is_active'] == "on" ? true : false) : false;
  $isAdmin = isset($_POST['is_admin']) ? ($_POST['is_admin'] == "on" ? true : false) : false;
  $isUser = isset($_POST['is_user']) ? ($_POST['is_user'] == "on" ? true : false) : false;

  if(empty($fname)){
    $msgs[] = array('danger'=>"First Name is Required.");
  }elseif(empty($lname)){
    $msgs[] = array('danger'=>"Last Name is Required.");
  }elseif(empty($username)){
    $msgs[] = array('danger'=>"Username is Required.");
  }elseif(empty($email)){
    $msgs[] = array('danger'=>"Email is Required.");
  }elseif(empty($userId)){
    $msgs[] = array('danger'=>"UserId is required.");
  }else{
    $msgs[] = $admin->updateUser($userId, $fname, $lname, $username, $email, $mobile, $isActive, $isAdmin, $isUser);
  }
}



/////////////////////////PAGINATION/////////////////////////
// get the page number for pagination
if (!isset ($_GET['page']) ) {  
  $page_number = 1;  
} else {  
  $page_number = $_GET['page'];  
}
// Rows per page
$limit = 20;
// get the initial page number
$initial_page = ($page_number-1) * $limit; 
// get all the rows
$result = $admin->getUsers();
//echo "<pre>"; print_r($result); echo "</pre>";
$total_rows = count($result);
// get the required number of pages
$total_pages = ceil ($total_rows / $limit);  
$limit = $initial_page.",".$limit;
/////////////////////////PAGINATION/////////////////////////



////////////DISPLAY THE PAGE////////////
?>
<div class="container">
  <div class="row my-5">
    <?php
    //echo "New: <pre>"; print_r($msgs); echo "</pre>";
    ?>
    <?php msgs($msgs) ?>
  </div>

  
  </div>
  
  <div class="container-fluid">
      <div class="row">
      <?php // EDIT THE USER SECTION
          if(isset($editUser)){ ?>
            <h2>Edit User</h2>
            <form action="<?php echo $url ?>/admin/users" method="post">
              <input type="hidden" name="userId" value="<?php echo $editUser['id'] ?>" >
            <div class="row">
              <div class="col">
                
                <div class="mb-3">
                  <label class="form-label" for="id" >ID:</label>
                  <input type="text" readonly class="form-control" id="id" name="id" value="<?php echo $editUser['id'] ?>">
                </div>

                <div class="mb-3">
                  <label class="form-label" for="fname">First Name</label>
                  <input type="text" class="form-control" id="fname" name="fname" value="<?php echo $editUser['fname'] ?>">
                </div>

                <div class="mb-3">
                  <label for="lname" class="form-label">Last Name</label>
                  <input type="text" class="form-control" id="lname" name="lname" value="<?php echo $editUser['lname'] ?>">
                </div>

                </div>
                <div class="col">

                <div class="mb-3">
                  <label for="username" class="form-label">Username</label>
                  <input type="text" class="form-control" id="username" name="username" value="<?php echo $editUser['username'] ?>">
                </div>

                
                <div class="mb-3">
                  <label for="email" class="form-label">Email</label>
                  <input type="text" class="form-control" id="email" name="email" value="<?php echo $editUser['email'] ?>">
                </div>

                
                <div class="mb-3">
                  <label for="mobile" class="form-label">Mobile</label>
                  <input type="text" class="form-control" id="mobile" name="mobile" value="<?php echo $editUser['mobile'] ?>">
                </div>


                </div>
                <div class="col">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="gridCheck1" name="is_active"<?php echo ($editUser['isActive'] == 1 ? 'checked' : '') ?>>
                  <label class="form-check-label" for="gridCheck1">
                    Is Active
                  </label>
                </div>
              
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="gridCheck1" name="is_admin"<?php echo ($editUser['isAdmin'] == 1 ? 'checked' : '') ?>>
                  <label class="form-check-label" for="gridCheck1">
                    Is Admin
                  </label>
                </div>
              
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="gridCheck1" name="is_user"<?php echo ($editUser['isUser'] == 1 ? 'checked' : '') ?>>
                  <label class="form-check-label" for="gridCheck1">
                    Is User
                  </label>
                </div>

                  <div class="mb-3">
                    <button name="update" type="submit" class="btn btn-primary mb-3">Update</button>
                  </div>

                </div>
                <hr/>
              </div>
            </form>
          <?php } ?>
        
          <h2>User List</h2>
        <table class="table table-sm">
          <tr>
            <th>ID</th><th>Name</th><th>Username</th><th>Email Adress</th><th>Mobile</th><th>Status</th><th>Is Admin</th><th>Created</th><th>Updated</th><th>Account Options</th>
          </tr>

          <?php
          // PULL THE USERS LIST
          $users = $admin->getUsers($limit);
          
          if(is_array($users)){
            foreach($users as $user){ ?>
              <tr>
              <td><?php echo $user['id'] ?></td>
              <td><?php echo $user['fname']." ".$user['lname'] ?></td>
              <td><?php echo $user['username'] ?></td>
              <td><?php echo $user['email'] ?></td>
              <td><?php echo $user['mobile'] ?></td>
              <td>
              <?php 
              if($user['isActive'] == 1){
                echo "Yes";
               }else{
                 echo "No";
                 } ?>
              
              </td>
              <td>
              <?php 
              if($user['isAdmin'] == 1){
                echo "Yes";
               }else{
                 echo "No";
               } ?>
              </td>
              <td><?php echo timeago($user['created_at']) ?></td>
              <td><?php echo timeago($user['updated_at']) ?></td>
              <td>
                <form action="<?php echo $currentUrl ?>" method="post" >
                <input type="hidden" name="userId" value="<?php echo $user['id'] ?>" />
                <input type="hidden" name="email" value="<?php echo $user['email'] ?>" />

                <a class="btn btn-sm btn-success" href="#" role="button">View</a> 
                <a class="btn btn-sm btn-info" href="/admin/users?edit=true&userId=<?php echo $user['id'] ?>" role="button">Edit</a>
                <input type="submit" name="remove" class="btn btn-sm btn-danger" value="Remove" />
                <?php
                if($user['isActive']){ ?>
                  <input type="submit" name="status" class="btn btn-sm btn-warning" value="Disable" />
                <?php }else{ ?>
                  <input type="submit" name="status" class="btn btn-sm btn-success" value="Enable" />
                <?php } ?>
                </form>
              </td>
  
              </tr>
              <?php
            }
          }
          ?>

        </table>
      </div>

      <div class="row"><?php /////////////////////////PAGINATION/////////////////////////?>
        <nav>
          <ul class="pagination justify-content-center">
            <?php
            $current_page = $page_number;
            for($page_number = 1; $page_number<=$total_pages; $page_number++){
              ?><li class="page-item <?php echo ($page_number == $current_page) ? "active" : '' ?>"><a href="<?php echo currentUrl(['page'])."?page=".$page_number ?>" class="page-link"><?php echo $page_number ?></a></li><?php
            }
            ?>
          </ul>
        </nav>
      </div><?php /////////////////////////PAGINATION///////////////////////// ?>
  
  
    </div>
  
  
  
  
