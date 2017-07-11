<?php
/**
 * Custom post type for Implementation Events
 *
 * @author csweet
 */
class IATI_Implementation extends Sweetapple_Posttype
{

    protected $_postname = "iati_event";


    protected $_metaprefix = 'wpcf-implementation_event_';


    public function __construct(Array $values = null)
    {
        parent::__construct($values);
    }


    public function __get($name)
    {
        return ( isset($this->$name) ) ? $this->$name : "";
    }


    public function getDocument(){
        return $this->document;
    }


}
