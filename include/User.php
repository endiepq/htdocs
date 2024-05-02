<?php
// user class that handles user login and info.
class User{
    private $id;
    private $roles;
    private $email;
    private $authenticated;
    private $isAdmin;
    private $isUser;


    public function __construct(){
        $this->id = NULL; // being used by: registerLoginSession(), 
        $this->email = NULL; // being used by: editAccountInfo(), 
        $this->authenticated = false;
        $this->isAdmin = false;
        $this->isUser = false;
    }

    public function editAccountPassword(string $newPass){ // RETURNS array WITH MESSAGES danger OR success
        global $pdo;
        
        if(!$this->isPasswdValid($newPass)){
            return array('danger' => "Invalid New Password");
        }

        //UPDATE THE PASSWORD WITH THE NEW PASSWORD
        $newHash = password_hash($newPass, PASSWORD_DEFAULT);
        $q = "UPDATE `users` SET password=:newHash WHERE id=:id AND email=:email";
        $stmt = $pdo->prepare($q);
        if($stmt->execute(['id'=>$this->id, 'newHash'=>$newHash, 'email'=>$this->email])){
            if($stmt->rowCount() == 1){
                return array('success'=>"Successfully Updated Your Password.");
            }else{
                return array('danger'=>"Failed to Update your password");
            }
        }else{
            return array('danger'=>"There was a Database Issue!");
        }      
    }

    private function isPasswdValid($passwd){ // RETURNS TRUE OR FALSE /being used by: editAccountPassword(), 
        /* Example check: the length must be between 8 and 16 chars */
        $len = mb_strlen($passwd);
        if ($len < 8){
            return false;
        }
        return true;
    }
    
    public function editAccountInfo($username, $fname, $lname, $email, $mobile){ // RETURNS array WITH MESSAGES danger OR success
        global $pdo;

        $username = filter_var(trim($username), FILTER_SANITIZE_STRING);
        $fname = filter_var(trim($fname), FILTER_SANITIZE_STRING);
        $lname = filter_var(trim($lname), FILTER_SANITIZE_STRING);
        $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
        $mobile = filter_var(trim($mobile), FILTER_SANITIZE_STRING);

        if(empty($username)){
            return array('danger'=>"Username Must Not be Empty");
        }elseif(empty($fname)){
            return array('danger'=>"First Name Must Not be Empty");
        }elseif(empty($lname)){
            return array('danger'=>"Last Name Must Not be Empty");
        }elseif(empty($email)){
            return array('danger'=>"Invalid Email");
        }elseif($email != $this->email){
            return array('danger'=>"Wrong Email");
        }else{
            // Update/delete from
            $sql = "UPDATE users SET fname=:fname, lname=:lname, username=:username, mobile=:mobile WHERE email=:email";
            $stmt = $pdo->prepare($sql);
            if($stmt->execute(['fname'=>$fname, 'lname'=>$lname, 'username'=>$username, 'mobile'=>$mobile, 'email'=>$email])){
                //echo $stmt->rowCount();
                if($stmt->rowCount() == 1){
                    return array('success'=>"Successfully Updated Your Information.");
                }else{
                    return array('danger'=>"No Change was recorded!");
                }
            }else{
                return array('danger'=>"Database Error");
            }
        }
    }

	public function login(string $email, string $passwd): array{ //RETURNS array with msg. 
        global $pdo;
        $q = "SELECT * FROM users WHERE (email = :email) AND (isActive = 1) LIMIT 1";
        //$sql = 'SELECT * FROM posts WHERE id = :id';
        $stmt = $pdo->prepare($q);
        $stmt->execute(['email' => $email]);
        //echo $post->body;
        if($stmt->rowCount() == 1){
            $row = $stmt->fetch();
            if(password_verify($passwd, $row['password'])){
                $this->authenticated = true;
                $this->id = intval($row['id'], 10);
                //$name = $row['name'];
                if($this->registerLoginSession()){
                    return array('success'=>"Successfully loged in.");
                }else{
                    return array('danger'=>"Failed to setup Session.");
                }
            }
            else{
                return array('danger'=>"Wrong password");
            }
        }else{
            return array('danger'=>"Failed to Find Account,<br/> or your account might need to be activated!");
        }
	}

