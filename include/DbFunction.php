<?php

/**
 * Handling Function
 */
class DbFunction {

    private $size_folder = array('l','s');
    private $size_image = array(320,800);
    function __construct() {
    }


    /*** สร้าง API KEY ให้กับผู้ใช้แต่ละคน ***/
    function generateApiKey() {
        return md5(uniqid(rand(), true));
    }



      Function UTF16Encode($msg)
      {
          $total = '';
          $msg = mb_convert_encoding($msg,'UTF-16','UTF-8'); For ($i = 0; $i < mb_strlen($msg,'UTF-16'); $i++)
          {
          $total .= bin2hex(mb_substr($msg,$i,1,'UTF-16')); }
          Return strtoupper($total);
      }



    /*** อัพโหลดรูปภาพ ***/
    function uploadImageBase64($img_url, $folder_name , $lastid) {
      $nameiamge = time();
      $nameImage = '';
      $textreturn = $this->check_baseimg_ext($img_url);
      // var_dump($this->size_folder);
      // exit();
      $uploadFileNew = $this->create_folder($folder_name,$lastid,'',$nameiamge.".".$textreturn['ext'],"1");
      file_put_contents($uploadFileNew, $textreturn['url']);

      $nameImage = $nameiamge.".".$textreturn['ext'];
      $file_fullsize =$uploadFileNew;
      $data=explode("/".$lastid."/",$file_fullsize);

      for($i = 0 ; $i < count($this->size_folder) ; $i++)
			{
        // echo $this->size_folder[$i];
        $uploadFileNew=$data[0]."/".$lastid."/".$this->size_folder[$i]."/".$data[1];
        // $file_fullsize = $uploadFileNew;
        // $uploadFileNew = $this->create_folder($folder_name,$lastid,$this->size_folder[$i],$nameiamge.".".$textreturn['ext'],"1");
        // $success = file_put_contents($uploadFileNew, $textreturn['url']);
        // if($success){
        //   $nameImage = $nameiamge.".".$textreturn['ext'];
        // }

        if($this->size_folder[$i] == "l"){
          $wid=800;
					$this->resize($wid, $uploadFileNew, $file_fullsize);
				}else{
					$wid=320;
					$this->resize($wid, $uploadFileNew, $file_fullsize);
				}
			}

      return $nameImage;
    }

    function uploadfilemessage($img_url, $folder_name , $lastid,$namefile) {
      $nameiamge = time();
      $nameImage = '';
      $textreturn = $this->check_baseimg_ext($img_url);
        if (!is_dir('../../data')){
           mkdir('../../data', 0777, true);
        }
        if (!is_dir('../../data/'.$folder_name.'')){
           mkdir('../../data/'.$folder_name.'', 0777, true);
        }
        if (!is_dir('../../data/'.$folder_name.'/'.$lastid.'/')){
           mkdir('../../data/'.$folder_name.'/'.$lastid.'/', 0777, true);
        }
        $uploadFileNew = "../../data/".$folder_name."/".$lastid."/".$namefile;

        $success = file_put_contents($uploadFileNew, $textreturn['url']);
          if($success){
            $nameImage = $nameiamge.".".$textreturn['ext'];
          }else{
              $nameImage = "123";
          }
				// $file_fullsize = $uploadFileNew;
      return $textreturn ;
    }



    /*** เช็คนามสกุลภาพ ***/
    function check_baseimg_ext($data){
  		 list($type, $data) = explode(';', $data);
  		 list(, $data) = explode(',', $data);
  		 $data = base64_decode($data);
  		 list($type_data, $mime) = explode(':', $type);
  		 switch ($mime) {
  			  case 'image/jpeg':
  					  $dot='jpg';
  					  break;

  			  case 'image/png':
  					  $dot='png';
  					  break;

  			  case 'image/gif':
  					 $dot='gif';
  					 break;

          case 'application/zip':
   					 $dot='zip';
   					 break;
          case 'application/pdf':
 					   $dot='pdf';
 					   break;

  			  default:
  					 $dot='no_dot';
  		}
  		return array('url' => $data, 'ext' => $dot);
  	}

