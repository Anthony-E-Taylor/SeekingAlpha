<?php

echo  "As of: ". preg_replace('/Today -/','',$date[0])."\n\n";

?>

</dt>

<br>
	<div class="container">
		<form action="seek.php" method="post">
			<div>
				<input type="submit" value="Create" name="db-btn" title="Create 'beta' table"/>
				<input type="submit" value="Insert" name="db-btn" title="Insert into 'beta' table" /> 
				<input type="submit" value="Drop"   name="db-btn" title="Drop 'beta' table" />
				<small class="text-danger"><?php echo $msg ?></small>
			</div>
		</form>
	</div>
	<br>
<table id="alphaTable"  class="table table-condensed">

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
         <!--  <th align="center" valign="center"><b>UNION</b></th> -->
       </tr>
   </thead>

   <tbody>

   <form action="formprocessing.php" method="post">
   
