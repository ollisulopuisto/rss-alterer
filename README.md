# rss-alterer
This gets a rss-feed from Internet or from local source and either picks or removes items from it

Can be called with php commandline or from a php-script

Once you're satisfied with the results, add a cron entry and pronto,
e.g.

*/20 * * * * cd /home/dst/rss; php sites.php > /dev/null
