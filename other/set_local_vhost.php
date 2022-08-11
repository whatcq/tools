<?php

if ($argc != 3 || in_array($argv[1], array('--help', '-help', '-h', '-?'))) {
?>

D:\xampp\php\php.exe set_local_vhost.php dev-tff.tff.com D:\www\dev-tff
Set local vhost for development.

  Usage :
  <?php echo $argv[0]; ?> <domain> <DocumentRoot>
  eg.
  <?php echo $argv[0]; ?> test.tff.com D:\www\trunk

<?php
	exit;
}
$domain = $argv[1];
$DocumentRoot = $argv[2];


/*
echo '<pre>';
if(isset($_POST['domain'])&&isset($_POST['DocumentRoot'])){
	extract($_POST);
}else {
?>

<form method="post" action="">
	Domain:      <input type="text" name="domain" size="20" />
	DocumentRoot:<input type="text" name="DocumentRoot" size="50" />
	<input type="submit" />
</form>
<?php
	exit;
}
*/

//=====
echo "\n\nset DNS hosts:\n";
$dns_file = 'C:\Windows\System32\drivers\etc\hosts';
file_put_contents($dns_file, "\r\n127.0.0.1 $domain", FILE_APPEND) or print('Failed to add to DNS!');

//=====
echo "\n\nupdate vhosts:\n";
$vhost_file = 'D:\xampp\apache\conf\extra\httpd-vhosts.conf';
$httpd_file = 'D:\xampp\apache\conf\httpd.conf';
if(!file_exists($vhost_file)) {
	$vhost_file = $httpd_file;
}
$DocumentRoot = strtr($DocumentRoot,'\\','/');
$VirtualHost=<<<EOF

<VirtualHost *:80>
    ServerAdmin webmaster@dummy-host.example.com
    DocumentRoot $DocumentRoot
    ServerName $domain
    #ServerAlias www.$domain
    ErrorLog logs/$domain-error_log
    CustomLog logs/$domain-access_log common
#    <Directory "D:/www/dev-tff">
#        Options Indexes MultiViews
#        AllowOverride All
#        Order allow,deny
#        Allow from all
#    </Directory>
</VirtualHost>

EOF;
file_put_contents($vhost_file, $VirtualHost, FILE_APPEND) or print('Failed to add VirtualHost!');

//=====
echo "\n\nrestart apache:\n";
$apacheServiceName = 'Apache2.4';
echo `net stop $apacheServiceName`;
echo `net start $apacheServiceName`;

# 这个不行。。？
###RewriteEngine on
###RewriteMap   lowercase  int:tolower
#### define the map file
###RewriteMap   vhost      txt:D:/www/vhost.map
#### deal with aliases as above
###RewriteCond  %{REQUEST_URI}               !^/icons/
###RewriteCond  %{REQUEST_URI}               !^/cgi-bin/
###RewriteCond  ${lowercase:%{SERVER_NAME}}  ^(.+)$
#### this does the file-based remap
###RewriteCond  ${vhost:%1}                  ^(/.*)$
###RewriteRule  ^/(.*)$                      %1/docs/$1
###RewriteCond  %{REQUEST_URI}               ^/cgi-bin/
###RewriteCond  ${lowercase:%{SERVER_NAME}}  ^(.+)$
###RewriteCond  ${vhost:%1}                  ^(/.*)$
###RewriteRule  ^/(.*)$                      %1/cgi-bin/$1 [H=cgi-script]

#这个和vhost不能并存？
# RewriteEngine on
# RewriteMap    lowercase int:tolower
# RewriteCond   ${lowercase:%{HTTP_HOST}}   ^([^.]+)\.tff\.com$
# RewriteRule   ^(.*) D:/www/%1$1