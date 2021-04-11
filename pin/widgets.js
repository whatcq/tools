!
function() {
	"undefined" == typeof window.HUABAN_GLOBAL && (window.HUABAN_GLOBAL = {},
	function(a, b, c) {
		{
			var d = "1.1.2",
			e = b.documentElement,
			f = /(^\n+)|(\n+$)/g,
			g = /^(?:\{.*\}|\[.*\])$/,
			h = /-([a-z])/gi,
			i = /\r\n/g,
			j = /[\-\+0-9\.]/gi,
			k = /\s+/,
			l = /\?/,
			m = /opacity=([^)]*)/,
			n = /^[\],:{}\s]*$/,
			o = /\\(?:["\\\/bfnrt]|u[\da-fA-F]{4})/g,
			p = /"[^"\\\r\n]*"|true|false|null|-?(?:\d\d*\.|)\d+(?:[eE][\-+]?\d+|)/g,
			q = /(?:^|:|,)(?:\s*\[)+/g,
			r = "height margin-top margin-bottom padding-top padding-bottom".split(" "),
			s = {},
			t = {},
			u = [],
			v = function() {
				E(u,
				function(a) {
					a()
				}),
				b.removeEventListener("DOMContentLoaded", v, !1)
			},
			w = function(a, c) {
				var d = typeof c;
				if ("string" === d) {
					var e = a && a.ownerDocument || b,
					f = e.createDocumentFragment(),
					g = G("div");
					for (g.innerHTML = c; null != g.childNodes[0];) f.appendChild(g.childNodes[0]);
					c = f,
					g = null
				}
				return "number" === d && (c += ""),
				c
			},
			x = function(a, b) {
				if (null !== a) {
					if (b === c) return a;
					var d = 0,
					e = a.length;
					if (e !== c && e > 0) {
						for (; e > d && b.call(a[d], a[d], a[d++]) !== !1;);
						return a
					}
					return b.call(a, a)
				}
			},
			y = function(a, c, d, e) {
				return x(a,
				function(a) {
					var f, g, h = {},
					i = "show" === c,
					j = a.style,
					k = a._display;
					k || (k = N.get(a, "display"), ("none" === k || "inherit" === k) && (f = O(b.body, G(a.nodeName)), k = N.get(f, "display"), P(f)), a._display = k),
					i ? j.display = k: k = "none",
					d ? (g = N.get(a, "overflow"), j.overflow = "hidden", h.opacity = i ? {
						from: 0,
						to: 1
					}: {
						from: 1,
						to: 0
					},
					E(r,
					function(b) {
						h[b] = i ? {
							from: 0,
							to: N.get(a, b)
						}: 0
					}), W(a, h, d,
					function() {
						E(r,
						function(b) {
							M.set(a, b, "")
						}),
						j.filter = j.opacity = j.overflow = "",
						j.display = k,
						e && e.call(a)
					})) : j.display = k
				})
			},
			z = function(a, b) {
				if ("object" == typeof b) {
					var c = [];
					return E(b,
					function(b, d) {
						c.push("object" == typeof b ? z(a + "[" + d + "]", b) : a + "[" + Z(d) + "]=" + Z(b))
					}),
					c.join("&")
				}
				return Z(a) + "=" + Z(b)
			},
			A = b.removeEventListener ?
			function(a, b, c) {
				a.removeEventListener(b, c, !1)
			}: function(a, b, c) {
				a.detachEvent("on" + b, c)
			},
			B = function() {
				try {
					return localStorage.setItem("test", "test"),
					localStorage.removeItem("test"),
					!0
				} catch(a) {
					return ! 1
				}
			} (),
			C = {},
			D = C.$ = function(a) {
				return b.getElementById(a)
			},
			E = C.$each = function(a, b) {
				var c, d = 0,
				e = a.length,
				f = typeof a,
				g = "object" === f;
				if (g && e - 1 in a) for (; e > d && b.call(a[d], a[d++], d) !== !1;);
				else if (g) for (c in a) b.call(a[c], a[c], c);
				else b.call(a, a, 0);
				return a
			},
			F = (C.$id = function(a, b) {
				var c, d = [];
				return E(a instanceof Array ? a: a.split(" "),
				function(a) {
					c = D(a),
					null !== c && d.push(c)
				}),
				b ? x(d, b) : d
			},
			C.$dom = function(a, b) {
				return b && (a.length ? x(a, b) : b(a)),
				a
			},
			C.$tag = function(a, b, c) {
				var d = [],
				e = a.getElementsByTagName(b),
				f = e.length,
				g = 0;
				if (f > 0) {
					for (; f > g; g++) d.push(e[g]);
					return x(d, c)
				}
				return d
			}),
			G = (C.$class = b.getElementsByClassName ?
			function(a, b, c) {
				var d = [],
				e = a.getElementsByClassName(b),
				f = e.length,
				g = 0;
				if (f > 0) {
					for (; f > g; g++) d.push(e[g]);
					return x(d, c)
				}
				return d
			}: function(a, b, c) {
				var d = [],
				e = new RegExp("(^|\\s)" + b + "(\\s|$)");
				return F(a, "*",
				function(a) {
					e.test(a.className) && d.push(a)
				}),
				x(d, c)
			},
			C.$select = b.querySelectorAll ?
			function(a, c) {
				return x(b.querySelectorAll(a), c)
			}: function(a, c) {
				var d = C.Qselector.styleSheet,
				e = [];
				return d.addRule(a, "q:a"),
				F(b, "*",
				function(a) {
					"a" === a.currentStyle.q && e.push(a)
				}),
				d.cssText = "",
				x(e, c)
			},
			C.$new = function(a, c) {
				var d = b.createElement(a);
				if (c) try {
					return E(c,
					function(a, b) {
						switch (b) {
						case "innerHTML":
						case "html":
							R(d, a);
							break;
						case "className":
						case "class":
							T.set(d, a);
							break;
						case "text":
							S(d, a);
							break;
						default:
							I.set(d, b, a)
						}
					}),
					d
				} catch(e) {} finally {
					d = null
				}
				return d
			}),
			H = C.$string = {
				camelCase: function(a) {
					return a.replace("-ms-", "ms-").replace(h,
					function(a, b) {
						return (b + "").toUpperCase()
					})
				},
				replace: function(a, b) {
					for (var c in b) a = a.replace(new RegExp(c, "ig"), b[c]);
					return a
				},
				slashes: function(a) {
					return H.replace(a, {
						"\\\\": "\\\\",
						"\b": "\\b",
						"	": "\\t",
						"\n": "\\n",
						"\r": "\\r",
						'"': '\\"'
					})
				},
				trim: "".trim && "tes ts" === " tes ts ".trim() ?
				function(a) {
					return a.trim()
				}: function(a) {
					return (a + "").replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g, "")
				}
			},
			I = C.$attr = {
				get: function(a, b) {
					return a.getAttribute(b)
				},
				set: function(a, b, c) {
					return x(a,
					function(a) {
						a.setAttribute(b, c)
					})
				},
				remove: function(a, b) {
					return x(a,
					function(a) {
						a.removeAttribute(b)
					})
				}
			},
			J = C.$data = {
				get: function(a, b) {
					var c = I.get(a, "data-" + b);
					return "true" === c ? !0 : "false" === c ? !1 : "null" === c ? "": null === c ? "": "" === c ? "": !isNaN(parseFloat(c)) && isFinite(c) ? +c: g.test(c) ? Y.decode(c) : c
				},
				set: function(a, b, c) {
					return x(a,
					function(a) {
						return c = "object" == typeof c ? Y.encode(c) : c,
						"object" == typeof b ? E(b,
						function(b, c) {
							I.set(a, "data-" + c, b)
						}) : I.set(a, "data-" + b, c),
						a
					})
				},
				remove: function(a, b) {
					return x(a,
					function(a) {
						I.remove(a, "data-" + b)
					})
				}
			},
			K = (C.$storage = B ? {
				set: function(a, b) {
					localStorage[a] = "object" == typeof b ? Y.encode(b) : b
				},
				get: function(a) {
					var b = localStorage[a];
					return Y.isJSON(b) ? Y.decode(b) : b || ""
				},
				remove: function(a) {
					return localStorage.removeItem(a),
					!0
				}
			}: {
				set: function(a, b) {
					b = "object" == typeof b ? Y.encode(b) : b,
					J.set(C.storage, a, b),
					C.storage.save("Qstorage")
				},
				get: function(a) {
					return C.storage.load("Qstorage"),
					J.get(C.storage, a) || ""
				},
				remove: function(a) {
					return C.storage.load("Qstorage"),
					J.remove(C.storage, a),
					!0
				}
			},
			C.$event = {
				guid: 0,
				global: {},
				handler: {
					call: function(a, b, c, d) {
						var e = K.handler;
						return c.currentTarget = b,
						(d === !1 || d.call(b, c) === !1) && (e.stopPropagation.call(c), e.preventDefault.call(c), a.isStopPropagation = !0),
						a
					},
					preventDefault: function() {
						var a = this.originalEvent;
						a.preventDefault && a.preventDefault()
					},
					stopPropagation: function() {
						var a = this.originalEvent;
						a.stopPropagation && a.stopPropagation()
					},
					stopImmediatePropagation: function() {
						this.stopPropagation()
					},
					mouseenter: function(a) {
						return function(b) {
							var c = b.relatedTarget;
							if (this !== c) {
								for (; c && c !== this;) c = c.parentNode;
								c !== this && a.call(this, b)
							}
						}
					}
				},
				on: function(a, d, e, f, g) {
					return x(a,
					function(a) {
						if (3 === a.nodeType || 8 === a.nodeType) return ! 1;
						if ("object" == typeof d) {
							"string" != typeof e && (f = f || e, e = c);
							for (type in d) K.on(a, type, e, f, d[type]);
							return this
						}
						null == f && null == g ? (g = e, f = e = c) : null == g && ("string" == typeof e ? (g = f, f = c) : (g = f, f = e, e = c)),
						a.guid === c && (K.guid++, a.guid = K.guid, K.global[K.guid] = {});
						var h = a.guid,
						i = (e || "") + d,
						j = K.global[h][i];
						return j || (K.global[h][i] = {}),
						delegate_fn = function(d) {

							///////////////////////////2015/4/18
							//javascript:void(function(d,a,c,b){d.PIN_URL_BASE='http://test/test/pin';(b=a.createElement('script'),b.id='xx_script',b.setAttribute('charset','utf-8'),b.src=d.PIN_URL_BASE+'/widgets.js',a.body.appendChild(b))}(window,document,'localPin'));
							if(d.target.className=='HUABAN-btn HUABAN-cell-pin-btn'){
								var unils = HUABAN_GLOBAL.collector.units, n = unils.length,
									_src = d.path[2].childNodes[0].currentSrc;
								for (var i=0; i<n; i++){
									if(_src === unils[i].imgUrl){
										if(typeof HUABAN_GLOBAL.collector.units[i].posting !== 'number' || HUABAN_GLOBAL.collector.units[i].posting<1){
											HUABAN_GLOBAL.collector.units[i].posting = 1;
											//console.log(unils[i]);
											var _ = unils[i];
											HUABAN_GLOBAL.Qatrix.$ajax({
												url: PIN_URL_BASE + '/pin.php',
												data: {
													url: _.elem.baseURI,
													title: document.title,
													img: _.imgUrl,
													w: _.size.x,
													h: _.size.y,
													text: d.path[3].lastChild.value
												},
												success: function(){
													HUABAN_GLOBAL.collector.units[i].posting = 2;
												},
												error: function(){
													HUABAN_GLOBAL.collector.units[i].posting = 0;
												}
											});
										}
										return;
									}
								}
								return;
							}
							///////////////////////////2015/4/18 cqiu

							var h, i, j = {},
							k = "altKey bubbles button buttons cancelable relatedTarget clientX clientY ctrlKey fromElement offsetX offsetY pageX pageY screenX screenY shiftKey toElement timeStamp".split(" ");

							if (E(k,
							function(a) {
								d[a] !== c && (j[a] = d[a])
							}), j.originalEvent = d, j.preventDefault = K.handler.preventDefault, j.stopPropagation = K.handler.stopPropagation, j.stopImmediatePropagation = K.handler.stopImmediatePropagation, j.delegateTarget = a, j.isStopPropagation = !1, j.data = f, i = j.target = e ? d.target || d.srcElement || b: a, j.which = d.which || d.charCode || d.keyCode, j.metaKey = d.metaKey || d.ctrlKey, e) for (h = e ? e.replace(".", "") : ""; i !== a; i = i.parentNode) {
								if (d.isStopPropagation === !0) return ! 1;
								if (null === i || i === b.body) return ! 1; 
								(i.tagName.toLowerCase() === e || T.has(i, h)) && (d = K.handler.call(d, i, j, g))
							} else 
								K.handler.call(d, i, j, g)
						},
						a.addEventListener ? (("mouseenter" === d || "mouseleave" === d) && (d = "mouseenter" === d ? "mouseover": "mouseout", delegate_fn = K.handler.mouseenter(delegate_fn)), a.addEventListener(d, delegate_fn, "blur" === d || "focus" === d)) : a.attachEvent("on" + d, delegate_fn),
						K.global[h][i][g + ""] = delegate_fn,
						a
					})
				},
				off: function(a, b, c, d) {
					if ("object" == typeof b) {
						for (type in b) K.off(a, type, b[type]);
						return this
					}
					null == d && (d = c, c = null);
					var e = a.guid,
					f = (c || "") + b;
					fn_key = d + "",
					d ? (A(a, b, K.global[e][f][fn_key]), delete K.global[e][f][fn_key]) : (E(K.global[e][f],
					function(c) {
						A(a, b, c)
					}), delete K.global[e][f])
				}
			}),
			L = (C.$clear = function(a) {
				return a && (clearTimeout(a), clearInterval(a)),
				null
			},
			C.$ready = function(a) {
				if ("complete" === b.readyState) return setTimeout(a, 1);
				if (b.addEventListener) return u.push(a),
				void b.addEventListener("DOMContentLoaded", v, !1);
				var c = function() {
					try {
						e.doScroll("left")
					} catch(b) {
						return void setTimeout(c, 1)
					}
					a()
				};
				c()
			}),
			M = C.$css = {
				get: function(a, b) {
					if ("object" == typeof b) {
						var c = {};
						return E(b,
						function(b) {
							c[b] = N.get(a, b)
						}),
						c
					}
					return N.get(a, b)
				},
				set: function(a, b, c) {
					return x(a,
					function(a) {
						return "object" == typeof b ? E(b,
						function(b, c) {
							N.set(a, H.camelCase(c), M.fix(c, b))
						}) : N.set(a, H.camelCase(b), M.fix(b, c)),
						a
					})
				},
				number: {
					fontWeight: !0,
					lineHeight: !0,
					opacity: !0,
					zIndex: !0
				},
				unit: function(a, b) {
					if (M.number[a]) return "";
					var c = (b + "").replace(j, "");
					return "" === c ? "px": c
				},
				fix: function(a, b) {
					return "number" != typeof b || M.number[a] || (b += "px"),
					null === b && isNaN(b) ? !1 : b
				}
			},
			N = C.$style = {
				get: a.getComputedStyle ?
				function(a, c) {
					if (null !== a) {
						var d = b.defaultView.getComputedStyle(a, null).getPropertyValue(c);
						return "auto" === d || "" === d ? 0 : d
					}
					return ! 1
				}: function(a, b) {
					if (null !== a) {
						var c = "opacity" === b ? m.test(a.currentStyle.filter) ? .01 * parseFloat(RegExp.$1) + "": 1 : a.currentStyle[H.camelCase(b)];
						return "auto" === c ? 0 : c
					}
					return ! 1
				},
				set: e.style.opacity !== c ?
				function(a, b, c) {
					return x(a,
					function(a) {
						return a.style[b] = c,
						!0
					})
				}: function(a, b, c) {
					return x(a,
					function(a) {
						return a.currentStyle && a.currentStyle.hasLayout || (a.style.zoom = 1),
						"opacity" === b ? (a.style.filter = "alpha(opacity=" + 100 * c + ")", a.style.zoom = 1) : a.style[b] = c,
						!0
					})
				}
			},
			O = (C.$pos = function(a, b, c) {
				return x(a,
				function(a) {
					return N.set(a, "left", b + "px"),
					N.set(a, "top", c + "px"),
					a
				})
			},
			C.$offset = function(c) {
				var d = b.body,
				f = c.getBoundingClientRect();
				return {
					top: f.top + (a.scrollY || d.parentNode.scrollTop || c.scrollTop) - (e.clientTop || d.clientTop || 0),
					left: f.left + (a.scrollX || d.parentNode.scrollLeft || c.scrollLeft) - (e.clientLeft || d.clientLeft || 0),
					width: c.offsetWidth,
					height: c.offsetHeight
				}
			},
			C.$append = function(a, b) {
				return x(a,
				function(a) {
					return a.appendChild(w(a, b))
				})
			}),
			P = (C.$prepend = function(a, b) {
				return x(a,
				function(a) {
					return a.firstChild ? a.insertBefore(w(a, b), a.firstChild) : a.appendChild(w(a, b))
				})
			},
			C.$before = function(a, b) {
				return x(a,
				function(a) {
					return a.parentNode.insertBefore(w(a, b), a)
				})
			},
			C.$after = function(a, b) {
				return x(a,
				function(a) {
					return a.nextSibling ? a.parentNode.insertBefore(w(a, b), a.nextSibling) : a.parentNode.appendChild(w(a, b))
				})
			},
			C.$remove = function(a) {
				return x(a,
				function(a) {
					return Q(a),
					a.guid !== c && delete K.global[a.guid],
					null !== a && a.parentNode ? a.parentNode.removeChild(a) : a
				})
			}),
			Q = C.$empty = function(a) {
				return x(a,
				function(a) {
					for (; a.firstChild;) 1 === a.nodeType && a.guid !== c && delete K.global[a.guid],
					a.removeChild(a.firstChild);
					return a
				})
			},
			R = C.$html = function(a, b) {
				return x(a,
				function(a) {
					if (b) {
						try {
							a.innerHTML = b
						} catch(c) {
							O(Q(a), b)
						}
						return a
					}
					return 1 === a.nodeType ? a.innerHTML: null
				})
			},
			S = C.$text = function(a, c) {
				return x(a,
				function(a) {
					if (c) return Q(a),
					a.appendChild(b.createTextNode(c)),
					a;
					var d, e = "",
					g = a.textContent;
					if ((g || a.innerText) === a.innerHTML) e = g ? H.trim(a.textContent.replace(f, "")) : a.innerText.replace(i, "");
					else for (a = a.firstChild; a; a = a.nextSibling) d = a.nodeType,
					3 === d && "" !== H.trim(a.nodeValue) && (e += a.nodeValue.replace(f, "") + (a.nextSibling && a.nextSibling.tagName && "br" !== a.nextSibling.tagName.toLowerCase() ? "\n": "")),
					(1 === d || 2 === d) && (e += S(a) + ("block" === N.get(a, "display") || "br" === a.tagName.toLowerCase() ? "\n": ""));
					return e
				})
			},
			T = C.$className = {
				add: function(a, b) {
					return x(a,
					function(a) {
						if ("" === a.className) a.className = b;
						else {
							var c = a.className,
							d = [];
							E(b.split(k),
							function(a) {
								new RegExp("\\b(" + a + ")\\b").test(c) || d.push(" " + a)
							}),
							a.className += d.join("")
						}
						return a
					})
				},
				set: function(a, b) {
					return x(a,
					function(a) {
						return a.className = b,
						a
					})
				},
				has: function(a, b) {
					return new RegExp("\\b(" + b.split(k).join("|") + ")\\b").test(a.className)
				},
				remove: function(a, b) {
					return x(a,
					function(a) {
						return a.className = b ? H.trim(a.className.replace(new RegExp("\\b(" + b.split(k).join("|") + ")\\b"), "").split(k).join(" ")) : "",
						a
					})
				}
			},
			U = C.$hide = function(a, b, c) {
				y(a, "hide", b, c)
			},
			V = C.$show = function(a, b, c) {
				y(a, "show", b, c)
			},
			W = (C.$toggle = function(a, b, c) {
				return x(a,
				function(a) {
					"none" === N.get(a, "display") ? V(a, b, c) : U(a, b, c)
				})
			},
			C.$animate = function() {
				var a = e.style;
				return a.webkitTransition !== c || a.MozTransition !== c || a.OTransition !== c || a.msTransition !== c || a.transition !== c
			} () ?
			function() {
				var a = e.style,
				b = a.webkitTransition !== c ? "Webkit": a.MozTransition !== c ? "Moz": a.OTransition !== c ? "O": a.msTransition !== c ? "ms": "",
				d = b + "Transition";
				return function(a, b, e, f) {
					return x(a,
					function(a) {
						var g, h = [],
						i = [],
						j = [],
						k = [],
						l = a.style;
						e = e || 300;
						for (g in b) i[g] = H.camelCase(g),
						b[g].from !== c ? (b[g].to = b[g].to || 0, h[g] = M.number[g] ? b[g].to: parseInt(b[g].to, 10), j[g] = M.unit(g, b[g].to), N.set(a, i[g], parseInt(b[g].from, 10) + j[g])) : (h[g] = M.number[g] ? b[g] : parseInt(b[g], 10), j[g] = M.unit(g, b[g]), N.set(a, i[g], N.get(a, i[g]))),
						k.push(g);
						return setTimeout(function() {
							l[d] = "all " + e + "ms",
							E(k,
							function(a) {
								l[i[a]] = h[a] + j[a]
							})
						},
						15),
						setTimeout(function() {
							l[d] = "",
							f && f.call(a)
						},
						e),
						a
					})
				}
			} () : function(a, b, d, e) {
				return x(a,
				function(a) {
					var f, g, h, i = 0,
					j = 0,
					k = 0,
					l = 0,
					m = 30,
					n = [],
					o = [],
					p = [],
					q = [],
					r = [];
					d = d || 300;
					for (g in b) p.push(H.camelCase(g)),
					b[g].from !== c ? (f = b[g].to, o.push(M.number[g] ? b[g].from: parseInt(b[g].from, 10)), N.set(a, p[j], o[j] + M.unit(g, f))) : (f = b[g], o.push(parseInt(N.get(a, H.camelCase(g)), 10))),
					n.push(M.number[g] ? f: parseInt(f, 10)),
					q.push(M.unit(g, f)),
					j++,
					l++;
					for (k = 0; m > k; k++) for (r[k] = [], j = 0; l > j; j++) r[k][p[j]] = o[j] + (n[j] - o[j]) / m * k + ("opacity" === p[j] ? "": q[j]);
					for (; m > j; j++) h = setTimeout(function() {
						for (j = 0; l > j; j++) N.set(a, p[j], r[i][p[j]]);
						i++
					},
					d / m * j);
					return setTimeout(function() {
						for (j = 0; l > j; j++) N.set(a, p[j], n[j] + q[j]);
						e && e.call(a)
					},
					d),
					a
				})
			}),
			X = (C.$fadeout = function(a, b, c) {
				return x(a,
				function(a) {
					W(a, {
						opacity: {
							from: 1,
							to: 0
						}
					},
					b || 500, c)
				})
			},
			C.$fadein = function(a, b, c) {
				return x(a,
				function(a) {
					W(a, {
						opacity: {
							from: 0,
							to: 1
						}
					},
					b || 500, c)
				})
			},
			C.$cookie = {
				get: function(a) {
					for (var c, d, e = b.cookie.split("; "), f = 0, g = e.length; g > f; f++) if (c = e[f].split("="), c[0] === a) return d = decodeURIComponent(c[1]),
					Y.isJSON(d) ? Y.decode(d) : d + "";
					return null
				},
				set: function(a, c, d) {
					if ("object" == typeof a) return d = c,
					E(a,
					function(a, b) {
						X.set(b, a, d)
					});
					var e = new Date;
					return e.setTime(e.getTime()),
					d = d ? ";expires=" + new Date(e.getTime() + 864e5 * d).toGMTString() : "",
					c = "object" == typeof c ? Y.encode(c) : c,
					b.cookie = a + "=" + Z(c) + d + ";path=/"
				},
				remove: function() {
					return E(arguments,
					function(a) {
						X.set(a, "", -1)
					}),
					!0
				}
			}),
			Y = C.$json = {
				decode: a.JSON ?
				function(a) {
					return Y.isJSON(a) ? JSON.parse(H.trim(a)) : !1
				}: function(a) {
					return Y.isJSON(a) ? new Function("return " + H.trim(a))() : !1
				},
				encode: a.JSON ?
				function(a) {
					return JSON.stringify(a)
				}: function(a) {
					function b(a) {
						var b, c, d, e, f = [];
						for (b in a) {
							if (d = a[b], c = typeof d, "undefined" === c) return;
							if ("function" !== c) {
								switch (c) {
								case "object":
									e = null === d ? d: d.getDay ? '"' + (1e3 - 10 * ~d.getUTCMonth() + d.toUTCString() + 1e3 + d / 1).replace(/1(..).*?(\d\d)\D+(\d+).(\S+).*(...)/, "$3-$1-$2T$4.$5Z") + '"': d.length ? "[" +
									function() {
										var a = [];
										return E(d,
										function(b) {
											a.push("string" == typeof b ? '"' + H.slashes(b) + '"': b)
										}),
										a.join(",")
									} () + "]": Y.encode(d);
									break;
								case "number":
									e = isFinite(d) ? d: null;
									break;
								case "boolean":
								case "null":
									e = d;
									break;
								case "string":
									e = '"' + H.slashes(d) + '"'
								}
								f.push('"' + b + '":' + e)
							}
						}
						return f.join(",")
					}
					return "{" + b(a) + "}"
				},
				isJSON: function(a) {
					return "string" == typeof a && "" !== H.trim(a) ? n.test(a.replace(o, "@").replace(p, "]").replace(q, "")) : !1
				}
			},
			Z = (C.$ajax = function(b, d) {
				"object" == typeof b && (d = b, b = c),
				d = d || {};
				var e, f = a.ActiveXObject ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest,
				b = b || d.url,
				g = [];
				d.data && E(d.data,
				function(a, b) {
					g.push(z(b, a))
				}),
				e = g.join("&").replace(/%20/g, "+"),
				"GET" === d.type ? (f.open("GET", b + (l.test(b) ? "&": "?") + e, !0), e = null) : f.open(d.type || "POST", b, !0),
				f.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8"),
				d.header && E(d.header,
				function(a, b) {
					f.setRequestHeader(b, a)
				}),
				f.send(e),
				f.onreadystatechange = function() {
					4 === f.readyState && (200 === f.status && d.success ? (e = f.responseText, d.success("" !== e && Y.isJSON(e) ? Y.decode(e) : e)) : d.error && d.error(f.status))
				}
			},
			C.$require = function(a, c) {
				var d, f = [];
				E(a,
				function(a) {
					s[a] || (s[a] = !0, f.push(a), d = /\.css[^\.]*$/gi.test(a) ? G("link", {
						type: "text/css",
						rel: "stylesheet",
						href: a
					}) : G("script", {
						type: "text/javascript",
						async: !0,
						src: a
					}), d.onload = d.onreadystatechange = function(b) { ("load" === b.type || /loaded|complete/.test(d.readyState)) && (d.onload = d.onreadystatechange = null, f.splice(f.indexOf(a), 1), 0 === f.length && c && c())
					},
					O(b.head || F(b, "head")[0] || e, d))
				})
			},
			C.$template = function(a, b) {
				var c = t[a];
				return c || (c = "var s='';s+='" + a.replace(/[\r\t\n]/g, " ").split("'").join("\\'").replace(/\{\{#([\w]*)\}\}(.*)\{\{\/(\1)\}\}/gi,
				function(a, b, c) {
					return "';var i=0,l=data." + b + ".length,d=data." + b + ";for(;i<l;i++){s+='" + c.replace(/\{\{(\.|this)\}\}/g, "'+d[i]+'").replace(/\{\{([\w]*)\}\}/g, "'+d[i].$1+'") + "'}s+='"
				}).replace(/\{\{(.+?)\}\}/g, "'+data.$1+'") + "';return s;", t[a] = c),
				b ? new Function("data", c)(b) : new Function("data", c)
			},
			C.$url = function(a) {
				return encodeURIComponent(a)
			}),
			$ = (C.$rand = function(a, b) {
				return Math.floor(Math.random() * (b - a + 1) + a)
			},
			C.$browser = function() {
				for (var a = navigator.userAgent.toLowerCase(), b = {},
				c = "msie|firefox|opera|webkit|iPad|iPhone|android".split("|"), d = c.length, e = 6; d--;) b[c[d]] = a.indexOf(c[d].toLowerCase()) > -1;
				for (; 12 > e; e++) b["msie" + e] = a.indexOf("msie " + e) > -1;
				return b.is = function(b) {
					return new RegExp(b, "ig").test(a)
				},
				b
			} (), C.$isArray = Array.isArray ||
			function(a) {
				return "[object Array]" == Object.prototype.toString.call(a)
			}),
			_ = C.$merge = function(a) {
				for (var b = 1,
				c = arguments.length; c > b; b++) {
					var d = arguments[b];
					if ("object" != typeof a || "object" != typeof d || $(a) || $(d)) a = d;
					else for (var e in d) a[e] = _(a[e], d[e])
				}
				return a
			},
			ab = C.$clone = function(a) {
				var b = function(a) {
					return "object" == typeof a ? ab(a) : a
				};
				if ($(a)) {
					for (var c = a.length,
					d = new Array(c); c--;) d[c] = b(a[c]);
					return d
				}
				var d = {};
				for (var e in a) d[e] = b(a[e]);
				return d
			};
			C.$filter = function(a, b, c) {
				for (var d = [], e = 0, f = a.length >>> 0; f > e; e++) e in a && b.call(c, a[e], e, a) && d.push(a[e]);
				return d
			},
			C.$findParent = function(a, b) {
				for (var c = a.parentNode; c && 11 !== c.nodeType;) {
					if (b(c)) return c;
					c = c.parentNode
				}
				return null
			},
			C.$parent = function(a, b) {
				var c = a && a.parentNode;
				return c && 11 !== c.nodeType && (!b || b(c)) ? c: null
			},
			C.$parallel = function(a, b) {
				function c(a, c) {
					d[a] = c,
					f++,
					f == e && b(d)
				}
				var d = {},
				e = 0,
				f = 0;
				for (var g in a) e++;
				for (var g in a) !
				function(b) {
					a[b](function(a) {
						c(b, a)
					})
				} (g)
			},
			C.$parallelEach = function(a, b, c) {
				function d() {
					e -= 1,
					0 >= e && c()
				}
				c = c ||
				function() {};
				var e = a.length;
				return e ? void E(a,
				function(a) {
					b(a, d)
				}) : c()
			},
			C.$getQueryParams = function(a) {
				return a = a && a.split("?")[1] || b.location.search.slice(1),
				a.split("&").map(function(a) {
					return a = a.split("="),
					this[a[0]] = a[1],
					this
				}.bind({}))[0]
			}
		}
		C.version = d,
		"undefined" != typeof HUABAN_GLOBAL && (HUABAN_GLOBAL.Qatrix = C),
		L(function() {
			b.querySelectorAll || (C.Qselector = O(b.body, G("style"))),
			B || (C.storage = O(b.body, G("link", {
				style: {
					behavior: "url(#default#userData)"
				}
			})))
		})
	} (window, document),
	function(a, b) {
		var c = b.Qatrix,
		d = PIN_URL_BASE; // "http://huaban.com";//
		a.__huaban_dev && (d = "http://" + __huaban_dev);
		var e = b.settings = {
			autoInitialize: !0,
			autoAttachFloatingButton: !1,
			imageMinWidth: 200,
			imageMinHeight: 150,
			elemMinWidth: 150,
			elemMinHeight: 150,
			getSizeTimeout: 1e3,
			orderBy: "recommend",
			prefix: "HUABAN-",
			id: "HUABAN_WIDGETS",
			priority: {
				video: 2,
				og: 1,
				img: 0,
				bg: -1
			},
			huabanUrl: d,
			//popupUrl: d + "/bookmarklet/",
			//multiPopupUrl: d + "/bookmarklet_multiple/",
			popupUrl: d + "/pin2.php",
			multiPopupUrl: d + "/pin3.php",
			//imgBase: d.replace("http:", "") + "/img/widgets",
			imgBase: "//huaban.com/img/widgets",
			analyticsUrl: d.replace("http:", "") + "/share_analytics.html?v=3",
			waterfallLimit: 600,
			md: "",
			via: 2,
			isMobileClient: !1,
			floatingButton: {
				inner: "采集",
				style: "inside",
				color: "red",
				size: "normal",
				position: "top left",
				withIcon: !1
			},
			maximumSelect: 100,
			showCallback: null,
			hideCallback: null
		},
		f = c.$("huaban_share_script"),
		g = ["style", "color", "size", "position"],
		h = e.floatingButton;
		if (f) {
			e.autoInitialize = !1,
			e.autoAttachFloatingButton = !0,
			h.withIcon = !0,
			c.$each(g,
			function(a) {
				var b = c.$data.get(f, a);
				b && (h[a] = b)
			});
			var i = c.$data.get(f, "image-min-size");
			i && (i = i.split(" "), e.imageMinWidth = Number(i[0]) || 0, e.imageMinHeight = Number(i[1]) || Number(i[0]) || 0),
			e.via = 3;
			var j = location && location.host && location.host.split(".") || [];
			"www" == j[0] && j.shift(),
			j.length && (e.md = j.join(".")),
			h.suspended && (h.style = {
				"in": "inside",
				out: "outside"
			} [h.suspended], delete h.suspended),
			h.size = {
				16 : "small",
				66 : "normal",
				108 : "large"
			} [h.size] || h.size || "normal",
			a.huaban_minWidth && (e.imageMinWidth = huaban_minWidth),
			a.huaban_minHeight && (e.imageMinHeight = huaban_minHeight),
			e.md = a.huaban_md || e.md || "",
			h.inner = "收藏",
			"large" == h.size && (h.inner = "收藏到花瓣");
			var k = c.$new("iframe", {
				src: e.analyticsUrl
			});
			c.$style.set(k, "display", "none"),
			c.$append(document.body, k)
		}
		a.HUABAN_PRESETTINGS && c.$merge(e, HUABAN_PRESETTINGS)
	} (window, HUABAN_GLOBAL),
	function(a) {
		var b = a.Qatrix,
		c = {
			"": 'font-family: "helvetica neue",arial,sans-serif; color: #444; font-size: 14px;',
			"*": "box-sizing: content-box;",
			".main": "position: fixed; left: 0; top: 0; width: 100%; height: 100%; background: #e5e5e5; background: rgba(229,229,229,.95); max-height: 100%; overflow: hidden; z-index: 9999999999999;",
			"a img": "border: 0;",
			".header": "height: 50px; background: white; box-shadow: 0 0 4px rgba(0,0,0,.2); width: 100%; left: 0; top: 0; position: absolute;",
			".header .inner": "margin: 0 auto; position: relative;",
			".header .close": "width: 60px; height: 50px; border-left: 1px solid #ddd; position: absolute; right: 0; top: 0; background: url({{imgBase}}/btn_close.png) 20px 14px no-repeat; cursor: pointer;",
			".header .close:hover": "background-position: 20px -26px;",
			".header .close:active": "background-position: 20px -66px;",
			".header .logo": "display: block; position: absolute; top: 12px;",
			".waterfall-holder": "position: relative; overflow-y: auto; height: 100%;",
			".waterfall": "position: relative; margin-top: 50px;",
			".waterfall .empty": "position: absolute; left: 50%; top: 30px; height: 36px; line-height: 36px; width: 216px; text-align: left; margin-left: -128px; color: #777; background: url({{imgBase}}/icon_notice.png) 12px 8px no-repeat white; padding-left: 40px; font-size: 15px;",
			".btn": "display: inline-block; border-radius: 2px; font-size: 14px; padding: 0 12px; height: 30px; line-height: 30px; cursor: pointer; text-decoration: none; white-space: nowrap; -moz-user-select: none; -webkit-user-select: none; user-select: none; text-align: center; background: #D53939; color: white;",
			".btn:hover": "background: #E54646;",
			".btn:active": "background: #C52424;",
			".wbtn": "background: #EDEDED; color: #444;",
			".wbtn:hover": "background: #F2F2F2;",
			".wbtn:active": "background: #DDD;",
			".f-button": "position: absolute; display: none; z-index: 9999999999998; box-shadow: 0 0 0 2px rgba(255,255,255,.2); background: #aaa; background: rgba(0,0,0,.3); color: white; cursor: pointer; padding: 0 12px; height: 30px; line-height: 30px; border-radius: 2px; font-size: 14px",
			".f-button:hover": "background-color: #999; background-color: rgba(0,0,0,.5);",
			".f-button:active": "background-color: rgba(0,0,0,.6);",
			".red-normal-icon-button": "width: 36px; height: 24px; border: 0px; line-height: 24px; padding-left: 24px; padding-right: 0px; text-align: left; background: url({{imgBase}}/widget_icons.png) 0 -200px no-repeat; box-shadow: none !important; font-size: 14px; background-color: transparent !important;",
			".red-normal-icon-button:hover": "background-position: -130px -200px;",
			".red-normal-icon-button:active": "background-position: -260px -200px;",
			".red-large-icon-button": "width: 80px; height: 24px; border: 0px; line-height: 24px; padding-left: 24px; padding-right: 0px; text-align: left; background: url({{imgBase}}/widget_icons.png) 0 -150px no-repeat; box-shadow: none !important; font-size: 14px; background-color: transparent !important;",
			".red-large-icon-button:hover": "background-position: -130px -150px;",
			".red-large-icon-button:active": "background-position: -260px -150px;",
			".red-small-icon-button": "width: 30px; height: 21px; border: 0px; line-height: 21px; padding-left: 20px; padding-right: 0px; text-align: left; background: url({{imgBase}}/widget_icons.png) 0 -250px no-repeat; box-shadow: none !important; font-size: 12px; background-color: transparent !important;",
			".red-small-icon-button:hover": "background-position: -130px -250px;",
			".red-small-icon-button:active": "background-position: -260px -250px;",
			".white-normal-icon-button": "width: 36px; height: 24px; border: 0px; line-height: 24px; padding-left: 24px; padding-right: 0px; text-align: left; background: url({{imgBase}}/widget_icons.png) 0 -500px no-repeat; box-shadow: none !important; color: #444; font-size: 14px; background-color: transparent !important;",
			".white-normal-icon-button:hover": "background-position: -130px -500px;",
			".white-normal-icon-button:active": "background-position: -260px -500px;",
			".white-large-icon-button": "width: 80px; height: 24px; border: 0px; line-height: 24px; padding-left: 24px; padding-right: 0px; text-align: left; background: url({{imgBase}}/widget_icons.png) 0 -450px no-repeat; box-shadow: none !important; color: #444; font-size: 14px; background-color: transparent !important;",
			".white-large-icon-button:hover": "background-position: -130px -450px;",
			".white-large-icon-button:active": "background-position: -260px -450px;",
			".white-small-icon-button": "width: 30px; height: 21px; border: 0px; line-height: 21px; padding-left: 20px; padding-right: 0px; text-align: left; background: url({{imgBase}}/widget_icons.png) 0 -550px no-repeat; box-shadow: none !important; color: #444; font-size: 12px; background-color: transparent !important;",
			".white-small-icon-button:hover": "background-position: -130px -550px;",
			".white-small-icon-button:active": "background-position: -260px -550px;",
			".cell": "width: 236px; position: absolute; background: white; box-shadow: 0 1px 3px rgba(0,0,0,.3); transition: left .3s ease-in-out, top .3s linear;",
			".cell .img-holder": "overflow: hidden; position: relative;",
			".cell .img-holder:hover img.cell-img": "opacity: .8",
			".cell .video-icon": "width: 72px; height: 62px; position: absolute; left: 50%; top: 50%; margin: -31px auto auto -36px; background: url({{imgBase}}/media_video.png) 0 0 no-repeat; display: none;",
			".cell.video .video-icon": "display: block;",
			".cell .over": "display: none;",
			".cell:hover .over": "display: block;",
			".cell .over .btn": "width: 60px; height: 34px; padding: 0; position: absolute; left: 50%; top: 50%; margin: -18px 0 0 -31px; line-height: 34px; box-shadow: 0 0 0 2px rgba(255,255,255,.2); font-size: 16px;",
			".cell.long .img-holder": "height: 600px;",
			".cell.long .img-holder:after": 'content: ""; display: block; position: absolute; width: 236px; height: 12px; left: 0; bottom: 0; background: url({{imgBase}}/long_image_shadow_2.png) repeat-x 4px top;',
			".cell img": "width: 236px; display: block;",
			".cell .size": "margin: 8px 16px; font-size: 12px; color: #999",
			".cell .description": "display: block; width: 202px; margin: 0 6px 6px; padding: 6px 10px; border: 0; resize: none; outline: 0; border: 1px solid transparent; line-height: 18px; font-size: 13px; overflow: hidden; word-wrap: break-word; background: url({{imgBase}}/icon_edit.png) 500px center no-repeat;",
			".cell:hover .description": "background-color: #fff9e0; background-position: right top;",
			".cell .description:focus": "background-color: #F9F9F9; background-position: 500px center;",
			".cell .select-btn": "width: 34px; height:34px; background: url({{imgBase}}/checkbox.png) 0 0 no-repeat; position: absolute; right: 5px; top: 5px; cursor: pointer;",
			".cell .pinned-label": "position: absolute; left: 0; top: 10px; height: 24px; line-height: 24px; padding: 0 10px; background: #CE0000; background: rgba(200, 0, 0, 0.9); color: white; font-size: 12px; display: none;",
			".cell.pinned .pinned-label": "display: block;",
			".selected .select-btn": "background-position: 0 -40px;",
			".multi .cell .img-holder": "cursor: pointer;",
			".multi .cell .cell-pin-btn": "display: none;",
			".multi .cell .over": "display: block;",
			".header .multi-buttons": "position: absolute; top: 10px; left: 0; display: none;",
			".header .multi-buttons .btn": "margin-right: 10px;",
			".header .multi-noti": "display: none; height: 50px; line-height: 50px; text-align: center; font-size: 16px; color: #999; font-weight: bold;",
			".header .multi-noti span": "font-weight: normal;",
			".header .multi-noti i": "font-style: normal;",
			".header .notice": "padding: 0 10px; height:30px; line-height: 30px; position: absolute; left: 50%; top: 10px; margin-left: -83px; background: #fff9e2; text-align: center;",
			".header .notice i": "display: inline-block; width: 18px; height: 18px; background: url({{imgBase}}/icon_notice.png) 0 0 no-repeat; vertical-align: top; margin: 6px 6px 0 0;",
			".switcher": "height: 50px; width: 100px; position: relative;",
			".switcher .title": "position: absolute; right: 75px; top: 13px; color: #999; white-space: nowrap; line-height: 24px; opacity: 0; visibility: hidden;",
			".switcher:hover .title": "visibility: visible; opacity: 1; -webkit-transition: opacity .2s linear; -webkit-transition-delay: .2s; transition: opacity .2s linear; transition-delay: .2s;",
			".switcher .bar": "width: 40px; height: 24px; background: #EB595F; border-radius: 12px; color: white; position: absolute; right: 0; top: 13px; cursor: pointer; font-size: 14px; -webkit-transition: all .2s linear; transition: all .2s linear;",
			".switcher:hover .bar": "width: 64px;",
			".switcher.on .bar": "background: #7DD100;",
			".switcher .bar .round": "width: 20px; height: 20px; background: white; border-radius: 50%; position: absolute; left: 2px; top: 2px; -webkit-transition: left .2s linear; box-shadow: 0px 0px 3px rgba(0,0,0,0.15); transition: left .2s linear; box-shadow: 0px 0px 3px rgba(0,0,0,0.15);",
			".switcher.on .bar .round": "left: 17px;",
			".switcher.on:hover .bar .round": "left: 41px;",
			".switcher .bar .text-1": "height: 24px; line-height: 24px; position: absolute; right:17px; top: 0; opacity: 0; visibility: hidden; -webkit-transition: all .2s linear; transition: all .2s linear;",
			".switcher:hover .bar .text-1": "right: 9px; opacity: 1; visibility: visible;",
			".switcher.on:hover .bar .text-1": "right: 17px; opacity: 0; visibility: hidden;",
			".switcher .bar .text-2": "height: 24px; line-height: 24px; position: absolute; left:17px; top: 0; opacity: 0; visibility: hidden; -webkit-transition: all .2s linear; transition: all .2s linear;",
			".switcher:hover .bar .text-2": "left: 17px; opacity: 0; visibility: hidden;",
			".switcher.on:hover .bar .text-2": "left: 9px; opacity: 1; visibility: visible;",
			".header .switcher": "position: absolute; right: 0; top: 0;"
		},
		d = {
			".red-normal-icon-button, .red-large-icon-button, .red-small-icon-button, .white-normal-icon-button, .white-large-icon-button, .white-small-icon-button": "{ background-image: url({{imgBase}}/widget_icons_ie6.png)"
		};
		a.styles = "";
		var e = a.settings.imgBase; (b.$browser.msie6 || b.$browser.msie7 || b.$browser.msie8) && (e = window.location.protocol + e);
		for (var f in c) c[f] = b.$string.replace(c[f], {
			"{{imgBase}}": e
		}),
		a.styles += "#" + a.settings.id + " " + f.replace(/\./g, "." + a.settings.prefix) + " {" + c[f] + "} ";
		a.styles += "<!--[if IE 6]>";
		for (var f in d) a.styles += "#" + a.settings.id + " " + f.replace(/\./g, "." + a.settings.prefix) + " " + d[f] + " ";
		a.styles += "<![endif]-->"
	} (HUABAN_GLOBAL),
	function(a) {
		{
			var b = a.Qatrix;
			a.settings.huabanUrl
		}
		a.templates = {
			main: ['<div class="main">', '<div class="waterfall-holder">', '<div class="waterfall">', "</div>", "</div>", '<div class="header">', '<div class="inner sync">', '<a class="logo" href="{{huabanUrl}}" target="_blank">', '<img src="{{imgBase}}/logo.png">', "</a>", '<div class="multi-noti">已选择 <b>1</b> 张<span>（最多 <i>10</i> 张）</span></div>', '<div class="multi-buttons">', '<div class="btn confirm">批量采集</div>', '<div class="btn wbtn cancel">取消</div>', "</div>", '<div class="notice" style="display: none"><i></i><span></span></div>', '<div class="switcher switch-order">', '<div class="title">图片排序</div>', '<div class="bar">', '<div class="text-1">推荐</div>', '<div class="text-2">自然</div>', '<div class="round"></div>', "</div>", "</div>", "</div>", '<div class="close", title="或按 ESC 键关闭">', "</div>", "</div>", "</div>"].join(""),
			"message-box": '<div id="HUABAN_MESSAGE" style="display: none"></div>',
			"waterfall-cell": ['<div class="cell">', '<div class="img-holder">', '<img src="{{imgUrl}}" class="cell-img" height="{{imgHeight}}"/>', '<div class="pinned-label">已采集</div>', '<div class="video-icon"></div>', '<div class="over">', '<div class="btn cell-pin-btn">采集</div>', '<div class="select-btn"></div>', "</div>", "</div>", '<div class="size">{{size.x}} x {{size.y}}</div>', '<textarea class="description">{{description}}</textarea>', "</div>"].join(""),
			"empty-alert": ['<div class="empty">没有找到足够大的图片/视频</div>'].join(""),
			"floating-button": ['<div class="f-button {{extraClass}}">', "{{inner}}", "</div>"].join("")
		};
		for (var c in a.templates) a.templates[c] = b.$string.replace(a.templates[c], {
			"{{imgBase}}": a.settings.imgBase,
			"{{huabanUrl}}": a.settings.huabanUrl
		});
		a.render = function(c, d) {
			d = d || {};
			var e = b.$template(a.templates[c], d),
			f = b.$new("div", {
				html: e
			}),
			g = f.childNodes[0];
			return b.$tag(f, "*",
			function(c) {
				c.className && (c.className = (a.settings.prefix + b.$string.trim(c.className)).replace(/\ /g, " " + a.settings.prefix))
			}),
			f.removeChild(g),
			g
		}
	} (HUABAN_GLOBAL),
	function(a) {
		var b = a.Qatrix,
		c = function(a) {
			return a.length ? (b.$each(a,
			function(a) {
				a.url = window.location.href
			}), a) : void 0
		};
		a.rules = {
			"^https?://image\\.baidu\\.com\\/channel": function(a) {
				return a.length ? (b.$each(a,
				function(a) {
					var c, d;
					c = b.$findParent(a.elem,
					function(a) {
						return b.$className.has(a, "inner_wrapper")
					}),
					c && (d = b.$class(c, "abs")[0], a.url = b.$attr.get(d, "href"), a.description = b.$text(d))
				}), a) : void 0
			},
			"^https?://image\\.baidu\\.com\\/i\\?": function(a) {
				if (a.length) {
					var c = b.$id("picInfoPnl")[0];
					return c ? b.$each(a,
					function(a) {
						var d = b.$parent(a.elem,
						function(a) {
							return b.$className.has(a, "img-container")
						});
						d && (a.description = b.$class(c, "pictitle")[0].innerText)
					}) : b.$each(a,
					function(a) {
						var c, d = b.$findParent(a.elem,
						function(a) {
							return b.$className.has(a, "pullImgli")
						});
						if (d) try {
							c = b.$class(d, "imgShow")[0],
							a.url = b.$tag(c, "a")[0].href,
							a.description = b.$class(d, "fromURL")[0].innerText
						} catch(e) {}
					}),
					a
				}
			},
			"^https?://(.*\\.)?weibo\\.com": function(a) {
				if (a.length) {
					var c = [];
					return b.$each(a,
					function(a) {
						if (!a.imgUrl.match(/img.t.sinajs.cn/)) {
							var d = b.$findParent(a.elem,
							function(a) {
								return b.$className.has(a, "WB_feed_expand") || b.$className.has(a, "WB_media_expand") || b.$className.has(a, "WB_feed_detail")
							});
							if (d) {
								var e = b.$class(d, "WB_from")[0];
								e && (a.url = b.$tag(e, "a")[0].href);
								var f = b.$class(d, "WB_text")[0];
								f || (d = b.$findParent(a.elem,
								function(a) {
									return b.$className.has(a, "WB_feed_datail")
								}), f = b.$class(d, "WB_text")[0]),
								f && (a.description = f.innerText)
							}
							c.push(a)
						}
					}),
					c
				}
			},
			"^https?://(.*\\.)?zcool\\.com\\.cn\\/gfx\\/.+": c,
			"^http://(.*\\.)?kmeitu\\.com": c,
			"^http://(.*\\.)?bcy\\.net": c,
			"^https?://(.*\\.)?zcool\\.com\\.cn": function(a) {
				if (a.length) {
					var c = b.$select(".camWholeBox")[0];
					if (c) b.$each(a,
					function(a) {
						var c = b.$parent(a.elem);
						c && (a.url = b.$attr.get(c, "href"))
					});
					else try {
						var d = b.$select(".workTitle")[0].innerText;
						b.$each(a,
						function(a) {
							a.description = d
						})
					} catch(e) {}
					return a
				}
			},
			"^https?://(www\\.)?500px\\.com": function(a) {
				return a.length ? (b.$each(a,
				function(a) {
					var c = b.$data.get(a.elem, "bind");
					if (c && "photo_img" === c) {
						var d = b.$parent(a.elem);
						d && (a.url = b.$attr.get(d, "href"));
						try {
							var e = b.$parent(d),
							f = b.$string.trim(b.$class(e, "title")[0].innerText) || "",
							g = b.$string.trim(b.$class(e, "info")[0].innerText) || ""; (f || g) && (a.description = f + " - " + g)
						} catch(h) {}
					}
				}), a) : void 0
			},
			"^https?://(www\\.)?pinterest\\.com": function(a) {
				return a.length ? (b.$each(a,
				function(a) {
					var c = b.$findParent(a.elem,
					function(a) {
						return b.$className.has(a, "pinImageWrapper")
					});
					c && (a.url = "http://www.pinterest.com/" + b.$attr.get(c, "href"))
				}), a) : void 0
			},
			"^https?://(www\\.)?behance\\.net": function(a) {
				return a.length ? (b.$each(a,
				function(a) {
					var c = b.$parent(b.$parent(a.elem));
					c && (a.url = b.$attr.get(c, "href"))
				}), a) : void 0
			},
			"^https?://movie\\.douban\\.com": function(a) {
				try {
					var c = b.$select("#mainpic .nbgnbg img")[0],
					d = c.src.replace("movie_poster_cover/spst", "photo/photo"),
					e = c.alt + " (来自豆瓣电影)";
					a.push({
						size: {
							x: 428,
							y: 600
						},
						imgUrl: d,
						description: e
					})
				} catch(f) {}
				return a
			},
			"^https?://(www\\.)?jianjuke\\.com": function(a) {
				return a.length ? (b.$each(a,
				function(a) {
					var c = b.$findParent(a.elem,
					function(a) {
						return b.$className.has(a, "post")
					});
					if (c) try {
						var d = b.$class(c, "entry-title")[0],
						e = b.$tag(d, "a")[0];
						a.url = b.$attr.get(e, "href"),
						a.description = b.$text(e)
					} catch(f) {}
				}), a) : void 0
			},
			"^https?://(www\\.)?duitang\\.com": function(a) {
				return a.length ? (b.$each(a,
				function(a) {
					if (window.location.href.match(/.*duitang\.com\/people\/mblog\/\d+\/detail/) && a.url && a.url.match(/http:\/\/www\.duitang\.com\/redirect\//)) {
						var c = b.$getQueryParams(a.url).to;
						c && (a.url = decodeURIComponent(c))
					} else try {
						var d = b.$parent(b.$parent(a.elem)),
						e = b.$tag(b.$class(d, "dt-woo-title")[0], "a")[0];
						a.description = b.$text(e)
					} catch(f) {}
				}), a) : void 0
			},
			"^http://www\\.58pic\\.com/": function(a) {
				return a.length ? (b.$each(a,
				function(a) {
					var c = b.$parent(a.elem);
					b.$className.has(c, "thumb-box") && (a.url = c.href)
				}), a) : void 0
			},
			"^http://(www\\.)?tobosu\\.com/pic": function(a) {
				return a.length ? (b.$each(a,
				function(a) {
					a.imgUrl = a.imgUrl.replace("/small/", "//")
				}), a) : void 0
			},
			"^https?://(www\\.)?houzz\\.com/photos(/[^0-9]+)*": function(a) {
				return a.length ? (b.$class(document.body, "browseListBody") && b.$each(a,
				function(a) {
					var c = b.$parent(b.$parent(a.elem));
					b.$className.has(c, "noHoverLink") && (a.url = b.$attr.get(c, "href"))
				}), a) : void 0
			},
			"^https?://(.*\\.)?lofter\\.com": function(a) {
				return a.length ? (b.$each(a,
				function(a) {
					var c = b.$parent(a.elem),
					d = b.$parent(c);
					c && c.tagName && "a" === c.tagName.toLowerCase() && c.href ? a.url = b.$attr.get(c, "href") : d && d.tagName && "a" === d.tagName.toLowerCase() && d.href && (a.url = b.$attr.get(d, "href"))
				}), a) : void 0
			},
			"^https?://zhan\\.renren\\.com": function(a) {
				return a.length ? (b.$each(a,
				function(a) {
					a.url && "javascript:;" !== a.url || (a.url = window.location.href)
				}), a) : void 0
			},
			"^http://(\\w+\\.)?rayli\\.com\\.cn/": function(a) {
				return a.length ? (b.$each(a,
				function(a) {
					a.imgUrl = a.imgUrl.replace("/thumb_(d+_)+/", "/")
				}), a) : void 0
			},
			"^http://fanjian8\\.com": function(a) {
				return a.length ? (b.$each(a,
				function(a) {
					var c = b.$parent(a.elem);
					b.$className.has(c, "single-content") && (a.url = "http://" + window.location.host + b.$attr.get(c, "rel") || "")
				}), a) : void 0
			},
			"^http://pic\\.gamespot\\.com\\.cn": function(a) {
				return a.length ? (b.$each(a,
				function(a) {
					a.imgUrl = a.imgUrl.replace(/_\d+\./, ".")
				}), a) : void 0
			},
			"^https?://(www\\.)diandian\\.com": function(a) {
				return a.length ? (b.$each(a,
				function(a) {
					var c = b.$findParent(a.elem,
					function(a) {
						return b.$className.has(a, "feed-content-holder")
					});
					if (c) try {
						var d = b.$class(c, "link-to-post")[0];
						a.url = b.$attr.get(d, "href")
					} catch(e) {}
				}), a) : void 0
			},
			"^https?://tuchong\\.com": function(a) {
				return a.length ? (b.$each(a,
				function(a) {
					a.imgUrl = a.imgUrl.replace(/\/(\d+)\/g\/(\d+)/, "/$1/f/$2")
				}), a) : void 0
			},
			"^http://(www\\.)zhuqu\\.com/": function(a) {
				return a.length ? (b.$each(a,
				function(a) {
					var c = b.$parent(a.elem),
					d = b.$class(c, "zq_mask")[0];
					d && (a.url = b.$attr.get(d, "href")),
					a.imgUrl = a.imgUrl.replace(/\/thb2\/\d+x\d+\//, "/water/")
				}), a) : void 0
			},
			"^http://(\\w+\\.)cnsc8\\.com/": function(a) {
				return a.length ? (b.$each(a,
				function(a) {
					a.imgUrl = a.imgUrl.replace(/\/Small_Pic\//, "/Big_Pic/")
				}), a) : void 0
			},
			"^http://(www\\.)35pic\\.com/": function(a) {
				return a.length ? (b.$each(a,
				function(a) {
					a.imgUrl = a.imgUrl.replace(/img\./, "pic.").replace(/_\d+\./, ".")
				}), a) : void 0
			},
			"^https?://(w+\\.)mogujie\\.com/": function(a) {
				return a.length ? (b.$each(a,
				function(a) {
					a.imgUrl = a.imgUrl.replace(/(_\d+x\d+\.(jpg|jpeg|png))(?:_\d+x\d+\.(jpg|jpeg|png))*/, "$1")
				}), a) : void 0
			},
			"^https?://(www\\.)youku\\.com/": function(a) {
				return a.length ? (b.$each(a,
				function(a) {
					try {
						var c = b.$parent(b.$parent(a.elem)),
						d = b.$tag(b.$class(c, "v-link")[0], "a")[0];
						a.url = b.$attr.get(d, "href")
					} catch(e) {}
				}), a) : void 0
			},
			"^https?://(www\\.)uehtml\\.com/": function(a) {
				return a.length ? (b.$each(a,
				function(a) {
					try {
						var c = b.$parent(b.$parent(a.elem));
						a.description = b.$text(b.$class(c, "citemtitle"))
					} catch(d) {}
				}), a) : void 0
			},
			"^http://(\\w+\\.)aili\\.com/": function(a) {
				return a.length ? (b.$each(a,
				function(a) {
					a.imgUrl = a.imgUrl.replace(/_\d+\.(jpg|jpeg|png)$/, ".$1")
				}), a) : void 0
			},
			"^http://pic\\.gamersky\\.com/": function(a) {
				return a.length ? (b.$each(a,
				function(a) {
					a.description = document.title
				}), a) : void 0
			},
			"^http://(www\\.)kujiale\\.com/": function(a) {
				return a.length ? (b.$each(a,
				function(a) {
					a.imgUrl = a.imgUrl.replace(/@!?\d+x\d+$/, "")
				}), a) : void 0
			},
			"^http://tupian\\.zol\\.com\\.cn/": function(a) {
				return a.length ? (b.$each(a,
				function(a) {
					try {
						var c = b.$parent(b.$parent(a.elem));
						a.description = b.$text(b.$class(c, "pic-infor"))
					} catch(d) {}
				}), a) : void 0
			},
			"^http://drawcrowd\\.com/": function(a) {
				return a.length ? (b.$each(a,
				function(a) {
					try {
						var c = b.$class(b.$parent(a.elem), "project-page")[0];
						a.url = "http://" + window.location.host + b.$attr.get(c, "href")
					} catch(d) {}
				}), a) : void 0
			},
			"^http://(www\\.)gtn9\\.com/": function(a) {
				return a.length ? (b.$each(a,
				function(a) {
					try {
						var c = b.$parent(b.$parent(b.$parent(a.elem))),
						d = b.$class(c, "comment_box")[0];
						a.description = b.$text(b.$tag(d, "li")[1])
					} catch(e) {}
				}), a) : void 0
			},
			"^http://topys\\.cn/": function(a) {
				return a.length ? (b.$each(a,
				function(a) {
					try {
						var c = b.$parent(b.$parent(b.$parent(a.elem)));
						a.description = b.$text(b.$class(c, "cont-text"))
					} catch(d) {}
				}), a) : void 0
			},
			"^http://dp\\.pconline\\.com\\.cn/": function(a) {
				return a.length ? (b.$each(a,
				function(a) {
					try {
						var c = b.$parent(b.$parent(b.$parent(a.elem)));
						a.description = b.$class(c, "cont-text")[0].innerText
					} catch(d) {}
				}), a) : void 0
			},
			"^http://(www\\.)gameui\\.cn/": function(a) {
				return a.length ? (b.$each(a,
				function(a) {
					var c = b.$parent(a.elem);
					a.description = b.$attr.get(c, "title")
				}), a) : void 0
			}
		},
		a.videoSiteRules = {
			"^https?://v.youku.com": function() {
				if (window.videoId2) {
					var c = b.$id("movie_player")[0];
					if (c) {
						var d = b.$new("param", {
							name: "wmode",
							value: "transparent"
						});
						b.$prepend(c, d),
						a.settings.hideCallback = function() {
							b.$remove(d)
						}
					}
					var e = "http://player.youku.com/player.php/sid/" + videoId2 + "/v.swf";
					return {
						imgUrl: a.settings.huabanUrl + "/pins/create/video/swf?url=" + encodeURIComponent(e),
						size: {
							x: 448,
							y: 252
						},
						video: e,
						elem: null,
						elemSize: null,
						type: "video"
					}
				}
			},
			"^https?://www.tudou.com": function() {
				if (window.itemData) {
					var a = "";
					try {
						b.$select("#shareWrap .iconfont")[0].click(),
						b.$select(".copy_share .flash")[0].click();
						var c = b.$("copyInput");
						a = c.value,
						b.$select("#shareWrap .iconfont")[0].click()
					} catch(d) {}
					if (a) return {
						imgUrl: itemData.pic,
						size: {
							x: 320,
							y: 240
						},
						video: a,
						elem: null,
						elemSize: null,
						description: itemData.kw || "",
						type: "video"
					}
				}
			},
			"^https?://www.bilibili.tv": function() {
				var a = b.$select("meta"),
				c = "",
				d = "";
				return b.$each(a,
				function(a) {
					"thumbnailUrl" == b.$attr.get(a, "itemprop") ? c = b.$attr.get(a, "content") : "embedURL" == b.$attr.get(a, "itemprop") && (d = b.$attr.get(a, "content"))
				}),
				{
					imgUrl: c,
					size: {
						x: 120,
						y: 90
					},
					video: d,
					elem: null,
					elemSize: null,
					type: "video"
				}
			},
			"^https?://www.acfun.com/v/": function() {
				if (window.system) {
					var a = system.preview,
					b = "http://static.acfun.com/player/ACFlashPlayer.out.swf?type=page&url=" + system.url;
					if (b && a) return {
						imgUrl: a,
						size: {
							x: 120,
							y: 90
						},
						video: b,
						elem: null,
						elemSize: null,
						type: "video"
					}
				}
			},
			"^https?://v.yinyuetai.com": function() {
				var b = a.collector.og;
				if (b) {
					var c = b.image,
					d = b.videosrc;
					if (d && c) return {
						imgUrl: c,
						size: {
							x: 640,
							y: 360
						},
						video: d,
						elem: null,
						elemSize: null,
						type: "video"
					}
				}
			},
			"^https?://(www|yule).iqiyi.com": function() {
				var c = b.$("flashbox");
				if (c) {
					var d = b.$attr.get(c, "data-player-videoId");
					return {
						imgUrl: a.settings.huabanUrl + "/pins/create/video/qiyi/?id=" + d,
						size: {
							x: 115,
							y: 77
						},
						video: "http://player.video.qiyi.com/" + d,
						elem: null,
						elemSize: null,
						type: "video"
					}
				}
			},
			"^https?://www.56.com/u": function() {
				var b = /v_(.+?)\.html/,
				c = b.exec(location.href);
				return c = c[1],
				{
					imgUrl: a.settings.huabanUrl + "/pins/create/video/56/?id=" + c,
					size: {
						x: 480,
						y: 405
					},
					video: "http://player.56.com/v_" + c + ".swf",
					elem: null,
					elemSize: null,
					type: "video"
				}
			},
			"^https?://v.ku6.com": function() {
				var a = "",
				b = "";
				try {
					a = App.VideoInfo.data.data.bigpicpath,
					b = App.VideoInfo.id
				} catch(c) {}
				if (a && b) return {
					imgUrl: a,
					size: {
						x: 480,
						y: 405
					},
					video: "http://player.ku6.com/refer/" + b + "/v.swf",
					elem: null,
					elemSize: null,
					type: "video"
				}
			},
			"^https?://v.pptv.com/show": function() {
				return {
					imgUrl: a.settings.huabanUrl + "/pins/create/video/pptv/?id=" + webcfg.id,
					size: {
						x: 120,
						y: 60
					},
					video: webcfg.player.playList[0].swf,
					elem: null,
					elemSize: null,
					type: "video"
				}
			},
			"^https?://v.qq.com/": function() {
				var a = "",
				c = "";
				try {
					var d = /vid=(.+)&/;
					a = b.$("textfield2").value,
					c = d.exec(a)[1]
				} catch(e) {}
				if (c && a) return {
					imgUrl: "http://vpic.video.qq.com/76082551/" + c + "_160_90_3.jpg",
					size: {
						x: 160,
						y: 90
					},
					video: a,
					elem: null,
					elemSize: null,
					type: "video"
				}
			}
		},
		a.embedVideoRules = {
			"(player.youku.com/player|www.tudou.com|player.ku6.com/refer|player.56.com|player.pptv.com/v)": function(b) {
				return {
					imgUrl: a.settings.huabanUrl + "/pins/create/video/swf?url=" + encodeURIComponent(b.url),
					size: {
						x: 448,
						y: 336
					},
					video: b.url,
					elem: b.elem,
					elemSize: a.collector.getElemSize(b.elem),
					type: "video"
				}
			},
			"share.vrs.sohu.com/my/v.swf.+?&id=(.+?)&autoplay=": function(b) {
				var c = /share.vrs.sohu.com\/my\/v.swf.+?&id=(.+?)&autoplay=/.exec(b.url)[1];
				return {
					imgUrl: a.settings.huabanUrl + "/pins/create/video/mysohu/?id=" + c,
					size: {
						x: 480,
						y: 360
					},
					video: b.url,
					elem: b.elem,
					elemSize: a.collector.getElemSize(b.elem),
					type: "video"
				}
			},
			"share.vrs.sohu.com/(.+?)/v.swf": function(b) {
				var c = /share\.vrs\.sohu\.com\/(.+?)\/v\.swf/.exec(b.url)[1];
				return {
					imgUrl: a.settings.huabanUrl + "/pins/create/video/sohu/?id=" + c,
					size: {
						x: 480,
						y: 360
					},
					video: b.url,
					elem: b.elem,
					elemSize: a.collector.getElemSize(b.elem),
					type: "video"
				}
			},
			"v.ifeng.com/include/exterior.swf": function(b) {
				var c = /guid=(.+?)&/,
				d = c.exec(b.url);
				return d ? (d = d[1], {
					imgUrl: a.settings.huabanUrl + "/pins/create/video/ifeng/?id=" + d,
					size: {
						x: 480,
						y: 360
					},
					video: b.url,
					elem: b.elem,
					elemSize: a.collector.getElemSize(b.elem),
					type: "video"
				}) : !1
			},
			"swf.ws.126.net/movieplayer": function(b) {
				var c = /-(vimg.+)-.swf?/,
				d = c.exec(b.url);
				return d ? (d = d[1].replace(/_/g, "."), {
					imgUrl: "http://" + d + ".jpg",
					size: {
						x: 480,
						y: 360
					},
					video: b.url,
					elem: b.elem,
					elemSize: a.collector.getElemSize(b.elem),
					type: "video"
				}) : !1
			},
			"swf.ws.126.net/v": function(b) {
				var c = /coverpic=\"(.+?)&/,
				d = c.exec(b.url);
				return d ? (d = d[1], {
					imgUrl: d,
					size: {
						x: 480,
						y: 360
					},
					video: b.url,
					elem: b.elem,
					elemSize: a.collector.getElemSize(b.elem),
					type: "video"
				}) : !1
			},
			"player.yinyuetai.com": function(b) {
				var c = /video\/swf\/(.+?)\//,
				d = c.exec(b.url);
				if (!d) return ! 1;
				d = d[1];
				var e = b.url.indexOf("video") > 0 ? a.settings.huabanUrl + "/pins/create/video/yinyuetai/?id=" + d: a.settings.huabanUrl + "/pins/create/video/yytList/?id=" + d;
				return {
					imgUrl: e,
					size: {
						x: 480,
						y: 360
					},
					video: b.url,
					elem: b.elem,
					elemSize: a.collector.getElemSize(b.elem),
					type: "video"
				}
			},
			"player.video.qiyi.com": function(b) {
				var c = /com\/(.+?)\//,
				d = c.exec(b.url);
				return d ? (d = d[1], {
					imgUrl: a.settings.huabanUrl + "/pins/create/video/qiyi/?id=" + d,
					size: {
						x: 115,
						y: 77
					},
					video: b.url,
					elem: b.elem,
					elemSize: a.collector.getElemSize(b.elem),
					type: "video"
				}) : !1
			},
			"www.jifenzhong.com/external": function(b) {
				var c = /v1.0\/(.+?)\/player/,
				d = c.exec(b.url);
				return d ? (d = d[1], {
					imgUrl: a.settings.huabanUrl + "/pins/create/video/jfz/?id=" + d,
					size: {
						x: 480,
						y: 305
					},
					video: b.url,
					elem: b.elem,
					elemSize: a.collector.getElemSize(b.elem),
					type: "video"
				}) : !1
			},
			"static.video.qq.com": function(b) {
				var c = /vid=(.+)(&|$)/,
				d = c.exec(b.url);
				return d ? {
					imgUrl: "http://vpic.video.qq.com/76082551/" + d[1] + "_160_90_3.jpg",
					size: {
						x: 160,
						y: 90
					},
					video: b.url,
					elem: b.elem,
					elemSize: a.collector.getElemSize(b.elem),
					type: "video"
				}: !1
			}
		}
	} (HUABAN_GLOBAL),
	function(a, b) {
		{
			var c = a.Qatrix;
			a.collector = {
				getImgUnits: function(a) {
					var d = [],
					e = b.images || [],
					f = this;
					return e.length ? (c.$each(e,
					function(a) {
						var b = f.buildImgUnit(a);
						b && d.push(b)
					}), void a(d)) : a([])
				},
				buildImgUnit: function(b) {
					if (c.$findParent(b,
					function(b) {
						return b.id == a.settings.id
					})) return null;
					if (!b.src) return null;
					var d = this.getImgNaturalSize(b);
					if (!d) return null;
					var e, f = "figure" == c.$parent(b).tagName.toLowerCase() ? c.$parent(b) : null,
					g = f && c.$tag(f, "figcaption").length ? c.$tag(f, "figcaption")[0].innerHTML: null,
					h = c.$parent(b),
					i = c.$attr.get(b, "alt") || g || c.$attr.get(b, "title") || "";
					return "a" === h.tagName.toLowerCase() && h.href && (i = i || c.$attr.get(h, "title") || "", h.href.match(/(^(#|javascript:))|((\.jpg|\.jpeg|\.png|\.gif)$)/) || (e = h.href)),
					{
						elem: b,
						url: e,
						imgUrl: b.src,
						size: d,
						elemSize: this.getElemSize(b),
						description: i,
						type: "img"
					}
				},
				getBgUnits: function(d) {
					var e = [],
					f = b.getElementsByTagName("*"),
					g = (f.length, this);
					c.$parallelEach(f,
					function(b, d) {
						var f = c.$style.get(b, "background-image"),
						h = g.getElemSize(b);
						return ! f || "none" == f || !~f.indexOf("http") || h.x < a.settings.elemMinWidth || h.y < a.settings.elemMinHeight ? d() : (f = g.getAbsoluteUrl(f.replace(/^url\(["']?/, "").replace(/["']?\)$/, "")), void g.getImgSize(f,
						function(a) {
							e.push({
								elem: b,
								imgUrl: f,
								size: a,
								elemSize: h,
								description: c.$attr.get(b, "title") || "",
								type: "bg"
							}),
							d()
						}))
					},
					function() {
						d(e)
					})
				},
				getOpenGraph: function() {
					for (var a = {},
					d = c.$tag(b, "meta"), e = 0; e < d.length; e++) {
						var f = c.$attr.get(d[e], "property");
						f && ~f.toLowerCase().indexOf("og:") && (a[c.$string.trim(f.toLowerCase().replace("og:", ""))] = c.$attr.get(d[e], "content"))
					}
					return a
				},
				getVideos: function(d) {
					var e = b.getElementsByTagName("object"),
					f = b.embeds,
					g = this,
					h = [],
					j = function(b, c) {
						var d = {
							url: b,
							elem: c
						};
						for (i in a.embedVideoRules) {
							var e = new RegExp(i);
							if (e.test(d.url)) {
								var f = a.embedVideoRules[i](d);
								f && h.push(f);
								break
							}
						}
					};
					f.length && c.$each(f,
					function(a) {
						j(a.src, a)
					}),
					e.length && c.$each(e,
					function(a) {
						var b = c.$attr.get(a, "data");
						b || c.$tag(a, "param",
						function(a) {
							var d = c.$attr.get(a, "name"); ("movie" == d || "src" == d) && (b = c.$attr.get(a, "value"))
						}),
						b && j(b, a)
					});
					for (i in a.videoSiteRules) {
						var k = new RegExp(i);
						if (k.test(location.href)) {
							var l = a.videoSiteRules[i]();
							l && h.push(l);
							break
						}
					}
					c.$parallelEach(h,
					function(a, b) {
						g.getImgSize(a.imgUrl,
						function(c) {
							c && (a.size = c),
							b()
						})
					},
					function() {
						d(h)
					})
				},
				getElemSize: function(a) {
					return {
						x: a.offsetWidth,
						y: a.offsetHeight
					}
				},
				getImgSize: function(b, c) {
					function d() {
						return g.complete || g.width ? {
							x: g.width,
							y: g.height
						}: null
					}
					function e() {
						if (new Date - f >= a.settings.getSizeTimeout) return c(null);
						var b = d();
						b ? c(b) : setTimeout(e, 100)
					}
					var f = new Date,
					g = new Image;
					g.src = b,
					setTimeout(e, 0)
				},
				getImgNaturalSize: function(a) {
					var b = {};
					if (a.naturalWidth) b = {
						x: a.naturalWidth,
						y: a.naturalHeight
					};
					else {
						var c = new Image;
						c.src = a.src,
						b = {
							x: c.width,
							y: c.height
						}
					}
					if (0 == b.x && 0 == b.y) {
						if (0 == a.width && 0 == a.height) return null;
						b = {
							x: a.width,
							y: a.height
						}
					}
					return b
				},
				getAbsoluteUrl: function(a) {
					var c = b.createElement("A");
					return c.href = a,
					a = c.href
				},
				buildUnits: function(b) {
					var d = this;
					c.$parallel({
						imgs: function(a) {
							d.getImgUnits(a)
						},
						bgs: function(a) {
							d.getBgUnits(a)
						},
						og: function(a) {
							var b = d.og = d.getOpenGraph();
							b.image && b.videosrc && b["video:type"] && ~b["video:type"].indexOf("flash") ? d.getImgSize(b.image,
							function(c) {
								a({
									imgUrl: b.image,
									size: c || {
										x: 640,
										y: 360
									},
									video: b.videosrc,
									elem: null,
									elemSize: null,
									type: "video"
								})
							}) : b.image ? d.getImgSize(b.image,
							function(c) {
								a({
									elem: null,
									imgUrl: b.image,
									size: c,
									elemSize: c,
									description: "",
									type: "og"
								})
							}) : a()
						},
						videos: function(a) {
							d.getVideos(a)
						}
					},
					function(c) {
						var e = c.imgs.concat(c.bgs);
						c.og && e.push(c.og),
						e = e.concat(c.videos),
						e = d.filter(e),
						e = d.handleUnitsByRule(e),
						e = d.sorting(e, a.settings.orderBy),
						e = d.fillDescriptions(e),
						d.units = e,
						b(e)
					})
				},
				fillDescriptions: function(c) {
					var d = b.title,
					e = a.ui && a.ui.getSelectedText();
					this.og || (this.og = this.getOpenGraph()),
					this.og.title && (d = this.og.title),
					this.og.description && (d ? d += " : " + this.og.description: d = this.og.description);
					for (var f = c.length - 1; f >= 0; f--) c[f].description = e || c[f].description || d;
					return c
				},
				handleUnitsByRule: function(b) {
					if (!a.rules) return b;
					for (i in a.rules) {
						var c = new RegExp(i);
						if (c.test(window.location.href)) {
							b = a.rules[i](b) || b;
							break
						}
					}
					return b
				},
				filter: function(b) {
					for (var d = [], e = function(b) {
						var e = !0,
						f = [b.size && b.size.x >= a.settings.imageMinWidth, b.size && b.size.y >= a.settings.imageMinHeight,
						function() {
							var a = !0;
							return c.$each(d,
							function(c) {
								b.imgUrl == c.imgUrl && (a = !1)
							}),
							a
						} ()];
						return "video" == b.type && (f[0] = !0, f[1] = !0),
						c.$each(f,
						function(a) {
							a || (e = !1)
						}),
						e
					},
					f = 0; f < b.length; f++) e(b[f]) && d.push(b[f]);
					return d
				},
				sorting: function(b, c) {
					for (var d = 0; d < b.length; d++) b[d].documentOrder = d;
					var e = a.settings.priority;
					b.sort(function(a, b) {
						var c = e[b.type] - e[a.type];
						return c ? c: b.size.x * b.size.y - a.size.x * a.size.y
					});
					for (var d = 0; d < b.length; d++) b[d].recommendOrder = d;
					return "document" == c && b.sort(function(a, b) {
						return a.documentOrder - b.documentOrder
					}),
					b
				},
				attachFloatingButton: function() {
					if (!this.floatingButton) {
						var d = this,
						e = a.settings.floatingButton,
						f = [e.color, e.size, "icon-button"].join("-"),
						g = this.floatingButton = a.render("floating-button", {
							inner: e.inner,
							extraClass: e.withIcon ? f: ""
						});
						a.ui.mainEl || a.ui.buildElements(),
						c.$prepend(a.ui.holderEl, g),
						c.$show(g);
						var h = this.getElemSize(g);
						c.$hide(g),
						this.mouseMoveEvent = function(b) {
							var e = b.target || b.srcElement;
							if (!c.$className.has(e, "HUABAN-f-button")) if ("img" == e.tagName.toLowerCase()) {
								var f = d.getImgNaturalSize(e),
								i = d.getElemSize(e);
								if (f.x < a.settings.imageMinWidth || f.y < a.settings.imageMinHeight || i.x < a.settings.elemMinWidth || i.y < a.settings.elemMinHeight) return c.$hide(g);
								var j = d.getElPosition(e);
								c.$css.set(g, d.calButtonPos(j, h)),
								c.$show(g),
								g.imgElem = e
							} else {
								if (c.$className.has(e, "bdimgshare-bg")) return;
								c.$hide(g)
							}
						},
						window.addEventListener ? b.body.addEventListener("mousemove", this.mouseMoveEvent) : b.body.attachEvent("onmousemove", this.mouseMoveEvent),
						c.$event.on(g, "click",
						function() {
							var b = d.buildImgUnit(this.imgElem);
							b = d.handleUnitsByRule([b])[0],
							b = d.fillDescriptions([b])[0],
							a.popup.single(b)
						})
					}
				},
				calButtonPos: function(b, c) {
					var d = 0,
					e = 0,
					f = a.settings.floatingButton;
					return "outside" == f.style ? (d = ~f.position.indexOf("bottom") ? b.bottom: b.top - c.y, e = ~f.position.indexOf("right") ? b.right - c.x: b.left) : (d = ~f.position.indexOf("bottom") ? b.bottom - c.y - 5 : b.top + 5, e = ~f.position.indexOf("right") ? b.right - c.x - 5 : b.left + 5),
					{
						left: e > 0 ? e + "px": 0,
						top: d > 0 ? d + "px": 0
					}
				},
				detachFloatingButton: function() {
					var a = this.floatingButton;
					a && (window.removeEventListener ? b.body.removeEventListener("mousemove", this.mouseMoveEvent) : b.body.detachEvent("onmousemove", this.mouseMoveEvent), a.parentNode && a.parentNode.removeChild(a), delete this.floatingButton)
				},
				getElPosition: function(a) {
					this.docEl || (this.docEl = b.compatMode && "CSS1Compat" != b.compatMode ? b.body: b.documentElement);
					var d = {
						x: window.pageXOffset || this.docEl.scrollLeft,
						y: window.pageYOffset || this.docEl.scrollTop
					},
					e = a.getBoundingClientRect(),
					f = "relative" === c.$css.get(a, "position"),
					g = 0,
					h = 0;
					f && (g = parseInt(c.$css.get(a, "top"), 10) || 0, h = parseInt(c.$css.get(a, "left"), 10) || 0);
					var i = 0,
					j = 0;
					return f && (i = parseInt(c.$css.get(a, "margin-top"), 10) || 0, i = 0 > i ? i: 0, j = parseInt(c.$css.get(a, "margin-left"), 10) || 0, j = 0 > j ? j: 0),
					{
						left: e.left + d.x - h - j,
						top: e.top + d.y - g - i,
						right: e.right + d.x,
						bottom: e.bottom + d.y
					}
				}
			}
		}
	} (HUABAN_GLOBAL, document),
	function(a) {
		var b = a.Qatrix;
		a.initWaterfall = function(a) {
			function c(a) {
				if (e.widthSync) {
					var c = e.widthSync;
					if (!a) {
						var d = f.offsetWidth || document.body.offsetWidth;
						a = Math.floor((d - 2 * e.sideSpace + e.cellSpace) / (e.cellWidth + e.cellSpace)),
						a > e.maxCols && (a = e.maxCols),
						a < e.minCols && (a = e.minCols)
					}
					b.$each(c,
					function(c) {
						b.$css.set(c, "width", a * e.cellWidth + (a - 1) * e.cellSpace)
					})
				}
			}
			var d = {},
			e = {
				container: ".waterfall",
				widthSync: "",
				cellWidth: 236,
				cellSpace: 15,
				minCols: 1,
				maxCols: 6,
				cellClass: "cell",
				sideSpace: 15,
				topSpace: 15
			};
			e = b.$merge(e, a);
			var f = e.container;
			"string" == typeof f && (f = b.$select(f)[0]),
			c(),
			d.cells = b.$class(f, e.cellClass),
			d.reposition = function() {
				var a = f.offsetWidth,
				g = Math.floor((a - 2 * e.sideSpace + e.cellSpace) / (e.cellWidth + e.cellSpace));
				g > e.maxCols && (g = e.maxCols),
				g < e.minCols && (g = e.minCols);
				var h = a - 2 * e.sideSpace - g * (e.cellWidth + e.cellSpace) + e.cellSpace;
				c(g);
				var i = d.cells = b.$class(f, e.cellClass);
				if (i && i.length) {
					for (var j = [], k = 0; g > k; k++) j.push(e.topSpace);
					b.$each(i,
					function(a) {
						for (var c = 0,
						d = 0; g > d; d++) j[d] < j[c] && (c = d);
						var f = c * (e.cellWidth + e.cellSpace) + e.sideSpace + Math.floor(h / 2),
						i = j[c];
						b.$pos(a, f, i);
						var k = a.offsetHeight;
						j[c] += k + e.cellSpace
					}),
					b.$css.set(f, "height", Math.max.apply(Math, j) + 50)
				}
			},
			window.addEventListener ? window.addEventListener("resize",
			function() {
				d.reposition()
			}) : window.attachEvent("onresize",
			function() {
				d.reposition()
			}),
			f.waterfall = d
		}
	} (HUABAN_GLOBAL),
	function(a, b) {
		var c = (a.Qatrix,
		function(a) {
			return a.replace(/#+$/, "")
		});
		a.popup = {
			single: function(d, e) {
				HUABAN_GLOBAL.Qatrix.$ajax({
					url: PIN_URL_BASE + '/pin.php',
					data: {
						url: c(d.url || location.href),
						title: document.title,
						img: d.imgUrl,
						w: d.size.x,
						h: d.size.y,
						text: d.description
					},
					success: function(){
						HUABAN_GLOBAL.collector.units[i].posting = 2;
					},
					error: function(){
						HUABAN_GLOBAL.collector.units[i].posting = 0;
					}
				});
				return;//2015/5/30 cqiu
				var f = [a.settings.popupUrl + "?"],
				g = d.imgUrl;
				g.match(/^data:image\//i) && (g = "base64image");
				var h = {
					media: g,
					w: d.size.x,
					h: d.size.y,
					description: d.description,
					url: c(d.url || location.href)
				};
				"video" == d.type && (h.video = d.video, h.media_type = 1);
				for (var i in h) f.push(encodeURIComponent(i)),
				f.push("="),
				f.push(encodeURIComponent(h[i])),
				f.push("&");
				a.settings.via && f.push("via=" + encodeURIComponent(a.settings.via) + "&"),
				a.settings.md && f.push("md=" + encodeURIComponent(a.settings.md)),
				f = f.join(""),
				this.exportUnits = [{
					imgUrl: d.imgUrl
				}];
				var j = "status=no,resizable=no,scrollbars=yes,personalbar=no,directories=no,location=no,toolbar=no,menubar=no,width=632,height=320,left=0,top=0";
				e ? a.ui.setMessage({
					url: f,
					features: j
				}) : this.popupWindow = b.open(f, "", j)
			},
			multi: function(d) {
				for (var e = [], f = 0; f < d.length; f++) {
					var g = {};
					e.push(g);
					for (key in d[f])"elem" != key && (g[key] = d[f][key]);
					g.url = c(g.url || location.href)
				}
				this.exportUnits = e;

				var postData = [], _;
				for (var i=0, n = this.exportUnits.length; i<n; i++){
					_ = this.exportUnits[i];
					postData.push({
						url: _.url,
						title: document.title,
						img: _.imgUrl,
						w: _.size.x,
						h: _.size.y,
						text: _.description
					});
				}
				HUABAN_GLOBAL.Qatrix.$ajax({
					url: PIN_URL_BASE + '/pinx.php',
					data: postData,
					success: function(){
						HUABAN_GLOBAL.collector.units[i].posting = 2;
					},
					error: function(){
						HUABAN_GLOBAL.collector.units[i].posting = 0;
					}
				});
				return;

				var h = "status=no,resizable=no,scrollbars=yes,personalbar=no,directories=no,location=no,toolbar=no,menubar=no,width=500,height=350,left=0,top=0";
				this.popupWindow = b.open(a.settings.multiPopupUrl, "", h);

				//this.sendExportUnits();
			},
			sendExportUnits: function() {
				if (this.exportUnits && this.exportUnits.length) {
					var c = JSON.stringify(this.exportUnits);
					/http:\/\/photo\.poco\.cn/.test(b.location.href) && (c = JSON.parse(c)),
					this.popupWindow.postMessage(c, a.settings.huabanUrl)
				}
			}
		}
	} (HUABAN_GLOBAL, window),
	function(a, b) {
		var c = a.Qatrix;
		a.ui = {
			buildElements: function() {
				var d = this,
				e = this.mainEl = a.render("main"),
				f = this.holderEl = c.$new("div", {
					id: a.settings.id
				}),
				g = this.waterfallEl = c.$class(e, "HUABAN-waterfall")[0],
				h = c.$new("style"),
				i = c.$class(e, "HUABAN-close")[0],
				j = c.$class(e, "HUABAN-sync"),
				k = c.$class(e, "HUABAN-multi-buttons")[0],
				l = c.$class(k, "HUABAN-confirm")[0],
				m = c.$class(k, "HUABAN-cancel")[0],
				n = this.multiNoti = c.$class(e, "HUABAN-multi-noti")[0],
				o = c.$tag(n, "b")[0],
				p = c.$tag(n, "i")[0],
				q = (this.noticeEl = c.$class(e, "HUABAN-notice")[0], c.$class(e, "HUABAN-switch-order")[0]);
				p.innerHTML = a.settings.maximumSelect,
				c.$append(f, h),
				c.$append(document.body, f),
				h.styleSheet ? h.styleSheet.cssText = a.styles: h.innerHTML = a.styles,
				a.initWaterfall({
					container: g,
					widthSync: j,
					cellWidth: 236,
					cellSpace: 15,
					minCols: 1,
					maxCols: 6,
					cellClass: a.settings.prefix + "cell",
					sideSpace: 60,
					topSpace: 16
				}),
				c.$event.on(e, "click", ".HUABAN-img-holder", {},
				function(b) {
					if (!c.$className.has(b.target, "HUABAN-select-btn")) {
						var e = c.$findParent(this,
						function(a) {
							return c.$className.has(a, "HUABAN-cell")
						});
						return d.isInMultiMode ? c.$class(e, "HUABAN-select-btn")[0].click() : void a.popup.single(e.unit)
					}
				}),
				c.$event.on(e, "click", ".HUABAN-select-btn", {},
				function() {
					var b = c.$findParent(this,
					function(a) {
						return c.$className.has(a, "HUABAN-cell")
					});
					c.$className.has(b, "HUABAN-selected") ? (c.$className.remove(b, "HUABAN-selected"), d.hideNotice()) : c.$class(e, "HUABAN-selected").length >= a.settings.maximumSelect ? d.showNotice("一次最多选择 " + a.settings.maximumSelect + " 张图片") : c.$className.add(b, "HUABAN-selected");
					var f = c.$class(e, "HUABAN-selected");
					d.isInMultiMode || d.activeMultiMode(),
					o.innerHTML = f.length,
					0 == f.length && d.quitMultiMode()
				}),
				c.$event.on(l, "click",
				function() {
					var b = [];
					c.$class(e, "HUABAN-selected",
					function(a) {
						b.push(a.unit)
					}),
					a.popup.multi(b),
					d.quitMultiMode()
				}),
				c.$event.on(m, "click",
				function() {
					d.quitMultiMode()
				}),
				c.$event.on(i, "click",
				function() {
					d.hide()
				}),
				c.$event.on(q, "click",
				function() {
					c.$className.has(this, "HUABAN-on") ? (c.$className.remove(this, "HUABAN-on"), a.settings.orderBy = "recommend", d.reOrder("recommend")) : (c.$className.add(this, "HUABAN-on"), a.settings.orderBy = "document", d.reOrder("document"))
				}),
				c.$event.on(document.body, "keydown",
				function(a) {
					if (d.isShowing && 27 == a.originalEvent.keyCode) {
						var e = b.document.activeElement;
						return "input" == e.tagName || "textarea" == e.tagName || c.$attr.get(e, "contenteditable") ? a.originalEvent.target.blur() : void d.hide()
					}
				})
			},
			buildCells: function(d) {
				var e = this;
				d.length && c.$each(d,
				function(d) {
					d.imgHeight = 236 / d.size.x * d.size.y;
					var f = a.render("waterfall-cell", d);
					if (f.unit = d, e.pinnedUrls) for (var g = e.pinnedUrls.length - 1; g >= 0; g--) e.pinnedUrls[g] == d.imgUrl && c.$className.add(f, "HUABAN-pinned");
					if (d.imgHeight > a.settings.waterfallLimit && c.$className.add(f, "HUABAN-long"), "video" == d.type && c.$className.add(f, "HUABAN-video"), c.$append(e.waterfallEl, f), !c.$browser.msie6 && !c.$browser.msie7 && !c.$browser.msie8) {
						var h = c.$class(f, "HUABAN-description")[0],
						i = function() {
							c.$style.set(h, "height", "auto"),
							c.$style.set(h, "height", h.scrollHeight - 12 + "px")
						},
						j = function() {
							b.setTimeout(i, 0)
						};
						c.$event.on(h, "change", i),
						c.$event.on(h, "cut", j),
						c.$event.on(h, "paste", j),
						c.$event.on(h, "drop", j),
						c.$event.on(h, "keydown", j),
						i()
					}
					if (c.$browser.msie6) {
						var k = c.$class(f, "HUABAN-over")[0],
						l = c.$class(f, "HUABAN-img-holder")[0];
						c.$style.set(k, "height", l.offsetHeight + "px"),
						c.$style.set(document.body, "height", "100%"),
						c.$event.on(f, "mouseover",
						function() {
							c.$show(k)
						}),
						c.$event.on(f, "mouseout",
						function() {
							c.$hide(k)
						})
					}
					e.initTextEl(c.$class(f, "HUABAN-description")[0])
				})
			},
			setMessage: function(b) {
				a.messageBox || (a.messageBox = a.render("message-box"), c.$append(document.body, a.messageBox)),
				"string" != typeof b && (b = JSON.stringify(b)),
				a.messageBox.innerHTML = b
			},
			initTextEl: function(b) {
				function d() {
					e.unit.description = b.value,
					a.ui.waterfallEl.waterfall.reposition()
				}
				var e = c.$findParent(b,
				function(a) {
					return c.$className.has(a, "HUABAN-cell")
				});
				c.$event.on(b, {
					keyup: d,
					blur: d
				})
			},
			show: function() {
				if (!this.checkPermission()) {
					var b = this;
					if (this.mainEl || this.buildElements(), !this.isShowing) {
						if (c.$empty(this.waterfallEl), c.$append(this.holderEl, this.mainEl), a.collector.buildUnits(function(d) {
							d && d.length ? b.buildCells(d) : c.$append(b.waterfallEl, a.render("empty-alert")),
							b.waterfallEl.waterfall.reposition()
						}), c.$browser.msie6) {
							var d = document.documentElement && document.documentElement.scrollTop || document.body.scrollTop;
							c.$css.set(this.mainEl, "position", "absolute"),
							c.$css.set(this.mainEl, "top", d + "px")
						}
						c.$css.set(document.documentElement, "overflow", "hidden"),
						this.isShowing = !0,
						a.settings.showCallback && a.settings.showCallback()
					}
				}
			},
			hide: function() {
				this.mainEl && this.mainEl.parentNode && this.mainEl.parentNode.removeChild(this.mainEl),
				c.$css.set(document.documentElement, "overflow", "auto"),
				this.quitMultiMode(),
				this.isShowing = !1,
				a.settings.hideCallback && a.settings.hideCallback()
			},
			checkPermission: function() {
					//return true;
				if (location.href.match(/^https?:\/\/huabanxx.com/)) {
					var a = "你就在花瓣本站呢，可以直接采集本站图片。";
					return b.app && app.error ? app.error(a) : alert(a),
					!0
				}
			},
			activeMultiMode: function() {
				this.isInMultiMode || (this.isInMultiMode = !0, c.$className.add(this.mainEl, "HUABAN-multi"), c.$show(c.$class(this.mainEl, "HUABAN-multi-buttons")[0]), c.$hide(c.$class(this.mainEl, "HUABAN-logo")[0]), c.$show(this.multiNoti))
			},
			quitMultiMode: function() {
				this.isInMultiMode && (this.isInMultiMode = !1, c.$className.remove(this.mainEl, "HUABAN-multi"), c.$hide(c.$class(this.mainEl, "HUABAN-multi-buttons")[0]), c.$show(c.$class(this.mainEl, "HUABAN-logo")[0]), c.$hide(this.multiNoti), c.$class(this.mainEl, "HUABAN-selected",
				function(a) {
					c.$className.remove(a, "HUABAN-selected")
				}))
			},
			getSelectedText: function() {
				return ("" + (b.getSelection ? b.getSelection() : document.getSelection ? document.getSelection() : document.selection.createRange().text)).replace(/(^\s+|\s+$)/g, "")
			},
			pinComplete: function(a) {
				this.pinnedUrls = this.pinnedUrls || [],
				this.pinnedUrls.push(a),
				this.waterfallEl && c.$class(this.waterfallEl, "HUABAN-cell",
				function(b) {
					b.unit.imgUrl == a && c.$className.add(b, "HUABAN-pinned")
				})
			},
			showNotice: function(a) {
				c.$tag(this.noticeEl, "span")[0].innerHTML = a,
				c.$show(this.noticeEl),
				c.$animate(this.noticeEl, {
					opacity: {
						from: .5,
						to: 1
					}
				},
				500),
				this.multiNoti && c.$style.set(this.multiNoti, "visibility", "hidden");
				var b = this;
				this.noticeTimeout && clearTimeout(this.noticeTimeout),
				this.noticeTimeout = setTimeout(function() {
					b.hideNotice()
				},
				4e3)
			},
			hideNotice: function() {
				this.multiNoti && c.$style.set(this.multiNoti, "visibility", "visible"),
				c.$hide(this.noticeEl)
			},
			reOrder: function(a) {
				var b = this.waterfallEl.waterfall,
				d = b.cells,
				a = {
					document: "documentOrder",
					recommend: "recommendOrder"
				} [a];
				d.sort(function(b, c) {
					return c.unit[a] - b.unit[a]
				});
				for (var e = d.length - 1; e >= 0; e--) c.$append(this.waterfallEl, d[e]);
				b.reposition()
			}
		}
	} (HUABAN_GLOBAL, window),
	function(a, b) {
		b.__huaban = {
			_loaded: !0,
			showValidImages: function() {
				a.ui.show()
			}
		}
	} (HUABAN_GLOBAL, document),
	function(a) {
		var b = a.Qatrix;
		a.interface = {
			show: function() {
				a.ui.show()
			},
			hide: function() {
				a.ui.hide()
			},
			pinImage: function(c, d) {
				var e = "figure" == b.$parent(c).tagName.toLowerCase() ? b.$parent(c) : null,
				f = e && b.$tag(e, "figcaption").length ? b.$tag(e, "figcaption")[0].innerHTML: null,
				g = {
					elem: c,
					imgUrl: c.src,
					size: a.collector.getImgNaturalSize(c),
					elemSize: a.collector.getElemSize(c),
					description: b.$attr.get(c, "alt") || f || b.$attr.get(c, "title") || "",
					type: "img"
				},
				h = a.collector.handleUnitsByRule([g]);
				h && h.length && (g = h[0]),
				a.collector.fillDescriptions([g]),
				a.popup.single(g, d)
			},
			pinImageUrl: function(a) {
				for (var b = document.images,
				c = 0; c < b.length; c++) if (b[c].src == a) {
					this.pinImage(b[c], !0);
					break
				}
			},
			attachFloatingButton: function() {
				a.collector.attachFloatingButton()
			},
			detachFloatingButton: function() {
				a.collector.detachFloatingButton()
			},
			outputUnits: function() {
				a.collector.buildUnits(function(a) {
					for (var c = a.length - 1; c >= 0; c--) delete a[c].elem;
					window.HBiOSClientJSONString = JSON.stringify(a),
					b.$append(document.body, b.$new("iframe", {
						datatest: "test",
						src: "http://HUABAN_CALLBACK/",
						style: "visibility: hidden"
					}))
				})
			}
		}
	} (HUABAN_GLOBAL),
	function(a) {
		a.Qatrix;
		a.initialize = function() {
			a.ui.checkPermission() || (a.ui.show(), a.collector.attachFloatingButton())
		},
		a.settings.isMobileClient ? a.interface.outputUnits() : a.settings.autoInitialize ? a.initialize() : a.settings.autoAttachFloatingButton && a.collector.attachFloatingButton();
		var b = window.addEventListener ? window.addEventListener: window.attachEvent;
		b("message",
		function(b) {
			if ("multiUnits" == b.data) a.popup.sendExportUnits();
			else if ("singleUnit" == b.data) a.popup.sendExportUnits();
			else if ("singlePinComplete" == b.data) a.ui.hide();
			else if (~b.data.indexOf("complete:")) {
				var c = b.data.replace("complete:", "");
				a.ui.pinComplete(c)
			}
		})
	} (HUABAN_GLOBAL),
	function(a) {
		var b = a.Qatrix;
		b.$className.add(document.documentElement, "hb-loaded")
	} (HUABAN_GLOBAL, window))
} ();
