<?php


	if(isset($_POST['submit'])){		
		$target_dir = "./";
		$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
		$uploadOk = 1;
		$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
		
		$new_name = $target_dir."test_data".time().$imageFileType;
		
		if($imageFileType != "jpg"){
			echo "File type is not supported";
		}
		else{
			if(file_exists($target_file)){
				echo "Sorry this file is already exists";
				// for file deletion
				// unlink($target_file);
				// echo "file is deleted";
			}
			else{
				if(move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $new_name)){
					echo "your file has been successfully uploaded: ".$new_name;
				}
				else{
					echo "please check your file";
				}
			}
		}
	}
	else{
		echo "kindly select the file for upload";
	}
?>