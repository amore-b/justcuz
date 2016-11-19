<html> 
<?php 
if ($c=OCILogon("ora_m3c9", "a39296132", "dbhost.ugrad.cs.ubc.ca:1522/ug")) { 
  echo "Successfully connected to Oracle.\n"; 
} else { 
  $err = OCIError(); 
  echo "Oracle Connect Error " . $err['message']; 
}
//The following queries satisfy our first interesting functionality requirement. That is,
//"Managers can generate a report that shows the names of suppliers who supplied our top five best-selling items (query based on user input).""

$sql = "create view topfive as item_num, count(*) as total_sales from order_delivers_buys group by item_num order by total_sales desc";
$st=oci_parse($c, $sql);
oci_execute($st);
$sql = "select distinct company_name from merchandise_supplies where item_num in (select item_num from topfive where rownum <=5)";
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
