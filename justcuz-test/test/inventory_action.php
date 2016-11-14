<html>
<?php
if ($c=OCILogon("ora_m3c9", "a39296132", "dbhost.ugrad.cs.ubc.ca:1522/ug")) { 
  echo "Inventory Updated.\n"; 
} else { 
  $err = OCIError(); 
  echo "Oracle Connect Error " . $err['message']; 
}
//create 4 digit cid at random
//get cids from table
//if
$item = $_GET["item"];
$size = $_GET["size"];
$quantity = $_GET["howmany"];

if($item=="" || $size=="" || $quantity==""){
	echo "Invalid inventory update. Please specify all fields (redirecting...).";
	header( "refresh:3;url=http://www.ugrad.cs.ubc.ca/~m3c9/temp/justcuz/justcuz-test/test/manager.html" );
	die;
}
//need to check db if item_num exists in inventory
$sql = "select item_num from inventory_tracks";
$std = oci_parse($c, $sql);
oci_execute($std);

$exists=False;
while ($row1 = oci_fetch_array($std, OCI_ASSOC+OCI_RETURN_NULLS)) {
    echo "<tr>\n";
    foreach ($row1 as $itemdb) {
        if($item==$itemdb){
        	$exists=True;
        }
    }
}
oci_free_statement($std);

if(!$exists){
	echo "Item does not exist in inventory. Please re-specify item.";
	die;
}

//need to check db if size exists in inventory
$sq = "SELECT size_label from inventory_tracks WHERE item_num = '$item'";
$stz = oci_parse($c, $sq);
if(!oci_execute($stz)){
	echo "wtf";
}
$exists1=False;
while ($row2 = oci_fetch_array($stz, OCI_ASSOC+OCI_RETURN_NULLS)) {
    echo "<tr>\n";
    foreach ($row2 as $itemsize) {
        if($size . " "==$itemsize){
        	$exists1=True;
        }
    }
}
oci_free_statement($stz);

if(!$exists1){
	echo "Size does not exist for the item. Please re-specify size.";
	die;
}

$sql1 = "UPDATE inventory_tracks SET count = count + '$quantity' WHERE item_num = '$item' AND size_label = '$size'";
$st = oci_parse($c, $sql1);
if(!oci_execute($st)){
	echo "Invalid inventory update. Please re-specify item";	//type checking (i.e. quantity)
}
oci_free_statement($st);

$sql2 = "SELECT * FROM inventory_tracks WHERE item_num = '$item'";

$stid = oci_parse($c, $sql2);
oci_execute($stid);
echo "<table border='1'>\n";
while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
    echo "<tr>\n";
    foreach ($row as $itm) {
        echo "    <td>" . ($itm !== null ? htmlentities($itm, ENT_QUOTES) : "&nbsp;") . "</td>\n";
    }
    echo "</tr>\n";
}
echo "</table>\n";
oci_close($c);
?>
</html>
