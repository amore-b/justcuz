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

$sql1 = "UPDATE inventory_tracks SET count = '$quantity' WHERE item_num = '$item' AND size_label = '$size'";
$st = oci_parse($c, $sql1);
oci_execute($st);
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
