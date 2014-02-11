var getremotecourses = function(url){
    YUI(M.yui.loader).use('node', 'io-base', 'json-parse', 'jsonp', function(Y) {
        Y.one('img.roverview-loading').show(true);
        var handleSuccess = function (response) {
            var node;
            if (response) {
                node = Y.one('.block_myremotecourses h2 a');
                if (response.title) {
                    node.set('innerHTML', response.title);
                }
                if (response.url) {
                    node.set('href', response.url);
                }
            }
            if (response.html && response.html != '<!--KO-->'){
                Y.one('#rcourse-list').setContent(response.html);
                Y.all('div.rcourse-overview').hide(true);
                Y.on('click', function(e) {
                    e.preventDefault();
                    node = Y.one("#" + /^(.*)-link/.exec(this.get('id'))[1]);
                    node.toggleView().siblings('div.rcourse-overview').hide(true);
                }, 'a.roverview-link');
                Y.one('.block_myremotecourses').removeClass('remote-hidden');
            }
        };
        var handleFailure = function () {
                var node = '<div class="box center">'+M.util.get_string('errormyremotehost','block_myremotecourses')+'</div>';
                Y.one('#rcourse-list').setContent(node);
        };
        Y.jsonp(url + '?callback={callback}&t=' + new Date().getTime(), {
            on: {
                success: handleSuccess,
                failure: handleFailure
            }
        });
    });
};
