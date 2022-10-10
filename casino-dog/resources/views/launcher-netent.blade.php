<html lang="en"><head>
  <meta charset="utf-8">
  <title>NetEnt Game Loader</title>
  <meta name="description" content="NetEnt Game">
  <meta name="author" content="NetEnt">
        <script>
      var netent_netentextend = function() {
              var n;
              return n = function(n, e, t) {
                  var o = this;
                  e = e || {}, o.hide = function() {
                      n.style.visibility = "hidden"
                  }, o.show = function() {
                      n.style.visibility = "visible"
                  }, o.resize = function(t, o) {
                      var i;
                      if (void 0 === t || void 0 === o) throw new netent_error_handling.GiError(1, "netent_netentextend.main", "resize", "resize");
                      i = netent_tools.resize(e.defaultWidth, e.defaultHeight, t, o, e.enforceRatio), n.style.width = i.width, n.style.height = i.height
                  }, o.addEventListener = function(n, e) {
                      void 0 === t[n] && (t[n] = [], o.sendSubscriptionLog(n)), t[n].push(e)
                  }, o.removeEventListener = function(n, e) {
                      var o, i = t[n];
                      void 0 !== i && (o = i.indexOf(e), o >= 0 && i.splice(o, 1))
                  }
              }, n.prototype.get = function(n, e) {}, n.prototype.set = function(n, e, t) {}, n.prototype.call = function(n, e, t) {}, n.prototype.post = function(n, e, t, o, i) {}, n.prototype.sendSubscriptionLog = function(n) {}, {
                  Base: n
              }
          }(),
          netent_nee_html_embed = function() {
              var n = function(n, e) {
                  var t, o, i, r, a, s, c, l, u, g, f, d, p = 0,
                      h = {},
                      m = "subscriptionLog",
                      _ = !1,
                      v = [],
                      y = function() {};
                  t = {}, f = function(n) {
                      var e = Array.prototype.slice.call(arguments, 1);
                      n.postMessage.apply(n, e)
                  }, netent_netentextend.Base.call(this, n, e, h), s = function(n) {
                      _ && console.warn("sendSubscriptionLog something went wrong: ", n)
                  }, a = function(n) {
                      _ && console.log("sendSubscriptionLog success: ", n)
                  }, r = function(n) {
                      var e, t = Array.prototype.slice.call(arguments, 1),
                          o = h[n];
                      if (void 0 !== o)
                          for (e = 0; e < o.length; e++) o[e].apply(null, t)
                  }, l = function(n) {
                      var e = t[n];
                      return e ? (clearTimeout(e.timeout), delete t[n]) : e = {
                          success: y,
                          error: y
                      }, e
                  }, u = function(n) {
                      var e = l(n),
                          t = e.success,
                          o = Array.prototype.slice.call(arguments, 1);
                      t.apply(null, o)
                  }, g = function(n, e, t) {
                      var o = l(n),
                          i = o.error;
                      i(new netent_error_handling.GiError(e, "netent_nee_html_embed", t || ""))
                  }, i = function(n) {
                      var e, t;
                      netent_validation.validateMessage(n) && (e = n.data[0], t = n.data.slice(1), "success" === e ? u.apply(null, t) : "error" === e ? g.apply(null, t) : "event" === e && (d = !0, c(), r.apply(null, t)))
                  }, o = function(e, o, i, r, a) {
                      p += 1, t[p] = {
                          success: r || y,
                          error: a,
                          timeout: setTimeout(function(n) {
                              return function() {
                                  g(n, 11)
                              }
                          }(p), 1e3)
                      }, f(n.contentWindow, [e, p, o].concat(i), "*")
                  }, c = function() {
                      v.length && (v.forEach(function(n) {
                          o(m, n, [], a.bind(null, n), s)
                      }), v.length = 0)
                  }, this.get = function(n, e, t) {
                      var i = this,
                          r = netent_error_handling.handler(t);
                      if ("function" != typeof e && "function" != typeof t) return new Promise(function(e, t) {
                          i.get(n, e, t)
                      });
                      try {
                          netent_netentextend.Base.prototype.get.call(this, n, e, r), o("get", n, [], e, r)
                      } catch (a) {
                          r(a)
                      }
                  }, this.sendSubscriptionLog = function(n) {
                      try {
                          d ? o(m, n, [], a.bind(null, n), s) : v.push(n)
                      } catch (e) {
                          s(e)
                      }
                  }, this.set = function(n, e, t, i) {
                      var r = this,
                          a = netent_error_handling.handler(i);
                      if ("function" != typeof t && "function" != typeof i) return new Promise(function(t, o) {
                          r.set(n, e, t, o)
                      });
                      try {
                          netent_netentextend.Base.prototype.set.call(this, n, e, t, a), o("set", n, [e], t, a)
                      } catch (s) {
                          a(s)
                      }
                  }, this.call = function(n, e, t, i) {
                      var r = this,
                          a = netent_error_handling.handler(i);
                      if ("function" != typeof t && "function" != typeof i) return new Promise(function(t, o) {
                          r.call(n, e, t, o)
                      });
                      try {
                          o("call", n, e, t, a)
                      } catch (s) {
                          a(s)
                      }
                  }, window.removeEventListener("message", window.neeOnMessage), window.neeOnMessage = i, window.addEventListener("message", i)
              };
              return netent_netentextend.Html = n, {
                  Html: n
              }
          }(),
          netent_config_handling = function() {
              var n = {
                      targetElement: "neGameClient"
                  },
                  e = {
                      gameId: "string",
                      gameName: "string",
                      sessionId: "string",
                      staticServer: "string",
                      gameServerURL: "string",
                      giLocation: "string",
                      width: "string",
                      height: "string",
                      enforceRatio: "boolean",
                      targetElement: "string",
                      walletMode: "string",
                      currency: "string",
                      operatorId: "string",
                      liveCasinoParams: {
                          casinoId: "string"
                      }
                  },
                  t = [{
                      from: "gameServer",
                      to: "gameServerURL"
                  }, {
                      from: "historyUrl",
                      to: "historyURL"
                  }, {
                      from: "pluginUrl",
                      to: "pluginURL"
                  }, {
                      from: "lobbyUrl",
                      to: "lobbyURL"
                  }, {
                      from: "staticServerURL",
                      to: "staticServer"
                  }, {
                      from: "helpUrl",
                      to: "helpURL"
                  }],
                  o = function(n) {
                      t.forEach(function(e) {
                          n.hasOwnProperty(e.from) && !n.hasOwnProperty(e.to) && (n[e.to] = n[e.from]), delete n[e.from]
                      }), Object.keys(n).forEach(function(e) {
                          "object" == typeof n[e] && Object.keys(n[e]).length > 0 && o(n[e])
                      })
                  },
                  i = function(n) {
                      var e;
                      for (e in n) "object" == typeof n[e] && null !== n[e] ? (n[e] = i(n[e]), 0 === Object.keys(n[e]).length && delete n[e]) : (null === n[e] || "undefined" == typeof n[e] || "string" == typeof n[e] && !n[e]) && "gameRulesURL" !== e && delete n[e];
                      return n
                  },
                  r = function(n, e) {
                      i(n), o(n), n.giLocation && (n.giLocation = n.giLocation.replace(/\/?$/, "/")), n.staticServer && (n.staticServer = n.staticServer.replace(/\/?$/, "/")), n.gameServerURL && (n.gameServerURL = n.gameServerURL.replace(/\/?$/, "/")), e(n)
                  };
              return {
                  essentialParameters: e,
                  handleConfig: r,
                  defaultValues: n,
                  filterRedundantParameters: i
              }
          }(),
          netent_error_handling = function() {
              var n = function(n, e, t, o) {
                      var i = this;
                      return "number" != typeof n ? (i.code = 0, i.error = n, i.message = netent_errors[0].replace("<error>", n), i.causedBy = t, void(i.origin = e)) : (i.code = n, i.message = netent_errors[n], i.origin = e, i.causedBy = t, void(o && (Object.getOwnPropertyNames(o).forEach(function(n) {
                          i.message ? i.message = i.message.replace("<" + n + ">", o[n]) : i.message = o.error
                      }, i), i.variables = o)))
                  },
                  e = function(e) {
                      return function(t, o, i, r) {
                          var a;
                          e && (a = t instanceof n ? t : t instanceof TypeError || t instanceof ReferenceError ? new n(21, o, t) : new n(t, o, i, r), netent_logging_handling.log(a), e(a))
                      }
                  };
              return {
                  GiError: n,
                  handler: e
              }
          }(),
          netent_errors = function() {
              return {
                  0: "Unknown error: '<error>'",
                  1: "Value for '<key>' is invalid, expects to be <type>",
                  4: "Could not retrieve game configuration",
                  9: "This functionality is not supported by this game",
                  10: "Wrong number of arguments",
                  11: "No answer from game",
                  13: "SWFObject could not launch game",
                  14: "Could not load SWFObject",
                  16: "Target element '<value>' does not exist",
                  17: "No value provided for essential parameter '<parameter>'",
                  18: "Unable to launch HTML game",
                  21: "This browser is not supported",
                  23: "Could not open '<url>'.",
                  24: "Could not init module '<module>'.",
                  25: "Could not load module '<module>'.",
                  26: "Height or width in percentage is not supported when enforceRatio=true."
              }
          }(),
          netent_gi_core = function() {
              var n, e = function(n, e, t, o) {
                      t = t || function() {}, o = netent_error_handling.handler(o);
                      try {
                          netent_config_handling.handleConfig(e, function() {
                              netent_module_handling.addAndLoadWithConfig(n, e, t, o)
                          })
                      } catch (i) {
                          o(i)
                      }
                  },
                  t = function(n, o, i) {
                      var r, a, s;
                      if ("function" != typeof o && "function" != typeof i) return new Promise(function(e, o) {
                          t(n, e, o)
                      });
                      if (netent_logging_handling.queue(), a = function(n) {
                              var t = netent_tools.combine({}, n);
                              e("launch", n, function(n) {
                                  o(n)
                              }, function(e) {
                                  var o;
                                  i(e), o = netent_tools.combine({}, t), delete o.giLocation, t = Object.assign(t, {
                                      operatorConfiguration: o
                                  }), netent_logging_handling.initialized || netent_logging_handling.initLogging(n, t)
                              })
                          }, s = n.staticServer || n.staticServerURL || "", "string" != typeof s) throw new Error("Cannot launch the game with the current configuration.");
                      r = netent_tools.concat(s, "/config/services.json"), netent_gi_core.getDynamicHostname(r, s, function(e) {
                          var t = netent_tools.combine(n, {
                              staticServer: e
                          });
                          a(t)
                      })
                  },
                  o = function(n, e, t) {
                      netent_json_handling.getJson(n, function(n) {
                          t(n ? n.clienthost ? n.clienthost.trim() : e : e)
                      }, function(n) {
                          t(e)
                      })
                  },
                  i = function(n, t, o) {
                      if ("function" != typeof t && "function" != typeof o) return new Promise(function(e, t) {
                          i(n, e, t)
                      });
                      const r = n.staticServer || n.staticServerURL || "";
                      if ("string" != typeof r) throw new Error("Cannot launch the game with the current configuration.");
                      const a = netent_tools.concat(r, "/config/services.json");
                      netent_gi_core.getDynamicHostname(a, r, function(i) {
                          const r = netent_tools.combine(n, {
                                  staticServer: i
                              }),
                              a = netent_tools.combine({}, r);
                          e("getgamerules", a, t, o)
                      })
                  },
                  r = function(e) {
                      var t, o;
                      return n ? n : (e || (e = window.parent), t = {
                          contentWindow: e
                      }, o = new netent_netentextend.Html(t, {
                          netentExtendSupported: !0
                      }), n = {
                          get: o.get.bind(o),
                          set: o.set.bind(o),
                          call: o.call.bind(o),
                          addEventListener: o.addEventListener.bind(o),
                          removeEventListener: o.removeEventListener.bind(o)
                      })
                  },
                  a = function(n, t, o) {
                      return "function" != typeof t && "function" != typeof o ? new Promise(function(e, t) {
                          a(n, e, t)
                      }) : void e("lcapi", n, t, o)
                  };
              return {
                  launch: t,
                  getDynamicHostname: o,
                  getGameRules: i,
                  getOpenTables: a,
                  plugin: r
              }
          }(),
          netent_json_handling = function() {
              var n = function(n, e, t, o, i) {
                      var r = new XMLHttpRequest;
                      r.open(n, e, !0), r.onreadystatechange = function() {
                          4 === r.readyState && (200 === r.status ? t(r.responseText) : o(r.status > 0 && 200 !== r.status ? r.responseText : new netent_error_handling.GiError(23, "netent_gi_core", r, {
                              url: e
                          })))
                      }, "GET" === n ? r.send() : (r.setRequestHeader("Content-Type", "application/json;charset=utf-8"), r.send(JSON.stringify(i)))
                  },
                  e = function(e, t, o, i, r) {
                      n(e, t, function(n) {
                          try {
                              o(JSON.parse(n))
                          } catch (e) {
                              i(new netent_error_handling.GiError(23, "netent_gi_core", {
                                  url: t
                              }))
                          }
                      }, i, r)
                  },
                  t = function(n, t, o, i) {
                      e("POST", n, o, i, t)
                  },
                  o = function() {
                      var n = {};
                      return function(t, o, i, r) {
                          !r && n[t] ? o(n[t]) : e("GET", t, function(e) {
                              n[t] = e, o(n[t])
                          }, i)
                      }
                  }();
              return {
                  getJson: o,
                  postJson: t
              }
          }(),
          netent_language_handling = function() {
              var n = "en",
                  e = {
                      ar: "ar-KW",
                      he: "iw",
                      "pt-BR": "br",
                      "pt-PT": "pt",
                      "zh-Hans": "cn",
                      "zh-Hant": "zh-TW"
                  },
                  t = {
                      "fr-CA": "fr",
                      "es-US": "es",
                      "zh-TW": "cn",
                      "nl-BE": "nl"
                  },
                  o = function(n, e) {
                      var t = n.staticServer + e + "langlib/";
                      return function(n) {
                          var e, o;
                          o = t + n + "/" + n + ".json";
                          try {
                              return e = new XMLHttpRequest, e.open("GET", o, !1), e.send(), 200 === e.status
                          } catch (i) {
                              return !1
                          }
                      }
                  },
                  i = function(n) {
                      return n in t
                  },
                  r = function(n) {
                      return n in e
                  },
                  a = function(n) {
                      var e;
                      return i(n) ? (e = t[n], [e]) : []
                  },
                  s = function(n) {
                      if (r(n)) {
                          const t = e[n];
                          return [n, t].concat(a(t))
                      }
                      return [n].concat(a(n))
                  },
                  c = function(e, t) {
                      var i, r, a, c, l;
                      if (i = e.language, r = o(e, t), !i) return n;
                      for (l = s(i), c = 0; c < l.length; c++)
                          if (a = l[c], r(a)) return a;
                      return i
                  };
              return {
                  getLanguage: c,
                  getFallbackList: s
              }
          }(),
          initConfig = {
              url: "https://gcs-prod.casinomodule.com/gcs/init",
              clientname: "game-inclusion",
              clientver: "1.26.1"
          },
          netent_logging_handling = function() {
              var n = new Date,
                  e = function() {
                      var n = [];
                      return function(e) {
                          return e && n.push({
                              event: e,
                              timestamp: new Date
                          }), n
                      }
                  }(),
                  t = function() {
                      e().length = 0, netent_logging_handling.log = function() {}, netent_logging_handling.initialized = !0
                  },
                  o = function() {
                      netent_logging_handling.log = e, netent_logging_handling.initialized = !1
                  },
                  i = function(n) {
                      var e, t;
                      if (n.casinoBrand) return n.casinoBrand;
                      try {
                          return e = new RegExp("^(?:\\w*:\\/\\/)?([a-zA-Z0-9-]+?)(?:-static|-scs|\\.)\\S+$"), t = e.exec(n.staticServer)[1], t || "unbranded"
                      } catch (o) {
                          return ""
                      }
                  },
                  r = function(n, e, t) {
                      var o = {
                              clientname: initConfig.clientname,
                              clientver: initConfig.clientver,
                              casinoid: i(n),
                              gameid: n.gameId || ""
                          },
                          r = n && n.loggingURL || initConfig.url;
                      netent_json_handling.postJson(r, o, function(n) {
                          n.initRequest = o, e(n)
                      }, t)
                  },
                  a = function(n) {
                      return netent_tools.getBooleanValue(n.enabled, !1) && n.hasOwnProperty("configuration")
                  },
                  s = function(i, s, c) {
                      var l = function(n, e, t, o) {
                              console.warn(n, e, t, o), "function" == typeof c && c()
                          },
                          u = function(n) {
                              var t;
                              if (n) {
                                  for (; e().length;) t = e().shift(), n(t.event, t.timestamp);
                                  netent_logging_handling.log = n, "function" == typeof c && c()
                              }
                          };
                      return netent_tools.getBooleanValue(i.disableLogging, !1) ? (t(), void("function" == typeof c && c())) : (o(), netent_logging_handling.initialized = !0, void r(i, function(e) {
                          a(e) ? (e.gi_started_time = n, e.operatorConfig = i, e.launchConfig = s, e.staticServer = i.staticServer, s && s.giLocation && (e.giLocation = s.giLocation), netent_logging_handling.statisticEndpointURL = e.configuration.endpoint, netent_module_handling.addAndLoadWithConfig("logging", e, u, l)) : (t(), "function" == typeof c && c())
                      }, l))
                  };
              return {
                  initLogging: s,
                  log: e,
                  queue: o,
                  initialized: !1,
                  listenersAdded: !1
              }
          }(),
          netent_module_handling = function() {
              var n = {},
                  e = "netent_module_handling",
                  t = function(e) {
                      return n[e].loaded
                  },
                  o = function(e, t) {
                      return n[e].essentialParameters = window[e].essentialParameters, Boolean(netent_validation.validateEssentialParameters(n[e].essentialParameters, n[e].config, t))
                  },
                  i = function(t, i, r) {
                      var a, s;
                      try {
                          o(t, r) && (a = window[t].init, s = n[t], a(s.config, i, r))
                      } catch (c) {
                          r(24, e, c, {
                              module: t
                          })
                      }
                  },
                  r = function(e, t) {
                      n[e].loaded = t
                  },
                  a = function(e, t) {
                      var o = n[e].config.giLocation || n[e].config.staticServer + "gameinclusion/library/",
                          i = "modules" + (t || "") + "/" + e.split("netent_")[1] + "/main.js",
                          r = "";
                        //added
                      console.log(o = decodeURIComponent(o), o.split("?")[1] && (r = "?" + o.split("?")[1]), o.split("?")[0] + i + r)
                        //exitadded
                      return o = decodeURIComponent(o), o.split("?")[1] && (r = "?" + o.split("?")[1]), o.split("?")[0] + i + r
                  },
                  s = function(n, o, s, c) {
                      t(n) ? i(n, s, c) : netent_tools.loadScript(a(n, o), function() {
                          r(n, !0), i(n, s, c)
                      }, function(t) {
                          c(25, e, t, {
                              module: n
                          })
                      })
                  },
                  c = function(e, t) {
                      n[e] || (n[e] = {}, r(e, !1)), n[e].config = t
                  },
                  l = function(n, e, t, o, i) {
                      n = "netent_" + n, c(n, e), s(n, i, t, o)
                  };
              return {
                  addAndLoadWithConfig: l
              }
          }(),
          netent_tools = function() {
              var n = function(e, t) {
                      var o, i, r, a = {};
                      for (o in e)
                          if (e.hasOwnProperty(o))
                              if ("object" == typeof e[o]) {
                                  i = n(e[o], t);
                                  for (r in i) i.hasOwnProperty(r) && (a[t ? o + "." + r : r] = i[r])
                              } else a[o] = e[o];
                      return a
                  },
                  e = function() {
                      var n = function(n, t) {
                          var o, i = JSON.parse(JSON.stringify(n));
                          for (o in t) "object" == typeof t[o] && "[object Array]" !== Object.prototype.toString.call(t[o]) ? (n[o] = n[o] || {}, i[o] = e(n[o], t[o])) : i[o] = t[o];
                          return i
                      };
                      return function() {
                          var e, t, o = arguments[0];
                          for (t = 1; t < arguments.length; t++) e = arguments[t], void 0 !== e && (o = n(o, e));
                          return o
                      }
                  }(),
                  t = function(n, e, t, o) {
                      var i = document.createElement("script");
                      i.setAttribute("src", n), i.onload = e, i.onerror = t, o && i.setAttribute("id", o), document.getElementsByTagName("head")[0].appendChild(i)
                  },
                  o = function(n) {
                      return "string" == typeof n ? /^\d+\.?\d*$/.test(n) : "number" == typeof n
                  },
                  i = function(n) {
                      return /^(px|em|pt|in|cm|mm|ex|pc|rem|vw|vh|%)$/.test(n)
                  },
                  r = function(n, e, t, o) {
                      var r = i(e) ? e : "px",
                          a = i(o) ? o : "px";
                      return t >= n ? i(e) ? e : a : i(o) ? o : r
                  },
                  a = function(n, e, t, i, a) {
                      var s = /^(\d+\.?\d*)(\D*)$/,
                          c = s.exec(n),
                          l = s.exec(e),
                          u = parseInt(c[1], 10),
                          g = parseInt(l[1], 10),
                          f = s.exec(t),
                          d = s.exec(i),
                          p = parseInt(f[1], 10),
                          h = parseInt(d[1], 10),
                          m = u / g,
                          _ = g / u,
                          v = {},
                          y = r(p, f[2], h, d[2]);
                      if (!a) return o(t) && (t += y), o(i) && (i += y), {
                          width: t || "",
                          height: i || ""
                      };
                      if ("%" === y) throw new netent_error_handling.GiError(26, "common.embed", "% as unit");
                      return o(p) || o(h) ? !o(h) || h >= p * _ ? (v.width = p, v.height = p * _) : (v.height = h, v.width = h * m) : (v.width = u, v.height = g), v.width = Math.round(parseInt(v.width, 10)) + y, v.height = Math.round(parseInt(v.height, 10)) + y, v
                  },
                  s = function(n) {
                      var e;
                      for (e in n) !n.hasOwnProperty(e) || null !== n[e] && void 0 !== n[e] || delete n[e]
                  },
                  c = function(n, e) {
                      var t = e;
                      return "boolean" == typeof n ? n : n ? (n = n.toLowerCase(), "true" === n ? t = !0 : "false" === n && (t = !1), t) : t
                  },
                  l = function(n, e, t) {
                      var o;
                      return netent_logging_handling.statisticEndpointURL ? (t = "undefined" != typeof t ? t : !0, o = t ? encodeURIComponent(JSON.stringify(netent_logging.giOperatorConfig)) : JSON.stringify(netent_logging.giOperatorConfig), "object" == typeof n && (n.statisticEndpointURL = netent_logging_handling.statisticEndpointURL, n.logsId = netent_logging.logsId, n.loadStarted = netent_logging.game_load_started_time, n.giOperatorConfig = o, n.casinourl = netent_logging.casinourl, n.loadSeqNo = netent_logging.loadSeqNo, e && (n.redirect = "true")), "string" == typeof n && (n += -1 === n.indexOf("?") ? "?" : "&", n += "statisticEndpointURL=" + netent_logging_handling.statisticEndpointURL, n += "&logsId=" + netent_logging.logsId, n += "&loadStarted=" + netent_logging.game_load_started_time, n += "&giOperatorConfig=" + o, n += "&casinourl=" + netent_logging.casinourl, n += "&loadSeqNo=" + netent_logging.loadSeqNo, e && (n += "&redirect=true")), n) : n
                  },
                  u = function(n) {
                      var e, t = "DEMO-";
                      for (e = 0; 13 > e; e++) t += Math.floor(10 * Math.random());
                      return t += "-" + (n || "USD")
                  },
                  g = function(n, e) {
                      var t, o = [{
                          from: "gameServerURL",
                          to: "server"
                      }, {
                          from: "language",
                          to: "lang"
                      }, {
                          from: "sessionId",
                          to: e
                      }, {
                          from: "casinoBrand",
                          to: "operatorId"
                      }];
                      return n.hasOwnProperty("mobileParams") && (t = n.mobileParams, Object.keys(t).forEach(function(e) {
                          t.hasOwnProperty(e) && !n.hasOwnProperty(e) && (n[e] = t[e])
                      })), delete n.mobileParams, o.forEach(function(e) {
                          n.hasOwnProperty(e.from) && (n[e.to] = n[e.from]), delete n[e.from]
                      }), n
                  },
                  f = function(n, e) {
                      var t, o;
                      return n = n.lastIndexOf("/") === n.length - 1 ? n.substr(0, n.length - 1) : n, e = 0 === e.indexOf("/") ? e.substr(1) : e, t = "" === n && "" === e, o = t ? "" : String(n + "/" + e)
                  };
              return {
                  flatten: n,
                  combine: e,
                  loadScript: t,
                  removeMissingProperties: s,
                  resize: a,
                  getBooleanValue: c,
                  addLoggingData: l,
                  createDemoSessionID: u,
                  transformConfig: g,
                  concat: f
              }
          }(),
          netent_validation = function() {
              var n = {
                      string: /.*$/,
                      "boolean": /^(true|false|TRUE|FALSE|True|False)$/
                  },
                  e = function(e, t, o) {
                      return void 0 === t || void 0 === o || n[o].test(t) ? 0 : {
                          key: e,
                          type: o
                      }
                  },
                  t = function(n) {
                      var t, o, i, r, a = netent_config_handling.essentialParameters,
                          s = [];
                      for (t in a)
                          if (a.hasOwnProperty(t))
                              if (o = a[t], "string" == typeof o) s.push(e(t, n[t], o));
                              else
                                  for (i in o) o.hasOwnProperty(i) && (r = n[t] ? n[t][i] : void 0, s.push(e(t + "." + i, r, o[i])));
                      return s.filter(Boolean)
                  },
                  o = function(n, e) {
                      var t;
                      if (-1 !== n.indexOf(".")) {
                          if (t = n.split(".", 2), !e[t[0]] || !e[t[0]][t[1]]) return !1
                      } else if (!e[n]) return !1;
                      return !0
                  },
                  i = function(n, e, t) {
                      var i, r, a, s, c, l, u = 0,
                          g = "||";
                      if (n)
                          for (i = 0; i < n.length; i++)
                              if (a = n[i], -1 !== a.indexOf(g)) {
                                  for (s = a.split(g), c = !1, r = 0; r < s.length; r++)
                                      if (o(s[r], e)) {
                                          c = !0;
                                          break
                                      } c || (u++, t(17, "netent_config_handling", a, {
                                      parameter: a
                                  }))
                              } else o(a, e) || (u++, t(17, "netent_config_handling", a, {
                                  parameter: a
                              }));
                      return l = netent_validation.verifyConfigValueTypes(e), 0 !== l.length && l.forEach(function(n) {
                          u++, t(1, "netent_config_handling", "validate parameters", n)
                      }), 0 === u
                  },
                  r = function(n) {
                      return n && "[object Array]" === Object.prototype.toString.call(n.data) && n.data.length > 0
                  };
              return {
                  validateEssentialParameters: i,
                  verifyConfigValueTypes: t,
                  validateMessage: r
              }
          }();
      window.netent = {
          launch: netent_gi_core.launch,
          getGameRulesUrl: netent_gi_core.getGameRules,
          getGameRulesURL: netent_gi_core.getGameRules,
          getGameRules: netent_gi_core.getGameRules,
          getOpenTables: netent_gi_core.getOpenTables
      }, Object.defineProperty(window.netent, "plugin", {
          get: netent_gi_core.plugin
      });
  </script>
  <style>
    html {
        background: #000;
    }
    * { margin: 0; padding: 0;}
    a {
      color: #fff;
    }
    #netentPoweredBy {
      position: fixed;
      bottom: 0px;
      text-align: center;
      width: 100%;
      height: 3vh;
      color: #fff;
      background: #333;
      line-height: 3vh;
      font-size: 2.5vh;
    }

    </style>

