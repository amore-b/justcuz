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
				
			case 'cid' :
				//does this work, do we need to check for specific ID?
				$cc = $_SESSION["c"];
                $cid = $request->url_elements[3];
                $query = "SELECT email, cid FROM customer where cid='" . $cid. "' union select email, eid from employee where eid = '". $cid. "'";
                //$query = "SELECT FROM customer c, member m where c.cid = m.cid and c.cid='" . $cid. "'";//= '". $cid. "'";
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
                        //get user type based on email
                        //if employee 1 then check whether manager
                        $row = oci_fetch_array($stid, OCI_ASSOC);
                        $q2 = "select u_type from users where email='". $row["EMAIL"]. "'";
                        $s2 = oci_parse($cc, $q2);
                        if(!$s2) {
                            $data['message'] = OCIERROR($cc);
                        } else {
                            $r2 = oci_execute($s2);
                            if(!$r2) {
                                $data['message'] = OCIERROR($s2);
                            } else {
                                $row2 = oci_fetch_array($s2, OCI_ASSOC);
                                //if row is 1 then employee. need to check whether manager
                                if($row2["U_TYPE"]== 1) {
                                 //check if employee is a manager
                                    $q3 = "SELECT * FROM manager WHERE eid='". $row["EID"]. "'";
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
                                            } else {
                                                $data["U_TYPE"] = "emp";
                                            }
                                        }
                                    }                                   
                                } else {
                                    //user is a member
                                    $data["U_TYPE"] = "mem";
                                }
                                //$data = $row3;
                                //$data["U_TYPE"] = "emp";

                            }
                        }                        
                    }
                }
                oci_free_statement($stid);
                oci_close($cc);          
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