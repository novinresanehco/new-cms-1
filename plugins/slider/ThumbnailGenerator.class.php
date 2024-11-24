<?php

/**
 * Create thumbails from images or show scaled, already cached version.
 *
 * Use:
 * $thumb = new ThumbnailGenerator('srcfile.jpg', 100, null, '/tmp', 10);
 * $thumb->showImage();
 *
 * More info: http://netnexus.de
 *
 * @author     Johannes M. Pfeiffer <johannespfeiffer@netnexus.de>
 * @license    LGPL
 *
 */


class ThumbnailGenerator {
    public $src;
    public $width;
    public $height;
    public $cachedir;
    public $daystocache;
    public $extention;

    /**
     * ThumbnailGenerator::__construct()
     *
     * @param mixed $srcFile Path to source file
     * @param mixed $width Thumbnail file width or null
     * @param mixed $height Thumbnail file height or null
     * @param mixed $cachedir Dir for image cache
     * @param mixed $daystocache optional, days to cache by the user, defaults to 32
     * @return
     */
    public function __construct($srcFile, $width, $height, $cachedir, $extention) {
        $this->src = $srcFile;
        $this->width = $width;
        $this->height = $height;
        $this->cachedir = $cachedir;
        $this->daystocache = 32;
        $this->extention = $extention;
    }

    /**
     * Send image to browser.
     *
     * @return boolean true on success.
     */
    public function showimage() {
        $hash = md5(
            $this->src
            . '-'
            . $this->width
            . '-'
            . $this->height
        ) . '.' .$this->extention;
        $cachedfile = $this->cachedir . $hash;
        if (!file_exists($cachedfile)) {
            return  $this->create($cachedfile);
        }
		return  $cachedfile;
        ob_start('ob_gzhandler');

        // Get Image info.
        $imginfo = getimagesize($cachedfile);
        Header('Content-type: ' . $imginfo['mime']);

        $this->sendcaching();
        return readfile($cachedfile);

    }

    /**
     * Send headers for caching to the browser.
     */
    private function sendcaching() {
        $expires = 60 * 60 * 24 * $this->daystocache;
        header('Pragma: public');
        header('Cache-Control: public');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires)
            . ' GMT');
    }

    /**
     * ThumbnailGenerator::create()
     *
     * @param mixed $newfilename new filename for cached image.
     * @return
     */
    private function create($newfilename) {
        // Do not scale at all.
        if (!($this->width > 0) && !($this->height > 0)) {
            copy($this->src, $newfilename);
            return true;
        }

        // Check for GD extension.
        if (!extension_loaded('gd') && !extension_loaded('gd2')) {
            trigger_error('GD is not loaded', E_USER_WARNING);
            return false;
        }
        // Get image size.
        list($oldWidth, $oldHeight, $imageType) = getimagesize($this->src);

        switch ($imageType) {
            case 1: $im = imagecreatefromgif($this->src);
                break;
            case 2: $im = imagecreatefromjpeg($this->src);
                break;
            case 3: $im = imagecreatefrompng($this->src);
                break;
            default:  trigger_error('Unsupported filetype.', E_USER_WARNING);
                break;
        }

        // calculate aspect ratio.
        $ratio = (float) $oldHeight / $oldWidth;

        // calulate width based on height.
        $newHeight = round($this->width * $ratio);

        while($this->width > $oldWidth) {
            $this->width -= 10;
            $newHeight = round($this->width * $ratio);
        }

        $newImg = imagecreatetruecolor($this->width, $newHeight);

        // If this image is a PNG or GIF set alpha channel.
        if(($imageType == 1) OR ($imageType==3)) {
            imagealphablending($newImg, false);
            imagesavealpha($newImg, true);
            $alpha = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
            imagefilledrectangle($newImg, 0, 0, $this->width, $newHeight,
                $alpha);
        }
        imagecopyresampled($newImg, $im, 0, 0, 0, 0, $this->width, $newHeight,
            $oldWidth, $oldHeight);

        // create and save the file
        switch ($imageType) {
            case 1: imagegif($newImg, $newfilename);
                break;
            case 2: imagejpeg($newImg, $newfilename);
                break;
            case 3: imagepng($newImg, $newfilename);
                break;
            default:  trigger_error('Failed to create scaled image.',
                    E_USER_WARNING);
                break;
        }

        return $newfilename;
    }

}
?>