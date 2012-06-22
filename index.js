var getremotecourses = function(myurl){
    YUI(M.yui.loader).use('node', 'jsonp', 'jsonp-url', function(Y) {
        Y.one('img.roverview-loading').show(true);
        var handleSuccess = function (response) {
            if (response){
                Y.one('#rcourse-list').setContent(response);
                Y.all('div.rcourse-overview').hide(true);
                Y.on('click', function(e) {
                    e.preventDefault();
                    var node = Y.one("#" + /^(.*)-link/.exec(this.get('id'))[1]);
                    node.toggleView().siblings('div.rcourse-overview').hide(true);
                }, 'a.roverview-link');
            }else{
                var node = '<div class="box center">'+M.util.get_string('nocourses','block_myremotecourses')+'</div>';
                Y.one('#rcourse-list').setContent(node);
            }
        };
        var handleFailure = function () {
                console.log('timeout');
                var node = '<div class="box center">'+M.util.get_string('errormyremotehost','block_myremotecourses')+'</div>';
                Y.one('#rcourse-list').setContent(node);
        };
        Y.jsonp(myurl+'?callback={callback}', {
            on: {
                success: handleSuccess,
                failure: handleFailure
            }
        });
    });
}