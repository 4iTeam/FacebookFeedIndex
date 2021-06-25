(function(factory){
    factory(la);
})(function(app){
    app.view('role',function(View,$){
        var CapsView=app.View.extend({
            el:'#capabilities',
            hide:function(){
                this.$el.hide();
            },
            show:function(){
                this.$el.show();
            }
        });
        var CapView=app.View.extend({
            tagName:'div',
            className:'col-lg-4',
            template:app.template('capability'),
            events:{
                "click a":"remove"
            }

        });
        function slugify(text)
        {
            return text.toString().toLowerCase()
                .replace(/\s+/g, '-')           // Replace spaces with -
                .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
                .replace(/\-\-+/g, '-')         // Replace multiple - with single -
                .replace(/^-+/, '')             // Trim - from start of text
                .replace(/-+$/, '').replace(/\-/g,'_');            // Trim - from end of text
        }
        var RoleView=app.View.extend({
            events:{
                "click #add-permission button":"addPermission",
                "click #all_caps":"allPermissionSwitch"
            },
            initialize:function(params){
                RoleView.__super__.initialize.call(this, params);
                this.$cap=$('#cap-input');
                this.capabilities=new CapsView();
                var self=this;
                this.allPermissionSwitch({currentTarget:'#all_caps'});
                _.each(caps,function(value,cap){
                    value && this.capabilities.views.add(new CapView({cap:cap}));
                },this);
                this.$cap.on('keydown',function(e){
                    if (e.keyCode === 13) {
                        self.addPermission(e);
                        return false;
                    }
                });
            },
            addPermission:function(e){
                e.preventDefault();
                var self=this;
                var cap=this.$cap.val();
                cap=slugify(cap);
                if(cap) {
                    if(!caps[cap]) {
                        this.capabilities.views.add(new CapView({cap: cap}));
                        this.$cap.val('');
                    }
                    caps[cap]=true;
                }
            },
            allPermissionSwitch:function(e){
                var self=this;
                if($(e.currentTarget).is(':checked')){
                    self.capabilities.hide();
                }else{
                    self.capabilities.show();
                }
            }

        });
        return new RoleView({el:'#role-edit'});
    });
});
