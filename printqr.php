<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print QR</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css" />
    <style>
        @page {
            size: A4;
        }
    </style>
</head>

<body>
    <div class="container">

        <div class="row">
            <?php
            for ($i = 1; $i <= 6; $i++) {
            ?>
                <div class="col-6 px-4 py-3">
                    <img src="<?php echo $_GET["qr"]; ?>" class="img img-fluid">
                </div>
            <?php } ?>
        </div>
    </div>
    <script type="text/javascript">
        window.print();
    </script>
</body>

</html>