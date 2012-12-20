/* (c) 2008 Add This, LLC */
var _atd="http://www.addthis.com/",
		_atr="http://s7.addthis.com/",
		_euc=encodeURIComponent,
		_atu="undefined",
		_ath="",
		_atc={sec:_atr.indexOf("https")===0,
					ver:152,
					enote:"",
					cwait:500,
					samp:0.01,
					vamp:1,
					addr:-1,
					addt:1,
					xfl:!!window.addthis_disable_flash};

try {
	_ath=document.location.hostname;
} catch(e) {
}

if (typeof(addthis_conf)===_atu) {
	var addthis_conf={};
}

for (i in addthis_conf) {
	_atc[i]=addthis_conf[i];
}

if (typeof(_ate)===_atu) { (
	function() {
		var ua=navigator.userAgent.toLowerCase(),
				d=document,
				w=window,
				wa=w.addEventListener,
				we=w.attachEvent,
				dl=d.location,
				b= {
					win:/windows/.test(ua),
					chr:/chrome/.test(ua),
					saf:(/webkit/.test(ua))&&!(/chrome/.test(ua)),
					opr:/opera/.test(ua),
					msi:(/msie/.test(ua))&&(!/opera/.test(ua)),
					ie6:/msie 6.0/.test(ua)
				},
				_8= {
					isBound:false,
					isReady:false,
					readyList:[],
					onReady:function() {
						if (!_8.isReady) {
							_8.isReady=true;
							var l=_8.readyList;
							for (var fn=0;fn<l.length;fn++) {
								l[fn].call(window,[]);
							}
							_8.readyList=[];
						}
					}
				,addLoad:function(_b) {
					var _c=w.onload;
					if (typeof w.onload!="function") {
						w.onload=_b;
					} else {
						w.onload=function() {
							if (_c) {
								_c();
							}
							_b();
						};
					}
				},
				bindReady:function() {
					if (r.isBound) {
						return;
					}
					r.isBound=true;
					if (d.addEventListener&&!b.opr) {
						d.addEventListener("DOMContentLoaded",r.onReady,false);
					}
					if (b.msi&&window==top) {(
						function() {
							if (r.isReady) {
								return;
							}
							try {
								d.documentElement.doScroll("left");
							}
							catch (error) {
								setTimeout(arguments.callee,0);
								return;
							}
							r.onReady();
						})();
					}
					if (b.opr) {
						d.addEventListener("DOMContentLoaded",function() {
							if (r.isReady) {
								return;
							}
							for (var i=0;i<d.styleSheets.length;i++) {
								if (d.styleSheets[i].disabled) {
									setTimeout(arguments.callee,0);
									return;
								}
							}
							r.onReady();
						},false);
					}
					if(b.saf) {
						var _e;(function() {
							if (r.isReady) {
								return;
							}
							if (d.readyState!="loaded"&&d.readyState!="complete") {
								setTimeout(arguments.callee,0);
								return;
							}
							if (_e===undefined) {
								var _10=d.getElementsByTagName("link");
								for (var i=0;i<_10.length;i++) {
									if (_10[i].getAttribute("rel")=="stylesheet") {
										_e++;
									}
								}
								var _12=d.getElementsByTagName("style");
								_e+=_12.length;
							}
							if (d.styleSheets.length!=_e) {
								setTimeout(arguments.callee,0);
								return;
							}
							r.onReady();
						})();
					}
					r.addLoad(r.onReady);
				},
				append:function(fn,_14) {
					r.bindReady();
					if (r.isReady) {
						fn.call(window,[]);
					} else {
						r.readyList.push(function() {
							return fn.call(window,[]);
						});
					}
				}
			},
			r=_8,
			_15= {
						rev:"$Rev: 60199 $",
						ab:"-",
						bro:b,
						clck:1,
						show:1,
						dl:dl,
						samp:_atc.samp-Math.random(),
						vamp:_atc.vamp-Math.random(),
						scnt:1,
						seq:1,
						inst:1,
						wait:500,
						tmo:null,
						cvt:[],
						svt:[],
						sttm:new Date().getTime(),
						max:268435455,
						pix:"tev",
						sid:0,
						sub:typeof(at_sub)!==_atu,
						uid:null,
						swf:"http://bin.clearspring.com/at/v/1/button1.swf",
						evu:"http://e1.clearspring.com/at/",
						mun:function(s) {
							var mv=291;
							if(s) {
								for (var i=0;i<s.length;i++) {
									mv=(mv*(s.charCodeAt(i)+i)+3)&1048575;
								}
							}
							return (mv&16777215).toString(32);
						},
						off:function() {
							return Math.floor((new Date().getTime()-_15.sttm)/100).toString(16);
						},
						ran:function() {
							return Math.floor(Math.random()*4294967295).toString(36);
						},
						cst:function(c) {
							return "CXNID=2000001.521545608054043907"+(c||2)+"NXC";
						},
						img:function(i,c) {
							if (typeof(at_sub)===_atu) {
								new Image().src=_atr+"live/t00/"+i+".gif?"+_15.ran()+"&"+_15.cst(c);
							}
						},
						cuid:function() {
							return (_15.sttm&_15.max).toString(16)+(Math.floor(Math.random()*_15.max)).toString(16);
						},
						ssid:function() {
							if(_15.sid===0) {
								_15.sid=_15.cuid();
							}
							return _15.sid;
						},
						sev:function(id,_1d) {
							_15.pix="sev-"+(typeof(id)!=="number"?_euc(id):id);
							_15.svt.push(id+";"+_15.off());
							if(_1d===1) {
								_15.xmi(true);
							} else {
								_15.sxm(true);
							}
						},
						cev:function(k,v) {
							_15.pix="cev-"+_euc(k);
							_15.cvt.push(_euc(k)+"="+_euc(v)+";"+_15.off());
							_15.sxm(true);
						},
						sxm:function(b) {
							if(_15.tmo!==null) {
								clearTimeout(_15.tmo);
							}
							if(b) {
								_15.tmo=_15.sto("_ate.xmi(false)",
								_15.wait);
							}
						},
						sto:function(c,t) {
							return setTimeout(c,t);
						},
						sta:function() {
							var a=_15;
							return"AT-"+(typeof(addthis_pub)!==_atu?_euc(addthis_pub):"unknown")+"/-/"+a.ab+"/"+a.ssid()+"/"+(a.seq++)+(a.uid!==null?"/"+a.uid:"");
						},
						xmi:function(_24) {
							var a=_15,
									h=a.dl?a.dl.hostname:"";
							if(!a.uid) {
								a.dck("X"+a.cuid());
							}
							if(a.cvt.length+a.svt.length>0) {
								a.sxm(false);
								if(a.seq===1) {
									a.cev("pin",a.inst);
								}
								var url=a.evu+a.pix+"-"+a.ran()+".png?ev="+_15.sta()+"&se="+a.svt.join(",")+"&ce="+a.cvt.join(",");
								a.cvt=[];
								a.svt=[];
								if(_atc.xtr||h.indexOf(".gov")>-1||h.indexOf(".mil")>-1) {
									_atc.xtr=1;
									return;
								}
								if(_24) {
									var d=document,
											i=d.ce("iframe");
											i.id="_atf";
											i.src=url;
											_15.opp(i.style);
											d.body.appendChild(i);
											i=d.getElementById("_atf");
								} else {
									new Image().src=url;
								}
							}
						},
						loc:function() {
							try {
								var l=window.location;
								return(l.protocol.indexOf("file")===0||l.hostname.indexOf("localhost")!=-1);
							}
							catch(e) {}
							return false;
						},
						opp:function(st) {
							st.width="1px";
							st.height="1px";
							st.position="absolute";
							st.zIndex=100000;
						},
						pub:function() {
							return typeof(addthis_pub)!==_atu?_euc(addthis_pub):"";
						},
						lad:function(x) {
							_15.plo.push(x);
						},
						plo:[],
						jlo:function() {
							try {
								var d=document,
										al=(window.addthis_language||(_15.bro.msi?navigator.userLanguage:navigator.language));
								if(al&&(al.toLowerCase()).indexOf("en")!==0&&!_15.pll) {
									var o=d.ce("script");
									o.src=_atr+"static/r05/lang02.js";
									d.gn("head")[0].appendChild(o);
									_15.pll=o;
								}
								if(!_15.pld) {
									var m=d.ce("script");
									m.src="/boom/js/menu16.js";
									d.gn("head")[0].appendChild(m);
									_15.pld=m;
								}
							}
							catch(e) {}
						},
						lod:function(arg) {
							try {
								var a=_15,
								hp=0,
								f=arg===1,
								dr=d.referer||d.referrer,
								du=dl?dl.href:null,
								si=du?du.indexOf("sms_ss"):-1;
								if(!f) {
									if(a.samp>=0&&!a.sub) {
										a.sev("20");
										a.cev("plo",Math.round(1/_atc.samp));
										if(dr) {
											a.cev("pre",dr);
											hp=1;
										}
									}
									if(si>-1) {
										var sm=du.substr(si),
												am=sm.indexOf("&");
										if(am>-1) {
											sm=sm.substr(0,am);
										}
										sm=(sm.split("="))[1];
										if(a.vamp>=0&&!a.sub&&sm.length) {
											a.cev("plv",Math.round(1/_atc.vamp));
											a.cev("rsc",sm);
										}
									}
									a.img(_atc.ver+"lo","2");
								}
								if(a.plo.length>0) {
									a.jlo();
								}
								if(_15.samp>=0) {
									try {
										var z=d.gn("a");
										if(z) {
											for(var y in z) {
												y=z[y];
												if(y.toString().indexOf("/bookmark.php")>0&&!y.onmouseover) {
													y.onmouseover=function() {
														return addthis_open(this,"","","");
													};
													y.onmouseout=function() {
														addthis_close();
													};
												}
											}
										}
									}
									catch(e) {}
								}
								if (!_atc.xfl&&!(a.loc())&&(f||a.uid===null)&&a.swf) {
									var _3c=function(o,n,v) {
									var c=d.createElement("param");
									c.name=n;c.value=v;o.appendChild(c);};var o=d.createElement("object");a.opp(o.style);o.id="atff";if(b.msi){o.classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000";_3c(o,"movie",a.swf);}else{o.data=a.swf;o.quality="high";o.type="application/x-shockwave-flash";}_3c(o,"wmode","transparent");_3c(o,"allowScriptAccess","always");d.body.insertBefore(o,d.body.firstChild);if(b.msi){o.outerHTML+=" ";}}}catch(e){}},unl:function(){var a=_15;if(a.samp>=0&&!a.sub){a.sev("21",1);a.cev("pun",1/_atc.samp);}return true;},dck:function(c){_15.uid=c;var h=_15.dl?_15.dl.hostname:"";if(h.indexOf(".gov")>-1||h.indexOf(".mil")>-1){_atc.xtr=1;return;}var p=_15.pub(),x="dodpubweb,usarmymedia,usagov,disamil,education,gobiernousa,loc_webservices,massgov,govgab1".split(",");for(i in x){if(p==x[i]){return;}}if(!_atc.xck){document.cookie="_csuid="+c+"; expires=Wed, 04 Oct 2028 03:19:53 GMT; path=/";}},fcl:null,asetup:function(x){var a=_15;try{if(x!==null&&x!==_atu){a.dck(x);}if(a.fcl){a.fcl();}}catch(e){}return x;},ao:function(elt,_4b,_4c,_4d){_15.lad(["open",elt,_4b,_4c,_4d]);_15.jlo();return false;},ac:function(){},as:function(s){_15.lad(["send",s]);_15.jlo();}},a=_15;w._ate=a;w._adr=r;d.ce=d.createElement;d.gn=d.getElementsByTagName;r.bindReady();if(wa){wa("unload",a.unl,false);}else{if(we){we("onunload",a.unl);}else{w.onunload=a.unl;}}r.append(a.lod);if(d.cookie){var ck=d.cookie.split(";");for(var i=0;i<ck.length;i++){var c=ck[i],x=c.indexOf("_csuid=");if(x>=0){_15.uid=c.substring(x+7);}}}try{var l=d.ce("link");l.rel="stylesheet";l.type="text/css";l.href=_atr+"static/r05/widget08.css";l.media="all";d.gn("head")[0].appendChild(l);}catch(e){}})();function addthis_to(s){return addthis_sendto(s);}function addthis_onmouseover(elt,e,_57,_58,_59,_5a,_5b){if(_59){addthis_pub=_59;}if(_5a){addthis_language=_5a;}addthis_content=_5b||"";return addthis_open(elt,"share",_57,_58);}function addthis_onmouseout(){addthis_close();}function addthis_open(elt,_5d,_5e,_5f){return _ate.ao(elt,_5d,_5e,_5f);}function addthis_close(){_ate.ac();}function addthis_invoke(s,u,t,p){addthis_pub=p||_ate.pub();addthis_url=(u||w.addthis_url);addthis_title=(t||w.addthis_title);var w=window,lu=addthis_url.toLowerCase(),lt=addthis_title.toLowerCase();if(lu===""||lu==="[url]"){addthis_url=location.href;}if(lt===""||lt==="[title]"){addthis_title=document.title;}_ate.as(s);return false;}function addthis_sendto(s){_ate.as(s);return false;}}else{_ate.inst++;}try{if(_atc.ver===120){function x(v,n){return eval("("+v+"=(typeof "+v+" === '"+_atu+"' ? '"+(n||"")+"' : "+v+"))");}var r="atb"+_ate.cuid();document.write("<span id=\""+r+"\"></span>");_ate.lad(["span",r,x("addthis_url",""),x("addthis_title","")]);}if(window.addthis_clickout){_ate.lad(["cout"]);}}catch(e){}
