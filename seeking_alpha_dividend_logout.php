<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" />
  
  <title><font color="deep-orange"><i><?php if(isset($_COOKIE['user']))echo $_COOKIE ['user']  ?> </i></font> $_SESSION[] | $_COOKIE[]  Logout</title>    
</head>
<body>
  

 <?php require('header.html'); ?>
 <?php require('connect-db.php'); ?>



 <div class="container" style="float:center; padding:50px;">

 <div class = 'row d-flex justify-content-center'>

    <h5> <font color="deep-orange"><i><?php if(isset($_COOKIE['user']))echo $_COOKIE ['user']  ?> </i></font> Successfully Logged Out </h5>
   </div>
 </div>   
 
 <?php
          unset_Cookies();
 function unset_Cookies()
 {
     if (count($_COOKIE) > 0)
    {
       echo "<br/><div class = 'row d-flex justify-content-center'><br/><pre><strong> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \$_COOKIE['INFO']</strong><hr/><br/>" . print_r($_COOKIE, TRUE) . '</pre></div><br/><br/><br/>';


       foreach ($_COOKIE as $key => $value)
       {
   	      // Deletes the variable (array element) where the value is stored in this PHP.
   	      // However, the original cookie still remains intact in the browser.
   	      unset($_COOKIE[$key]);    
   	
          // To completely remove cookies from the client, set the expiration time to be in the past
          setcookie($key, '', time() - 3600);  
       }
   
       // redirect to the unset $_SESSION 
       unset_Sessions();    
    }
 }

 function unset_Sessions()
 {
    // Set session variables can be removed by specifying their element name to unset() function.
    // A session can be completely terminated by calling the session_destroy() function.

    if (count($_SESSION) > 0)     // Check if there are session variables
    {  
       echo "<div class = 'row d-flex justify-content-center'>
            <br/><br/>
            <pre><strong> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \$_SESSION['INFO']</strong><hr/><br/>" . print_r($_SESSION, TRUE) . '</pre>
            </div><br/><br/><br/><br/>';

       foreach ($_SESSION as $key => $value)
       {
          // Deletes the variable (array element) where the value is stored in this PHP.
          // However, the session object still remains on the server.    	
          unset($_SESSION[$key]);
       }      
       session_destroy();     // complete terminate the session
   
       // redirect to the login page immediately 
       // header('Location: login.php');

       // redirect with 5 seconds delay
       //header('refresh:5; url=localhost/seeking_alpha_dividend_login.php');
    }
 }
 ?>           

 
</body>
<?php require ('footer.html'); ?>

</html>
