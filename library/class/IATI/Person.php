<?php
/**
 * Description of Person
 *
 * @author csweet
 */
class IATI_Person extends Sweetapple_Posttype
{

    protected $_postname = "person";


    protected $_metaprefix = 'wpcf-person_';



    public function __construct(Array $values = null)
    {
        parent::__construct($values);
    }


    public function __get($name)
    {
        return ( isset($this->$name) ) ? $this->$name : "";
    }


    public function getName()
    {
        return $this->post_title;
    }


}