    private function registerLoginSession(): bool{ // RETURNS TRUE OR FALSE /being used by: login(),
        global $pdo;
        if(session_status() == PHP_SESSION_ACTIVE){
            //use replace statement to:
            // insert a new row with the session id, if it doesnt exist or
            // update the row having thes session id if it does exists.
            $sessionId = session_id();
            $q = "REPLACE INTO `sessions` (session_id, account_id, login_time) VALUES ('$sessionId', '$this->id', NOW())";
            $stmt = $pdo->prepare($q);
            if($stmt->execute()){
                return true;
            }else{
                return false;
                echo mysqli_error($pdo);
                die("Database Error");
            }
        }
        return false;
    }

    public function isAdmin(){
        return $this->isAdmin;
    }   

    public function addAccount(string $fname, string $lname, string $username, string $email, string $passwd){ // RETURNS array WITH MESSAGES danger OR success
        global $url;
        global $pdo;

        $fname = filter_var(trim($fname), FILTER_SANITIZE_STRING);
        $lname = filter_var(trim($lname), FILTER_SANITIZE_STRING);
        $username = filter_var(trim($username), FILTER_SANITIZE_STRING);
        $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);;
        $passwd = trim($passwd);

        if(empty($fname)){
            return array('danger'=> "Invalid First Name");
        }elseif(empty($lname)){
            return array('danger'=> "Invalid Last Name");
        }elseif(empty($username)){
            return array('danger'=> "Invalid Username");
        }elseif(empty($email)){
            return array('danger'=> "Invalid Email");
        }else{
            // verify passsword. 
            if(!$this->isPasswdValid($passwd)){
                return array('danger' => "Invalid Password, the length must be between 8 and 16 chars");
            }

            // check if account with same email exists
            if($this->getIdFromEmail($email)){ 
                return array('danger' => "Email Address already exists");
            }

            // check if username exists
            if($this->usernameExists($username)){
                return array('danger'=>"Username Already Exists");                
            }

            $hash = password_hash($passwd, PASSWORD_DEFAULT);

            // Generate random 32 character hash and assign it to a local variable.// Example output: f4552671f8909587cf485ea990207f3b
            $code = md5( rand(0,1000) ); 

            $q = "INSERT INTO `users`(fname, lname, username, email, password, code, isActive, isAdmin, created_at) VALUES (:fname, :lname, :username, :email, :hash, :code, 0, 0, NOW())";
            $stmt = $pdo->prepare($q);
            $stmt->execute(['fname'=>$fname,'lname'=>$lname,'username'=>$username,'email'=>$email,'hash'=>$hash,'code'=>$code]);

            //echo "\nPDOStatement::errorInfo():\n";
            //print_r($stmt->errorInfo());
            
            if($stmt->rowCount() > 0){

                $last_id = $pdo->lastInsertId();

                // BUILD VERIFICATION EMAIL 
                $subject = 'Signup | Verification';
                $message = '
                Thanks for signing up!<br/>
                Your account has been created successfully.<br/>
                <hr/>
                                  
                Please click this <a href="'.$url.'/register?email='.$email.'&code='.$code.'">link</a> to activate your account:<br/>
                or copy and paste this url on your browser:<br/>
                '.$url.'/register?email='.$email.'&code='.$code.'
                    ';
                    $send = smtpMail($email, $subject, $message);
                    return $send;
            }else{
                
                //echo $q."<br/>";
                //echo mysqli_error($link);
                return array('danger' => "There was a Database Issue!");
            }
        }    
        return array('danger' => "There was an Issue");
    }


    function getIdFromEmail(string $email): ?int{ // RETURNS FALSE OR id NUMBER
        // returns false or ID number
        global $pdo;
        
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        if(empty($email)){
            return false;
        }

        $q = "SELECT id FROM `users` WHERE email = :email ";
        $stmt = $pdo->prepare($q);
        $stmt->execute(['email'=>$email]);
        $user = $stmt->fetch();
    


        if($stmt->rowCount() == 1){
            return true;
        }
        else{
            //echo mysqli_error($link);
            return false;
            die("Database Error");
        }
    }

    function usernameExists(string $username): ?int{ // RETURNS FALSE OR id NUMBER
        // returns false or ID number
        global $pdo;
        
        $email = filter_var($username, FILTER_SANITIZE_EMAIL);
        if(empty($email)){
            return false;
        }

        $q = "SELECT id FROM `users` WHERE username = :username LIMIT 1";
        $stmt = $pdo->prepare($q);
        $stmt->execute(['username'=>$username]);
        $user = $stmt->fetch();
    
        if($stmt->rowCount() == 1){
            return true;
        }
        else{
            //echo mysqli_error($link);
            return false;
            die("Database Error");
        }
    }

    public function sessionLogin(): bool{ // RETURNS TRUE AND SETS id, email, authenticated and roles. 
        global $pdo;

        if (session_status() == PHP_SESSION_ACTIVE){
            $sessionId = session_id();

            $q = "SELECT * FROM sessions, users WHERE (sessions.session_id = '$sessionId') " . "AND (sessions.login_time >= (NOW() - INTERVAL 7 DAY)) AND (sessions.account_id = users.id) " . "AND (users.isActive = 1)";
            //echo $q;
            if($stmt = $pdo->prepare($q)){
                $stmt->execute();
                $user = $stmt->fetch();
                if($stmt->rowCount() == 1){
                        $row = $user; //mysqli_fetch_assoc($r);
                        $this->id = intval($row['account_id'], 10);
                        $this->email = $row['email'];
                        $this->authenticated = TRUE;
                        $this->isAdmin = ($row['isAdmin'] == 1 ? true : false);
                        $this->isUser = ($row['isUser'] == 1 ? true : false);
                    
                        //$this->roles = $row['roles'];
                        return true;
                    
                }else{
                    return false;
                    //echo mysqli_error($link);
                    die("Database Error S");
                }
            }else{
                // query failed
                die("There was a Database Error");
            }
            return false;
        }
    }

    public function getUserInfoById(){ //RETURNS false OR info object
        global $pdo;

        // ID is setup at the class level: $this->id;
        $q = "SELECT id, fname, lname, username, email, mobile, isActive FROM users WHERE id = :id LIMIT 1";  
        $stmt = $pdo->prepare($q); 
        
        //$result =  // get the mysqli result
        //$user = $result->fetch_assoc(); // fetch data  

        if($stmt->execute(['id'=>$this->id])){
            if($stmt->rowCount() == 1){
                $res = $stmt->fetch();
                //var_dump($res);
                return $res;
            }else{
                return false;
            }
        }
        else{
            //echo mysqli_error($link);
            die("Database Error g");
        }
        return false;
    }
     ///////////////////////////////////// functions converted to PDO ^^

    public function logout(){
        global $pdo;
        $this->id = NULL;
        $this->name = NULL;	
        $this->authenticated = FALSE;
        
        if (session_status() == PHP_SESSION_ACTIVE){
            $sid = session_id();
            $q = 'DELETE FROM sessions WHERE `session_id` = "'.$sid.'"';
            //echo $q;
            $stmt = $pdo->prepare($q);

            if($stmt->execute()){
                return true;            
            }
            else{
                //echo mysqli_error($link);
                return false;
                die("Database Error Login");
            }
        }
    }

    private function validateEmail($email){
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        if(empty($email)){
            return false;
        }
        return true;
    }

    function verifyAccount($email, $code){ // RETURNS ARRAY: array('success'=>"Success Message")
        global $pdo;
        $code = filter_var($_GET['code'], FILTER_SANITIZE_STRING);
        $email = filter_var($_GET['email'], FILTER_SANITIZE_EMAIL); 

        if(!empty($code) && !empty($email)){

            $q = "UPDATE `users` SET isActive=1 WHERE code=:code AND email=:email";
            $stmt = $pdo->prepare($q);


            if($stmt->execute(['code'=>$code,'email'=>$email])){
                $affected = $stmt->rowCount();
                //$affected = mysqli_affected_rows($link);
                if($affected == 1){
                    return array('success' => "Activated your account successfully.");
                }elseif($affected == 0){
                    return array('danger' => "Failed to Activate your Account.");
                }elseif($affected < 1){
                    //echo $q;
                    return array('danger'=> "Failed to Activate your account");
                    //echo mysqli_error($link);
                    //die();
                }
            }else{
                //echo $q;
                //echo mysqli_error($link);
                return array('danger' => "There was a Database Issue!");
            }
        }
    }
}

?>