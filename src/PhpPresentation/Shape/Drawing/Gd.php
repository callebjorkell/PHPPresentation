<?php
/**
 * This file is part of PHPPresentation - A pure PHP library for reading and writing
 * presentations documents.
 *
 * PHPPresentation is free software distributed under the terms of the GNU Lesser
 * General Public License version 3 as published by the Free Software Foundation.
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code. For the full list of
 * contributors, visit https://github.com/PHPOffice/PHPPresentation/contributors.
 *
 * @see        https://github.com/PHPOffice/PHPPresentation
 *
 * @license     http://www.gnu.org/licenses/lgpl.txt LGPL version 3
 */

declare(strict_types=1);

namespace PhpOffice\PhpPresentation\Shape\Drawing;

class Gd extends AbstractDrawingAdapter
{
    // Rendering functions
    public const RENDERING_DEFAULT = 'imagepng';
    public const RENDERING_PNG = 'imagepng';
    public const RENDERING_GIF = 'imagegif';
    public const RENDERING_JPEG = 'imagejpeg';

    // MIME types
    public const MIMETYPE_DEFAULT = 'image/png';
    public const MIMETYPE_PNG = 'image/png';
    public const MIMETYPE_GIF = 'image/gif';
    public const MIMETYPE_JPEG = 'image/jpeg';

    /**
     * Image resource.
     *
     * @var resource
     */
    protected $imageResource;

    /**
     * Rendering function.
     *
     * @var string
     */
    protected $renderingFunction = self::RENDERING_DEFAULT;

    /**
     * Mime type.
     *
     * @var string
     */
    protected $mimeType = self::MIMETYPE_DEFAULT;

    /**
     * Unique name.
     *
     * @var string
     */
    protected $uniqueName;

    /**
     * Rendered content "cache" for the image. Reset if the
     * @var
     */
    protected $content;

    /**
     * Gd constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->uniqueName = md5(mt_rand(0, 9999) . time() . mt_rand(0, 9999));
    }

    /**
     * Get image resource.
     *
     * @return resource
     */
    public function getImageResource()
    {
        return $this->imageResource;
    }

    /**
     * Set image resource.
     *
     * @param resource $value
     *
     * @return $this
     */
    public function setImageResource($value = null)
    {
        $this->content = null;
        $this->imageResource = $value;

        if (null !== $this->imageResource) {
            // Get width/height
            $this->width = imagesx($this->imageResource);
            $this->height = imagesy($this->imageResource);
        }

        return $this;
    }

    /**
     * Get rendering function.
     *
     * @return string
     */
    public function getRenderingFunction()
    {
        return $this->renderingFunction;
    }

    /**
     * Set rendering function.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setRenderingFunction($value = self::RENDERING_DEFAULT)
    {
        if ($value === $this->renderingFunction) {
            return $this;
        }

        $this->content = null;
        $this->renderingFunction = $value;

        return $this;
    }

    /**
     * Get mime type.
     */
    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    /**
     * Set mime type.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setMimeType($value = self::MIMETYPE_DEFAULT)
    {
        if ($this->mimeType === $value) {
            return $this;
        }
        $this->content = null;
        $this->mimeType = $value;

        return $this;
    }

    public function getContents(): string
    {
        if ($this->content !== null) {
            return $this->content;
        }

        ob_start();
        if (self::MIMETYPE_DEFAULT === $this->getMimeType()) {
            imagealphablending($this->getImageResource(), false);
            imagesavealpha($this->getImageResource(), true);
        }
        call_user_func($this->getRenderingFunction(), $this->getImageResource());
        $imageContents = ob_get_contents();
        ob_end_clean();

        $this->content = $imageContents;
        return $this->content;
    }

    public function getExtension(): string
    {
        $extension = strtolower($this->getMimeType());
        $extension = explode('/', $extension);
        $extension = $extension[1];

        return $extension;
    }

    public function getIndexedFilename(): string
    {
        return $this->getHashCode() . '.' . $this->getExtension();
    }

    /**
     * @var string
     */
    protected $path;

    /**
     * Get Path.
     */
    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Overridden hashCode to provide the same hashcode if the image resource is the exact same one.
     * @return string
     */
    public function getHashCode(): string
    {
        return hash('sha256', $this->getContents());
    }
}