    /*** รีไซร์รูป ***/
    function resize($newWidth, $targetFile, $originalFile) {

      $info = getimagesize($originalFile);
      $mime = $info['mime'];

      switch ($mime) {
          case 'image/jpeg':
              $image_create_func = 'imagecreatefromjpeg';
              $image_save_func = 'imagejpeg';
              break;

          case 'image/png':
              $image_create_func = 'imagecreatefrompng';
              $image_save_func = 'imagepng';
              break;

          case 'image/gif':
              $image_create_func = 'imagecreatefromgif';
              $image_save_func = 'imagegif';
              break;

          default:
              throw new Exception('Unknown image type.');
      }

      $img = $image_create_func($originalFile);
      imagejpeg($img, $targetFile, 65);
      //ImageDestroy($image);
      list($width, $height) = getimagesize($targetFile);


      $newHeight = ($height / $width) * $newWidth;
      $tmp = imagecreatetruecolor($newWidth, $newHeight);
      imagecopyresampled($tmp, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
      if (file_exists($targetFile)) {
          unlink($targetFile);
      }
      $image_save_func($tmp, "$targetFile");
    }

  	function create_folder($namefolder,$last_id,$size_text,$namefile,$type){

  		//$uploaddirNew = '../../data/'.$namefolder.'/'.$last_id.'/'.$size_text.'/';
      $uploaddirNew = '../../data/'.$namefolder.'/'.$last_id;

  		$uploadFileNew = $uploaddirNew.'/'.$namefile;
      if ($size_text!='') {
        $uploadFileNew = $uploaddirNew.'/'.$size_text.'/'.$namefile;
      }
      if (!is_dir('../../data')){
  		   mkdir('../../data', 0777, true);
  		}
  		if (!is_dir('../../data/'.$namefolder.'')){
  		   mkdir('../../data/'.$namefolder.'', 0777, true);
  		}

      if (!is_dir('../../data/'.$namefolder.'/'.$last_id.'/')){
         mkdir('../../data/'.$namefolder.'/'.$last_id.'/', 0777, true);
         mkdir("../../data/".$namefolder."/".$last_id."/s");
         mkdir("../../data/".$namefolder."/".$last_id."/l");
      }else{
        $this->remove_dir("../../data/".$namefolder."/".$last_id);
        // // rmdir("../../data/".$namefolder."/".$last_id."/");
        mkdir("../../data/".$namefolder."/".$last_id."/");
        mkdir("../../data/".$namefolder."/".$last_id."/s");
        mkdir("../../data/".$namefolder."/".$last_id."/l");
      }



  		/*if (!is_dir('../../data/'.$namefolder.'/'.$last_id.'/'.$size_text.'/')){
  		   mkdir('../../data/'.$namefolder.'/'.$last_id.'/'.$size_text.'/', 0777, true);
  		}*/
      // if($type == '1'){
      //   rmdir("../../data/img_member/".$last_id."/");
      //   //  $this->remove_dir('../../data/'.$namefolder.'/'.$last_id.'/');
      //   //$this->remove_dir('../../data/img_member/107');
      // }


      // $this->remove_dir('../../data/'.$namefolder.'/'.$last_id.'/');

      // if (condition) {
      //   # code...
      // }


  		return $uploadFileNew;
  	}

    function remove_dir($dir)
    {
       if(is_dir($dir))
       {
         $dir = (substr($dir, -1) != "/")? $dir."/":$dir;

         $openDir = opendir($dir);
         while($file = readdir($openDir))
         {
           if(!in_array($file, array(".", "..")))
           {
             if(!is_dir($dir.$file))
             {
               @unlink($dir.$file);
             }
             else
             {
               $this->remove_dir($dir.$file);
             }
           }
         }
         closedir($openDir);
         @rmdir($dir);
       }
     }

    function filter_sql($filter){
      $filtersql = "";
      $wheresql = " and ";
      if(isset($filter) && $filter != ''){
        $filtersql_tem = $filter;
        $res_filter = json_decode($filtersql_tem,true);
        foreach($res_filter as $key=>$val)
        {
          if($val == ""){
            $filtersql .= "";
          }else{
            $sb_per_bf = "";
            $sb_per_af = "";
            $sb_per_fun = "'";
            if (stripos($key, ">=") !== false) { // ?filter={">=name_item":"10"}
              $symbolsql = ">= ";
            }else if (stripos($key, "<=") !== false) { // ?filter={"<=name_item":"10"}
              $symbolsql = "<= ";
            }else if (stripos($key, "<") !== false) { // ?filter={"<name_item":"10"}
              $symbolsql = "< ";
            }else if (stripos($key, ">") !== false) { // ?filter={">name_item":"10"}
              $symbolsql = "> ";
            }else if (stripos($key, "!=") !== false) { // ?filter={"!=name_item":"10"}
              $symbolsql = "!= ";
            }else if (stripos($key, "%") !== false) { // ?filter={"% name_item %":"10"}
              if($key[0] == "%"){ $sb_per_bf = "%";}
              if($key[strlen($key)-1] == "%"){ $sb_per_af = "%";}
              $key = str_replace("%",'', $key);
              $symbolsql = "Like ";
            }else if (stripos($key, "(") !== false) { // ?filter={"(name_item)":"10"}
              if($key[0] == "("){ $sb_per_bf = "(";}
              if($key[strlen($key)-1] == ")"){ $sb_per_af = ")";}
              $key = str_replace("(",'', $key);
              $key = str_replace(")",'', $key);
              $symbolsql = "IN";
              $sb_per_fun = "";
            }else{
              $symbolsql = "=";
            }
            $key = str_replace($symbolsql,'', $key);
            $filtersql .= $wheresql.$key." ".$symbolsql.$sb_per_fun.$sb_per_bf.$val.$sb_per_af.$sb_per_fun;
            //$wheresql = " and ";
          }

        }
        return $filtersql;
      }
    }


    function limit_sql($limit,$offset){
      $limitsql = "";
      if(isset($limit) && $limit != ''){
        if(isset($offset) && $offset != ''){
          $offset = ($offset*10).",";
        }
        $limitsql = " limit ".$offset.$limit;
      }
      return $limitsql;
    }

    function sort_sql($sort){
      $sortsql = "";
      if(isset($sort) && $sort != ''){
        $spsort = split(",",$sort);
        foreach($spsort as $key=>$val){
          $checktypesort = strpos($val,"-");
          if($checktypesort  === false){
            $typesort = " ASC ";
          }else{
            $typesort = " DESC ";
            $val = substr($val,1);
          }
          if($key > 0){
            $sortsql .= ",";
          }
          $sortsql .= $val.$typesort;
        }
        $sortsql = " ORDER BY ".$sortsql;
      }
      return $sortsql;
    }

    function sendEmail($from_email,$from_name,$to_email,$to_name,$subject,$message,$newpassword2,$mail_check){
  		$body = $message;
  		try {
  			$mail = new PHPMailer(true);
  			$mail->CharSet = "utf-8";
  			$mail->IsSMTP();
  			$mail->SMTPDebug = 0;
  			$mail->SMTPAuth = true;
  			$mail->SMTPSecure = "ssl";	// sets the prefix to the servier
        $mail->Host = "smtp.gmail.com";
        $mail->Port = "465"; // or 587
  			// $mail->Username = "ditp.noreply@gmail.com"; // account SMTP
  			// $mail->Password = "ibusiness"; // รหัสผ่าน SMTP
        $mail->Username = "******"; // account SMTP
  			$mail->Password = "******"; // รหัสผ่าน SMTP

  			$mail->SetFrom($from_email, $from_name);
  			$mail->AddReplyTo($from_email, $from_name);
  			$mail->Subject = $subject;
  			$mail->MsgHTML($body);
  			$toEmail= $to_email;
  			$toName = $to_name;
  			$i=0;
  			// foreach($toEmail as $toEmail_add){
  			$mail->AddAddress($toEmail,$toName);
  				// $i++;
  			// }
        // return "Success";
  			if(!$mail->Send()) {
  				return 1;
  			} else {
  				return "Success ";
  			}
  		} catch (phpmailerException $e) {
  		  return  $e->getMessage();
  		} catch (Exception $e) {
  		  return   $e->getMessage();
  		}
	}

  function random_password( $length = 8 ) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOP1234567890";
    $password = substr( str_shuffle( $chars ), 0, $length );
    return $password;
  }



}

?>
