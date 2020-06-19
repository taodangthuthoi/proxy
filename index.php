<?php
header('Access-Control-Allow-Origin: *');
error_reporting(0);
set_time_limit(0);
/*

header('access-control-allow-credentials: true');
header('access-control-allow-headers: Overwrite, Destination, Content-Type, Depth, User-Agent, X-File-Size, X-Requested-With, If-Modified-Since, X-File-Name, Cache-Control, Range');
header('access-control-allow-methods: HEAD, GET, POST');
ini_set('max_execution_time', 0);
header_remove("X-Powered-By");
*/
ob_clean();
if(ini_get('zlib.output_compression')) {
    ini_set('zlib.output_compression', 'Off'); 
}

$filename = urldecode($_GET['url']);

$useragent = $_SERVER['HTTP_USER_AGENT'];
$v = $filename;
$ch = curl_init();
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 222222);
curl_setopt($ch, CURLOPT_URL, $v);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
curl_setopt($ch, CURLOPT_NOBODY, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FILETIME, TRUE);
$info = curl_exec($ch);
$size2 = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
$filetime = curl_getinfo($ch, CURLINFO_FILETIME);
header("Content-Type: video/mp4");
//header('Content-Type: application/force-download');
$filesize = $size2;
$offset = 0;
$length = $filesize;
//var_dump($filetime); exit;
if (isset($_SERVER['HTTP_RANGE'])) {
    $partialContent = "true";
    preg_match('/bytes=(\d+)-(\d+)?/', $_SERVER['HTTP_RANGE'], $matches);
    $offset = intval($matches[1]);
    $length = $size2 - $offset - 1;
} else {
    $partialContent = "false";
}
if ($partialContent == "true") {
    header('HTTP/1.1 206 Partial Content');
    header('Accept-Ranges: bytes');
    header('Content-Range: bytes '.$offset.
        '-'.($offset + $length).
        '/'.$filesize);
} else {
    header('Accept-Ranges: bytes');
}

header("Content-length: ".$size2);
header("Etag: \"" . md5( 'videoplayback' ) . $filesize . "\"" );
header("Last-Modified: " . gmdate( "D, d M Y H:i:s", $filetime) . " GMT");
header("Content-Description: File Transfer");
header("Content-Disposition: attachment; filename=\"videoplayback.mp4\"");
header("Cache-Control: max-age=2592000, public");
header("Content-Transfer-Encoding: binary");
header('Connection: close');
$ch = curl_init();
if (isset($_SERVER['HTTP_RANGE'])) {
    // if the HTTP_RANGE header is set we're dealing with partial content
    $partialContent = true;
    // find the requested range
    // this might be too simplistic, apparently the client can request
    // multiple ranges, which can become pretty complex, so ignore it for now
    preg_match('/bytes=(\d+)-(\d+)?/', $_SERVER['HTTP_RANGE'], $matches);
    $offset = intval($matches[1]);
    $length = $filesize - $offset - 1;
    $headers = array(
        'Range: bytes='.$offset.
        '-'.($offset + $length).
        ''
    );
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
}
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 222222);
curl_setopt($ch, CURLOPT_URL, $v);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
curl_setopt($ch, CURLOPT_NOBODY, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
curl_exec($ch);
curl_close($ch);
exit;
/*
header('Content-Type: video/mp4');//header('Content-Type: video/MP2T');
header('Content-Disposition: attachment; filename="' . md5($filename) . '.mp4"');
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$filename);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 500);
curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($curl, $data) {
    echo $data;
    return strlen($data);
});
curl_exec($ch);
curl_close($ch);
*/
?>
