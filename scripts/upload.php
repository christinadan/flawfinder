<?php
//Christina Dan
//1000795128
//March 22, 2015
//Programming Assignment 3
$target_dir = "../uploads/";
$result_dir = "../results/";
$user_email = filter_var($_POST["userEmail"], FILTER_SANITIZE_EMAIL);
$target_file = $target_dir . $user_email . "-" . basename($_FILES["fileToUpload"]["name"]);
$result_file = $result_dir . $user_email . "-" . basename($_FILES["fileToUpload"]["name"]) . ".html";
$uploadOk = 1;
$fileType = pathinfo($target_file,PATHINFO_EXTENSION);

if(isset($_POST["submit"])) {
    $check = filesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        $uploadOk = 0;
    }
}
// Check if file already exists
if (file_exists($target_file)) {
    $uploadOk = 0;
    $array = array("fileError"=>true, "results"=>"File already exists");
    echo json_encode($array);
}
// Check file size
if ($_FILES["fileToUpload"]["size"] > 10000000) {
    $uploadOk = 0;
    $array = array("fileError"=>true, "results"=>"File size exceeds limit (10MB)");
    echo json_encode($array);
}
// Allow certain file formats
if($fileType != "c" && $fileType != "cpp" ) {
    $uploadOk = 0;
    $array = array("fileError"=>true, "results"=>"File type must be .c or.cpp");
    echo json_encode($array);
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    $array = array("fileError"=>true, "results"=>"There was a problem uploading your file. Please try again.");
    echo json_encode($array);
} 
else { // if everything is ok, try to upload file
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file) && filter_var($_POST["userEmail"], FILTER_SANITIZE_EMAIL)) {
        exec("(cd ../flawfinder-1.31 && ./flawfinder --html " 
            . escapeshellarg($target_file) . " > " . escapeshellarg($result_dir) . escapeshellarg($user_email) . "-" 
            . basename( $_FILES["fileToUpload"]["name"]) . ".html)");
        if(file_exists($result_file)) {
            $content = file_get_contents($result_dir . $user_email . "-" . basename( $_FILES["fileToUpload"]["name"]) . ".html");
            $array = array("fileError"=>false, "results"=>str_replace("../uploads/" . "$user_email" . "-", "", $content));
            echo json_encode($array);
            unlink($result_file);
        }
        if($target_file) {
            unlink($target_file);
        }
    } else {
        $array = array("fileError"=>true, "results"=>"There was a problem uploading your file. Please try again.");
        echo json_encode($array);
    }
}
?>