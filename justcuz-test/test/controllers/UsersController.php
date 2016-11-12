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
                $query = "SELECT * FROM customer c, member m where c.cid = m.cid and m.password ='". $password. "' and c.email = '". $email. "'";
                $stid = oci_parse($cc, $query);
                if (!$stid) {
                    $e = OCIError($cc);
                    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
                }
                $r = oci_execute($stid);
                if (!$r) {
                    $e = OCIERROR($stid);
                    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
                };
                $row = oci_fetch_array($stid, OCI_ASSOC);
                if(!$row) {
                    $data['message'] = "Invalid username or email";
                } else {
                    //login worked
                    $_SESSION['cid'] = $row['CID'];
                    $_SESSION['name'] = true;//$row['NAME'];
                    $_SESSION['points'] = $row['POINTS'];
					//test for param vals here
					$data = $row;//array($row);
					//$j[] = $query;
					//$data = $json;//['message']= $row[0];
                    $data['message'] = $_SESSION['cid'];
                }
                break;
				
			case 'cid' :
				//does this work, do we need to check for specific ID?
				$cc = $_SESSION["c"];
                $cid = $request->url_elements[3];
                $query = "SELECT * FROM customer c, member m where c.cid = m.cid and c.cid='" . $cid. "'";//= '". $cid. "'";
                $stid = oci_parse($cc, $query);
                if (!$stid) {
                    $e = OCIError($cc);
                    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
                }
                $r = oci_execute($stid);
                if (!$r) {
                    $e = OCIERROR($stid);
                    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
                };
                $row = oci_fetch_array($stid, OCI_ASSOC);
                if(!$row) {
                    $data['message'] = "Invalid username or email";
                } else {
                    $data = $row;//array
                }
				//get logged in user's info, if any. return it or just return true?
				//i guess return it so the front end can change its setup
			break;
			
			//case: 'logout' :
				//check if active first?
			//break;

                default:
                    $data = $request->parameters;
                    $data['message'] = "This data was submitted";
                break;

            }
        } else {
            $data = $request->parameters;
            $data['message'] = "This data was submitted";            
        }
		/*if(isset($request->url_elements[2])) {
            $user_id = (int)$request->url_elements[2];
            if(isset($request->url_elements[3])) {
                switch($request->url_elements[3]) {
                case 'friends':
                    $data["message"] = "user " . $user_id . "has many friends";
                    break;
                default:
                    // do nothing, this is not a supported action
                    break;
                }
            } else {
                $data["message"] = "here is the info for user " . $user_id;
            }
        } else {
            $data["message"] = "you want a list of users";
        }*/
        return $data;
    }

    public function postAction($request) {
        //check for login, get uname and password in different vars
        // write query 
        // if passed set vars, redirect to main, with vars set
        // else return error....but do i need an ajax? 
        if(isset($request->url_elements[2])) {
            switch($request->url_elements[2]) {
            case 'login' :
                $cc = $_SESSION["c"];
                $email = $request->parameters["email"];
                $password = $request->parameters["password"];
                $query = "SELECT * FROM customer c, member m where c.cid = m.cid and m.password ='". $password. "'";//' and c.email = '$email'";
                $stid = oci_parse($cc, $query);
                if (!$stid) {
                    $e = OCIError($cc);
                    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
                }
                $r = oci_execute($stid);
                if (!$r) {
                    $e = OCIERROR($stid);
                    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
                };
                $row = oci_fetch_array($stid, OCI_ASSOC);
                //test for param vals here
                $json = array();
                $json[] = $request;
                $data = $json;//['message']= $row[0];
                if(!$row) {
                    //invalid username or password
                } else {
                    //login worked
                    //$_SESSION['cid'] = $row["CID"];
                    //$_SESSION['name'] = $row["NAME"];
                    //$_SESSION['points'] = $row["POINTS"];
                    //$data['name'] = $_SESSION['name'];
                }
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
}
?>