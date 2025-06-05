<?php

    session_start();

    if(isset($_SESSION["user"])){
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='p'){
            header("location: ../login.php");
            exit();
        }
    }else{
        header("location: ../login.php");
        exit();
    }
    
    
    if($_GET && isset($_GET["id"])){
        //import database
        include("../connection.php");
        $id = intval($_GET["id"]);
        // Only delete the appointment for the logged-in patient
        $useremail = $_SESSION["user"];
        $sqlmain = "select pid from patient where pemail=?";
        $stmt = $database->prepare($sqlmain);
        $stmt->bind_param("s", $useremail);
        $stmt->execute();
        $result = $stmt->get_result();
        $userfetch = $result->fetch_assoc();
        $pid = $userfetch["pid"];
        // Delete only if the appointment belongs to this patient
        $sql = $database->prepare("DELETE FROM appointment WHERE appoid=? AND pid=?");
        $sql->bind_param("ii", $id, $pid);
        $sql->execute();
        $sql->close();
        header("location: appointment.php");
        exit();
    }


?>