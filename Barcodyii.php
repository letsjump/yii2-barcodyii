<?php

/**
 * @copyright  Copyright &copy; Gianpaolo Scrigna, letsjump, 2016
 * @package    yii2-widgets
 * @subpackage yii2-widget-barcodyii
 * @version    1.0
 */

namespace letsjump\barcodyii;

use Yii;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;

class Barcodyii extends Widget
{
    /**
     * @var array $action Example: ['barcode/search']
     */
    public $action          = ['barcode/search'];
    public $visible         = true;
    public $options         = [];
    public $inputOptions    = [];
    
    protected $baseInputOptions = [
        'class'             => 'barcode_input',
        'id'                => 'barcode_input',
        'style'             => 'position:absolute; left: -9999px',
        
    ];
    
    protected $baseOptions = [
        'maxEntryTime'      =>  500,
        'minEntryChars'     =>  6,
    ];
    
    /**
     *
     */
    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        
        $this->inputOptions = ArrayHelper::merge($this->baseInputOptions, $this->inputOptions);
    }
    
    
    protected function registerInputJs()
    {
        $js = <<<js
            $('#{$this->inputOptions['id']}').codeScanner({$this->getJqueryOptions()});
js;

        $this->getView()->registerJs($js);
    }
    
    protected function getJqueryOptions() {
        $action = Yii::$app->urlManager->createUrl($this->action);
        $this->baseOptions['onScan'] = <<<js
            function (element, barcode) {
                jQuery.get(
                    "{$action}?code="+barcode,
                    function(data, status) {
                        $("#systemModalContent").html(data.html);
                        $("#systemModal .modal-header h4").html(data.title);
                        $("#systemModal").modal();
                    }
                );
            }
js;
        $this->options = ArrayHelper::merge($this->baseOptions, $this->options);
        $options = "{";
        $separator = "";
        foreach ($this->options as $key=>$option) {
            $options .= $separator . $key . ":" . $option;
            $separator = ',';
        }
        $options .= "}";
        return $options;
    }
    
    public function run()
    {
        if($this->visible != true) {
            return null;
        }
        parent::run(); // TODO: Change the autogenerated stub
        BarcodyiiAsset::register($this->getView());
        
        $html = Html::beginForm(
            $this->action,
            'get'
        );
        
        $html .= Html::textInput('code', null, $this->inputOptions);
        $html .= Html::endForm();
        
        $this->registerInputJs();
        
        return $html;

    }
    
}