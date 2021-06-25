(function(app) {
    app.module('users', function (Module, $) {
        var DataTableButtons;

        var mod = {
            register: function () {
                add_filter('users-list-table', function (options) {
                    options.columns[0].render = function (data, type, row, meta) {
                        var cell = mod.api.cell({row: meta.row, column: meta.col});
                        if (data) {
                            $(cell.node()).addClass('select-checkbox');
                        }
                    };
                    return options;
                })
            },
            init: function () {
                this.$table = $('#users-list-table');
                this.api = this.$table.DataTable();
            },
            boot: function () {

            }
        };

        return mod;
    });
})(la);