<?php

class Base_model
{
    /**
     * @var Db_model
     */
    public $db = null;

    /**
     * Base_model constructor.
     */
    public function __construct()
    {
        if (!$this->db) {
            $this->db = new Db_model();
        }
    }
}