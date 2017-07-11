jQuery(document).ready(function($) {

    //Placeholder support for old browsers...
    var $searchInput = $('#s');
    $searchInput.addClass('light');
    var searchPlaceHolder = $searchInput.attr('placeholder');
    $('#s').val(searchPlaceHolder)
        .focus(
        function(){
            if( this.value == searchPlaceHolder ){
                this.value='';
                $(this).removeClass('light');
            }
        }).blur(function(){
            if( this.value == '' ){
                $(this).val(searchPlaceHolder).addClass('light');
            }
        }
    );

    //Vimeo Code
    $("a.vimeo-video").each(function(){
        AidtransparencyJS.Vimeo.loadVimeo(this);
    });

    //Fix main navigation hovers
    $('ul.menu-main').on('hover', 'li', function(){
        //Get proceeding link...
        var $leftAdjacent = $(this).prev();
        if($leftAdjacent.length = 1){
            $leftAdjacent.toggleClass('left-adjacent');
        }
    });

    //Add icon css for external files...
    var $resourceWrapper = $('.resources-wrapper');
    IatiJS.FileInfo.linkIcons( $resourceWrapper );
    //Make panels collapseable
    IatiJS.UI.collapseable( $resourceWrapper, true );

    $("article.hentry div.file-list a").each(function(){
        IatiJS.FileInfo.linkIcon( $(this) );
    });


    //Setup the timelines...
    if( !$("html").hasClass('ie7') ){
        var $timelineTable = $('#timeline-table');
        if($timelineTable.length > 0) {
            AidtransparencyJS.Timeline.init( $timelineTable );
            createStoryJS({
                type:		'timeline',
                width:		'620',
                height:		'600',
                source:             AidtransparencyJS.Timeline.getJsonFromTable(),
                embed_id:           'timelinejs',
                debug:		false
            });

        }
    }

    //Equalise height of Home Page Icons and Contact Page Team People
    IatiJS.Utilities.equaliseHeight($('#home-icon-panels div.icon'));
//    IatiJS.Utilities.equaliseHeight($('div.people.team div.person'));

});
