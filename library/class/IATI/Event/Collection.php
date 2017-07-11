<?php
/**
 * Description of IATI_Event_Collection
 *
 * @author csweet
 */
class IATI_Event_Collection extends Sweetapple_Posttype_Collection
{

    //put your code here
    public function __construct( $event = null )
    {
        if(!$event) {
            $event = new IATI_Event();
        }
        parent::__construct( $event );
    }

}
