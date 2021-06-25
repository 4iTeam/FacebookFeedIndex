(function(factory){
    factory(la);
})(function(app){
    app.module('global',function(Module){
        var confirmable={
            init:function(){
                $(document).on('click','.has-confirm',function(e){
                    var message=$(this).data('confirm');
                    if(message){
                        if(!confirm(message)){
                            e.preventDefault();
                            return false;
                        }
                    }
                    return true;
                });
            }
        };
        var simulateLink={
            init:function(){
                $(document).on('click','[data-href]',function(e){
                    var href=$(this).data('href');
                    if(href){
                        window.location.href=href;
                    }
                    return true;
                });
            }
        };

        return Module.extend({
            init:function(){
                //$('[data-toggle="tooltip"]').tooltip();
                confirmable.init();
                simulateLink.init();
            }
        });
    });
});
