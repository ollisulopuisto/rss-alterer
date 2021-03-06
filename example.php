<?php

require("parse.php");

// examples

// parse namespaced stuff
writeRss(curlFetch("http://yle.fi/uutiset/rss/uutiset.rss"), "tempx.xml", "Kreikan", "", array("content:encoded"), array(), true, true);

// just get all and write temp.xml
writeRss(curlFetch("http://www.hs.fi/uutiset/rss/"), "temp.xml", "", "", array(), array(), false, true);

// remove Talous and Ulkomaat
writeRss(file_get_contents("temp.xml"), "temp_catsremoved.xml", "", "Talous|Ulkomaat", array(), array("category"), false, true);

// leave only stuff with am or pm pattern remove rest
writeRss(file_get_contents("temp_catsremoved.xml"), "temp_onlyos.xml", "am|pm", "", array("title", "description", "nonexistingfield"), array(), true, true);