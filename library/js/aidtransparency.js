(function () {

    //Alias $ safely inside the closure
    var $ = jQuery;

    //Don't declare as var AidtransparencyJS so it goes into the gobal scope
    AidtransparencyJS = {};


    AidtransparencyJS.Timeline = {

        table:undefined,
        json:{},

        init:function ($ele) {
            AidtransparencyJS.Timeline.setContainer($ele);
            AidtransparencyJS.Timeline.setJsonFromTable();
        },

        setContainer:function ($ele) {
            AidtransparencyJS.Timeline.table = $ele;
        },

        setJsonFromTable:function () {
            var json = AidtransparencyJS.Timeline.json;
            json.timeline = {};
            json.timeline.headline = "Aid Transparency";
            json.timeline.type = "default";
            json.timeline.text = "A timeline of significant events";
            json.timeline.date = [];
            AidtransparencyJS.Timeline.getDatesFromTableRows();
            AidtransparencyJS.Timeline.setStartDate();
        },

        getJsonFromTable:function () {
            AidtransparencyJS.Timeline.removeSourceTable();
            return AidtransparencyJS.Timeline.json;
        },

        /**
         *
         */
        getDatesFromTableRows:function () {
            var table = AidtransparencyJS.Timeline.table;
            var json = AidtransparencyJS.Timeline.json;
            table.find("tbody tr").each(function () {
                var row = AidtransparencyJS.Timeline.getDateFromTableRow(this);
                json.timeline.date.push(row);
            });
        },

        getDateFromTableRow:function (row) {
            var event = {};
            event.startDate = $(row).find('td.date').data('startdate');
            event.endDate = $(row).find('td.date').data('enddate');
            event.headline = $(row).find('td.event').html();
            event.text = $(row).find('td.details').html();
            var asset = {};
            asset.media = $(row).find('td.media').data('media-url');
            asset.credit = $(row).find('td.media').data('media-credit');
            asset.caption = $(row).find('td.media').data('media-caption');
            event.asset = asset;
            return event;
        },

        setStartDate:function () {
            var json = AidtransparencyJS.Timeline.json;
            json.timeline.startDate = json.timeline.date[0].startDate;
        },

        removeSourceTable:function () {
            if (AidtransparencyJS.Timeline.table != undefined) {
                AidtransparencyJS.Timeline.table.remove();
            }
        }


    },

        AidtransparencyJS.Vimeo = {

            playerUrl : "http://player.vimeo.com/video/",
            playerColor : "a2ad00",

            /**
             * Loads a Vimeo video replacing the element passed in...
             * @param element
             * @return {Boolean}
             */
            loadVimeo:function (element) {
                var vimeoVideoId = $(element).data('vimeoid');

                if(IatiJS.Utilities.isInteger(vimeoVideoId) ){
                    $.ajax({
                        type        : "POST",
                        url         : Aidtransparency.ajaxUrl,
                        data        : { action : 'get_vimeo_data', vimeoid : vimeoVideoId },
                        dataType    : 'json',
                        success     : function (data) {
                            var iframe = AidtransparencyJS.Vimeo.createVimeoIFrame(data[0]);
                            $(element).replaceWith(iframe);
                        },
                        error       : function (data) {
                            //Bad video, remove placeholder...
                            $(element).remove();
                        },
                        complete    : function(){
//                        Once succes sand error have run...
                        }
                    });
                }
                return false;
            },

            /**
             * Create a jQuery object of the Vimeo iFrame
             * @param vimeoVid
             * @return {*}
             * @private
             */
            createVimeoIFrame:function( vimeoVid ) {
                var playerUrl = AidtransparencyJS.Vimeo.playerUrl;
                var playerColor =  AidtransparencyJS.Vimeo.playerColor;
                var iframeStr = "<iframe src='" + playerUrl + vimeoVid.id + "?color=" + playerColor + "&title=0&byline=0' width='290' height='163' frameborder='0' webkitAllowFullScreen allowFullScreen></iframe>";
                var iframeDescription = "<p>" + vimeoVid.title +  "</p>";
                $iframe = iframeStr + iframeDescription;
                return $iframe;
            }

        }


})();
