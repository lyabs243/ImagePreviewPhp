<?php
use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;
/**
 * Created by PhpStorm.
 * User: lyabs
 * Date: 10/10/2018
 * Time: 12:04
 */

class ImagePreviewPhp
{
    private $url;

    private static $imagePreview = null;
    private static $resolutionImg;
    private static $baseUrlImg;

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    function getImagePreview()
    {
        try {
            $guzzleClient = new GuzzleClient(array(
                'timeout' => 20,
            ));
            ImagePreviewPhp::$imagePreview = '';
            $client = new Client();
            $client->setClient($guzzleClient);
            $site_parameters = array('scheme' => '', 'host' => '', 'path' => '');
            $site_parameters = parse_url(strtolower($this->url));
            $absolute_url = $site_parameters['scheme'] . '://' . $site_parameters['host'];
            $url = $absolute_url;
            if(isset($site_parameters['path']))
            {
                $url .= $site_parameters['path'];
            }

            $crawler = $client->request('GET', $url);
            $crawler->filter('meta')->each(function ($node) {
                if ($node->attr('property') == 'og:image') {
                    ImagePreviewPhp::$imagePreview = $node->attr('content');
                } else if ($node->attr('property') == 'twitter:image') {
                    ImagePreviewPhp::$imagePreview = $node->attr('content');
                } else if ($node->attr('property') == 'http://ogp.me/ns#image') {
                    ImagePreviewPhp::$imagePreview = $node->attr('content');
                }
            });
            //si on ne retrouve toujours pas l url img on recupere la plus grande imgae
            if (ImagePreviewPhp::$imagePreview == null) {
                ImagePreviewPhp::$resolutionImg = 0;
                ImagePreviewPhp::$baseUrlImg = $absolute_url;
                $crawler->filter('img')->each(function ($node) {
                    try {
                        list($width, $height) = getimagesize(url_to_absolute(ImagePreviewPhp::$baseUrlImg, $node->attr('src')));
                        $resolution = $width * $height;
                        if ($resolution > ImagePreviewPhp::$resolutionImg) {
                            ImagePreviewPhp::$resolutionImg = $resolution;
                            ImagePreviewPhp::$imagePreview = $node->attr('src');
                        }
                    }catch (Exception $e){}
                });
            }
            //si on ne retrouve toujours pas l url img on recupere la premiere imgae
            if (ImagePreviewPhp::$imagePreview == null) {
                $crawler->filter('img')->each(function ($node) {
                    ImagePreviewPhp::$imagePreview = $node->attr('src');
                });
            }
            if (ImagePreviewPhp::$imagePreview != null) {
                ImagePreviewPhp::$imagePreview = url_to_absolute($absolute_url, ImagePreviewPhp::$imagePreview);
            }

        }
        catch(Exception $e){}
        return ImagePreviewPhp::$imagePreview;
    }
}