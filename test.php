#!/usr/bin/php
<?php
$phpVersion=phpversion();
echo("phpVersion $phpVersion");
if (!extension_loaded('java')) {
  if ((version_compare("5.0.0", phpversion(), "<=")) && (version_compare("5.0.4", phpversion(), ">"))) {
    echo "This PHP version does not support dl().\n";
    echo "Please add an extension=java.so or extension=php_java.dll entry to your php.ini file.\n";
    exit(1);
  }

  echo "Please permanently activate the extension. Loading java extension now...\n";
 
  if (!@dl('java.so')&&!@dl('php_java.dll')) {
    echo "Error: Either the java extension is not installed \n";
    echo "or it was compiled against an older or newer php version.\n";
    echo "See the HTTP (IIS or Apache) server log for details.\n";
    !dl('java.so')&&!dl('php_java.dll');
    exit(2);
  }
 }
if(@java_get_server_name() != null) {

  try {
    phpinfo();
    print "\n\n";
    
    $v = new JavaClass("java.lang.System");
    $arr=java_values($v->getProperties());
    foreach ($arr as $key => $value) {
      print $key . " -> " .  $value . "<br>\n";
    }
  } catch (JavaException $ex) {
    $trace = new Java("java.io.ByteArrayOutputStream");
    $ex->printStackTrace(new java("java.io.PrintStream", $trace));
    echo "Exception $ex occured:<br>\n" . $trace . "<br>\n";
  }
  echo "<br>\n";
  $Util = new JavaClass("php.java.bridge.Util");
  echo "JavaBridge backend version: {$Util->VERSION}<br>\n";
  echo "<br>\n";

 } else {

  phpinfo();
  print "\n\n";

  /* java_get_server_name() == null means that the backend is not
   running */

  $ext_name="java.so";
  if(PHP_SHLIB_SUFFIX != "so") $ext_name="php_java.dll";

  echo "Error: The PHP/Java Bridge backend is not running.\n";
  echo "\n";
  echo "Please start it and/or check if the directory\n";
  echo "\n\t".ini_get("extension_dir")."\n\n";
  echo "contains \"$ext_name\" and \"JavaBridge.jar\".\n";
  echo "\n";
  echo "Check if the following values are correct:\n\n";
  echo "\tjava.java_home = ".ini_get("java.java_home")."\n";
  echo "\tjava.java = ".ini_get("java.java")."\n\n";
  echo "If you want to start the backend automatically, disable:\n\n";
  echo "\tjava.socketname = ".ini_get("java.socketname")."\n";
  echo "\tjava.hosts = ".ini_get("java.hosts")."\n";
  echo "\tjava.servlet = ".ini_get("java.servlet")."\n";
  echo "\n";
  echo "If that still doesn't work, please check the \"java command\" above and\n";
  echo "report this problem to:\n\n";
  echo "\tphp-java-bridge-users@lists.sourceforge.net.\n";

 }
?>
