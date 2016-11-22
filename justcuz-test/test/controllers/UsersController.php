<?php

session_start();
class UsersController extends MyController
{
    public function getAction($request) {
        if(isset($request->url_elements[2])) {
            switch($request->url_elements[2]) {
            case 'login' :
                $cc = $_SESSION["c"];
                $email = $request->parameters["email"];
                $password = $request->parameters["password"];
                //$query = "SELECT * FROM customer c, member m where c.cid = m.cid and m.password ='". $password. "' and c.email = '". $email. "'";
                $query = "SELECT * FROM users WHERE email='". $email. "'";
                $stid = oci_parse($cc, $query);
                if (!$stid) {
                    $e = OCIError($cc);
                    $data['message'] = $e['message'];
                    //E_USER_ERROR halts script execution
                    //trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
                } else {
                    $r = oci_execute($stid);
                    if (!$r) {
                        $e = OCIERROR($stid);
                        $data['message'] = $e['message'];
                        //trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
                    } else {
                        $row = oci_fetch_array($stid, OCI_ASSOC);
                        if(!$row) {
                            $data['message'] = "Invalid username or email";
                        } else {
                            //email is valid
                            if($row["U_TYPE"] == 2) {
                                //member login
                                $q2 = "SELECT * FROM customer c, member m where c.cid = m.cid and m.password ='". $password. "' and c.email = '". $email. "'";
                                $s2 = oci_parse($cc, $q2);
                                if(!$s2) {
                                    $data['message'] = OCIERROR($cc);
                                } else {
                                    $r2 = oci_execute($s2);
                                    if(!$r2) {
                                        $data['message'] = OCIERROR($s2);
                                    } else {
                                        $row2 = oci_fetch_array($s2, OCI_ASSOC);
                                        $data = $row2;
                                        $data["U_TYPE"] = "mem";
                                    }
                                }
                            } else {
                                //employee login
                                $q2 = "SELECT * FROM employee where password ='". $password. "' and email = '". $email. "'";
                                $s2 = oci_parse($cc, $q2);
                                if(!$s2) {
                                    $data['message'] = OCIERROR($cc);
                                } else {
                                    $r2 = oci_execute($s2);
                                    if(!$r2) {
                                        $data['message'] = OCIERROR($s2);
                                    } else {
                                        $row2 = oci_fetch_array($s2, OCI_ASSOC);
                                        $data = $row2;
                                        $data["U_TYPE"] = "emp";
                                        //check if employee is a manager
                                        $q3 = "SELECT * FROM manager WHERE eid='". $row2["EID"]. "'";
                                        $s3 = oci_parse($cc, $q3);
                                        if(!$s3) {
                                            $data['message'] = OCIERROR($cc);
                                        } else {
                                            $r3 = oci_execute($s3);
                                            if(!$r3) {
                                                $data['message'] = OCIERROR($s3);
                                            } else {
                                                if(oci_fetch_array($s3, OCI_ASSOC)) {
                                                    $data["U_TYPE"] = "mgr";
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            //$data = $row;
                        }                         
                    }
                    oci_free_statement($stid);
                    oci_close($cc);                  
                }
            break;

            case 'info' :
                //assuming we're passing email and id
                //does this work, do we need to check for specific ID?
                $cc = $_SESSION["c"];
                $email = $request->url_elements[3];
                $id = $request->url_elements[4];
                //^check that these exist, if not return error.
                $query = "SELECT * FROM users where email='" . $email. "'";
                $stid = oci_parse($cc, $query);
                if (!$stid) {
                    $e = OCIError($cc);
                    $data['message'] = $e['message'];
                    //trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
                } else {
                    $r = oci_execute($stid);
                    if (!$r) {
                        $e = OCIERROR($stid);
                        $data['message'] = $e['message'];
                        //trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
                    } else {
                        $row = oci_fetch_array($stid, OCI_ASSOC);
                        if($row["U_TYPE"] == 2) {
                            //get customer info -------missing--------
                            $q2 = "SELECT * FROM customer c, member m where c.cid = m.cid and c.email = '". $email. "'";
                            $s2 = oci_parse($cc, $q2);
                            if(!$s2) {
                                $data['message'] = OCIERROR($cc);
                            } else {
                                $r2 = oci_execute($s2);
                                $data = oci_fetch_array($s2, OCI_ASSOC);                           
                            }                           

                            $data["U_TYPE"] = "mem";
                        } else {
                            //either manager or employee
                            $q2 = "select * from manager where eid='". $id. "'";
                            $s2 = oci_parse($cc, $q2);
                            if(!$s2) {
                                $data['message'] = OCIERROR($cc);
                            } else {
                                $r2 = oci_execute($s2);
                                if(!$r2) {
                                    $data['message'] = OCIERROR($s2);
                                } else {
                                    $q3 = "SELECT * FROM employee WHERE eid='". $id. "'";
                                    $s3 = oci_parse($cc, $q3);
                                    if(!$s3) {
                                        $data['message'] = OCIERROR($cc);
                                    } else {
                                        $r3 = oci_execute($s3);
                                        if(!$r3) {
                                            $data['message'] = OCIERROR($s3);
                                        } else {
                                            $data = oci_fetch_array($s3, OCI_ASSOC);
                                            /*if(oci_fetch_array($s3, OCI_ASSOC)) {
                                                $data["U_TYPE"] = "mgr";
                                            } else {
                                                $data["U_TYPE"] = "emp";
                                            }*/
                                            $row2 = oci_fetch_array($s2, OCI_ASSOC);
                                            //if row is 1 then employee. need to check whether manager
                                            if(!$row2) {
                                                //employee
                                                $data["U_TYPE"] = "emp";                                   
                                            } else {
                                                //user is a member
                                                $data["U_TYPE"] = "mgr";
                                            }                                            
                                        }
                                    }                                                                        
                                }                           
                            }

                        }                        
                    }
                }
                oci_free_statement($stid);
                oci_close($cc);          
            break;            
				
            case 'new':
              $cc = $_SESSION["c"];
              $name = $request->parameters["name"];
              $email = $request->parameters["email"];
              $password = $request->parameters["password"];
              $address = $request->parameters["address"];
              $cardType = $request->parameters["cardType"];
              $cardNum = $request->parameters["cardNum"];

              $q = "SELECT * from users where email='". $email. "'";
              $st = oci_parse($cc, $q);
              oci_execute($st);
              $rr = oci_fetch_array($st, OCI_ASSOC);
              if($rr) {
                $data = $rr;
                //$data["q"] = $q;
                $data["error"] = "email address already exists. Please provide a different email";
                break;
              } else {
                $cid = rand(1, 999999);
                $stid = oci_parse($cc, 'select cid from customer');
                oci_execute($stid);
                $exists = True;
                while($exists){                  
                  while ($row1 = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
                    foreach ($row1 as $item1) {
                        if($item1 == $cid){
                            $cid = $cid + 1;
                            $exists=True;
                        }
                        else{
                            $exists=False;
                        }
                    }
                  }
                }
                oci_free_statement($stid);
            
                $sql69 = "INSERT INTO users VALUES('$email', 2)";
                $st69=oci_parse($cc, $sql69);
                if(!oci_execute($st69)){
                    $data["error"] = "user addition unsuccessful";
                }
                oci_free_statement($st69);
                 
                $sql69 = "INSERT INTO customer VALUES('$cid', '$name', '$email', '$address', '$cardNum', '$cardType')";
                $st69=oci_parse($cc, $sql69);
                if(!oci_execute($st69)){
                    $data["error"] = "customer addition unsuccessful";
                }
                oci_free_statement($st69);

                $sql69 = "INSERT INTO member VALUES('$cid', '$password', 100)";
                $st69=oci_parse($cc, $sql69);
                if(!oci_execute($st69)){
                    $data["error"] = "member addition unsuccessful";
                }
                oci_free_statement($st69);
              }
              oci_free_statement($st);

              $data["CID"] = $cid;
              $data["ERROR"] = false;
              $data["POINTS"] = 100;

            break;

            default:
                $data = $request->parameters;
                $data['message'] = "This data was submitted";
            break;

            }
        } else {
            $data = $request->parameters;
            $data['message'] = "This data was submitted";            
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