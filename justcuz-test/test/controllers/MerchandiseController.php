<?php

class MerchandiseController extends MyController
{
    public function getAction($request) {
        if(isset($request->url_elements[2])) {

            $cc = $_SESSION["c"];

            $size = count($request->url_elements);
            $attr = (string)$request->url_elements[2];
            $val = (string)$request->url_elements[3];
            $showarray = [];
            $showstring = "item_num";

            if ($size > 4) {

                if ((string)$request->url_elements[4] == 'all') {
                    $showstring = "*";
                } else {

                    for ($x = 4; $x <= $size-1; $x++) {

                        $showstring .= ", " . (string)$request->url_elements[$x];
                        //$showarray[x] = $request->url_elements[x];
                    } 
                }
            } 

            // for ($i=0; $i <= count($showarray); $i++) {
            //     $showstring = $showstring + ", " + $showarray[i];
            // }

            if ($val == 'all') {
                    $stid = oci_parse($cc, "SELECT " . $showstring . " from merchandise_supplies");
            } else {
                switch($attr) {
                    case "type":
                        $item_type = $val;
                        $stid = oci_parse($cc, "SELECT " . $showstring . " from merchandise_supplies where type ='". $item_type. "'");
                        break;
                    case "gender":
                        $gender = $val;
                        $stid = oci_parse($cc, "SELECT " . $showstring . " from merchandise_supplies where gender ='". $gender. "'");
                        break;
                    case "color":
                        $color = $val;
                        $stid = oci_parse($cc, "SELECT " . $showstring . " from merchandise_supplies where color ='". $color. "'");
                        break;
                    case "item_num":
                        $item_num = $val;
                        $stid = oci_parse($cc, "SELECT " . $showstring . " from merchandise_supplies where item_num ='". $item_num. "'");
                        break;
                    default:
                        break;
                }
            }
            
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
