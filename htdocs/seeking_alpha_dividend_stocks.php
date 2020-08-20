<?php
// Extract daily dividend files from .\data 

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
      foreach ( $aFnames as $fname ) {
         $g->aFnames[] = realpath( $fname );
         $g->aBnames[] = basename( $fname );
         $g->aDnames[] = preg_replace( '/[^(202\d{5})]/', '', $fname );
      }
   
   for ($i = 0; $i < count ( $g->aBnames ); $i++)
    {
   //  if ( !strpos( $g->aBnames[ $i ], '-' ))
     $g->csvfmat .= $g->aBnames[ $i ] . "," . $g->aDnames [$i ] . "\r\n";
     echo $g->aFnames[ $i ] . " &nbsp; " . $g->aBnames[ $i ] . " &nbsp; " . $g->aDnames[ $i ] ."<br/>";
	}
            file_put_contents( $g->txtfname, rtrim( $g->csvfmat, ',' ));
            
        //  create_dir_table();
            load_dir_table();
            load_stocks_table();

// Sort in ascending order - this is default
//$a = scandir($dir);

// Sort in descending order
$g->xmlfname = scandir($g->dateDir,1);

//print_r($g->xmlfname);
?>