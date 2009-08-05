<?php

// A class to login to update Brightkite location,
// based on code to update Google Latitude.
//
// Martin May <martin@martinmay.net>
// Nat Friedman <nat@nat.org>
// Jack Catchpoole <jack@catchpoole.com>
//
// MIT license.
//

class Brightkite
{
    private $bk_username = null; // Your Brightkite username
    private $bk_password = null; // Your Brightkite password

    // update URL
    private $updateUrl = "https://brightkite.com/callbacks/playnice?";

    public function __construct()
    {
    }
    
    public function credentials_set()
    {
      return !($this->bk_username == null or $this->bk_password == null);
    }

    public function updateBrightkite($lat, $lng, $accuracy)
    {

      $ig = curl_init();
      
      $url = $this->updateUrl . "lat=" . urlencode($lat) . "&lng=" . urlencode($lng) . "&acc=" . urlencode($accuracy);

      curl_setopt($ig, CURLOPT_URL, $url);
      curl_setopt($ig, CURLOPT_RETURNTRANSFER, TRUE);      // Don't output results of transfer, instead send as return val
      
      curl_setopt($ig, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);  // Basic Auth
      curl_setopt($ig, CURLOPT_USERPWD, $this->bk_username . ":" . $this->bk_password);  // Basic Auth

      //curl_setopt($ig, CURLOPT_HEADER, TRUE);              // Include headers in output, for debugging
      //curl_setopt($ig, CURLOPT_VERBOSE, TRUE);             // Verbose output for debugging

      $junk = curl_exec ($ig);
    }

}
