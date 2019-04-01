<?php

class Base_controller
{
    public $models = [];

    public function __construct()
    {
        $config = require_once APP_PATH . 'config/config.php';
        $this->models = array_map('ucfirst', array_map('strtolower', $config['autoload_models']));

        $this->_initModels();
    }

    /**
     * Require models from autoload
     */
    private function _initModels()
    {
        if ( !empty($this->models) ) {
            foreach ($this->models as $model) {
                $model_file = APP_PATH . 'models/' . ucfirst(strtolower($model)) . '.php';
                if (file_exists($model_file)) {
                    require_once $model_file;
                }
            }
        }
    }

    /**
     * Require model file by name
     * @param bool $model_name
     */
    public function loadModel( $model_name = false )
    {
        if ( $model_name && !in_array($model_name, $this->models) ) {
            $model_file = APP_PATH . 'models/' . ucfirst(strtolower($model_name)) . '.php';
            if (file_exists($model_file)) {
                require_once $model_file;
            }
        }
    }

}