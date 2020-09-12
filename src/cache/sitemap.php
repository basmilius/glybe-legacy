<?php
include'../includes/inc.bootstrap.php';
header("Content-Type: text/xml");

echo'<?xml version="1.0" encoding="UTF-8"?>';
echo'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
?>
<url>
	<loc>http://www.glybe.nl/</loc>
	<changefreq>always</changefreq>
	<priority>1.00</priority>
</url>
<url>
	<loc>http://www.glybe.nl/aanmelden</loc>
	<changefreq>always</changefreq>
	<priority>0.90</priority>
</url>
<url>
	<loc>http://www.glybe.nl/login</loc>
	<changefreq>always</changefreq>
	<priority>1.00</priority>
</url>
<url>
	<loc>http://www.glybe.nl/forum/index</loc>
	<changefreq>always</changefreq>
	<priority>1.00</priority>
</url>
<url>
	<loc>http://www.glybe.nl/glybe/team</loc>
	<changefreq>always</changefreq>
	<priority>1.00</priority>
</url>
<url>
	<loc>http://www.glybe.nl/glybe/over</loc>
	<changefreq>always</changefreq>
	<priority>1.00</priority>
</url>
<url>
	<loc>http://www.glybe.nl/glybe/online</loc>
	<changefreq>always</changefreq>
	<priority>1.00</priority>
</url>
<?php
$userQuery = DB::Query("SELECT * FROM users WHERE id != 3");
while($userFetch = DB::Fetch($userQuery))
{
	echo'<url>
	<loc>http://www.glybe.nl/profiel/' . strtolower($userFetch['username']) . '</loc>
	<changefreq>always</changefreq>
	<priority>' . (($userFetch['id'] == 1) ? '1.00' : '0.50') . '</priority>
</url>
';
}
$topicQuery = DB::Query("SELECT * FROM forum_topics WHERE state != 'deleted'");
while($topicFetch = DB::Fetch($topicQuery))
{
	echo'<url>
	<loc>http://www.glybe.nl' . Glybe::TopicUrl($topicFetch['id'], $topicFetch['caption'], 1) . '</loc>
	<changefreq>always</changefreq>
	<priority>1.00</priority>
</url>
';
}

echo'</urlset>';
?>