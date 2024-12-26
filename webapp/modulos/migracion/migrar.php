<?php 



$myServer = "serversisb"; 
$myUser = "nmragus0405"; 
$myPass = "nmragus25262325"; 
$myDB = "nmfico"; 



$s = @mssql_connect($myServer, $myUser, $myPass) 
or die("Couldn't connect to SQL Server on $myServer"); 

echo "Entre";

$d = @mssql_select_db($myDB, $s) 
or die("Couldn't open database $myDB"); 


$query= "select AA020000000 from [CD Bodegas]"; 

$result = mssql_query($query); 
$numRows = mssql_num_rows($result); 

echo "<h1>" . $numRows . " Row" . ($numRows == 1 ? "" : "s") . " Returned </h1>"; 

while($row = mssql_fetch_array($result)) 
{ 
echo "<li>" . $row["AA020000000"] . "</li>"; 
} 

?> 