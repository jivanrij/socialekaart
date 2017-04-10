<?php
namespace Models;

class Factory{
    public static $instance = null;
    public $models = array();

    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new Factory();
        }
        return self::$instance;
    }

    // Get's the requested model
    public function getModel($id, $type, $machineName = '') {
        if(!isset($this->models[$id.$type])) {
            switch($type) {
                case 'Locationset':
                    $this->models[$id.$type] = new Locationset($id);
                    break;
                case 'Location':
                    $this->models[$id.$type] = new Location($id);
                    break;
                case 'User':
                    $this->models[$id.$type] = new User($id);
                    break;
                case 'Vocabulary':
                    $this->models[$id.$type] = new Vocabulary($id, $machineName);
                    break;
            }
        }
        return $this->models[$id.$type];
    }
}
