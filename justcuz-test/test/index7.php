<?php
spl_autoload_register('apiAutoload');
function apiAutoload($classname) {
  if (preg_match('/[a-zA-Z]+Controller$/', $classname)) {
      include __DIR__ . '/controllers/' . $classname . '.php';
      return true;
  } elseif (preg_match('/[a-zA-Z]+Model$/', $classname)) {
      include __DIR__ . '/models/' . $classname . '.php';
      return true;
  } elseif (preg_match('/[a-zA-Z]+View$/', $classname)) {
      include __DIR__ . '/views/' . $classname . '.php';
      return true;
  } else {
      include __DIR__ . '/library/' . str_replace('_', DIRECTORY_SEPARATOR, $classname) . '.php';
      return true;
  }
  return false;
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


if ($c=OCILogon("ora_n1c0b", "a66216086", "dbhost.ugrad.cs.ubc.ca:1522/ug")) { 
  $_SESSION["c"] = $c;
  //echo "Successfully connected to Oracle.\n"; 
} else { 
  $err = OCIError(); 
  //echo "Oracle Connect Error " . $err['message']; 
}
/*
$stid = oci_parse($c, "SELECT * from member where cid=1212 and password='password'");
if (!$stid) {
    $e = OCIError($c);
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}
$r = oci_execute($stid);
if (!$r) {
    $e = OCIERROR($stid);
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}
*/
$request = new Request();

// route the request to the right place
$controller_name = ucfirst($request->url_elements[1]) . 'Controller';
if (class_exists($controller_name)) {
    $controller = new $controller_name();
    $action_name = strtolower($request->verb) . 'Action';
    $result = $controller->$action_name($request);

    $view_name = ucfirst($request->format) . 'View';
    if(class_exists($view_name)) {
        $view = new $view_name();
        $view->render($result);
    }
}

//oci_free_statement($stid);
//oci_close($c);
?>