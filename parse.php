<?php

/**
* writes rss to local file with some parsing
* @param String $url - what xml to parse
* @param String $result_file - where to write result
* @param String $keep_pattern - what pattern to keep "keeme|keepalsome"
* @param String $remove_pattern - what pattern to remove "removeme|removealsome"
* @param array $keep_fields - what fields to match the keep_pattern - array("title", "decription")
* @param array $remove_fields - what fields to match the remove_pattern - array("title", "decription")
* @param boolean $remove_default - if remove is the default action
*/
function writeRss($xml, $result_file, $keep_pattern, $remove_pattern, $keep_fields = array("title"), $remove_fields = array("title"), $remove_default = true, $debug = false) {

    
  // load file
  $xml = simplexml_load_string($xml);
  if($debug) { 
      $debugfeed = $xml->channel->title;
      print "<h1>FEED:".$debugfeed."</h1><br />\n"; 
  }
       
  for($i = 0; $i < count($xml->channel->item); $i++){
    // pick item
    $item = $xml->channel->item[$i];
    
    // match pattern to these
    $keep_match = "";
    $remove_match = "";
    
    //get namespace so we can parse for example dc:creator tags
    $namespaces = $item->getNameSpaces(true);
    
    foreach($keep_fields AS $field) {
      $field_explode = explode(":", $field);
      if(count($field_explode) > 1){
        $ns = $item->children($namespaces[$field_explode[0]]);
        $keep_match .= " ".$ns->$field_explode[1];
      } else if(isset($item->$field)){
        $keep_match .= " ".$item->$field;
      }
    }

    foreach($remove_fields AS $field) {
      $field_explode = explode(":", $field);
      if(count($field_explode) > 1){
        $ns = $item->children($namespaces[$field_explode[0]]);
        $remove_match .= " ".$ns->$field_explode[1];
      } else if(isset($item->$field)){
        $remove_match .= " ".$item->$field;
      }
    }
    // keep these 
    if(!empty($keep_pattern) && !empty($keep_match) && preg_match('/('.$keep_pattern.')/i', $keep_match) > 0) {
      if($debug) { print "<strong>KEEP:".$keep_match."</strong><br />\n"; }
    } else if(!empty($remove_pattern) && !empty($remove_match) && preg_match('/('.$remove_pattern.')/i', $remove_match) > 0){
    // remove these
      if($debug) { print "REMOVE:".$remove_match."<br />\n"; }
      unset($xml->channel->item[$i]);
      $i--; // move the counter as the items are decreased
    } else if($remove_default){ // default action
      if($debug) { print "DEFAULT REMOVE:".$item->title."<br />\n"; }
      unset($xml->channel->item[$i]);
      $i--;
    } else { // default keep
      if($debug) { print "DEFAULT KEEP:".$item->title."<br />\n"; }
    }
  }

  file_put_contents($result_file, $xml->asXML());
}

/**
* helper to fetch remote file
* @param String $url - url to fetch
*/
function curlFetch($url) {
  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  // 5 second timeout
  curl_setopt($curl, CURLOPT_TIMEOUT, 5);
  curl_setopt($curl, CURLOPT_CONNECTTIMEOUT ,5);
  // on SSL connection do not verify
  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
  $data = curl_exec($curl);
  // close
  curl_close($curl);
  return $data;
}

				    