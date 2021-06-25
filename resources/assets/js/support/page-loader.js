/**
 * Created by Alt on 29-Mar-17.
 */
(function (drv) {
    drv.Support = drv.Support || {};
    function get(obj, path) {
        return path.split('.').reduce(function (prev, curr) {
            return prev ? prev[curr] : undefined
        }, obj || self)
    }
    function parse_str (str, array) { // eslint-disable-line camelcase
                                      //       discuss at: http://locutus.io/php/parse_str/
                                      //      original by: Cagri Ekin
                                      //      improved by: Michael White (http://getsprink.com)
                                      //      improved by: Jack
                                      //      improved by: Brett Zamir (http://brett-zamir.me)
                                      //      bugfixed by: Onno Marsman (https://twitter.com/onnomarsman)
                                      //      bugfixed by: Brett Zamir (http://brett-zamir.me)
                                      //      bugfixed by: stag019
                                      //      bugfixed by: Brett Zamir (http://brett-zamir.me)
                                      //      bugfixed by: MIO_KODUKI (http://mio-koduki.blogspot.com/)
                                      // reimplemented by: stag019
                                      //         input by: Dreamer
                                      //         input by: Zaide (http://zaidesthings.com/)
                                      //         input by: David Pesta (http://davidpesta.com/)
                                      //         input by: jeicquest
                                      //           note 1: When no argument is specified, will put variables in global scope.
                                      //           note 1: When a particular argument has been passed, and the
                                      //           note 1: returned value is different parse_str of PHP.
                                      //           note 1: For example, a=b=c&d====c
                                      //        example 1: var $arr = {}
                                      //        example 1: parse_str('first=foo&second=bar', $arr)
                                      //        example 1: var $result = $arr
                                      //        returns 1: { first: 'foo', second: 'bar' }
                                      //        example 2: var $arr = {}
                                      //        example 2: parse_str('str_a=Jack+and+Jill+didn%27t+see+the+well.', $arr)
                                      //        example 2: var $result = $arr
                                      //        returns 2: { str_a: "Jack and Jill didn't see the well." }
                                      //        example 3: var $abc = {3:'a'}
                                      //        example 3: parse_str('a[b]["c"]=def&a[q]=t+5', $abc)
                                      //        example 3: var $result = $abc
                                      //        returns 3: {"3":"a","a":{"b":{"c":"def"},"q":"t 5"}}
        var strArr = String(str).replace(/^&/, '').replace(/&$/, '').split('&');
        var sal = strArr.length;
        var i;
        var j;
        var ct;
        var p;
        var lastObj;
        var obj;
        var undef;
        var chr;
        var tmp;
        var key;
        var value;
        var postLeftBracketPos;
        var keys;
        var keysLen;
        var _fixStr = function (str) {
            return decodeURIComponent(str.replace(/\+/g, '%20'))
        };
        var $global = (typeof window !== 'undefined' ? window : global);
        $global.$locutus = $global.$locutus || {};
        var $locutus = $global.$locutus;
        $locutus.php = $locutus.php || {};
        if (!array) {
            array = $global
        }
        for (i = 0; i < sal; i++) {
            tmp = strArr[i].split('=');
            key = _fixStr(tmp[0]);
            value = (tmp.length < 2) ? '' : _fixStr(tmp[1]);
            while (key.charAt(0) === ' ') {
                key = key.slice(1)
            }
            if (key.indexOf('\x00') > -1) {
                key = key.slice(0, key.indexOf('\x00'))
            }
            if (key && key.charAt(0) !== '[') {
                keys = [];
                postLeftBracketPos = 0;
                for (j = 0; j < key.length; j++) {
                    if (key.charAt(j) === '[' && !postLeftBracketPos) {
                        postLeftBracketPos = j + 1
                    } else if (key.charAt(j) === ']') {
                        if (postLeftBracketPos) {
                            if (!keys.length) {
                                keys.push(key.slice(0, postLeftBracketPos - 1))
                            }
                            keys.push(key.substr(postLeftBracketPos, j - postLeftBracketPos));
                            postLeftBracketPos = 0;
                            if (key.charAt(j + 1) !== '[') {
                                break
                            }
                        }
                    }
                }
                if (!keys.length) {
                    keys = [key]
                }
                for (j = 0; j < keys[0].length; j++) {
                    chr = keys[0].charAt(j);
                    if (chr === ' ' || chr === '.' || chr === '[') {
                        keys[0] = keys[0].substr(0, j) + '_' + keys[0].substr(j + 1)
                    }
                    if (chr === '[') {
                        break
                    }
                }
                obj = array;
                for (j = 0, keysLen = keys.length; j < keysLen; j++) {
                    key = keys[j].replace(/^['"]/, '').replace(/['"]$/, '');
                    lastObj = obj;
                    if ((key !== '' && key !== ' ') || j === 0) {
                        if (obj[key] === undef) {
                            obj[key] = {}
                        }
                        obj = obj[key]
                    } else {
                        // To insert new dimension
                        ct = -1;
                        for (p in obj) {
                            if (obj.hasOwnProperty(p)) {
                                if (+p > ct && p.match(/^\d+$/g)) {
                                    ct = +p
                                }
                            }
                        }
                        key = ct + 1
                    }
                }
                lastObj[key] = value
            }
        }
    }
    drv.Support.parse_str=parse_str;
    var Paging = function (args) {
        if (_.isString(args)) {
            args = {url: args};
        }
        this.args = _.extend({
            url: '',
            callbacks: {
                page: function () {
                },
                done: function () {
                },
                fail: function () {
                }
            },
            nextKey: 'next',
            pageKey: 'page',
            page: '',
            data: {},
            method: 'post',
            auto: true
        }, args || {});
        this.last = undefined;
        this.next = undefined;
    };
    _.extend(Paging.prototype, {
        pageKey: function (pageKey) {
            this.args.pageKey = pageKey;
            return this;
        },
        nextKey: function (nextKey) {
            this.args.nextKey = nextKey;
            return this;
        },
        url: function (url) {
            this.args.url = url;
            return this;
        },
        data: function (data,merge) {
            if(_.isString(data)){
                var _data={};
                parse_str(data,_data);
                data=_data;
            }
            this.args.data = data;
            if(!merge){
                this.reset();
            }
            return this;
        },
        reset:function(){
            this.last=undefined;
            this.next=undefined;
            return this;
        },
        fail: function (callback) {
            this.args.callbacks.fail = callback;
            return this;
        },
        page: function (callback) {
            this.args.callbacks.page = callback;
            return this;
        },
        done: function (callback) {
            this.args.callbacks.done = callback;
            return this;
        },
        load: function (page) {
            page = page || this.next;
            this.args.auto = false;
            if (page !== false) {
                this.run(page);
            }
        },
        run: function (page) {
            page = page || '';
            if (this.last === page) {
                this.next = false;
                this.args.callbacks.done({success: 0, message: 'Duplicate'});
                return;
            }
            this.last = page;
            var pageObj = {},
                pageKey = this.args.pageKey,
                nextKey = this.args.nextKey,
                nextPage,
                self = this
            ;

            pageObj[pageKey] = page;
            _.extend(this.args.data, pageObj);
            $.ajax({
                method: this.args.method,
                url: this.args.url,
                dataType: 'json',
                data: this.args.data,
                context: this
            }).done(function (response) {
                if (response.success && response.data) {
                    nextPage = get(response.data, nextKey);
                    if (nextPage) {
                        self.args.callbacks.page(response.data, false);
                        self.next = nextPage;
                        self.args.auto && self.run(nextPage);
                    } else {
                        self.next = false;
                        //last page but still have data, fire page callback first
                        self.args.callbacks.page(response.data, true);
                        self.args.callbacks.done(response);
                    }

                } else {
                    self.next = false;
                    self.args.callbacks.done(response);
                }
            }).fail(function (response) {
                self.next = false;
                //still done when fail
                self.args.callbacks.done(response);
                self.args.callbacks.fail(response);
            })
        }

    });
    drv.Support.PageLoader = Paging;

})(drv);