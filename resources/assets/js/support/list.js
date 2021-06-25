var List;
(function($,_,View){
    List=View.extend({

        initialize:function (options) {
            _.defaults(options,{
                container:'.list',item:'list',
                idField:'id'
            });
            this.$container=this.$(options.container);
            if(_.isString(options.item)){
                options.item = drv.template(options.item);
            }
            this.$item=options.item;
            this.idField=options.idField;
            this.list={};
        },
        add:function(items,done){
            if(_.isArray(items)){
                _.each(items,function(item){
                    this._add(item);

                },this);

            }else{
                this._add(items);
            }
            if(_.isFunction(done)){
                done.call(this);
            }
        },
        _add:function(item){
            if(item[this.idField]){
                this.list[item[this.idField]]=item;
            }
            this.$container.append(this.$item(item));
        },
        find:function(id){
            return this.list[id];
        },
        clear:function(){
            this.list={};
            this.$container.html('');
        }


    });
})($,_,Backbone.View);