<?php
include("_config.php");
if (isset($_GET["login"])) {
    $username = $_GET["username"];
    $password = $_GET["password"];
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE u_username = :username");
        $stmt->execute([':username' => $username]);
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($row['u_password'] == $password) {
                $response["status"] = true;
                $response["data"]["u_id"] = intval($row["u_id"]);
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

if (isset($_GET["register"])) {
    $name = $_GET["name"];
    $mobile = $_GET["mobile"];
    $email = $_GET["email"];
    $city = $_GET["city"];
    $address = $_GET["address"];
    $username = $_GET["username"];
    $password = $_GET["password"];
    try {
        $stmt = $pdo->prepare("INSERT INTO users(u_name, u_mobile, u_email, u_city, u_address, u_username, u_password) VALUES (:u_name, :u_mobile, :u_email, :u_city, :u_address, :u_username, :u_password)");
        $stmt->execute([':u_name' => $name, ':u_mobile' => $mobile, ':u_email' => $email, ':u_city' => $city, ':u_address' => $address, ':u_username' => $username, ':u_password' => $password]);
        if ($pdo->lastInsertId() > 0) {
            $response["status"] = true;
            $response["data"]["u_id"] = $pdo->lastInsertId();
            $response["smsg"][]  = "Register Successfull";
        } else {
            $response["emsg"][]  = "Failed to Register";
        }
    } catch (PDOException $e) {
        $response["emsg"][]  = "Failed to Process Request";
    }
    echo json_encode($response);
    exit();
}
