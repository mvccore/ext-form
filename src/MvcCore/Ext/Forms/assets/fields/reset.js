(function(e){e.Reset=function(a){this.a=null;this.Name=a};e.Reset.prototype={Init:function(a){var b=this;b.a=a;this.a.j(this.a.c[this.Name],"click",function(a){b.ma(a)})},ma:function(a){var b={submit:0,button:0,reset:1,radio:1,checkbox:1};this.a.b(this.a.c,function(a,c){a=c.type;"string"==typeof a&&"number"==typeof b[a]?1!=!b[a]&&(c.checked=!1):c.value=""});a.preventDefault()}}})(window.MvcCoreForm);