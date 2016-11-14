<html>
<?php
if ($c=OCILogon("ora_m3c9", "a39296132", "dbhost.ugrad.cs.ubc.ca:1522/ug")) { 
  echo "Successfully connected to Oracle.\n"; 
} else { 
  $err = OCIError(); 
  echo "Oracle Connect Error " . $err['message']; 
}
//create 4 digit cid at random
//get cids from table
//if
$item = $_GET["item_number"];
$price = $_GET["price"];
$type = $_GET["type"];
$gender = $_GET["gender"];
$color = $_GET["color"];
$company = $_GET["company"];
$sql = "INSERT INTO merchandise_supplies(item_num, price, type, gender, color, company_name) VALUES ('$item' ,'$price' ,'$type' ,'$gender'  , '$color', '$company')";
$st = oci_parse($c, $sql);
oci_execute($st);
oci_free_statement($st);

$stid = oci_parse($c, 'SELECT * FROM merchandise_supplies');
oci_execute($stid);
echo "<table border='1'>\n";
while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
    echo "<tr>\n";
    foreach ($row as $item) {
        echo "    <td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>\n";
    }
    echo "</tr>\n";
}
echo "</table>\n";
oci_close($c);
?>
</html>
