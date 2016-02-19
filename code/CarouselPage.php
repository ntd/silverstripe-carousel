<?php

class CarouselImageExtension extends DataExtension
{
    /**
     * If $width and $height are greater than 0, it is equivalent to
     * Image_Backend::croppedResize().
     *
     * If only $width is greater than 0, it is equivalent to SetWidth().
     *
     * If only $height is greater than 0, it is equivalent to
     * SetHeight().
     *
     * If neither $width or $height are greater than 0, return the
     * original image.
     *
     * @param  integer $width   The width to set or 0.
     * @param  integer $height  The height to set or 0.
     * @return Image_Backend
     */
    public function MaybeCroppedImage($width, $height)
    {
        return $this->owner->getFormattedImage('MaybeCroppedImage', $width, $height);
    }

    public function generateMaybeCroppedImage(Image_Backend $backend, $width, $height)
    {
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

/**
 * This class is needed in order to handle the 'Content' field of the
 * 'File' table with the WYSIWYG editor. That field is TEXT, hence just
 * using HtmlEditorField will result in an error when the 'saveInto'
 * method is called.
 */
class CarouselCaptionField extends HtmlEditorField
{
    public function __construct($name, $title = null, $value = '')
    {
        parent::__construct($name, $title, $value);
        $this->rows = 5;
        // The .htmleditor class enables TinyMCE
        $this->addExtraClass('htmleditor');
    }

    /**
     * Implementation directly borrowed from HtmlEditorField
     * without the blocking or useless code.
     */
    public function saveInto(DataObjectInterface $record)
    {
        $htmlValue = Injector::inst()->create('HTMLValue', $this->value);

        // Sanitise if requested
        if ($this->config()->sanitise_server_side) {
            $santiser = Injector::inst()->create('HtmlEditorSanitiser', HtmlEditorConfig::get_active());
            $santiser->sanitise($htmlValue);
        }

        $this->extend('processHTML', $htmlValue);
        $record->{$this->name} = $htmlValue->getContent();
    }
}

class CarouselPage extends Page
{
    private static $icon = 'carousel/img/carousel.png';

    private static $db = array(
        'Captions' => 'Boolean',
        'Width'    => 'Int',
        'Height'   => 'Int',
    );

    private static $many_many = array(
        'Images'   => 'Image',
    );

    private static $many_many_extraFields = array(
        'Images'   => array(
            'SortOrder' => 'Int',
        ),
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
    protected function getClassFolder()
    {
        for ($class = $this->class; $class; $class = get_parent_class($class)) {
            $folder = preg_replace('/Page$/', '', $class);
            if ($folder != $class && is_dir(ASSETS_PATH . '/' . $folder)) {
                return $folder;
            }
        }

        // false is the proper value to set in setFolderName()
        // to get the default folder (usually 'Uploads').
        return false;
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $field = SortableUploadField::create('Images', _t('CarouselPage.db_Images'));
        $field->setFolderName($this->getClassFolder());

        // Enable HTML caption handling if captions are enabled
        if ($this->Captions) {
            $caption = CarouselCaptionField::create('Content', _t('CarouselPage.Caption'));
            $field->setFileEditFields(FieldList::create($caption));
            unset($caption);
        }

        $root = $fields->fieldByName('Root');
        $tab = $root->fieldByName('Images');
        if (! $tab) {
            $tab = Tab::create('Images');
            $tab->setTitle(_t('CarouselPage.db_Images'));
            $root->insertAfter($tab, 'Main');
        }
        $tab->push($field);

        return $fields;
    }

    public function getSettingsFields()
    {
        $fields = parent::getSettingsFields();

        $settings = FieldGroup::create(
            FieldGroup::create(
                NumericField::create('Width', _t('CarouselPage.db_Width')),
                NumericField::create('Height', _t('CarouselPage.db_Height')),
                CheckboxField::create('Captions', _t('CarouselPage.db_Captions'))
            )
        );
        $settings->setName('Carousel');
        $settings->setTitle(_t('CarouselPage.SINGULARNAME'));
        $fields->addFieldToTab('Root.Settings', $settings);

        return $fields;
    }

    public function getCMSValidator()
    {
        return RequiredFields::create(
            'ThumbnailWidth',
            'ThumbnailHeight'
        );
    }

    /**
     * Out of the box support for silverstripe/silverstripe-translatable.
     *
     * It the translatable module is not used, this will simply be a
     * dead method.
    */
    public function onTranslatableCreate($save)
    {
        // Chain up the parent method, if it exists
        if (method_exists('Page', 'onTranslatableCreate')) {
            parent::onTranslatableCreate($save);
        }

        $master = $this->getTranslation(Translatable::default_locale());

        foreach ($master->Images() as $master_image) {
            $image = $master_image->duplicate($save);
            $this->Images()->add($image);
        }
    }
}

class CarouselPage_Controller extends Page_Controller
{
    /**
     * From the controller the images are returned in proper order.
     * This means `<% loop $Images %>` returns the expected result.
     */
    public function Images()
    {
        return $this->dataRecord->Images()->Sort('SortOrder');
    }
}
