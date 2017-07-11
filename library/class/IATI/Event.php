<?php
/**
 * Description of Event
 *
 * @author csweet
 */
class IATI_Event extends Sweetapple_Posttype
{

    protected $_postname = "event";


    protected $_metaprefix = 'wpcf-event_';



    public function __construct(Array $values = null)
    {
        parent::__construct($values);
    }


    public function __get($name)
    {
        return ( isset($this->$name) ) ? $this->$name : "";
    }


    public function getWhere()
    {
       return $this->box1_title;
    }

    public function getWhen()
    {
        return $this->box2_title;
    }



}
