(function () {

    //Alias $ safely inside the closure
    var $ = jQuery;

    //Don't declare as var IatiJS so it goes into the global scope
    IatiJS = {};


    IatiJS.Utilities = {

        equaliseHeight:function ($columns) {
            var tallestcolumn = 0;
            /*columns = $(selector);*/
            $columns.each(function () {
                currentHeight = $(this).height();
                if (currentHeight > tallestcolumn) {
                    tallestcolumn = currentHeight;
                }
            });
            $columns.height(tallestcolumn);
        },

        isInteger:function (value) {
            if ((parseFloat(value) == parseInt(value)) && !isNaN(value)) {
                return true;
            }
            return false;
        }
    }


    IatiJS.FileInfo = {

        content:undefined,
        links:undefined,

        linkIcons:function ($content) {
            IatiJS.FileInfo.content = $content;
            IatiJS.FileInfo.links = $content.find('a');
            var links = IatiJS.FileInfo.links;

            links.each(function () {
                IatiJS.FileInfo.linkIcon($(this));
            });

        },

        linkIcon:function ($link) {
            var url = $link[0].href;
            var extension = url.split('.').pop();
            $link.addClass('filetype ' + extension);
            var linkText = $link.text();
            $link.before("<span class='dark'>" + linkText + "</span><br/>");
            $link.text("Download");
        }

    }


    IatiJS.UI = {

        collapseable:function ($container, collapsed) {

            var $collapseables = $container.find('div.collapseable');

            if (collapsed == true) {
                $collapseables.each(function () {
                    $(this).hide();
                });
            }

            $container.find('h2').each(function () {
                $(this).on("click", function () {
                    $(this).next('.collapseable').toggle(500);
                    $(this).find('.toggle').first().toggleClass('open closed');
                });
                var $toggle = $('<span class="toggle closed"></span>');
                $(this).prepend($toggle);
            });

        }

    }


})();
