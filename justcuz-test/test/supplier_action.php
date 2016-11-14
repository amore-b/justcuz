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
$company = $_GET["company_name"];
$address = $_GET["address"];
$phone = $_GET["phone_num"];
$eid = $_GET["eid"];
$sql = "INSERT INTO supplier_adds(company_name, address, phone_num, eid) VALUES ('$company', '$address', '$phone', '$eid')";
$st = oci_parse($c, $sql);
oci_execute($st);
oci_free_statement($st);

$stid = oci_parse($c, 'SELECT * FROM supplier_adds');
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
