<?php
    require( 'seek-db.php' );
    require( "seeking_alpha_dividend_role.php" );
    require( "seeking_alpha_dividend_privilegedUser.php" );

    

session_start();

if (isset($_SESSION['user']))
{

/******************************/
// connecting to GCP cloud SQL instance

// $username = 'root';
// $password = 'your-root-password';

// $dbname = 'your-database-name';

// if PHP is on GCP standard App Engine, use instance name to connect
// $host = 'instance-connection-name';

// if PHP is hosted somewhere else (non-GCP), use public IP address to connect
// $host = "public-IP-address-to-cloud-instance";


/******************************/
// connecting to DB on XAMPP (local)


$username = 'root';
$password = '';
$host = 'localhost:3306';
$dbname = 'seeking_alpha';

/******************************/
// connecting to DB on CS server

// $username = 'your-computingID';
// $password = 'password-received-from-cshelpdesk';
// $host = 'cs4750.cs.virginia.edu';
// $dbname = 'your-computingID';


/******************************/

$dsn = "mysql:host=$host;dbname=$dbname";

/** connect to the database **/
try 
{
   $db = new PDO($dsn, $username, $password);   
    echo "<pre><small> &nbsp; &nbsp; &nbsp; &nbsp; Database Connected <br/> <font color='deep-orange'> " .$_SESSION['user']. " </font>is an Authorized User</small>".create_log()."</pre>";

}
catch (PDOException $e)     // handle a PDO exception (errors thrown by the PDO library)
{
   // Call a method from any object, 
   // use the object's name followed by -> and then method's name
   // All exception objects provide a getMessage() method that returns the error message 
   $error_message = $e->getMessage();        
   echo "<p>An error occurred while connecting to the database: $error_message </p>";
}
catch (Exception $e)       // handle any type of exception
{
   $error_message = $e->getMessage();
   echo "<p>Error message: $error_message </p>";
}
}
?>

<?php 
// To close a connection, uncomment the following line
// $db = null;
?>