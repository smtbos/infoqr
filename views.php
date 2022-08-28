<?php
include("_config.php");
include("_header.php");
?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">View Logs</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Info QR</a></li>
                        <li class="breadcrumb-item"><a href="informations.php">Informations</a></li>
                        <li class="breadcrumb-item active">View Logs</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <section class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-list mr-1"></i>
                                View Logs
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 pb-4">
                                    <h4 class="text-weight-bold">Information</h4>
                                    <h5>
                                        <?php
                                        $res = $pdo->query("SELECT * FROM informations WHERE i_id = " . $_GET["information"]);
                                        $row = $res->fetch(PDO::FETCH_ASSOC);
                                        echo $row["i_information"];
                                        ?>
                                    </h5>
                                </div>
                                <div class="col-12">
                                    <table class="table table-striped table-bordered">
                                        <tr>
                                            <th>User</th>
                                            <th>Latitude & Longitude</th>
                                            <th>Date & Time</th>
                                        </tr>
                                        <?php
                                        $i_id = $_GET["information"];
                                        $stmt = $pdo->prepare("SELECT * FROM views, users WHERE v_information = $i_id AND v_user = u_id ORDER BY v_id DESC");
                                        try {
                                            $stmt->execute();
                                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        ?>
                                                <tr>
                                                    <td><?php echo $row['u_name']; ?></td>
                                                    <td><?php echo $row['v_latitude'] . " " . $row['v_longitude']; ?></td>
                                                    <td>
                                                        <?php
                                                        echo date('d M Y', strtotime($row['v_timestamp'])) . "<br>";
                                                        echo date('H:i:s', strtotime($row['v_timestamp']));
                                                        ?>
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
                                <div class="col-12 text-center py-5">
                                    <a href="informations.php" class="btn btn-primary btn-lg">Back</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>
</div>
<?php
include("_footer.php");
?>