
<?php
   // require( 'header.html' );	   
	//require('connect-db.php');
    //require('seek-db.php');
// Parse https://seekingalpha.com/dividends/dividend-news for daily dividends.

$gsHelp = "
seeking_alpha_dividends.php - Extract the dividends' data for each SeekingAlpha stock and produce an .xml and .tsv file

Usage:    php.5.4  seeking_alpha_dividend.php   \\Vest\\etf\\Dividends\\SeekingAplha\\Data\\yyyymmdd.xml      >  \\vest\\etf\\Dividends\\SeekingAplha\\yyyymmdd.tsv
          php.5.4  seeking_alpha_dividend.php   \\Vest\\etf\\Dividends\\SeekingAplha\\Data\\yyyymmdd-pg*.xml  >  \\vest\\etf\\Dividends\\SeekingAplha\\yyyymmdd-pg*.tsv

 splExamples: php.5.4  seeking_alpha_dividend.php   \\Vest\\etf\\Dividends\\SeekingAplha\\Data\\20200202.xml      >  \\vest\\etf\\Dividends\\SeekingAplha\\2020202.tsv
          php.5.4  seeking_alpha_dividend.php   \\Vest\\etf\\Dividends\\SeekingAplha\\Data\\20200202-pg2.xml  >  \\vest\\etf\\Dividends\\SeekingAplha\\2020202-pg2.tsv
\n";

?>


<?php session_start(); // make sessions available 
   
//if (isset($_SESSION['user']))
//{

global $g;

 header('Access-Control-Allow-Origin: http://localhost:4200');
//header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Authorization, Accept, Client-Security-Token, Accept-Encoding');
header('Access-Control-Max-Age: 1000');  
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT');
   //header('Location: seeking_alpha_dividend.php');

//retrieve data from the request
//$str=
$getdata = $_GET[ 'str' ]; //assume params named 'str'

// change from JSON -> PHP array
$request = json_decode($getdata);

/*
foreach ($request as $k => $v)
{
  $g->date[0]['get_'.$k] = $v;
}
*/
// send reponse in JSON fmat
//echo $getdata;
file_put_contents("/xampp/htdocs/selected_xmlfile.txt", $getdata);

echo json_encode( $getdata );
   //}
//else 
  // header('Location: seeking_alpha_dividend_login.php');
   // Force login. If the user has not logged in, redirect to login page
?>

