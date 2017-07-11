<?php
/**
 * Custom post type for Timeline
 *
 * @author csweet
 */
class IATI_Timeline extends Sweetapple_Posttype
{

    protected $_postname = "timeline";


    protected $_metaprefix = 'wpcf-timeline_';


    public function __construct(Array $values = null)
    {
        parent::__construct($values);
    }


    public function __get($name)
    {
        return ( isset($this->$name) ) ? $this->$name : "";
    }


    public function getStartDate($format = 'Y,n,j')
    {
        $datetime = $this->timestampToDateTime($this->start);
        return $datetime->format($format);
    }

    public function getEndDate($format = 'Y,n,j')
    {
        $datetime = $this->timestampToDateTime($this->end);
        return $datetime->format($format);
    }


    public function getDocument(){
        return $this->document;
    }


}
