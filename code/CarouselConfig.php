<?php

class CarouselConfig extends DataExtension {

    static $db = array(
        'CarouselDelay' => 'Int'
    );

    static $defaults = array(
        'CarouselDelay' => 5000
    );


    public function updateCMSFields(FieldList $fields) {
        $fields->addFieldToTab('Root.Main', new NumericField('CarouselDelay', _t('CarouselPage.DELAY')));
    }
}
