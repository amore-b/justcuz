<?php

class CustomerController extends MyController
{
    public function getAction($request) {
        if(isset($request->url_elements[1])) {
            $data["message"] = "HERE";
            $cc = $_SESSION["c"];
            $stid = oci_parse($cc, "SELECT * from customer");
            //if(isset($request->url_elements[3])) {
                //what sort of error checking is needed?????
            //if ($c=OCILogon("ora_r5d8", "a29093119", "dbhost.ugrad.cs.ubc.ca:1522/ug")) { 
              //echo "Successfully connected to Oracle.\n"; 
            //} else { 
             // $err = OCIError(); 
              //echo "Oracle Connect Error " . $err['message']; 
            //}
            if (!$stid) {
                $e = OCIError($cc);
                trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            }
            $r = oci_execute($stid);
            if (!$r) {
                $e = OCIERROR($stid);
                trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            }

            $json = array();
            while($row = oci_fetch_array($stid,OCI_ASSOC))
            {
                      $json[] = $row;
            }
            $data = $json;
            //TODO: figure out where oci_free_statement and oci_close go
            oci_free_statement($stid);
            oci_close($cc);
                switch($item_type) {//request->url_elements[3]) {
                case "jeans":
                     //$data["message"] =  $item_type . " has been requested";
                    //$data = $json;
                    break;
                case "purse":
                    //$data["message"] =  $item_type . " has been requested";
                    break;
                case "shirt":
                    //$data["message"] =  $item_type . " has been requested";
                    break;
                case "shoes":
                    //$data["message"] =  $item_type . " has been requested";
                    break;
                case "sweater":
                    //$data["message"] =  $item_type . " has been requested";
                    break;
                default:
                    // do nothing, this is not a supported action
                    break;
                }
            //} else {
            //    $data["message"] = "here is the info for user " . $user_id;
            //}
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
