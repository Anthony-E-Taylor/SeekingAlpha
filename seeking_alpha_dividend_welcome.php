<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" />
  
  <title>Welcome <?php $_SESSION['user'] ?></title>    
    <style>
      a:hover { background-color:white; }
      label { padding: 4px 10px 0px 4px; }       
      .msg { margin-left:40px; font-style: italic; color: red; font-size:0.8em;}
    </style>
 
    <script type="text/javascript">
      function setFocus()
      {
    	  document.forms[0].elements[0].focus();
      }
    </script>
</head>
<body>

<?php require('header.html'); ?>
<?php require('connect-db.php'); ?>

 
<!-- <?php session_start(); // make sessions available ?> -->

<?php date_default_timezone_set('America/New_York');


global $g;

if (isset($_SESSION['user']))
{
   $g->file = file_get_contents("selected_xmlfile.txt");
   $g->fileDate = trim(rtrim( $g->file, ".xml" ));
   $g->currDateYYYYMMDD = $g->fileDate;

        $asOfDate = strftime( '%Y-%m-%d', strtotime(  $g->fileDate ));
        $today = "\$_GET [ 'Data' ] As Of $asOfDate";
        
echo create_log();


?>      
     <!-- <pre>  <font color="deep-orange" style="font-style:italic"> <?php echo $_SESSION['user'] ?> </font>is an Authorized User</pre> -->

<br/>

<?php 
}else 
   header('Location: seeking_alpha_dividend_login.php');
   // Force login. If the user has not logged in, redirect to login page
?>

<div class="container" style="float:center; padding:140px;">
   <div class = 'row d-flex justify-content-center'>
        <h5>
          <a href="seeking_alpha_dividend.php" title="$_GET Data"> <?php echo $today?></a>
          or <a href="seeking_alpha_dividend_logout.php">log out</a>.     
        </h5>
    </div>
</div>

</body>

<?php

// The implicit $_SESSION global array variable stores session data (name-value pairs)
// in an associative array of keys and values.
// To view the session data, loop through the array, or
// using var_dump() function

// uncomment the following code to see the session data

// Check if there is an param-value pair in the session object 
// if (count($_SESSION) > 0)
// {
// 	echo '<dl>';
// 	foreach ($_SESSION as $key => $value)
// 	{
// 		echo "<dt>Key: $key";
// 		echo "<dd>Value: $value";
// 	}
// 	echo '</dl><hr />';
// 	var_dump($_SESSION);
// }
// else
// 	echo 'Please <a href="login.php" >Log in</a>';

require ('footer.html');
// ?>

</html>
