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
            else if ( $_POST [ 'db-btn'] == "QUERY" && isset($_POST [ 'query'] ))   {  $query  = query($_POST['query']);  }  
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
   $g->fileDate = trim(rtrim( $g->file, ".xml" ));
   $g->currDateYYYYMMDD = $g->fileDate;

   //--------------------------------------------------------------------------
/*
   for ( $i = 1;   $i < $argc;  ++$i  )
   {
      $aFnames = glob( $argv[ $i ] );
      foreach ( $aFnames as $fname ) {
         $g->aFnames[] = realpath( $fname );
         $g->aFnames[] = realpath( $fname );
      }
   }

   if ( $g->aFnames == null ) 
   {
         MyLog( "INFO", "------------------------------------------------------------------------------" );
         MyLog( "INFO", "Help started; Current Biz-Date= $g->currBizDate " );
         MyLog( "INFO", $gsHelp );
   }

   
   MyLog( "INFO", "------------------------------------------------------------------------------" );
   MyLog( "INFO", "$argv[0] started; Current Biz-Date= $g->currBizDate " );


          // Pass|Open downloaded Dividend's yyyymmdd-pg.xml file as an argument 
          // Examples:       L:\Vest\etf\Dividends\SeekingAplha\Data\20002020.xml
          //                 L:\Vest\etf\Dividends\SeekingAplha\Data\20002020-pg2.xml

    
   //foreach ( $g->aFnames as $xmlFname )
   //{   
*/

            // Log out option. End Session | Clear Cookies
?>
             
       <?php
      if(isset($_POST['iD'])) 
      {
      $_ID = preg_replace('/[ A-z]/', '', $_POST['iD']);
      delete_data($_ID);}
       ?>

