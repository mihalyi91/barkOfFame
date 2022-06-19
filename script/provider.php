<?php
function starts_with($needle, $haystack): bool
{
    return substr($haystack, 0, strlen($needle)) === $needle;
}

try {
   $db = new PDO("sqlite:db.db");
   $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


	$stmt = $db->query("SELECT * FROM transactions ORDER BY id DESC limit 1");
	$last = $stmt->fetch();

	$result = json_decode(file_get_contents('https://algoindexer.algoexplorerapi.io/v2/accounts/4QSPOUGEJ73ZZQ65NFMBSH2W4WTDGJYLQDVMFG55YQBI2HYRHZLOFCZKEA/transactions?min-round=' . ($last['id'] +1)));
	foreach ($result->transactions as $transaction)
	{
		if($transaction->{'asset-transfer-transaction'}->{'asset-id'} == 523683256 && starts_with("bark of fame", strtolower(base64_decode($transaction->note))))
		{
			$date = date('Y-m-d H:i:s', $transaction->{'round-time'});
			$sql = "INSERT INTO transactions VALUES (?,?,?,?,?)";
			$db->prepare($sql)->execute([$transaction->{'confirmed-round'}, $date, base64_decode($transaction->note), $transaction->{'asset-transfer-transaction'}->amount / 1000000, $transaction->sender]);
		}
	}


$statement = $db->prepare("SELECT * FROM transactions ORDER BY " . (isset($_GET['amount']) ? "amount" : "date") . " DESC limit 10");
$statement->execute();
$results = $statement->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($results);


}   catch (Exception $e) {
    echo "Unable to connect";
    echo $e->getMessage();
    exit;
}

?>