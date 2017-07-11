(function(){

    //Alias $ safely inside the closure
    var $ = jQuery;

    //Don't declare as var Icuk so it goes into the gobal scope
    AidtransparencyJS = {};

    AidtransparencyJS.Timeline = {
        table : undefined,
        json : {},

        init : function($ele)
        {
            AidtransparencyJS.Timeline.setContainer($ele);
            AidtransparencyJS.Timeline.setJsonFromTable();
        },

        setContainer : function($ele) {
            AidtransparencyJS.Timeline.table = $ele.find('table.timeline-table').first();
        },

        setJsonFromTable : function(){
            var json = AidtransparencyJS.Timeline.json;
            json.timeline = {};
            json.timeline.headline = "Example of the Timeline...";
            json.timeline.type = "default";
            json.timeline.text = "People say stuff";
            json.timeline.date = [];
            AidtransparencyJS.Timeline.getDatesFromTableRows();
            AidtransparencyJS.Timeline.setStartDate();
        },

        getJsonFromTable : function(){
            AidtransparencyJS.Timeline.removeSourceTable();
            return AidtransparencyJS.Timeline.json;
        },

        /**
         *
         */
        getDatesFromTableRows : function() {
            var table = AidtransparencyJS.Timeline.table;
            var json = AidtransparencyJS.Timeline.json;
            $(table).find("tbody tr").each(function(){
                var row = AidtransparencyJS.Timeline.getDateFromTableRow(this);
                json.timeline.date.push(row);
            });
        },

        getDateFromTableRow : function(row){
            var date = {};
            date.startDate = $(row).find('td.date').data('startdate');
            date.endDate = $(row).find('td.date').data('enddate');
            date.headline = $(row).find('td.country').html() + ": " + $(row).find('td.event').html();
            date.text = $(row).find('td.details').html();
            return date;
        },

        setStartDate : function()
        {
            var json = AidtransparencyJS.Timeline.json;
            json.timeline.startDate = json.timeline.date[0].startDate;
        },

        removeSourceTable : function() {
            if(AidtransparencyJS.Timeline.table != undefined){
                AidtransparencyJS.Timeline.table.remove();
            }
        }


    },

    AidtransparencyJS.Vimeo = {

        loadVimeo : function($videoId)
        {
            var VimeoBaseurl = "http://vimeo.com/api/v2/video/";

        }

    }

})();