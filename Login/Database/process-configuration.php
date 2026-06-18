<?php
    $serverName = "localhost\SQLEXPRESS";
    $database = "bsp_document_request";
    $username = "bsp_php";
    $password = "BSP1234";

    try {
        $conn = new PDO("sqlsrv:Server=$serverName;Database=$database", $username, $password);
        // Set error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
?>