(function(g){g.Range=function(a){this.a=null;this.Name=a};g.Range.prototype={ga:"% - %",i:null,Y:null,M:null,j:null,l:null,Init:function(a){this.a=a;this.i=a.f[this.Name];null!=this.i.getAttribute("multiple")&&(this.ja(),this.qa(),this.ia(),this.oa())},ja:function(){var a=this.i,b=this.a,c=b.O,d=b.P,e=b.H,f=b.I,g=b.C,k=b.B,b=[],h=d("span"),d=d("span"),l=a.cloneNode();e(a,"range-multiple");var n=f(a,"data-value");n.length?b=n.split(","):b=[f(a,"min")||"0",f(a,"max")||"100"];k(l,"multiple");k(l,"id");
g(l,"value",b[0]);f=l.name;l.name=-1<f.indexOf("[]")?f:f+"[]";f=l.cloneNode();g(f,"value",b[1]);e(l,"first");e(f,"second");g(h,"id",a.id);h.className=a.className;d=c(h,d);l=c(h,l);f=c(h,f);h=a.parentNode.replaceChild(h,a);this.j=l;this.l=f;this.Y=h;this.M=d},qa:function(){var a=this.j,b=this.l;"stepDown"in a||(a.stepDown=function(b){a.value-=b});"stepUp"in b||(b.stepUp=function(a){b.value+=a})},ia:function(){var a=this,b=a.j,c=a.l,d=a.T,e=a.fa,f=e(b),g=e(c);a.L(b,function(){for(var e=0,g=d(b.value)+
f;g>d(c.value)&&!(c.stepUp(),e+=1,100<e););a.A()});a.L(c,function(){for(var e=0,f=d(c.value)-g;f<d(b.value)&&!(b.stepDown(),e+=1,100<e););a.A()});a.A()},oa:function(){var a=this,b=a.a,c=a.i.getAttribute("data-value"),d="",e="",f=["",""];null==c||""===c?(d=a.i.getAttribute("min"),e=a.i.getAttribute("max"),f=[null!=d&&""!==d?d:a.j.value,null!=e&&""!==e?e:a.l.value]):f=c.split(",");b.o(b.f,"reset",function(){a.j.value=f[0];a.l.value=f[1];a.A()})},L:function(a,b){this.a.o(a,this.a.ta?"change":"input",
b)},A:function(){this.M.innerHTML=this.ga.replace("%",this.j.value).replace("%",this.l.value)},T:function(a){return parseFloat(a)},fa:function(a){var b=a.step.toString(),c=1;0<b.length?c=parseInt(b,10):(a=a.length.toString(),0<a.length&&(b=a.lastIndexOf("."),-1<b&&(a=a.substr(b+1),0<a.length&&(c=parseInt(a,10)/10))));return c}}})(window.MvcCoreForm);