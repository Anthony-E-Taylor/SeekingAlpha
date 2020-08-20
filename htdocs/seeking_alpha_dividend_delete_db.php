<!doctype html>
<html lang="en">

<body>

<?php
    require( 'header.html' );	 
    require( 'connect-db.php' );


//session_start();

// Parse https://seekingalpha.com/dividends/dividend-news for daily dividends.

$gsHelp = "
seeking_alpha_dividends.php - Extract the dividends' data for each SeekingAlpha stock and produce an .xml and .tsv file

Usage:    php.5.4  seeking_alpha_dividend.php   \\Vest\\etf\\Dividends\\SeekingAplha\\Data\\yyyymmdd.xml      >  \\vest\\etf\\Dividends\\SeekingAplha\\yyyymmdd.tsv
          php.5.4  seeking_alpha_dividend.php   \\Vest\\etf\\Dividends\\SeekingAplha\\Data\\yyyymmdd-pg*.xml  >  \\vest\\etf\\Dividends\\SeekingAplha\\yyyymmdd-pg*.tsv

 splExamples: php.5.4  seeking_alpha_dividend.php   \\Vest\\etf\\Dividends\\SeekingAplha\\Data\\20200202.xml      >  \\vest\\etf\\Dividends\\SeekingAplha\\2020202.tsv
          php.5.4  seeking_alpha_dividend.php   \\Vest\\etf\\Dividends\\SeekingAplha\\Data\\20200202-pg2.xml  >  \\vest\\etf\\Dividends\\SeekingAplha\\2020202-pg2.tsv
\n";

?>

<?php
    //set_error_handler( "MyErrorHandler" );
    libxml_use_internal_errors( true );                                

    
    if (isset($_SESSION['user']))
    {
        $msg = '';
        $load = '';
        $etf_to_update = '';
        $stocks = getAllStocks();
        $u = PrivilegedUser::getByUsername($_SESSION["user"]);


        if ( !empty(  $_POST [ 'db-btn' ] ) && $u->hasPrivilege("ALL") == true )
        {
                 if ( $_POST [ 'db-btn'] == "CREATE")    {   $create = create_table();  }
            else if ( $_POST [ 'db-btn'] == "INSERT")    {   $load   = load_table()  ;  }
            else if ( $_POST [ 'db-btn'] == "DROP"  )    {   $drop   = drop_table()  ;  }  
            else      $msg = "ERROR: Permission to insert denied";
        }

?>
 
 <?php   

    date_default_timezone_set('America/New_York');

   //--------------------------------------------------------------------------
   // Globals
   
   class g__
   {
      var  $aFnames  = null;
      var  $aDivCnts = null;

      function g__() {     // Constructor
          $this->stderr     = fopen( 'php://stderr', 'w' );      //  Depricated ??
          $this->bizDate    = strftime( "%Y-%m-%d" );
      }
   }

   $g = new g__;
   //$g->bTestMode = ( $argc > 1  &&  strcasecmp( $argv[ 1 ], "-test" ) == 0 );
   $g->currBizDate = strftime( '%Y-%m-%d %H:%M:%S' );
   $g->currDateYYYYMMDD = strftime( '%Y%m%d' );
   $g->dateDir = "./data";
   $g->asOfDate = null;
   $g->pageNum = 1;
   $g->pageDate = null;
   $g->fnameSuffix = "/*.xml";
   $g->xmlfname = null;
   $g->txtfname = "seeking_alpha_dividend-data-files.txt";
   //$g->jsonfname = "C:\Users\ANTHONY TAYLOR\angular-s20\inclass6\src\app\seek.json";
   $g->jsonfname = "seek.json";
   $g->csvfmat = '';
   $g->jsonfmat = '';
   $g->file = file_get_contents("selected_xmlfile.txt");
   $g->fileDate = rtrim( $g->file, ".xml" );
   $g->currDateYYYYMMDD = $g->fileDate;
      $g->deleteID ='';


global $g;
   
   ?>
   
   
   <form method="post" action="seeking_alpha_dividend.php" name="rmv-db"> 
            <input type="textarea" id="iD" value="<?php echo $g->deleteID;?>"name="iD"> 
            <pre><input type="submit" name="btnaction" value="<?php echo $g->deleteID ?>" class="btn-light btn-outline-info waves-effect border-left" title="SELECT * FROM <?php echo $_POST['iD'] ?>"</pre>
       </form>

          
<?php 

}
else 
   header('Location: seeking_alpha_dividend_login.php');
   // Force login. If the user has not logged in, redirect to login page
?>

<br>

 
   <?php include( 'footer.html' ); ?>
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
   <script type="text/javascript" src="seeking_alpha_dividend.js"></script>
