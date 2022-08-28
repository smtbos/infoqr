<?php
include("_config.php");
include("lib/phpqrcode/qrlib.php");


$id = "";
$information = "";

if (isset($_POST["add"])) {
    extract($_POST);
    $stmt = $pdo->prepare("INSERT INTO informations (i_information) VALUES (:information)");

    try {
        $stmt->execute([':information' => $information]);
        $id = $pdo->lastInsertId();
        $uid = md5($id) . sha1($id);
        $qr = str_pad($id, 8, '0', STR_PAD_LEFT) . ".png";
        QRcode::png($uid, $path_to_qr . $qr, 'L', 100, 2);
        $stmt = $pdo->prepare("UPDATE informations set i_qr = :qr, i_uid = :uid WHERE i_id = :id");
        try {
            $stmt->execute([':qr' => $qr, ':uid' => $uid, ':id' => $id]);
            header("Location:informations.php?s=1");
        } catch (PDOException $e) {
            header("Location:informations.php?s=00");
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    exit();
}

if (isset($_POST['update'])) {
    try {
        $id = $_GET['edit'];
        $information = $_POST['information'];
        $stmt = $pdo->prepare("UPDATE informations SET i_information = :information WHERE i_id = :id");
        if ($stmt->execute([':id' => $id, ':information' => $information])) {
            header("Location:informations.php?s=1");
        } else {
            header("Location:informations.php?s=000");
        }
    } catch (PDOException $e) {
        header("Location:informations.php?s=00");
    }
    exit();
}

if (isset($_GET["edit"])) {
    $id = $_GET["edit"];
    try {
        $stmt = $pdo->prepare("SELECT * FROM informations WHERE i_id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $id = $row['i_id'];
            $information = $row['i_information'];
        } else {
            header("location:informations.php?s=0");
        }
    } catch (PDOException $e) {
        header("location:informations.php?s=00");
    }
}



include("_header.php");
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Informations</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Info QR</a></li>
                        <li class="breadcrumb-item active">Informations</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <section class="col-12">
                    <form action="" method="post">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <?php
                                    if (isset($_GET["edit"])) {
                                    ?>
                                        <i class="fas fa-edit mr-1"></i>
                                        Update Information
                                    <?php
                                    } else {
                                    ?>
                                        <i class="fas fa-plus mr-1"></i>
                                        Add Information
                                    <?php
                                    }
                                    ?>
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <textarea name="information" id="information" rows="5" class="form-control" placeholder="Enter Yout Information Here"><?php echo $information; ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <?php
                                if (isset($_GET["edit"])) {
                                ?>
                                    <input type="submit" value="Update" name="update" class="btn btn-primary">
                                <?php
                                } else {
                                ?>
                                    <input type="submit" value="Add" name="add" class="btn btn-primary">
                                <?php
                                }
                                ?>

                            </div>
                        </div>
                    </form>
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-list mr-1"></i>
                                All Informations
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <table class="table table-striped table-bordered">
                                        <tr>
                                            <th>ID</th>
                                            <th>Information</th>
                                            <th>Views</th>
                                            <th>QR</th>
                                            <th>Action</th>
                                        </tr>
                                        <?php
                                        $stmt = $pdo->prepare("SELECT * FROM informations");
                                        try {
                                            $stmt->execute();
                                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        ?>
                                                <tr>
                                                    <td><?php echo $row['i_id']; ?></td>
                                                    <td><?php echo $row['i_information']; ?></td>
                                                    <td><?php echo $row['i_views']; ?></td>
                                                    <td><img src="<?php echo $path_to_qr . $row['i_qr']; ?>" width="150" height="150" /></td>
                                                    <td>
                                                        <a href="informations.php?edit=<?php echo $row['i_id']; ?>" class="btn btn-primary">Edit</a><br>
                                                        <a href="printqr.php?qr=<?php echo $path_to_qr . $row['i_qr']; ?>" target="_blank" class="btn btn-primary mt-2">Print QR</a><br>
                                                        <a href="views.php?information=<?php echo  $row['i_id']; ?>" class="btn btn-primary mt-2">View Logs</a>
                                                    </td>
                                                </tr>
                                        <?php
                                            }
                                        } catch (PDOException $e) {
                                            echo "An error occurred while processing data.";
                                        }
                                        ?>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>
</div>
<!-- /.content-wrapper -->
<?php include("_footer.php"); ?>