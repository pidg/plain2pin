<?php

/*
	plain2pin

	This takes a plain text list of links (newest first) and converts to an HTML bookmarks
	file	that Pinboard can import.

	Save your list of links in input.txt, cross your fingers and run the script.

*/


$newest_first = 1;		// Set this if your links are in descending date order



// Try to force some unicode
ini_set("default_charset", 'utf-8');
header('Content-type: text/html; charset=utf-8');

?><html>
<head>
<title>Plain text to Pinboard import conversion</title>
</head>
<body>
<textarea rows="40" cols="100"><?php

// Header
echo <<<EOF
<!DOCTYPE NETSCAPE-Bookmark-file-1>
<!-- This is an automatically generated file.
     It will be read and overwritten.
     DO NOT EDIT! -->
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
<TITLE>Bookmarks</TITLE>
<H1>Bookmarks</H1>
<DL><p>
EOF;


$doc = new DOMDocument();

function getTitle($url)
{
	// Gets title from HTML doc

	global $doc;

	$html = file_get_contents($url);

	@$doc->loadHTML($html);
	$nodes = $doc->getElementsByTagName('title');
	$title = $nodes->item(0)->nodeValue;

	return $title;
}

$sites = file_get_contents("input.txt");		// Load list
$all = explode("\n", $sites);			// Turn it into an array

if ( $newest_first ) $all = array_reverse($all);	// Reverse array if it's in descending date order

$now = time();			// Get timestamp
$then = $now - count($all) - 1;	// Set earliest bookmark timestamp to (time now - number of links - 1) secs

echo "    <DT><H3 ADD_DATE=\"$now\" LAST_MODIFIED=\"$now\" PERSONAL_TOOLBAR_FOLDER=\"true\">Bookmarks</H3>";
echo "    <DL><p>\n";

foreach($all as $url)
{

	$then++;	// Increment timestamp

	if (trim($url))
	{
		$title = trim(getTitle($url));	// Grab and tidy title
		$url = trim($url);			// Tidy URL

		$title = str_replace( array("\n", "\r", "\t"), "", $title );	// Get rid of horrible chars

	       $links[] = "\t\t<DT><A HREF=\"$url\" ADD_DATE=\"$then\">$title</A>\n";	// Add link code to array
		$title = "";
	}

}

if ( $newest_first ) $links = array_reverse($links);	// Reverse array again if it was in descending date order

foreach ( $links as $link ) echo $link;		// Print links


?>
    </DL><p>
</DL><p></textarea>
