<?php 
    //set_error_handler( "MyErrorHandler" );
    libxml_use_internal_errors( true );
    date_default_timezone_set('America/New_York');

    
      //----------------------------------------------------------------------------
      //  Pass verbose error_reporting to MyLog

   //--------------------------------------------------------------------------
   // Globals

   $g = new stdclass();
   //$g = new g__;
   //$g->bTestMode = ( $argc > 1  &&  strcasecmp( $argv[ 1 ], "-test" ) == 0 );
   $g->currBizDate = strftime( '%Y-%m-%d %H:%M:%S' );
   $g->currDateYYYYMMDD = strftime( '%Y%m%d' );
   $g->dateDir = "./data";
   $g->asOfDate = null;
   $g->pageNum = 1;
   $g->pageDate = null;
   $g->fnameSuffix = "/*.xml";
   $g->dateDBSuffix = "-db.xml";
   $g->xmlfname = null;
   $g->txtfname = "seeking_alpha_dividend-data-files.txt";
   $g->csvfmat = '';
   $g->tab = " &nbsp;  &nbsp;  &nbsp;  &nbsp; &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp;  &nbsp; &nbsp;  &nbsp; &nbsp;  &nbsp; &nbsp;  &nbsp; &nbsp;   &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp; ";
   $g->file = file_get_contents("selected_xmlfile.txt");
   $g->fileDate = rtrim( $g->file, ".xml" );
   $g->currDateYYYYMMDD = $g->fileDate;