<?php 

          $xmlfname = "$g->dateDir/$g->currDateYYYYMMDD.xml";//$argv[1];
        //$xmlfname = "C:/xampp/htdocs/Data/20200330.xml";//$argv[1];
          $fileContents = file($xmlfname);
 
             // Remove encoding='utf-16' from first line of downloaded Dividend's yyyymmdd-pg.xml file
             // Examples:       <?xml version='1.0' encoding='utf-16'>

         array_shift($fileContents);

 
             // Replace first line of downloaded Dividend's yyyymmdd-pg.xml file
             // Examples:       <?xml version='1.0'>

         $utf16 = array( "<?xml version='1.0' ?>", "\n" );
         array_splice($fileContents, 0, 0, $utf16);
      

               $newContent =       implode      (  ""  ,      $fileContents);
               $newContent =       str_replace  ( "\n" ,  "", $newContent  );
               
               $newContent = trim( preg_replace ( '/</', "\n<", $newContent));
               $newContent = trim( strip_tags   ( $newContent , '<html><div><span><title><ul>'));

               $newContent = trim( preg_replace ( "/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $newContent));   

               $newContent =       preg_replace ( '/<\s+/',  ""   ,  $newContent);
               $newContent =       preg_replace ( '/>\s+/',  "> " ,  $newContent);
               
               $newContent = trim( str_replace  (  "\n"   ,  ""   ,  $newContent));
               $newContent = trim( preg_replace (  '/</'  ,  "\n<",  $newContent));
               
               $newContent =       preg_replace ( '/&(?!#?[a-z0-9]+;)/',   '&amp;',   $newContent);

         $xmlSuffix  = "-redacted.xml";
         $xmlPrefix  = "$g->dateDir/$g->currDateYYYYMMDD";
         $xmlRedact  = $xmlPrefix . $xmlSuffix;

            file_put_contents($xmlRedact, "<?xml version='1.0' ?>\n".$newContent."</html>");
      
            $contents = utf8_encode( file_get_contents( $xmlRedact ));
            $xml      = simplexml_load_string         ( $contents  ) ;


                // Query XML nodes 
                // Examples:      <div class="title">
                //                        <a href="/news/3549424-sabine-royalty-trust-declares-0_2366-dividend" sasource="title_mc_quote">Sabine Royalty Trust declares $0.2366 dividend</a>
                //                </div>
                //                <div class="bullets item-summary hidden">
                //                        <ul>
                //                              <li>Sabine Royalty Trust (NYSE:
                //                                    <a href="https://seekingalpha.com/symbol/SBR" title="Sabine Royalty Trust">SBR</a>) declares 
                //                                    <a target="_blank" href="https://seekingalpha.com/pr/17802306-sabine-royalty-trust-announces-monthly-cash-distribution-for-march-2020">$0.2366/share monthly dividend</a>, 
                //                                          <font color="green">8.1% increase</font> from prior dividend of $0.2189.
                //                              </li>
                //                              <li class="remove-link-for-mobile">
                //                                    <a target="_blank" href="https://seekingalpha.com/symbol/SBR/dividends/yield?source=news_bullet">Forward yield</a> 8.31%
                //                              </li>
                //                              <li>Payable March 30; for shareholders of record March 16; ex-div March 13.</li>
                //                </div>
      
               $asOf   = $xml->xpath("//ul[ @class = 'mc-list' ]"); 
               $cur    = $xml->xpath("//div[ starts-with( @class, 'bullets' )]/ul");  //////li[1]");
               $cr2    = $xml->xpath("//div[ @class='title']");
               $di2    = $xml->xpath("//div[ @class='title']");
               $dat    = $xml->xpath("//div[ starts-with( @class, 'bullets' )]/ul");  //////li[2]");
               $dis    = $xml->xpath("//div[ starts-with( @class, 'bullets' )]/ul");  //////li[1]");
               $div    = $xml->xpath("//div[ starts-with( @class, 'bullets' )]/ul");  //////li[1]");
               $py_    = $xml->xpath("//div[ starts-with( @class, 'bullets' )]/ul");  //////li[2]");
               $pay    = $xml->xpath("//div[ starts-with( @class, 'bullets' )]/ul");  //////li[last()-1]");
               $rec    = $xml->xpath("//div[ starts-with( @class, 'bullets' )]/ul");  /////li[last()-1]");
               $exD    = $xml->xpath("//div[ starts-with( @class, 'bullets' )]/ul");  //////li[last()-1]");
               $dv2    = $xml->xpath("//div[ @class='title']");
               $yld    = $xml->xpath("//div[ starts-with( @class, 'bullets' )]/ul");  //////li[]"); 
               $sec    = $xml->xpath("//div[ starts-with( @class, 'bullets' )]/ul");  //////li[2]"); 
               $fnd    = $xml->xpath("//div[ starts-with( @class, 'bullets' )]/ul");  //////li[1 and contains ( text(), '(')]");
               $xch    = $xml->xpath("//div[ starts-with( @class, 'bullets' )]/ul");  //////li[1]");
               $sym    = $xml->xpath("//div[ starts-with( @class, 'mc-share-info' )]/span[ starts-with( @class, 'rem-with-summaries hidden' )]/div[ starts-with( @data-tweet, '$' )]/@data-tweet"); 
               $tme    = $xml->xpath("//div[ starts-with( @class, 'mc-share-info' )]/span[starts-with ( @class, 'item-date' )]"); 
               $uni    = $xml->xpath("//div[ starts-with( @class, 'bullets' )]/ul/node()");// | //div[ starts-with( @class, 'bullets' )]/ul/preceding-sibling::li[last()]");
               $fnt    = $xml->xpath("//div[ starts-with( @class, 'bullets' )]/ul/");  //////li/font");// | //div[ starts-with( @class, 'bullets' )]/ul/preceding-sibling::li[last()]");
   //}


      //----------------------------------------------------------------------------
      //  Pass verbose error_reporting to MyLog
function MyErrorHandler( $errno, $errstr, $errfile, $errline )
{
   global $g;
   MyLog( "FATAL", "**MyErrorHandler**: $errfile, Line $errline, Error $errno: $errstr/n" );
   exit( 1 );
}

//----------------------------------------------------------------------------
// Example : MyLog( "Trace:", "Executing: $xmlCmd : As Of " . $g->currBizDate . "\n" );


function MyLog( $level, $text )
{
   global $g;

   if ( strlen( $text ) == 0 ) {
      return;
   }

   $logText = sprintf( "%-5s %s", $level, $text );
   if ( substr( $text, -1 ) != "\n" ) {
      $logText .= "\n";
   }

   list( $msec, $time ) = explode( " ", microtime());
   $logTime = strftime( "%H:%M:%S", (int) $time ) . substr( (string) $msec, 1, 4 );

   //fputs( STDERR,  "$logTime $logText" );

   if ( isset( $g->logFile )  &&  $g->logFile )
   {
      fputs( $g->logFile, "$logTime SeekingAlpha  $logText" );
      fflush( $g->logFile );
   }
}



       //--------------------------------------------------------------------------
       //   Create json-file and csv-file with Seeking Alpha filenames,dates from .\data\*.xml  
       //   Examples:       20200416.xml,20200416
       //                   20200416-db.xml,20200416
       //                   20200416-redacted.xml,20200416
  

       function create_xmlfnames_dates_csvfile()
       {
          global $g;
          
          $g->jsonfmat = "[\n";
          $g->aJsonfile = array();
          $aFnames = glob( $g->dateDir.$g->fnameSuffix  );
          foreach ( $aFnames as $fname ) 
          {
             $g->aFnames[] = realpath( $fname );
             $g->aBnames[] = basename( $fname );
             $g->aDnames[] = preg_replace( '/[^(202\d{5})]/', '', $fname );
             
             if(  !strpos(   basename( $fname ), "-", 8 )) 
             {
             $g->afnames[] = basename( $fname );
             $g->aJnames[] = preg_replace( '/[^(202\d{5})]/', '', $fname );
             }
          }
   
          for ($i = 0; $i < count ( $g->aBnames ); $i++)
                    $g->csvfmat  .= $g->aBnames[ $i ] . "," . $g->aDnames [ $i ] . "\r\n";           
        // echo $g->aFnames[ $i ] . " &nbsp; " . $g->aBnames[ $i ] . " &nbsp; " . $g->aDnames[ $i ] ."<br/>";

    
         for ($i = 0; $i < count ( $g->aJnames ); $i++)
         {
                $g->aJsonfile = array( 'file'=>$g->afnames[ $i ], 'date'=>$g->aJnames[ $i ] );
                $g->jsonfmat .= json_encode( $g->aJsonfile, JSON_PRETTY_PRINT ).",\n";
         }   
          

          file_put_contents( $g->txtfname, rtrim( $g->csvfmat, ',' ));
          
          $g->jsonfmat  =  trim( $g->jsonfmat );
          $g->jsonfmat  = rtrim( $g->jsonfmat, ',' );
          $g->jsonfmat .= "\n]";

          //echo "<br/><div class = 'row d-flex justify-content-center'><pre>". $g->jsonfmat . "</pre></div><br/>";//json_encode( $g->aJsonfile( $g->aBnames, $g->aDnames ));
           
          file_put_contents( $g->jsonfname, $g->jsonfmat );
       }    


      //----------------------------------------------------------------------------
      //  ARRAY PRETTY PRINTER 
     function myprint_r($my_array) 
     {
        global $g;
  
        if (is_array($my_array)) {
            echo "<table border=1 cellspacing=0 cellpadding=3 width=100%>";
            echo "<tr><td colspan=2 style='background-color:#333333;'><strong><font color=white>$g->currDateYYYYMMDD DB LOG</font></strong></td></tr>";
            foreach ($my_array as $k => $v) 
            {
                        echo '<tr><td valign="top" style="width:40px;background-color:#F0F0F0;">';
                        echo '<strong>' . $k . "</strong></td><td>";
                        myprint_r($v);
                        echo "</td></tr>";
             }
            echo "</table>";
            return;
        }
        echo $my_array;
    }

      //----------------------------------------------------------------------------
      //  Create new XML tags and nodes from the downloaded Dividend's yyyymmdd-pg.xml file
      //  Examples:        <asOf>2020-03-10
      //	                        <stock id="6">
      //		                           <ticker>WEYS</ticker>
      //		                           <fund>Weyco</fund>
      //		                           <exchange>NASDAQ</exchange>
      //	                              <currency>USD</currency>
      //		                           <dividend>0.2400</dividend>
      //		                           <distribution>quarterly</distribution>
      //		                           <yield>4.570</yield>
      //		                           <sec>      </sec>
      //		                           <payable>2020-03-31</payable>
      //		                           <record>2020-03-20</record>
      //		                           <ex_dividend>2020-03-19</ex_dividend>
      //	                        </stock>
      //                   </asOf>

         function createXmlTag($tag, $node)
         {
               return "\t\t <" . $tag . ">" . $node . "</" . $tag . ">\n";
         }


      //----------------------------------------------------------------------------
      //  Load stock attributes into XML && TSV files
      //  Examples:         $aSym[$i];       
      //                    addStock( $aSym[$i], "ticker", "%-10.10s\t", $num, createXmlTag('ticker', $aSym[$i]), $newXmlfname);
      //
      //                                                                                                   XML                              TSV
      //                    $fund = preg_split( '/[(]/', $aFnd[$i] );                                               
      //                    $fund = trim(preg_replace('/\/\K\(.*/', '$1', $fund[0]));                <stock id="15">               
      //                    $fund = preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $fund);             <ticker>CVS</ticker>            15   CVS    CVS Health
      //                   		                                                                    <fund>CVS Health</fund>    
      //                    addStock( $fund,  $newXmlfname,   createXmlTag( 'fund', $fund ),  "%-20.20s\t" );

      

      //----------------------------------------------------------------------------
      function tsvHeader( $tsvFmt, $attribute, $carriage)
      {
        //echo str_pad($attribute, $tsvFmt, " ", STR_PAD_BOTH).$carriage;
        //printf( $tsvFmt, $attribute);
      }
      
      //----------------------------------------------------------------------------
      function tokenizeDates( $match, $date )
      {
         $date = trim( preg_replace( $match, '', $date ));
         $date = preg_split( '/[;|,|.]/', $date );
         $date = strftime( '%Y-%m-%d', strtotime( $date[0] )); 

         return $date;
      }

      //----------------------------------------------------------------------------
      function addStock( $match, $newXmlfname, $node, $tsv )
      {
        fputs(  $newXmlfname, $node );
        printf( $tsv, $match);
      }
        
      //----------------------------------------------------------------------------
      function formatStock( $newXmlfname, $id )
      {
         $stockID = "\t<stock" . ' id="' . $id . '">' . "\n";
         fputs($newXmlfname, $stockID);
         printf( "%-3.3s\t" , $id );
      }

      //----------------------------------------------------------------------------
      function distribution_via_Regex( $distribution )
      {        
         $distribution = trim( preg_replace( '/[?&-_*!@#\$%\^&*().]/'        ,  ' ' ,  $distribution ));
         $distribution = trim( preg_replace( '/^.*declare.? /'               ,  ''  ,  $distribution ));
         $distribution = trim( preg_replace( '/dividend.*|distribution.*/'   ,  ''  ,  $distribution ));
         $distribution = trim( preg_replace( '/(share|ADS)/'                 ,  ''  ,  $distribution ));
          
            switch ($distribution) 
            {
               case 'semi annual':    break;
               case 'quarterly'  :    break;
               case 'monthly'    :    break;
               case 'interim'    :    break;               
               case 'special'    :    break;
               case 'weekly'     :    break;
               case 'annual'     :    break;
               case 'final'      :    break;
               default           :    $distribution = "";      
            }                
         
         return $distribution;
      }
           
      //----------------------------------------------------------------------------   
      //  Test for matched content from Xpath <ul> Query
      
           function ulContains( $ulXml, $match )
       {
          $ulXml = trim($ulXml);
          $match = str_replace( "'", '', $match );
        
          return strrpos( $ulXml, $match ) > 0 ? true : false;
       }

      //----------------------------------------------------------------------------
      //  Parse Xpath <ul> Query for dates

      function dates_via_regex( $match, $date, $attribute )
      {
             if( ulContains( $date, $attribute ))
             {         
                switch ( $attribute )
                {
                  case "'Payable'"    :   $date = tokenizeDates( $match, $date );                      break;        
                  case "'record'"     :   $date = tokenizeDates( $match, $date );                      break;    
                  case "'ex-div'"     :   $date = tokenizeDates( $match, $date );                      break;  
                  default           :   $date = ''; 
                }
             }
            
            else $date = '';
             
             if ( $date == "1969-12-31" ) 
                  $date =  "";
         
            return $date;
      }
      
      //----------------------------------------------------------------------------
      //  Parse Xpath <ul> Query for:  Forward Yield | 30-Day SEC Yield  

      function yield_via_Regex( $yield, $regex )
      {   
          switch ( $regex )
          {
            case "'/.*yield /'"    : $yield = trim( preg_replace(  '/.*yield /'  ,  '',   $yield ));    break;        
            case "'/.*Yield of /'" : $yield = trim( preg_replace(  '/.*Yield of/',  '',   $yield ));    break;    
          }
          
          $yield = trim( preg_replace( '/%.*/',  '',   $yield ));                         
         
          if( is_numeric( $yield ) == true)  $yield = number_format     ( $yield, 3 );   
          else $yield = '';     
         
         return $yield;
      }
      
      //----------------------------------------------------------------------------
      //  Tokenize string by (;) 
      //  Examples:         <li>Payable March 30; for shareholders of record March 12; ex-div March 11.</li>
      //                    <li>30-Day SEC Yield of 1.33% as of Feb. 26.</li>
      //                                                                  
      //
      // Parse the date 
      // Examples:  jan   21, 2020
      //            feb.  21, 2020
      //            March 21, 2020
      //            April 21, 2020 30-Day SEC
      //
     
      function _Date( $date, $element, $match, $newXmlfname, $tag, $tsv, $attribute)
      {
            if ( $tag == 'sec' )
            {
               if( ulContains( $date, $attribute ))
               {
                  $date = preg_replace( '/.*\% as of /'  , '',  $date ); // 30-Day SEC  
                  $date = preg_replace ('/\K\.[A-z].*/' , '', $date );
                  $date = strftime('%Y-%m-%d', strtotime($date));
               }
               //echo "\n\n*****************************\n$date*****************************\n\n";
                       
                     
              else 
               {
                  $date = '';
               }
            } 
         
              else  $date = dates_via_regex( $match, $date, $attribute );
              
        

 ?>
        <td class='date'> <?php  addStock( $date,  $newXmlfname,   createXmlTag( $tag, $date ),  $tsv ); }?> </td>

 <?php 


       
   //----------------------------------------------------------------------------
   //  Create|Replace XML file and add a well-formed XML decleration 
   //  Examples:        20200302-db.xml
   //                   20200312-db.xml


$date_db = date("Ymd") . "-db.xml";

$newXmlfname = fopen( "$g->dateDir/$date_db", "w+" );

fputs($newXmlfname, "<?xml version='1.0' ?>\n");



      //----------------------------------------------------------------------------
      //  add|Format 'as of date' from the downloaded Dividend's yyyymmdd-pg.xml file to new XML asOf node
      //  Examples:        <asOf> As of: 2020-03-10 
      //                   <asOf> As of: 2020-02-02 	                        

      //function asOfDate()
      //{
      //echo "\n******\n$asOf[0]\n*****\n";
      

      $g->asOfDate = str_replace  ( "\n" ,  "________", $asOf[0] );
      $g->asOfDate = preg_replace ( "/________.*/",'', $g->asOfDate );
         $asOfDate = strftime     ( '%Y-%m-%d', strtotime( $g->asOfDate ));
                     fputs        ( $newXmlfname, "<asOf>" . $asOfDate . "\n");

         tsvHeader(    2,   'id'          , "\t" );
         tsvHeader(   22,   'Ticker'      , "\t" );
         tsvHeader(   10,   'Exchange'    , "\t" );
         tsvHeader(    6,   'Currency'    , "\t" );
         tsvHeader(   10,   'Dividend'    , "\t" );
         tsvHeader(   12,   'Distribution', "\t" );
         tsvHeader(    8,   'Yield'       , "\t" );
         tsvHeader(   10,   '30-Day SEC'  , "\t" );
         tsvHeader(   10,   'Payable'     , "\t" );
         tsvHeader(   10,   'Record'      , "\t" );
         tsvHeader(   12,   'Ex-Dividend' , "\t" );
         tsvHeader(   10,   'Timestamp'   , "\t" );
         tsvHeader(   25,   'Fund'        , "\n" );
      //}


?>

		<br/>

  
  <div class="container my-4">
     
        <div class = 'row d-flex justify-content-center'>
	      <form action="<?php $_SERVER['PHP_SELF'] ?>" method="get">
            <pre><input type="submit" name="btnaction" value="CREATE" class="btn-outline-success waves-effect border-right" title="CREATE TABLE <?php echo $g->currDateYYYYMMDD?>;"/></pre>
          </form>
          
        &nbsp; &nbsp; &nbsp; &nbsp;

          <form action="seeking_alpha_dividend.php" method="post">
		    <pre><input type="submit"  name="db-btn"   value="INSERT" class="btn-light btn-outline-warning waves-effect border-right" title="INSERT * INTO <?php echo $g->currDateYYYYMMDD?>;" /></pre>         
                <small class="text-danger"><?php echo $msg ?></small>
	      </form>

        &nbsp; &nbsp; &nbsp; &nbsp;
      
        <form action="<?php $_SERVER['PHP_SELF'] ?>" method="get">
          <pre><input type="submit" name="btnaction" value="DROP" class="btn-light btn-outline-danger waves-effect border-left" title="DROP FROM <?php echo $g->currDateYYYYMMDD?>;"/></pre>
        </form>

        &nbsp; &nbsp; &nbsp; &nbsp;
      
        <form action="<?php $_SERVER['PHP_SELF'] ?>" method="get">
          <pre><input type="submit" name="btnaction" value="LOG" class="btn-light btn-outline-info waves-effect border-left" title="SELECT * FROM log_<?php echo $g->currDateYYYYMMDD ?>"<a href="seeking_alpha_dividend_logfile.php" target="popup" onclick="window.open('seeking_alpha_dividend_logfile.php','name','width=600,height=400')"LOG</a>
</pre>
        </form>


       <br/><br/>
    </div>
    <br/>         

    <hr/>

<?php 



if (isset($_GET['btnaction']  ))
{	
   try 
   { 	
      switch ( $_GET['btnaction'] ) 
      {   
         case 'CREATE': echo create_table();  break;
         case 'INSERT': load_table()  ;  break;
         case 'select': selectData()  ;  break;
         case 'update': updateData()  ;  break;
         case 'delete': deleteData()  ;  break;
         case 'LOG'   :  break;//myprint_r( log_table() ); break;
         case 'DROP'  : echo drop_table()  ;  break;      
      }
   }
   catch (Exception $e)       // handle any type of exception
   {
      $error_message = $e->getMessage();
      echo "<p>Error message: $error_message </p>";
   }   
}
?>
    <div class = 'row d-flex justify-content-center'>
<?php
echo $load;
?>
</div>

  </div>
   



   <hr/>
    <br/><br/>
    <div class = 'row d-flex justify-content-center'>
    <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post">

    <textarea id="textArea1" class='autoExpand form-control' rows='1' cols='75%'  placeholder='Enter SQL Query' name="query" tabindex="1" autofocus></textarea>
    <br/> <input type="submit" name="db-btn" value="QUERY" class="btn-light btn-outline-info waves-effect border-left btn-block" title="SELECT * FROM log_<?php echo $g->currDateYYYYMMDD ?>"\> 
        </form>
        </div>
<br/>
<br/>
<br/>
<br/>
<a>
  <script>
  $("#alphaTable tr").click(function () {
    $(this).toggleClass("selected");
});</script>
<section class="container">
   <?php include( 'seeking_alpha_dividend_filter.html' ); ?>
<i><strong><caption> 
            <?php echo  "As of: ". preg_replace( '/Today -|2020\K.* /', '',  $g->asOfDate ); ?>
        </caption></strong></i></a>
        
<table id="alphaTable"  class="order-table table sortable"> <!--table table-condensed">-->
    <thead>
       <!-- set table headers -->
       <tr>
            <th onclick="sortTable(0)"> <button type="button" class="btn btn-link">iD          </th>
            <th onclick="sortTable(1)"> <button type="button" class="btn btn-link">Ticker      </th>
            <th onclick="sortTable(2)"> <button type="button" class="btn btn-link">Exchange    </th>
            <th onclick="sortTable(3)"> <button type="button" class="btn btn-link">Currency    </th>
            <th onclick="sortTable(4)"> <button type="button" class="btn btn-link">Dividend    </th>
            <th onclick="sortTable(5)"> <button type="button" class="btn btn-link">Distribution</th>
            <th onclick="sortTable(6)"> <button type="button" class="btn btn-link">%Yield      </th>
            <th onclick="sortTable(7)"> <button type="button" class="btn btn-link">30-Day SEC  </th>
            <th onclick="sortTable(8)"> <button type="button" class="btn btn-link">Payable     </th>
            <th onclick="sortTable(9)"> <button type="button" class="btn btn-link">Record      </th>
            <th onclick="sortTable(10)"> <button type="button" class="btn btn-link">Ex-Div     </th>
            <th onclick="sortTable(11)"> <button type="button" class="btn btn-link">Timestamp  </th>
            <th onclick="sortTable(12)"> <button type="button" class="btn btn-link">Fund       </th>
            <th><button type="button" class="btn btn-sm">Remove</button></th>
         <!--  <th align="center" valign="center"><b>UNION</b></th> -->
       </tr>
  </thead>
  <tbody>


   <!--<form action="seeking_alpha_dividend.php" method="post"> -->
   
   <?php

      //----------------------------------------------------------------------------
      //  Populate an array foreach matched xpath query from the downloaded Dividend's yyyymmdd-pg.xml file 
  	                        

foreach ( $sym as $syms => $s ) { $aSym[] = $s; }
foreach ( $yld as $ylds => $y ) { $aYld[] = $y; }
foreach ( $pay as $pays => $p ) { $aPay[] = $p; }
foreach ( $py_ as $py_s => $a ) { $aPy_[] = $a; }
foreach ( $rec as $recs => $r ) { $aRec[] = $r; }
foreach ( $exD as $exDs => $x ) { $aExD[] = $x; }
foreach ( $dat as $dats => $t ) { $Date[] = $t; }
foreach ( $cur as $curr => $c ) { $aCur[] = $c; }
foreach ( $div as $divs => $v ) { $aDiv[] = $v; }
foreach ( $dis as $dist => $d ) { $aDis[] = $d; }
foreach ( $fnd as $fund => $f ) { $aFnd[] = $f; }
foreach ( $di2 as $dst2 => $i ) { $aDs2[] = $i; }
foreach ( $sec as $sec_ => $e ) { $aSec[] = $e; }
foreach ( $xch as $xchg => $h ) { $aXch[] = $h; }
foreach ( $dv2 as $div2 => $n ) { $aDv2[] = $n; }
foreach ( $cr2 as $cur2 => $y ) { $Cur2[] = $y; }
foreach ( $tme as $time => $m ) { $aTme[] = $m; }

?>
<form action="seeking_alpha_dividend.php" method="post">
<?php

      //----------------------------------------------------------------------------
      //  Parse each stock from the downloaded Dividend's yyyymmdd-pg.xml file 
for ( $i = 0 ; $i < count( $aSym ) ; $i++ ) 
{ 

    // Load XML file w/ opening stock tag|id  
    // Examples:               <stock id="1">
    //                         <stock id="77">
    //                         <stock id="194">

 ?>
     <tr title="<?php echo $aSym[$i]?>"> 
        <td class='id'> <?php formatStock( $newXmlfname, $i+1 ); ?> 
   
 <?php  
      

    // Load XML file w/ ticker tag|stock symbol  
    // Examples:       <ticker>DKS</ticker>
    //                 <ticker>BAC.PL</ticker>
    //                 <ticker>OTCQX:CSVI</ticker>
    
   
          $ticker = ltrim(preg_replace('/\K-.*/', '', $aSym[$i]), '\$');
          $ticker = preg_replace('/\$/', ' | ', $ticker);
     
 ?>
        <td class='ticker'> <?php addStock( $ticker,  $newXmlfname,   createXmlTag( 'ticker', $ticker ),  "%-22.22s\t" ); ?> </td>
   
 <?php  


    // Load XML file w/ exchange tag|stock exchange   
    // Examples:       <exchange>NYSE</exchange>
    //                 <exchange>NASDAQ</exchange>
    //                 <exchange>NYSEMKT</exchange>


    
    $exchange = $aXch[$i];
    if ( stripos( $exchange, '(' ) == true && stripos( $exchange, '(' ) == true )
    {
       $exchange = preg_split('/[(]/', $exchange);
       $exchange = trim(preg_replace('/\/\K\(.*/', '', $exchange[1])); //exchange 
         if ( strpos( $exchange, ':' ) == true )
         { 
          $exchange = strtok( $exchange, ':');
         }
         else 
         {
         $exchange = '';
         }
    }
    else $exchange = '';
    
 ?>
 
 <td class='exchange'> <?php  addStock( $exchange,  $newXmlfname,   createXmlTag( 'exchange', $exchange ),  "%-8.8s\t" ); ?> </td>

 <?php     
 
    // Load XML file w/ currency tag|stock currency   
    // Examples:       <currency>USD</currency>
    //       (         <currency>USD</currency>
    //                 <currency>BRL</currency>
    
    
    $currency  = preg_split ( '/\//', preg_replace ( '/^.*\).?/', '', $aCur[$i] )); 
    $currency2 = $Cur2[$i]; 
    
       if ( stripos( $currency[0], 'declares') === 0 )
       {
         $currency = trim(preg_replace( '/[a-z,\%\d\?\.]/', '', $currency[0]));
            if ( $currency === '$' )
            {
               $currency = 'USD';
            }
       }
      else if ( strpos( $currency2, 'declares') == true )
      {
            $currency = trim(preg_replace( '/.*declares.?[a-z\s]+/', '', $currency2));
            $currency = trim(preg_replace( '/[0-9\.].*/', '', $currency));
            if ( $currency === '$' )
            {
               $currency = 'USD';         
            }
      }

    else if ( strpos( $currency[0], 'declared') == true )
      {
            $currency = trim(preg_replace( '/[a-z,\%\d\?\.]/', '', $currency[0]));
            if ( $currency === '$' )
            {
               $currency = 'USD';
            }
      }

        else if ( strpos( $currency2, 'declared') == true )
      {
            $currency = "-----HELP----";
      }
        
    else $currency =  '';
   
                    /*        echo "____________$currency2____________";

                   $currency = trim(preg_replace('/^[A-z]+[-a-z%\d\'.,?]/', '', $currency[0])); 
                                  $currency = trim((preg_replace( '/[a-z\%\d\.\!\@\?\#\%\^\&\*\(\),-\:]/', '', $currency )));
                                  $currency = trim((preg_replace( '/[-\:]/', '', $currency )));
               if (strpos($currency,  "$ ") && $currency == "$") 
               { addStock( 'USD',  $newXmlfname,   createXmlTag( 'currency', 'USD' ),  "%8.8s\t" ); }



               if (!empty($currency))  
                    if ( !strpos($currency, 'declares' )) 
               addStock( '',  $newXmlfname,   createXmlTag( 'currency', '' ),  "%8.8s\t" );}
                              else if (strpos($currency, 'CAD')) { addStock( 'CAD',  $newXmlfname,   createXmlTag( 'currency', 'CAD' ),  "%8.8s\t" ); }
      
                       else                         { addStock( $currency,  $newXmlfname,   createXmlTag( 'currency', $currency ),  "%8.8s\t" );} 
                   }    */
     

 ?>

 <td class='currency'> <?php addStock( $currency,  $newXmlfname,   createXmlTag( 'currency', $currency ),  "%8.8s\t" ); ?> </td>

 <?php       
 
    // Load XML file w/ dividend tag|stock dividend amount   
    // Examples:       <dividend>0.0013</dividend>
    //                 <dividend>3.2500</dividend>
    //                 <dividend>18.1250</dividend>
    
    
   // echo $aDiv[$i];

   $dividend  = $aDiv[$i];
   $dividend2 = $aDv2[$i];
 
             if (strpos($dividend, 'declares') == true)
             {
      //       $dividend  =  preg_replace('/[(\d.+%)]/', '', $aDiv[$i]);
       $dividend  =  preg_replace('/^.*declares /', '', $dividend);
       $dividend  =  preg_replace('/\/.*/', '', $dividend);
       
       $dividend  =  trim(preg_replace('/[^\d.?\.\d+]/', '', $dividend),'.|\'|,|?');
       //");  /////
       $dividend  =  number_format( $dividend, 4 );
       }
       else if (strpos($dividend, 'declared') == true)
      {
      //       $dividend  =  preg_replace('/[(\d.+%)]/', '', $aDiv[$i]);
         $dividend  =  preg_replace('/^.*declared /', '', $dividend);
         $dividend  =  preg_replace('/\/.*/', '', $dividend);
       
         $dividend  =  trim(preg_replace('/[^\d.?\.\d+]/', '', $dividend),'.|\'|,|?');
         //");  /////
            if( is_numeric( $dividend ))
            {
               $dividend  =  number_format( $dividend, 4 );
            }
            else
            {
            $dividend = '';
            }
      }
       //sprintf( '%1.4f', $dividend );    
                            //                    $divi_tsv = $dividend;      
          else if (strpos($dividend2, 'declares') == true) 
           
             { 
           //$dividend = trim(preg_replace( '/(([0-9]*)|(([0-9]*)\.([0-9]*)))/', '', $aDv2[$i]));
          
             $dividend = trim(preg_replace( '/.*declares.?[A-z\s]+/', '', $dividend2));
             $dividend = trim(preg_replace( '/[^0-9\.]+/', '', $dividend));
             $dividend  =  number_format( $dividend, 4 );
          }
           else $dividend = '';

 ?>
 
 <td class='dividend'> <?php addStock( $dividend,  $newXmlfname,   createXmlTag( 'dividend', $dividend ),  "%9.9s\t" ); ?> </td>

 <?php    
           
 
    // Load XML file w/ distribution tag|stock distribution   
    // Examples:       <distribution>weekly</distribution>
    //                 <distribution>monthly</distribution>
    //                 <distribution>annually</distribution>
    
           if ( !strpos( $aDis[$i], 'declares' )    &&   !strpos( $aDis[$i], 'declared' )   && 
                !strpos( $aDs2[$i], 'declares' )    &&   !strpos( $aDs2[$i], 'declared' )) 
           {
              $distribution = '';
           }
            

      else if (  strpos( $aDis[$i], 'declares' )    ||    strpos( $aDis[$i], 'declared' ))     $distribution = distribution_via_Regex( $aDis [$i] );

      else if (  strpos( $aDs2[$i], 'declares' )    ||    strpos( $aDs2[$i], 'declared' ))     $distribution = distribution_via_Regex( $aDs2 [$i] );
      
      else {} //$distribution = '';

 ?>
 
 <td class='distribution'> <?php  addStock( $distribution,  $newXmlfname,   createXmlTag( 'distribution', $distribution ),  "%-12.12s\t" ); ?> </td>

 <?php   
               
 
    // Parse node for SEC yield or Forward yield 
    // Examples:  <li>30-Day SEC Yield of 4.03% as of Feb. 26.</li>
    //            <li>Payable Mar 05; for shareholders of record Mar 03; ex-div Mar 02.</li>
    //
    //
    // Load XML file w/ yield tag| yield   
    // Examples:       <yield></yield>
    //                 <yield>9.5100</yield>
    //                 <yield>19.8800</yield>

         $yield = $aYld[$i];
 
      //$yield = explode( ' ', $yield );
      //$yield = end    (      $yield    );
       switch ($yield) 
       {
	         case !strpos   ( $yield, '%'):                        $yield = '';	                                          break;
	         
            case  strpos   ( $yield, 'Forward yield') == true :   $yield = yield_via_Regex( $yield, "'/.*yield /'"   );    break;
               
            case  strpos   ( $yield, 'SEC Yield'    ) == true :   $yield = yield_via_Regex( $yield, "'/.*Yield of /'" );   break; 
            
            default:         $yield    = '';                                         
       }


 ?>
 
 <td class='yield'> <?php addStock( $yield,  $newXmlfname,   createXmlTag( 'yield', $yield ),  "%6.6s\t" ); ?> </td>

 <?php   
    
    
    // Load XML file w/ date tags|dates   
    // Examples:   <sec></sec>
	 //         	 <payable></payable>
	 //	          <record>2020-03-15</record>
	 //         	 <ex-dividend>2020-03-12</ex-dividend>
   
       _Date( $Date[$i], '', '/.*as of.?/'    , $newXmlfname, 'sec'         , "%-10.10s\t"  ,   "SEC"       );
       _Date( $aPay[$i],  0, '/.*\bPayable\b/', $newXmlfname, 'payable'     , "%-10.10s\t"  ,   "'Payable'" );   
       _Date( $aRec[$i],  1, '/.*\brecord\b/' , $newXmlfname, 'record'      , "%-10.10s\t"  ,   "'record'"  );
       _Date( $aExD[$i],  2, '/.*\bex-div\b/' , $newXmlfname, 'ex_dividend' , "%-10.10s\t"  ,   "'ex-div'"  );

       
 
      // Load XML file w/ timestamp tag|stocks' listing timestamp   
      // Examples:       <timestamp>weekly</timestamp>
      //                 <distribution>monthly</timestamp>
      //                 <timestamp>annually</timestamp>
      //
      //
      // Parse the timestamp 
      // Examples:  Today, 11:43 AM
      //            Thu, Mar. 12, 4:23 PM
      //            Fri, Mar. 13, 5:01 PM
      //            Fri, Feb. 14, 4:02 AM
      //
      //                 <div class="media-body">
      //                    <div class="mc-share-info">
      //                       <span class="rem-with-summaries hidden"></span>
      //                       <span class="item-date">Thu, Mar. 12, 4:23 PM</span>
      //                       <span class="mc_gray_separator">|</span>
      //                       <span class="comments-n market_current_comment//"></span>
      //                    </div>
      //                 </div>

       $timestamp = strftime('%Y-%m-%d %H:%M', strtotime( $aTme[$i] ));

 ?>
 
 <td class='timestamp'> <?php  addStock( $timestamp, $newXmlfname, createXmlTag( 'timestamp', $timestamp ), "%-20.20s\t" ); ?> </td>

 <?php   

    // Load XML file w/ fund tag|stock name  
    // Examples:       <fund>Weyco</fund>
    //                 <fund>Marsh &amp; McLennan</fund>
    //                 <fund>Bank of America Pfd Shs Series L</fund>

    $fund = preg_split('/[(]/', $aFnd[$i]);
    $fund = trim(preg_replace('/\/\K\(.*/', '$1', $fund[0])); //fund  
    $fund = preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $fund);

 ?>
 <?php 
 
 ?>
        <td class='fund'> <?php  addStock( $fund,  $newXmlfname,   createXmlTag( 'fund', $fund ),  "%-75.75s\n" ); ?> </td>
      <!--         <td>
          <form action="seeking_alpha_dividend.php" method="post">
            <input type="submit" value="X" name="delete" class="btn" />      
            <input type="hidden" name="ticker" value="<?php echo $stock['ticker'] ?>" />
          </form>
        </td>           -->
         <td class="rmv" name="X"> <button onclick=" openForm();  deleteRow(this);" class="btn btn-link "><b> X </b></button>
        </td>
 <?php      
    // Load|format XML file w/ closing stock tag 
    // Example:  </stock>
  
   fputs($newXmlfname, "\t</stock>\n");
}

    // Load|format XML file w/ closing As Of Date tag 
    // Example:  </asOf>
fputs($newXmlfname,"</asOf>\n");
create_xmlfnames_dates_csvfile();

?>

    </tr>
  
<?php 

}
else 
   header('Location: seeking_alpha_dividend_login.php');
   // Force login. If the user has not logged in, redirect to login page
?>

</tbody>
</table>
</section>
</div>

<br>


<div class="form-popup" id="myForm">
    <form method="post" action="" class="form-container" name="rmv-db">
              <h4>Confirm</h4>

              <input type="hidden" id="deletePost" value="<?php echo preg_replace('/[ A-z]/','',$_POST['iD'])?>"name="iD"> 
          <pre><input type="submit" name="btnaction" id="iD"  class="btn-warning"</pre>
          
 <button type="button" class="btn-danger" onclick="closeForm()">Close</button>
    </form>
</div>


 
   <?php include( 'footer.html' ); ?>
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
   <script type="text/javascript" src="seeking_alpha_dividend.js"></script>

</body>
</html>
