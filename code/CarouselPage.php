<?php

class CarouselSlot extends DataObject {
private static $db = array( 'Order' => 'Int'); private static $has_one = array( 'Image' => 'Image', 'Page'  => 'CarouselPage'
    );
    private static $summary_fields = array(
        'Image.CMSThumbnail',
        'Image.Title'
    );
    private static $default_sort = 'Order';


    public function getCMSFields() {
        $fields = parent::getCMSFields();

        // Set the default image folder to ASSETS_DIR . '/Carousel'
        $field = $fields->dataFieldByName('Image');
        $field->setFolderName('Carousel');

        // Remove useless fields
        $fields->removeByName('Order');
        $fields->removeByName('PageID');

        return $fields;
    }

    public function fieldLabels($includerelations = true) {
        $labels = parent::fieldLabels($includerelations);

        $labels['Image.CMSThumbnail'] = _t('CarouselSlot.IMAGE');
        $labels['Image.Title'] = _t('CarouselSlot.CAPTION');

        return $labels;
    }
}

class CarouselPage extends Page {

    private static $icon = 'carousel/img/carousel.png';
    private static $db = array(
        'Captions' => 'Boolean',
        'Width'    => 'Int',
        'Height'   => 'Int'
    );
    private static $has_many = array(
        'Slots'    => 'CarouselSlot'
    );
    private static $defaults = array(
        'Width'    => 800,
        'Height'   => 200,
        'Captions' => false
    );

    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $config = GridFieldConfig_RelationEditor::create(20);
        $config->addComponent(new GridFieldSortableRows('Order'));

        $tab = Tab::create('Carousel',
            FieldGroup::create(
                TextField::create('Width', _t('CarouselPage.db_Width')),
                TextField::create('Height', _t('CarouselPage.db_Height'))
            )->setTitle(_t('CarouselSlot.SIZE')),

            CheckboxField::create('Captions', _t('CarouselPage.db_Captions')),
            GridField::create('Slots', _t('CarouselSlot.PLURALNAME'), $this->Slots(), $config)
        )->setTitle(_t('CarouselPage.TITLE'));

        $tabset = $fields->fieldByName('Root');
        $tabset->push($tab);

        return $fields;
    }

    public function getCMSValidator() {
        return new RequiredFields(array('Width', 'Height'));
    }
}

class CarouselPage_Controller extends Page_Controller {
}

?>
