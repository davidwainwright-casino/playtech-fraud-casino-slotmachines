var Util = function() {
    var b = {
            LOWER: 0,
            UPPER: 1
        },
        i = function(a) {
            a = parseFloat(a);
            return !isNaN(a) && isFinite(a)
        },
        e = function(a) {
            return "function" == typeof a
        },
        g = function(a) {
            return "string" == typeof a
        },
        c = function(a) {
            return a && "object" == typeof a || a instanceof Object
        },
        d = function(a) {
            return "undefined" == typeof a
        },
        h = function(a, f) {
            if (c(a)) {
                var f = {},
                    b;
                for (b in a)
                    f[b] = h(a[b])
            } else
                f = a;
            return f
        },
        j = function(a, f, b) {
            if (!g(a))
                return {};
            for (var c = {}, a = a.split(f), d = 0; d < a.length; ++d)
                f = a[d].split(b, 2),
                c[f[0]] = 1 < f.length ? decodeURIComponent(f[1]).replace(/\+/g,
                    " ") : "";
            return c
        };
    return {
        CASE: b,
        inherit: function(a, f) {
            var b = function() {};
            b.prototype = f.prototype;
            var b = new b,
                c;
            for (c in a.prototype)
                b[c] = a.prototype[c];
            a.prototype = b;
            a.prototype["super"] = f.prototype
        },
        isNumber: i,
        isPosition: function(a) {
            return a && i(a.top) && i(a.left)
        },
        isFunction: e,
        isJQuery: function(a) {
            return a instanceof $
        },
        arrayRemove: function(a, b) {
            for (var c = [], d = 0; d < a.length; ++d)
                b != a[d] && c.push(a[d]);
            return c
        },
        isArray: function(a) {
            return a instanceof Array
        },
        isString: g,
        stringsEqual: function(a, b, c) {
            a = ["^", a, "$"].join("");
            return (c ? RegExp(a) : RegExp(a, "i")).test(b)
        },
        isObject: c,
        isUndefined: d,
        isOfSameType: function(a, b) {
            return typeof a == typeof b
        },
        clone: h,
        extend: function() {
            for (var a = 1; a < arguments.length; a++)
                for (var b in arguments[a])
                    arguments[a].hasOwnProperty(b) && (arguments[0][b] = arguments[a][b]);
            return arguments[0]
        },
        getObjectValues: function(a) {
            var b = [];
            if (c(a))
                for (var d in a)
                    e(a[d]) || b.push(a[d]);
            return b
        },
        parseParams: function(a) {
            return j(a, "&", "=")
        },
        parseParamsCustom: j,
        changeCase: function(a, c, e) {
            if (!g(a) ||
                0 == $.trim(a).length)
                return a;
            var h = !1;
            d(c) && (h = !0);
            if (!h && (!i(c) || 0 > c || c >= a.length))
                return a;
            switch (e) {
                case b.UPPER:
                    e = "toUpperCase";
                    break;
                default:
                    e = "toLowerCase"
            }
            if (h)
                return a[e]();
            h = [];
            0 < c && h.push(a.slice(0, c));
            h.push(a.charAt(c)[e]());
            h.push(a.slice(c + 1));
            return h.join("")
        }
    }
}();
var TimeUtil = function() {
    var b,
        i = (new Date).getTimezoneOffset(),
        e = Math.abs(i),
        g = Math.floor(e / 60),
        e = e - 60 * g;
    b = (0 > i ? "+" : "-") + (g ? (10 > g ? "0" : "") + g : "00") + ":" + (e ? (10 > e ? "0" : "") + e : "00");
    return {
        getTimeZoneOffset: function() {
            return b
        }
    }
}();
var Html5GameManager = function() {
    var b = {},
        i = null,
        e = function(c) {
            try {
                var d = JSON.parse(c);
                console.log("receiveMessageFromGame", d);
                if (Util.isObject(d)) {
                    var h = Util.isObject(d.args) ? d.args : {};
                    d.args = h;
                    switch (d.common) {
                        case "EVT_GET_CONFIGURATION":
                            h.config = b.gameConfig;
                            var e = JSON.stringify(d);
                            try {
                                var a = JSON.parse(e);
                                console.log("sendMessageToGame", a)
                            } catch (f) {
                                console.error("sendMessageToGame", f, e)
                            }
                            try {
                                Util.isFunction(window.sendToGame) ? window.sendToGame(e) : console.log("sendMessageToGame", "sendToGame is not function")
                            } catch (k) {
                                console.error("sendMessageToGame",
                                    k, e)
                            }
                            break;
                        case "EVT_OPEN_LOBBY":
                            g(b.lobbyUri, b.mobileLobbyUri);
                            break;
                        case "EVT_CLOSE_GAME":
                            g(b.lobbyUri, b.mobileLobbyUri);
                            break;
                        case "EVT_OPEN_CASHIER":
                            g(b.cashierUri, b.mobileCashierUri);
                            break;
                        case "UNLOGGED":
                            null != i && b.extendSessionUri && clearInterval(i)
                    }
                }
            } catch (l) {
                console.error("receiveMessageFromGame", l, c)
            }
        },
        g = function(b, d) {
            UHT_DEVICE_TYPE.MOBILE && d ? /^js:\/\/.*/i.test(d) ? (new Function(d.substring(5, d.length)))() : d && window.open(d, "_self") : b && (/^js:\/\/.*/i.test(b) ? (new Function(b.substring(5, b.length)))() :
                b && window.open(b, "_self"))
        };
    extendSessionRequest = function() {
        if (b.extendSessionUri) {
            for (var c = b.extendSessionUri.split(","), d = ["_t", (new Date).getTime()].join("="), e = 0; e < c.length; e++)
                try {
                    (new Image).src = [c[e], /\?/.test(c[e]) ? "&" : "?", d].join("")
                } catch (g) {
                    console.error("Extend Session Request", g)
                }
        }
    };
    return {
        init: function(c) {
            Util.extend(b, c);
            c = b.contextPath || "";
            Util.extend(b, {
                loginUri: [c, "/common/pages/unlogged.jsp"].join(""),
                entryUri: [c, "/mobile/generic/"].join("")
            });
            Util.extend(b, {
                lobbyUri: b.lobbyUrl,
                cashierUri: b.cashierUrl,
                mobileLobbyUri: b.mobileLobbyUrl,
                mobileCashierUri: b.mobileCashierUrl,
                extendSessionUri: b.extendSessionUrl
            });
            var c = JSON.parse(b.gameConfig),
                d = c.HISTORY;
            if (d) {
                if (!c.hasOwnProperty("historyType") || "external" != c.historyType)
                    d += "&tz=" + encodeURIComponent(TimeUtil.getTimeZoneOffset()),
                    c.HISTORY = d;
                Util.extend(b, {
                    gameConfig: c
                })
            }
            b.extendSessionInterval && b.extendSessionUri && (i = setInterval(extendSessionRequest, b.extendSessionInterval));
            window.sendToAdapter = e
        }
    }
}();