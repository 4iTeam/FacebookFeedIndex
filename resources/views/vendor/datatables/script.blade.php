(function(app){
    app.module('DataTables-%1$s',function(Module,$){
    return Module.extend({
            _init:function(){
                window.LaravelDataTables=window.LaravelDataTables||{};
                var options=apply_filters('dt.options',%2$s);
                    options=apply_filters('%1$s',options);
                window.LaravelDataTables["%1$s"]=$("#%1$s").DataTable(options);
            }
            });

})
})(la);
