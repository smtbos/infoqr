<?php
include("_config.php");
if (isset($_POST["login"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE u_username = :username");
        $stmt->execute([':username' => $username]);
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($row['u_password'] == $password) {
                $response["status"] = true;
                $response["data"]["u_id"] = $row["u_id"];
                $response["smsg"][]  = "Login Successfull";
            } else {
                $response["emsg"][]  = "Invalid Password";
            }
        } else {
            $response["emsg"][]  = "Invalid Username";
        }
    } catch (PDOException $e) {
        $response["emsg"][]  = "Failed to Process Request";
    }
    echo json_encode($response);
    exit();
}
