<?php
/**
 * Description of IATI_Event_Collection
 *
 * @author csweet
 */
class IATI_Person_Collection extends Sweetapple_Posttype_Collection
{

    //put your code here
    public function __construct( $person = null )
    {
        if(!$person) {
            $person = new IATI_Person();
        }
        parent::__construct( $person );
    }

}
