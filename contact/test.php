<?
//imageDirectory - Where is your original images stored?
//imageName - What's the filename of the file to make a thumbnail of?
//thumbDirectory - Where to store the newly created thumbnail?
//thumbWidth - What width do you want for the thumbnail?

function createThumbnail($imageDirectory, $imageName, $thumbDirectory, $thumbWidth)
{
$srcImg = imagecreatefromjpeg("$imageDirectory/$imageName");
$origWidth = imagesx($srcImg);
$origHeight = imagesy($srcImg);

$ratio = $origWidth / $thumbWidth;
$thumbHeight = $origHeight * $ratio;

$thumbImg = imagecreate($thumbWidth, $thumbHeight);
imagecopyresized($thumbImg, $srcImg, 0, 0, 0, 0, $thumbWidth, $thumbHeight, imagesx($thumbImg), imagesy($thumbImg));

imagejpeg($thumbImg, "$thumbDirectory/$imageName");
}

createThumbnail("mugshots", "Blue.jpg", "mugshots/save", 140); 

?>