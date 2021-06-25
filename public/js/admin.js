(function(app){
    app.module('popup',function(Module){
        function popup(url, title, w, h) {
            var left = (screen.width / 2) - (w / 2);
            var top = 0;
            return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
        }
        return Module.extend({
            open:function(url,width,height){
                if(!height) {
                    height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;
                    if (height < 200) {
                        height = screen.height - 100;
                    }
                }
                if(!width){
                    width=769;
                }
                return popup(url, "_blank", width, height);
            },
            boot:function(){
                var self=this;
                this.$('document').on('click','[data-popup]',function(e){
                    var clicked=$(e.currentTarget);
                    if(clicked.attr('href')){
                        return self.open(url);
                    }else{
                        if(clicked.data('popup')){
                            return self.open(clicked.data('popup'))
                        }
                    }
                });
            }

        });
    });
    app.module('timeago',function(Module,$){
        function setup_time_ago(){
            new timeago().render($('time.timeago'));
        }
        return Module.extend({
            register:function(){
                add_filter('dt.options',function(options){
                    options.drawCallback=function(){
                        setup_time_ago();
                    };
                    return options;
                });
            },
            boot:function(){
                setup_time_ago();
            }
        });
    });
    app.module('admin',function(Module){
        var menuView={
            setup:function(){
                var url = window.location;
                var $menus=$('#side-menu').find('a');
                $menus=_.sortBy($menus,function(item){
                    return -(item.href.length);
                });
                var $menu=$menus.find(function(m){
                    return url.href.indexOf(m.href)!==-1;
                });
                //$($menu).addClass('active');
            }
        };
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
        return Module.extend({
            boot:function(){
                menuView.setup();
                confirmable.init();
                this.formInputCondition();
            },
            formInputCondition: function () {
                $('[data-show-if]', this.$el).each(function () {
                    var $block = $(this),
                        handle_id = $block.data('show-if'),
                        handle_value = $block.data('show-value'),
                        comparator = $block.data('compare') || '==';

                    if (!$block.data('bind-show-if')) {
                        var handle = $('#' + handle_id);
                        if (handle.is('select')) {
                            var select_handle_function = function () {
                                var result;
                                if ($.isArray(handle_value)) {
                                    result = (-1 !== handle_value.indexOf(handle.val()));
                                } else {
                                    result = handle_value == handle.val();
                                }
                                if (comparator != '==') {
                                    result = !result;
                                }
                                if (result) {
                                    $block.removeClass('hidden').show();
                                } else {
                                    $block.addClass('hidden').hide();
                                }
                            };
                            handle.on('change', select_handle_function);
                            select_handle_function();
                        }
                        //console.log(handle);
                        if (handle.is('input[type=checkbox]')) {
                            var checkbox_handle_function = function () {
                                var result;
                                result = handle.is(handle_value);
                                if (comparator != '==') {
                                    result = !result;
                                }
                                if (result) {
                                    $block.removeClass('hidden').show();
                                } else {
                                    $block.addClass('hidden').hide();
                                }
                            };
                            handle.on('click', checkbox_handle_function);
                            checkbox_handle_function();
                        }
                        $block.data('bind-show-if', true);
                    }

                })
            }
        })
    });
})(la);
