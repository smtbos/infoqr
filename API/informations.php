<?php
include("_config.php");
if (isset($_POST["view"])) {
    $uid = $_POST["uid"];
    $u_id = $_POST["u_id"];
    $latitude = $_POST["latitude"];
    $longitude = $_POST["longitude"];

    try {
        $stmt = $pdo->prepare("SELECT * FROM informations WHERE i_uid = :i_uid");
        $stmt->execute([':i_uid' => $uid]);
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $response["status"] = true;
            $response["data"]["information"] = $row["i_information"];
            $stmt = $pdo->prepare("INSERT INTO views(v_user, v_information, v_longitude, v_latitude) VALUES (:v_user, :v_information, :v_longitude, :v_latitude)");
            $stmt->execute([':v_user' => $u_id, ':v_information' => $row['i_id'], ':v_longitude' => $longitude, ':v_latitude' => $latitude]);
        } else {
            $response["emsg"][]  = "Invalid QR, Try Again.";
        }
    } catch (Exception $e) {
        $response["emsg"][]  = "Failed to Process Request.";
    }
    echo json_encode($response);
    exit();
}
