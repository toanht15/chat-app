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
        if ( !empty($ngOptions) ) {
            // set: model && add key-value
            if ( !empty($ngOptions['entity']) ) {
                $value = Hash::get($this->request->data, $ngOptions['entity']);
                $options['ng-model'] = $fieldName;
                // set: default
                if ( empty($value) && !empty($ngOptions['default']) ) {
                    $options['ng-init'] = $fieldName .'="' . $ngOptions['default'] . '";';
                    $options['default'] = $ngOptions['default'];
                }
                else {
                    $options['ng-init'] = $fieldName .'="' . $value . '";';
                }

            }
            // set: ng-change
            if ( !empty($ngOptions['change']) ) {
                $options['ng-change'] = $ngOptions['change'];
            }
        }
        return $this->Form->input($fieldName, $options);
    }

    public function hidden($fieldName, $options = [], $ngOptions = []) {
        $value = null;
        $this->setEntity($fieldName);
        if ( !empty($ngOptions) ) {
            // set: model && add key-value
            if ( !empty($ngOptions['entity']) ) {
                $value = Hash::get($this->request->data, $ngOptions['entity']);
                $options['ng-model'] = $fieldName;
                // set: default
                if ( empty($value) && !empty($ngOptions['default']) ) {
                    $options['ng-init'] = $fieldName .'="' . $ngOptions['default'] . '";';
                    $options['default'] = $ngOptions['default'];
                }
                else {
                    $options['ng-init'] = $fieldName .'="' . $value . '";';
                }

            }
            // set: ng-change
            if ( !empty($ngOptions['change']) ) {
                $options['ng-change'] = $ngOptions['change'];
            }
        }
        return $this->Form->hidden($fieldName, $options);
    }
}
