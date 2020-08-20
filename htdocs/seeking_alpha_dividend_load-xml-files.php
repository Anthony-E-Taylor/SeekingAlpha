<?php
// Extract local xml_files from .\data 

$gsHelp = "
seeking_alpha_dividends.php - Extract the dividends' data for each SeekingAlpha stock and produce an .xml and .tsv file

Usage:    php.5.4  seeking_alpha_dividend.php   \\Vest\\etf\\Dividends\\SeekingAplha\\Data\\yyyymmdd.xml      >  \\vest\\etf\\Dividends\\SeekingAplha\\yyyymmdd.tsv
          php.5.4  seeking_alpha_dividend.php   \\Vest\\etf\\Dividends\\SeekingAplha\\Data\\yyyymmdd-pg*.xml  >  \\vest\\etf\\Dividends\\SeekingAplha\\yyyymmdd-pg*.tsv

Examples: php.5.4  seeking_alpha_dividend.php   \\Vest\\etf\\Dividends\\SeekingAplha\\Data\\20200202.xml      >  \\vest\\etf\\Dividends\\SeekingAplha\\2020202.tsv
          php.5.4  seeking_alpha_dividend.php   \\Vest\\etf\\Dividends\\SeekingAplha\\Data\\20200202-pg2.xml  >  \\vest\\etf\\Dividends\\SeekingAplha\\2020202-pg2.tsv
\n";

    include( 'header.html' );	   
    require('connect-db.php');
    require('seek-db.php');
    //require('seeking_alpha_dividend_stocks.php');
    //require('seeking_alpha_dividend_load-xml-files.php');
    
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
   $g->csvfmat = '';

   //--------------------------------------------------------------------------

             $aFnames = glob( $g->dateDir.$g->fnameSuffix  );
   foreach ( $aFnames as $fname ) 
   {      $g->aFnames[] = realpath( $fname );
          $g->aBnames[] = basename( $fname );
          $g->aDnames[] = preg_replace( '/[^(202\d{5})]/', '', $fname );
   }
   
   for ( $i = 0; $i < count ( $g->aBnames ); $i++)
       { $g->csvfmat .= $g->aBnames[ $i ] . "," . $g->aDnames [$i ] . "\r\n"; }
    
    file_put_contents( $g->txtfname, rtrim( $g->csvfmat, ',' ));
    
    echo trim(__FILE__, '.php');


$g->xmlfname = scandir( $g->dateDir );  // Sort in ascending  order - default
$g->xmlfname = scandir($g->dateDir,1);  // Sort in descending order


//  print_r($g->xmlfname);
//  if ( !strpos( $g->aBnames[ $i ], '-' ))
//  echo $g->aFnames[ $i ] . " &nbsp; " . $g->aBnames[ $i ] . " &nbsp; " . $g->aDnames[ $i ] ."<br/>";
	}
?>

