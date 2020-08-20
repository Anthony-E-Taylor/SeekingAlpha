<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" />
  
  <title>Seeking Alpha Dividend Login</title>    
</head>
<body>
 <?php require('header.html'); ?>
     <pre> Access Restricted to Authorized Users</pre>
       <br/><br/>

  <div class="container">
    <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post">
      Username: <input type="text" name="username" class="form-control" autofocus placeholder="Case_Sensitive" required /> <br/>
      Password: <input type="password" name="pwd" class="form-control" placeholder="!alpha_Numeric" required /> <br/>
      <input type="submit" value="Sign in" class="btn btn-block btn-light"  />   
    </form>
  </div>
  <br/>

  

<?php session_start();    // make sessions available
// Session data are accessible from an implicit $_SESSION global array variable
// after a call is made to the session_start() function.

// session_start() -- check if there exists a session, if there is, join that session
//                    if no session exists, create one

// every web component that expects to share the session must start a session 
// (~just like you want to use the club's facility, you must first join the club) 
?>

<?php

date_default_timezone_set('America/New_York');

// When an HTML form is submitted to the server using the post method,
// its field data are automatically assigned to the implicit $_POST global array variable.
// PHP script can check for the presence of individual submission fields using
// a built-in isset() function to seek an element of a specified HTML field name.
// When this confirms the field is present, its name and value can usually be
// stored in a cookie. This might be used to stored username and password details
// to be used across a website

// Define a function to handle failed validation attempts
function reject($entry)
{
    
    switch ($entry) {
	    case $_POST['username']    :   echo " &nbsp; &nbsp; <i><strong><font color = 'red'>User</font></i> is unauthenticated.<br/>";     break;
	    case $_POST[  'pwd'   ]    :   echo " &nbsp; &nbsp; <i><strong><font color = 'red'>Username</font></i> or <font color = 'red'>password </font></i>is invalid.<br/>";     break;
    
   
}

//    echo 'provide message why the user cannot proceed <br/>';
//   exit();    // exit the current script, no value is returned
}

				
if ($_SERVER['REQUEST_METHOD'] == "POST" )
{

   if (  strlen($_POST['username']) > 0)   
   $user = trim($_POST['username']);
   
   if ($user == 'CS_4640' || $user == 'CS_4750')   // ctype_alnum() check if the values contain only alphanumeric data
   $_SESSION[  'user'  ] = $user;

   else    reject( $_POST   ['username']);
   
   if (   isset($_POST['pwd']) )
    $pwd = trim($_POST['pwd']) ;
   
      if ($pwd != '!ctype_alnum' )
         reject($_POST['pwd']);
   
      else
      {
         // set session attributes
         
//          $hash_pwd = md5($pwd);     
//          $hash_pwd = password_hash($pwd, PASSWORD_DEFAULT);     // password_hash requires PHP >= 5.5
         //$hash_pwd = password_hash($pwd, PASSWORD_BCRYPT);
         $hash_pwd = "$2y$10\$DQLCwivVdcPj.CcToXWareI2tF6xuzOhCat1UOcLnMsk0q0zDopp6";    //password_hash($pwd, PASSWORD_BCRYPT);
         
         $_SESSION['pwd'] = $hash_pwd;
       }

          if (isset($_SESSION['user']) && isset($_SESSION['pwd']) )
          {

         // setcookie(name, value, expiery-time)
         // setcookie() function stores the submitted fields' name/value pair
         setcookie('user', $user, time()+3600);
         
         setcookie('pwd', md5($pwd), time()+3600);  // create a hash conversion of password values using md5() function   // CS server uses PHP 5.4
//          setcookie('pwd', password_hash($pwd, PASSWORD_DEFAULT), time()+3600);    // password_hash() requires at least PHP5.5
//          setcookie('pwd', password_hash($pwd, PASSWORD_BCRYPT), time()+3600);         
					
         // redirect the browser to another page using the header() function to specify the target URL
         header('Location: http://localhost:4200');
      }

      else //header('Location: http://localhost/seeking_alpha_dividend_login.php');
      {
      // reject( $_POST   ['username']);
       //reject( $_POST   [   'pwd'  ]);

	  }

}
?>
  <br/><br/>
  <br/><br/>


</body>
<?php require ('footer.html'); ?>

</html>
