var Indexer;
(function($,_,View){
    Indexer=View.extend({

        events:{
            'click .index-btn':'run'
        },
        initialize:function(options){
            var url=options.url||this.$el.data('url');
            this.loader = new drv.Support.PageLoader(url);
            this.debug=!!options.debug;
            this.id=this.$el.data('id');
            this.action=options.action || this.$el.data('action');
            this.$found=this.$('.items-found');
            this.$loading=this.$('.loading_icon');
            this.setup();
        },
        setup:function(){
            var count,
                self=this;
            this.loader.data({id:this.id,action:this.action}).nextKey('cursors.after').page(function (data) {
                if (data.count) {
                    count = parseInt(self.$found.html()) || 0;
                    count += parseInt(data.count) || 0;
                    self.$found.html(count);
                }
            }).done(function (response) {
                console.log(response);
                if(response && !self.debug) {
                    window.location.href = window.location.href;
                }
            });
        },
        run:function(e){
            e.preventDefault();
            $(e.currentTarget).prop('disabled', 'disabled');
            this.$loading.removeClass('hide');
            this.loader.run();
        }


    });
})($,_,Backbone.View);