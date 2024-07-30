<?php
// Data submitted
$data = $_POST;
echo "<pre>";
print_r($data);
print_r($_SERVER);
echo "</pre>";

// User hit the end point API with $data, $signature and $public_key
$message = http_build_query($data);
$receivedSignature = $_SERVER['HTTP_SIG'];
$privateKey = get_private_key_for_public_key($_SERVER['HTTP_SIG']);
//$publicKey = get_private_key_for_public_key($_SERVER['HTTP_PUBKEY']);
$computed_signature = base64_encode(hash_hmac('sha1', $message, $privateKey, TRUE));

if($computed_signature == $receivedSignature)
{
	echo "Content Signature Verified";
}
else
{
	echo "Invalid Content Verification Signature";
}

function get_private_key_for_public_key($public_key)
{
	$privateKey = File::read('/var/www/bcdkey.pem');

	return $privateKey;
}
class File
{
	static public function read($fileName)
	{
	   $contents = "";

	   try
	   {
		if (file_exists($fileName) && !is_dir($fileName))
		{
			$handle = fopen($fileName, "r");
			$contents = fread($handle, filesize($fileName));
			fclose($handle);
		}
	   }
	   catch (Exception $Error)
	   {
		   echo ("<!-- error: " . $Error . " -->");
	   }

	   return($contents);

	}
}
?>