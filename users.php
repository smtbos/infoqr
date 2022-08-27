<?php
include("_config.php");
include("lib/phpqrcode/qrlib.php");


$id = "";
$name = "";
$username = "";
$password = "";

if (isset($_POST["add"])) {
    $name = $_POST["name"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $stmt = $pdo->prepare("INSERT INTO users (u_name, u_username, u_password) VALUES (:u_name, :u_username, :u_password)");

    try {
        $stmt->execute([':u_name' => $name, ':u_username' => $username, ':u_password' => $password]);
        header("Location:users.php?s=1");
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    exit();
}

if (isset($_POST['update'])) {
    try {
        $id = $_GET['edit'];
        $name = $_POST["name"];
        $username = $_POST["username"];
        $password = $_POST["password"];
        $stmt = $pdo->prepare("UPDATE users SET u_name = :u_name, u_username = :u_username, u_password = :u_password WHERE u_id = :id");
        if ($stmt->execute([':id' => $id, ':u_name' => $name, ':u_username' => $username, ':u_password' => $password])) {
            header("Location:users.php?s=1");
        } else {
            header("Location:users.php?s=000");
        }
    } catch (PDOException $e) {
        header("Location:users.php?s=00");
    }
    exit();
}

if (isset($_GET["edit"])) {
    $id = $_GET["edit"];
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE u_id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $name = $row['u_name'];
            $username = $row['u_username'];
            $password = $row['u_password'];
        } else {
            header("location:users.php?s=0");
        }
    } catch (PDOException $e) {
        header("location:users.php?s=00");
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
                    <h1 class="m-0">Users</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Info QR</a></li>
                        <li class="breadcrumb-item active">Users</li>
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
                                        Update User
                                    <?php
                                    } else {
                                    ?>
                                        <i class="fas fa-plus mr-1"></i>
                                        Add User
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
                                    <div class="col-4">
                                        <label>Name :</label>
                                        <input type="text" name="name" id="name" class="form-control" placeholder="Enter Name" value="<?php echo $name; ?>">
                                    </div>
                                    <div class="col-4">
                                        <label>Username :</label>
                                        <input type="text" name="username" id="username" class="form-control" placeholder="Enter Username" value="<?php echo $username; ?>">
                                    </div>
                                    <div class="col-4">
                                        <label>Password :</label>
                                        <input type="text" name="password" id="password" class="form-control" placeholder="Enter Password" value="<?php echo $password; ?>">
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
                                All Users
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
                                            <th>Name</th>
                                            <th>Username</th>
                                            <th>Password</th>
                                            <th>Action</th>
                                        </tr>
                                        <?php
                                        $stmt = $pdo->prepare("SELECT * FROM users");
                                        try {
                                            $stmt->execute();
                                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        ?>
                                                <tr>
                                                    <td><?php echo $row['u_id']; ?></td>
                                                    <td><?php echo $row['u_name']; ?></td>
                                                    <td><?php echo $row['u_username']; ?></td>
                                                    <td><?php echo $row['u_password']; ?></td>
                                                    <td>
                                                        <a href="users.php?edit=<?php echo $row['u_id']; ?>" class="btn btn-primary">Edit</a>
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