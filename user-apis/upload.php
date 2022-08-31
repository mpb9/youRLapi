<?php 
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, GET, POST");

$response = array();
$upload_dir = 'images/';
$server_url = 'https://you-rl.000webhostapp.com/youRLapi';

if($_FILES['file']) {
    $count = count($_FILES['file']['name']);
    for ($i = 0; $i < $count; $i++) {

        $file_name = $_FILES["file"]["name"][$i];
        $file_tmp_name = $_FILES["file"]["tmp_name"][$i];
        $error = $_FILES["file"]["error"][$i];
    
        if($error > 0){
            array_push($response,array(
                "status" => "error",
                "error" => true,
                "message" => "Error uploading the file!",
                "number" => $i
            ));
            $upload_error = array(
                "response" => $response,
                "server_url" => $server_url,
                "file_name" =>  $file_name,
                "file_tmp_name" => $file_tmp_name,
                "number" => $i,
                "count" => $count,
                "error" => $error,
                "why" => "error uploading file"
            );
            echo json_encode($upload_error);
        }else{

            $random_name = rand(1000,1000000)."-".$file_name;
            $upload_name = $upload_dir.strtolower($random_name);
            $upload_name = preg_replace('/\s+/', '-', $upload_name);

            if(move_uploaded_file($file_tmp_name , $upload_name)) {

                // Convert uploaded file into Base64
                $path = './'.$upload_name;
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $base64URL = 'data:image/' . $type . ';base64,' . base64_encode($data);

                array_push($response,array(
                    "status" => "success",
                    "error" => false,
                    "message" => "File uploaded successfully",
                    "url" => $server_url."/".$upload_name,
                    "base64" => $base64URL,
                    "total" => $count
                ));
                echo json_encode('success ' .$upload_name);
            }else {

                array_push($response,array(
                    "status" => "danger",
                    "error" => true,
                    "url" =>  $file_name,
                    "message" => "Error uploading the file!"
                ));

                $upload_error = array(
                    "uploadname" => $upload_name,
                    "server_url" => $server_url,
                    "url" =>  $file_name,
                    "why" => "cant move file"
                );
                echo json_encode($upload_error);
            }
        }
    }

}else{
    $response = array(
        "status" => "error",
        "error" => true,
        "message" => print_r($_FILES['file']),
        "why" => 'no file'
    );
    echo json_encode($response);

}


?>