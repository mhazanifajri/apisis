<?php
 
ServerConfig();

$PdfUploadFolder = 'files/';
 
$ServerURL = 'https://sisinfo.herokuapp.com/'.$PdfUploadFolder;
 
if($_SERVER['REQUEST_METHOD']=='POST'){
 
    if(isset($_POST['name']) and isset($_FILES['pdf']['name'])){

	$con = mysqli_connect(HostName,HostUser,HostPass,DatabaseName);
		
        $PdfName = $_POST['name'];
		
        $PdfInfo = pathinfo($_FILES['pdf']['name']);
 
        $PdfFileExtension = $PdfInfo['extension'];
 
        $PdfFileURL = $ServerURL . GenerateFileNameUsingID() . '.' . $PdfFileExtension;
 
        $PdfFileFinalPath = $PdfUploadFolder . GenerateFileNameUsingID() . '.'. $PdfFileExtension;
 
        try{
			
            move_uploaded_file($_FILES['pdf']['tmp_name'],$PdfFileFinalPath);
			
            $InsertTableSQLQuery = "INSERT INTO tbl_modul (id_topic_modul, nama_modul, tgl_dibuat, link_file) VALUES ('1', 'ngising', '2018-09-12','$PdfUploadFolder.$PdfName') ;";

            mysqli_query($con,$InsertTableSQLQuery);

        }catch(Exception $e){} 
        mysqli_close($con);
		
    }
}

function ServerConfig(){
	
define('HostName','mirrorsfa.tk');
define('HostUser','sar');
define('HostPass','s4r');
define('DatabaseName','sar');
	
}

function GenerateFileNameUsingID(){
    
	$con2 = mysqli_connect(HostName,HostUser,HostPass,DatabaseName);
	
	$GenerateFileSQL = "SELECT max(id) as id FROM tbl_modul";
	
    $Holder = mysqli_fetch_array(mysqli_query($con2,$GenerateFileSQL));

    mysqli_close($con2);
	
    if($Holder['id']==null)
	{
        return 1;
	}
    else
	{
        return ++$Holder['id'];
	}
}

?>
