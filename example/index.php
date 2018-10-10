<?php
/**
 * Created by PhpStorm.
 * User: lyabs
 * Date: 10/10/2018
 * Time: 12:27
 */
require_once "../lib/Goutte-master/vendor/autoload.php";
include_once '../lib/class/url_to_absolute.php';
include_once  '../lib/ImagePreviewPhp.php';

$imagePreview = new ImagePreviewPhp();
$imagePreview->setUrl('https://www.huffingtonpost.fr/2018/10/01/charles-aznavour-les-images-de-lhommage-au-pied-de-la-tour-eiffel_a_23547640/');
?>
<img src="<?php echo $imagePreview->getImagePreview(); ?>" />
