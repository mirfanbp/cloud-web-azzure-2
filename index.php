<?php
require_once 'vendor/autoload.php';
require_once "./random_string.php";

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

$connectionString = "DefaultEndpointsProtocol=https;AccountName=fandowebapp2;AccountKey=PeNnkVFw2vvf0K4zFxDVZWsejpZojVvCnFLnrCn6ntOy4QCkMGzZ9xpMIm255eajAkIiAxy10cu3//p34nxhag==;";
// "DefaultEndpointsProtocol=https;AccountName=gnmsubmit2;AccountKey=nETKn9LreUmUCkpxCnG6US1QVkVFNDbszSlpzxyIEyqOTw32rsyuhXzoq35sbz5C/91Cg2B+TTEgzMwaDeHsrw==;";
$containerName = "gnmcontainer";
// Create blob client.
$blobClient = BlobRestProxy::createBlobService($connectionString);
if (isset($_POST['submit'])) {
  $fileToUpload = strtolower($_FILES["fileToUpload"]["name"]);
  $content = fopen($_FILES["fileToUpload"]["tmp_name"], "r");
  // echo fread($content, filesize($fileToUpload));
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
    <meta name="description" content="">
    <meta name="author" content="">
  
    <title>Analisa Gambar</title>

    <!-- Bootstrap -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Plugin CSS -->
    <link href="vendor/magnific-popup/magnific-popup.css" rel="stylesheet" type="text/css">
  </head>

  <body id="page-top">

    <!--Navigation Bar-->
    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle"data-toggle="collapse" data-target=".navbar-collapse">
            <span class="icon-bar"> </span>
            <span class="icon-bar"> </span>
            <span class="icon-bar"> </span>
          </button>
          <a href="#" class="navbar-brand"><i>Analisa Gambar</i></a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav right">
            <li><a href="#">Home</a></li>
          </ul>
        </div>
      </div>
    </div>
    <hr><hr> <!-- akhir navigation bar -->

    <!-- content -->
    <section>
      <div class="container">
        <h2 class="text-center text-uppercase text-secondary mb-0">Analisa</h2><hr>
        <main role="main" class="container">
          <div class="starter-template"> 
            <p align="Leftr" >
              1. Klik Choose File dan pilih gambar yang ingin Anda Analisa.<br>
              2. Kemudian Klik <b>Upload</b><br>
              3. Untuk menganalisa foto pilih <b>Analisa</b> pada tabel.</p>
            <span class="border-top my-3"></span>
          </div>
      <div class="mt-4 mb-2">
        <form class="d-flex justify-content-center" action="index.php" method="post" enctype="multipart/form-data">
          <input type="file" name="fileToUpload" accept=".jpeg,.jpg,.png" required="">
            <input type="submit" name="submit" value="Upload">
        </form>
      </div>
      <br>
      <br>
      <h4>Total Files : <?php echo sizeof($result->getBlobs())?></h4>
      <table class='table table-hover'>
        <thead>
          <tr>
            <th>File Name</th>
            <th>File URL</th>
            <th>Action</th>
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
                  <form action="computervision.php" method="post">
                    <input type="hidden" name="url" value="<?php echo $blob->getUrl()?>">
                    <input type="submit" name="submit" value="Analisa" class="btn btn-primary">
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

  <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery-slim.min.js"><\/script>')</script>
        <script src="https://getbootstrap.com/docs/4.0/assets/js/vendor/popper.min.js"></script>
        <script src="https://getbootstrap.com/docs/4.0/dist/js/bootstrap.min.js"></script>
      </div>
    </section>
  </body>
</html>
