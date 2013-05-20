<?php

class Seat extends DataObject {

    static $has_one = array(
        'Image' => 'Image',
        'Page'  => 'CarouselPage'
    );

    static $db = array(
        'Details' => 'HTMLText'
    );

    static $summary_fields = array(
        'Image',
        'Details'
    );

    function getCMSFields() {
        $fields = parent::getCMSFields();

        // Remove useless fields
        $fields->removeByName('PageID');

        return $fields;
    }
}

class CarouselPage extends Page {

    static $icon = 'carousel/img/carousel';

    static $description = 'Carousel page';

    static $has_many = array(
        'Seats' => 'Seat'
    );

    function getCMSFields() {
        $fields = parent::getCMSFields();

        $config = GridFieldConfig_RecordEditor::create();
        $tab = new Tab(
            'Seat',
            new GridField('Seats', 'Lista immagini', $this->Seats(), $config)
        );
        $tab->setTitle('Carosello');

        $tabset = $fields->fieldByName('Root');
        $tabset->push($tab);

        return $fields;
    }
}

class CarouselPage_Controller extends Page_Controller {
}

?>
