<?php
$filename = "";

function setUrlCookie($url, $postdata)
{
    $cookie_jar = './cookie.txt';
    $resource = curl_init();
    curl_setopt($resource, CURLOPT_URL, $url);
    curl_setopt($resource, CURLOPT_POST, 1);
    curl_setopt($resource, CURLOPT_POSTFIELDS, $postdata);
    curl_setopt($resource, CURLOPT_COOKIEJAR, $cookie_jar);
    curl_setopt($resource, CURLOPT_RETURNTRANSFER, 1);
    curl_exec($resource);

    return $resource;
}

function get($url)
{
	$res = curl_init();
	curl_setopt($res, CURLOPT_URL, $url);
    curl_setopt($res, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($res, CURLOPT_COOKIEFILE, "./cookie.txt");
	curl_setopt($res, CURLOPT_HEADER, 0);
    $content = curl_exec($res);
	curl_close($res);
    return $content;

}

function filter($str)
{
	$code = "";
	$flag = 0;
	$filt = "0123456789abcdefghijklmnopqrstuvwxyz";
	for($i = 0; $i < strlen($str); $i++)
	{
		//<td width="38%">
		if($flag == 0){
			if(substr($str, $i, 16) == "<td width=\"38%\">")
			{
				$flag = 1;
				$i += 15;
			}
		}
		else if($flag == 1){
			if($str[$i] != '<')
				$filename = $filename.$str[$i];
			else
				$flag = 2;
		}
		else if($flag == 2){
			//<textarea
			if(substr($str, $i, 9) == "<textarea")
				$flag = 3;
		}
		else if($flag == 3){
			if($str[$i] == '>')
				$flag = 4;
		}
		else if($flag == 4){
			if(substr($str, $i, 11) != "</textarea>") {
				$code = $code.$str[$i];
			}
			else {
				$filename2 = "";
				for($j = 0; $j < strlen($filename); $j++)
				{
					$chk = 0;
					for($k = 0; $k < strlen($filt); $k++)
						if($filename[$j] == $filt[$k])
						{
							$chk = 1;
							break;
						}
					if($chk == 1)$filename2 = $filename2.$filename[$j];
				}
				$file = fopen($filename2.".cpp",'w') or die("G_G");
				fwrite($file, $code);
				fclose($file);
				$code = $filename = "";
				$flag = 0;
			}
		}
	}
}


$url = 'http://zerojudge.tw/Login';
// login account and password
$postdata = 'account=w181496&passwd=******';
$resource = setUrlCookie($url, $postdata); 

// $i is the number of page
for($i = 25; $i >= 1; $i--) {
	$url = "http://zerojudge.tw/Submissions?&account=w181496&status=AC&page=".$i;
	echo $url."<br>";
	filter(get( $url));
}
?>
