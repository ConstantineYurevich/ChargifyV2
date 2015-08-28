<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

include_once realpath(dirname(__DIR__)) . '/vendor/autoload.php';

$direct = new \ChargifyV2\DirectHelper(
    '{{your api_id}}',
    '{{your api_secret}}',
    '{{your redirect_url}}'
);
$client = new \ChargifyV2\Client(
    '{{your api_id}}',
    '{{your api_password}}'
);
$isValidResponse = $direct->isValidResponseSignature(
    $_GET['signature'],
    $_GET['api_id'],
    $_GET['timestamp'],
    $_GET['nonce'],
    $_GET['status_code'],
    $_GET['result_code'],
    $_GET['call_id']
);
if ($isValidResponse) {
    $result = $client->getCall($_GET['call_id']);
}
?>
<html>
<head></head>
<body>
    <h1>Success</h1>
    <p>
        Response is <?php if($isValidResponse): ?>Valid<?php else: ?>Not Valid<?php endif ?>

        <?php if(isset($result)): ?>
    <h2>Call Result</h2>
            <pre>
<?php echo json_encode($result, JSON_PRETTY_PRINT) ?>
            </pre>
        <?php endif; ?>
    </p>
</body>
</html>