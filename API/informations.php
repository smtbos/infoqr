<?php
include("_config.php");
if (isset($_GET["view"])) {
    $uid = $_GET["uid"];
    $u_id = $_GET["u_id"];
    $latitude = $_GET["latitude"];
    $longitude = $_GET["longitude"];

    try {
        $stmt = $pdo->prepare("SELECT * FROM informations WHERE i_uid = :i_uid");
        $stmt->execute([':i_uid' => $uid]);
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $i_id = $row['i_id'];
            $response["status"] = true;
            $response["data"]["information"] = $row["i_information"];
            $stmt = $pdo->prepare("INSERT INTO views(v_user, v_information, v_longitude, v_latitude) VALUES (:v_user, :v_information, :v_longitude, :v_latitude)");
            $stmt->execute([':v_user' => $u_id, ':v_information' => $row['i_id'], ':v_longitude' => $longitude, ':v_latitude' => $latitude]);
            $pdo->query("UPDATE informations set i_views = (SELECT COUNT(*) FROM `views` WHERE v_information = $i_id) WHERE i_id = $i_id");
        } else {
            $response["emsg"][]  = "Invalid QR, Try Again.";
        }
    } catch (Exception $e) {
        $response["emsg"][]  = "Failed to Process Request.";
    }
    echo json_encode($response);
    exit();
}
