<?php

class CarouselImage extends Image {

    static $cms_thumbnail_width = 416;
    static $cms_thumbnail_height = 160;

    static $carousel_width = 1470;
    static $carousel_height = 450;


    /**
     * Resize the image for the carousel
     *
     * Generates an image suitable to be used inside the carousel.
     * Use $Image.Carousel in templates.
     *
     * @param   GD $gd  The GD device context
     * @return  GD      The context containing the resulting image
     */
    public function generateCarousel(GD $gd) {
        $gd->setQuality(85);
        return $gd->croppedResize($this->stat('carousel_width'),
                                  $this->stat('carousel_height'));
    }
}

class CarouselSeat extends DataObject {

    static $db = array(
        'Order' => 'Int'
    );

    static $has_one = array(
        'Image' => 'Image',
        'Page'  => 'CarouselPage'
    );

    static $summary_fields = array(
        'Image.CMSThumbnail',
        'Image.Title'
    );

    static $default_sort = 'Order';


    function getCMSFields() {
        $fields = parent::getCMSFields();

        // Set the default image folder to ASSETS_DIR . '/carousel'
        $field = $fields->dataFieldByName('Image');
        $field->setFolderName('carousel');

        // Remove useless fields
        $fields->removeByName('Order');
        $fields->removeByName('PageID');

        return $fields;
    }

    public function getIndex() {
        // Order is 1-based but bootstrap carousel js is 0-based
        return $this->Order - 1;
    }
}

class CarouselPage extends Page {

    static $icon = 'carousel/img/carousel';

    static $description = 'Standard page with an image carousel bound to it';

    static $has_many = array(
        'CarouselSeats' => 'CarouselSeat'
    );

    function getCMSFields() {
        $fields = parent::getCMSFields();

        $config = GridFieldConfig_RelationEditor::create(20);
        $config->addComponent(new GridFieldSortableRows('Order'));

        $tab = new Tab(
            'CarouselSeat',
            new GridField('CarouselSeats', _t('CarouselSeat.PLURALNAME'),
                          $this->CarouselSeats(), $config)
        );
        $tab->setTitle(_t('CarouselPage.TITLE'));

        $tabset = $fields->fieldByName('Root');
        $tabset->push($tab);

        return $fields;
    }
}

class CarouselPage_Controller extends Page_Controller {

    function init() {
        parent::init();

        $delay = $this->SiteConfig()->CarouselDelay;

        // jquery and bootstrap javascript should be loaded by the
        // parent controller or by the template
        Requirements::customScript(<<<JS
$('.carousel').carousel({
    interval: $delay
});
JS
        );
    }
}

?>
