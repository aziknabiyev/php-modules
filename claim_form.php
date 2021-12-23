<?php require_once('../s_mail/mail.php'); ?> 

<?php

$mail=new Mail();

$check=false;
if($_POST['time'])
{      
        $allowed = array('jpg','jpeg','pdf');
        $filename = $_FILES["file"]["name"];
        $filetype = $_FILES["file"]["type"];
        $filesize = $_FILES["file"]["size"];
        $filetmp = $_FILES["file"]["tmp_name"];
      
        $folder=preg_replace("/[^A-Za-z0-9?!]/",'',$_POST['time']);
       // print($folder);
        if (!file_exists("../upload_file/".$folder)) {
          mkdir("../upload_file/".$folder);        
        }
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if(in_array($ext,$allowed))
        {
            move_uploaded_file($filetmp, "../upload_file/".$folder."/".$filename);
            $url='https://'.$_SERVER['SERVER_NAME'].'/'.'upload_file/'.$folder.'/'.$filename; 
            die(json_encode(['data'=>['url'=>$url,'name'=>$filename]]));
        }
        else
        {
            die("Error: File format must be ".implode(',',$allowed));
        }
}

if($_POST['action'])
{
    $data=[];
        
    $errors=[];
    if(!$_POST['name']) $errors['name']='Name field is required !!!';
    else $data['name']=preg_replace("/[^A-Za-z0-9?![:space:]]/",'',$_POST['name']);

    if(!$_POST['desc']) $errors['desc']='Desc field is required !!!';
    else $data['desc']=preg_replace("/[^A-Za-z0-9?![:space:]]/",'',$_POST['desc']);

    if($_POST['action']=='claim'):
        if(!isset($_POST['type'])) $errors['type']='Type field is required !!!';
        else $data['type']=preg_replace("/[^A-Za-z0-9?![:space:]]/",'',$_POST['type']);
    endif;

    if(isset($_POST['g-recaptcha-response'])){
        $captcha=$_POST['g-recaptcha-response'];
      }
      if(!$captcha){
        $errors['captcha']='Recaptcha field is required !!!';
      }

    if(!$_POST['email']) $errors['email']='Email field is required !!!';
    else if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) $errors['email']='Please enter correct email !!!';
    else $data['email']=$_POST['email'];

    $data['desc']=preg_replace("/[^A-Za-z0-9?![:space:]]/",'',$_POST['desc']);

    $secretKey = "6LdFF-sZAAAAAOTUMaR8ncYZr7G4zzYyva8VI8ij";
    $ip = $_SERVER['REMOTE_ADDR'];
    // post request to server
    $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($secretKey) .  '&response=' . urlencode($captcha);
    $response = file_get_contents($url);
    $responseKeys = json_decode($response,true);


    if(!count($errors))
    {
        if($responseKeys["success"]) {
        
        } else {
            $errors['captcha']="Error";
            die(json_encode(['errors'=>$errors]));
        }
        $html='<ul>';
        $html.='<li style="font-size:16px">Name : '.$data['name'].'</li>';
        $html.='<li style="font-size:16px">Description : '.$data['desc'].'</li>';
        $html.='<li style="font-size:16px">Email : '.$data['email'].'</li>';
        if(isset($_POST['type'])) $html.='<li style="font-size:16px">Type : '.$data['type'].'</li>';


        $file_url=$_POST['file_url'];
        $file_name=$_POST['file_name'];
        for($i=0;$i<count($file_url);$i++)
        {
            $html.='<li style="font-size:16px">Download : <a href="'.$file_url[$i].'" download>'.$file_name[$i].'</a></li>';

        }
        $html.='</ul>';
        //print($html);
        $mail->send($html);

        die(json_encode(['data'=>$data]));

    }
    else
    {

        die(json_encode(['errors'=>$errors]));
    }
    //     print_r($_FILES);
    //     $bbb=[];
    //     foreach($filename as $k=>$file)
    //     {
    //         if($file)
    //         {
    //             $ext = pathinfo($file, PATHINFO_EXTENSION);
    //             if(in_array($ext,$allowed))
    //             {
    //                 move_uploaded_file($filetmp[$k], "../upload_file/".$folder."/".$file);
    //                 $url='https://'.$_SERVER['SERVER_NAME'].'/'.'upload_file/'.$folder.'/'.$file; 
    //                 $h_title=preg_replace("/[^A-Za-z0-9?![:space:]]/",'',$_POST['file_title'][$k]);
    //                 $html.='<li style="font-size:16px">'.$h_title.' : <a href="'.$url.'" download>download</li>';
    //                 $bbb[]=$url;
    //             }
    //             else
    //             {
    //                 die("Error: File format must be ".implode(',',$allowed));
    //             }
    //         }
    //     }
    //     echo json_encode($bbb);
      //  die();
    //     endif;
    //     $html.="</ul>";

        //print($html);
        // if(!count($errors))
        // {
        //     $mail->send($html);
        //     $check=true;
        // }
}





?>
