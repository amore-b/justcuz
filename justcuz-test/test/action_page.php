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
$stid = oci_parse($c, 'select cid from customer');
if (!$stid) {
    $e = OCIError($c);
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}
$r = oci_execute($stid);
if (!$r) {
    $e = OCIERROR($stid);
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}
$cid = 4208;
echo $cid;
$exists = True;
while($exists){
	while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
    foreach ($row as $item) {
        if($item == $cid){
        	$cid = rand(1000, 9999);
			echo $cid;
			echo "Fail! CID doesn't already exist in table";
        	$exists=True;
        }
        else{
        	echo "Success! CID doesn't already exist in table";
        	$exists=False;
        }
    }
	}
}
$cname = $_GET["name"];
$email = $_GET["email"];
$address = $_GET["address"];
$cardnum = $_GET["cardnumber"];
$cardtype = $_GET["cardtype"];

$sql = "INSERT INTO customer(cid,name,email,address,card_num,card_type) VALUES('$cid', '$cname', '$email', '$address', '$cardnum', '$cardtype')";
$st=oci_parse($c, $sql);
oci_execute($st);
?>
</html>