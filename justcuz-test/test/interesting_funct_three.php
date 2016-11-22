<html> 
<?php 
if ($c=OCILogon("ora_m3c9", "a39296132", "dbhost.ugrad.cs.ubc.ca:1522/ug")) { 
  echo "Customer who had the most average purchases:\n"; 
} else { 
  $err = OCIError(); 
  echo "Oracle Connect Error " . $err['message']; 
}
//nested agg rerun

$sql = " create view temporary as select cid, avg(quantity) avg_quant from order_delivers_buys group by cid order by avg_quant desc";
$st=oci_parse($c, $sql);
oci_execute($st);
$sql = "select name from customer where cid in (select cid from temp1 where rownum<=1)";
$st1=oci_parse($c, $sql);
oci_execute($st1);

//Execute the commented out code if you wanna check the result

print "<table border='1'>\n";
while ($row = oci_fetch_array($st1, OCI_ASSOC+OCI_RETURN_NULLS)) {
    print "<tr>\n";
    foreach ($row as $item) {
        print "    <td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>\n";
    }
    print "</tr>\n";
}
print "</table>\n";

oci_free_statement($st);
oci_free_statement($st1);
oci_close($c);
?> 
</html>
