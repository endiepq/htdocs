<?php
class Admin{
    
    public function getUsers($limit=false){ // GET THE LIST OF ALL USERS
        global $pdo;
        if($limit){
            $q = "SELECT * FROM users LIMIT $limit";
        }else{
            $q = "SELECT id FROM users"; 
        }
        
        $stmt = $pdo->prepare($q);         
        if($stmt->execute()){
            $result = $stmt->fetchAll();
            return $result;
        }else{
            //echo mysqli_error($link);
            die("Database Error g");
        }
        return false;
    }

    public function getUser($userId){ // GET THE INFO OF A SINGLE USER
        global $pdo;
        $q = "SELECT * FROM users WHERE id = :userId"; 
        $stmt = $pdo->prepare($q);         
        if($stmt->execute(['userId'=>$userId])){
            $result = $stmt->fetch();
            return $result;
        }else{
            //echo mysqli_error($link);
            die("Database Error g");
        }
        return false;
    }

    public function updateUser($userId, $fname, $lname, $username, $email, $mobile, $isActive, $isAdmin, $isUser){ // USED TO EDIT THE USER INFO FROM ADMIN PANEL, RETURNS TRUE OR FALSE
        /*
        $isActive, $isAdmin, $isUser = true or false values. 
        */
        $isActive = $isActive ? 1 : 0;
        $isAdmin = $isAdmin ? 1 : 0;
        $isUser = $isUser ? 1 : 0;

        global $pdo;
        $q = "UPDATE `users` SET fname=:fname, lname=:lname, username=:username, email=:email, mobile=:mobile, isActive=:isActive, isAdmin=:isAdmin, isUser=:isUser, updated_at=NOW() WHERE id=:userId";
        $stmt = $pdo->prepare($q);
        if($stmt->execute(['userId'=>$userId,'fname'=>$fname,'lname'=>$lname,'username'=>$username,'email'=>$email,'mobile'=>$mobile,'isActive'=>$isActive,'isAdmin'=>$isAdmin,'isUser'=>$isUser])){
            if($stmt->rowCount()){
                return array('success'=>"Successfully Updated the Account");
            }else{
                return array('danger'=>"No Changes Were Made");
            }
        }else{
            return array('danger'=>"Some other error happen");
            //echo mysqli_error($link);
            die("Database Error g");
        }
    }

    public function changeAccountStatus($userId, $email){ // CHANGES THE isActive STATUS TO THE OPOSIVE, if it was in 1, changes to 0 
        global $pdo;
        $q = "UPDATE users SET `isActive` = NOT `isActive` WHERE id=:id AND email=:email";
        $stmt = $pdo->prepare($q);
        if($stmt->execute(['id'=>$userId, 'email'=>$email])){
            // check the number of affected rows.
            if ($stmt->rowCount()){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
            //echo mysqli_error($link);
            die("Database Error g");
        }
        return true;
    }

    public function getOptions($userId){
        global $pdo;
        $q = "SELECT * FROM options"; 
        $stmt = $pdo->prepare($q);         
        if($stmt->execute()){
            $result = $stmt->fetchAll();
            return $result;
        }else{
            //echo mysqli_error($link);
            die("Database Error g");
        }
        return false;
    }

    public function removeAccount($userId, $email){ // RETURNS array WITH MESSAGES danger OR success
        global $pdo;

        // delete query
        $sql = "DELETE FROM users WHERE id=:id AND email=:email LIMIT 1";
        $stmt = $pdo->prepare($sql);
        if($stmt->execute(['id'=>$userId, 'email'=>$email])){
            //echo $stmt->rowCount();
            if($stmt->rowCount() == 1){
                return array('success'=>"Successfully Deleted the Account.");
            }else{
                return array('danger'=>"Failed to make changes to the Account");
            }
        }else{
            return array('danger'=>"Database Error");
        }
    
    }



}
?>