<?php

class CarouselImageExtension extends DataExtension {

    /**
     * If $width and $height are greater than 0, it is equivalent to
     * CroppedImage().
     *
     * If only $width is greater than 0, it is equivalent to SetWidth().
     *
     * If only $height is greater than 0, it is equivalent to
     * SetHeight().
     *
     * If neither $width or $height are greater than 0, return the
     * original image.
     *
     * @param  Image_Backend $backend
     * @param  integer $width   The width to set or 0.
     * @param  integer $height  The height to set or 0.
     * @return Image_Backend
     */
    public function MaybeCroppedImage($width, $height) {
        return $this->owner->getFormattedImage('MaybeCroppedImage', $width, $height);
    }

    public function generateMaybeCroppedImage(Image_Backend $backend, $width, $height) {
        if ($width > 0 && $height > 0) {
            return $backend->croppedResize($width, $height);
        } elseif ($width > 0) {
            return $backend->resizeByWidth($width);
        } elseif ($height > 0) {
            return $backend->resizeByHeight($height);
        } else {
            return $backend;
        }
    }
}

class CarouselPage extends Page {

    private static $icon = 'carousel/img/carousel.png';
    private static $db = array(
        'Captions' => 'Boolean default(true)',
        'Width'    => 'Int default(0)',
        'Height'   => 'Int default(200)',
    );
    private static $many_many = array(
        'Images' => 'Image',
    );
    private static $many_many_extraFields = array(
        'Images' => array(
            'SortOrder' => 'Int',
        ),
    );
    private static $defaults = array(
        'Captions' => true,
        'Width'    => 0,
        'Height'   => 200,
    );

    /**
     * Search the first class name (that must have a 'Page' suffix) in
     * the object hierarchy that has a correspoding folder in
     * ASSETS_PATH, that is a folder with the same name with the 'Page'
     * suffix stripped out. This folder will be returned and used as
     * custom folder in the upload field.
     *
     * For example, if this class is `HomePage` and it is inherited from
     * `CarouselPage`, this function will check for `Home` first and
     * `Carousel` after.
     *
     * If no valid folders are found, `false` is returned.
     */
    protected function getClassFolder() {
        for ($class = $this->class; $class; $class = get_parent_class($class)) {
            $folder = preg_replace('/Page$/', '', $class);
            if ($folder != $class && is_dir(ASSETS_PATH . '/' . $folder))
                return $folder;
        }

        // false is the proper value to set in setFolderName()
        // to get the default folder (usually 'Uploads').
        return false;
    }

    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $field = new SortableUploadField('Images', _t('CarouselPage.db_Images'));
        $field->setFolderName($this->getClassFolder());

        $fields->findOrMakeTab('Root.Images')
            ->setTitle(_t('CarouselPage.db_Images'))
            ->push($field);

        return $fields;
    }

    public function getSettingsFields() {
        $fields = parent::getSettingsFields();

        $field = FieldGroup::create(
            CheckboxField::create('Captions', _t('CarouselPage.db_Captions')),
            TextField::create('Width', _t('CarouselPage.db_Width')),
            TextField::create('Height', _t('CarouselPage.db_Height'))
        );
        $field->setName('Carousel');
        $field->setTitle(_t('CarouselPage.SINGULARNAME'));

        $fields->addFieldToTab('Root.Settings', $field);

        return $fields;
    }
}

class CarouselPage_Controller extends Page_Controller {

    /**
     * From the controller the images are returned in proper order.
     * This means `<% loop $Images %>` returns the expected result.
     */
    public function Images() {
        return $this->dataRecord->Images()->Sort('SortOrder');
    }
}
