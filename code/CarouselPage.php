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

    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $field = new SortableUploadField('Images', _t('CarouselPage.db_Images'));
        $fields->findOrMakeTab('Root.Images')
            ->setTitle(_t('CarouselPage.db_Images'))
            ->push($field);

        return $fields;
    }

    public function getSettingsFields() {
        $fields = parent::getSettingsFields();

        $fields->addFieldToTab('Root.Settings',
            FieldGroup::create(
                CheckboxField::create('Captions', _t('CarouselPage.db_Captions')),
                TextField::create('Width', _t('CarouselPage.db_Width')),
                TextField::create('Height', _t('CarouselPage.db_Height'))
            )->setTitle(_t('CarouselPage.SINGULARNAME'))
        );

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
