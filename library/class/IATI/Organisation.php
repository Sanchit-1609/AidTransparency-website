<?php
/**
 * Description of Organisation
 *
 * @author csweet
 */
class IATI_Organisation extends Sweetapple_Posttype
{

    protected $_postname = "organisation";


    protected $_metaprefix = 'wpcf-organisation_';



    public function __construct(Array $values = null)
    {
        parent::__construct($values);
    }


    public function __get($name)
    {
        return ( isset($this->$name) ) ? $this->$name : "";
    }


}
