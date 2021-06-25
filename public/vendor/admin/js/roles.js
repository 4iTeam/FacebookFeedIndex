(function(app){
    app.module('roles',function(Module,$){
        var mod={
            register:function () {
                add_filter('roles-list-table',function(options){
                    options.columns[0].render=function(data, type, row, meta){
                        var cell=mod.api.cell({row:meta.row,column:meta.col});
                        if(data) {
                            $(cell.node()).addClass('select-checkbox');
                        }
                    };
                    return options;
                })
            },
            init:function(){
                this.$table=$('#roles-list-table');
                this.api=this.$table.DataTable();
            },
            boot:function(){

            }
        };

        return mod;
    });
})(la);