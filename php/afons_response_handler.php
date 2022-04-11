<?php
include('/home/mariodemiranda/www/config/crypto.php');
error_reporting(0);

function handleResponse ($post) {

    $workingKey    = 'FA758434843586A264BE2F6E9F643383';
    $paramString   = decrypt ($post, $workingKey);
    $params        = array ();

    parse_str ($paramString, $params);

    $order_id     = $params ['order_id'];
    $order_status = $params ['order_status'];
    $email        = $params ['billing_email'];

    $response = array ();
    if ($order_status === "Success") {

	// Add order details to the database
	$response ['header'] = 'Thank you for the purchase!';
	$response ['body'] =<<<SUCCESS
	<p>
	    Your transaction for the purchase of "Fonseca"
	    has been successful. We will start preparing the
	    shipment shortly and inform you at {$email}
	</p>
	SUCCESS;
    }
    else if ($order_status == 'Aborted') {

	$response ['header'] = 'Our apologies. Your transaction was aborted';
	$response ['body'] =<<<ABORTED
	<p>
	    Your transaction for the purchase of "Fonseca"
	    was aborted. This happens often due to network
	    or connectivity issues. We hope that you will
	    attempt the transaction at a more convenient
	    time.
	</p>
	<p>
	    We look forward to seeing you soon.
	</p>
	ABORTED;
    }
    else {

	$response ['header'] = 'Our apologies. Your transaction failed';
	$response ['body'] =<<<FAILED
	<p>
	    Your transaction for the purchase of "Fonseca"
	    failed.
	</p>
	<p>
	    We look forward to seeing you soon.
	</p>
	FAILED;
    }

    return $response;
}

$message = handleResponse ($_POST ['encResp']);
?>
<!doctype html>
<html lang="en">
    <head>
        <title>Angelo da Fonseca</title >
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="css/bootstrap/bootstrap.min.css" rel="stylesheet">
        <link href="css/form-validation.css" rel="stylesheet">
        <style>
         .bd-placeholder-img {
             font-size: 1.125rem;
             text-anchor: middle;
             -webkit-user-select: none;
             -moz-user-select: none;
             user-select: none;
         }

         @media (min-width: 768px) {
             .bd-placeholder-img-lg {
                 font-size: 3.5rem;
             }
         }
        </style>
    </head>
    <body>
        <nav class="navbar navbar-expand-md navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.html">Angelo da Fonseca</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu" aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarMenu">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item mx-1">
                            <a class="nav-link" aria-current="page" href="index.html">Home</a>
                        </li>
                        <li class="nav-item mx-1">
                            <a class="nav-link" href="foreword.html">Foreword</a>
                        </li>
                        <li class="nav-item mx-1">
                            <a class="nav-link" href="intro.html">Read</a>
                        </li>
                        <li class="nav-item mx-1">
                            <a class="nav-link" href="about.html">About Us</a>
                        </li>
                        <li class="nav-item mx-1">
                            <a class="nav-link active" href="preorder.html">Pre-order Now</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="container">
            <main>
                <div class="row g-5">
                    <div class="col-md-7 col-lg-8">
                        <h1><?php echo $message ['header'] ?></h1>
                        <?php echo $message ['body'] ?>
		    </div>
		</div>
	    </main>
	</div>
    </body>
</html>
