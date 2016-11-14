<html> 
<?php 
if ($c=OCILogon("ora_m3c9", "a39296132", "dbhost.ugrad.cs.ubc.ca:1522/ug")) { 
  echo "Merry Christmas!\n"; 
} else { 
  $err = OCIError(); 
  echo "Oracle Connect Error " . $err['message']; 
}

//The following queries satisfy our second interesting functionality requirement. That is,
/*
Managers are also able to give bonus loyalty points to our top members on
Christmas (statement to update part of database).

Top members are those who bought all jeans.
*/
//show all members who bought all jeans, reward them with 10,000 points

$sql = "select m.cid from member m where not exists (select * from merchandise_supplies ms where ms.type='jeans' and not exists (select o.item_num from order_delivers_buys o where ms.item_num=o.item_num and ms.type='jeans' and o.cid=m.cid))
";
$st=oci_parse($c, $sql);
if(!$st){
	echo "ramz";
}
$r= oci_execute($st);
if(!$r){
	echo "ramz1";
}
//Execute the commented out code if you wanna check the result
echo "<table border='1'>\n";
while ($row = oci_fetch_array($st, OCI_ASSOC+OCI_RETURN_NULLS)) {
    echo "<tr>\n";
    foreach ($row as $item){
    	$sql1 = "UPDATE member set points = points + 10000 where cid ='". $item. "'";
		$st1=oci_parse($c, $sql1);
		if(!$st1){
			echo "ram";
		}
		$r1= oci_execute($st1);
		if(!$r1){
			echo "ram1";
		}
	echo "	<td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>\n";
	}
}
	echo "</table>\n";



oci_free_statement($st);
oci_free_statement($st1);
oci_close($c);
?> 
</html>
