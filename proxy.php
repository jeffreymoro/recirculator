<?php
if( ! isset($whitelist))
  $whitelist = [];
if( ! isset($curl_maxredirs))
  $curl_maxredirs = 10;
if( ! isset($curl_timeout))
  $curl_timeout = 30;
// Get stuff
$headers = getallheaders();
$method = __('REQUEST_METHOD', $_SERVER);
$url = __('X-Proxy-Url', $headers);
$cookie = __('X-Proxy-Cookie', $headers);
// Check that we have a URL
if( ! $url)
  http_response_code(400) and exit("X-Proxy-Url header missing");
// Check that the URL looks like an absolute URL
if( ! parse_url($url, PHP_URL_SCHEME))
  http_response_code(403) and exit("Not an absolute URL: $url");
// Check referer hostname
if( ! parse_url(__('Referer', $headers), PHP_URL_HOST) == $_SERVER['HTTP_HOST'])
  http_response_code(403) and exit("Invalid referer");
// Check whitelist, if not empty
if( ! empty($whitelist) and ! array_reduce($whitelist, 'whitelist', [$url, false]))
  http_response_code(403) and exit("Not whitelisted: $url");
// Remove ignored headers and prepare the rest for resending
$ignore = ['Cookie', 'Host', 'X-Proxy-URL'];
$headers = array_diff_key($headers, array_flip($ignore));
if($cookie)
  $headers['Cookie'] = $cookie;
foreach($headers as $key => &$value)
  $value = "$key: $value";
// Init curl
$curl = curl_init();
do
{
  // Set generic options
  curl_setopt_array($curl, [
      CURLOPT_URL => $url,
      CURLOPT_HTTPHEADER => $headers,
      CURLOPT_HEADER => TRUE,
      CURLOPT_TIMEOUT => $curl_timeout,
      CURLOPT_FOLLOWLOCATION => TRUE,
      CURLOPT_MAXREDIRS => $curl_maxredirs,
    ]);
  // Method specific options
  switch($method)
  {
    case 'HEAD':
      curl_setopt($curl, CURLOPT_NOBODY, TRUE);
      break;
    case 'GET':
      break;
    case 'PUT':
    case 'POST':
    case 'DELETE':
    default:
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
      curl_setopt($curl, CURLOPT_POSTFIELDS, file_get_contents('php://input'));
      break;
  }
  // Perform request
  ob_start();
  curl_exec($curl) or http_response_code(500) and exit(curl_error($curl));
  $out = ob_get_clean();
  // HACK: If for any reason redirection doesn't work, do it manually...
  $url = curl_getinfo($curl, CURLINFO_REDIRECT_URL);
}
while($url and --$maxredirs > 0);
// Get curl info and close handler
$info = curl_getinfo($curl);
curl_close($curl);
// Remove any existing headers
header_remove();
// Use gz, if acceptable
ob_start('ob_gzhandler');
// Output headers
$header = substr($out, 0, $info['header_size']);
array_map('header', explode("\r\n", $header));
// And finally the body
echo substr($out, $info['header_size']);
// Helper functions
function __($key, array $array, $default = null)
{
  return array_key_exists($key, $array) ? $array[$key] : $default;
}
function whitelist($carry, $item)
{
  static $url;
  if(is_array($carry))
  {
    $url = parse_url($carry[0]);
    $url['raw'] = $carry[0];
    $carry = $carry[1];
  }
  // Equals the full URL
  if(isset($item[0]))
    return $carry or $url['raw'] == $item[0];
  
  // Regex matches the full URL
  if(isset($item['regex']))
    return $carry or preg_match($item['regex'], $url['raw']);
  // Select components matches same components in the URL
  return $carry or $item == array_intersect_key($url, $item);
}