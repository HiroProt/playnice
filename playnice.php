<?php

//
// A little script to scrape your iPhone's location from MobileMe
// and update Brightkite with your iPhone's current position.
//
// Uses sosumi from http://github.com/tylerhall/sosumi/tree/master.
//
// Martin May <martin@martinmay.net>
// Nat Friedman <nat@nat.org>
//
// August 5th, 2009
//
// MIT license.
//

include 'class.brightkite.php';
include 'class.sosumi.php';

$mobileMePasswordFile = "./mobile-me-password.txt";

$bk = new Brightkite();
if (!$bk->credentials_set())
{
  die("Please set your Brightkite username and password in class.brightkite.php before using this script.\n");
}

function promptForLogin($serviceName)
{
    echo "$serviceName username: ";
    $username = trim(fgets(STDIN));

    if (empty($username)) {
	die("Error: No username specified.\n");
    }

    echo "$serviceName password: ";
    system ('stty -echo');
    $password = trim(fgets(STDIN));
    system ('stty echo');
    // add a new line since the users CR didn't echo
    echo "\n";

    if (empty ($password)) {
	die ("Error: No password specified.\n");
    }

    return array ($username, $password);
}

if (! file_exists ($mobileMePasswordFile)) {
    echo "You will need to type your MobileMe username/password. They will be\n";
    echo "saved in $mobileMePasswordFile so you don't have to type them again.\n";
    echo "If you're not cool with this, you probably want to delete that file\n";
    echo "at some point (they are stored in plaintext).\n\n";
    echo "You do need a working MobileMe account for playnice to work, and you\n";
    echo "need to have enabled the Find My iPhone feature on your phone.\n\n";
    

    list($mobileMeUsername, $mobileMePassword) = promptForLogin("MobileMe");

    $f = fopen ($mobileMePasswordFile, "w");
    fwrite ($f, "<?php\n\$mobileMeUsername=\"$mobileMeUsername\";\n\$mobileMePassword=\"$mobileMePassword\";\n?>\n");
    fclose ($f);
    chmod($mobileMePasswordFile, 0600);

    echo "\n";

} else {
    @include($mobileMePasswordFile);
}

// Get the iPhone location from MobileMe
echo "Fetching iPhone location...";
$mobileMe = new Sosumi ($mobileMeUsername, $mobileMePassword);
$iphoneLocation = $mobileMe->locate();
echo "got it.\n";

echo "iPhone location: $iphoneLocation->latitude, $iphoneLocation->longitude (accuracy: $iphoneLocation->accuracy meters)\n";

// Now update Brightkite
echo "Updating Brightkite...";
$bk->updateBrightkite($iphoneLocation->latitude, $iphoneLocation->longitude, $iphoneLocation->accuracy);

// All done.
echo "Done!\n";
