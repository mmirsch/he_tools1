<?php
class cURL { 
  /*
  * @author Keith Kurson (delusions@gmail.com)
  * @date September 09, 2006
  * @version 1.0
  */
     /*
     * Headers
     */
     var $headers;
     /*
     * User Agent
     */
     var $user_agent;
     /* 
     * Compression 
     */
     var $compression;
     /*
     * Cookie File
     */
     var $cookie_file;
     /* 
     * Proxy Server
     * ip:port
     */
     var $proxy;
     /* 
     * Initiate the class
     */
    function cURL($cookies=TRUE,$cookie,$compression='gzip',$proxy='') {
           $this->headers[] = "Accept: image/gif, image/x-bitmap, image/jpeg, image/pjpeg";
           $this->headers[] = "Connection: Keep-Alive";
           $this->headers[] = "Content-type: application/x-www-form-urlencoded";
           $this->user_agent = "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.0.3705; .NET CLR 1.1.4322; Media Center PC 4.0)";
           $this->compression=$compression;
           $this->proxy=$proxy;
           $this->cookies=$cookies;
           if ($this->cookies == TRUE) $this->cookie($cookie); 
     }
     /* 
     * Tests the Cookie File
     */ 
     function cookie($cookie_file) {
          if (file_exists($cookie_file)) {
                $this->cookie_file=$cookie_file;
          } else { 
                @fopen($cookie_file,'w') or $this->error("The cookie file could not be opened. Make sure this directory has the correct permissions");
                $this->cookie_file=$cookie_file;
                fclose($cookie_file);
          }
     }
     /*
     * Runs a GET through cURL
     */
     function get($url,$refer='') {


     			$process = curl_init($url);
          curl_setopt($process, CURLOPT_HEADER, 1);
     			curl_setopt($process, CURLOPT_REFERER, $refer);
          curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);
          curl_setopt($process, CURLOPT_USERAGENT, $this->user_agent);
          if ($this->cookies == TRUE) curl_setopt($process, CURLOPT_COOKIEFILE, $this->cookie_file);
          if ($this->cookies == TRUE) curl_setopt($process, CURLOPT_COOKIEJAR, $this->cookie_file);
          curl_setopt($process,CURLOPT_ENCODING , $this->compression);
          curl_setopt($process, CURLOPT_TIMEOUT, 30);
          if ($this->proxy) curl_setopt($cUrl, CURLOPT_PROXY, 'proxy_ip:proxy_port');
          curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
// Eingef�gt wegen Httpauthentification
					curl_setopt($process, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
					// This is occassionally required to stop CURL from verifying the peer's certificate.
					// CURLOPT_SSL_VERIFYHOST may also need to be TRUE or FALSE if
					// CURLOPT_SSL_VERIFYPEER is disabled (it defaults to 2 - check the existence of a
					// common name and also verify that it matches the hostname provided)
					curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
					
					// Optional: Return the result instead of printing it
// Eingef�gt wegen Httpauthentification ENDE
					
          $return = curl_exec($process);
          curl_close($process);
          return $return;
     }
     /* 
     * Runs a POST through cURL
     */
     function post($url,$data,$refer) {
          $process = curl_init($url);
          curl_setopt($process, CURLOPT_REFERER, $refer);
          curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);
          curl_setopt($process, CURLOPT_USERAGENT, $this->user_agent);
          if ($this->cookies == TRUE) curl_setopt($process, CURLOPT_COOKIEFILE, $this->cookie_file);
          if ($this->cookies == TRUE) curl_setopt($process, CURLOPT_COOKIEJAR, $this->cookie_file);
          curl_setopt($process, CURLOPT_ENCODING , $this->compression);
          curl_setopt($process, CURLOPT_TIMEOUT, 30);
          if ($this->proxy) curl_setopt($cUrl, CURLOPT_PROXY, 'proxy_ip:proxy_port');
          curl_setopt($process, CURLOPT_POSTFIELDS, $data);
          curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
          curl_setopt($process, CURLOPT_POST, 1);
          $return = curl_exec($process);
          curl_close($process);
          return $return;
     }
     /*
     * Error Output
     */
     function error($error) {
          echo "<center><div style='width:500px;border: 3px solid #FFEEFF; padding: 3px; background-color: #FFDDFF;font-family: verdana; font-size: 10px'><b>cURL Error</b><br>$error</div></center>";
          die;
     }
}
?>
