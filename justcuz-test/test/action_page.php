<html>
<?php
if ($c=OCILogon("ora_m3c9", "a39296132", "dbhost.ugrad.cs.ubc.ca:1522/ug")) { 
  //echo "Successfully connected to Oracle.\n"; 
} else { 
  $err = OCIError(); 
  echo "Oracle Connect Error " . $err['message']; 
}
//create 4 digit cid at random
//get cids from table
//if
$inum = $_GET["itemnum"];

$sti = oci_parse($c, "SELECT count from inventory_tracks where item_num ='". $inum. "'");
if (!$sti) {
    $e = OCIError($c);
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}
$r = oci_execute($sti);
if (!$r) {
    $e = OCIERROR($sti);
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}
$count;
while (($row = oci_fetch_array($sti, OCI_NUM)) != false) {
    $count=$row[0];
}
echo $count;
if($count <=0){
	echo "Sorry, sold out!";
	header( "refresh:3;url=http://www.ugrad.cs.ubc.ca/~m3c9/temp/justcuz/justcuz-test/test/main.html" );
}
else{
$sql1 = "UPDATE inventory_tracks set count = count - 1 where item_num ='". $inum. "'";
$std=oci_parse($c, $sql1);
oci_execute($std);

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

$cid = rand(1000,9999);
echo $cid;
$exists = True;
while($exists){
	while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
    foreach ($row as $item) {
        if($item == $cid){
        	$cid = rand(1000, 9999);
        	$exists=True;
        }
        else{
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
oci_execute($st);}
?>
</html>