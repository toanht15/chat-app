<?php
/**
 * Angular用
 * FormHelper拡張ヘルパー
 * ngFormHelper
 */
App::uses('Hash', 'Utility');
class ngFormHelper extends AppHelper {

    public $helpers = ['Form'];

    public function input($fieldName, $options = [], $ngOptions = []) {
        $value = null;
        $this->setEntity($fieldName);
        if ( !empty($ngOptions) && !empty($ngOptions['entity']) ) {
            $value = Hash::get($this->request->data, $ngOptions['entity']);
        }
        $options['ng-model'] = $fieldName;
        $options['ng-init'] = $fieldName .'="' . $value . '";';
        return $this->Form->input($fieldName, $options);
    }
}
