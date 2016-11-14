<?php

class EmployeeController extends MyController
{
    public function getAction($request) {
        if(isset($request->url_elements[2])) {
            $data["message"] = "HERE";
            $cc = $_SESSION["c"];
	    $todo = (string)$request->url_elements[2];
	    $eid = (string)$request->url_elements[3];
	    if ($todo == 'delete') {
            	$stid = oci_parse($cc, "DELETE FROM employee where eid ='".$eid."'") } 
	    else if ($todo == 'add') {
		$name = (string)$request->url_elements[4];
		$address = (string)$request->url_elements[5];
		$phone = (string)$request->url_elements[6];
		$hire_date = (string)$request->url_elements[7];
		$stid = oci_parse($cc, "INSERT INTO employee VALUES ('".$eid."' ,'".$name."' ,'".$address"' ,'".$phone."'  , sysdate)";
           
		//$data = url_elements;
 //if(isset($request->url_elements[3])) {
                //what sort of error checking is needed?????
            //if ($c=OCILogon("ora_r5d8", "a29093119", "dbhost.ugrad.cs.ubc.ca:1522/ug")) { 
              //echo "Successfully connected to Oracle.\n"; 
            //} else { 
             // $err = OCIError(); 
              //echo "Oracle Connect Error " . $err['message']; 
            //}
            if (!$stid) {
                echo "helloooo";
//$e = OCIError($cc);
                //trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            }
           // $r = oci_execute($stid);
            //if (!$r) {
              //  $e = OCIERROR($stid);
               // trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            }

            $json = array();
            while($row = oci_fetch_array($stid,OCI_ASSOC))
            {
                      $json[] = $row;
            }
            //$data = $json;
            //TODO: figure out where oci_free_statement and oci_close go
            oci_free_statement($stid);
            oci_close($cc);

} else {
            $data["message"] = "you want a list of items";
        }
        return $data;
    }

    public function postAction($request) {
        $data = $request->parameters;
        $data['message'] = "This data was submitted";
        return $data;
    }
}
?>

