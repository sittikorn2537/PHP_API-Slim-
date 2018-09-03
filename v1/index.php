<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
//exec("chmod -R 777 ../../data/MemberImage");
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization, AuthorizationToken, AuthorizationSession, AuthorizationPage');

require '../libs/Slim/Slim.php';

\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
$user_id = NULL;

require_once 'DbHandler.php';

/* ########################################################################### */
/* ########################### ฟังก์ชั่นที่ไม่มีการตรวจสอบ ########################### */
/* ########################################################################### */



// url - /DownloadRptByFund
$app->get('/newArticle', function() use ($app) {

      $response = array();
      $db = new DbHandler();
      $result = $db->newArticle();
      if ($result != NULL) {
        $response["res_code"] = "00";
        $response['res_text'] = "";
        $response["res_result"] = $result;
      } else {
        $response['res_code'] = "01";
        $response['res_text'] = "ไม่พบข้อมูล";
      }
      echoRespnse(200, $response);
});


$app->get('/home', function() use ($app) {

      $Lang = htmlspecialchars($app->request->post('Lang'), ENT_QUOTES);
      $response = array();
      $db = new DbHandler();
      $result = $db->home();
      if ($result != NULL) {
        $response["res_code"] = "00";
        $response['res_text'] = "";
        $response["res_result"] = $result;
      } else {
        $response['res_code'] = "01";
        $response['res_text'] = "ไม่พบข้อมูล";
      }
      echoRespnse(200, $response);
});

$app->post('/list', function() use ($app) {

      $ID = htmlspecialchars($app->request->post('ID'), ENT_QUOTES);
      $Row = htmlspecialchars($app->request->post('Row'), ENT_QUOTES);
      $response = array();
      $db = new DbHandler();
      $result = $db->dataList($ID,$Row);
      if ($result != NULL) {
        $response["res_code"] = "00";
        $response['res_text'] = "";
        $response["res_result"] = $result;
      } else {
        $response['res_code'] = "01";
        $response['res_text'] = "ไม่พบข้อมูล";
      }
      echoRespnse(200, $response);
});

/*** เช็ค service ***
 * url - /Checkserver
 */
$app->get('/Checkserver', function() use ($app) {
            $response["HTTP Code"] = "200";
            $response['Status'] = "OK";
            echoRespnse(200, $response);
        });


/*** เช็ค authenticate ***
 * url - /Checkauthenticate
 */
$app->get('/Checkauthenticate','authenticate', function() use ($app) {
            $response["HTTP Code"] = "200";
            $response['Status'] = "OK";
            echoRespnse(200, $response);
        });

/* ๑๑๑๑๑๑๑๑๑๑๑๑๑๑๑๑๑๑๑๑๑๑๑๑๑๑๑๑๑๑๑๑๑๑๑ ฟังก์ชั่น ๑๑๑๑๑๑๑๑๑๑๑๑๑๑๑๑๑๑๑๑๑๑๑๑๑๑๑๑๑๑๑๑๑๑๑ */


/*** แสดงผล json ***/
function echoRespnse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    $app->status($status_code);
    $app->contentType('application/json');
    echo json_encode($response);
}


function authenticate(\Slim\Route $route) {
    $headers = apache_request_headers();
    $response = array();
    $app = \Slim\Slim::getInstance();
    if (isset($headers['Authorization'])) {
        $db = new DbHandler();
        $api_key = $headers['Authorization'];
        if (!$db->isValidApiKey($api_key)) {
            $response["res_code"] = "01";
            $response["res_text"] = "Api key ไม่ถูกต้อง ไม่มีสิทธิ์การเข้าถึงข้อมูล";
            $response["api_key"] = $api_key;
            $response["device_uuid"] = $device_uuid;
            $response["headers"] = $headers;
            echoRespnse(200, $response);
            $app->stop();
        } else {
            global $user_id;
            $user_id = $db->getUserId($api_key);
        }
    } else {
        $response["res_code"] = "02";
        $response["res_text"] = "ไม่พบ Api key";
        echoRespnse(200, $response);
        $app->stop();
    }
}


$app->run();
?>
