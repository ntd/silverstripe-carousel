<?php

/**
 * Provide additional methods specially crafted for carousels.
 *
 * @package silverstripe-carousel
 */
class CarouselImageExtension extends DataExtension
{
    /**
     * Generate a scaled image suitable for a carousel.
     *
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

    /**
     * Low level function for CarouselImageExtension::MaybeCroppedImage().
     *
     * @param  Image_Backend $backend
     * @param  integer $width
     * @param  integer $height
     * @return Image_Backend
     */
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

    /**
     * Retrieve the fields used by SortableUploadField internal form.
     *
     * @return FieldList
     */
    public function getCarouselEditFields()
    {
        // This is *required* otherwise TinyMCE in SilverStripe 3.3 will
        // not be enabled and the <textarea> will simply disappear
        // without apparent reasons
        if (method_exists('HtmlEditorConfig', 'require_js')) {
            HtmlEditorConfig::require_js();
        }

        $fields = FieldList::create();
        $fields->push(CarouselCaptionField::create('Content', _t('CarouselPage.Caption')));
        return $fields;
    }
}

/**
 * This class is needed in order to handle the 'Content' field of the
 * 'File' table with the WYSIWYG editor. That field is TEXT, hence just
 * using HtmlEditorField will result in an error when the 'saveInto'
 * method is called.
 *
 * @package silverstripe-carousel
 */
class CarouselCaptionField extends HtmlEditorField
{
    /**
     * Override the default constructor to have saner settings.
     *
     * @param string      $name  The internal field name, passed to forms.
     * @param string|null $title The human-readable field label.
     * @param mixed       $value The value of the field.
     */
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
     *
     * @param DataObjectInterface $record
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

/**
 * Basic page type owning a carousel.
 *
 * @package silverstripe-carousel
 */
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
     *
     * @return string|false
     */
    protected function getClassFolder()
    {
        for ($class = $this->class; $class; $class = get_parent_class($class)) {
            $folder = preg_replace('/Page$/', '', $class);
            if ($folder != $class && is_dir(ASSETS_PATH . '/' . $folder)) {
                return $folder;
            }
        }

        // Why false? Because false is the proper value to set in
        // setFolderName() to get the default folder (i.e. 'Uploads').
        return false;
    }

    /**
     * Add the "Images" tab to the content form of the page.
     *
     * The images are linked to the page with a many-many relationship,
     * so if an image is shared among different carousels there is no
     * need to upload it multiple times.
     *
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $field = SortableUploadField::create('Images', _t('CarouselPage.db_Images'));
        $field->setFolderName($this->getClassFolder());
        $field->setFileEditFields('getCarouselEditFields');

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

    /**
     * Add carousel related fields to the page settings.
     *
     * Every CarouselPage instance can have its own settings, that is
     * different pages can own carousels of different sizes.
     *
     * @return FieldList
     */
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

    /**
     * Ensure ThumbnailWidth and ThumbnailHeight are valorized.
     *
     * Although width and height for the images in the carousel can be
     * omitted (see CarouselImageExtension::MaybeCroppedImage() for
     * algorithm details) the thumbnail extents must be defined.
     *
     * @return Validator
     */
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
     * Duplicate the image list whenever a new translation is created.
     * It the translatable module is not used, this will simply be a
     * dead method.
     *
     * @param boolean $save Whether the new page should be saved to the
     *                      database.
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

/**
 * Controller for CarouselPage.
 *
 * @package silverstripe-carousel
 */
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
