<?php
class DbHandler {

    public $conn,$func,$ktam_ws,$logws_id;
    function __construct() {
        require_once '../include/DbConnect.php';
        $db = new DbConnect();
        $this->conn = $db->connect();
        require_once '../include/DbFunction.php';
        $this->func = new DbFunction();
        $this->result=array();
    }
    public function DateThai($strDate)
    	{
    		$strYear = date("Y",strtotime($strDate))+543;
    		$strMonth= date("n",strtotime($strDate));
    		$strDay= date("j",strtotime($strDate));
    		$strHour= date("H",strtotime($strDate));
    		$strMinute= date("i",strtotime($strDate));
    		$strSeconds= date("s",strtotime($strDate));
    		$strMonthCut = Array("","ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
    		$strMonthThai=$strMonthCut[$strMonth];
    		return "$strDay $strMonthThai $strYear, $strHour:$strMinute";
    	}
    /*** HOME ***/

    public function newArticle() {
      $stmt = $this->conn->prepare("SELECT * FROM tb_article_cat WHERE cat_id = 33");
      $stmt->execute();
      $result = $stmt->get_result();
        while($res = $result->fetch_assoc()){
            $sql = $this->conn->prepare("SELECT * FROM tb_article WHERE cat_id = 33");
            $sql->execute();
            $resSql = $sql->get_result();
            while($resA = $resSql->fetch_assoc()){
              $responseA = array(
                "date_added" => $resA['date_added'],
                "subject" => $resA['subject'],
                "detail" => $resA['detail']

                );
                $outputA[]=$responseA;
            }
            $response = array(
              "cat_id" => $res['cat_id'],
              "cat_name" => $res['cat_name'],
              "article" => $outputA
       );
       $output[]=$response;
     }
        if($result->num_rows > 0){
          $stmt->close();
          return $output;
        }else{
          $stmt->close();
          return NULL;
        }
    }

    public function home() {
      $stmt = $this->conn->prepare("SELECT * FROM tb_article_cat WHERE cat_id != 33 ORDER BY ordering DESC");
      $stmt->execute();
      $result = $stmt->get_result();
        while($res = $result->fetch_assoc()){
            $response = array(
              "cat_id" => $res['cat_id'],
              "cat_name" => $res['cat_name']
       );
       $output[]=$response;
     }
        if($result->num_rows > 0){
          $stmt->close();
          return $output;
        }else{
          $stmt->close();
          return NULL;
        }
    }
    /*********** end home ************/
    public function dataList($ID,$Row) {

      $stmt = $this->conn->prepare("SELECT * FROM tb_article WHERE cat_id = $ID LIMIT $Row ORDER BY id");
      $stmt->execute();
      $result = $stmt->get_result();

        while($res = $result->fetch_assoc()){
            $response = array(
              "subject" => $res['subject'],
              "detail" => $res['detail'],
              "img1" => $res['img1']
       );
       $output[]=$response;
     }
        if($result->num_rows > 0){
          $stmt->close();
          return $output;
        }else{
          $stmt->close();
          return NULL;
        }

    }


    /*** เช็ค user จาก apikey ***/
    public function isValidApiKey($api_key) {
        $stmt = $this->conn->prepare("SELECT member_id from phya2_member WHERE member_api_key = '$api_key' ");
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }



}

?>
