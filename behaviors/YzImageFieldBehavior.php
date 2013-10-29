<?php

class YzImageFieldBehavior extends CActiveRecordBehavior
{
    /**
     * @var bool This is default property for deleting image.
     * If owner class does not have property defined in {@see $deleteImageAttribute}
     * then this property will be used
     */
    public $deleteImage = false;
    /**
     * @var string Set this property to the path of image that should be copied
     */
    public $copyImage = null;
    /**
     * @var bool Set this property to true to upload image
     */
    public $uploadImage = false;
    /**
     * @var string Image attribute of the owner
     */
    public $attribute = 'image';
    /**
     * @var string Name of the attribute that is used to delete image
     */
    public $deleteImageAttribute = 'deleteImage';
    /**
     * @var string Template for file name. Default is '{id}'
     */
    public $fileNameTemplate = '{id}';
    /**
     * @var string Path to images
     */
    public $imagesPath = 'webroot.images';
    /**
     * @var string Url to images
     */
    public $imagesUrl = 'images';
    /**
     * @var string Name of the image component of application
     */
    public $imageComponent = 'image';
    /**
     * If is true, behavior will try to check image file existence
     * and will create one if needed
     * @var bool
     */
    public $checkForFiles = true;
    /**
     * @var bool Whether to create file on model insert/update. If false
     * only original file will be created
     */
    public $createFilesOnUpdate = false;
    /**
     * Mode for newly created directories
     * @var int
     */
    public $newDirectoryMode = 0755;
    /**
     * Formats are having following form:
     * <code>
     * array(
     *  'original' => array( // This is the main format of the image
     *      'path' => '.',
     *      'type' => 'jpg',
     *  ),
     *  'formatName' => array(
     *      'path' => 'pathToImages',
     *      'type' => 'jpg',
     *      'size' => array(width, height), // optional
     *      'transform' => function($image) { ... }, // transformation rules, optional
     *  ),
     * )
     * </code>
     * @var array
     */
    public $formats = array(
        'original' => array(
            'path' => '.',
            'type' => 'jpg',
        )
    );

    /**
     * Name of original image format. Defaults to 'original'.
     * @var string
     */
    public $originalFormatName = 'original';

    public function getImagePath($formatName = null)
    {
        if($this->owner->{$this->attribute} == '')
            return null;

        if($formatName === null)
            $format = reset($this->formats);
        else
            $format = isset($this->formats[$formatName])?$this->formats[$formatName]:null;

        if(empty($format))
            return null;

        $path = (empty($format['path']) || $format['path'] == '.')? '' :
            trim($format['path'],'/') . '/';

        $imagePath = Yii::getPathOfAlias($this->imagesPath) . '/' .
            $path . $this->owner->{$this->attribute} . '.' . $format['type'];

        if($this->checkForFiles && $formatName != $this->originalFormatName) {
            if(!file_exists($imagePath)) {
                if($this->processImageFormat($format,
                    $this->getImagePath($this->originalFormatName),
                    $this->owner->{$this->attribute}
                ) == false)
                    return null;
            }
        }

        return $imagePath;
    }

    public function getImageUrl($formatName = null)
    {
        if($this->owner->{$this->attribute} == '')
            return null;

        if($formatName === null)
            $format = reset($this->formats);
        else
            $format = isset($this->formats[$formatName])?$this->formats[$formatName]:null;

        if(empty($format))
            return null;

        $path = (empty($format['path']) || $format['path'] == '.')? '' :
            trim($format['path'],'/') . '/';

        if($this->checkForFiles && $formatName != $this->originalFormatName)
            if($this->getImagePath($formatName) == false)
                return null;

        $url = rtrim($this->imagesUrl,'/') . '/' .
            $path . $this->owner->{$this->attribute} . '.' . $format['type'];

        return $url;
    }

    public function afterSave($event)
    {
        if ($this->copyImage !== null) {
            $this->deleteImage();
            $newImageName = strtr($this->fileNameTemplate,array(
                '{id}' => $this->owner->getPrimaryKey(),
            ));
            $this->processImage($this->copyImage, $newImageName);
            $this->owner->updateByPk($this->owner->getPrimaryKey(),
                array($this->attribute => $this->owner->{$this->attribute}));
            $this->copyImage = null;
        } elseif($this->getIsDeleteImage() == true) {
            $this->deleteImage();
            $this->owner->updateByPk($this->owner->getPrimaryKey(),
                array($this->attribute => $this->owner->{$this->attribute}));
            $this->deleteImage = false;
        } elseif($this->uploadImage) {
            if ($image = CUploadedFile::getInstance($this->owner, $this->attribute)) {
                $newImageName = strtr($this->fileNameTemplate,array(
                    '{id}' => $this->owner->getPrimaryKey(),
                ));
                $this->deleteImage();
                $this->processImage($image->tempName, $newImageName);
                $this->owner->updateByPk($this->owner->getPrimaryKey(),
                    array($this->attribute => $this->owner->{$this->attribute}));
            }
        }
    }

    public function beforeDelete($event)
    {
        $this->deleteImage();
    }

    protected function processImage($imagePath, $newImageName)
    {
        try {
            if($this->createFilesOnUpdate) {
                foreach($this->formats as $formatName => $format) {
                    $this->processImageFormat($format, $imagePath, $newImageName);
                }
            } else {
                $this->processImageFormat($this->formats[$this->originalFormatName], $imagePath, $newImageName);
            }
        } catch(CException $e) {
            $this->owner->{$this->attribute} = $newImageName;
            $this->deleteImage();
            return;
        }
        $this->owner->{$this->attribute} = $newImageName;
    }

    /**
     * @param array $format
     * @param string $imagePath
     * @param string $newImageName
     * @return bool
     */
    protected function processImageFormat($format, $imagePath, $newImageName)
    {
        $basePath = Yii::getPathOfAlias($this->imagesPath);

        if(!file_exists($imagePath))
            return false;

        /** @var $image Image */
        $image = Yii::app()->{$this->imageComponent}->load($imagePath);

        $path = (empty($format['path']) || $format['path'] == '.')? '' :
            trim($format['path'],'/');

        if(!is_dir($basePath . '/' . $path))
            mkdir($basePath . '/' . $path, $this->newDirectoryMode, true);

        $path .= '/';

        $newImagePath = $basePath . '/' . $path . $newImageName . '.' . $format['type'];

        if(isset($format['size']))
            $image->resize($format['size'][0],$format['size'][1]);
        if(isset($format['transform']) && is_callable($format['transform']))
            $image = call_user_func($format['transform'], $image);
        $image->save($newImagePath);

        return true;
    }

    protected function deleteImage()
    {
        if($this->owner->{$this->attribute}) {
            $basePath = Yii::getPathOfAlias($this->imagesPath);
            foreach($this->formats as $formatName => $format) {

                $path = (empty($format['path']) || $format['path'] == '.')? '' :
                    trim($format['path'],'/') . '/';

                $imagePath = $basePath . '/' . $path
                    . $this->owner->{$this->attribute} . '.' . $format['type'];
                if(file_exists($imagePath))
                    unlink($imagePath);
            }
            $this->owner->{$this->attribute} = '';
        }
    }

    /**
     * @return bool
     */
    public function getIsDeleteImage()
    {
        return (property_exists($this->owner, $this->deleteImageAttribute)
            || isset($this->owner->{$this->deleteImageAttribute}))
            && ($this->owner->{$this->deleteImageAttribute} != false);
    }
}