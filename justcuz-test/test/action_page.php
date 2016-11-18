<html>
<body>
      <a href="main.html#" class="item">Home</a>
    </body>
<?php
if ($c=OCILogon("ora_m3c9", "a39296132", "dbhost.ugrad.cs.ubc.ca:1522/ug")) { 
  echo "Successfully connected to Oracle.\n"; 
} else { 
  $err = OCIError(); 
  echo "Oracle Connect Error " . $err['message']; 
}
//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!NEGATIVE POINTS!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

//create 4 digit cid at random
//get cids from table
//if
$inum = $_GET["itemnum"];
$cname = $_GET["name"];
$email = $_GET["email"];
$address = $_GET["address"];
$cardnum = $_GET["cardnumber"];
$cardtype = $_GET["cardtype"];
$quantity = $_GET["quantity"];
$points = $_GET["points"];
$price = $_GET["price"];
$memberid= $_GET["memberid"];
$totalprice = $price * $quantity;
//need variable to store points to add as a result of purchase
//need to get the existing count of the desired item
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
while ($row = oci_fetch_array($sti, OCI_ASSOC+OCI_RETURN_NULLS)) {
    foreach ($row as $item) {
        $count=$item;
        //echo $count;
    }
}
oci_free_statement($sti);
//if the quantity desired is greater than the count, redirect them to the homepage
if($count - $quantity < 0){ //TODO: this is executing before the update table. figure it out
    echo "Sorry, insufficient quantity!";
    //header( "refresh:3;url=http://www.ugrad.cs.ubc.ca/~m3c9/temp/justcuz/justcuz-test/test/main.html" );
}

//otherwise, add the customer to the db, deduct the existing quantity of the item, and track the order
else{
//the following creates a record of an order
//just randomly assigning the order to our exisitng, dedicated employees 
$eid = rand(5000, 5005);
//echo "eid is " . $eid;

//need to generate a unique order number in the same fashion as the cid
$stid1 = oci_parse($c, 'select order_num from order_delivers_buys');
oci_execute($stid1);
$ordernum = rand(1,999999);
$exists1 = True;
while($exists1){
    while ($row1 = oci_fetch_array($stid1, OCI_ASSOC+OCI_RETURN_NULLS)) {
    foreach ($row1 as $item1) {
        if($item1 == $ordernum){
            $ordernum = rand(1,999999);
            $exists1=True;
        }
        else{
            $exists1=False;
        }
    }
    }
}
oci_free_statement($stid1);
//echo "ordernum is " . $ordernum . "\n";
//echo "inum is " . $inum . "\n";
//echo "cid is " . $cid . "\n";
//echo "quantity is " . $quantity . "\n";
if($memberid=="undefined"){
//We need to generate a unique customer id (there's probably an easier way to do this). the following code ensures the randomly generated cid is unique.
//add a new customer
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
oci_free_statement($stid);
$sql = "INSERT INTO customer(cid,name,email,address,card_num,card_type) VALUES('$cid', '$cname', '$email', '$address', '$cardnum', '$cardtype')";
$st=oci_parse($c, $sql);
oci_execute($st);
oci_free_statement($st);
}
else{
    //means a member is purchasing shit
    $cid = $memberid;

    /*
    if dollarvalue of points is greater than dollar value of total price
    subtract member points by point value of total price
    add member points by points acquired by total (10% of point value of total price)

    e.g.
    total price = $30
    dollar value of points = $40

    4000 - 3000

    new points = 1000

    points from total: 3000*10% = 300

    new points = 1300

    if dollarvalue of points is less than dollar value of total price
        total price = $40
        dollar value of points = $30

    new points = just the points acquired by total
    */
    if($points > $totalprice){
        $newpoints = $points*100 - $totalprice*100 + $totalprice*100*0.1;
    }
    else{
        $newpoints = $totalprice*100*0.1;
    }
    $sql1 = "UPDATE member set points = '$newpoints' where cid ='". $cid. "'";
    $std=oci_parse($c, $sql1);
    oci_execute($std);
    oci_free_statement($st);
}
//update count in inventory_tracks
//if count - quantity purchased <= 0, invalid quantity (not enough in stock)
$sql1 = "UPDATE inventory_tracks set count = count - '$quantity' where item_num ='". $inum. "'";
$std=oci_parse($c, $sql1);
oci_execute($std);
oci_free_statement($st);
//insert new order in order_delivers_buys table NOT FUNCTIONAL
echo "order number: " . $ordernum;
echo " item number: " . $inum;
echo " employee id: " . $eid;
echo " cust id: " . $cid;
echo " quantity: " . $quantity;

$sql2 = "INSERT INTO order_delivers_buys VALUES ('$ordernum', '$inum', '$eid', '$cid', sysdate, '$quantity')";
$std1=oci_parse($c, $sql2);
if (!$std1) {
    echo "ram_init";
}
$r2 = oci_execute($std1);
if (!$r2) {
    //$e = oci_error();   // For oci_connect errors pass no handle
    echo "ramz";
}
oci_free_statement($std1);

oci_close($c);
}
?>
</html>
