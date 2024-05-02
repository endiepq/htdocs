<?php

if (file_exists($directory.'/env.php')) {
  require($directory.'/env.php');
}else{
  die("Failed to find env file");
}

//SET required variables
date_default_timezone_set('America/Los_Angeles');

// SETUP PDO CONNECTION INSTANCE
 // Set DSN
 $dsn = 'mysql:host='. $host .';dbname='. $dbname;

 /////////// Create a PDO instance ///////////
try {
  $pdo = new PDO($dsn, $user, $passwd);
  $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
  $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
  //$pdo = null;
}catch (PDOException $e) {
  //echo "There was a Database Connection Issue ";
  print "Error!: " . $e->getMessage() . "<br/>";
  die();
}

/////////// Create a MYSQLI instance /////////// 
/*
$links = mysqli_connect($host, $user, $passwd, $dbname);
// Check connection
if (!$links) {
  die("Connection failed: " . mysqli_connect_error());
}

if (!mysqli_set_charset($links, "utf8")) {
  printf("Error loading character set utf8: %s\n", mysqli_error($link));
  die();
}
*/


/*
sql_query() is used to send sql queries to sql server through pdo. 
->accepts:
$query: the actual text query
$placeholders: array of placeholders for pdo
->returns:
On failed: 
 $data['error'] // contains the error details. 
On success: 
 $data['result'] // contains the result array
 $data['count'] // contains the total count
*/
function sql_query($query, $placeholders = false){
  $data = array();
  global $pdo;
  $letter = substr($query, 0, 1);
  
  try{
    $stmt = $pdo->prepare($query);
    //echo "Successfully Prepared<br/>";
  }
  catch(PDOException $e){
    $data['error'] = "Prepare Failed: ".$e->getMessage();
    return $data;
  }
  
  if(!$stmt){
    $data['error'] = "Query Failed";
    return $data;
  }
  
  try{
    if($placeholders){
      $stmt->execute($placeholders);
    }
    else{
      $stmt->execute();
    }
    
    $data['count'] = $stmt->rowCount();
    
    if($letter === "S"){
      if($data['count'] > 1){
        $data['result'] = $stmt->fetchAll();
      }else{
        $data['result'][] = $stmt->fetch();
      }
    }
    
  }
  catch(PDOException $e){
    $data['error'] = 'Connection failed: ' . $e->getMessage();
  }  
  return $data;
}

function smtpMail($to, $subject, $message){
  global $enableSmtp;
  global $url;
  global $smtpHost;
  global $smtpUsername;
  global $smtpPassword;
  global $smtpPort;
  global $smtpFrom;
  global $smtpFromName;

  if($enableSmtp){
    //require (__DIR__.'/include/phpMailer/Exception.php');
    require (__DIR__.'/include/phpMailer/SMTP.php');
    require (__DIR__.'/include/phpMailer/PHPMailer.php');

    $mail = new PHPMailer(true);
    $mail->setLanguage("en");
    $mail->SMTPDebug = 0;     
    $mail->isSMTP();   
    $mail->Host = $smtpHost; 
    $mail->SMTPAuth = true;                          
    $mail->Username = $smtpUsername;                  
    $mail->Password = $smtpPassword;                           
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = $smtpPort;                                   

    $mail->From = $smtpFrom; 
    $mail->FromName = $smtpFromName; 

    $mail->addAddress($to); 
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $message;

    //$mail->AltBody = "This is the plain text version of the email content";

    $SentEmail = false;
    try {
        $mail->send();
        $SentEmail = true;
        //echo "Send Succesfull";
        return array('success'=>"Succesfully send your Email");
    } 
    catch (Exception $e) {
        //echo "Mailer Error: " . $mail->ErrorInfo;
        $SentEmail = false;
        return array('danger' => "Failed to send email");
    }
  }
  else{
    return array('danger'=>"SMTP Is not Enabled");
  }
}

function timeago($date) {
  $timestamp = strtotime($date);	
  
  $strTime = array("second", "minute", "hour", "day", "month", "year");
  $length = array("60","60","24","30","12","10");

  $currentTime = time();
  if($currentTime >= $timestamp) {
  $diff     = time()- $timestamp;
  for($i = 0; $diff >= $length[$i] && $i < count($length)-1; $i++) {
  $diff = $diff / $length[$i];
  }

  $diff = round($diff);
  return $diff . " " . $strTime[$i] . "(s) ago ";
  }
}

/* this function picks up an array of messages from the code processing,
stores them in the session['msgs'] then
redirects to the same page
then loads the messages from session['msgs]
and displays them to the visitor. 

this is done to avoid the resubmit on refresh. 
*/
function msgs($msgs = null){
  global $currentUrl;

  // IF THERE IS A MESSAGE TO BE DISPLYED, DISPLAY IT.
  if(isset($_SESSION['msgs'])){
    // display the message
    $msgs = $_SESSION['msgs'];
    unset($_SESSION['msgs']); // unset the msg var so it wont be picked up again. 

    //echo "<pre>"; print_r($msgs); echo "</pre>"; 

    foreach($msgs as $msg){
      foreach($msg as $k=>$v){ ?>
          <div class="alert alert-<?php echo $k ?>" role="alert"><?php echo $v ?></div>
      <?php } }
  }
  // ELSE IF THERE ARE NEW MESSAGES REDIRECT TO DISPLAY THE MESSAGES. 
  elseif(isset($msgs)){  
    // save the msg to sessions
    $_SESSION['msgs'] = $msgs;
    // redirect to msg page
    //header("Location: ".$currentUrl."&".http_build_query(array('msgs' => $msg)));
    //echo $currentUrl."&".http_build_query(array('msgs' => $msg));
    header("Location: ".$currentUrl);
    die();
  }
}

/* displays the current url of the page, and removes a unwanted query value from url $page=21
$unwanted = array of unwanted query variables 
['page','count','etc']
*/
function currentUrl($val = null){ //param 
  $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]".$_SERVER['REQUEST_URI'];

  if(isset($val)){
    $base_url = strtok($url, '?');                // Get the base url
    $parsed_url = parse_url($url);                // Parse it
    if(!empty($parsed_url['query'])){
      $query = $parsed_url['query'];              // Get the query string
      parse_str( $query, $parameters );           // Convert Parameters into array
      foreach($val as $v){         
        unset( $parameters[$v] );                 // Delete the one you want
      }
      $new_query = http_build_query($parameters); // Rebuilt query string
      if(!empty($new_query)){
        echo $base_url.'?'.$new_query;            // Finally url is ready
      }else{
        echo $base_url;
      }
    }else{
      echo $base_url;
    }
  }else{
    echo $url;
  }
}

?>
