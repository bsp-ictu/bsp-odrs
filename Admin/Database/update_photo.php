<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include('../../Login/Database/process-configuration.php');

if (isset($_POST['submit']) && isset($_FILES['photo'])) {
    $accountName = $_SESSION['accountname'];
    $accountEmail = $_SESSION['accountemail'];
    $currentPhoto = $_SESSION['accountpic'];

    $imageFile = $_FILES['photo'];
    $imageName = basename($imageFile['name']);
    $imageTmp = $imageFile['tmp_name'];

    $imageFolderPath = "../../Images/Employee_Pic/";
    $newImageName = uniqid('', true) . "-" . preg_replace("/[^A-Za-z0-9.\-_]/", '', $imageName);

    if (!empty($imageTmp) && move_uploaded_file($imageTmp, $imageFolderPath . $newImageName)) {
        
        if ($currentPhoto !== "user_default.jpg" && file_exists($imageFolderPath . $currentPhoto)) {
            @unlink($imageFolderPath . $currentPhoto);
        }

        try {
            $updateProfilePic = "UPDATE bsp_account SET account_pic = :pic WHERE account_email = :email AND account_name = :username";
            $stmt = $conn->prepare($updateProfilePic);
            $stmt->bindParam(':pic', $newImageName);
            $stmt->bindParam(':email', $accountEmail);
            $stmt->bindParam(':username', $accountName);

            if ($stmt->execute()) {
                $_SESSION['accountpic'] = $newImageName;
                $conn->prepare("
                        INSERT INTO audit_trail (
                            account_name, action_type, table_name, record_id, old_value, new_value, action_description
                        ) VALUES (?, 'UPDATE', 'bsp_account', '-', '-', '-', 'Changed the profile picture')
                    ")->execute([
                        $_SESSION['accountname']
                ]);
                header("Location: ../editAccount.php");
                exit();
            } else {
                echo "Error updating profile picture.";
            }
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
        }

    } else {
        echo "File upload failed.";
    }
}
?>