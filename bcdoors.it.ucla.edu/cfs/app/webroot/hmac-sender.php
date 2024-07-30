<?php

// Data to be submitted
$data = array('Photo-uid' => $_GET['uid']);

//echo "http://ee.healthyhomecompany.com/hmac-receiver.php?data=" . urlencode($json_data); exit(1);
// Finally submit to api end point
//process("https://local.healthyhomecompany.com/hmac-receiver.php", array('data' => $data, 'sig' => $sig, 'pubKey' => $public_key));

//process("https://bcdoors-dev.it.ucla.edu/cfs/hmac-receiver.php", $data);
//process("https://{$_SERVER['HTTP_HOST']}/cfs/photos/download", $data);
$url = "https://bc-as-d02.dev.it.ucla.edu/cfs/photos/download";
process($url, $data);

function process($url, $data)
{
     // User Public/Private Keys
    $private_key = File::read('/var/www/bcdoors.it.ucla.edu/bcdkey.pem');
    $public_key = File::read('/var/www/bcdoors.it.ucla.edu/bcdkey.pub');

    // Generate content verification signature
    $sig = base64_encode(hash_hmac('sha1', http_build_query($data), $private_key, TRUE));

    // Prepare json data to be submitted
    //$json_data = json_encode(array('data' => $data, 'sig' => $sig, 'pubKey' => $public_key));

    $opts = array(
                    CURLOPT_CONNECTTIMEOUT => 30,
                    CURLOPT_TIMEOUT        => 60,
                    CURLOPT_FRESH_CONNECT  => 1,
			        CURLOPT_PORT => 443,
                    CURLOPT_USERAGENT      => 'curl-php',
                    CURLOPT_FOLLOWLOCATION => false,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CUSTOMREQUEST  => 'POST');

    $opts[CURLOPT_SSL_VERIFYHOST] = false;
    $opts[CURLOPT_SSL_VERIFYPEER] = false;
    //$opts[CURLOPT_SSLVERSION] = 0;

    //$opts[CURLOPT_SSL_CIPHER_LIST] = 'SSLv3';
    //$opts[CURLOPT_SSL_CIPHER_LIST] = 'TLSv1';

    $credentials['username'] = "cfss3rv1c3";
    $credentials['password'] = "b*r13E#sk3";

    $opts[CURLOPT_USERPWD] = "{$credentials['username']}:{$credentials['password']}";

    #$opts[CURLOPT_SSLVERSION] = 0;

    //$opts[CURLOPT_POSTFIELDS] = array('data' => $data);
    $opts[CURLOPT_POSTFIELDS] = $data;

    $opts[CURLOPT_URL] = $url;

    //$opts[CURLOPT_HTTPHEADER] = array('sig: ' . $sig, 'pubKey: ' . urlencode($public_key));
    $opts[CURLOPT_HTTPHEADER] = array('sig: ' . $sig);

    $ch = curl_init();
    curl_setopt_array($ch, $opts);

    $response = curl_exec($ch);
    $headers = curl_getinfo($ch);

    $errorNo = curl_errno($ch);
    $error = curl_error($ch);

#    echo $response . "\n\n";
#    echo "error: " . $error . "\n\n";

    if (empty($error))
    {
        //$response = json_decode($response);
    }


    header('Content-Type: image/jpeg');
    echo $response;
    return $response;
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