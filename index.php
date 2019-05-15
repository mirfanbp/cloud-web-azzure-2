<?php
    require_once 'vendor/autoload.php';
    require_once "./random_string.php";

    use MicrosoftAzure\Storage\Blob\BlobRestProxy;
    use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
    use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
    use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
    use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

    $connectionString = "DefaultEndpointsProtocol=https;AccountName=irfanwebapp;AccountKey=kx+MPPujYQURhJlTUFS4Syq+rqc2Y9BZ3EYhMJfggq/UUOGuog4BG5/x7WRw83OcI0L/3Qj5ZRjDSq1nhGeH0g==;";

    $containerName = "fandocontainer";
     
    $blobClient = BlobRestProxy::createBlobService($connectionString);
    if (isset($_POST['submit'])) {
        $fileToUpload = strtolower($_FILES["fileToUpload"]["name"]);
        $content = fopen($_FILES["fileToUpload"]["tmp_name"], "r");
        $blobClient->createBlockBlob($containerName, $fileToUpload, $content);
        header("Location: index.php");
    }

    $listBlobsOptions = new ListBlobsOptions();
    $listBlobsOptions->setPrefix("");
    $result = $blobClient->listBlobs($containerName, $listBlobsOptions);
?> 

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <title>Analisa Gambar</title>
        <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>

    <!--Navigation header Bar-->
        <div class="navbar navbar-inverse navbar-fixed-top">
            <div class="container">
                  <div class="navbar-header">
                      <a href="#" class="navbar-brand"><i>Azure Computer Vision</i></a>
                  </div>
            </div>
        </div><hr> 
        <!-- akhir navigation header bar -->

        <!-- content -->
        <div class="container" class=col-md-8>
            <h2>Analisa Gambar dengan Azure Computer Vision</h2><hr>
            <p align="left">
                Pilih gambar yang akan di Analisa.
            </p>
            <form class="" action="index.php" method="post" enctype="multipart/form-data">
              <input type="file" name="fileToUpload" accept=".jpeg,.jpg,.png" required=""><br>
              <input type="submit" name="submit" value="Upload" class="btn btn-primary btn-sm">
            </form><br>
            <h4>Total Files : <?php echo sizeof($result->getBlobs())?></h4>
            <table class='table table-hover'>
                <thead>
                    <tr>
                        <th>Nama File</th>
                        <th>URL</th>
                        <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                    do {
                        foreach ($result->getBlobs() as $blob)
                    {
                    ?>
                    <tr>
                        <td><?php echo $blob->getName() ?></td>
                        <td><?php echo $blob->getUrl() ?></td>
                        <td>
                        <form action="azurevision.php" method="post">
                            <input type="hidden" name="url" value="<?php echo $blob->getUrl()?>">
                            <input type="submit" name="submit" value="Analisa" class="btn btn-success btn-sm">
                        </form>
                        </td>
                    </tr>
                <?php
                  }
                      $listBlobsOptions->setContinuationToken($result->getContinuationToken());
                  } while($result->getContinuationToken());
                ?>
              </tbody>
            </table>
        </div>
      <!-- akhir content -->

        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script> 
        <script src="https://getbootstrap.com/docs/4.0/assets/js/vendor/popper.min.js"></script>
        <script src="https://getbootstrap.com/docs/4.0/dist/js/bootstrap.min.js"></script>
  </body>
</html>
