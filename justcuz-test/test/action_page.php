<html>
<?php
if ($c=OCILogon("ora_m3c9", "a39296132", "dbhost.ugrad.cs.ubc.ca:1522/ug")) { 
  echo ".\n"; 
} else { 
  $err = OCIError(); 
  echo "Oracle Connect Error " . $err['message']; 
  die;
}

//initializes variables corresponding to the form attributes, grabbed from the query tags in the url
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
$discount = $totalprice - $points; //members receive a discounted total if they have points

//We need to check the stock of the desired item
$sti = oci_parse($c, "SELECT count from inventory_tracks where item_num ='". $inum. "'");
if (!$sti) {
    $e = OCIError($c);
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
    die;
}
$r = oci_execute($sti);
if (!$r) {
    $e = OCIERROR($sti);
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
    die;
}
while ($row = oci_fetch_array($sti, OCI_ASSOC+OCI_RETURN_NULLS)) {
    foreach ($row as $item) {
        $count=$item;
    }
}
oci_free_statement($sti);
//If the quantity desired is greater than what's in stock, redirect them to the homepage
if($count - $quantity < 0){
    echo "Sorry, insufficient quantity! Please reselect a lower quantity. We promise to restock asap. Redirecting...";
    header( "refresh:5;url=http://www.ugrad.cs.ubc.ca/~m3c9/final/justcuz/justcuz-test/test/main.html" );
    die;
}

//otherwise, add the customer to the db, deduct the existing quantity of the item, and track the order
else{
//the following creates a record of an order
//just randomly assigning the order to our exisitng, dedicated employees 
$eid = rand(5000, 5005);

//need to generate a unique order number to satisfy the primary key constraint
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

//if the memberid in the query tag is undefined, it means we're dealing with a new customer
if($memberid=="undefined"){
//We need to generate a unique customer id. The following code ensures the randomly generated cid is unique.
$stid = oci_parse($c, 'select cid from customer');
if (!$stid) {
    $e = OCIError($c);
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
    die;
}
$r = oci_execute($stid);
if (!$r) {
    $e = OCIERROR($stid);
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
    die;
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
//add a new user
$sql69 = "INSERT INTO users VALUES('$email', 2)";
$st69=oci_parse($c, $sql69);
if(!oci_execute($st69)){
    echo "Failed to add user! Redirecting...";
    header( "refresh:3;url=http://www.ugrad.cs.ubc.ca/~m3c9/final/justcuz/justcuz-test/test/main.html" );
    die;
}
oci_free_statement($st69);
//add a new customer
$sql = "INSERT INTO customer VALUES('$cid', '$cname', '$email', '$address', '$cardnum', '$cardtype')";
$st=oci_parse($c, $sql);
if(!oci_execute($st)){
    echo "Failed to add customer! Redirecting...";
    header( "refresh:3;url=http://www.ugrad.cs.ubc.ca/~m3c9/final/justcuz/justcuz-test/test/main.html" );
    die;
}
oci_free_statement($st);
}
//if memberid is not undefined, we are dealing with a member
else{
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
//update stock in inventory_tracks
//if count - quantity purchased <= 0, invalid quantity (not enough in stock)
$sql1 = "UPDATE inventory_tracks set count = count - '$quantity' where item_num ='". $inum. "'";
$std=oci_parse($c, $sql1);
oci_execute($std);
oci_free_statement($st);
//insert new order in order_delivers_buys table
$sql2 = "INSERT INTO order_delivers_buys VALUES ('$ordernum', '$totalprice', '$inum', '$eid', '$cid', sysdate, '$quantity')";
$std1=oci_parse($c, $sql2);
if (!$std1) {
    echo "order delivers buys insert query parse failed. Redirecting...";
    header( "refresh:3;url=http://www.ugrad.cs.ubc.ca/~m3c9/final/justcuz/justcuz-test/test/main.html" );
    die;
}
$r2 = oci_execute($std1);
if (!$r2) {
    echo "order delivers buys insert query execution failed. Redirecting...";
    header( "refresh:3;url=http://www.ugrad.cs.ubc.ca/~m3c9/final/justcuz/justcuz-test/test/main.html" );
    die;
}
oci_free_statement($std1);

oci_close($c);
//Send the customer an email with a succinct receipt
echo "Purchase successful! Sending confirmation email. Redirecting... ";
$message = "Hi " . $cname . ", here's your tl;dr order information:\r\n" . "Order number: " . $ordernum . "\r\n" . "Total paid: $" . $discount ."\r\n" . "Thank you for shopping at Stuff 'n Things :)";
mail($email, 'Order receipt', $message);
header( "refresh:5;url=http://www.ugrad.cs.ubc.ca/~m3c9/final/justcuz/justcuz-test/test/main.html" );
}
?>
</html>