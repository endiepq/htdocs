<?php

if(!$userInfo = $user->getUserInfoById()){
    header("Location: /logout");
    die();
}

$msgs = array();


if(isset($_POST['update-info'])){
    $username = filter_var(trim($_POST['username']), FILTER_SANITIZE_STRING);
    $fname = filter_var(trim($_POST['fname']), FILTER_SANITIZE_STRING);
    $lname = filter_var(trim($_POST['lname']), FILTER_SANITIZE_STRING);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $mobile = filter_var(trim($_POST['mobilePhone']), FILTER_SANITIZE_STRING);
    if(empty($username)){
		$msgs[] = array('danger'=>"Username Must Not be Empty");
    }elseif(empty($fname)){
        $msgs[] = array('danger'=>"First Name Must Not be Empty");
    }elseif(empty($lname)){
        $$msgs[] = array('danger'=>"Last Name Must Not be Empty");
    }elseif(empty($email)){
        $msgs[] = array('danger'=>"Email Must Not be Empty");
    }else{
        $msgs[] = $user->editAccountInfo($username, $fname, $lname, $email, $mobile);  
	} 
}

if(isset($_POST['update-password'])){
	if($_POST['new-pass'] === $_POST['verify-pass']){
		$newPass = $_POST['new-pass'];
		if(!empty($oldPass) || !empty($newPass)){
			$msgs[] = $user->editAccountPassword($newPass);
		}else{
			$msgs[] = array('danger'=>"Cannot use Empty passwords");
		}
	}else{
		$msgs[] = array('danger'=>"Passwords Do Not Match!");
	}
}


if(!$getUinfo = $user->getUserInfoById()){
    header("Location: /logout");
    die();
}


?>

<?php
$title = "Account | ".$WebsiteName;
include (__DIR__.'/../include/header.php');
include(__DIR__.'/../include/nav.php');
?>





<div class="container">
<?php 
      if(isset($msg) && !empty($msg)){
        foreach ($msg as $key=>$message ){
          echo "<div class='alert alert-$key' role='alert'>OLD".$message."</div>";         
        }
      }
  ?>
  <div class="row my-5">
    <?php msgs($msgs) ?>
    <h3>Обновите Вашу информацию</h3>
	<div class="col">
	  <form action="/account" method="post">

		<div class="form-group">
			<label for="inputUsername">Ник</label>
			<input type="text" class="form-control" id="inputUsername" placeholder="Username" name="username" value="<?php echo $getUinfo['username']; ?>">
		</div>
		
		<div class="form-group ">
			<label for="inputFirstName">Имя</label>
			<input type="text" class="form-control" id="inputFirstName" placeholder="First name" name="fname" value="<?php echo $getUinfo['fname']; ?>">
		</div>
		<div class="form-group ">
			<label for="inputLastName">Фамилия</label>
			<input type="text" class="form-control" id="inputLastName" placeholder="Last name" name="lname" value="<?php echo $getUinfo['lname']; ?>">
		</div>

		<div class="form-group">
			<label for="inputEmail4">Почта</label>
			<input readonly type="email" class="form-control" id="inputEmail4" placeholder="Email" name="email" value="<?php echo $getUinfo['email']; ?>">
		</div>

		<div class="form-group">
			<label for="inputMobile">Мобильный номер</label>
			<input type="phone" class="form-control" id="inputMobile" placeholder="Mobile Phone Number" name="mobilePhone" value="<?php echo $getUinfo['mobile']; ?>">
		</div>

		<br/>
		<div class="form-group">
			<button type="submit" class="btn btn-primary" name="update-info">Обновить информацию</button>
		</div>
			</form>
		</div>
	</div>


<hr/>

  <div class="row">
	  <h3>Обновите Ваш пароль</h3>
		<div class="col">
			<form action="/account" method="post">
				<div class="form-group">
					<label for="inputPasswordNew">Новый пароль</label>
					<input type="password" class="form-control" name="new-pass" id="inputPasswordNew">
				</div>
				<div class="form-group">
					<label for="inputPasswordNew2">Подтвердите пароль</label>
					<input type="password" class="form-control" name="verify-pass" id="inputPasswordNew2">
				</div>
				<br/>
				<div class="form-group">
					<button type="submit" class="btn btn-primary" name="update-password">Обновить пароль</button>
				</div>
			</form>
		</div>
	</div>
</div>
<hr/>

<?php
include(__DIR__.'/../include/footer.php'); 
?>