function create_table()
{ 
   global $db;
   global $g;

   $query = "CREATE TABLE IF NOT EXISTS _$g->currDateYYYYMMDD (
             id INT, 
             ticker VARCHAR(15), 
             fund TEXT, 
             exchange CHAR(10), 
             currency VARCHAR(10), 
             dividend FLOAT(7,4), 
             distribution VARCHAR(10), 
             yield FLOAT(10, 4), 
             sec DATE, 
             payable DATE, 
             record DATE, 
             ex_dividend  DATE, 
             created TIMESTAMP); ";
   try {
      $statement = $db->prepare($query);
      $statement->execute();
      $statement->closeCursor();	
      
      //$warning_message = show_warnings();
      if( strpos( show_warnings(), 'already exists' ))
         $create = "<br/><div class = 'row d-flex justify-content-center'><strong><pre><i>TABLE <u>$g->currDateYYYYMMDD</u> <font color = 'red'>already exists</font></strong></i></pre></div><br/>";
      else     
         $create = "<br/><br/><div class = 'row d-flex justify-content-center'><pre> $query </pre></div><br/>";

         echo create_trigger();
         return $create;
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

function create_log()
{ 
   global $db;
   global $g;
   
   $query = "CREATE TABLE IF NOT EXISTS log_$g->currDateYYYYMMDD (
             action ENUM('create', 'update','delete', 'insert', 'drop'),
             id INT, 
             ticker VARCHAR(15), 
             fund TEXT, 
             exchange CHAR(10), 
             currency VARCHAR(10), 
             dividend FLOAT(7,4), 
             distribution VARCHAR(10), 
             yield FLOAT(10, 4), 
             sec DATE, 
             payable DATE, 
             record DATE, 
             ex_dividend  DATE, 
             created TIMESTAMP); ";
   try {
      $statement = $db->prepare($query);
      $statement->execute();
      $statement->closeCursor();	
      
      //$warning_message = show_warnings();
      if( strpos( show_warnings(), 'already exists' ))
         $create = "$g->tab log_$g->currDateYYYYMMDD exists</strong><br/>";
      else     
         $create = "<br/><br/><pre> $query </pre><br/>";

         return;// $create;
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

function create_trigger()
{
    global $db;
    global $g;  
    static $load;
    
    if ( $load !== null )
    return $load;
    
   // INSERT TRIGGER
   $query = "CREATE TRIGGER ai_$g->currDateYYYYMMDD AFTER INSERT ON _$g->currDateYYYYMMDD
             FOR EACH ROW
             BEGIN
                INSERT INTO log_$g->currDateYYYYMMDD (action, id, ticker, fund, exchange, currency, dividend, distribution, yield, sec, payable, record, ex_dividend, created)
                VALUES('insert', NEW.id, NEW.ticker, NEW.fund, NEW.exchange, NEW.currency, NEW.dividend, NEW.distribution, NEW.yield, NEW.sec, NEW.payable, NEW.record, NEW.ex_dividend, NOW());
             END;";
	$statement = $db->prepare($query);
    $statement->execute();    
    $statement->closeCursor();

            $load .= "<br/><div class = 'row d-flex justify-content-center'><pre>".show_warnings()."<hr/> $query </pre></div><br/>";

   // UPDATE TRIGGER  
   $query = "CREATE TRIGGER au_$g->currDateYYYYMMDD AFTER UPDATE ON _$g->currDateYYYYMMDD
             FOR EACH ROW
             BEGIN
                INSERT INTO log_$g->currDateYYYYMMDD (action, id, ticker, fund, exchange, currency, dividend, distribution, yield, sec, payable, record, ex_dividend, created)
                VALUES('update', NEW.id, NEW.ticker, NEW.fund, NEW.exchange, NEW.currency, NEW.dividend, NEW.distribution, NEW.yield, NEW.sec, NEW.payable, NEW.record, OLD.ex_dividend, NOW());
             END;";
	$statement = $db->prepare($query);
    $statement->execute();    
    $statement->closeCursor();

           $load .= "<br/><div class = 'row d-flex justify-content-center'><pre>".show_warnings()."<hr/> $query </pre></div><br/>";

   // DELETE TRIGGER
   $query = "CREATE TRIGGER ad_$g->currDateYYYYMMDD AFTER DELETE ON _$g->currDateYYYYMMDD
             FOR EACH ROW
             BEGIN
                INSERT INTO log_$g->currDateYYYYMMDD (action, id, ticker, fund, exchange, currency, dividend, distribution, yield, sec, payable, record, ex_dividend, created)
                VALUES('delete', OLD.id, OLD.ticker, OLD.fund, OLD.exchange, OLD.currency, OLD.dividend, OLD.distribution, OLD.yield, OLD.sec, OLD.payable, OLD.record, OLD.ex_dividend, OLD.created);
             END;";
	$statement = $db->prepare($query);
    $statement->execute();    
    $statement->closeCursor();

           $load .= "<br/><div class = 'row d-flex justify-content-center'><pre>".show_warnings()."<hr/> $query </pre></div><br/>";
    return ;//$load;
//	echo "<script> console.log(show_warnings())<?script>";
}


function log_table()
{
    global $db;
    global $g;  
  
    $g->xamppDataDir = "/xampp/htdocs/data/";
    //$query = "LOAD XML INFILE './data/20200416-db.xml' IGNORE INTO TABLE _20200416 ROWS IDENTIFIED BY '<stock>';";
    $query = "SELECT * FROM log_$g->currDateYYYYMMDD;";
	$statement = $db->prepare($query);
    $statement->execute();
    $results = $statement->fetchAll();
    $statement->closeCursor();

    $load = "<br/><div class = 'row d-flex justify-content-center'><pre> $query </pre></div><br/>";
    echo   $load;
    return $results;
//	echo "<script> console.log(show_warnings())<?script>";
}

function load_table()
{
    global $db;
    global $g;  
  
  
    if( get_stock_data(1)[0] > 0 )
    return "<br/><div class = 'row d-flex justify-content-center'><strong><pre><i> <font color = 'red'>Already INSERTED INTO </font>TABLE <u>$g->currDateYYYYMMDD</u></strong></i></pre></div><br/>";
    $g->xamppDataDir = "/xampp/htdocs/data/";
    //$query = "LOAD XML INFILE './data/20200416-db.xml' IGNORE INTO TABLE _20200416 ROWS IDENTIFIED BY '<stock>';";
    $query = "LOAD XML INFILE '$g->xamppDataDir$g->currDateYYYYMMDD-db.xml' INTO TABLE _$g->currDateYYYYMMDD ROWS IDENTIFIED BY '<stock>';";
	$statement = $db->prepare($query);
    $statement->execute();    
    $statement->closeCursor();

    $query = "LOAD XML INFILE 'c:/xampp/htdocs/data/$g->currDateYYYYMMDD-db.xml'  INTO TABLE _$g->currDateYYYYMMDD ROWS IDENTIFIED BY '&lt;stock&gt';";

    $load = show_warnings()."<br/><div class = 'row d-flex justify-content-center'><pre> $query </pre></div><br/>";
    return $load;
//	echo "<script> console.log(show_warnings())<?script>";
}


function drop_table()
{
   global $db;
   global $g;

   $query = "DROP TABLE _$g->currDateYYYYMMDD";
	
   $statement = $db->prepare($query);
   $statement->execute();
   $statement->closeCursor();
   
   $drop = "<br/><small>DROPPED TABLE <small class='text-danger'><i>&nbsp; $g->currDateYYYYMMDD &nbsp;</i></small> FROM seeking_alpha_dividend;</small>";
   //$drop = "<br/><div class = 'row d-flex justify-content-center'><pre> $query </pre></div><br/>";
   //echo $drop;
   return $drop;
}

// Prepared statement (or parameterized statement) happens in 2 phases:
//   1. prepare() sends a template to the server, the server analyzes the syntax
//                and initialize the internal structure.
//   2. bind value (if applicable) and execute
//      bindValue() fills in the template (~fill in the blanks.
//                For example, bindValue(':name', $name);
//                the server will locate the missing part signified by a colon
//                (in this example, :name) in the template
//                and replaces it with the actual value from $name.
//                Thus, be sure to match the name; a mismatch is ignored.
//      execute() actually executes the SQL statement


function getAllStocks()
{
   global $db;
   global $g;

   $query = "SELECT * FROM _$g->currDateYYYYMMDD";
   $statement = $db->prepare($query);
   $statement->execute();
	
   // fetchAll() returns an array for all of the rows in the result set
   $results = $statement->fetchAll();
	
   // closes the cursor and frees the connection to the server so other SQL statements may be issued
   $statement->closecursor();
	
   return $results;
}


function get_stock_data($id)
{
   global $db;
   global $g;
	$results='';
   $query = "SELECT * FROM _$g->currDateYYYYMMDD where id = $id";
   $statement = $db->prepare($query);
   $statement->execute();
	
   // fetchAll() returns an array for all of the rows in the result set
   // fetch() return a row
   $results = $statement->fetch();
	
   // closes the cursor and frees the connection to the server so other SQL statements may be issued
   $statement->closeCursor();
	
   return $results;
}


function create_dir_table()
{   $txtfname = "xampp/mysql/bin/seeking_alpha_dividend-data-files.txt";

echo "<pre>BETA : CREATING DIRECTORY TABLE</pre>";

	global $db;
    global $g;
	//exec("mysql -u myuser -pMyPass -e \"USE mydb;TRUNCATE mytable;LOAD DATA INFILE '" . $file . "' IGNORE  INTO TABLE mytable;\"; ");
	//$query=exec("mysql -u root -p \"USE seek;TRUNCATE Seeking_Alpha_Dividends;LOAD XML INFILE '/xampp/htdocs/seek-db.xml' INTO TABLE Seeking_Alpha_Dividends ROWS IDENTIFIED BY '<stock>';\"; ");

    $query = "SOURCE c:/xampp/htdocs/seeking_alpha_dividend.sql";
	$statement = $db->prepare($query);
    $test = $statement->execute();    
    $statement->closeCursor();

	echo show_warnings();
}

function load_dir_table()
{   $txtfname = "c:/xampp/mysql/bin/seeking_alpha_dividend-data-files.txt";

echo "<pre>BETA : LOADING DIRECTORY INTO DATABASE</pre>";

	global $db;
	global $g;
    
    $query = "DELETE FROM xml_file;";
	$statement = $db->prepare($query);
    $test = $statement->execute();    
    $statement->closeCursor();
    
	echo show_warnings();

	$query = "LOAD DATA INFILE 'c:/xampp/htdocs/seeking_alpha_dividend-data-files.txt' IGNORE INTO TABLE xml_file FIELDS TERMINATED BY ','";
    //ENCLOSED BY '\"' LINES TERMINATED BY '\r\n' ";
	$statement = $db->prepare($query);
    $test = $statement->execute();    
    $statement->closeCursor();
   
    echo "<pre>BETA : LOADING FILE_DATES INTO XML_FILE DATABASE</pre>";
   // MyLog( "TRACE", "LOADING FILE_DATES INTO XML_FILE DATABASE; Current Biz-Date= $g->currBizDate" );
    
    

    $query = "UPDATE IGNORE xml_file SET file_date = (SELECT SUBSTRING(_file_, 1, 8) FROM xml_file WHERE _file_ LIKE '2020____');";
	$statement = $db->prepare($query);
    $test = $statement->execute();    
    $statement->closeCursor();
    
	echo show_warnings();

    load_stocks_table();

}

function load_stocks_table()
{
    global $db;
    global $g;   
    $query = "DELETE FROM stocks;";
	$statement = $db->prepare($query);
    $test = $statement->execute();    
    $statement->closeCursor();

    echo "<pre>BETA : LOADING fund INTO stocks TABLE FROM tsv_file</pre>";

    $query = "LOAD XML INFILE 'c:/xampp/htdocs/Data/20200404-db.xml' IGNORE INTO TABLE stocks ROWS IDENTIFIED BY '<ticker>','<yield>','<fund>';";
	$statement = $db->prepare($query);
    $test = $statement->execute();    
    $statement->closeCursor();
   
    echo "<pre>BETA : LOADING yield INTO stocks TABLE FROM tsv_file</pre>";


    $query = "LOAD XML INFILE 'c:/xampp/htdocs/data/20200404-db.xml' IGNORE INTO TABLE stocks ROWS IDENTIFIED BY '<fund>';";
	$statement = $db->prepare($query);
    $test = $statement->execute();    
    $statement->closeCursor();

	echo show_warnings();
}

function load_dividends_table()
{
echo "<pre>BETA : LOADING dividends TABLE</pre>";

	global $db;
    global $g;   	
    $query = "LOAD XML INFILE 'c:/xampp/htdocs/data/20200404-db.xml' IGNORE INTO TABLE dividends ROWS IDENTIFIED BY '<stock>';";//,'<dividend>','<distribution>','<ex_dividend>','<payable>';";
	$statement = $db->prepare($query);
    $test = $statement->execute();    
    $statement->closeCursor();

	echo show_warnings();
}

function load_exchange_table()
{
echo "<pre>BETA : LOADING exchange TABLE</pre>";

	global $db;
    global $g;   
	
    $query = "LOAD XML INFILE 'c:/xampp/htdocs/data/20200404-db.xml' IGNORE INTO TABLE exchange ROWS IDENTIFIED BY '<stock>';";//,'<dividend>','<distribution>','<ex_dividend>','<payable>';";
	$statement = $db->prepare($query);
    $test = $statement->execute();    
    $statement->closeCursor();

	echo show_warnings();
}

function load_shareholders_table()
{
echo "<pre>BETA : LOADING shareholders TABLE</pre>";

	global $db;
    global $g;   
    
    $query = "LOAD XML INFILE 'c:/xampp/htdocs/data/20200404-db.xml' IGNORE INTO TABLE shareholders ROWS IDENTIFIED BY '<stock>';";//,'<dividend>','<distribution>','<ex_dividend>','<payable>';";
	$statement = $db->prepare($query);
    $test = $statement->execute();    
    $statement->closeCursor();

	echo show_warnings();
}
function load_website_table()
{
echo "<pre>BETA : LOADING <strong>website<strong> TABLE</pre>";

	global $db;
    global $g;   
    
    $query = "LOAD XML INFILE 'c:/xampp/htdocs/data/20200404-db.xml' IGNORE INTO TABLE website ROWS IDENTIFIED BY '<stock>';";
	$statement = $db->prepare($query);
    $test = $statement->execute();    
    $statement->closeCursor();

	echo show_warnings();
}

function show_warnings()
{
	global $db;
    global $g;   

	$query = "SHOW WARNINGS";	
	$statement = $db->prepare($query);
    $statement->execute();   
    $result = $statement->fetch();
   	$statement->closeCursor();

	//print_r( $result );
    return $result[2];
	
}

function insertData($id, $ticker, $fund, $currency, $dividend, $distribution, $yield, $sec, $payable, $record, $ex_div)
{
    global $db;
    global $g;   	
   // insert into Seeking_Alpha_Dividends (name, major, year) values ('someone', 'CS', 4);
   //$query = "INSERT INTO Seeking_Alpha_Dividends VALUES (:id, :ticker, :fund, :currency, :dividend, :distribution, :yield, :sec, :payable, :record, :ex_dividend)";
  $query = "LOAD XML LOCAL INFILE '/xampp/htdocs/seek-db.xml' INTO TABLE Seeking_Alpha_Dividends ROWS IDENTIFIED BY '<stock>'";

  echo "$id : $ticker : $fund : $currency : $dividend : $distribution : $yield : $sec : $payable : $record : $ex_divdend <br/>";
   $statement = $db->prepare($query);
  /* $statement->bindValue(':id', $id);
   $statement->bindValue('ticker', $ticker);
   $statement->bindValue(':fund', $fund);
   $statement->bindValue(':currency', $currency);
   $statement->bindValue(':dividend', $dividend);
   $statement->bindValue(':distribution', $distribution);
   $statement->bindValue(':yield', $yield);
   $statement->bindValue(':sec', $sec);
   $statement->bindValue(':payable', $payable);
   $statement->bindValue(':record', $record);
   $statement->bindValue(':ex_dividend', $ex_dividend);*/
   $statement->execute();     // if the statement is successfully executed, execute() returns true
   // false otherwise
		
   $statement->closeCursor();
}


function updateData($name, $major, $year)
{
    global $db;
    global $g;   	
   // update Seeking_Alpha_Dividends set major="EE", year=2 where name="someoneelse"
   $query = "UPDATE Seeking_Alpha_Dividends SET major=:major, year=:year WHERE name=:name";
   $statement = $db->prepare($query);
   $statement->bindValue(':name', $name);
   $statement->bindValue(':major', $major);
   $statement->bindValue(':year', $year);
   $statement->execute();
   $statement->closeCursor();
}


function delete_data($ticker)
{
    global $db;
    global $g;   	
   $query = "DELETE FROM _$g->currDateYYYYMMDD WHERE ticker=:ticker";
   $statement = $db->prepare($query);
   $statement->bindValue(':ticker', $ticker);
   $statement->execute();
   $statement->closeCursor();
}
?>