</head>
<body>
<div id="netentGame"></div>

<script>

/* function to get the parameter */
function findGetParameter(parameterName) {
    var result = null,
        tmp = [];
    var items = location.search.substr(1).split("&");
    for (var index = 0; index < items.length; index++) {
        tmp = items[index].split("=");
        if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
    }
    return result;
}


// set default loader params
var loaderParams = {};
loaderParams.currency   = 'USD'
loaderParams.language   = 'en'
loaderParams.gameId     = '<?php echo $game_request['game'] ?? 'starburst_not_mobile' ?>'

// if we have an empty or not valid currency just use the default
if (findGetParameter('currency') !== null) {
    var currency = /^[a-zA-Z0-9_-]+$/.test(findGetParameter('currency'));
    if(currency) { loaderParams.currency = findGetParameter('currency').toUpperCase();}
}

// if we have an empty or not valid language just use the default
if (findGetParameter('language') !== null) {
    var language = /^[a-zA-Z0-9_-]+$/.test(findGetParameter('language'));
    if(language) { loaderParams.language = findGetParameter('language').toLowerCase();}
}
// if we have an empty or not valid gameId just use the default
if (findGetParameter('gameId') !== null) {
    var gameId = /^[a-zA-Z0-9_-]+$/.test(findGetParameter('gameId'));
    if(gameId) { loaderParams.gameId = findGetParameter('gameId').toLowerCase();}
}


console.log(loaderParams);

  var sessId = Math.floor(Math.random() * 10000000000) + 1;

  var startGame = function () {

      var config = {
        staticServerURL: "https://netentff-static.casinomodule.com",
        gameServerURL: "https://netentff-game.casinomodule.com",
        sessionId: "DEMO-"+sessId+"-"+"<?php echo $game_request['currency'] ?? 'USD' ?>",
        targetElement: "netentGame",
        walletMode : "basicwallet",
        language: loaderParams.language,
        gameId: loaderParams.gameId

      };

      var success = function (netEntExtend) {
          //added
          window.location.replace('https://<?php echo $_SERVER['HTTP_HOST'] ?>/redirect_netent?token='+"<?php echo $game_request['token'] ?? '' ?>"+'&verify_url=' + netentGame.src);
                        //exitadded

          //resize game windows
          netEntExtend.resize(window.innerWidth, window.innerHeight-30);

          window.addEventListener('resize', function() {
            netEntExtend.resize(window.innerWidth, window.innerHeight-30);
          });
      };
      var error = function (e) { console.log(e) };

      netent.launch(config, success, error);
  }
  startGame();

</script>




</body></html>