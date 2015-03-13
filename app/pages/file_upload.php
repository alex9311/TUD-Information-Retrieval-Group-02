<?php
//this file handles new ideas being submitted 


if($_POST['formSubmit'] == "Submit"){	
	//first figure out where the image will be stored and get file type
	$target_dir = "/var/www/html/uploads/";
	$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
	$uploadOk = 1;
	$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

	if (!file_exists($target_dir)){
		return_to_submit_page('Upload directory does not exist, contant sys admin');
	}

	if(!is_writable($target_dir)) {
		return_to_submit_page('Upload directory is not writable, contact sys admin.');
	}

	// Check if image file is a actual image or fake image
	if(isset($_POST["submit"])) {
	    	$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
	    	if($check !== false) {
	        	$uploadOk = 1;
	    	} else {
	        	$uploadOk = 0;
	        	return_to_submit_page("File is not an image.");
	    	}
	}
	// Check if file already exists
	if (file_exists($target_file)) {
	    	$uploadOk = 0;
	    	return_to_submit_page("Sorry, file already exists.");
	}
	// Check file size
	if ($_FILES["fileToUpload"]["size"] > 500000) {
	    	$uploadOk = 0;
	    	return_to_submit_page("Sorry, your file is too large.");
	}
	// Allow certain file formats
	if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
	    	$uploadOk = 0;
	    	return_to_submit_page("Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
	}

	//try to move the image to the target directory
	if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
		insert_idea($_POST['text_description'],$target_file);
		return_to_submit_page("success");
	} else {
		return_to_submit_page("Sorry, there was an error uploading your file.");
	}
}

function insert_idea($valueText,$valueImage){
	$servername = "localhost";
	$username = "root";
	$password = "root";
	$dbname = "Sparked";

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
    		die("Connection failed: " . $conn->connect_error);
	}
	//create query string from parameters
        $sql = 'INSERT INTO Idea (text_description, image) VALUES ("'.$valueText.'", "'.$valueImage.'")';

	//and insert!
	if ($conn->query($sql) === TRUE) {
       		echo "New record created successfully";
       	} else {
       		echo "Error: " . $sql . "<br>" . $conn->error;
       	}
}

function return_to_submit_page($status){
	session_start();
       	$_SESSION["POST"] = $status;
       	header("Location: submit.php");
       	exit();
}
?>
