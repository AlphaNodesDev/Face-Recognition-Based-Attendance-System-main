<?php 
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

if(isset($_GET['save'])){
    $admissionNo=$_GET['admissionNo'];
    $email=$_GET['email'];
    $password = md5($_GET['password']);

    
    // Check if email and password match
    $query = "SELECT * FROM tblteachers WHERE emailAddress = '$email' AND password = '$password'";
    $rs = $conn->query($query);
    $num = $rs->num_rows;

    if($num > 0) {
        // Email and password are correct, proceed with the attendance saving
        $row = $rs->fetch_assoc();
        $classId = $row['classId'];
        $classArmId = $row['classArmId'];

        // Get session and term
        $querey=mysqli_query($conn,"select * from tblsessionterm where isActive ='1'");
        $rwws=mysqli_fetch_array($querey);
        $sessionTermId = $rwws['Id'];

        $dateTaken = date("Y-m-d");

        $qurty=mysqli_query($conn,"select * from tblattendance  where classId = '$classId' and classArmId = '$classArmId' and dateTimeTaken='$dateTaken'");
        $count = mysqli_num_rows($qurty);

        if($count == 0) { //if Record does not exist, insert the new record
            // Insert the students' records into the attendance table on page load
            $qus=mysqli_query($conn,"select * from tblstudents  where classId = '$classId' and classArmId = '$classArmId'");
            while ($ros = $qus->fetch_assoc()) {
                $qquery=mysqli_query($conn,"insert into tblattendance(admissionNo,classId,classArmId,sessionTermId,status,dateTimeTaken) 
                value('$ros[admissionNumber]','$classId','$classArmId','$sessionTermId','0','$dateTaken')");
            }
        }

        // Update status to '1' where admissionNo matches
        $update_query = "UPDATE tblattendance SET status='1' WHERE admissionNo = '$admissionNo'";
        mysqli_query($conn, $update_query);

        // Send success response to Python code
        echo "true";
    } else {
        // Email and password are incorrect, send false to Python code
        echo "false";
    }

    
}
?>
