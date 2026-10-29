<?php
include('/home/mariodemiranda/www/config/crypto.php');

session_start();

$sender = parse_url($_SERVER['HTTP_REFERER'])['host'];

$postage_pickup     = 0;
$postage_goa        = 60;
$postage_india      = 120;
$postage_anywhere   = 1600;

if (($_SERVER['REQUEST_METHOD'] == 'POST') &&
    isset($_SERVER['HTTP_REFERER']) &&
    preg_match("/angelofonseca.com$/", $sender)) {

    $name       = $_POST ['firstName'] . ' ' . $_POST['lastName'];
    $email      = $_POST ['email'];
    $address    = $_POST ['address'];
    $address2   = $_POST ['address2'];
    $city       = $_POST ['city'];
    $state      = $_POST ['state'];
    $country    = $_POST ['country'];
    $zip        = $_POST ['zip'];
    $pickup     = $_POST ['pickup'];

    $copies     = (int) $_POST ['copies'];

    // Check if the number of copies is numeric/integere
    $PRICE_SPEC = array (
        '1'   => 1800,
        '3'   => 1725,
        '5'   => 1675,
        '10'  => 1600,
        '25'  => 1525,
        '50'  => 1450
    );

    $pp_copy = $PRICE_SPEC ['1'];
    $price   = $copies * $pp_copy;
    foreach ($PRICE_SPEC as $key => $value) {

        if ($copies >= (int) $key) {

            $price   = $copies * $value;
            $pp_copy = $value;
        }
    }

    if ($pickup == 1) {
	$postage = $postage_pickup;
    }
    else {
	$postage = $postage_anywhere;
	if ($country == 'India') {
            $postage = ($state == 'Goa') ? $postage_goa : $postage_india;
	}
	$postage = $copies * $postage;
    }

    $total_charge = number_format((float)($price + $postage), 2, '.', '');

    $order_id = uniqid('AFONS');

    $site = "https://mariodemiranda.com/";
    $CCAV_REQUEST = array (
        'merchant_id'       => '15070',
        'order_id'          => $order_id,
        'currency'          => 'INR',
        'amount'            => $total_charge,
        'redirect_url'      => $site . 'angelo_fonseca/afons_response_handler.php',
        'cancel_url'        => $site . 'angelo_fonseca/afons_response_handler.php',
        'language'          => 'en',
        'billing_name'      => $name,
        'billing_address'   => $address . '; ' . $address2,
        'billing_city'      => $city,
        'billing_state'     => $state,
        'billing_zip'       => $zip,
        'billing_country'   => $country,
        'billing_email'     => $email,
        'delivery_name'     => $name,
        'delivery_address'  => $address . '; ' . $address2,
        'delivery_city'     => $city,
        'delivery_state'    => $state,
        'delivery_zip'      => $zip,
        'delivery_country'  => $country,
    );

    $params         = '';
    $working_key    = 'FA758434843586A264BE2F6E9F643383'; //Shared by CCAVENUES
    $access_code    = 'QUK4LJVWZHIELYFM'; //Shared by CCAVENUES

    foreach ($CCAV_REQUEST as $key => $value)   {
        $params .= $key . '=' . $value . '&';
    }

    $encrypted_data  = encrypt ($params, $working_key);
    $transact_url    = "https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction";
}
else {
    header('Location: https://angelofonseca.com/error.html');
    exit ();
}
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
                        <h1>Thank you for the order!</h1>
                        <h2>Order Details</h2>
                        <p>
                            Thank you for placing an order for
                            <?php echo $copies . ' ' . ($copies > 1 ? 'copies' : 'copy'); ?>
                            at INR <?php echo $pp_copy; ?> per copy of
                            the book. We are certain that you will
                            enjoy the writing and the beautiful plates
                            of Angelo da Fonseca's art.
                        </p>
                        <p>
                            All prices below are in INR (Indian Rupee
                            ₹). If you choose to proceed to our secure
                            payment gateway, this price will be
                            converted to your local currency with the
                            rate as laid down by your Bank or
                            Credit/Debit Card
                        </p>
                        <hr />
                        <p>
                            Your Order Details are:
                        </p>
                        <table class="table table-sm table-borderless">
                            <tbody>
                                <tr>
                                    <th scope="row">Deliver To:</th>
                                    <td><?php echo $name; ?></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td><?php echo $address; ?></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td><?php echo $address2; ?></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td><?php echo $city . ', ' . $state . ' ' . $zip; ?></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td><?php echo $country; ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Address For Communication:</th>
                                    <td><?php echo $email; ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Price for copies (in INR):</th>
                                    <td><?php echo $price . ' ' . '₹'; ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Postage (in INR):</th>
                                    <td><?php echo $postage . ' ' . '₹'; ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Total to be Charged (in INR):</th>
                                    <td><?php echo $total_charge . ' ' . '₹'; ?></td>
                                </tr>
                        </table>
                    </div>
                </div>
            </main>
        </div>
        <form method="post" action="<?php echo $transact_url ?>" novalidate>
            <?php
                $input_list = '';
                foreach ($CCAV_REQUEST as $key => $value)   {
                    $input_list .= '<input type="hidden" name="' . $key . '" value="' . $value . '">' . "\n";
		}
                echo $input_list;
            ?>
            <input type="hidden" name="encRequest" value="<?php echo $encrypted_data; ?>">
            <input type="hidden" name="access_code" value="<?php echo $access_code; ?>">
            <button class="w-100 btn btn-primary btn-lg" type="submit">Continue and Pay</button>
        </form>
        <script src="js/bootstrap/bootstrap.min.js"></script>
    </body>
</html>
