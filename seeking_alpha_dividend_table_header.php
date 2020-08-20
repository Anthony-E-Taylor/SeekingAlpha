
<br>
<div class="container">
    <form action="seek.php" method="post">
        <div>
            <input type="submit" value="Create" name="db-btn" title="Create 'beta' table" />
            <input type="submit" value="Insert" name="db-btn" title="Insert into 'beta' table" />
            <input type="submit" value="Drop" name="db-btn" title="Drop 'beta' table" />
            <small class="text-danger"><?php echo $msg ?></small>
        </div>
    </form>
</div>
<br>

   <form action="<?php $_SERVER['PHP_SELF'] ?>" method="get">
      <input type="submit" name="btnaction" value="create" class="btn btn-light" />
      <input type="submit" name="btnaction" value="insert" class="btn btn-light" />   
      <input type="submit" name="btnaction" value="select" class="btn btn-light" />
      <input type="submit" name="btnaction" value="update" class="btn btn-light" />
      <input type="submit" name="btnaction" value="delete" class="btn btn-light" />
      <input type="submit" name="btnaction" value="drop" class="btn btn-light" />            
    </form>

<?php 
global $g;
if (isset($_GET['btnaction']))
{	
   try 
   { 	
      switch ($_GET['btnaction']) 
      {
         case 'create': create_Table(); break;
         case 'insert': insertData();  break;
         case 'select': selectData();  break;
         case 'update': updateData();  break;
         case 'delete': deleteData();  break;
         case 'drop':   dropTable();   break;      
      }
   }
   catch (Exception $e)       // handle any type of exception
   {
      $error_message = $e->getMessage();
      echo "<p>Error message: $error_message </p>";
   }   
}


      

        $row = 1;
        if (($handle = fopen("./data/20200412.tsv", "r")) !== FALSE) {

        echo '<table id="alphaTable" class="table table-condensed">';

            while (($data = fgetcsv($handle, 1000, "\t")) !== FALSE) {
            $num = count($data);
            if ($row == 1) {
            echo $g->asOfDate;
            echo '
            

    <thead>
        <!-- set table headers -->
        <tr>
            <th onclick="sortTable(0)"> <button type="button" class="btn btn-sm">iD          </th>
            <th onclick="sortTable(1)"> <button type="button" class="btn btn-sm">Ticker      </th>
            <th onclick="sortTable(2)"> <button type="button" class="btn btn-sm">Exchange    </th>
            <th onclick="sortTable(3)"> <button type="button" class="btn btn-sm">Currency    </th>
            <th onclick="sortTable(4)"> <button type="button" class="btn btn-sm">Dividend    </th>
            <th onclick="sortTable(5)"> <button type="button" class="btn btn-sm">Distribution</th>
            <th onclick="sortTable(6)"> <button type="button" class="btn btn-sm">%Yield      </th>
            <th onclick="sortTable(7)"> <button type="button" class="btn btn-sm">30-Day SEC  </th>
            <th onclick="sortTable(8)"> <button type="button" class="btn btn-sm">Payable     </th>
            <th onclick="sortTable(9)"> <button type="button" class="btn btn-sm">Record      </th>
            <th onclick="sortTable(10)"> <button type="button" class="btn btn-sm">Ex-Div     </th>
            <th onclick="sortTable(11)"> <button type="button" class="btn btn-sm">Timestamp  </th>
            <th onclick="sortTable(12)"> <button type="button" class="btn btn-sm">Fund       </th>
            <th><button type="button" class="btn btn-sm">Remove</button></th>
         
        </tr>  
    </thead> 
    <tbody>
    <tr>'; }
                else{ echo '<tr>'; }

                    for ($c=0; $c < $num; $c++) 
                    {
                        //echo $data[$c] . "
                        //"<br />\n";
                        if( empty( $data[ $c ] )) { $value = "-"; }
                        else     { $value = $data[ $c ]; }
                        
                        if ( $row == 1 ) {}
                        else{echo '<td>'.$value.'</td>';}
                    
                    }

                        if ($row == 1) { echo '</tr>'; }
                        else{            echo '</tr>';}
                $row++;
                }

                echo '
            </tbody>
        </table>';
        fclose($handle);
        }
        ?>
