! function() {
    var e = {
            6567: function(e, t, n) {
                "use strict";
                var r, o = this && this.__extends || (r = function(e, t) {
                        return (r = Object.setPrototypeOf || {
                                __proto__: []
                            }
                            instanceof Array && function(e, t) {
                                e.__proto__ = t
                            } || function(e, t) {
                                for (var n in t) Object.prototype.hasOwnProperty.call(t, n) && (e[n] = t[n])
                            })(e, t)
                    }, function(e, t) {
                        if ("function" != typeof t && null !== t) throw new TypeError("Class extends value " + String(t) + " is not a constructor or null");

                        function n() {
                            this.constructor = e
                        }
                        r(e, t), e.prototype = null === t ? Object.create(t) : (n.prototype = t.prototype, new n)
                    }),
                    i = this && this.__spreadArray || function(e, t) {
                        for (var n = 0, r = t.length, o = e.length; n < r; n++, o++) e[o] = t[n];
                        return e
                    },
                    a = this && this.__importDefault || function(e) {
                        return e && e.__esModule ? e : {
                            default: e
                        }
                    };
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.AbstractWidgetDraggableEventListenerCommand = void 0;
                var s = a(n(4293)),
                    u = n(2485),
                    c = n(2374),
                    l = n(4019),
                    d = n(7344),
                    f = function(e) {
                        function t() {
                            return null !== e && e.apply(this, arguments) || this
                        }
                        return o(t, e), t.prototype.execute = function() {
                            this.setDraggableElements(this.draggableModel), this.addEventListeners(this.draggableModel)
                        }, t.prototype.setDraggableElements = function(e) {
                            var t = document.querySelector("[name=" + (l.StringUtil.toSnakeCase(d.EWidgetLayoutType.DRAGGABLE) + e.widgetUniqueId) + "]");
                            e.element = t.firstElementChild, e.layoutElement = t.parentElement
                        }, t.prototype.addEventListeners = function(e) {
                            e.element.addEventListener(this.model.interaction.DOWN, this.bind(this.dragStart, e)), e.element.addEventListener(this.model.interaction.MOVE, this.bind(this.drag, e)), e.element.addEventListener(this.model.interaction.UP, this.bind(this.dragEnd, e)), e.element.addEventListener(this.model.interaction.OUT, this.bind(this.createDragAreaElement, e)), e.layoutElement.addEventListener("contextmenu", (function(e) {
                                return e.preventDefault(), !1
                            })), document.addEventListener(this.model.interaction.OUT, this.bind(this.onPointerLeftWindow, e))
                        }, t.prototype.bind = function(e, t) {
                            var n = this;
                            return function() {
                                for (var r = [], o = 0; o < arguments.length; o++) r[o] = arguments[o];
                                e.call.apply(e, i([n, t], r))
                            }
                        }, t.prototype.dragStart = function(e, t) {
                            e.isElementVisible && !e.isElementFullScreen && (t.type === c.CoreInteractionEventNames.TOUCH_START ? (e.initialX = t.touches[0].clientX, e.initialY = t.touches[0].clientY) : (e.initialX = t.clientX, e.initialY = t.clientY), t.target !== e.element && t.target.closest("#" + e.element.id) !== e.element || (e.active = !0))
                        }, t.prototype.drag = function(e, t) {
                            if (e.active) {
                                t.preventDefault(), t.type === c.CoreInteractionEventNames.TOUCH_MOVE ? (e.currentX = e.initialX - t.touches[0].clientX, e.currentY = e.initialY - t.touches[0].clientY) : (e.currentX = e.initialX - t.clientX, e.currentY = e.initialY - t.clientY), e.initialX = t.clientX, e.initialY = t.clientY;
                                var n = e.element.offsetLeft - e.currentX,
                                    r = e.element.offsetTop - e.currentY;
                                n >= 0 && r >= 0 && n < window.innerWidth - e.element.offsetWidth && r < window.innerHeight - e.element.offsetHeight && (e.element.style.left = n + "px", e.element.style.top = r + "px"), (t.clientX > window.innerWidth || t.clientX < 0 || t.clientY > window.innerHeight || t.clientY < 0) && this.dragEnd(e)
                            }
                        }, t.prototype.dragEnd = function(e) {
                            this.destroyDragAreaElement(e), e.active && (e.initialX = e.currentX, e.initialY = e.currentY, e.saveCoordinates(), e.active = !1)
                        }, t.prototype.createDragAreaElement = function(e) {
                            e.active && !e.dragAreaElement && (this.destroyDragAreaElement(e), e.dragAreaElement = document.createElement("div"), e.dragAreaElement.style.cssText = "position: absolute; top: 0; left: 0; right: 0; bottom: 0; pointer-events: all; z-index: 99999;", e.layoutElement.appendChild(e.dragAreaElement), e.dragAreaElement.addEventListener(this.model.interaction.MOVE, this.bind(this.drag, e)), e.dragAreaElement.addEventListener(this.model.interaction.UP, this.bind(this.dragEnd, e)), e.dragAreaElement.addEventListener(this.model.interaction.OUT, this.bind(this.dragEnd, e)))
                        }, t.prototype.destroyDragAreaElement = function(e) {
                            s.default(e.dragAreaElement) || s.default(e.dragAreaElement.parentElement) || (e.dragAreaElement.parentElement.removeChild(e.dragAreaElement), e.dragAreaElement = null)
                        }, t.prototype.onPointerLeftWindow = function(e, t) {
                            var n = t || window.event,
                                r = n.relatedTarget || n.toElement;
                            r && "HTML" !== r.nodeName || this.dragEnd(e)
                        }, t
                    }(u.BaseCommand);
                t.AbstractWidgetDraggableEventListenerCommand = f
            },
            8305: function(e, t, n) {
                "use strict";
                var r, o = this && this.__extends || (r = function(e, t) {
                        return (r = Object.setPrototypeOf || {
                                __proto__: []
                            }
                            instanceof Array && function(e, t) {
                                e.__proto__ = t
                            } || function(e, t) {
                                for (var n in t) Object.prototype.hasOwnProperty.call(t, n) && (e[n] = t[n])
                            })(e, t)
                    }, function(e, t) {
                        if ("function" != typeof t && null !== t) throw new TypeError("Class extends value " + String(t) + " is not a constructor or null");

                        function n() {
                            this.constructor = e
                        }
                        r(e, t), e.prototype = null === t ? Object.create(t) : (n.prototype = t.prototype, new n)
                    }),
                    i = this && this.__importDefault || function(e) {
                        return e && e.__esModule ? e : {
                            default: e
                        }
                    };
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.AbstractWidgetDraggableSetDefaultPositionCommand = void 0;
                var a = i(n(4293)),
                    s = n(6667),
                    u = n(2485),
                    c = n(7949),
                    l = n(6645),
                    d = n(1135),
                    f = function(e) {
                        function t() {
                            return null !== e && e.apply(this, arguments) || this
                        }
                        return o(t, e), t.prototype.execute = function() {
                            var e = this.draggableModel;
                            l.PlatformUtil.isMobileDevice ? (this.checkDraggablePosition(e), l.PlatformUtil.isIOS || l.PlatformUtil.isSafari ? e.portraitOrientation.addListener(this.checkDraggablePosition.bind(this, e)) : e.portraitOrientation.addEventListener("change", this.checkDraggablePosition.bind(this, e))) : l.PlatformUtil.isMobileDevice || (this.model.cookies.hasKey(e.cookiesLandscapeName) ? this.setElementPositionFromCookies(e, e.cookiesLandscapeName) : this.setDefaultElementPosition(e)), window.addEventListener(s.EEventName.RESIZE, this.checkBounds.bind(this, e))
                        }, t.prototype.checkDraggablePosition = function(e) {
                            this.model.cookies.hasKey(e.cookiesPortraitName) && e.isPortraitOrientation ? this.setElementPositionFromCookies(e, e.cookiesPortraitName) : this.model.cookies.hasKey(e.cookiesLandscapeName) && e.isLandscapeOrientation ? this.setElementPositionFromCookies(e, e.cookiesLandscapeName) : this.setDefaultElementPosition(e)
                        }, t.prototype.checkBounds = function(e) {
                            var t = e.left,
                                n = e.top,
                                r = !1;
                            e.right > e.layoutElement.offsetWidth ? (t = e.layoutElement.offsetWidth - e.element.offsetWidth, r = !0) : e.left < 0 && (t = 0, r = !0), e.bottom > e.layoutElement.offsetHeight ? (n = e.layoutElement.offsetHeight - e.element.offsetHeight, r = !0) : e.top < 0 && (n = 0, r = !0), r && (this.setElementPosition(e, t + "px", n + "px"), e.saveCoordinates())
                        }, t.prototype.setElementPositionFromCookies = function(e, t) {
                            var n = this,
                                r = this.model.cookies.getByKey(t).split("|"),
                                o = r[0],
                                i = r[1];
                            this.setElementPosition(e, o, i), setTimeout((function() {
                                n.checkBounds(e)
                            }))
                        }, t.prototype.setDefaultElementPosition = function(e) {
                            c.CssUtil.add(e.element, {
                                top: "auto",
                                right: "auto",
                                bottom: "auto",
                                left: "auto"
                            }), a.default(e.defaultPositionData) ? this.setElementPosition(e, "0", "0") : this.calculateOffsetForWidget(e)
                        }, t.prototype.calculateOffsetForWidget = function(e) {
                            var t, n;
                            switch (e.defaultPositionData.position) {
                                case d.EWidgetDefaultPosition.TOP_LEFT:
                                    this.detectScreenRotationAndSetPosition(e, 0, 0);
                                    break;
                                case d.EWidgetDefaultPosition.TOP_CENTER:
                                    t = (window.innerWidth - e.element.offsetWidth) / 2, this.detectScreenRotationAndSetPosition(e, t, 0);
                                    break;
                                case d.EWidgetDefaultPosition.TOP_RIGHT:
                                    t = window.innerWidth - e.element.offsetWidth, this.detectScreenRotationAndSetPosition(e, t, 0);
                                    break;
                                case d.EWidgetDefaultPosition.CENTER_LEFT:
                                    n = (window.innerHeight - e.element.offsetHeight) / 2, this.detectScreenRotationAndSetPosition(e, 0, n);
                                    break;
                                case d.EWidgetDefaultPosition.CENTER_CENTER:
                                    t = (window.innerWidth - e.element.offsetWidth) / 2, n = (window.innerHeight - e.element.offsetHeight) / 2, this.detectScreenRotationAndSetPosition(e, t, n);
                                    break;
                                case d.EWidgetDefaultPosition.CENTER_RIGHT:
                                    t = window.innerWidth - e.element.offsetWidth, n = (window.innerHeight - e.element.offsetHeight) / 2, this.detectScreenRotationAndSetPosition(e, t, n);
                                    break;
                                case d.EWidgetDefaultPosition.BOTTOM_LEFT:
                                    n = window.innerHeight - e.element.offsetHeight, this.detectScreenRotationAndSetPosition(e, 0, n);
                                    break;
                                case d.EWidgetDefaultPosition.BOTTOM_CENTER:
                                    t = (window.innerWidth - e.element.offsetWidth) / 2, n = window.innerHeight - e.element.offsetHeight, this.detectScreenRotationAndSetPosition(e, t, n);
                                    break;
                                case d.EWidgetDefaultPosition.BOTTOM_RIGHT:
                                    t = window.innerWidth - e.element.offsetWidth, n = window.innerHeight - e.element.offsetHeight, this.detectScreenRotationAndSetPosition(e, t, n)
                            }
                        }, t.prototype.detectScreenRotationAndSetPosition = function(e, t, n) {
                            var r, o;
                            e.isLandscapeOrientation && !a.default(e.defaultPositionData.positionOffset) ? (r = e.defaultPositionData.positionOffset.landscape.x + t + "px", o = e.defaultPositionData.positionOffset.landscape.y + n + "px") : e.isPortraitOrientation && !a.default(e.defaultPositionData.positionOffset) && (r = e.defaultPositionData.positionOffset.portrait.x + t + "px", o = e.defaultPositionData.positionOffset.portrait.y + n + "px"), this.setElementPosition(e, r, o)
                        }, t.prototype.setElementPosition = function(e, t, n) {
                            e.element.style.left = t, e.element.style.top = n
                        }, t
                    }(u.BaseCommand);
                t.AbstractWidgetDraggableSetDefaultPositionCommand = f
            },
            1135: function(e, t) {
                "use strict";
                Object.defineProperty(t, "__esModule", {
                        value: !0
                    }), t.EWidgetDefaultPosition = void 0,
                    function(e) {
                        e.TOP_LEFT = "top_left", e.TOP_CENTER = "top_center", e.TOP_RIGHT = "top_right", e.CENTER_LEFT = "center_left", e.CENTER_CENTER = "center_center", e.CENTER_RIGHT = "center_right", e.BOTTOM_LEFT = "bottom_left", e.BOTTOM_CENTER = "bottom_center", e.BOTTOM_RIGHT = "bottom_right"
                    }(t.EWidgetDefaultPosition || (t.EWidgetDefaultPosition = {}))
            },
            7344: function(e, t) {
                "use strict";
                Object.defineProperty(t, "__esModule", {
                        value: !0
                    }), t.EWidgetLayoutType = void 0,
                    function(e) {
                        e.NONE = "None", e.HEADER = "Header", e.HEADER_OVER = "Header Over", e.OVERLAY_TOP = "Overlay Top", e.SIDE_1 = "Side 1", e.SIDE_2 = "Side 2", e.MIDDLE_1 = "Middle 1", e.MIDDLE_2 = "Middle 2", e.MIDDLE_3 = "Middle 3", e.CENTER = "Center", e.FOOTER = "Footer", e.FULL_SCREEN = "Full Screen", e.DRAGGABLE = "Draggable"
                    }(t.EWidgetLayoutType || (t.EWidgetLayoutType = {}))
            },
            490: function(e, t, n) {
                "use strict";
                var r = this && this.__importDefault || function(e) {
                    return e && e.__esModule ? e : {
                        default: e
                    }
                };
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.AbstractWidgetDraggableModel = void 0;
                var o = r(n(3311)),
                    i = r(n(4293)),
                    a = r(n(4061)),
                    s = r(n(6968)),
                    u = n(7960),
                    c = n(5401),
                    l = n(660),
                    d = n(6645),
                    f = n(3431),
                    p = function() {
                        function e() {
                            this.currentX = 0, this.currentY = 0, this.initialX = 0, this.initialY = 0, this.portraitOrientation = window.matchMedia("(orientation: portrait)"), this.landscapeOrientation = window.matchMedia("(orientation: landscape)")
                        }
                        return Object.defineProperty(e.prototype, "widgetUniqueId", {
                            get: function() {
                                return this._data.widgetUniqueId
                            },
                            enumerable: !1,
                            configurable: !0
                        }), Object.defineProperty(e.prototype, "isPortraitOrientation", {
                            get: function() {
                                return this.portraitOrientation.matches
                            },
                            enumerable: !1,
                            configurable: !0
                        }), Object.defineProperty(e.prototype, "isLandscapeOrientation", {
                            get: function() {
                                return this.landscapeOrientation.matches
                            },
                            enumerable: !1,
                            configurable: !0
                        }), Object.defineProperty(e.prototype, "cookiesPortraitName", {
                            get: function() {
                                return "draggable_widget_portrait_coordinates_" + this.widgetUniqueId
                            },
                            enumerable: !1,
                            configurable: !0
                        }), Object.defineProperty(e.prototype, "cookiesLandscapeName", {
                            get: function() {
                                return "draggable_widget_landscape_coordinates_" + this.widgetUniqueId
                            },
                            enumerable: !1,
                            configurable: !0
                        }), Object.defineProperty(e.prototype, "left", {
                            get: function() {
                                return this.element.offsetLeft
                            },
                            enumerable: !1,
                            configurable: !0
                        }), Object.defineProperty(e.prototype, "top", {
                            get: function() {
                                return this.element.offsetTop
                            },
                            enumerable: !1,
                            configurable: !0
                        }), Object.defineProperty(e.prototype, "right", {
                            get: function() {
                                return this.element.offsetLeft + this.element.offsetWidth
                            },
                            enumerable: !1,
                            configurable: !0
                        }), Object.defineProperty(e.prototype, "bottom", {
                            get: function() {
                                return this.element.offsetTop + this.element.offsetHeight
                            },
                            enumerable: !1,
                            configurable: !0
                        }), Object.defineProperty(e.prototype, "isElementVisible", {
                            get: function() {
                                return 0 !== this.element.offsetWidth && 0 !== this.element.offsetHeight
                            },
                            enumerable: !1,
                            configurable: !0
                        }), Object.defineProperty(e.prototype, "isElementFullScreen", {
                            get: function() {
                                return this.element.offsetWidth >= window.innerWidth && this.element.offsetHeight >= window.innerHeight
                            },
                            enumerable: !1,
                            configurable: !0
                        }), e.prototype.parseDefaultPositionFromActionParams = function() {
                            var e = this;
                            if (i.default(this.defaultPositionData)) {
                                var t = function(t) {
                                        return l.ActionParameterUtil.getActionParameter(e._data, u.EActionName.DRAGGABLE_DEFAULT_POSITION, t)
                                    },
                                    n = t(c.EActionParameterName.POSITION),
                                    r = o.default(n, (function(e) {
                                        var t = e.value;
                                        return f.TypesUtil.isTruthy(t)
                                    })),
                                    d = t(c.EActionParameterName.OFFSET_LANDSCAPE),
                                    p = a.default(d, (function(e, t) {
                                        var n = t.name,
                                            r = t.value;
                                        return s.default(e, n, Number(r))
                                    }), {}),
                                    h = t(c.EActionParameterName.OFFSET_PORTRAIT),
                                    m = a.default(h, (function(e, t) {
                                        var n = t.name,
                                            r = t.value;
                                        return s.default(e, n, Number(r))
                                    }), {});
                                if (i.default(r) || i.default(p) || i.default(m)) return;
                                this.defaultPositionData = {
                                    position: r.name,
                                    positionOffset: {
                                        landscape: p,
                                        portrait: m
                                    }
                                }
                            }
                            return this.defaultPositionData
                        }, e.prototype.saveCoordinates = function() {
                            d.PlatformUtil.isMobileDevice ? this.isPortraitOrientation ? this.addCoordinatesToCookies(this.cookiesPortraitName) : this.isLandscapeOrientation && this.addCoordinatesToCookies(this.cookiesLandscapeName) : this.addCoordinatesToCookies(this.cookiesLandscapeName)
                        }, e.prototype.addCoordinatesToCookies = function(e) {
                            var t = new Date((new Date).setFullYear((new Date).getFullYear() + 1));
                            this._cookies.setByKey(e, this.element.style.left + "|" + this.element.style.top), this._cookies.setExpireDate(e, t)
                        }, e
                    }();
                t.AbstractWidgetDraggableModel = p
            },
            7960: function(e, t) {
                "use strict";
                Object.defineProperty(t, "__esModule", {
                        value: !0
                    }), t.EActionName = void 0,
                    function(e) {
                        e.CONFIG = "Config", e.PRELOADER = "Preloader", e.MAPPING = "Mapping", e.TRANSLATIONS = "Translations", e.DRAGGABLE_DEFAULT_POSITION = "Draggable Default Position"
                    }(t.EActionName || (t.EActionName = {}))
            },
            5401: function(e, t) {
                "use strict";
                Object.defineProperty(t, "__esModule", {
                        value: !0
                    }), t.EActionParameterName = void 0,
                    function(e) {
                        e.ASSETS_PATH = "assets_path", e.SOCKET_URL = "socket_url", e.INLINE_CSS = "inline_css", e.TRANSLATIONS = "translations", e.SCENE_SIZE = "scene_size", e.ANIMATIONS = "animations", e.SOUNDS = "sounds", e.CELEBRATION_TYPES = "celebration_types", e.DIGITS_NAMES = "digits_names", e.POSITION = "position", e.OFFSET_LANDSCAPE = "offset_landscape", e.OFFSET_PORTRAIT = "offset_portrait"
                    }(t.EActionParameterName || (t.EActionParameterName = {}))
            },
            4069: function(e, t) {
                "use strict";
                Object.defineProperty(t, "__esModule", {
                        value: !0
                    }), t.EAssetFontName = void 0,
                    function(e) {
                        e.BOLD = "notosansdisplay-condensedbold", e.BLACK = "NotoSans-CondensedBlack", e.HELVETICA = "Helvetica_Neue"
                    }(t.EAssetFontName || (t.EAssetFontName = {}))
            },
            7863: function(e, t) {
                "use strict";
                Object.defineProperty(t, "__esModule", {
                        value: !0
                    }), t.EDeviceName = void 0,
                    function(e) {
                        e.DESKTOP = "desktop", e.MOBILE = "mobile", e.TABLET = "tablet"
                    }(t.EDeviceName || (t.EDeviceName = {}))
            },
            6667: function(e, t) {
                "use strict";
                Object.defineProperty(t, "__esModule", {
                        value: !0
                    }), t.EEventName = void 0,
                    function(e) {
                        e.LOAD = "load", e.RESIZE = "resize", e.FULLSCREENCHANGE = "fullscreenchange", e.BEFOREEND = "beforeend", e.MESSAGE = "message"
                    }(t.EEventName || (t.EEventName = {}))
            },
            6901: function(e, t) {
                "use strict";
                Object.defineProperty(t, "__esModule", {
                        value: !0
                    }), t.EGenericSignalNames = void 0,
                    function(e) {
                        e.GAMES_EVENT_NAMES = "gamesEventNames", e.UNIVERSAL_COMMUNICATION_EVENT = "UniversalCommunicationEvent", e.UNIVERSAL_COMMUNICATION_EVENT_BACKWARD = "UniversalCommunicationEventBackward", e.OPT_IN_COMMUNICATION_EVENT = "OptInCommunicationEvent"
                    }(t.EGenericSignalNames || (t.EGenericSignalNames = {}))
            },
            7732: function(e, t) {
                "use strict";
                Object.defineProperty(t, "__esModule", {
                        value: !0
                    }), t.EGroupStatus = void 0,
                    function(e) {
                        e.ACCEPT = "ACCEPT", e.DECLINE = "DECLINE", e.EXCLUDED = "EXCLUDED", e.PENDING = "PENDING"
                    }(t.EGroupStatus || (t.EGroupStatus = {}))
            },
            8323: function(e, t) {
                "use strict";
                Object.defineProperty(t, "__esModule", {
                        value: !0
                    }), t.EPlatformName = t.EPlatformManufacturerName = t.EPlatformProductName = t.EPlatformOSName = t.EPlatformBrowserName = void 0,
                    function(e) {
                        e.CHROME = "Chrome", e.CHROME_MOBILE = "Chrome Mobile", e.SAFARI = "Safari", e.FIREFOX = "Firefox", e.FIREFOX_FOR_IOS = "Firefox for iOS", e.FIREFOX_MOBILE = "Firefox Mobile", e.IE = "IE", e.MICROSOFT_EDGE = "Microsoft Edge", e.OPERA = "Opera", e.OPERA_MINI = "Opera Mini", e.SAMSUNG_I = "Samsung Internet", e.UCBROWSER = "Android Browser", e.SEAMONKEY = "SeaMonkey", e.SILK = "Silk", e.PHANTOMJS = "PhantomJS"
                    }(t.EPlatformBrowserName || (t.EPlatformBrowserName = {})),
                    function(e) {
                        e.IOS = "iOS", e.ANDROID = "Android"
                    }(t.EPlatformOSName || (t.EPlatformOSName = {})),
                    function(e) {
                        e.IPHONE = "iPhone", e.MOBILE = "Mobile"
                    }(t.EPlatformProductName || (t.EPlatformProductName = {})),
                    function(e) {
                        e.APPLE = "Apple", e.SAMSUNG = "Samsung"
                    }(t.EPlatformManufacturerName || (t.EPlatformManufacturerName = {})),
                    function(e) {
                        e.IOS = "iOS", e.CHROME = "Chrome", e.CHROME_MOBILE = "Chrome Mobile", e.ANDROID = "Android", e.ELECTRON = "Electron", e.FIREFOX = "Firefox", e.FIREFOX_FOR_IOS = "Firefox for iOS", e.FIREFOX_MOBILE = "Firefox Mobile", e.IE = "IE", e.MICROSOFT_EDGE = "Microsoft Edge", e.PHANTOMJS = "PhantomJS", e.SAFARI = "Safari", e.SAFARI_IOS = "Safari iOS", e.SEAMONKEY = "SeaMonkey", e.SILK = "Silk", e.OPERA_MINI = "Opera Mini", e.SAMSUNG_I = "Samsung Internet", e.UCBROWSER = "Android Browser", e.OPERA = "Opera"
                    }(t.EPlatformName || (t.EPlatformName = {}))
            },
            5402: function(e, t) {
                "use strict";
                Object.defineProperty(t, "__esModule", {
                        value: !0
                    }), t.EUrlParamName = void 0,
                    function(e) {
                        e.CACHE_BUSTER = "cacheBuster", e.URL_CONVERTER = "urlConverter", e.ENABLE_LOGS = "enableLogs"
                    }(t.EUrlParamName || (t.EUrlParamName = {}))
            },
            4107: function(e, t) {
                "use strict";
                Object.defineProperty(t, "__esModule", {
                        value: !0
                    }), t.ECommandName = void 0,
                    function(e) {
                        e.APP_ACTIVATED = "APP_ACTIVATED", e.APP_RESIZE = "APP_RESIZE", e.APP_DEACTIVATED = "APP_DEACTIVATED", e.APP_VIEW_REDRAW = "APP_VIEW_REDRAW", e.POST_MESSAGE = "POST_MESSAGE", e.PRINT_VERSION = "PRINT_VERSION"
                    }(t.ECommandName || (t.ECommandName = {}))
            },
            369: function(e, t) {
                "use strict";
                var n = this && this.__spreadArray || function(e, t) {
                    for (var n = 0, r = t.length, o = e.length; n < r; n++, o++) e[o] = t[n];
                    return e
                };
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.AbstractCommand = void 0;
                var r = function() {
                    function e(e) {
                        for (var t = [], r = 1; r < arguments.length; r++) t[r - 1] = arguments[r];
                        this.init.apply(this, n([e], t))
                    }
                    return e.prototype.init = function(e) {
                        for (var t = [], n = 1; n < arguments.length; n++) t[n - 1] = arguments[n];
                        this.model = e, 1 === t.length && (this.view = t[0]), this.views = t
                    }, e
                }();
                t.AbstractCommand = r
            },
            1529: function(e, t, n) {
                "use strict";
                var r = this && this.__importDefault || function(e) {
                    return e && e.__esModule ? e : {
                        default: e
                    }
                };
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.AbstractContext = void 0;
                var o = r(n(4293)),
                    i = n(4107),
                    a = n(4396),
                    s = n(110),
                    u = n(5378),
                    c = n(2318),
                    l = n(8631),
                    d = function() {
                        function e() {}
                        return e.replaceOrGetDefault = function(e, t, n) {
                            return n && !o.default(n[e]) ? n[e] : t
                        }, e.prototype.enterFrameUpdate = function(e) {}, e.prototype.initialize = function() {
                            this.printLibVersion()
                        }, e.prototype.registerCoreCommands = function(t) {
                            var n = this.getController();
                            n.registerCommand(i.ECommandName.APP_RESIZE, e.replaceOrGetDefault(i.ECommandName.APP_RESIZE, c.ResizeCommand, t)), n.registerCommand(i.ECommandName.APP_ACTIVATED, e.replaceOrGetDefault(i.ECommandName.APP_ACTIVATED, a.ActivatedCommand, t)), n.registerCommand(i.ECommandName.APP_DEACTIVATED, e.replaceOrGetDefault(i.ECommandName.APP_DEACTIVATED, s.DeactivatedCommand, t)), n.registerCommand(i.ECommandName.APP_VIEW_REDRAW, e.replaceOrGetDefault(i.ECommandName.APP_VIEW_REDRAW, l.ViewRedrawCommand, t)), n.registerCommand(i.ECommandName.PRINT_VERSION, e.replaceOrGetDefault(i.ECommandName.PRINT_VERSION, u.PrintVersionCommand, t))
                        }, e.prototype.printLibVersion = function() {
                            this.getController().executeCommand(i.ECommandName.PRINT_VERSION)
                        }, e
                    }();
                t.AbstractContext = d
            },
            8827: function(e, t, n) {
                "use strict";
                var r = this && this.__spreadArray || function(e, t) {
                        for (var n = 0, r = t.length, o = e.length; n < r; n++, o++) e[o] = t[n];
                        return e
                    },
                    o = this && this.__importDefault || function(e) {
                        return e && e.__esModule ? e : {
                            default: e
                        }
                    };
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.AbstractController = void 0;
                var i = o(n(4293)),
                    a = function() {
                        function e() {
                            this._commandMap = {}, this._commandsInMemory = {}
                        }
                        return e.prototype.registerCommand = function(e, t) {
                            if (this.hasCommand(e)) throw new Error("Error register new command with name:" + e + ", command already registered");
                            this._commandMap[e] = t
                        }, e.prototype.hasCommand = function(e) {
                            return !i.default(this._commandMap[e])
                        }, e.prototype.executeCommand = function(e, t) {
                            if (!this.hasCommand(e)) throw new Error("Error execute not registered command.");
                            var n;
                            if (i.default(this._commandsInMemory[e])) {
                                var o = this._commandMap[e],
                                    a = this.context.getViews();
                                (n = a.length > 0 ? new(o.bind.apply(o, r([void 0, this.context.getModel()], a))) : new o(this.context.getModel(), this.context.getView())).controller = this.context.getController(), n.IsStayInMemory && (this._commandsInMemory[e] = n)
                            } else n = this._commandsInMemory[e];
                            return n.execute(t)
                        }, e.prototype.replaceCommand = function(e, t) {
                            if (!this.hasCommand(e)) throw new Error("Error while replacing not existing command with name:" + e);
                            this.removeCommand(e), this.registerCommand(e, t)
                        }, e.prototype.removeCommand = function(e) {
                            this.hasCommand(e) && (i.default(this._commandsInMemory[e]) || (this._commandsInMemory[e].dispose(), delete this._commandsInMemory[e]), delete this._commandMap[e])
                        }, e.prototype.dispose = function() {
                            if (null !== this._commandsInMemory)
                                for (var e in this._commandsInMemory) this._commandsInMemory[e].dispose(), delete this._commandsInMemory[e]
                        }, e
                    }();
                t.AbstractController = a
            },
            5272: function(e, t, n) {
                "use strict";
                var r = this && this.__importDefault || function(e) {
                    return e && e.__esModule ? e : {
                        default: e
                    }
                };
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.AbstractModel = void 0;
                var o = r(n(4293)),
                    i = r(n(855)),
                    a = function() {
                        function e() {
                            this.init()
                        }
                        return Object.defineProperty(e.prototype, "width", {
                            get: function() {
                                return this._width
                            },
                            enumerable: !1,
                            configurable: !0
                        }), Object.defineProperty(e.prototype, "height", {
                            get: function() {
                                return this._height
                            },
                            enumerable: !1,
                            configurable: !0
                        }), e.prototype.init = function() {
                            this.isActive = !1, this._htmlElement = document.getElementById(this.htmlElementName), o.default(this._htmlElement) || (this._width = this._htmlElement.offsetWidth, this._height = this._htmlElement.offsetHeight)
                        }, e.prototype.onResize = function(e, t) {
                            this._width = e, this._height = t
                        }, Object.defineProperty(e.prototype, "versionLib", {
                            get: function() {
                                return i.default.version
                            },
                            enumerable: !1,
                            configurable: !0
                        }), e
                    }();
                t.AbstractModel = a
            },
            4396: function(e, t, n) {
                "use strict";
                var r, o = this && this.__extends || (r = function(e, t) {
                    return (r = Object.setPrototypeOf || {
                            __proto__: []
                        }
                        instanceof Array && function(e, t) {
                            e.__proto__ = t
                        } || function(e, t) {
                            for (var n in t) Object.prototype.hasOwnProperty.call(t, n) && (e[n] = t[n])
                        })(e, t)
                }, function(e, t) {
                    if ("function" != typeof t && null !== t) throw new TypeError("Class extends value " + String(t) + " is not a constructor or null");

                    function n() {
                        this.constructor = e
                    }
                    r(e, t), e.prototype = null === t ? Object.create(t) : (n.prototype = t.prototype, new n)
                });
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.ActivatedCommand = void 0;
                var i = function(e) {
                    function t() {
                        return null !== e && e.apply(this, arguments) || this
                    }
                    return o(t, e), t.prototype.execute = function() {
                        this.model.isActive = !0
                    }, t
                }(n(2485).BaseCommand);
                t.ActivatedCommand = i
            },
            2485: function(e, t, n) {
                "use strict";
                var r, o = this && this.__extends || (r = function(e, t) {
                        return (r = Object.setPrototypeOf || {
                                __proto__: []
                            }
                            instanceof Array && function(e, t) {
                                e.__proto__ = t
                            } || function(e, t) {
                                for (var n in t) Object.prototype.hasOwnProperty.call(t, n) && (e[n] = t[n])
                            })(e, t)
                    }, function(e, t) {
                        if ("function" != typeof t && null !== t) throw new TypeError("Class extends value " + String(t) + " is not a constructor or null");

                        function n() {
                            this.constructor = e
                        }
                        r(e, t), e.prototype = null === t ? Object.create(t) : (n.prototype = t.prototype, new n)
                    }),
                    i = this && this.__spreadArray || function(e, t) {
                        for (var n = 0, r = t.length, o = e.length; n < r; n++, o++) e[o] = t[n];
                        return e
                    };
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.BaseCommand = void 0;
                var a = function(e) {
                    function t(t) {
                        for (var n = [], r = 1; r < arguments.length; r++) n[r - 1] = arguments[r];
                        var o = e.apply(this, i([t], n)) || this,
                            a = typeof o;
                        if ("BaseCommand" === a) throw new Error("BaseCommand can't an instance, because this is a base class, please use his extend classes for instance. ");
                        return o
                    }
                    return o(t, e), Object.defineProperty(t.prototype, "IsStayInMemory", {
                        get: function() {
                            return !0
                        },
                        enumerable: !1,
                        configurable: !0
                    }), Object.defineProperty(t.prototype, "views", {
                        get: function() {
                            return this._views
                        },
                        set: function(e) {
                            this._views = e
                        },
                        enumerable: !1,
                        configurable: !0
                    }), Object.defineProperty(t.prototype, "view", {
                        get: function() {
                            return this._view
                        },
                        set: function(e) {
                            this._view = e
                        },
                        enumerable: !1,
                        configurable: !0
                    }), Object.defineProperty(t.prototype, "model", {
                        get: function() {
                            return this._model
                        },
                        set: function(e) {
                            this._model = e
                        },
                        enumerable: !1,
                        configurable: !0
                    }), Object.defineProperty(t.prototype, "controller", {
                        get: function() {
                            return this._controller
                        },
                        set: function(e) {
                            this._controller = e
                        },
                        enumerable: !1,
                        configurable: !0
                    }), t.prototype.execute = function(e) {}, t.prototype.dispose = function() {
                        this._view = null, this._model = null
                    }, t
                }(n(369).AbstractCommand);
                t.BaseCommand = a
            },
            110: function(e, t, n) {
                "use strict";
                var r, o = this && this.__extends || (r = function(e, t) {
                    return (r = Object.setPrototypeOf || {
                            __proto__: []
                        }
                        instanceof Array && function(e, t) {
                            e.__proto__ = t
                        } || function(e, t) {
                            for (var n in t) Object.prototype.hasOwnProperty.call(t, n) && (e[n] = t[n])
                        })(e, t)
                }, function(e, t) {
                    if ("function" != typeof t && null !== t) throw new TypeError("Class extends value " + String(t) + " is not a constructor or null");

                    function n() {
                        this.constructor = e
                    }
                    r(e, t), e.prototype = null === t ? Object.create(t) : (n.prototype = t.prototype, new n)
                });
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.DeactivatedCommand = void 0;
                var i = function(e) {
                    function t() {
                        return null !== e && e.apply(this, arguments) || this
                    }
                    return o(t, e), t.prototype.execute = function() {
                        this.model.isActive = !1, this.view.setEnabled(!1)
                    }, t
                }(n(2485).BaseCommand);
                t.DeactivatedCommand = i
            },
            5378: function(e, t, n) {
                "use strict";
                var r, o = this && this.__extends || (r = function(e, t) {
                    return (r = Object.setPrototypeOf || {
                            __proto__: []
                        }
                        instanceof Array && function(e, t) {
                            e.__proto__ = t
                        } || function(e, t) {
                            for (var n in t) Object.prototype.hasOwnProperty.call(t, n) && (e[n] = t[n])
                        })(e, t)
                }, function(e, t) {
                    if ("function" != typeof t && null !== t) throw new TypeError("Class extends value " + String(t) + " is not a constructor or null");

                    function n() {
                        this.constructor = e
                    }
                    r(e, t), e.prototype = null === t ? Object.create(t) : (n.prototype = t.prototype, new n)
                });
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.PrintVersionCommand = void 0;
                var i = n(9945),
                    a = function(e) {
                        function t() {
                            return null !== e && e.apply(this, arguments) || this
                        }
                        return o(t, e), t.prototype.execute = function() {
                            i.Log.forceInfoConsolech("Overlay Lib version", this.model.versionLib)
                        }, t
                    }(n(2485).BaseCommand);
                t.PrintVersionCommand = a
            },
            2318: function(e, t, n) {
                "use strict";
                var r, o = this && this.__extends || (r = function(e, t) {
                        return (r = Object.setPrototypeOf || {
                                __proto__: []
                            }
                            instanceof Array && function(e, t) {
                                e.__proto__ = t
                            } || function(e, t) {
                                for (var n in t) Object.prototype.hasOwnProperty.call(t, n) && (e[n] = t[n])
                            })(e, t)
                    }, function(e, t) {
                        if ("function" != typeof t && null !== t) throw new TypeError("Class extends value " + String(t) + " is not a constructor or null");

                        function n() {
                            this.constructor = e
                        }
                        r(e, t), e.prototype = null === t ? Object.create(t) : (n.prototype = t.prototype, new n)
                    }),
                    i = this && this.__importDefault || function(e) {
                        return e && e.__esModule ? e : {
                            default: e
                        }
                    };
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.ResizeCommand = void 0;
                var a = i(n(4293)),
                    s = function(e) {
                        function t() {
                            return null !== e && e.apply(this, arguments) || this
                        }
                        return o(t, e), t.prototype.execute = function() {
                            var e = document.getElementById(this.model.htmlElementName);
                            a.default(e) || (this.model.onResize(e.offsetWidth, e.offsetHeight), this.view.resize(this.model.width, this.model.height))
                        }, t
                    }(n(2485).BaseCommand);
                t.ResizeCommand = s
            },
            8631: function(e, t, n) {
                "use strict";
                var r, o = this && this.__extends || (r = function(e, t) {
                    return (r = Object.setPrototypeOf || {
                            __proto__: []
                        }
                        instanceof Array && function(e, t) {
                            e.__proto__ = t
                        } || function(e, t) {
                            for (var n in t) Object.prototype.hasOwnProperty.call(t, n) && (e[n] = t[n])
                        })(e, t)
                }, function(e, t) {
                    if ("function" != typeof t && null !== t) throw new TypeError("Class extends value " + String(t) + " is not a constructor or null");

                    function n() {
                        this.constructor = e
                    }
                    r(e, t), e.prototype = null === t ? Object.create(t) : (n.prototype = t.prototype, new n)
                });
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.ViewRedrawCommand = void 0;
                var i = function(e) {
                    function t() {
                        return null !== e && e.apply(this, arguments) || this
                    }
                    return o(t, e), t.prototype.execute = function() {
                        this.view.onEnterFrameRedraw(this.model.deltaTime)
                    }, t
                }(n(2485).BaseCommand);
                t.ViewRedrawCommand = i
            },
            660: function(e, t, n) {
                "use strict";
                var r = this && this.__importDefault || function(e) {
                    return e && e.__esModule ? e : {
                        default: e
                    }
                };
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.ActionParameterUtil = void 0;
                var o = r(n(3311)),
                    i = r(n(4293)),
                    a = r(n(8613)),
                    s = n(9945),
                    u = function() {
                        function e() {}
                        return e.getAction = function(e, t) {
                            if (!i.default(e) && !i.default(e.actions)) return o.default(e.actions, (function(e) {
                                return e.actionName === t
                            }));
                            s.Log.warning("Action is not ready. Probably data is still not loaded")
                        }, e.getActionParameter = function(t, n, r) {
                            var o = [],
                                a = e.getAction(t, n);
                            return i.default(a) || a.parameters.forEach((function(e) {
                                e.name === r && (o = e.values)
                            })), o
                        }, e.getActionParameterValue = function(e, t) {
                            return a.default(o.default(e, (function(e) {
                                return e.name === t
                            })), "value")
                        }, e
                    }();
                t.ActionParameterUtil = u
            },
            7949: function(e, t) {
                "use strict";
                Object.defineProperty(t, "__esModule", {
                        value: !0
                    }), t.CssUtil = void 0,
                    function(e) {
                        e.add = function(e, t) {
                            for (var n in t) e.style[n] = t[n]
                        }
                    }(t.CssUtil || (t.CssUtil = {}))
            },
            64: function(e, t, n) {
                "use strict";
                var r = this && this.__importDefault || function(e) {
                    return e && e.__esModule ? e : {
                        default: e
                    }
                };
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.HtmlUtil = void 0;
                var o = r(n(4486)),
                    i = n(6667),
                    a = n(5402),
                    s = n(9979);
                ! function(e) {
                    e.addScript = function(e, t, n, r) {
                        void 0 === r && (r = !0), r && (r = !s.UrlUtil.checkUrlParamValue(a.EUrlParamName.CACHE_BUSTER, !1));
                        var o = document.createElement("script");
                        o.onload = t, o.onerror = n, o.type = "text/javascript", o.src = e + (r ? "?hash=" + Math.random().toString(36).substr(2, 9) : ""), document.head.appendChild(o)
                    }, e.includeHTML = function(t, n, r, o) {
                        void 0 === r && (r = ""), void 0 === o && (o = []);
                        var a = e.checkForAttribute(t);
                        if (a) {
                            var s = a.getAttribute(t);
                            if (s) return void fetch(r + s).then((function(e) {
                                var t = e.text();
                                return o.push(t), t
                            })).then((function(s) {
                                a.insertAdjacentHTML(i.EEventName.BEFOREEND, s), a.removeAttribute(t), e.includeHTML(t, n, r, o)
                            })).catch((function() {}))
                        } else Promise.all(o).then((function() {
                            n()
                        }))
                    }, e.checkForAttribute = function(e) {
                        for (var t = document.getElementsByTagName("*"), n = 0; n < t.length; n++) {
                            var r = t[n];
                            if (r.getAttribute(e)) return r
                        }
                    }, e.addStyle = function(e, t, n, r, o) {
                        void 0 === t && (t = ""), void 0 === n && (n = !0), n && (n = !s.UrlUtil.checkUrlParamValue(a.EUrlParamName.CACHE_BUSTER, !1));
                        var i = document.createElement("link");
                        i.onload = r, i.onerror = o, i.rel = "stylesheet", i.type = "text/css", i.href = t + e + (n ? "?hash=" + Math.random().toString(36).substr(2, 9) : ""), document.head.appendChild(i)
                    }, e.addInlineStyles = function(e, t, n) {
                        var r = document.createElement("style");
                        r.onload = t, r.onerror = n, r.textContent = e, document.head.appendChild(r)
                    }, e.isFileExistOnServer = function(e, t, n) {
                        fetch(e).then(t).catch(n)
                    }, e.setClassNames = function(e, t) {
                        o.default(t, (function(t, n) {
                            t ? e.classList.add(n) : e.classList.remove(n)
                        }))
                    }
                }(t.HtmlUtil || (t.HtmlUtil = {}))
            },
            9945: function(e, t, n) {
                "use strict";
                var r = this && this.__importDefault || function(e) {
                    return e && e.__esModule ? e : {
                        default: e
                    }
                };
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.Log = void 0;
                var o = r(n(7037)),
                    i = n(5402),
                    a = n(9979),
                    s = function() {
                        function e() {}
                        return e.debug = function(t) {
                            e.disableConsole || console.debug.apply(console, e.prepareArgs(t, "#660000"))
                        }, e.debugch = function(t, n) {
                            e.disableConsole || console.debug.apply(console, e.prepareChArgs(t, n, "#660000"))
                        }, e.error = function(t) {
                            e.disableConsole && !e.forceShowErrors || console.error.apply(console, e.prepareArgs(t, "#DD0000"))
                        }, e.errorch = function(t, n) {
                            e.disableConsole && !e.forceShowErrors || console.error.apply(console, e.prepareChArgs(t, n, "#DD0000"))
                        }, e.info = function(t) {
                            e.disableConsole || console.info.apply(console, e.prepareArgs(t, "#44A044"))
                        }, e.infoch = function(t, n) {
                            e.disableConsole || console.info.apply(console, e.prepareChArgs(t, n, "#44A044"))
                        }, e.warning = function(t) {
                            e.disableConsole && !e.forceShowErrors || console.warn.apply(console, e.prepareArgs(t, "#ff843d"))
                        }, e.warningch = function(t, n) {
                            e.disableConsole && !e.forceShowErrors || console.warn.apply(console, e.prepareChArgs(t, n, "#ff843d"))
                        }, e.log = function(t) {
                            e.disableConsole || console.log(t)
                        }, e.logch = function(t, n) {
                            e.disableConsole || console.log.apply(console, e.prepareChArgs(t, n))
                        }, e.forceLogConsolech = function(t, n) {
                            console.log.apply(console, e.prepareChArgs(t, n))
                        }, e.forceInfoConsolech = function(t, n) {
                            console.info.apply(console, e.prepareChArgs(t, n, "#44A044"))
                        }, e.prepareArgs = function(e, t) {
                            return t && o.default(e) ? ["%c" + e, "color:" + t] : [e]
                        }, e.prepareChArgs = function(e, t, n) {
                            return n ? n && o.default(t) ? ["%c" + e + " : " + t, "color:" + n] : ["%c" + e + " :", "color:" + n, t] : [e + " :", t]
                        }, e.disableConsole = !a.UrlUtil.checkUrlParamValue(i.EUrlParamName.ENABLE_LOGS, !0), e.forceShowErrors = !0, e
                    }();
                t.Log = s
            },
            6071: function(e, t) {
                "use strict";
                Object.defineProperty(t, "__esModule", {
                        value: !0
                    }), t.PlatformDeviceSpecs = void 0,
                    function(e) {
                        e.IPHONE_12PRO_MAX = {
                            screen: {
                                width: 428,
                                height: 926
                            },
                            devicePixelRatio: 3
                        }, e.IPHONE_12_12PRO = {
                            screen: {
                                width: 390,
                                height: 844
                            },
                            devicePixelRatio: 3
                        }, e.IPHONE_12MINI = {
                            screen: {
                                width: 375,
                                height: 812
                            },
                            devicePixelRatio: 3
                        }, e.IPHONE_XS_11PRO_MAX = {
                            screen: {
                                width: 414,
                                height: 896
                            },
                            devicePixelRatio: 3
                        }, e.IPHONE_X_XS_11PRO = {
                            screen: {
                                width: 375,
                                height: 812
                            },
                            devicePixelRatio: 3
                        }, e.IPHONE_XR_11 = {
                            screen: {
                                width: 414,
                                height: 896
                            },
                            devicePixelRatio: 2
                        }, e.IPHONE_6_7_8_PLUS = {
                            screen: {
                                width: 375,
                                height: 667
                            },
                            devicePixelRatio: 2
                        }, e.IPHONE_6_7_8 = {
                            screen: {
                                width: 375,
                                height: 667
                            },
                            devicePixelRatio: 2
                        }
                    }(t.PlatformDeviceSpecs || (t.PlatformDeviceSpecs = {}))
            },
            6645: function(e, t, n) {
                "use strict";
                var r = this && this.__importDefault || function(e) {
                    return e && e.__esModule ? e : {
                        default: e
                    }
                };
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.PlatformUtil = void 0;
                var o = r(n(4293)),
                    i = r(n(1795)),
                    a = n(7863),
                    s = n(8323),
                    u = n(6071),
                    c = n(2906),
                    l = n(1302);
                ! function(e) {
                    e.isIOS = i.default.os.family === s.EPlatformOSName.IOS, e.isAndroid = i.default.os.family === s.EPlatformOSName.ANDROID, e.isIPhone = i.default.product === s.EPlatformProductName.IPHONE, e.getMajorVersionOfOS = function() {
                        return i.default.os.version.split(".")[0]
                    }, e.getPlatformEventName = function(e) {
                        return i.default.name === s.EPlatformBrowserName.IE || i.default.name === s.EPlatformBrowserName.MICROSOFT_EDGE ? "ms" + e : i.default.name === s.EPlatformBrowserName.FIREFOX_FOR_IOS || i.default.name === s.EPlatformBrowserName.FIREFOX ? "moz" + e : i.default.name === s.EPlatformBrowserName.CHROME || i.default.name === s.EPlatformBrowserName.CHROME_MOBILE || i.default.name === s.EPlatformBrowserName.SAFARI || i.default.name === s.EPlatformBrowserName.OPERA ? "webkit" + e : e
                    }, e.isFirefox = "undefined" != typeof InstallTrigger, e.isSafari = /constructor/i.test(window.HTMLElement) || "[object SafariRemoteNotification]" === (!window.safari || "undefined" != typeof safari && safari.pushNotification).toString(), e.isIE = !!document.documentMode, e.isEdge = !e.isIE && !!window.StyleMedia, e.isChrome = !(!window.chrome || !window.chrome.webstore && !window.chrome.runtime), e.isEdgeChromium = e.isChrome && -1 !== navigator.userAgent.indexOf("Edg"), e.isOpera = !!window.opera || navigator.userAgent.indexOf("OPR/") >= 0, e.isFirefoxiOS = i.default.name === s.EPlatformName.FIREFOX_FOR_IOS, e.isMobileDevice = !o.default(window.orientation), e.isMobileSafari = e.isMobileDevice && i.default.name === s.EPlatformBrowserName.SAFARI, e.isMobileChrome = e.isMobileDevice && i.default.name === s.EPlatformBrowserName.CHROME_MOBILE, e.isMobileFirefox = navigator.userAgent.indexOf("Android") > -1 && e.isFirefox, e.getDeviceType = function() {
                        return /(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i.test(navigator.userAgent) ? a.EDeviceName.TABLET : /Mobile|iP(hone|od|ad)|Android|BlackBerry|IEMobile|Kindle|Silk-Accelerated|(hpw|web)OS|Opera M(obi|ini)/.test(navigator.userAgent) ? a.EDeviceName.MOBILE : a.EDeviceName.DESKTOP
                    }, e.browserName = i.default.name, e.browserVersion = i.default.version, e.browserMajorVersion = Number(e.browserVersion.split(".")[0]), e.isMobilePhoneDevice = e.getDeviceType() === a.EDeviceName.MOBILE, e.isTabletDevice = e.getDeviceType() === a.EDeviceName.TABLET, e.isDesktopDevice = e.getDeviceType() === a.EDeviceName.DESKTOP, e.orientation = function() {
                        return 0 === window.orientation || 180 === window.orientation ? l.EOrientationType.PORTRAIT : l.EOrientationType.LANDSCAPE
                    }, e.screenWidth = function() {
                        return window.screen[e.orientation() === l.EOrientationType.PORTRAIT ? c.EDimensions.WIDTH : c.EDimensions.HEIGHT]
                    }, e.screenHeight = function() {
                        return window.screen[e.orientation() === l.EOrientationType.PORTRAIT ? c.EDimensions.HEIGHT : c.EDimensions.WIDTH]
                    }, e.isIPhoneX = function() {
                        if (!e.isIPhone) return !1;
                        var t = u.PlatformDeviceSpecs.IPHONE_X_XS_11PRO,
                            n = t.screen.width * t.devicePixelRatio,
                            r = t.screen.height * t.devicePixelRatio,
                            o = u.PlatformDeviceSpecs.IPHONE_XR_11,
                            i = o.screen.width * o.devicePixelRatio,
                            a = o.screen.height * o.devicePixelRatio,
                            s = window.devicePixelRatio || 1,
                            c = e.screenWidth() * s,
                            l = e.screenHeight() * s;
                        return c === i && l === a || c >= n && l >= r
                    }
                }(t.PlatformUtil || (t.PlatformUtil = {}))
            },
            4019: function(e, t, n) {
                "use strict";
                var r = this && this.__importDefault || function(e) {
                    return e && e.__esModule ? e : {
                        default: e
                    }
                };
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.StringUtil = void 0;
                var o = r(n(1865)),
                    i = function() {
                        function e() {}
                        return e.isValidJSONString = function(e) {
                            try {
                                JSON.parse(e)
                            } catch (e) {
                                return !1
                            }
                            return !0
                        }, e.toSnakeCase = function(e) {
                            return o.default(e)
                        }, e
                    }();
                t.StringUtil = i
            },
            3431: function(e, t) {
                "use strict";
                Object.defineProperty(t, "__esModule", {
                        value: !0
                    }), t.TypesUtil = void 0,
                    function(e) {
                        e.isTruthy = function(e) {
                            return "true" === e || 1 === e || !0 === e
                        }
                    }(t.TypesUtil || (t.TypesUtil = {}))
            },
            9979: function(e, t, n) {
                "use strict";
                var r = this && this.__importDefault || function(e) {
                    return e && e.__esModule ? e : {
                        default: e
                    }
                };
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.UrlUtil = void 0;
                var o = r(n(4293));
                ! function(e) {
                    e.urlParams = function(e, t) {
                        var n = e ? e.split("?")[1] : window.location.search.slice(1),
                            r = {};
                        if (n)
                            for (var i = (n = n.split("#")[0]).split("&"), a = 0; a < i.length; a++) {
                                var s = i[a].split("="),
                                    u = s[0],
                                    c = void 0 === s[1] ? "true" : s[1];
                                if ((u = u.toLowerCase()).match(/\[(\d+)?\]$/)) {
                                    var l = u.replace(/\[(\d+)?\]/, "");
                                    r[l] || (r[l] = []);
                                    var d = r[l];
                                    if (u.match(/\[\d+\]$/)) d[Number(/\[(\d+)\]/.exec(u)[1])] = c;
                                    else d.push(c)
                                } else {
                                    var f = r[u];
                                    f ? f && "string" == typeof f ? r[u] = [f, c] : r[u].push(String(c)) : r[u] = c
                                }
                            }
                        var p = r[t.toLowerCase()];
                        return o.default(p) ? p : String(p)
                    }, e.urlDecodeParams = function(t, n) {
                        var r = e.urlParams(t, n);
                        if (r && r.length) {
                            var o = (r = decodeURIComponent(r)).length;
                            '"' !== r[0] && "'" !== r[0] || (r = r.slice(1)), '"' !== r[o - 1] && "'" !== r[o - 1] || (r = r.slice(0, -1))
                        }
                        return r || ""
                    }, e.convertUrlToDomainBaseUrl = function(e, t, n) {
                        void 0 === n && (n = !0);
                        var r = t,
                            o = e.split("/"),
                            i = n ? o[0] + "//" + o[1] + o[2] : e;
                        return -1 !== r.indexOf("http") ? (r = (r = (r = r.replace("http://", "")).replace("https://", "")).split("/")[0], i + t.split(r)[1]) : i + t
                    }, e.checkUrlIncludeParam = function(e, t) {
                        return void 0 === t && (t = window.location.href), -1 !== t.indexOf("&" + e + "=") || -1 !== t.indexOf("?" + e + "=")
                    }, e.checkUrlParamValue = function(e, t, n) {
                        return void 0 === n && (n = window.location.href), void 0 === t ? -1 !== n.indexOf("&" + e + "=") || -1 !== n.indexOf("?" + e + "=") : -1 !== n.indexOf("&" + e + "=" + t) || -1 !== n.indexOf("?" + e + "=" + t)
                    }, e.getUrlParamsFromLocationHref = function(e) {
                        if (-1 !== e.indexOf("?")) {
                            var t = {},
                                n = e.split("?");
                            return (n.length > 1 ? n[1].split("&") : n[0].split("&")).forEach((function(e) {
                                var n = e.split("=");
                                t[n[0]] = n[1]
                            })), t
                        }
                    }, e.isUrl = function(e) {
                        return !!/^(http|https):\/\//gm.exec(e)
                    }, e.getLicenseIdFromUrl = function(e) {
                        return +(e = (e = e.replace("http://", "")).replace("https://", "")).split("/")[1]
                    }
                }(t.UrlUtil || (t.UrlUtil = {}))
            },
            9222: function(e, t) {
                "use strict";
                Object.defineProperty(t, "__esModule", {
                        value: !0
                    }), t.EFontTypes = void 0,
                    function(e) {
                        e.WOFF = "woff", e.WOFF2 = "woff2", e.TTF = "ttf", e.EOT = "eot"
                    }(t.EFontTypes || (t.EFontTypes = {}))
            },
            1039: function(e, t, n) {
                "use strict";
                var r = this && this.__awaiter || function(e, t, n, r) {
                        return new(n || (n = Promise))((function(o, i) {
                            function a(e) {
                                try {
                                    u(r.next(e))
                                } catch (e) {
                                    i(e)
                                }
                            }

                            function s(e) {
                                try {
                                    u(r.throw(e))
                                } catch (e) {
                                    i(e)
                                }
                            }

                            function u(e) {
                                var t;
                                e.done ? o(e.value) : (t = e.value, t instanceof n ? t : new n((function(e) {
                                    e(t)
                                }))).then(a, s)
                            }
                            u((r = r.apply(e, t || [])).next())
                        }))
                    },
                    o = this && this.__generator || function(e, t) {
                        var n, r, o, i, a = {
                            label: 0,
                            sent: function() {
                                if (1 & o[0]) throw o[1];
                                return o[1]
                            },
                            trys: [],
                            ops: []
                        };
                        return i = {
                            next: s(0),
                            throw: s(1),
                            return: s(2)
                        }, "function" == typeof Symbol && (i[Symbol.iterator] = function() {
                            return this
                        }), i;

                        function s(i) {
                            return function(s) {
                                return function(i) {
                                    if (n) throw new TypeError("Generator is already executing.");
                                    for (; a;) try {
                                        if (n = 1, r && (o = 2 & i[0] ? r.return : i[0] ? r.throw || ((o = r.return) && o.call(r), 0) : r.next) && !(o = o.call(r, i[1])).done) return o;
                                        switch (r = 0, o && (i = [2 & i[0], o.value]), i[0]) {
                                            case 0:
                                            case 1:
                                                o = i;
                                                break;
                                            case 4:
                                                return a.label++, {
                                                    value: i[1],
                                                    done: !1
                                                };
                                            case 5:
                                                a.label++, r = i[1], i = [0];
                                                continue;
                                            case 7:
                                                i = a.ops.pop(), a.trys.pop();
                                                continue;
                                            default:
                                                if (!(o = a.trys, (o = o.length > 0 && o[o.length - 1]) || 6 !== i[0] && 2 !== i[0])) {
                                                    a = 0;
                                                    continue
                                                }
                                                if (3 === i[0] && (!o || i[1] > o[0] && i[1] < o[3])) {
                                                    a.label = i[1];
                                                    break
                                                }
                                                if (6 === i[0] && a.label < o[1]) {
                                                    a.label = o[1], o = i;
                                                    break
                                                }
                                                if (o && a.label < o[2]) {
                                                    a.label = o[2], a.ops.push(i);
                                                    break
                                                }
                                                o[2] && a.ops.pop(), a.trys.pop();
                                                continue
                                        }
                                        i = t.call(e, a)
                                    } catch (e) {
                                        i = [6, e], r = 0
                                    } finally {
                                        n = o = 0
                                    }
                                    if (5 & i[0]) throw i[1];
                                    return {
                                        value: i[0] ? i[1] : void 0,
                                        done: !0
                                    }
                                }([i, s])
                            }
                        }
                    },
                    i = this && this.__importDefault || function(e) {
                        return e && e.__esModule ? e : {
                            default: e
                        }
                    };
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.FontFaceUtil = void 0;
                var a = i(n(7347)),
                    s = i(n(6073)),
                    u = i(n(4293)),
                    c = n(9945),
                    l = n(9222),
                    d = {
                        woff: !0,
                        woff2: !0,
                        ttf: !0,
                        eot: !0
                    },
                    f = function() {
                        function e() {}
                        return e.add = function(e) {
                            var t = e.path,
                                n = e.fonts,
                                r = e.nodeId,
                                o = e.fontTypesToLoad,
                                i = void 0 === o ? d : o;
                            if (0 !== n.length) {
                                var a = document.createElement("style");
                                r && (a.id = r);
                                var u = function(e, n) {
                                        var r = function(e) {
                                            switch (e) {
                                                case l.EFontTypes.EOT:
                                                    return "embedded-opentype";
                                                case l.EFontTypes.TTF:
                                                    return "truetype";
                                                default:
                                                    return e
                                            }
                                        }(n);
                                        return 'url("' + (t + e) + "." + n + '") format("' + r + '"),'
                                    },
                                    f = "";
                                s.default(n, (function(e) {
                                    f += '\n\t\t\t\t@font-face {\n\t\t\t\t    font-family: "' + e.name + '";\n\t\t\t\t    src: ', f += i.woff ? "\n" + u(e.file, l.EFontTypes.WOFF) : "", f += i.woff2 ? "\n" + u(e.file, l.EFontTypes.WOFF2) : "", f += i.ttf ? "\n" + u(e.file, l.EFontTypes.TTF) : "", f = (f += i.eot ? "\n" + u(e.file, l.EFontTypes.EOT) : "").slice(0, -1), f += ";", f += "\n\t\t\t\t    font-weight: normal;\n\t\t\t\t    font-style: normal;\n\t\t\t\t}"
                                })), a.appendChild(document.createTextNode(f)), document.head.appendChild(a), c.Log.log("Fonts style added")
                            }
                        }, e.checkLoading = function(e) {
                            var t = e.fonts,
                                n = e.signal,
                                i = e.commandName,
                                l = e.timeout;
                            return r(this, void 0, void 0, (function() {
                                var e, r, d, f;
                                return o(this, (function(o) {
                                    switch (o.label) {
                                        case 0:
                                            e = null != i ? i : "FontFaceUtil", r = null != l ? l : 5e3, d = new Array, s.default(t, (function(t) {
                                                var n = new a.default(t.name).load(null, r);
                                                d.push(n), n.then((function() {
                                                    c.Log.log("Font " + t.name + " was loaded!")
                                                }), (function() {
                                                    c.Log.warningch(e, "Error while loading font " + t.file)
                                                }))
                                            })), o.label = 1;
                                        case 1:
                                            return o.trys.push([1, 3, , 4]), [4, Promise.all(d)];
                                        case 2:
                                            return o.sent(), c.Log.logch(e, "All Fonts loaded"), u.default(n) || n.dispatch(!0), [3, 4];
                                        case 3:
                                            return f = o.sent(), c.Log.warningch(e, f), u.default(n) || n.dispatch(!1), [3, 4];
                                        case 4:
                                            return [2]
                                    }
                                }))
                            }))
                        }, e
                    }();
                t.FontFaceUtil = f
            },
            8394: function(e, t, n) {
                "use strict";
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.CoreInteractionManager = void 0;
                var r = n(2374),
                    o = function() {
                        function e() {
                            this._supportsTouchEvents = "ontouchstart" in window, this._supportsPointerEvents = !!window.PointerEvent
                        }
                        return Object.defineProperty(e.prototype, "supportsPointerEvents", {
                            get: function() {
                                return this._supportsPointerEvents
                            },
                            enumerable: !1,
                            configurable: !0
                        }), Object.defineProperty(e.prototype, "supportsTouchEvents", {
                            get: function() {
                                return this._supportsTouchEvents
                            },
                            enumerable: !1,
                            configurable: !0
                        }), Object.defineProperty(e.prototype, "UP", {
                            get: function() {
                                return this.supportsPointerEvents ? r.CoreInteractionEventNames.POINTER_UP : this.supportsTouchEvents ? r.CoreInteractionEventNames.TOUCH_END : r.CoreInteractionEventNames.MOUSE_UP
                            },
                            enumerable: !1,
                            configurable: !0
                        }), Object.defineProperty(e.prototype, "DOWN", {
                            get: function() {
                                return this.supportsPointerEvents ? r.CoreInteractionEventNames.POINTER_DOWN : this.supportsTouchEvents ? r.CoreInteractionEventNames.TOUCH_START : r.CoreInteractionEventNames.MOUSE_DOWN
                            },
                            enumerable: !1,
                            configurable: !0
                        }), Object.defineProperty(e.prototype, "MOVE", {
                            get: function() {
                                return this.supportsPointerEvents ? r.CoreInteractionEventNames.POINTER_MOVE : this.supportsTouchEvents ? r.CoreInteractionEventNames.TOUCH_MOVE : r.CoreInteractionEventNames.MOUSE_MOVE
                            },
                            enumerable: !1,
                            configurable: !0
                        }), Object.defineProperty(e.prototype, "OVER", {
                            get: function() {
                                return this.supportsPointerEvents ? r.CoreInteractionEventNames.POINTER_OVER : r.CoreInteractionEventNames.MOUSE_OVER
                            },
                            enumerable: !1,
                            configurable: !0
                        }), Object.defineProperty(e.prototype, "OUT", {
                            get: function() {
                                return this.supportsPointerEvents ? r.CoreInteractionEventNames.POINTER_OUT : r.CoreInteractionEventNames.MOUSE_OUT
                            },
                            enumerable: !1,
                            configurable: !0
                        }), e
                    }();
                t.CoreInteractionManager = o
            },
            2374: function(e, t) {
                "use strict";
                Object.defineProperty(t, "__esModule", {
                        value: !0
                    }), t.CoreInteractionEventNames = void 0,
                    function(e) {
                        e.POINTER_UP = "pointerup", e.POINTER_DOWN = "pointerdown", e.POINTER_MOVE = "pointermove", e.POINTER_OVER = "pointerover", e.POINTER_OUT = "pointerout", e.TOUCH_START = "touchstart", e.TOUCH_END = "touchend", e.TOUCH_MOVE = "touchmove", e.MOUSE_UP = "mouseup", e.MOUSE_DOWN = "mousedown", e.MOUSE_MOVE = "mousemove", e.MOUSE_OVER = "mouseover", e.MOUSE_OUT = "mouseout", e.KEY_DOWN = "keydown"
                    }(t.CoreInteractionEventNames || (t.CoreInteractionEventNames = {}))
            },
            2906: function(e, t, n) {
                "use strict";
                var r = this && this.__importDefault || function(e) {
                    return e && e.__esModule ? e : {
                        default: e
                    }
                };
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.IOSScreenManager = t.EScreenViewMode = t.EDimensions = t.EOrientation = void 0;
                var o, i = r(n(4174)),
                    a = r(n(4293)),
                    s = n(3664),
                    u = n(7949),
                    c = n(9945),
                    l = n(6645),
                    d = n(1302);
                ! function(e) {
                    e.LANDSCAPE = "landscape", e.PORTRAIT = "portrait"
                }(t.EOrientation || (t.EOrientation = {})),
                function(e) {
                    e.WIDTH = "width", e.HEIGHT = "height"
                }(t.EDimensions || (t.EDimensions = {})),
                function(e) {
                    e.MINIMAL = "minimal", e.FULL = "full"
                }(o = t.EScreenViewMode || (t.EScreenViewMode = {}));
                var f = function() {
                    function e(e) {
                        var t = this;
                        this.orientationChangeManager = new d.OrientationChangeManager, this.onViewChange = new s.Signal, this._rulerElementID = "ios_ruler_minimal_view", this._config = e || {
                            viewport: !0,
                            width: {
                                portrait: window.screen.width,
                                landscape: window.screen.width
                            }
                        }, this._config.width.portrait || (this._config.width.portrait = window.screen.width), this._config.width.landscape || (this._config.width.landscape = window.screen.width), a.default(this._config.viewport) && (this._config.viewport = !0), this.appendRuler(), this.orientationChangeManager.onOrientationChangeEnd.add((function(e) {
                            c.Log.log("orientation: " + e), c.Log.log("viewport-width: " + t.getViewportWidth()), c.Log.log("viewport-height: " + t.getViewportHeight()), c.Log.log("scale: " + t.getScale()), c.Log.log("is-minimal-view: " + (l.PlatformUtil.isIOS ? t.isMinimalView() ? "yes" : "no" : "N/A"))
                        })), this.updateViewport(), this.setupDOMEventListeners()
                    }
                    return e.prototype.manageViewport = function() {
                        return this._config.viewport
                    }, e.prototype.getViewportWidth = function() {
                        return this._config.width[this.orientationChangeManager.orientation]
                    }, e.prototype.getViewportHeight = function() {
                        return Math.round(this.getScreenHeight() / this.getScale())
                    }, e.prototype.getScale = function() {
                        return this.getScreenWidth() / this.getViewportWidth()
                    }, e.prototype.getOrientation = function() {
                        return this.orientationChangeManager.orientation
                    }, e.prototype.getScreenWidth = function() {
                        return l.PlatformUtil.screenWidth()
                    }, e.prototype.getScreenHeight = function() {
                        return l.PlatformUtil.screenHeight()
                    }, e.prototype.updateViewport = function() {
                        var e = this;
                        if (this.manageViewport()) {
                            var t = this.getViewportWidth(),
                                n = this.getScale(),
                                r = this.getNotchPadding(),
                                o = "width=device-width, height=device-height, initial-scale=" + n + ", minimum-scale=" + n + ", maximum-scale=" + n + ", user-scalable=no, viewport-fit=cover, target-densitydpi=device-dpi, minimal-ui";
                            r > 0 && (o = "width=" + (t - r) + ", initial-scale=" + n + ", minimum-scale=" + n + ", maximum-scale=" + n + ", user-scalable=no, target-densitydpi=device-dpi, minimal-ui");
                            var i = document.createElement("meta");
                            i.name = "viewport", i.content = o;
                            var a = window.document.head.querySelector('meta[name="viewport"]');
                            a ? (window.setTimeout((function(t) {
                                e.getNotchPadding() > 0 && o !== t && e.updateViewport()
                            }), 2e3, a.getAttribute("content")), a.parentNode.removeChild(a)) : window.setTimeout((function() {
                                e.updateViewport()
                            }), 1e3), window.document.head.appendChild(i)
                        }
                    }, e.prototype.isIPhoneX = function() {
                        return l.PlatformUtil.isIPhoneX()
                    }, e.prototype.getMinimalViewHeight = function() {
                        var e, t = this.orientationChangeManager.orientation;
                        return a.default(this.rulerElement) ? (c.Log.warningch("IOSScreenManager", "element " + this._rulerElementID + " wasn't detected! Manager can't recognize minimal view sizes!"), 0) : ((t === d.EOrientationType.PORTRAIT || t === d.EOrientationType.LANDSCAPE) && (e = this.rulerElement.offsetHeight), e)
                    }, e.prototype.getMinimalViewSize = function() {
                        var e = this.getViewportWidth();
                        return {
                            height: this.getMinimalViewHeight(),
                            width: e
                        }
                    }, e.prototype.getNotchPadding = function() {
                        var e = !1,
                            t = document.createElement("div");
                        if (CSS.supports("padding-left: env(safe-area-inset-left)") && CSS.supports("padding-left: env(safe-area-inset-right)") ? (t.style.paddingLeft = "env(safe-area-inset-left)", t.style.paddingRight = "env(safe-area-inset-right)", e = !0) : CSS.supports("padding-left: constant(safe-area-inset-left)") && CSS.supports("padding-left: constant(safe-area-inset-right)") && (t.style.paddingLeft = "constant(safe-area-inset-left)", t.style.paddingRight = "constant(safe-area-inset-right)", e = !0), e) {
                            document.body.appendChild(t);
                            var n = parseInt(window.getComputedStyle(t).paddingLeft),
                                r = parseInt(window.getComputedStyle(t).paddingRight);
                            return document.body.removeChild(t), n + r
                        }
                        return 0
                    }, e.prototype.isMinimalView = function() {
                        return i.default(this.getMinimalViewSize().height, window.innerHeight - 2, window.innerHeight + 2)
                    }, e.prototype.detectViewChange = function() {
                        var e = this.isMinimalView() ? o.MINIMAL : o.FULL;
                        this._lastView !== e && (this.onViewChange.dispatch({
                            viewName: e
                        }), this._lastView = e)
                    }, e.prototype.setupDOMEventListeners = function() {
                        var e, t = this;
                        window.matchMedia("(orientation: portrait)").addListener((function() {
                            e = !0
                        })), this.orientationChangeManager.onOrientationChangeEnd.add((function() {
                            e = !1, t.updateViewport(), t.detectViewChange()
                        })), window.addEventListener("orientationchange", (function() {
                            t.updateViewport()
                        })), window.addEventListener("resize", (function() {
                            e || t.detectViewChange()
                        })), window.addEventListener("scroll", (function() {
                            e || t.detectViewChange()
                        })), window.setTimeout((function() {
                            t.detectViewChange()
                        }))
                    }, e.prototype.appendRuler = function() {
                        var e = document.createElement("div");
                        e.id = this._rulerElementID, u.CssUtil.add(e, {
                            position: "fixed",
                            top: "0",
                            left: "0",
                            width: "100%",
                            minHeight: "100vh",
                            margin: "0",
                            padding: "0",
                            overflow: "auto",
                            userSelect: "none",
                            touchAction: "none",
                            pointerEvents: "none"
                        }), document.body.appendChild(e)
                    }, Object.defineProperty(e.prototype, "rulerElement", {
                        get: function() {
                            return document.body.querySelector("#" + this._rulerElementID)
                        },
                        enumerable: !1,
                        configurable: !0
                    }), e
                }();
                t.IOSScreenManager = f
            },
            1302: function(e, t, n) {
                "use strict";
                var r = this && this.__importDefault || function(e) {
                    return e && e.__esModule ? e : {
                        default: e
                    }
                };
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.OrientationChangeManager = t.EOrientationType = void 0;
                var o = r(n(4293)),
                    i = n(3664),
                    a = n(9945),
                    s = n(6645);
                ! function(e) {
                    e.LANDSCAPE = "landscape", e.PORTRAIT = "portrait"
                }(t.EOrientationType || (t.EOrientationType = {}));
                var u = function() {
                    function e(e) {
                        var t = this;
                        this.onOrientaationChengeEnd = new i.Signal, this.onOrientationChangeEnd = new i.Signal, this._config = e || {
                            noChangeCountToEnd: 100,
                            noEndTimeout: 1e3,
                            debug: !1
                        }, window.addEventListener("orientationchange", (function() {
                            var e, n, r, i, s, u = function(r) {
                                clearInterval(e), clearTimeout(n), e = null, n = null, r && (t.onOrientaationChengeEnd.dispatch(), t.onOrientationChangeEnd.dispatch(t.orientation))
                            };
                            o.default(t._lastEnd) || t._lastEnd(!1), t._lastEnd = u, e = window.setInterval((function() {
                                window.innerWidth === r && window.innerHeight === i ? ++s === t._config.noChangeCountToEnd && (t._config.debug && a.Log.debug("setInterval"), u(!0)) : (r = window.innerWidth, i = window.innerHeight, s = 0)
                            })), n = window.setTimeout((function() {
                                t._config.debug && a.Log.debug("setTimeout"), u(!0)
                            }), t._config.noEndTimeout)
                        }))
                    }
                    return Object.defineProperty(e.prototype, "orientation", {
                        get: function() {
                            return s.PlatformUtil.orientation()
                        },
                        enumerable: !1,
                        configurable: !0
                    }), e
                }();
                t.OrientationChangeManager = u
            },
            7388: function(e, t) {
                "use strict";
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.CookiesManager = void 0;
                var n = function() {
                    function e() {
                        this._keys = {}, this._values = {}
                    }
                    return e.prototype.isAvailable = function() {
                        return navigator.cookieEnabled
                    }, e.prototype.initWithKey = function(e, t) {
                        return this.isAvailable() ? null === this.getByKey(e) ? (this.setByKey(e, t), t) : this.getByKey(e) : (this._values[e] = t, t)
                    }, e.prototype.setByKey = function(e, t, n) {
                        void 0 === n && (n = ""), this.isAvailable() ? document.cookie = e + "=" + encodeURIComponent(t) + n : this._values[e] = t
                    }, e.prototype.getByKey = function(e) {
                        if (!this.isAvailable()) return this._values[e];
                        var t = document.cookie.match(new RegExp("(?:^|; )" + e.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, "\\$1") + "=([^;]*)"));
                        return t ? decodeURIComponent(t[1]) : void 0
                    }, e.prototype.hasKey = function(e) {
                        return !(!e || "" === e) && Boolean(this.getByKey(e))
                    }, e.prototype.setOptions = function(e, t) {
                        if (this.hasKey(e)) {
                            var n = "";
                            for (var r in t) {
                                n += "; " + r;
                                var o = t[r];
                                !0 !== o && (n += "=" + o)
                            }
                            this.setByKey(e, this.getByKey(e), n)
                        }
                    }, e.prototype.setExpireDate = function(e, t) {
                        this.setOptions(e, {
                            expires: new Date(+t).toUTCString()
                        })
                    }, e.prototype.deleteByKey = function(e) {
                        this.setByKey(e, ""), this.setOptions(e, {
                            expires: -1
                        })
                    }, e
                }();
                t.CookiesManager = n
            },
            9272: function(e, t, n) {
                "use strict";
                var r = n(723),
                    o = [],
                    i = [],
                    a = r.makeRequestCallFromTimer((function() {
                        if (i.length) throw i.shift()
                    }));

                function s(e) {
                    var t;
                    (t = o.length ? o.pop() : new u).task = e, r(t)
                }

                function u() {
                    this.task = null
                }
                e.exports = s, u.prototype.call = function() {
                    try {
                        this.task.call()
                    } catch (e) {
                        s.onerror ? s.onerror(e) : (i.push(e), a())
                    } finally {
                        this.task = null, o[o.length] = this
                    }
                }
            },
            723: function(e, t, n) {
                "use strict";

                function r(e) {
                    i.length || (o(), !0), i[i.length] = e
                }
                e.exports = r;
                var o, i = [],
                    a = 0;

                function s() {
                    for (; a < i.length;) {
                        var e = a;
                        if (a += 1, i[e].call(), a > 1024) {
                            for (var t = 0, n = i.length - a; t < n; t++) i[t] = i[t + a];
                            i.length -= a, a = 0
                        }
                    }
                    i.length = 0, a = 0, !1
                }
                var u, c, l, d = void 0 !== n.g ? n.g : self,
                    f = d.MutationObserver || d.WebKitMutationObserver;

                function p(e) {
                    return function() {
                        var t = setTimeout(r, 0),
                            n = setInterval(r, 50);

                        function r() {
                            clearTimeout(t), clearInterval(n), e()
                        }
                    }
                }
                "function" == typeof f ? (u = 1, c = new f(s), l = document.createTextNode(""), c.observe(l, {
                    characterData: !0
                }), o = function() {
                    u = -u, l.data = u
                }) : o = p(s), r.requestFlush = o, r.makeRequestCallFromTimer = p
            },
            6993: function(e, t, n) {
                "use strict";
                var r = n(3645),
                    o = n.n(r)()((function(e) {
                        return e[1]
                    }));
                o.push([e.id, "html{height:100%}body{-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;-webkit-text-size-adjust:none;-moz-text-size-adjust:none;-ms-text-size-adjust:none;text-size-adjust:none;scroll-behavior:smooth;text-rendering:optimizeSpeed;line-height:1.5;min-height:100vh}html,body{margin:0;padding:0;width:100%;overflow:auto;-ms-touch-action:none;touch-action:none;background-color:#000}*{-webkit-touch-callout:none;-ms-touch-action:auto;touch-action:auto;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;-webkit-box-sizing:inherit;box-sizing:inherit;cursor:default;margin:0;padding:0;-webkit-user-drag:none;-khtml-user-drag:none;-moz-user-drag:none;-o-user-drag:none;user-drag:none}a,area{cursor:pointer}#html_layout{opacity:0;pointer-events:none}#html_layout_container{height:inherit}iframe#game{position:absolute;border:none;width:1px;height:1px;min-width:100%;min-height:100%;max-width:100%;max-height:100%}#game_form{display:none}", ""]), o.locals = {}, t.Z = o
            },
            3645: function(e) {
                "use strict";
                e.exports = function(e) {
                    var t = [];
                    return t.toString = function() {
                        return this.map((function(t) {
                            var n = e(t);
                            return t[2] ? "@media ".concat(t[2], " {").concat(n, "}") : n
                        })).join("")
                    }, t.i = function(e, n, r) {
                        "string" == typeof e && (e = [
                            [null, e, ""]
                        ]);
                        var o = {};
                        if (r)
                            for (var i = 0; i < this.length; i++) {
                                var a = this[i][0];
                                null != a && (o[a] = !0)
                            }
                        for (var s = 0; s < e.length; s++) {
                            var u = [].concat(e[s]);
                            r && o[u[0]] || (n && (u[2] ? u[2] = "".concat(n, " and ").concat(u[2]) : u[2] = n), t.push(u))
                        }
                    }, t
                }
            },
            232: function() {
                "undefined" != typeof Element && (Element.prototype.matches || (Element.prototype.matches = Element.prototype.msMatchesSelector || Element.prototype.webkitMatchesSelector), Element.prototype.closest || (Element.prototype.closest = function(e) {
                    var t = this;
                    do {
                        if (t.matches(e)) return t;
                        t = t.parentElement || t.parentNode
                    } while (null !== t && 1 === t.nodeType);
                    return null
                }))
            },
            1585: function(e, t, n) {
                var r = n(6214);
                n(7672).ISB_OW = r, e.exports = r
            },
            7672: function(e, t, n) {
                "use strict";
                e.exports = function() {
                    if ("object" == typeof globalThis) return globalThis;
                    var e;
                    try {
                        e = this || new Function("return this")()
                    } catch (e) {
                        if ("object" == typeof window) return window;
                        if ("object" == typeof self) return self;
                        if (void 0 !== n.g) return n.g
                    }
                    return e
                }()
            },
            7347: function(e) {
                ! function() {
                    function t(e, t) {
                        document.addEventListener ? e.addEventListener("scroll", t, !1) : e.attachEvent("scroll", t)
                    }

                    function n(e) {
                        this.a = document.createElement("div"), this.a.setAttribute("aria-hidden", "true"), this.a.appendChild(document.createTextNode(e)), this.b = document.createElement("span"), this.c = document.createElement("span"), this.h = document.createElement("span"), this.f = document.createElement("span"), this.g = -1, this.b.style.cssText = "max-width:none;display:inline-block;position:absolute;height:100%;width:100%;overflow:scroll;font-size:16px;", this.c.style.cssText = "max-width:none;display:inline-block;position:absolute;height:100%;width:100%;overflow:scroll;font-size:16px;", this.f.style.cssText = "max-width:none;display:inline-block;position:absolute;height:100%;width:100%;overflow:scroll;font-size:16px;", this.h.style.cssText = "display:inline-block;width:200%;height:200%;font-size:16px;max-width:none;", this.b.appendChild(this.h), this.c.appendChild(this.f), this.a.appendChild(this.b), this.a.appendChild(this.c)
                    }

                    function r(e, t) {
                        e.a.style.cssText = "max-width:none;min-width:20px;min-height:20px;display:inline-block;overflow:hidden;position:absolute;width:auto;margin:0;padding:0;top:-999px;white-space:nowrap;font-synthesis:none;font:" + t + ";"
                    }

                    function o(e) {
                        var t = e.a.offsetWidth,
                            n = t + 100;
                        return e.f.style.width = n + "px", e.c.scrollLeft = n, e.b.scrollLeft = e.b.scrollWidth + 100, e.g !== t && (e.g = t, !0)
                    }

                    function i(e, n) {
                        function r() {
                            var e = i;
                            o(e) && e.a.parentNode && n(e.g)
                        }
                        var i = e;
                        t(e.b, r), t(e.c, r), o(e)
                    }

                    function a(e, t) {
                        var n = t || {};
                        this.family = e, this.style = n.style || "normal", this.weight = n.weight || "normal", this.stretch = n.stretch || "normal"
                    }
                    var s = null,
                        u = null,
                        c = null,
                        l = null;

                    function d() {
                        return null === l && (l = !!document.fonts), l
                    }

                    function f() {
                        if (null === c) {
                            var e = document.createElement("div");
                            try {
                                e.style.font = "condensed 100px sans-serif"
                            } catch (e) {}
                            c = "" !== e.style.font
                        }
                        return c
                    }

                    function p(e, t) {
                        return [e.style, e.weight, f() ? e.stretch : "", "100px", t].join(" ")
                    }
                    a.prototype.load = function(e, t) {
                        var o = this,
                            a = e || "BESbswy",
                            c = 0,
                            l = t || 3e3,
                            f = (new Date).getTime();
                        return new Promise((function(e, t) {
                            if (d() && ! function() {
                                    if (null === u)
                                        if (d() && /Apple/.test(window.navigator.vendor)) {
                                            var e = /AppleWebKit\/([0-9]+)(?:\.([0-9]+))(?:\.([0-9]+))/.exec(window.navigator.userAgent);
                                            u = !!e && 603 > parseInt(e[1], 10)
                                        } else u = !1;
                                    return u
                                }()) {
                                var h = new Promise((function(e, t) {
                                        ! function n() {
                                            (new Date).getTime() - f >= l ? t(Error(l + "ms timeout exceeded")) : document.fonts.load(p(o, '"' + o.family + '"'), a).then((function(t) {
                                                1 <= t.length ? e() : setTimeout(n, 25)
                                            }), t)
                                        }()
                                    })),
                                    m = new Promise((function(e, t) {
                                        c = setTimeout((function() {
                                            t(Error(l + "ms timeout exceeded"))
                                        }), l)
                                    }));
                                Promise.race([m, h]).then((function() {
                                    clearTimeout(c), e(o)
                                }), t)
                            } else ! function(e) {
                                document.body ? e() : document.addEventListener ? document.addEventListener("DOMContentLoaded", (function t() {
                                    document.removeEventListener("DOMContentLoaded", t), e()
                                })) : document.attachEvent("onreadystatechange", (function t() {
                                    "interactive" != document.readyState && "complete" != document.readyState || (document.detachEvent("onreadystatechange", t), e())
                                }))
                            }((function() {
                                function u() {
                                    var t;
                                    (t = -1 != _ && -1 != g || -1 != _ && -1 != v || -1 != g && -1 != v) && ((t = _ != g && _ != v && g != v) || (null === s && (t = /AppleWebKit\/([0-9]+)(?:\.([0-9]+))/.exec(window.navigator.userAgent), s = !!t && (536 > parseInt(t[1], 10) || 536 === parseInt(t[1], 10) && 11 >= parseInt(t[2], 10))), t = s && (_ == y && g == y && v == y || _ == E && g == E && v == E || _ == b && g == b && v == b)), t = !t), t && (O.parentNode && O.parentNode.removeChild(O), clearTimeout(c), e(o))
                                }
                                var d = new n(a),
                                    h = new n(a),
                                    m = new n(a),
                                    _ = -1,
                                    g = -1,
                                    v = -1,
                                    y = -1,
                                    E = -1,
                                    b = -1,
                                    O = document.createElement("div");
                                O.dir = "ltr", r(d, p(o, "sans-serif")), r(h, p(o, "serif")), r(m, p(o, "monospace")), O.appendChild(d.a), O.appendChild(h.a), O.appendChild(m.a), document.body.appendChild(O), y = d.a.offsetWidth, E = h.a.offsetWidth, b = m.a.offsetWidth,
                                    function e() {
                                        if ((new Date).getTime() - f >= l) O.parentNode && O.parentNode.removeChild(O), t(Error(l + "ms timeout exceeded"));
                                        else {
                                            var n = document.hidden;
                                            !0 !== n && void 0 !== n || (_ = d.a.offsetWidth, g = h.a.offsetWidth, v = m.a.offsetWidth, u()), c = setTimeout(e, 50)
                                        }
                                    }(), i(d, (function(e) {
                                        _ = e, u()
                                    })), r(d, p(o, '"' + o.family + '",sans-serif')), i(h, (function(e) {
                                        g = e, u()
                                    })), r(h, p(o, '"' + o.family + '",serif')), i(m, (function(e) {
                                        v = e, u()
                                    })), r(m, p(o, '"' + o.family + '",monospace'))
                            }))
                        }))
                    }, e.exports = a
                }()
            },
            8552: function(e, t, n) {
                var r = n(852)(n(5639), "DataView");
                e.exports = r
            },
            1989: function(e, t, n) {
                var r = n(1789),
                    o = n(401),
                    i = n(7667),
                    a = n(1327),
                    s = n(1866);

                function u(e) {
                    var t = -1,
                        n = null == e ? 0 : e.length;
                    for (this.clear(); ++t < n;) {
                        var r = e[t];
                        this.set(r[0], r[1])
                    }
                }
                u.prototype.clear = r, u.prototype.delete = o, u.prototype.get = i, u.prototype.has = a, u.prototype.set = s, e.exports = u
            },
            8407: function(e, t, n) {
                var r = n(7040),
                    o = n(4125),
                    i = n(2117),
                    a = n(7518),
                    s = n(4705);

                function u(e) {
                    var t = -1,
                        n = null == e ? 0 : e.length;
                    for (this.clear(); ++t < n;) {
                        var r = e[t];
                        this.set(r[0], r[1])
                    }
                }
                u.prototype.clear = r, u.prototype.delete = o, u.prototype.get = i, u.prototype.has = a, u.prototype.set = s, e.exports = u
            },
            7071: function(e, t, n) {
                var r = n(852)(n(5639), "Map");
                e.exports = r
            },
            3369: function(e, t, n) {
                var r = n(4785),
                    o = n(1285),
                    i = n(6e3),
                    a = n(9916),
                    s = n(5265);

                function u(e) {
                    var t = -1,
                        n = null == e ? 0 : e.length;
                    for (this.clear(); ++t < n;) {
                        var r = e[t];
                        this.set(r[0], r[1])
                    }
                }
                u.prototype.clear = r, u.prototype.delete = o, u.prototype.get = i, u.prototype.has = a, u.prototype.set = s, e.exports = u
            },
            3818: function(e, t, n) {
                var r = n(852)(n(5639), "Promise");
                e.exports = r
            },
            8525: function(e, t, n) {
                var r = n(852)(n(5639), "Set");
                e.exports = r
            },
            8668: function(e, t, n) {
                var r = n(3369),
                    o = n(619),
                    i = n(2385);

                function a(e) {
                    var t = -1,
                        n = null == e ? 0 : e.length;
                    for (this.__data__ = new r; ++t < n;) this.add(e[t])
                }
                a.prototype.add = a.prototype.push = o, a.prototype.has = i, e.exports = a
            },
            6384: function(e, t, n) {
                var r = n(8407),
                    o = n(7465),
                    i = n(3779),
                    a = n(7599),
                    s = n(4758),
                    u = n(4309);

                function c(e) {
                    var t = this.__data__ = new r(e);
                    this.size = t.size
                }
                c.prototype.clear = o, c.prototype.delete = i, c.prototype.get = a, c.prototype.has = s, c.prototype.set = u, e.exports = c
            },
            2705: function(e, t, n) {
                var r = n(5639).Symbol;
                e.exports = r
            },
            1149: function(e, t, n) {
                var r = n(5639).Uint8Array;
                e.exports = r
            },
            577: function(e, t, n) {
                var r = n(852)(n(5639), "WeakMap");
                e.exports = r
            },
            7412: function(e) {
                e.exports = function(e, t) {
                    for (var n = -1, r = null == e ? 0 : e.length; ++n < r && !1 !== t(e[n], n, e););
                    return e
                }
            },
            6193: function(e) {
                e.exports = function(e, t) {
                    for (var n = -1, r = null == e ? 0 : e.length; ++n < r;)
                        if (!t(e[n], n, e)) return !1;
                    return !0
                }
            },
            4963: function(e) {
                e.exports = function(e, t) {
                    for (var n = -1, r = null == e ? 0 : e.length, o = 0, i = []; ++n < r;) {
                        var a = e[n];
                        t(a, n, e) && (i[o++] = a)
                    }
                    return i
                }
            },
            4636: function(e, t, n) {
                var r = n(2545),
                    o = n(5694),
                    i = n(1469),
                    a = n(4144),
                    s = n(5776),
                    u = n(6719),
                    c = Object.prototype.hasOwnProperty;
                e.exports = function(e, t) {
                    var n = i(e),
                        l = !n && o(e),
                        d = !n && !l && a(e),
                        f = !n && !l && !d && u(e),
                        p = n || l || d || f,
                        h = p ? r(e.length, String) : [],
                        m = h.length;
                    for (var _ in e) !t && !c.call(e, _) || p && ("length" == _ || d && ("offset" == _ || "parent" == _) || f && ("buffer" == _ || "byteLength" == _ || "byteOffset" == _) || s(_, m)) || h.push(_);
                    return h
                }
            },
            9932: function(e) {
                e.exports = function(e, t) {
                    for (var n = -1, r = null == e ? 0 : e.length, o = Array(r); ++n < r;) o[n] = t(e[n], n, e);
                    return o
                }
            },
            2488: function(e) {
                e.exports = function(e, t) {
                    for (var n = -1, r = t.length, o = e.length; ++n < r;) e[o + n] = t[n];
                    return e
                }
            },
            2663: function(e) {
                e.exports = function(e, t, n, r) {
                    var o = -1,
                        i = null == e ? 0 : e.length;
                    for (r && i && (n = e[++o]); ++o < i;) n = t(n, e[o], o, e);
                    return n
                }
            },
            2908: function(e) {
                e.exports = function(e, t) {
                    for (var n = -1, r = null == e ? 0 : e.length; ++n < r;)
                        if (t(e[n], n, e)) return !0;
                    return !1
                }
            },
            9029: function(e) {
                var t = /[^\x00-\x2f\x3a-\x40\x5b-\x60\x7b-\x7f]+/g;
                e.exports = function(e) {
                    return e.match(t) || []
                }
            },
            4865: function(e, t, n) {
                var r = n(9465),
                    o = n(7813),
                    i = Object.prototype.hasOwnProperty;
                e.exports = function(e, t, n) {
                    var a = e[t];
                    i.call(e, t) && o(a, n) && (void 0 !== n || t in e) || r(e, t, n)
                }
            },
            8470: function(e, t, n) {
                var r = n(7813);
                e.exports = function(e, t) {
                    for (var n = e.length; n--;)
                        if (r(e[n][0], t)) return n;
                    return -1
                }
            },
            4037: function(e, t, n) {
                var r = n(8363),
                    o = n(3674);
                e.exports = function(e, t) {
                    return e && r(t, o(t), e)
                }
            },
            3886: function(e, t, n) {
                var r = n(8363),
                    o = n(1704);
                e.exports = function(e, t) {
                    return e && r(t, o(t), e)
                }
            },
            9465: function(e, t, n) {
                var r = n(8777);
                e.exports = function(e, t, n) {
                    "__proto__" == t && r ? r(e, t, {
                        configurable: !0,
                        enumerable: !0,
                        value: n,
                        writable: !0
                    }) : e[t] = n
                }
            },
            5990: function(e, t, n) {
                var r = n(6384),
                    o = n(7412),
                    i = n(4865),
                    a = n(4037),
                    s = n(3886),
                    u = n(4626),
                    c = n(278),
                    l = n(8805),
                    d = n(1911),
                    f = n(8234),
                    p = n(6904),
                    h = n(4160),
                    m = n(3824),
                    _ = n(9148),
                    g = n(8517),
                    v = n(1469),
                    y = n(4144),
                    E = n(6688),
                    b = n(3218),
                    O = n(2928),
                    w = n(3674),
                    S = n(1704),
                    P = "[object Arguments]",
                    A = "[object Function]",
                    T = "[object Object]",
                    C = {};
                C[P] = C["[object Array]"] = C["[object ArrayBuffer]"] = C["[object DataView]"] = C["[object Boolean]"] = C["[object Date]"] = C["[object Float32Array]"] = C["[object Float64Array]"] = C["[object Int8Array]"] = C["[object Int16Array]"] = C["[object Int32Array]"] = C["[object Map]"] = C["[object Number]"] = C[T] = C["[object RegExp]"] = C["[object Set]"] = C["[object String]"] = C["[object Symbol]"] = C["[object Uint8Array]"] = C["[object Uint8ClampedArray]"] = C["[object Uint16Array]"] = C["[object Uint32Array]"] = !0, C["[object Error]"] = C[A] = C["[object WeakMap]"] = !1, e.exports = function e(t, n, D, x, I, N) {
                    var M, L = 1 & n,
                        W = 2 & n,
                        R = 4 & n;
                    if (D && (M = I ? D(t, x, I, N) : D(t)), void 0 !== M) return M;
                    if (!b(t)) return t;
                    var j = v(t);
                    if (j) {
                        if (M = m(t), !L) return c(t, M)
                    } else {
                        var U = h(t),
                            k = U == A || "[object GeneratorFunction]" == U;
                        if (y(t)) return u(t, L);
                        if (U == T || U == P || k && !I) {
                            if (M = W || k ? {} : g(t), !L) return W ? d(t, s(M, t)) : l(t, a(M, t))
                        } else {
                            if (!C[U]) return I ? t : {};
                            M = _(t, U, L)
                        }
                    }
                    N || (N = new r);
                    var B = N.get(t);
                    if (B) return B;
                    N.set(t, M), O(t) ? t.forEach((function(r) {
                        M.add(e(r, n, D, r, t, N))
                    })) : E(t) && t.forEach((function(r, o) {
                        M.set(o, e(r, n, D, o, t, N))
                    }));
                    var G = j ? void 0 : (R ? W ? p : f : W ? S : w)(t);
                    return o(G || t, (function(r, o) {
                        G && (r = t[o = r]), i(M, o, e(r, n, D, o, t, N))
                    })), M
                }
            },
            3118: function(e, t, n) {
                var r = n(3218),
                    o = Object.create,
                    i = function() {
                        function e() {}
                        return function(t) {
                            if (!r(t)) return {};
                            if (o) return o(t);
                            e.prototype = t;
                            var n = new e;
                            return e.prototype = void 0, n
                        }
                    }();
                e.exports = i
            },
            9881: function(e, t, n) {
                var r = n(7816),
                    o = n(9291)(r);
                e.exports = o
            },
            3239: function(e, t, n) {
                var r = n(9881);
                e.exports = function(e, t) {
                    var n = !0;
                    return r(e, (function(e, r, o) {
                        return n = !!t(e, r, o)
                    })), n
                }
            },
            760: function(e, t, n) {
                var r = n(9881);
                e.exports = function(e, t) {
                    var n = [];
                    return r(e, (function(e, r, o) {
                        t(e, r, o) && n.push(e)
                    })), n
                }
            },
            1848: function(e) {
                e.exports = function(e, t, n, r) {
                    for (var o = e.length, i = n + (r ? 1 : -1); r ? i-- : ++i < o;)
                        if (t(e[i], i, e)) return i;
                    return -1
                }
            },
            8483: function(e, t, n) {
                var r = n(5063)();
                e.exports = r
            },
            7816: function(e, t, n) {
                var r = n(8483),
                    o = n(3674);
                e.exports = function(e, t) {
                    return e && r(e, t, o)
                }
            },
            7786: function(e, t, n) {
                var r = n(1811),
                    o = n(327);
                e.exports = function(e, t) {
                    for (var n = 0, i = (t = r(t, e)).length; null != e && n < i;) e = e[o(t[n++])];
                    return n && n == i ? e : void 0
                }
            },
            8866: function(e, t, n) {
                var r = n(2488),
                    o = n(1469);
                e.exports = function(e, t, n) {
                    var i = t(e);
                    return o(e) ? i : r(i, n(e))
                }
            },
            4239: function(e, t, n) {
                var r = n(2705),
                    o = n(9607),
                    i = n(2333),
                    a = r ? r.toStringTag : void 0;
                e.exports = function(e) {
                    return null == e ? void 0 === e ? "[object Undefined]" : "[object Null]" : a && a in Object(e) ? o(e) : i(e)
                }
            },
            13: function(e) {
                e.exports = function(e, t) {
                    return null != e && t in Object(e)
                }
            },
            5600: function(e) {
                var t = Math.max,
                    n = Math.min;
                e.exports = function(e, r, o) {
                    return e >= n(r, o) && e < t(r, o)
                }
            },
            9454: function(e, t, n) {
                var r = n(4239),
                    o = n(7005);
                e.exports = function(e) {
                    return o(e) && "[object Arguments]" == r(e)
                }
            },
            939: function(e, t, n) {
                var r = n(2492),
                    o = n(7005);
                e.exports = function e(t, n, i, a, s) {
                    return t === n || (null == t || null == n || !o(t) && !o(n) ? t != t && n != n : r(t, n, i, a, e, s))
                }
            },
            2492: function(e, t, n) {
                var r = n(6384),
                    o = n(7114),
                    i = n(8351),
                    a = n(6096),
                    s = n(4160),
                    u = n(1469),
                    c = n(4144),
                    l = n(6719),
                    d = "[object Arguments]",
                    f = "[object Array]",
                    p = "[object Object]",
                    h = Object.prototype.hasOwnProperty;
                e.exports = function(e, t, n, m, _, g) {
                    var v = u(e),
                        y = u(t),
                        E = v ? f : s(e),
                        b = y ? f : s(t),
                        O = (E = E == d ? p : E) == p,
                        w = (b = b == d ? p : b) == p,
                        S = E == b;
                    if (S && c(e)) {
                        if (!c(t)) return !1;
                        v = !0, O = !1
                    }
                    if (S && !O) return g || (g = new r), v || l(e) ? o(e, t, n, m, _, g) : i(e, t, E, n, m, _, g);
                    if (!(1 & n)) {
                        var P = O && h.call(e, "__wrapped__"),
                            A = w && h.call(t, "__wrapped__");
                        if (P || A) {
                            var T = P ? e.value() : e,
                                C = A ? t.value() : t;
                            return g || (g = new r), _(T, C, n, m, g)
                        }
                    }
                    return !!S && (g || (g = new r), a(e, t, n, m, _, g))
                }
            },
            5588: function(e, t, n) {
                var r = n(4160),
                    o = n(7005);
                e.exports = function(e) {
                    return o(e) && "[object Map]" == r(e)
                }
            },
            2958: function(e, t, n) {
                var r = n(6384),
                    o = n(939);
                e.exports = function(e, t, n, i) {
                    var a = n.length,
                        s = a,
                        u = !i;
                    if (null == e) return !s;
                    for (e = Object(e); a--;) {
                        var c = n[a];
                        if (u && c[2] ? c[1] !== e[c[0]] : !(c[0] in e)) return !1
                    }
                    for (; ++a < s;) {
                        var l = (c = n[a])[0],
                            d = e[l],
                            f = c[1];
                        if (u && c[2]) {
                            if (void 0 === d && !(l in e)) return !1
                        } else {
                            var p = new r;
                            if (i) var h = i(d, f, l, e, t, p);
                            if (!(void 0 === h ? o(f, d, 3, i, p) : h)) return !1
                        }
                    }
                    return !0
                }
            },
            8458: function(e, t, n) {
                var r = n(3560),
                    o = n(5346),
                    i = n(3218),
                    a = n(346),
                    s = /^\[object .+?Constructor\]$/,
                    u = Function.prototype,
                    c = Object.prototype,
                    l = u.toString,
                    d = c.hasOwnProperty,
                    f = RegExp("^" + l.call(d).replace(/[\\^$.*+?()[\]{}|]/g, "\\$&").replace(/hasOwnProperty|(function).*?(?=\\\()| for .+?(?=\\\])/g, "$1.*?") + "$");
                e.exports = function(e) {
                    return !(!i(e) || o(e)) && (r(e) ? f : s).test(a(e))
                }
            },
            9221: function(e, t, n) {
                var r = n(4160),
                    o = n(7005);
                e.exports = function(e) {
                    return o(e) && "[object Set]" == r(e)
                }
            },
            8749: function(e, t, n) {
                var r = n(4239),
                    o = n(1780),
                    i = n(7005),
                    a = {};
                a["[object Float32Array]"] = a["[object Float64Array]"] = a["[object Int8Array]"] = a["[object Int16Array]"] = a["[object Int32Array]"] = a["[object Uint8Array]"] = a["[object Uint8ClampedArray]"] = a["[object Uint16Array]"] = a["[object Uint32Array]"] = !0, a["[object Arguments]"] = a["[object Array]"] = a["[object ArrayBuffer]"] = a["[object Boolean]"] = a["[object DataView]"] = a["[object Date]"] = a["[object Error]"] = a["[object Function]"] = a["[object Map]"] = a["[object Number]"] = a["[object Object]"] = a["[object RegExp]"] = a["[object Set]"] = a["[object String]"] = a["[object WeakMap]"] = !1, e.exports = function(e) {
                    return i(e) && o(e.length) && !!a[r(e)]
                }
            },
            7206: function(e, t, n) {
                var r = n(1573),
                    o = n(6432),
                    i = n(6557),
                    a = n(1469),
                    s = n(9601);
                e.exports = function(e) {
                    return "function" == typeof e ? e : null == e ? i : "object" == typeof e ? a(e) ? o(e[0], e[1]) : r(e) : s(e)
                }
            },
            280: function(e, t, n) {
                var r = n(5726),
                    o = n(6916),
                    i = Object.prototype.hasOwnProperty;
                e.exports = function(e) {
                    if (!r(e)) return o(e);
                    var t = [];
                    for (var n in Object(e)) i.call(e, n) && "constructor" != n && t.push(n);
                    return t
                }
            },
            313: function(e, t, n) {
                var r = n(3218),
                    o = n(5726),
                    i = n(3498),
                    a = Object.prototype.hasOwnProperty;
                e.exports = function(e) {
                    if (!r(e)) return i(e);
                    var t = o(e),
                        n = [];
                    for (var s in e)("constructor" != s || !t && a.call(e, s)) && n.push(s);
                    return n
                }
            },
            1573: function(e, t, n) {
                var r = n(2958),
                    o = n(1499),
                    i = n(2634);
                e.exports = function(e) {
                    var t = o(e);
                    return 1 == t.length && t[0][2] ? i(t[0][0], t[0][1]) : function(n) {
                        return n === e || r(n, e, t)
                    }
                }
            },
            6432: function(e, t, n) {
                var r = n(939),
                    o = n(7361),
                    i = n(9095),
                    a = n(5403),
                    s = n(9162),
                    u = n(2634),
                    c = n(327);
                e.exports = function(e, t) {
                    return a(e) && s(t) ? u(c(e), t) : function(n) {
                        var a = o(n, e);
                        return void 0 === a && a === t ? i(n, e) : r(t, a, 3)
                    }
                }
            },
            371: function(e) {
                e.exports = function(e) {
                    return function(t) {
                        return null == t ? void 0 : t[e]
                    }
                }
            },
            9152: function(e, t, n) {
                var r = n(7786);
                e.exports = function(e) {
                    return function(t) {
                        return r(t, e)
                    }
                }
            },
            8674: function(e) {
                e.exports = function(e) {
                    return function(t) {
                        return null == e ? void 0 : e[t]
                    }
                }
            },
            107: function(e) {
                e.exports = function(e, t, n, r, o) {
                    return o(e, (function(e, o, i) {
                        n = r ? (r = !1, e) : t(n, e, o, i)
                    })), n
                }
            },
            611: function(e, t, n) {
                var r = n(4865),
                    o = n(1811),
                    i = n(5776),
                    a = n(3218),
                    s = n(327);
                e.exports = function(e, t, n, u) {
                    if (!a(e)) return e;
                    for (var c = -1, l = (t = o(t, e)).length, d = l - 1, f = e; null != f && ++c < l;) {
                        var p = s(t[c]),
                            h = n;
                        if ("__proto__" === p || "constructor" === p || "prototype" === p) return e;
                        if (c != d) {
                            var m = f[p];
                            void 0 === (h = u ? u(m, p, f) : void 0) && (h = a(m) ? m : i(t[c + 1]) ? [] : {})
                        }
                        r(f, p, h), f = f[p]
                    }
                    return e
                }
            },
            5076: function(e, t, n) {
                var r = n(9881);
                e.exports = function(e, t) {
                    var n;
                    return r(e, (function(e, r, o) {
                        return !(n = t(e, r, o))
                    })), !!n
                }
            },
            2545: function(e) {
                e.exports = function(e, t) {
                    for (var n = -1, r = Array(e); ++n < e;) r[n] = t(n);
                    return r
                }
            },
            531: function(e, t, n) {
                var r = n(2705),
                    o = n(9932),
                    i = n(1469),
                    a = n(3448),
                    s = r ? r.prototype : void 0,
                    u = s ? s.toString : void 0;
                e.exports = function e(t) {
                    if ("string" == typeof t) return t;
                    if (i(t)) return o(t, e) + "";
                    if (a(t)) return u ? u.call(t) : "";
                    var n = t + "";
                    return "0" == n && 1 / t == -Infinity ? "-0" : n
                }
            },
            7561: function(e, t, n) {
                var r = n(7990),
                    o = /^\s+/;
                e.exports = function(e) {
                    return e ? e.slice(0, r(e) + 1).replace(o, "") : e
                }
            },
            1717: function(e) {
                e.exports = function(e) {
                    return function(t) {
                        return e(t)
                    }
                }
            },
            4757: function(e) {
                e.exports = function(e, t) {
                    return e.has(t)
                }
            },
            4290: function(e, t, n) {
                var r = n(6557);
                e.exports = function(e) {
                    return "function" == typeof e ? e : r
                }
            },
            1811: function(e, t, n) {
                var r = n(1469),
                    o = n(5403),
                    i = n(5514),
                    a = n(9833);
                e.exports = function(e, t) {
                    return r(e) ? e : o(e, t) ? [e] : i(a(e))
                }
            },
            4318: function(e, t, n) {
                var r = n(1149);
                e.exports = function(e) {
                    var t = new e.constructor(e.byteLength);
                    return new r(t).set(new r(e)), t
                }
            },
            4626: function(e, t, n) {
                e = n.nmd(e);
                var r = n(5639),
                    o = t && !t.nodeType && t,
                    i = o && e && !e.nodeType && e,
                    a = i && i.exports === o ? r.Buffer : void 0,
                    s = a ? a.allocUnsafe : void 0;
                e.exports = function(e, t) {
                    if (t) return e.slice();
                    var n = e.length,
                        r = s ? s(n) : new e.constructor(n);
                    return e.copy(r), r
                }
            },
            7157: function(e, t, n) {
                var r = n(4318);
                e.exports = function(e, t) {
                    var n = t ? r(e.buffer) : e.buffer;
                    return new e.constructor(n, e.byteOffset, e.byteLength)
                }
            },
            3147: function(e) {
                var t = /\w*$/;
                e.exports = function(e) {
                    var n = new e.constructor(e.source, t.exec(e));
                    return n.lastIndex = e.lastIndex, n
                }
            },
            419: function(e, t, n) {
                var r = n(2705),
                    o = r ? r.prototype : void 0,
                    i = o ? o.valueOf : void 0;
                e.exports = function(e) {
                    return i ? Object(i.call(e)) : {}
                }
            },
            7133: function(e, t, n) {
                var r = n(4318);
                e.exports = function(e, t) {
                    var n = t ? r(e.buffer) : e.buffer;
                    return new e.constructor(n, e.byteOffset, e.length)
                }
            },
            278: function(e) {
                e.exports = function(e, t) {
                    var n = -1,
                        r = e.length;
                    for (t || (t = Array(r)); ++n < r;) t[n] = e[n];
                    return t
                }
            },
            8363: function(e, t, n) {
                var r = n(4865),
                    o = n(9465);
                e.exports = function(e, t, n, i) {
                    var a = !n;
                    n || (n = {});
                    for (var s = -1, u = t.length; ++s < u;) {
                        var c = t[s],
                            l = i ? i(n[c], e[c], c, n, e) : void 0;
                        void 0 === l && (l = e[c]), a ? o(n, c, l) : r(n, c, l)
                    }
                    return n
                }
            },
            8805: function(e, t, n) {
                var r = n(8363),
                    o = n(9551);
                e.exports = function(e, t) {
                    return r(e, o(e), t)
                }
            },
            1911: function(e, t, n) {
                var r = n(8363),
                    o = n(1442);
                e.exports = function(e, t) {
                    return r(e, o(e), t)
                }
            },
            4429: function(e, t, n) {
                var r = n(5639)["__core-js_shared__"];
                e.exports = r
            },
            9291: function(e, t, n) {
                var r = n(8612);
                e.exports = function(e, t) {
                    return function(n, o) {
                        if (null == n) return n;
                        if (!r(n)) return e(n, o);
                        for (var i = n.length, a = t ? i : -1, s = Object(n);
                            (t ? a-- : ++a < i) && !1 !== o(s[a], a, s););
                        return n
                    }
                }
            },
            5063: function(e) {
                e.exports = function(e) {
                    return function(t, n, r) {
                        for (var o = -1, i = Object(t), a = r(t), s = a.length; s--;) {
                            var u = a[e ? s : ++o];
                            if (!1 === n(i[u], u, i)) break
                        }
                        return t
                    }
                }
            },
            5393: function(e, t, n) {
                var r = n(2663),
                    o = n(3816),
                    i = n(8748),
                    a = RegExp("['â€™]", "g");
                e.exports = function(e) {
                    return function(t) {
                        return r(i(o(t).replace(a, "")), e, "")
                    }
                }
            },
            7740: function(e, t, n) {
                var r = n(7206),
                    o = n(8612),
                    i = n(3674);
                e.exports = function(e) {
                    return function(t, n, a) {
                        var s = Object(t);
                        if (!o(t)) {
                            var u = r(n, 3);
                            t = i(t), n = function(e) {
                                return u(s[e], e, s)
                            }
                        }
                        var c = e(t, n, a);
                        return c > -1 ? s[u ? t[c] : c] : void 0
                    }
                }
            },
            9389: function(e, t, n) {
                var r = n(8674)({
                    "Ã€": "A",
                    "Ã": "A",
                    "Ã‚": "A",
                    "Ãƒ": "A",
                    "Ã„": "A",
                    "Ã…": "A",
                    "Ã ": "a",
                    "Ã¡": "a",
                    "Ã¢": "a",
                    "Ã£": "a",
                    "Ã¤": "a",
                    "Ã¥": "a",
                    "Ã‡": "C",
                    "Ã§": "c",
                    "Ã": "D",
                    "Ã°": "d",
                    "Ãˆ": "E",
                    "Ã‰": "E",
                    "ÃŠ": "E",
                    "Ã‹": "E",
                    "Ã¨": "e",
                    "Ã©": "e",
                    "Ãª": "e",
                    "Ã«": "e",
                    "ÃŒ": "I",
                    "Ã": "I",
                    "ÃŽ": "I",
                    "Ã": "I",
                    "Ã¬": "i",
                    "Ã­": "i",
                    "Ã®": "i",
                    "Ã¯": "i",
                    "Ã‘": "N",
                    "Ã±": "n",
                    "Ã’": "O",
                    "Ã“": "O",
                    "Ã”": "O",
                    "Ã•": "O",
                    "Ã–": "O",
                    "Ã˜": "O",
                    "Ã²": "o",
                    "Ã³": "o",
                    "Ã´": "o",
                    "Ãµ": "o",
                    "Ã¶": "o",
                    "Ã¸": "o",
                    "Ã™": "U",
                    "Ãš": "U",
                    "Ã›": "U",
                    "Ãœ": "U",
                    "Ã¹": "u",
                    "Ãº": "u",
                    "Ã»": "u",
                    "Ã¼": "u",
                    "Ã": "Y",
                    "Ã½": "y",
                    "Ã¿": "y",
                    "Ã†": "Ae",
                    "Ã¦": "ae",
                    "Ãž": "Th",
                    "Ã¾": "th",
                    "ÃŸ": "ss",
                    "Ä€": "A",
                    "Ä‚": "A",
                    "Ä„": "A",
                    "Ä": "a",
                    "Äƒ": "a",
                    "Ä…": "a",
                    "Ä†": "C",
                    "Äˆ": "C",
                    "ÄŠ": "C",
                    "ÄŒ": "C",
                    "Ä‡": "c",
                    "Ä‰": "c",
                    "Ä‹": "c",
                    "Ä": "c",
                    "ÄŽ": "D",
                    "Ä": "D",
                    "Ä": "d",
                    "Ä‘": "d",
                    "Ä’": "E",
                    "Ä”": "E",
                    "Ä–": "E",
                    "Ä˜": "E",
                    "Äš": "E",
                    "Ä“": "e",
                    "Ä•": "e",
                    "Ä—": "e",
                    "Ä™": "e",
                    "Ä›": "e",
                    "Äœ": "G",
                    "Äž": "G",
                    "Ä ": "G",
                    "Ä¢": "G",
                    "Ä": "g",
                    "ÄŸ": "g",
                    "Ä¡": "g",
                    "Ä£": "g",
                    "Ä¤": "H",
                    "Ä¦": "H",
                    "Ä¥": "h",
                    "Ä§": "h",
                    "Ä¨": "I",
                    "Äª": "I",
                    "Ä¬": "I",
                    "Ä®": "I",
                    "Ä°": "I",
                    "Ä©": "i",
                    "Ä«": "i",
                    "Ä­": "i",
                    "Ä¯": "i",
                    "Ä±": "i",
                    "Ä´": "J",
                    "Äµ": "j",
                    "Ä¶": "K",
                    "Ä·": "k",
                    "Ä¸": "k",
                    "Ä¹": "L",
                    "Ä»": "L",
                    "Ä½": "L",
                    "Ä¿": "L",
                    "Å": "L",
                    "Äº": "l",
                    "Ä¼": "l",
                    "Ä¾": "l",
                    "Å€": "l",
                    "Å‚": "l",
                    "Åƒ": "N",
                    "Å…": "N",
                    "Å‡": "N",
                    "ÅŠ": "N",
                    "Å„": "n",
                    "Å†": "n",
                    "Åˆ": "n",
                    "Å‹": "n",
                    "ÅŒ": "O",
                    "ÅŽ": "O",
                    "Å": "O",
                    "Å": "o",
                    "Å": "o",
                    "Å‘": "o",
                    "Å”": "R",
                    "Å–": "R",
                    "Å˜": "R",
                    "Å•": "r",
                    "Å—": "r",
                    "Å™": "r",
                    "Åš": "S",
                    "Åœ": "S",
                    "Åž": "S",
                    "Å ": "S",
                    "Å›": "s",
                    "Å": "s",
                    "ÅŸ": "s",
                    "Å¡": "s",
                    "Å¢": "T",
                    "Å¤": "T",
                    "Å¦": "T",
                    "Å£": "t",
                    "Å¥": "t",
                    "Å§": "t",
                    "Å¨": "U",
                    "Åª": "U",
                    "Å¬": "U",
                    "Å®": "U",
                    "Å°": "U",
                    "Å²": "U",
                    "Å©": "u",
                    "Å«": "u",
                    "Å­": "u",
                    "Å¯": "u",
                    "Å±": "u",
                    "Å³": "u",
                    "Å´": "W",
                    "Åµ": "w",
                    "Å¶": "Y",
                    "Å·": "y",
                    "Å¸": "Y",
                    "Å¹": "Z",
                    "Å»": "Z",
                    "Å½": "Z",
                    "Åº": "z",
                    "Å¼": "z",
                    "Å¾": "z",
                    "Ä²": "IJ",
                    "Ä³": "ij",
                    "Å’": "Oe",
                    "Å“": "oe",
                    "Å‰": "'n",
                    "Å¿": "s"
                });
                e.exports = r
            },
            8777: function(e, t, n) {
                var r = n(852),
                    o = function() {
                        try {
                            var e = r(Object, "defineProperty");
                            return e({}, "", {}), e
                        } catch (e) {}
                    }();
                e.exports = o
            },
            7114: function(e, t, n) {
                var r = n(8668),
                    o = n(2908),
                    i = n(4757);
                e.exports = function(e, t, n, a, s, u) {
                    var c = 1 & n,
                        l = e.length,
                        d = t.length;
                    if (l != d && !(c && d > l)) return !1;
                    var f = u.get(e),
                        p = u.get(t);
                    if (f && p) return f == t && p == e;
                    var h = -1,
                        m = !0,
                        _ = 2 & n ? new r : void 0;
                    for (u.set(e, t), u.set(t, e); ++h < l;) {
                        var g = e[h],
                            v = t[h];
                        if (a) var y = c ? a(v, g, h, t, e, u) : a(g, v, h, e, t, u);
                        if (void 0 !== y) {
                            if (y) continue;
                            m = !1;
                            break
                        }
                        if (_) {
                            if (!o(t, (function(e, t) {
                                    if (!i(_, t) && (g === e || s(g, e, n, a, u))) return _.push(t)
                                }))) {
                                m = !1;
                                break
                            }
                        } else if (g !== v && !s(g, v, n, a, u)) {
                            m = !1;
                            break
                        }
                    }
                    return u.delete(e), u.delete(t), m
                }
            },
            8351: function(e, t, n) {
                var r = n(2705),
                    o = n(1149),
                    i = n(7813),
                    a = n(7114),
                    s = n(8776),
                    u = n(1814),
                    c = r ? r.prototype : void 0,
                    l = c ? c.valueOf : void 0;
                e.exports = function(e, t, n, r, c, d, f) {
                    switch (n) {
                        case "[object DataView]":
                            if (e.byteLength != t.byteLength || e.byteOffset != t.byteOffset) return !1;
                            e = e.buffer, t = t.buffer;
                        case "[object ArrayBuffer]":
                            return !(e.byteLength != t.byteLength || !d(new o(e), new o(t)));
                        case "[object Boolean]":
                        case "[object Date]":
                        case "[object Number]":
                            return i(+e, +t);
                        case "[object Error]":
                            return e.name == t.name && e.message == t.message;
                        case "[object RegExp]":
                        case "[object String]":
                            return e == t + "";
                        case "[object Map]":
                            var p = s;
                        case "[object Set]":
                            var h = 1 & r;
                            if (p || (p = u), e.size != t.size && !h) return !1;
                            var m = f.get(e);
                            if (m) return m == t;
                            r |= 2, f.set(e, t);
                            var _ = a(p(e), p(t), r, c, d, f);
                            return f.delete(e), _;
                        case "[object Symbol]":
                            if (l) return l.call(e) == l.call(t)
                    }
                    return !1
                }
            },
            6096: function(e, t, n) {
                var r = n(8234),
                    o = Object.prototype.hasOwnProperty;
                e.exports = function(e, t, n, i, a, s) {
                    var u = 1 & n,
                        c = r(e),
                        l = c.length;
                    if (l != r(t).length && !u) return !1;
                    for (var d = l; d--;) {
                        var f = c[d];
                        if (!(u ? f in t : o.call(t, f))) return !1
                    }
                    var p = s.get(e),
                        h = s.get(t);
                    if (p && h) return p == t && h == e;
                    var m = !0;
                    s.set(e, t), s.set(t, e);
                    for (var _ = u; ++d < l;) {
                        var g = e[f = c[d]],
                            v = t[f];
                        if (i) var y = u ? i(v, g, f, t, e, s) : i(g, v, f, e, t, s);
                        if (!(void 0 === y ? g === v || a(g, v, n, i, s) : y)) {
                            m = !1;
                            break
                        }
                        _ || (_ = "constructor" == f)
                    }
                    if (m && !_) {
                        var E = e.constructor,
                            b = t.constructor;
                        E == b || !("constructor" in e) || !("constructor" in t) || "function" == typeof E && E instanceof E && "function" == typeof b && b instanceof b || (m = !1)
                    }
                    return s.delete(e), s.delete(t), m
                }
            },
            1957: function(e, t, n) {
                var r = "object" == typeof n.g && n.g && n.g.Object === Object && n.g;
                e.exports = r
            },
            8234: function(e, t, n) {
                var r = n(8866),
                    o = n(9551),
                    i = n(3674);
                e.exports = function(e) {
                    return r(e, i, o)
                }
            },
            6904: function(e, t, n) {
                var r = n(8866),
                    o = n(1442),
                    i = n(1704);
                e.exports = function(e) {
                    return r(e, i, o)
                }
            },
            5050: function(e, t, n) {
                var r = n(7019);
                e.exports = function(e, t) {
                    var n = e.__data__;
                    return r(t) ? n["string" == typeof t ? "string" : "hash"] : n.map
                }
            },
            1499: function(e, t, n) {
                var r = n(9162),
                    o = n(3674);
                e.exports = function(e) {
                    for (var t = o(e), n = t.length; n--;) {
                        var i = t[n],
                            a = e[i];
                        t[n] = [i, a, r(a)]
                    }
                    return t
                }
            },
            852: function(e, t, n) {
                var r = n(8458),
                    o = n(7801);
                e.exports = function(e, t) {
                    var n = o(e, t);
                    return r(n) ? n : void 0
                }
            },
            5924: function(e, t, n) {
                var r = n(5569)(Object.getPrototypeOf, Object);
                e.exports = r
            },
            9607: function(e, t, n) {
                var r = n(2705),
                    o = Object.prototype,
                    i = o.hasOwnProperty,
                    a = o.toString,
                    s = r ? r.toStringTag : void 0;
                e.exports = function(e) {
                    var t = i.call(e, s),
                        n = e[s];
                    try {
                        e[s] = void 0;
                        var r = !0
                    } catch (e) {}
                    var o = a.call(e);
                    return r && (t ? e[s] = n : delete e[s]), o
                }
            },
            9551: function(e, t, n) {
                var r = n(4963),
                    o = n(479),
                    i = Object.prototype.propertyIsEnumerable,
                    a = Object.getOwnPropertySymbols,
                    s = a ? function(e) {
                        return null == e ? [] : (e = Object(e), r(a(e), (function(t) {
                            return i.call(e, t)
                        })))
                    } : o;
                e.exports = s
            },
            1442: function(e, t, n) {
                var r = n(2488),
                    o = n(5924),
                    i = n(9551),
                    a = n(479),
                    s = Object.getOwnPropertySymbols ? function(e) {
                        for (var t = []; e;) r(t, i(e)), e = o(e);
                        return t
                    } : a;
                e.exports = s
            },
            4160: function(e, t, n) {
                var r = n(8552),
                    o = n(7071),
                    i = n(3818),
                    a = n(8525),
                    s = n(577),
                    u = n(4239),
                    c = n(346),
                    l = "[object Map]",
                    d = "[object Promise]",
                    f = "[object Set]",
                    p = "[object WeakMap]",
                    h = "[object DataView]",
                    m = c(r),
                    _ = c(o),
                    g = c(i),
                    v = c(a),
                    y = c(s),
                    E = u;
                (r && E(new r(new ArrayBuffer(1))) != h || o && E(new o) != l || i && E(i.resolve()) != d || a && E(new a) != f || s && E(new s) != p) && (E = function(e) {
                    var t = u(e),
                        n = "[object Object]" == t ? e.constructor : void 0,
                        r = n ? c(n) : "";
                    if (r) switch (r) {
                        case m:
                            return h;
                        case _:
                            return l;
                        case g:
                            return d;
                        case v:
                            return f;
                        case y:
                            return p
                    }
                    return t
                }), e.exports = E
            },
            7801: function(e) {
                e.exports = function(e, t) {
                    return null == e ? void 0 : e[t]
                }
            },
            222: function(e, t, n) {
                var r = n(1811),
                    o = n(5694),
                    i = n(1469),
                    a = n(5776),
                    s = n(1780),
                    u = n(327);
                e.exports = function(e, t, n) {
                    for (var c = -1, l = (t = r(t, e)).length, d = !1; ++c < l;) {
                        var f = u(t[c]);
                        if (!(d = null != e && n(e, f))) break;
                        e = e[f]
                    }
                    return d || ++c != l ? d : !!(l = null == e ? 0 : e.length) && s(l) && a(f, l) && (i(e) || o(e))
                }
            },
            3157: function(e) {
                var t = /[a-z][A-Z]|[A-Z]{2}[a-z]|[0-9][a-zA-Z]|[a-zA-Z][0-9]|[^a-zA-Z0-9 ]/;
                e.exports = function(e) {
                    return t.test(e)
                }
            },
            1789: function(e, t, n) {
                var r = n(4536);
                e.exports = function() {
                    this.__data__ = r ? r(null) : {}, this.size = 0
                }
            },
            401: function(e) {
                e.exports = function(e) {
                    var t = this.has(e) && delete this.__data__[e];
                    return this.size -= t ? 1 : 0, t
                }
            },
            7667: function(e, t, n) {
                var r = n(4536),
                    o = Object.prototype.hasOwnProperty;
                e.exports = function(e) {
                    var t = this.__data__;
                    if (r) {
                        var n = t[e];
                        return "__lodash_hash_undefined__" === n ? void 0 : n
                    }
                    return o.call(t, e) ? t[e] : void 0
                }
            },
            1327: function(e, t, n) {
                var r = n(4536),
                    o = Object.prototype.hasOwnProperty;
                e.exports = function(e) {
                    var t = this.__data__;
                    return r ? void 0 !== t[e] : o.call(t, e)
                }
            },
            1866: function(e, t, n) {
                var r = n(4536);
                e.exports = function(e, t) {
                    var n = this.__data__;
                    return this.size += this.has(e) ? 0 : 1, n[e] = r && void 0 === t ? "__lodash_hash_undefined__" : t, this
                }
            },
            3824: function(e) {
                var t = Object.prototype.hasOwnProperty;
                e.exports = function(e) {
                    var n = e.length,
                        r = new e.constructor(n);
                    return n && "string" == typeof e[0] && t.call(e, "index") && (r.index = e.index, r.input = e.input), r
                }
            },
            9148: function(e, t, n) {
                var r = n(4318),
                    o = n(7157),
                    i = n(3147),
                    a = n(419),
                    s = n(7133);
                e.exports = function(e, t, n) {
                    var u = e.constructor;
                    switch (t) {
                        case "[object ArrayBuffer]":
                            return r(e);
                        case "[object Boolean]":
                        case "[object Date]":
                            return new u(+e);
                        case "[object DataView]":
                            return o(e, n);
                        case "[object Float32Array]":
                        case "[object Float64Array]":
                        case "[object Int8Array]":
                        case "[object Int16Array]":
                        case "[object Int32Array]":
                        case "[object Uint8Array]":
                        case "[object Uint8ClampedArray]":
                        case "[object Uint16Array]":
                        case "[object Uint32Array]":
                            return s(e, n);
                        case "[object Map]":
                            return new u;
                        case "[object Number]":
                        case "[object String]":
                            return new u(e);
                        case "[object RegExp]":
                            return i(e);
                        case "[object Set]":
                            return new u;
                        case "[object Symbol]":
                            return a(e)
                    }
                }
            },
            8517: function(e, t, n) {
                var r = n(3118),
                    o = n(5924),
                    i = n(5726);
                e.exports = function(e) {
                    return "function" != typeof e.constructor || i(e) ? {} : r(o(e))
                }
            },
            5776: function(e) {
                var t = /^(?:0|[1-9]\d*)$/;
                e.exports = function(e, n) {
                    var r = typeof e;
                    return !!(n = null == n ? 9007199254740991 : n) && ("number" == r || "symbol" != r && t.test(e)) && e > -1 && e % 1 == 0 && e < n
                }
            },
            6612: function(e, t, n) {
                var r = n(7813),
                    o = n(8612),
                    i = n(5776),
                    a = n(3218);
                e.exports = function(e, t, n) {
                    if (!a(n)) return !1;
                    var s = typeof t;
                    return !!("number" == s ? o(n) && i(t, n.length) : "string" == s && t in n) && r(n[t], e)
                }
            },
            5403: function(e, t, n) {
                var r = n(1469),
                    o = n(3448),
                    i = /\.|\[(?:[^[\]]*|(["'])(?:(?!\1)[^\\]|\\.)*?\1)\]/,
                    a = /^\w*$/;
                e.exports = function(e, t) {
                    if (r(e)) return !1;
                    var n = typeof e;
                    return !("number" != n && "symbol" != n && "boolean" != n && null != e && !o(e)) || (a.test(e) || !i.test(e) || null != t && e in Object(t))
                }
            },
            7019: function(e) {
                e.exports = function(e) {
                    var t = typeof e;
                    return "string" == t || "number" == t || "symbol" == t || "boolean" == t ? "__proto__" !== e : null === e
                }
            },
            5346: function(e, t, n) {
                var r, o = n(4429),
                    i = (r = /[^.]+$/.exec(o && o.keys && o.keys.IE_PROTO || "")) ? "Symbol(src)_1." + r : "";
                e.exports = function(e) {
                    return !!i && i in e
                }
            },
            5726: function(e) {
                var t = Object.prototype;
                e.exports = function(e) {
                    var n = e && e.constructor;
                    return e === ("function" == typeof n && n.prototype || t)
                }
            },
            9162: function(e, t, n) {
                var r = n(3218);
                e.exports = function(e) {
                    return e == e && !r(e)
                }
            },
            7040: function(e) {
                e.exports = function() {
                    this.__data__ = [], this.size = 0
                }
            },
            4125: function(e, t, n) {
                var r = n(8470),
                    o = Array.prototype.splice;
                e.exports = function(e) {
                    var t = this.__data__,
                        n = r(t, e);
                    return !(n < 0) && (n == t.length - 1 ? t.pop() : o.call(t, n, 1), --this.size, !0)
                }
            },
            2117: function(e, t, n) {
                var r = n(8470);
                e.exports = function(e) {
                    var t = this.__data__,
                        n = r(t, e);
                    return n < 0 ? void 0 : t[n][1]
                }
            },
            7518: function(e, t, n) {
                var r = n(8470);
                e.exports = function(e) {
                    return r(this.__data__, e) > -1
                }
            },
            4705: function(e, t, n) {
                var r = n(8470);
                e.exports = function(e, t) {
                    var n = this.__data__,
                        o = r(n, e);
                    return o < 0 ? (++this.size, n.push([e, t])) : n[o][1] = t, this
                }
            },
            4785: function(e, t, n) {
                var r = n(1989),
                    o = n(8407),
                    i = n(7071);
                e.exports = function() {
                    this.size = 0, this.__data__ = {
                        hash: new r,
                        map: new(i || o),
                        string: new r
                    }
                }
            },
            1285: function(e, t, n) {
                var r = n(5050);
                e.exports = function(e) {
                    var t = r(this, e).delete(e);
                    return this.size -= t ? 1 : 0, t
                }
            },
            6e3: function(e, t, n) {
                var r = n(5050);
                e.exports = function(e) {
                    return r(this, e).get(e)
                }
            },
            9916: function(e, t, n) {
                var r = n(5050);
                e.exports = function(e) {
                    return r(this, e).has(e)
                }
            },
            5265: function(e, t, n) {
                var r = n(5050);
                e.exports = function(e, t) {
                    var n = r(this, e),
                        o = n.size;
                    return n.set(e, t), this.size += n.size == o ? 0 : 1, this
                }
            },
            8776: function(e) {
                e.exports = function(e) {
                    var t = -1,
                        n = Array(e.size);
                    return e.forEach((function(e, r) {
                        n[++t] = [r, e]
                    })), n
                }
            },
            2634: function(e) {
                e.exports = function(e, t) {
                    return function(n) {
                        return null != n && (n[e] === t && (void 0 !== t || e in Object(n)))
                    }
                }
            },
            4523: function(e, t, n) {
                var r = n(8306);
                e.exports = function(e) {
                    var t = r(e, (function(e) {
                            return 500 === n.size && n.clear(), e
                        })),
                        n = t.cache;
                    return t
                }
            },
            4536: function(e, t, n) {
                var r = n(852)(Object, "create");
                e.exports = r
            },
            6916: function(e, t, n) {
                var r = n(5569)(Object.keys, Object);
                e.exports = r
            },
            3498: function(e) {
                e.exports = function(e) {
                    var t = [];
                    if (null != e)
                        for (var n in Object(e)) t.push(n);
                    return t
                }
            },
            1167: function(e, t, n) {
                e = n.nmd(e);
                var r = n(1957),
                    o = t && !t.nodeType && t,
                    i = o && e && !e.nodeType && e,
                    a = i && i.exports === o && r.process,
                    s = function() {
                        try {
                            var e = i && i.require && i.require("util").types;
                            return e || a && a.binding && a.binding("util")
                        } catch (e) {}
                    }();
                e.exports = s
            },
            2333: function(e) {
                var t = Object.prototype.toString;
                e.exports = function(e) {
                    return t.call(e)
                }
            },
            5569: function(e) {
                e.exports = function(e, t) {
                    return function(n) {
                        return e(t(n))
                    }
                }
            },
            5639: function(e, t, n) {
                var r = n(1957),
                    o = "object" == typeof self && self && self.Object === Object && self,
                    i = r || o || Function("return this")();
                e.exports = i
            },
            619: function(e) {
                e.exports = function(e) {
                    return this.__data__.set(e, "__lodash_hash_undefined__"), this
                }
            },
            2385: function(e) {
                e.exports = function(e) {
                    return this.__data__.has(e)
                }
            },
            1814: function(e) {
                e.exports = function(e) {
                    var t = -1,
                        n = Array(e.size);
                    return e.forEach((function(e) {
                        n[++t] = e
                    })), n
                }
            },
            7465: function(e, t, n) {
                var r = n(8407);
                e.exports = function() {
                    this.__data__ = new r, this.size = 0
                }
            },
            3779: function(e) {
                e.exports = function(e) {
                    var t = this.__data__,
                        n = t.delete(e);
                    return this.size = t.size, n
                }
            },
            7599: function(e) {
                e.exports = function(e) {
                    return this.__data__.get(e)
                }
            },
            4758: function(e) {
                e.exports = function(e) {
                    return this.__data__.has(e)
                }
            },
            4309: function(e, t, n) {
                var r = n(8407),
                    o = n(7071),
                    i = n(3369);
                e.exports = function(e, t) {
                    var n = this.__data__;
                    if (n instanceof r) {
                        var a = n.__data__;
                        if (!o || a.length < 199) return a.push([e, t]), this.size = ++n.size, this;
                        n = this.__data__ = new i(a)
                    }
                    return n.set(e, t), this.size = n.size, this
                }
            },
            5514: function(e, t, n) {
                var r = n(4523),
                    o = /[^.[\]]+|\[(?:(-?\d+(?:\.\d+)?)|(["'])((?:(?!\2)[^\\]|\\.)*?)\2)\]|(?=(?:\.|\[\])(?:\.|\[\]|$))/g,
                    i = /\\(\\)?/g,
                    a = r((function(e) {
                        var t = [];
                        return 46 === e.charCodeAt(0) && t.push(""), e.replace(o, (function(e, n, r, o) {
                            t.push(r ? o.replace(i, "$1") : n || e)
                        })), t
                    }));
                e.exports = a
            },
            327: function(e, t, n) {
                var r = n(3448);
                e.exports = function(e) {
                    if ("string" == typeof e || r(e)) return e;
                    var t = e + "";
                    return "0" == t && 1 / e == -Infinity ? "-0" : t
                }
            },
            346: function(e) {
                var t = Function.prototype.toString;
                e.exports = function(e) {
                    if (null != e) {
                        try {
                            return t.call(e)
                        } catch (e) {}
                        try {
                            return e + ""
                        } catch (e) {}
                    }
                    return ""
                }
            },
            7990: function(e) {
                var t = /\s/;
                e.exports = function(e) {
                    for (var n = e.length; n-- && t.test(e.charAt(n)););
                    return n
                }
            },
            2757: function(e) {
                var t = "\\u2700-\\u27bf",
                    n = "a-z\\xdf-\\xf6\\xf8-\\xff",
                    r = "A-Z\\xc0-\\xd6\\xd8-\\xde",
                    o = "\\xac\\xb1\\xd7\\xf7\\x00-\\x2f\\x3a-\\x40\\x5b-\\x60\\x7b-\\xbf\\u2000-\\u206f \\t\\x0b\\f\\xa0\\ufeff\\n\\r\\u2028\\u2029\\u1680\\u180e\\u2000\\u2001\\u2002\\u2003\\u2004\\u2005\\u2006\\u2007\\u2008\\u2009\\u200a\\u202f\\u205f\\u3000",
                    i = "[" + o + "]",
                    a = "\\d+",
                    s = "[\\u2700-\\u27bf]",
                    u = "[" + n + "]",
                    c = "[^\\ud800-\\udfff" + o + a + t + n + r + "]",
                    l = "(?:\\ud83c[\\udde6-\\uddff]){2}",
                    d = "[\\ud800-\\udbff][\\udc00-\\udfff]",
                    f = "[" + r + "]",
                    p = "(?:" + u + "|" + c + ")",
                    h = "(?:" + f + "|" + c + ")",
                    m = "(?:['â€™](?:d|ll|m|re|s|t|ve))?",
                    _ = "(?:['â€™](?:D|LL|M|RE|S|T|VE))?",
                    g = "(?:[\\u0300-\\u036f\\ufe20-\\ufe2f\\u20d0-\\u20ff]|\\ud83c[\\udffb-\\udfff])?",
                    v = "[\\ufe0e\\ufe0f]?",
                    y = v + g + ("(?:\\u200d(?:" + ["[^\\ud800-\\udfff]", l, d].join("|") + ")" + v + g + ")*"),
                    E = "(?:" + [s, l, d].join("|") + ")" + y,
                    b = RegExp([f + "?" + u + "+" + m + "(?=" + [i, f, "$"].join("|") + ")", h + "+" + _ + "(?=" + [i, f + p, "$"].join("|") + ")", f + "?" + p + "+" + m, f + "+" + _, "\\d*(?:1ST|2ND|3RD|(?![123])\\dTH)(?=\\b|[a-z_])", "\\d*(?:1st|2nd|3rd|(?![123])\\dth)(?=\\b|[A-Z_])", a, E].join("|"), "g");
                e.exports = function(e) {
                    return e.match(b) || []
                }
            },
            9567: function(e, t, n) {
                var r = n(554);
                e.exports = function(e, t) {
                    var n;
                    if ("function" != typeof t) throw new TypeError("Expected a function");
                    return e = r(e),
                        function() {
                            return --e > 0 && (n = t.apply(this, arguments)), e <= 1 && (t = void 0), n
                        }
                }
            },
            361: function(e, t, n) {
                var r = n(5990);
                e.exports = function(e) {
                    return r(e, 5)
                }
            },
            3816: function(e, t, n) {
                var r = n(9389),
                    o = n(9833),
                    i = /[\xc0-\xd6\xd8-\xf6\xf8-\xff\u0100-\u017f]/g,
                    a = RegExp("[\\u0300-\\u036f\\ufe20-\\ufe2f\\u20d0-\\u20ff]", "g");
                e.exports = function(e) {
                    return (e = o(e)) && e.replace(i, r).replace(a, "")
                }
            },
            6073: function(e, t, n) {
                e.exports = n(4486)
            },
            7813: function(e) {
                e.exports = function(e, t) {
                    return e === t || e != e && t != t
                }
            },
            711: function(e, t, n) {
                var r = n(6193),
                    o = n(3239),
                    i = n(7206),
                    a = n(1469),
                    s = n(6612);
                e.exports = function(e, t, n) {
                    var u = a(e) ? r : o;
                    return n && s(e, t, n) && (t = void 0), u(e, i(t, 3))
                }
            },
            3105: function(e, t, n) {
                var r = n(4963),
                    o = n(760),
                    i = n(7206),
                    a = n(1469);
                e.exports = function(e, t) {
                    return (a(e) ? r : o)(e, i(t, 3))
                }
            },
            3311: function(e, t, n) {
                var r = n(7740)(n(998));
                e.exports = r
            },
            998: function(e, t, n) {
                var r = n(1848),
                    o = n(7206),
                    i = n(554),
                    a = Math.max;
                e.exports = function(e, t, n) {
                    var s = null == e ? 0 : e.length;
                    if (!s) return -1;
                    var u = null == n ? 0 : i(n);
                    return u < 0 && (u = a(s + u, 0)), r(e, o(t, 3), u)
                }
            },
            4486: function(e, t, n) {
                var r = n(7412),
                    o = n(9881),
                    i = n(4290),
                    a = n(1469);
                e.exports = function(e, t) {
                    return (a(e) ? r : o)(e, i(t))
                }
            },
            7361: function(e, t, n) {
                var r = n(7786);
                e.exports = function(e, t, n) {
                    var o = null == e ? void 0 : r(e, t);
                    return void 0 === o ? n : o
                }
            },
            9095: function(e, t, n) {
                var r = n(13),
                    o = n(222);
                e.exports = function(e, t) {
                    return null != e && o(e, t, r)
                }
            },
            6557: function(e) {
                e.exports = function(e) {
                    return e
                }
            },
            4174: function(e, t, n) {
                var r = n(5600),
                    o = n(8601),
                    i = n(4841);
                e.exports = function(e, t, n) {
                    return t = o(t), void 0 === n ? (n = t, t = 0) : n = o(n), e = i(e), r(e, t, n)
                }
            },
            5694: function(e, t, n) {
                var r = n(9454),
                    o = n(7005),
                    i = Object.prototype,
                    a = i.hasOwnProperty,
                    s = i.propertyIsEnumerable,
                    u = r(function() {
                        return arguments
                    }()) ? r : function(e) {
                        return o(e) && a.call(e, "callee") && !s.call(e, "callee")
                    };
                e.exports = u
            },
            1469: function(e) {
                var t = Array.isArray;
                e.exports = t
            },
            8612: function(e, t, n) {
                var r = n(3560),
                    o = n(1780);
                e.exports = function(e) {
                    return null != e && o(e.length) && !r(e)
                }
            },
            1584: function(e, t, n) {
                var r = n(4239),
                    o = n(7005);
                e.exports = function(e) {
                    return !0 === e || !1 === e || o(e) && "[object Boolean]" == r(e)
                }
            },
            4144: function(e, t, n) {
                e = n.nmd(e);
                var r = n(5639),
                    o = n(5062),
                    i = t && !t.nodeType && t,
                    a = i && e && !e.nodeType && e,
                    s = a && a.exports === i ? r.Buffer : void 0,
                    u = (s ? s.isBuffer : void 0) || o;
                e.exports = u
            },
            1609: function(e, t, n) {
                var r = n(280),
                    o = n(4160),
                    i = n(5694),
                    a = n(1469),
                    s = n(8612),
                    u = n(4144),
                    c = n(5726),
                    l = n(6719),
                    d = Object.prototype.hasOwnProperty;
                e.exports = function(e) {
                    if (null == e) return !0;
                    if (s(e) && (a(e) || "string" == typeof e || "function" == typeof e.splice || u(e) || l(e) || i(e))) return !e.length;
                    var t = o(e);
                    if ("[object Map]" == t || "[object Set]" == t) return !e.size;
                    if (c(e)) return !r(e).length;
                    for (var n in e)
                        if (d.call(e, n)) return !1;
                    return !0
                }
            },
            3560: function(e, t, n) {
                var r = n(4239),
                    o = n(3218);
                e.exports = function(e) {
                    if (!o(e)) return !1;
                    var t = r(e);
                    return "[object Function]" == t || "[object GeneratorFunction]" == t || "[object AsyncFunction]" == t || "[object Proxy]" == t
                }
            },
            1780: function(e) {
                e.exports = function(e) {
                    return "number" == typeof e && e > -1 && e % 1 == 0 && e <= 9007199254740991
                }
            },
            6688: function(e, t, n) {
                var r = n(5588),
                    o = n(1717),
                    i = n(1167),
                    a = i && i.isMap,
                    s = a ? o(a) : r;
                e.exports = s
            },
            4293: function(e) {
                e.exports = function(e) {
                    return null == e
                }
            },
            5220: function(e) {
                e.exports = function(e) {
                    return null === e
                }
            },
            1763: function(e, t, n) {
                var r = n(4239),
                    o = n(7005);
                e.exports = function(e) {
                    return "number" == typeof e || o(e) && "[object Number]" == r(e)
                }
            },
            3218: function(e) {
                e.exports = function(e) {
                    var t = typeof e;
                    return null != e && ("object" == t || "function" == t)
                }
            },
            7005: function(e) {
                e.exports = function(e) {
                    return null != e && "object" == typeof e
                }
            },
            2928: function(e, t, n) {
                var r = n(9221),
                    o = n(1717),
                    i = n(1167),
                    a = i && i.isSet,
                    s = a ? o(a) : r;
                e.exports = s
            },
            7037: function(e, t, n) {
                var r = n(4239),
                    o = n(1469),
                    i = n(7005);
                e.exports = function(e) {
                    return "string" == typeof e || !o(e) && i(e) && "[object String]" == r(e)
                }
            },
            3448: function(e, t, n) {
                var r = n(4239),
                    o = n(7005);
                e.exports = function(e) {
                    return "symbol" == typeof e || o(e) && "[object Symbol]" == r(e)
                }
            },
            6719: function(e, t, n) {
                var r = n(8749),
                    o = n(1717),
                    i = n(1167),
                    a = i && i.isTypedArray,
                    s = a ? o(a) : r;
                e.exports = s
            },
            3674: function(e, t, n) {
                var r = n(4636),
                    o = n(280),
                    i = n(8612);
                e.exports = function(e) {
                    return i(e) ? r(e) : o(e)
                }
            },
            1704: function(e, t, n) {
                var r = n(4636),
                    o = n(313),
                    i = n(8612);
                e.exports = function(e) {
                    return i(e) ? r(e, !0) : o(e)
                }
            },
            8306: function(e, t, n) {
                var r = n(3369);

                function o(e, t) {
                    if ("function" != typeof e || null != t && "function" != typeof t) throw new TypeError("Expected a function");
                    var n = function() {
                        var r = arguments,
                            o = t ? t.apply(this, r) : r[0],
                            i = n.cache;
                        if (i.has(o)) return i.get(o);
                        var a = e.apply(this, r);
                        return n.cache = i.set(o, a) || i, a
                    };
                    return n.cache = new(o.Cache || r), n
                }
                o.Cache = r, e.exports = o
            },
            1463: function(e, t, n) {
                var r = n(9567);
                e.exports = function(e) {
                    return r(2, e)
                }
            },
            9601: function(e, t, n) {
                var r = n(371),
                    o = n(9152),
                    i = n(5403),
                    a = n(327);
                e.exports = function(e) {
                    return i(e) ? r(a(e)) : o(e)
                }
            },
            4061: function(e, t, n) {
                var r = n(2663),
                    o = n(9881),
                    i = n(7206),
                    a = n(107),
                    s = n(1469);
                e.exports = function(e, t, n) {
                    var u = s(e) ? r : a,
                        c = arguments.length < 3;
                    return u(e, i(t, 4), n, c, o)
                }
            },
            8613: function(e, t, n) {
                var r = n(1811),
                    o = n(3560),
                    i = n(327);
                e.exports = function(e, t, n) {
                    var a = -1,
                        s = (t = r(t, e)).length;
                    for (s || (s = 1, e = void 0); ++a < s;) {
                        var u = null == e ? void 0 : e[i(t[a])];
                        void 0 === u && (a = s, u = n), e = o(u) ? u.call(e) : u
                    }
                    return e
                }
            },
            6968: function(e, t, n) {
                var r = n(611);
                e.exports = function(e, t, n) {
                    return null == e ? e : r(e, t, n)
                }
            },
            1865: function(e, t, n) {
                var r = n(5393)((function(e, t, n) {
                    return e + (n ? "_" : "") + t.toLowerCase()
                }));
                e.exports = r
            },
            9704: function(e, t, n) {
                var r = n(2908),
                    o = n(7206),
                    i = n(5076),
                    a = n(1469),
                    s = n(6612);
                e.exports = function(e, t, n) {
                    var u = a(e) ? r : i;
                    return n && s(e, t, n) && (t = void 0), u(e, o(t, 3))
                }
            },
            479: function(e) {
                e.exports = function() {
                    return []
                }
            },
            5062: function(e) {
                e.exports = function() {
                    return !1
                }
            },
            8601: function(e, t, n) {
                var r = n(4841),
                    o = 1 / 0;
                e.exports = function(e) {
                    return e ? (e = r(e)) === o || e === -1 / 0 ? 17976931348623157e292 * (e < 0 ? -1 : 1) : e == e ? e : 0 : 0 === e ? e : 0
                }
            },
            554: function(e, t, n) {
                var r = n(8601);
                e.exports = function(e) {
                    var t = r(e),
                        n = t % 1;
                    return t == t ? n ? t - n : t : 0
                }
            },
            4841: function(e, t, n) {
                var r = n(7561),
                    o = n(3218),
                    i = n(3448),
                    a = /^[-+]0x[0-9a-f]+$/i,
                    s = /^0b[01]+$/i,
                    u = /^0o[0-7]+$/i,
                    c = parseInt;
                e.exports = function(e) {
                    if ("number" == typeof e) return e;
                    if (i(e)) return NaN;
                    if (o(e)) {
                        var t = "function" == typeof e.valueOf ? e.valueOf() : e;
                        e = o(t) ? t + "" : t
                    }
                    if ("string" != typeof e) return 0 === e ? e : +e;
                    e = r(e);
                    var n = s.test(e);
                    return n || u.test(e) ? c(e.slice(2), n ? 2 : 8) : a.test(e) ? NaN : +e
                }
            },
            9833: function(e, t, n) {
                var r = n(531);
                e.exports = function(e) {
                    return null == e ? "" : r(e)
                }
            },
            8748: function(e, t, n) {
                var r = n(9029),
                    o = n(3157),
                    i = n(9833),
                    a = n(2757);
                e.exports = function(e, t, n) {
                    return e = i(e), void 0 === (t = n ? void 0 : t) ? o(e) ? a(e) : r(e) : e.match(t) || []
                }
            },
            312: function(e, t, n) {
                "use strict";
                n.r(t), t.default = {
                    defaultFontBold: '"Widget Default Noto Sans Bold"',
                    defaultFontBlack: '"Widget Default Noto Sans Black"'
                }
            },
            2981: function(e, t, n) {
                "use strict";
                n.r(t), t.default = {}
            },
            1795: function(e, t, n) {
                var r;
                e = n.nmd(e),
                    function() {
                        "use strict";
                        var o = {
                                function: !0,
                                object: !0
                            },
                            i = o[typeof window] && window || this,
                            a = o[typeof t] && t,
                            s = o.object && e && !e.nodeType && e,
                            u = a && s && "object" == typeof n.g && n.g;
                        !u || u.global !== u && u.window !== u && u.self !== u || (i = u);
                        var c = Math.pow(2, 53) - 1,
                            l = /\bOpera/,
                            d = Object.prototype,
                            f = d.hasOwnProperty,
                            p = d.toString;

                        function h(e) {
                            return (e = String(e)).charAt(0).toUpperCase() + e.slice(1)
                        }

                        function m(e) {
                            return e = E(e), /^(?:webOS|i(?:OS|P))/.test(e) ? e : h(e)
                        }

                        function _(e, t) {
                            for (var n in e) f.call(e, n) && t(e[n], n, e)
                        }

                        function g(e) {
                            return null == e ? h(e) : p.call(e).slice(8, -1)
                        }

                        function v(e) {
                            return String(e).replace(/([ -])(?!$)/g, "$1?")
                        }

                        function y(e, t) {
                            var n = null;
                            return function(e, t) {
                                var n = -1,
                                    r = e ? e.length : 0;
                                if ("number" == typeof r && r > -1 && r <= c)
                                    for (; ++n < r;) t(e[n], n, e);
                                else _(e, t)
                            }(e, (function(r, o) {
                                n = t(n, r, o, e)
                            })), n
                        }

                        function E(e) {
                            return String(e).replace(/^ +| +$/g, "")
                        }
                        var b = function e(t) {
                            var n = i,
                                r = t && "object" == typeof t && "String" != g(t);
                            r && (n = t, t = null);
                            var o = n.navigator || {},
                                a = o.userAgent || "";
                            t || (t = a);
                            var s, u, c, d, f, h = r ? !!o.likeChrome : /\bChrome\b/.test(t) && !/internal|\n/i.test(p.toString()),
                                b = "Object",
                                O = r ? b : "ScriptBridgingProxyObject",
                                w = r ? b : "Environment",
                                S = r && n.java ? "JavaPackage" : g(n.java),
                                P = r ? b : "RuntimeObject",
                                A = /\bJava/.test(S) && n.java,
                                T = A && g(n.environment) == w,
                                C = A ? "a" : "Î±",
                                D = A ? "b" : "Î²",
                                x = n.document || {},
                                I = n.operamini || n.opera,
                                N = l.test(N = r && I ? I["[[Class]]"] : g(I)) ? N : I = null,
                                M = t,
                                L = [],
                                W = null,
                                R = t == a,
                                j = R && I && "function" == typeof I.version && I.version(),
                                U = y([{
                                    label: "EdgeHTML",
                                    pattern: "Edge"
                                }, "Trident", {
                                    label: "WebKit",
                                    pattern: "AppleWebKit"
                                }, "iCab", "Presto", "NetFront", "Tasman", "KHTML", "Gecko"], (function(e, n) {
                                    return e || RegExp("\\b" + (n.pattern || v(n)) + "\\b", "i").exec(t) && (n.label || n)
                                })),
                                k = function(e) {
                                    return y(e, (function(e, n) {
                                        return e || RegExp("\\b" + (n.pattern || v(n)) + "\\b", "i").exec(t) && (n.label || n)
                                    }))
                                }(["Adobe AIR", "Arora", "Avant Browser", "Breach", "Camino", "Electron", "Epiphany", "Fennec", "Flock", "Galeon", "GreenBrowser", "iCab", "Iceweasel", "K-Meleon", "Konqueror", "Lunascape", "Maxthon", {
                                    label: "Microsoft Edge",
                                    pattern: "(?:Edge|Edg|EdgA|EdgiOS)"
                                }, "Midori", "Nook Browser", "PaleMoon", "PhantomJS", "Raven", "Rekonq", "RockMelt", {
                                    label: "Samsung Internet",
                                    pattern: "SamsungBrowser"
                                }, "SeaMonkey", {
                                    label: "Silk",
                                    pattern: "(?:Cloud9|Silk-Accelerated)"
                                }, "Sleipnir", "SlimBrowser", {
                                    label: "SRWare Iron",
                                    pattern: "Iron"
                                }, "Sunrise", "Swiftfox", "Vivaldi", "Waterfox", "WebPositive", {
                                    label: "Yandex Browser",
                                    pattern: "YaBrowser"
                                }, {
                                    label: "UC Browser",
                                    pattern: "UCBrowser"
                                }, "Opera Mini", {
                                    label: "Opera Mini",
                                    pattern: "OPiOS"
                                }, "Opera", {
                                    label: "Opera",
                                    pattern: "OPR"
                                }, "Chromium", "Chrome", {
                                    label: "Chrome",
                                    pattern: "(?:HeadlessChrome)"
                                }, {
                                    label: "Chrome Mobile",
                                    pattern: "(?:CriOS|CrMo)"
                                }, {
                                    label: "Firefox",
                                    pattern: "(?:Firefox|Minefield)"
                                }, {
                                    label: "Firefox for iOS",
                                    pattern: "FxiOS"
                                }, {
                                    label: "IE",
                                    pattern: "IEMobile"
                                }, {
                                    label: "IE",
                                    pattern: "MSIE"
                                }, "Safari"]),
                                B = F([{
                                    label: "BlackBerry",
                                    pattern: "BB10"
                                }, "BlackBerry", {
                                    label: "Galaxy S",
                                    pattern: "GT-I9000"
                                }, {
                                    label: "Galaxy S2",
                                    pattern: "GT-I9100"
                                }, {
                                    label: "Galaxy S3",
                                    pattern: "GT-I9300"
                                }, {
                                    label: "Galaxy S4",
                                    pattern: "GT-I9500"
                                }, {
                                    label: "Galaxy S5",
                                    pattern: "SM-G900"
                                }, {
                                    label: "Galaxy S6",
                                    pattern: "SM-G920"
                                }, {
                                    label: "Galaxy S6 Edge",
                                    pattern: "SM-G925"
                                }, {
                                    label: "Galaxy S7",
                                    pattern: "SM-G930"
                                }, {
                                    label: "Galaxy S7 Edge",
                                    pattern: "SM-G935"
                                }, "Google TV", "Lumia", "iPad", "iPod", "iPhone", "Kindle", {
                                    label: "Kindle Fire",
                                    pattern: "(?:Cloud9|Silk-Accelerated)"
                                }, "Nexus", "Nook", "PlayBook", "PlayStation Vita", "PlayStation", "TouchPad", "Transformer", {
                                    label: "Wii U",
                                    pattern: "WiiU"
                                }, "Wii", "Xbox One", {
                                    label: "Xbox 360",
                                    pattern: "Xbox"
                                }, "Xoom"]),
                                G = function(e) {
                                    return y(e, (function(e, n, r) {
                                        return e || (n[B] || n[/^[a-z]+(?: +[a-z]+\b)*/i.exec(B)] || RegExp("\\b" + v(r) + "(?:\\b|\\w*\\d)", "i").exec(t)) && r
                                    }))
                                }({
                                    Apple: {
                                        iPad: 1,
                                        iPhone: 1,
                                        iPod: 1
                                    },
                                    Alcatel: {},
                                    Archos: {},
                                    Amazon: {
                                        Kindle: 1,
                                        "Kindle Fire": 1
                                    },
                                    Asus: {
                                        Transformer: 1
                                    },
                                    "Barnes & Noble": {
                                        Nook: 1
                                    },
                                    BlackBerry: {
                                        PlayBook: 1
                                    },
                                    Google: {
                                        "Google TV": 1,
                                        Nexus: 1
                                    },
                                    HP: {
                                        TouchPad: 1
                                    },
                                    HTC: {},
                                    Huawei: {},
                                    Lenovo: {},
                                    LG: {},
                                    Microsoft: {
                                        Xbox: 1,
                                        "Xbox One": 1
                                    },
                                    Motorola: {
                                        Xoom: 1
                                    },
                                    Nintendo: {
                                        "Wii U": 1,
                                        Wii: 1
                                    },
                                    Nokia: {
                                        Lumia: 1
                                    },
                                    Oppo: {},
                                    Samsung: {
                                        "Galaxy S": 1,
                                        "Galaxy S2": 1,
                                        "Galaxy S3": 1,
                                        "Galaxy S4": 1
                                    },
                                    Sony: {
                                        PlayStation: 1,
                                        "PlayStation Vita": 1
                                    },
                                    Xiaomi: {
                                        Mi: 1,
                                        Redmi: 1
                                    }
                                }),
                                V = function(e) {
                                    return y(e, (function(e, n) {
                                        var r = n.pattern || v(n);
                                        return !e && (e = RegExp("\\b" + r + "(?:/[\\d.]+|[ \\w.]*)", "i").exec(t)) && (e = function(e, t, n) {
                                            var r = {
                                                "10.0": "10",
                                                6.4: "10 Technical Preview",
                                                6.3: "8.1",
                                                6.2: "8",
                                                6.1: "Server 2008 R2 / 7",
                                                "6.0": "Server 2008 / Vista",
                                                5.2: "Server 2003 / XP 64-bit",
                                                5.1: "XP",
                                                5.01: "2000 SP1",
                                                "5.0": "2000",
                                                "4.0": "NT",
                                                "4.90": "ME"
                                            };
                                            return t && n && /^Win/i.test(e) && !/^Windows Phone /i.test(e) && (r = r[/[\d.]+$/.exec(e)]) && (e = "Windows " + r), e = String(e), t && n && (e = e.replace(RegExp(t, "i"), n)), m(e.replace(/ ce$/i, " CE").replace(/\bhpw/i, "web").replace(/\bMacintosh\b/, "Mac OS").replace(/_PowerPC\b/i, " OS").replace(/\b(OS X) [^ \d]+/i, "$1").replace(/\bMac (OS X)\b/, "$1").replace(/\/(\d)/, " $1").replace(/_/g, ".").replace(/(?: BePC|[ .]*fc[ \d.]+)$/i, "").replace(/\bx86\.64\b/gi, "x86_64").replace(/\b(Windows Phone) OS\b/, "$1").replace(/\b(Chrome OS \w+) [\d.]+\b/, "$1").split(" on ")[0])
                                        }(e, r, n.label || n)), e
                                    }))
                                }(["Windows Phone", "KaiOS", "Android", "CentOS", {
                                    label: "Chrome OS",
                                    pattern: "CrOS"
                                }, "Debian", {
                                    label: "DragonFly BSD",
                                    pattern: "DragonFly"
                                }, "Fedora", "FreeBSD", "Gentoo", "Haiku", "Kubuntu", "Linux Mint", "OpenBSD", "Red Hat", "SuSE", "Ubuntu", "Xubuntu", "Cygwin", "Symbian OS", "hpwOS", "webOS ", "webOS", "Tablet OS", "Tizen", "Linux", "Mac OS X", "Macintosh", "Mac", "Windows 98;", "Windows "]);

                            function F(e) {
                                return y(e, (function(e, n) {
                                    var r = n.pattern || v(n);
                                    return !e && (e = RegExp("\\b" + r + " *\\d+[.\\w_]*", "i").exec(t) || RegExp("\\b" + r + " *\\w+-[\\w]*", "i").exec(t) || RegExp("\\b" + r + "(?:; *(?:[a-z]+[_-])?[a-z]+\\d+|[^ ();-]*)", "i").exec(t)) && ((e = String(n.label && !RegExp(r, "i").test(n.label) ? n.label : e).split("/"))[1] && !/[\d.]+/.test(e[0]) && (e[0] += " " + e[1]), n = n.label || n, e = m(e[0].replace(RegExp(r, "i"), n).replace(RegExp("; *(?:" + n + "[_-])?", "i"), " ").replace(RegExp("(" + n + ")[-_.]?(\\w)", "i"), "$1 $2"))), e
                                }))
                            }

                            function H(e) {
                                return y(e, (function(e, n) {
                                    return e || (RegExp(n + "(?:-[\\d.]+/|(?: for [\\w-]+)?[ /-])([\\d.]+[^ ();/_-]*)", "i").exec(t) || 0)[1] || null
                                }))
                            }
                            if (U && (U = [U]), /\bAndroid\b/.test(V) && !B && (s = /\bAndroid[^;]*;(.*?)(?:Build|\) AppleWebKit)\b/i.exec(t)) && (B = E(s[1]).replace(/^[a-z]{2}-[a-z]{2};\s*/i, "") || null), G && !B ? B = F([G]) : G && B && (B = B.replace(RegExp("^(" + v(G) + ")[-_.\\s]", "i"), G + " ").replace(RegExp("^(" + v(G) + ")[-_.]?(\\w)", "i"), G + " $2")), (s = /\bGoogle TV\b/.exec(B)) && (B = s[0]), /\bSimulator\b/i.test(t) && (B = (B ? B + " " : "") + "Simulator"), "Opera Mini" == k && /\bOPiOS\b/.test(t) && L.push("running in Turbo/Uncompressed mode"), "IE" == k && /\blike iPhone OS\b/.test(t) ? (G = (s = e(t.replace(/like iPhone OS/, ""))).manufacturer, B = s.product) : /^iP/.test(B) ? (k || (k = "Safari"), V = "iOS" + ((s = / OS ([\d_]+)/i.exec(t)) ? " " + s[1].replace(/_/g, ".") : "")) : "Konqueror" == k && /^Linux\b/i.test(V) ? V = "Kubuntu" : G && "Google" != G && (/Chrome/.test(k) && !/\bMobile Safari\b/i.test(t) || /\bVita\b/.test(B)) || /\bAndroid\b/.test(V) && /^Chrome/.test(k) && /\bVersion\//i.test(t) ? (k = "Android Browser", V = /\bAndroid\b/.test(V) ? V : "Android") : "Silk" == k ? (/\bMobi/i.test(t) || (V = "Android", L.unshift("desktop mode")), /Accelerated *= *true/i.test(t) && L.unshift("accelerated")) : "UC Browser" == k && /\bUCWEB\b/.test(t) ? L.push("speed mode") : "PaleMoon" == k && (s = /\bFirefox\/([\d.]+)\b/.exec(t)) ? L.push("identifying as Firefox " + s[1]) : "Firefox" == k && (s = /\b(Mobile|Tablet|TV)\b/i.exec(t)) ? (V || (V = "Firefox OS"), B || (B = s[1])) : !k || (s = !/\bMinefield\b/i.test(t) && /\b(?:Firefox|Safari)\b/.exec(k)) ? (k && !B && /[\/,]|^[^(]+?\)/.test(t.slice(t.indexOf(s + "/") + 8)) && (k = null), (s = B || G || V) && (B || G || /\b(?:Android|Symbian OS|Tablet OS|webOS)\b/.test(V)) && (k = /[a-z]+(?: Hat)?/i.exec(/\bAndroid\b/.test(V) ? V : s) + " Browser")) : "Electron" == k && (s = (/\bChrome\/([\d.]+)\b/.exec(t) || 0)[1]) && L.push("Chromium " + s), j || (j = H(["(?:Cloud9|CriOS|CrMo|Edge|Edg|EdgA|EdgiOS|FxiOS|HeadlessChrome|IEMobile|Iron|Opera ?Mini|OPiOS|OPR|Raven|SamsungBrowser|Silk(?!/[\\d.]+$)|UCBrowser|YaBrowser)", "Version", v(k), "(?:Firefox|Minefield|NetFront)"])), (s = ("iCab" == U && parseFloat(j) > 3 ? "WebKit" : /\bOpera\b/.test(k) && (/\bOPR\b/.test(t) ? "Blink" : "Presto")) || /\b(?:Midori|Nook|Safari)\b/i.test(t) && !/^(?:Trident|EdgeHTML)$/.test(U) && "WebKit" || !U && /\bMSIE\b/i.test(t) && ("Mac OS" == V ? "Tasman" : "Trident") || "WebKit" == U && /\bPlayStation\b(?! Vita\b)/i.test(k) && "NetFront") && (U = [s]), "IE" == k && (s = (/; *(?:XBLWP|ZuneWP)(\d+)/i.exec(t) || 0)[1]) ? (k += " Mobile", V = "Windows Phone " + (/\+$/.test(s) ? s : s + ".x"), L.unshift("desktop mode")) : /\bWPDesktop\b/i.test(t) ? (k = "IE Mobile", V = "Windows Phone 8.x", L.unshift("desktop mode"), j || (j = (/\brv:([\d.]+)/.exec(t) || 0)[1])) : "IE" != k && "Trident" == U && (s = /\brv:([\d.]+)/.exec(t)) && (k && L.push("identifying as " + k + (j ? " " + j : "")), k = "IE", j = s[1]), R) {
                                if (d = "global", f = null != (c = n) ? typeof c[d] : "number", /^(?:boolean|number|string|undefined)$/.test(f) || "object" == f && !c[d]) g(s = n.runtime) == O ? (k = "Adobe AIR", V = s.flash.system.Capabilities.os) : g(s = n.phantom) == P ? (k = "PhantomJS", j = (s = s.version || null) && s.major + "." + s.minor + "." + s.patch) : "number" == typeof x.documentMode && (s = /\bTrident\/(\d+)/i.exec(t)) ? (j = [j, x.documentMode], (s = +s[1] + 4) != j[1] && (L.push("IE " + j[1] + " mode"), U && (U[1] = ""), j[1] = s), j = "IE" == k ? String(j[1].toFixed(1)) : j[0]) : "number" == typeof x.documentMode && /^(?:Chrome|Firefox)\b/.test(k) && (L.push("masking as " + k + " " + j), k = "IE", j = "11.0", U = ["Trident"], V = "Windows");
                                else if (A && (M = (s = A.lang.System).getProperty("os.arch"), V = V || s.getProperty("os.name") + " " + s.getProperty("os.version")), T) {
                                    try {
                                        j = n.require("ringo/engine").version.join("."), k = "RingoJS"
                                    } catch (e) {
                                        (s = n.system) && s.global.system == n.system && (k = "Narwhal", V || (V = s[0].os || null))
                                    }
                                    k || (k = "Rhino")
                                } else "object" == typeof n.process && !n.process.browser && (s = n.process) && ("object" == typeof s.versions && ("string" == typeof s.versions.electron ? (L.push("Node " + s.versions.node), k = "Electron", j = s.versions.electron) : "string" == typeof s.versions.nw && (L.push("Chromium " + j, "Node " + s.versions.node), k = "NW.js", j = s.versions.nw)), k || (k = "Node.js", M = s.arch, V = s.platform, j = (j = /[\d.]+/.exec(s.version)) ? j[0] : null));
                                V = V && m(V)
                            }
                            if (j && (s = /(?:[ab]|dp|pre|[ab]\d+pre)(?:\d+\+?)?$/i.exec(j) || /(?:alpha|beta)(?: ?\d)?/i.exec(t + ";" + (R && o.appMinorVersion)) || /\bMinefield\b/i.test(t) && "a") && (W = /b/i.test(s) ? "beta" : "alpha", j = j.replace(RegExp(s + "\\+?$"), "") + ("beta" == W ? D : C) + (/\d+\+?/.exec(s) || "")), "Fennec" == k || "Firefox" == k && /\b(?:Android|Firefox OS|KaiOS)\b/.test(V)) k = "Firefox Mobile";
                            else if ("Maxthon" == k && j) j = j.replace(/\.[\d.]+/, ".x");
                            else if (/\bXbox\b/i.test(B)) "Xbox 360" == B && (V = null), "Xbox 360" == B && /\bIEMobile\b/.test(t) && L.unshift("mobile mode");
                            else if (!/^(?:Chrome|IE|Opera)$/.test(k) && (!k || B || /Browser|Mobi/.test(k)) || "Windows CE" != V && !/Mobi/i.test(t))
                                if ("IE" == k && R) try {
                                    null === n.external && L.unshift("platform preview")
                                } catch (e) {
                                    L.unshift("embedded")
                                } else(/\bBlackBerry\b/.test(B) || /\bBB10\b/.test(t)) && (s = (RegExp(B.replace(/ +/g, " *") + "/([.\\d]+)", "i").exec(t) || 0)[1] || j) ? (V = ((s = [s, /BB10/.test(t)])[1] ? (B = null, G = "BlackBerry") : "Device Software") + " " + s[0], j = null) : this != _ && "Wii" != B && (R && I || /Opera/.test(k) && /\b(?:MSIE|Firefox)\b/i.test(t) || "Firefox" == k && /\bOS X (?:\d+\.){2,}/.test(V) || "IE" == k && (V && !/^Win/.test(V) && j > 5.5 || /\bWindows XP\b/.test(V) && j > 8 || 8 == j && !/\bTrident\b/.test(t))) && !l.test(s = e.call(_, t.replace(l, "") + ";")) && s.name && (s = "ing as " + s.name + ((s = s.version) ? " " + s : ""), l.test(k) ? (/\bIE\b/.test(s) && "Mac OS" == V && (V = null), s = "identify" + s) : (s = "mask" + s, k = N ? m(N.replace(/([a-z])([A-Z])/g, "$1 $2")) : "Opera", /\bIE\b/.test(s) && (V = null), R || (j = null)), U = ["Presto"], L.push(s));
                                else k += " Mobile";
                            (s = (/\bAppleWebKit\/([\d.]+\+?)/i.exec(t) || 0)[1]) && (s = [parseFloat(s.replace(/\.(\d)$/, ".0$1")), s], "Safari" == k && "+" == s[1].slice(-1) ? (k = "WebKit Nightly", W = "alpha", j = s[1].slice(0, -1)) : j != s[1] && j != (s[2] = (/\bSafari\/([\d.]+\+?)/i.exec(t) || 0)[1]) || (j = null), s[1] = (/\b(?:Headless)?Chrome\/([\d.]+)/i.exec(t) || 0)[1], 537.36 == s[0] && 537.36 == s[2] && parseFloat(s[1]) >= 28 && "WebKit" == U && (U = ["Blink"]), R && (h || s[1]) ? (U && (U[1] = "like Chrome"), s = s[1] || ((s = s[0]) < 530 ? 1 : s < 532 ? 2 : s < 532.05 ? 3 : s < 533 ? 4 : s < 534.03 ? 5 : s < 534.07 ? 6 : s < 534.1 ? 7 : s < 534.13 ? 8 : s < 534.16 ? 9 : s < 534.24 ? 10 : s < 534.3 ? 11 : s < 535.01 ? 12 : s < 535.02 ? "13+" : s < 535.07 ? 15 : s < 535.11 ? 16 : s < 535.19 ? 17 : s < 536.05 ? 18 : s < 536.1 ? 19 : s < 537.01 ? 20 : s < 537.11 ? "21+" : s < 537.13 ? 23 : s < 537.18 ? 24 : s < 537.24 ? 25 : s < 537.36 ? 26 : "Blink" != U ? "27" : "28")) : (U && (U[1] = "like Safari"), s = (s = s[0]) < 400 ? 1 : s < 500 ? 2 : s < 526 ? 3 : s < 533 ? 4 : s < 534 ? "4+" : s < 535 ? 5 : s < 537 ? 6 : s < 538 ? 7 : s < 601 ? 8 : s < 602 ? 9 : s < 604 ? 10 : s < 606 ? 11 : s < 608 ? 12 : "12"), U && (U[1] += " " + (s += "number" == typeof s ? ".x" : /[.+]/.test(s) ? "" : "+")), "Safari" == k && (!j || parseInt(j) > 45) ? j = s : "Chrome" == k && /\bHeadlessChrome/i.test(t) && L.unshift("headless")), "Opera" == k && (s = /\bzbov|zvav$/.exec(V)) ? (k += " ", L.unshift("desktop mode"), "zvav" == s ? (k += "Mini", j = null) : k += "Mobile", V = V.replace(RegExp(" *" + s + "$"), "")) : "Safari" == k && /\bChrome\b/.exec(U && U[1]) ? (L.unshift("desktop mode"), k = "Chrome Mobile", j = null, /\bOS X\b/.test(V) ? (G = "Apple", V = "iOS 4.3+") : V = null) : /\bSRWare Iron\b/.test(k) && !j && (j = H("Chrome")), j && 0 == j.indexOf(s = /[\d.]+$/.exec(V)) && t.indexOf("/" + s + "-") > -1 && (V = E(V.replace(s, ""))), V && -1 != V.indexOf(k) && !RegExp(k + " OS").test(V) && (V = V.replace(RegExp(" *" + v(k) + " *"), "")), U && !/\b(?:Avant|Nook)\b/.test(k) && (/Browser|Lunascape|Maxthon/.test(k) || "Safari" != k && /^iOS/.test(V) && /\bSafari\b/.test(U[1]) || /^(?:Adobe|Arora|Breach|Midori|Opera|Phantom|Rekonq|Rock|Samsung Internet|Sleipnir|SRWare Iron|Vivaldi|Web)/.test(k) && U[1]) && (s = U[U.length - 1]) && L.push(s), L.length && (L = ["(" + L.join("; ") + ")"]), G && B && B.indexOf(G) < 0 && L.push("on " + G), B && L.push((/^on /.test(L[L.length - 1]) ? "" : "on ") + B), V && (s = / ([\d.+]+)$/.exec(V), u = s && "/" == V.charAt(V.length - s[0].length - 1), V = {
                                architecture: 32,
                                family: s && !u ? V.replace(s[0], "") : V,
                                version: s ? s[1] : null,
                                toString: function() {
                                    var e = this.version;
                                    return this.family + (e && !u ? " " + e : "") + (64 == this.architecture ? " 64-bit" : "")
                                }
                            }), (s = /\b(?:AMD|IA|Win|WOW|x86_|x)64\b/i.exec(M)) && !/\bi686\b/i.test(M) ? (V && (V.architecture = 64, V.family = V.family.replace(RegExp(" *" + s), "")), k && (/\bWOW64\b/i.test(t) || R && /\w(?:86|32)$/.test(o.cpuClass || o.platform) && !/\bWin64; x64\b/i.test(t)) && L.unshift("32-bit")) : V && /^OS X/.test(V.family) && "Chrome" == k && parseFloat(j) >= 39 && (V.architecture = 64), t || (t = null);
                            var K = {};
                            return K.description = t, K.layout = U && U[0], K.manufacturer = G, K.name = k, K.prerelease = W, K.product = B, K.ua = t, K.version = k && j, K.os = V || {
                                architecture: null,
                                family: null,
                                version: null,
                                toString: function() {
                                    return "null"
                                }
                            }, K.parse = e, K.toString = function() {
                                return this.description || ""
                            }, K.version && L.unshift(j), K.name && L.unshift(k), V && k && (V != String(V).split(" ")[0] || V != k.split(" ")[0] && !B) && L.push(B ? "(" + V + ")" : "on " + V), L.length && (K.description = L.join(" ")), K
                        }();
                        i.platform = b, void 0 === (r = function() {
                            return b
                        }.call(t, n, t, e)) || (e.exports = r)
                    }.call(this)
            },
            3434: function(e, t, n) {
                "use strict";
                var r = n(723);

                function o() {}
                var i = null,
                    a = {};

                function s(e) {
                    if ("object" != typeof this) throw new TypeError("Promises must be constructed via new");
                    if ("function" != typeof e) throw new TypeError("Promise constructor's argument is not a function");
                    this._U = 0, this._V = 0, this._W = null, this._X = null, e !== o && p(e, this)
                }

                function u(e, t) {
                    for (; 3 === e._V;) e = e._W;
                    if (s._Y && s._Y(e), 0 === e._V) return 0 === e._U ? (e._U = 1, void(e._X = t)) : 1 === e._U ? (e._U = 2, void(e._X = [e._X, t])) : void e._X.push(t);
                    ! function(e, t) {
                        r((function() {
                            var n = 1 === e._V ? t.onFulfilled : t.onRejected;
                            if (null !== n) {
                                var r = function(e, t) {
                                    try {
                                        return e(t)
                                    } catch (e) {
                                        return i = e, a
                                    }
                                }(n, e._W);
                                r === a ? l(t.promise, i) : c(t.promise, r)
                            } else 1 === e._V ? c(t.promise, e._W) : l(t.promise, e._W)
                        }))
                    }(e, t)
                }

                function c(e, t) {
                    if (t === e) return l(e, new TypeError("A promise cannot be resolved with itself."));
                    if (t && ("object" == typeof t || "function" == typeof t)) {
                        var n = function(e) {
                            try {
                                return e.then
                            } catch (e) {
                                return i = e, a
                            }
                        }(t);
                        if (n === a) return l(e, i);
                        if (n === e.then && t instanceof s) return e._V = 3, e._W = t, void d(e);
                        if ("function" == typeof n) return void p(n.bind(t), e)
                    }
                    e._V = 1, e._W = t, d(e)
                }

                function l(e, t) {
                    e._V = 2, e._W = t, s._Z && s._Z(e, t), d(e)
                }

                function d(e) {
                    if (1 === e._U && (u(e, e._X), e._X = null), 2 === e._U) {
                        for (var t = 0; t < e._X.length; t++) u(e, e._X[t]);
                        e._X = null
                    }
                }

                function f(e, t, n) {
                    this.onFulfilled = "function" == typeof e ? e : null, this.onRejected = "function" == typeof t ? t : null, this.promise = n
                }

                function p(e, t) {
                    var n = !1,
                        r = function(e, t, n) {
                            try {
                                e(t, n)
                            } catch (e) {
                                return i = e, a
                            }
                        }(e, (function(e) {
                            n || (n = !0, c(t, e))
                        }), (function(e) {
                            n || (n = !0, l(t, e))
                        }));
                    n || r !== a || (n = !0, l(t, i))
                }
                e.exports = s, s._Y = null, s._Z = null, s._0 = o, s.prototype.then = function(e, t) {
                    if (this.constructor !== s) return function(e, t, n) {
                        return new e.constructor((function(r, i) {
                            var a = new s(o);
                            a.then(r, i), u(e, new f(t, n, a))
                        }))
                    }(this, e, t);
                    var n = new s(o);
                    return u(this, new f(e, t, n)), n
                }
            },
            1803: function(e, t, n) {
                "use strict";
                var r = n(3434);
                e.exports = r;
                var o = l(!0),
                    i = l(!1),
                    a = l(null),
                    s = l(void 0),
                    u = l(0),
                    c = l("");

                function l(e) {
                    var t = new r(r._0);
                    return t._V = 1, t._W = e, t
                }
                r.resolve = function(e) {
                    if (e instanceof r) return e;
                    if (null === e) return a;
                    if (void 0 === e) return s;
                    if (!0 === e) return o;
                    if (!1 === e) return i;
                    if (0 === e) return u;
                    if ("" === e) return c;
                    if ("object" == typeof e || "function" == typeof e) try {
                        var t = e.then;
                        if ("function" == typeof t) return new r(t.bind(e))
                    } catch (e) {
                        return new r((function(t, n) {
                            n(e)
                        }))
                    }
                    return l(e)
                };
                var d = function(e) {
                    return "function" == typeof Array.from ? (d = Array.from, Array.from(e)) : (d = function(e) {
                        return Array.prototype.slice.call(e)
                    }, Array.prototype.slice.call(e))
                };
                r.all = function(e) {
                    var t = d(e);
                    return new r((function(e, n) {
                        if (0 === t.length) return e([]);
                        var o = t.length;

                        function i(a, s) {
                            if (s && ("object" == typeof s || "function" == typeof s)) {
                                if (s instanceof r && s.then === r.prototype.then) {
                                    for (; 3 === s._V;) s = s._W;
                                    return 1 === s._V ? i(a, s._W) : (2 === s._V && n(s._W), void s.then((function(e) {
                                        i(a, e)
                                    }), n))
                                }
                                var u = s.then;
                                if ("function" == typeof u) return void new r(u.bind(s)).then((function(e) {
                                    i(a, e)
                                }), n)
                            }
                            t[a] = s, 0 == --o && e(t)
                        }
                        for (var a = 0; a < t.length; a++) i(a, t[a])
                    }))
                }, r.reject = function(e) {
                    return new r((function(t, n) {
                        n(e)
                    }))
                }, r.race = function(e) {
                    return new r((function(t, n) {
                        d(e).forEach((function(e) {
                            r.resolve(e).then(t, n)
                        }))
                    }))
                }, r.prototype.catch = function(e) {
                    return this.then(null, e)
                }
            },
            8048: function() {
                "function" != typeof Promise.prototype.done && (Promise.prototype.done = function(e, t) {
                    var n = arguments.length ? this.then.apply(this, arguments) : this;
                    n.then(null, (function(e) {
                        setTimeout((function() {
                            throw e
                        }), 0)
                    }))
                })
            },
            4015: function(e, t, n) {
                n(9272);
                "undefined" == typeof Promise && (Promise = n(3434), n(1803)), n(8048)
            },
            3664: function(e, t, n) {
                var r;
                ! function(o) {
                    function i(e, t, n, r, o) {
                        this._listener = t, this._isOnce = n, this.context = r, this._signal = e, this._priority = o || 0
                    }

                    function a(e, t) {
                        if ("function" != typeof e) throw new Error("listener is a required param of {fn}() and should be a Function.".replace("{fn}", t))
                    }

                    function s() {
                        this._bindings = [], this._prevParams = null;
                        var e = this;
                        this.dispatch = function() {
                            s.prototype.dispatch.apply(e, arguments)
                        }
                    }
                    i.prototype = {
                        active: !0,
                        params: null,
                        execute: function(e) {
                            var t, n;
                            return this.active && this._listener && (n = this.params ? this.params.concat(e) : e, t = this._listener.apply(this.context, n), this._isOnce && this.detach()), t
                        },
                        detach: function() {
                            return this.isBound() ? this._signal.remove(this._listener, this.context) : null
                        },
                        isBound: function() {
                            return !!this._signal && !!this._listener
                        },
                        isOnce: function() {
                            return this._isOnce
                        },
                        getListener: function() {
                            return this._listener
                        },
                        getSignal: function() {
                            return this._signal
                        },
                        _destroy: function() {
                            delete this._signal, delete this._listener, delete this.context
                        },
                        toString: function() {
                            return "[SignalBinding isOnce:" + this._isOnce + ", isBound:" + this.isBound() + ", active:" + this.active + "]"
                        }
                    }, s.prototype = {
                        VERSION: "1.0.0",
                        memorize: !1,
                        _shouldPropagate: !0,
                        active: !0,
                        _registerListener: function(e, t, n, r) {
                            var o, a = this._indexOfListener(e, n);
                            if (-1 !== a) {
                                if ((o = this._bindings[a]).isOnce() !== t) throw new Error("You cannot add" + (t ? "" : "Once") + "() then add" + (t ? "Once" : "") + "() the same listener without removing the relationship first.")
                            } else o = new i(this, e, t, n, r), this._addBinding(o);
                            return this.memorize && this._prevParams && o.execute(this._prevParams), o
                        },
                        _addBinding: function(e) {
                            var t = this._bindings.length;
                            do {
                                --t
                            } while (this._bindings[t] && e._priority <= this._bindings[t]._priority);
                            this._bindings.splice(t + 1, 0, e)
                        },
                        _indexOfListener: function(e, t) {
                            for (var n, r = this._bindings.length; r--;)
                                if ((n = this._bindings[r])._listener === e && n.context === t) return r;
                            return -1
                        },
                        has: function(e, t) {
                            return -1 !== this._indexOfListener(e, t)
                        },
                        add: function(e, t, n) {
                            return a(e, "add"), this._registerListener(e, !1, t, n)
                        },
                        addOnce: function(e, t, n) {
                            return a(e, "addOnce"), this._registerListener(e, !0, t, n)
                        },
                        remove: function(e, t) {
                            a(e, "remove");
                            var n = this._indexOfListener(e, t);
                            return -1 !== n && (this._bindings[n]._destroy(), this._bindings.splice(n, 1)), e
                        },
                        removeAll: function() {
                            for (var e = this._bindings.length; e--;) this._bindings[e]._destroy();
                            this._bindings.length = 0
                        },
                        getNumListeners: function() {
                            return this._bindings.length
                        },
                        halt: function() {
                            this._shouldPropagate = !1
                        },
                        dispatch: function(e) {
                            if (this.active) {
                                var t, n = Array.prototype.slice.call(arguments),
                                    r = this._bindings.length;
                                if (this.memorize && (this._prevParams = n), r) {
                                    t = this._bindings.slice(), this._shouldPropagate = !0;
                                    do {
                                        r--
                                    } while (t[r] && this._shouldPropagate && !1 !== t[r].execute(n))
                                }
                            }
                        },
                        forget: function() {
                            this._prevParams = null
                        },
                        dispose: function() {
                            this.removeAll(), delete this._bindings, delete this._prevParams
                        },
                        toString: function() {
                            return "[Signal active:" + this.active + " numListeners:" + this.getNumListeners() + "]"
                        }
                    };
                    var u = s;
                    u.Signal = s, void 0 === (r = function() {
                        return u
                    }.call(t, n, t, e)) || (e.exports = r)
                }()
            },
            4751: function(e, t, n) {
                "use strict";
                n.r(t);
                var r = n(3379),
                    o = n.n(r),
                    i = n(7795),
                    a = n.n(i),
                    s = n(569),
                    u = n.n(s),
                    c = n(3565),
                    l = n.n(c),
                    d = n(9216),
                    f = n.n(d),
                    p = n(4589),
                    h = n.n(p),
                    m = n(6993),
                    _ = {};
                _.styleTagTransform = h(), _.setAttributes = l(), _.insert = u().bind(null, "head"), _.domAPI = a(), _.insertStyleElement = f();
                o()(m.Z, _);
                t.default = m.Z && m.Z.locals ? m.Z.locals : void 0
            },
            3379: function(e) {
                "use strict";
                var t = [];

                function n(e) {
                    for (var n = -1, r = 0; r < t.length; r++)
                        if (t[r].identifier === e) {
                            n = r;
                            break
                        } return n
                }

                function r(e, r) {
                    for (var i = {}, a = [], s = 0; s < e.length; s++) {
                        var u = e[s],
                            c = r.base ? u[0] + r.base : u[0],
                            l = i[c] || 0,
                            d = "".concat(c, " ").concat(l);
                        i[c] = l + 1;
                        var f = n(d),
                            p = {
                                css: u[1],
                                media: u[2],
                                sourceMap: u[3]
                            }; - 1 !== f ? (t[f].references++, t[f].updater(p)) : t.push({
                            identifier: d,
                            updater: o(p, r),
                            references: 1
                        }), a.push(d)
                    }
                    return a
                }

                function o(e, t) {
                    var n = t.domAPI(t);
                    return n.update(e),
                        function(t) {
                            if (t) {
                                if (t.css === e.css && t.media === e.media && t.sourceMap === e.sourceMap) return;
                                n.update(e = t)
                            } else n.remove()
                        }
                }
                e.exports = function(e, o) {
                    var i = r(e = e || [], o = o || {});
                    return function(e) {
                        e = e || [];
                        for (var a = 0; a < i.length; a++) {
                            var s = n(i[a]);
                            t[s].references--
                        }
                        for (var u = r(e, o), c = 0; c < i.length; c++) {
                            var l = n(i[c]);
                            0 === t[l].references && (t[l].updater(), t.splice(l, 1))
                        }
                        i = u
                    }
                }
            },
            569: function(e) {
                "use strict";
                var t = {};
                e.exports = function(e, n) {
                    var r = function(e) {
                        if (void 0 === t[e]) {
                            var n = document.querySelector(e);
                            if (window.HTMLIFrameElement && n instanceof window.HTMLIFrameElement) try {
                                n = n.contentDocument.head
                            } catch (e) {
                                n = null
                            }
                            t[e] = n
                        }
                        return t[e]
                    }(e);
                    if (!r) throw new Error("Couldn't find a style target. This probably means that the value for the 'insert' parameter is invalid.");
                    r.appendChild(n)
                }
            },
            9216: function(e) {
                "use strict";
                e.exports = function(e) {
                    var t = document.createElement("style");
                    return e.setAttributes(t, e.attributes), e.insert(t), t
                }
            },
            3565: function(e, t, n) {
                "use strict";
                e.exports = function(e) {
                    var t = n.nc;
                    t && e.setAttribute("nonce", t)
                }
            },
            7795: function(e) {
                "use strict";
                e.exports = function(e) {
                    var t = e.insertStyleElement(e);
                    return {
                        update: function(n) {
                            ! function(e, t, n) {
                                var r = n.css,
                                    o = n.media,
                                    i = n.sourceMap;
                                o ? e.setAttribute("media", o) : e.removeAttribute("media"), i && "undefined" != typeof btoa && (r += "\n/*# sourceMappingURL=data:application/json;base64,".concat(btoa(unescape(encodeURIComponent(JSON.stringify(i)))), " */")), t.styleTagTransform(r, e)
                            }(t, e, n)
                        },
                        remove: function() {
                            ! function(e) {
                                if (null === e.parentNode) return !1;
                                e.parentNode.removeChild(e)
                            }(t)
                        }
                    }
                }
            },
            4589: function(e) {
                "use strict";
                e.exports = function(e, t) {
                    if (t.styleSheet) t.styleSheet.cssText = e;
                    else {
                        for (; t.firstChild;) t.removeChild(t.firstChild);
                        t.appendChild(document.createTextNode(e))
                    }
                }
            },
            9417: function(e, t, n) {
                "use strict";
                var r, o = this && this.__extends || (r = function(e, t) {
                        return (r = Object.setPrototypeOf || {
                                __proto__: []
                            }
                            instanceof Array && function(e, t) {
                                e.__proto__ = t
                            } || function(e, t) {
                                for (var n in t) Object.prototype.hasOwnProperty.call(t, n) && (e[n] = t[n])
                            })(e, t)
                    }, function(e, t) {
                        if ("function" != typeof t && null !== t) throw new TypeError("Class extends value " + String(t) + " is not a constructor or null");

                        function n() {
                            this.constructor = e
                        }
                        r(e, t), e.prototype = null === t ? Object.create(t) : (n.prototype = t.prototype, new n)
                    }),
                    i = this && this.__createBinding || (Object.create ? function(e, t, n, r) {
                        void 0 === r && (r = n), Object.defineProperty(e, r, {
                            enumerable: !0,
                            get: function() {
                                return t[n]
                            }
                        })
                    } : function(e, t, n, r) {
                        void 0 === r && (r = n), e[r] = t[n]
                    }),
                    a = this && this.__setModuleDefault || (Object.create ? function(e, t) {
                        Object.defineProperty(e, "default", {
                            enumerable: !0,
                            value: t
                        })
                    } : function(e, t) {
                        e.default = t
                    }),
                    s = (this && this.__importStar, this && this.__importDefault || function(e) {
                        return e && e.__esModule ? e : {
                            default: e
                        }
                    });
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.WrapperContext = void 0, n(4751), n(2981);
                var u = n(6667),
                    c = n(6901),
                    l = n(7732),
                    d = n(4107),
                    f = n(1529),
                    p = n(64),
                    h = n(9945),
                    m = n(9979),
                    _ = s(n(3311)),
                    g = s(n(1609)),
                    v = s(n(4293)),
                    y = s(n(1463)),
                    E = s(n(9704)),
                    b = n(4076),
                    O = n(6789),
                    w = n(5425),
                    S = n(2010),
                    P = n(1361),
                    A = n(4624),
                    T = n(1653),
                    C = n(6415),
                    D = n(4330),
                    x = n(1052),
                    I = n(7097),
                    N = n(1556),
                    M = n(3790),
                    L = n(5040),
                    W = n(9852),
                    R = n(3362),
                    j = n(9812),
                    U = n(1197),
                    k = n(3113),
                    B = n(3967),
                    G = n(4746),
                    V = n(7696),
                    F = n(2304),
                    H = function(e) {
                        function t() {
                            return null !== e && e.apply(this, arguments) || this
                        }
                        return o(t, e), t.prototype.initialize = function(e) {
                            var t = this;
                            if (m.UrlUtil.checkUrlParamValue(B.EWrapperUrlParamName.DISABLE_WRAPPER_ERROR_HANDLER, !0) || this.setErrorHandlers(), v.default(e)) throw new Error("Overlay Wrapper : The widget data is undefined.");
                            this._data = e, 0 === e.operator.length && (this._data.operator = "0"), this.updateGameRevision(), this.createModel(), window.addEventListener(u.EEventName.LOAD, y.default((function() {
                                document.getElementById(t._model.htmlElementName).classList.add("wrapperContainer");
                                var e = document.getElementsByTagName("script");
                                t._model.wrapperPath = _.default(e, (function(e) {
                                    return -1 !== e.src.indexOf("js/isb_overlay_wrapper.js")
                                })).src.split("js/isb_overlay_wrapper.js")[0], p.HtmlUtil.addStyle("css/index.css", t._model.wrapperPath), document.head.setAttribute("isb_ow_include_html", "html/header_template.html"), document.getElementById("wrapper_container").setAttribute("isb_ow_include_html", "html/base_template.html"), p.HtmlUtil.includeHTML("isb_ow_include_html", (function() {
                                    t._model.onHtmlBuilt.dispatch()
                                }), t._model.wrapperPath)
                            }))), this._model.onHtmlBuilt.addOnce((function() {
                                t._dummyHtmlBuilt = !0, t._model.init(), t.createView(), t.createController(), t.registerCommands(), t.registerEventListeners(), t._model.getWrapperConfig()
                            }))
                        }, t.prototype.drawWidget = function(e) {
                            for (var t = this, n = [], r = 1; r < arguments.length; r++) n[r - 1] = arguments[r];
                            var o = function() {
                                t._model.currentWidgetID = e, t._model.widgetList[e] = n, t._controller.executeCommand(U.EWrapperCommandName.DRAW_WIDGET), t._model.isCurrentWidgetDraggable() && t.makeWidgetDraggable()
                            };
                            this._dummyHtmlBuilt ? o() : this._model.onHtmlBuilt.add((function() {
                                o()
                            }))
                        }, t.prototype.setDefaultPositionForDraggableWidget = function(e, t) {
                            var n = this._model.getCurrentDraggableWidgetModel();
                            v.default(n) || (n.defaultPositionData = {
                                position: e,
                                positionOffset: t
                            }, this._controller.executeCommand(U.EWrapperCommandName.CHECK_DRAGGABLE_POSITION))
                        }, t.prototype.showWidget = function(e) {
                            this._model.widgetToShow = e, this._controller.executeCommand(U.EWrapperCommandName.SHOW_WIDGET)
                        }, t.prototype.hideWidget = function(e) {
                            this._model.widgetToHide = e, this._controller.executeCommand(U.EWrapperCommandName.HIDE_WIDGET)
                        }, t.prototype.onWidgetAssetsLoaded = function() {}, t.prototype.getModel = function() {
                            return this._model
                        }, t.prototype.getView = function() {
                            return this._view
                        }, t.prototype.getController = function() {
                            return this._controller
                        }, t.prototype.getViews = function() {
                            return []
                        }, t.prototype.addUrlParamsToGameLink = function() {
                            this._controller.executeCommand(U.EWrapperCommandName.ADD_URL_PARAMS_TO_GAME_LINK)
                        }, t.prototype.loadFonts = function() {
                            this._controller.executeCommand(U.EWrapperCommandName.LOAD_FONTS)
                        }, t.prototype.createView = function() {
                            this._view = new F.WrapperView(this._model.width, this._model.height, document.getElementById(this._model.htmlWidgetContainerName), document.getElementById(this._model.htmlElementName))
                        }, t.prototype.createModel = function() {
                            this._model = new V.WrapperModel
                        }, t.prototype.createController = function() {
                            this._controller = new j.WrapperController(this)
                        }, t.prototype.registerCommands = function() {
                            var e;
                            this.registerCoreCommands(((e = {})[d.ECommandName.PRINT_VERSION] = R.WrapperVersionPrintCommand, e[d.ECommandName.APP_RESIZE] = N.WrapperResizeCommand, e)), this._controller.registerCommand(U.EWrapperCommandName.GET_WIDGETS_DATA, T.WrapperGetWidgetsDataCommand), this._controller.registerCommand(U.EWrapperCommandName.GET_GAMES_EVENT_DATA, A.WrapperGetGamesEventDataCommand), this._controller.registerCommand(U.EWrapperCommandName.DRAW_WIDGET, P.WrapperDrawWidgetCommand), this._controller.registerCommand(U.EWrapperCommandName.INIT_WIDGETS, D.WrapperInitWidgetsCommand), this._controller.registerCommand(U.EWrapperCommandName.ADD_URL_PARAMS_TO_GAME_LINK, w.WrapperAddUrlParamsToGameLinkCommand), this._controller.registerCommand(U.EWrapperCommandName.SET_GAME_SRC, L.WrapperSetGameSrcCommand), this._controller.registerCommand(U.EWrapperCommandName.SET_GAME_SKIN_SRC, M.WrapperSetGameSkinSrcCommand), this._controller.registerCommand(U.EWrapperCommandName.HIDE_WIDGET, C.WrapperHideWidgetCommand), this._controller.registerCommand(U.EWrapperCommandName.SHOW_WIDGET, W.WrapperShowWidgetCommand), this._controller.registerCommand(U.EWrapperCommandName.LOAD_FONTS, x.WrapperLoadFontsCommand), this._controller.registerCommand(U.EWrapperCommandName.REGISTER_DISABLE_PINCH_TO_ZOOM, I.WrapperRegisterDisablePinchToZoomCommand), this._controller.registerCommand(U.EWrapperCommandName.ADD_DRAGGABLE_EVENT_LISTENER, O.WrapperAddDraggableEventListenerCommand), this._controller.registerCommand(U.EWrapperCommandName.CHECK_DRAGGABLE_POSITION, S.WrapperCheckDraggablePositionCommand)
                        }, t.prototype.registerEventListeners = function() {
                            var t, n = this;
                            if (this._model.onSetGameSrc.add((function() {
                                    n._controller.executeCommand(U.EWrapperCommandName.SET_GAME_SRC)
                                })), this._model.onOMDown.add((function() {
                                    n._model.onSetGameSrc.dispatch()
                                })), this._model.onWidgetsDataReceived.add((function() {
                                    if (n._controller.executeCommand(U.EWrapperCommandName.INIT_WIDGETS), n._model.isWidgetExist(G.EWrapperWidgetID.UNIVERSAL_WIDGET) && n._controller.executeCommand(U.EWrapperCommandName.GET_GAMES_EVENT_DATA), n._model.isWidgetExist(G.EWrapperWidgetID.UNIVERSAL_COMPLIANCE_FULLSCREEN))
                                        if (g.default(n._data.rulesURL)) {
                                            var e = m.UrlUtil.urlDecodeParams(n._data.gameLink, k.EWrapperGameLinkUrlParamName.RULES_URL);
                                            o = m.UrlUtil.urlDecodeParams(e, k.EWrapperGameLinkUrlParamName.LID);
                                            g.default(o) || (n._data.gameLink += "&" + k.EWrapperGameLinkUrlParamName.AUTO_UPDATE_RTP_SKIN_ID + "=" + n._data.skinId)
                                        } else {
                                            var r = decodeURIComponent(n._data.rulesURL),
                                                o = m.UrlUtil.urlDecodeParams(r, k.EWrapperGameLinkUrlParamName.LID);
                                            g.default(o) || (n._data.rulesURL += "&" + k.EWrapperGameLinkUrlParamName.AUTO_UPDATE_RTP_SKIN_ID + "=" + n._data.skinId)
                                        } var i = n._model.getWidgetData(G.EWrapperWidgetID.GAMES_SKINS_WIDGET);
                                    v.default(i) || n.setGameSkinSrc(), t = n._model.getWidgetData(G.EWrapperWidgetID.REQUEST_FULLSCREEN), v.default(t) && n._model.onSetGameSrc.dispatch()
                                })), this._model.onWidgetsInited.add((function() {
                                    var e = document.getElementById("game");
                                    v.default(t) || v.default(e) || !g.default(e.src) || n._model.onSetGameSrc.dispatch();
                                    var r = n._model.getWidgetData(G.EWrapperWidgetID.OPT_IN_WIDGET);
                                    (v.default(r) || !E.default(r.groupsData, (function(e) {
                                        return e.status === l.EGroupStatus.PENDING
                                    }))) && window.ISB_OW && window.ISB_OW.PublicApi && window.ISB_OW.PublicApi.getSignal(c.EGenericSignalNames.OPT_IN_COMMUNICATION_EVENT).dispatch()
                                })), window.ISB_OW && window.ISB_OW.PublicApi) {
                                window.ISB_OW.PublicApi.addSignal(c.EGenericSignalNames.UNIVERSAL_COMMUNICATION_EVENT), window.ISB_OW.PublicApi.getSignal(c.EGenericSignalNames.UNIVERSAL_COMMUNICATION_EVENT).add((function(e) {
                                    n._model.setLatestBroadcastEvents(e)
                                })), window.ISB_OW.PublicApi.addSignal(c.EGenericSignalNames.UNIVERSAL_COMMUNICATION_EVENT_BACKWARD), window.ISB_OW.PublicApi.addSignal(c.EGenericSignalNames.OPT_IN_COMMUNICATION_EVENT), window.ISB_OW.PublicApi.addSignal(c.EGenericSignalNames.GAMES_EVENT_NAMES);
                                var r = window.ISB_OW.PublicApi.getSignal(c.EGenericSignalNames.GAMES_EVENT_NAMES);
                                r.memorize = !0, this._model.onGamesEventDataReceived.add((function() {
                                    r.dispatch(n._model.gamesEventData)
                                }))
                            }
                            this._model.onConfigLoaded.addOnce((function() {
                                n.loadFonts()
                            })), this._model.onFontsLoaded.addOnce((function() {
                                n.getWidgetsData(n._data), n._controller.executeCommand(U.EWrapperCommandName.REGISTER_DISABLE_PINCH_TO_ZOOM), n._controller.executeCommand(d.ECommandName.APP_RESIZE), e.prototype.initialize.call(n), n._model.shouldIframeScrollingBeEnabled && n._view.enableGameIframeScrolling()
                            })), window.addEventListener(u.EEventName.RESIZE, (function() {
                                n._controller.executeCommand(d.ECommandName.APP_RESIZE)
                            }))
                        }, t.prototype.getWidgetsData = function(e) {
                            if (v.default(e)) throw new Error("Overlay Wrapper : The widget data is undefined.");
                            this._model.wrapperRequestData = e, this.addUrlParamsToGameLink(), this._controller.executeCommand(U.EWrapperCommandName.GET_WIDGETS_DATA)
                        }, t.prototype.setGameSkinSrc = function() {
                            this._controller.executeCommand(U.EWrapperCommandName.SET_GAME_SKIN_SRC)
                        }, t.prototype.updateGameRevision = function() {
                            var e = window.location.search.split("&").filter((function(e) {
                                return -1 !== e.indexOf("revisionGame")
                            }))[0];
                            if (e) {
                                var t = e.match(/(\d+)/)[0];
                                this._data.gameLink = this._data.gameLink.replace(this._data.identifier, this._data.identifier + "/" + this._data.identifier + "_r" + t)
                            }
                        }, t.prototype.makeWidgetDraggable = function() {
                            this._model.createDraggableWidgetModel(), this._controller.executeCommand(U.EWrapperCommandName.ADD_DRAGGABLE_EVENT_LISTENER), this._controller.executeCommand(U.EWrapperCommandName.CHECK_DRAGGABLE_POSITION)
                        }, t.prototype.setErrorHandlers = function() {
                            window.onerror = function(e, t, n, r, o) {
                                return v.default(o) ? (h.Log.errorch(b.WRAPPER_NAME, "Error occurs. Check error message below:"), h.Log.error(e)) : (h.Log.errorch(b.WRAPPER_NAME, "Error occurs. Check error stack below:"), h.Log.error(o.stack)), !0
                            }, window.onunhandledrejection = function(e) {
                                return h.Log.errorch(b.WRAPPER_NAME, "Unhandled rejection"), h.Log.error(e.reason.stack), !0
                            }
                        }, t
                    }(f.AbstractContext);
                t.WrapperContext = H
            },
            4076: function(e, t) {
                "use strict";
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.WRAPPER_NAME = void 0, t.WRAPPER_NAME = "Overlay Wrapper"
            },
            9812: function(e, t, n) {
                "use strict";
                var r, o = this && this.__extends || (r = function(e, t) {
                    return (r = Object.setPrototypeOf || {
                            __proto__: []
                        }
                        instanceof Array && function(e, t) {
                            e.__proto__ = t
                        } || function(e, t) {
                            for (var n in t) Object.prototype.hasOwnProperty.call(t, n) && (e[n] = t[n])
                        })(e, t)
                }, function(e, t) {
                    if ("function" != typeof t && null !== t) throw new TypeError("Class extends value " + String(t) + " is not a constructor or null");

                    function n() {
                        this.constructor = e
                    }
                    r(e, t), e.prototype = null === t ? Object.create(t) : (n.prototype = t.prototype, new n)
                });
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.WrapperController = void 0;
                var i = function(e) {
                    function t(t) {
                        var n = e.call(this) || this;
                        return n._context = t, n
                    }
                    return o(t, e), Object.defineProperty(t.prototype, "context", {
                        get: function() {
                            return this._context
                        },
                        set: function(e) {
                            this._context = e
                        },
                        enumerable: !1,
                        configurable: !0
                    }), t
                }(n(8827).AbstractController);
                t.WrapperController = i
            },
            6789: function(e, t, n) {
                "use strict";
                var r, o = this && this.__extends || (r = function(e, t) {
                    return (r = Object.setPrototypeOf || {
                            __proto__: []
                        }
                        instanceof Array && function(e, t) {
                            e.__proto__ = t
                        } || function(e, t) {
                            for (var n in t) Object.prototype.hasOwnProperty.call(t, n) && (e[n] = t[n])
                        })(e, t)
                }, function(e, t) {
                    if ("function" != typeof t && null !== t) throw new TypeError("Class extends value " + String(t) + " is not a constructor or null");

                    function n() {
                        this.constructor = e
                    }
                    r(e, t), e.prototype = null === t ? Object.create(t) : (n.prototype = t.prototype, new n)
                });
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.WrapperAddDraggableEventListenerCommand = void 0;
                var i = function(e) {
                    function t() {
                        return null !== e && e.apply(this, arguments) || this
                    }
                    return o(t, e), t.prototype.execute = function() {
                        e.prototype.execute.call(this)
                    }, Object.defineProperty(t.prototype, "draggableModel", {
                        get: function() {
                            return this.model.getCurrentDraggableWidgetModel()
                        },
                        enumerable: !1,
                        configurable: !0
                    }), t
                }(n(6567).AbstractWidgetDraggableEventListenerCommand);
                t.WrapperAddDraggableEventListenerCommand = i
            },
            5425: function(e, t, n) {
                "use strict";
                var r, o = this && this.__extends || (r = function(e, t) {
                        return (r = Object.setPrototypeOf || {
                                __proto__: []
                            }
                            instanceof Array && function(e, t) {
                                e.__proto__ = t
                            } || function(e, t) {
                                for (var n in t) Object.prototype.hasOwnProperty.call(t, n) && (e[n] = t[n])
                            })(e, t)
                    }, function(e, t) {
                        if ("function" != typeof t && null !== t) throw new TypeError("Class extends value " + String(t) + " is not a constructor or null");

                        function n() {
                            this.constructor = e
                        }
                        r(e, t), e.prototype = null === t ? Object.create(t) : (n.prototype = t.prototype, new n)
                    }),
                    i = this && this.__importDefault || function(e) {
                        return e && e.__esModule ? e : {
                            default: e
                        }
                    };
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.WrapperAddUrlParamsToGameLinkCommand = void 0;
                var a = n(9979),
                    s = i(n(4293)),
                    u = n(1669),
                    c = function(e) {
                        function t() {
                            return null !== e && e.apply(this, arguments) || this
                        }
                        return o(t, e), t.prototype.execute = function() {
                            for (var t in e.prototype.execute.call(this), this._paramsLocationHref = a.UrlUtil.getUrlParamsFromLocationHref(window.location.href), this.model.wrapperRequestData) t === u.EWrapperWidgetRequestData.GAME_LINK || a.UrlUtil.checkUrlParamValue(t.toLowerCase(), void 0, this.model.wrapperRequestData.gameLink.toLowerCase()) || (-1 === this.model.wrapperRequestData.gameLink.indexOf("?") ? this.model.wrapperRequestData.gameLink += "?" + t + "=" + this.model.wrapperRequestData[t] : this.model.wrapperRequestData.gameLink += "&" + t + "=" + this.model.wrapperRequestData[t]);
                            if (!s.default(this._paramsLocationHref))
                                for (var n in this._paramsLocationHref) a.UrlUtil.checkUrlParamValue(n.toLowerCase(), void 0, this.model.wrapperRequestData.gameLink.toLowerCase()) || (-1 === this.model.wrapperRequestData.gameLink.indexOf("?") ? this.model.wrapperRequestData.gameLink += "?" + n + "=" + this._paramsLocationHref[n] : this.model.wrapperRequestData.gameLink += "&" + n + "=" + this._paramsLocationHref[n])
                        }, t
                    }(n(7854).WrapperBaseCommand);
                t.WrapperAddUrlParamsToGameLinkCommand = c
            },
            7854: function(e, t, n) {
                "use strict";
                var r, o = this && this.__extends || (r = function(e, t) {
                    return (r = Object.setPrototypeOf || {
                            __proto__: []
                        }
                        instanceof Array && function(e, t) {
                            e.__proto__ = t
                        } || function(e, t) {
                            for (var n in t) Object.prototype.hasOwnProperty.call(t, n) && (e[n] = t[n])
                        })(e, t)
                }, function(e, t) {
                    if ("function" != typeof t && null !== t) throw new TypeError("Class extends value " + String(t) + " is not a constructor or null");

                    function n() {
                        this.constructor = e
                    }
                    r(e, t), e.prototype = null === t ? Object.create(t) : (n.prototype = t.prototype, new n)
                });
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.WrapperBaseCommand = void 0;
                var i = function(e) {
                    function t() {
                        return null !== e && e.apply(this, arguments) || this
                    }
                    return o(t, e), t
                }(n(2485).BaseCommand);
                t.WrapperBaseCommand = i
            },
            2010: function(e, t, n) {
                "use strict";
                var r, o = this && this.__extends || (r = function(e, t) {
                        return (r = Object.setPrototypeOf || {
                                __proto__: []
                            }
                            instanceof Array && function(e, t) {
                                e.__proto__ = t
                            } || function(e, t) {
                                for (var n in t) Object.prototype.hasOwnProperty.call(t, n) && (e[n] = t[n])
                            })(e, t)
                    }, function(e, t) {
                        if ("function" != typeof t && null !== t) throw new TypeError("Class extends value " + String(t) + " is not a constructor or null");

                        function n() {
                            this.constructor = e
                        }
                        r(e, t), e.prototype = null === t ? Object.create(t) : (n.prototype = t.prototype, new n)
                    }),
                    i = this && this.__importDefault || function(e) {
                        return e && e.__esModule ? e : {
                            default: e
                        }
                    };
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.WrapperCheckDraggablePositionCommand = void 0;
                var a = n(8305),
                    s = i(n(4293)),
                    u = function(e) {
                        function t() {
                            return null !== e && e.apply(this, arguments) || this
                        }
                        return o(t, e), t.prototype.execute = function() {
                            s.default(this.model.widgetList[this.draggableModel.widgetUniqueId]) || e.prototype.execute.call(this)
                        }, Object.defineProperty(t.prototype, "draggableModel", {
                            get: function() {
                                return this.model.getCurrentDraggableWidgetModel()
                            },
                            enumerable: !1,
                            configurable: !0
                        }), t
                    }(a.AbstractWidgetDraggableSetDefaultPositionCommand);
                t.WrapperCheckDraggablePositionCommand = u
            },
            1361: function(e, t, n) {
                "use strict";
                var r, o = this && this.__extends || (r = function(e, t) {
                        return (r = Object.setPrototypeOf || {
                                __proto__: []
                            }
                            instanceof Array && function(e, t) {
                                e.__proto__ = t
                            } || function(e, t) {
                                for (var n in t) Object.prototype.hasOwnProperty.call(t, n) && (e[n] = t[n])
                            })(e, t)
                    }, function(e, t) {
                        if ("function" != typeof t && null !== t) throw new TypeError("Class extends value " + String(t) + " is not a constructor or null");

                        function n() {
                            this.constructor = e
                        }
                        r(e, t), e.prototype = null === t ? Object.create(t) : (n.prototype = t.prototype, new n)
                    }),
                    i = this && this.__importDefault || function(e) {
                        return e && e.__esModule ? e : {
                            default: e
                        }
                    };
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.WrapperDrawWidgetCommand = void 0;
                var a = i(n(4293)),
                    s = function(e) {
                        function t() {
                            return null !== e && e.apply(this, arguments) || this
                        }
                        return o(t, e), t.prototype.execute = function() {
                            this._widgetData = this.model.getCurrentWidgetData(), this._layoutPosition = this.model.getLayoutPosition(this._widgetData), a.default(this.model.widgetList[this._widgetData.widgetUniqueId]) || this.view.drawWidget(this._layoutPosition, this._widgetData.widgetUniqueId, this.model.widgetList[this._widgetData.widgetUniqueId], this._widgetData.layout.depth)
                        }, t
                    }(n(7854).WrapperBaseCommand);
                t.WrapperDrawWidgetCommand = s
            },
            4624: function(e, t, n) {
                "use strict";
                var r, o = this && this.__extends || (r = function(e, t) {
                        return (r = Object.setPrototypeOf || {
                                __proto__: []
                            }
                            instanceof Array && function(e, t) {
                                e.__proto__ = t
                            } || function(e, t) {
                                for (var n in t) Object.prototype.hasOwnProperty.call(t, n) && (e[n] = t[n])
                            })(e, t)
                    }, function(e, t) {
                        if ("function" != typeof t && null !== t) throw new TypeError("Class extends value " + String(t) + " is not a constructor or null");

                        function n() {
                            this.constructor = e
                        }
                        r(e, t), e.prototype = null === t ? Object.create(t) : (n.prototype = t.prototype, new n)
                    }),
                    i = this && this.__assign || function() {
                        return (i = Object.assign || function(e) {
                            for (var t, n = 1, r = arguments.length; n < r; n++)
                                for (var o in t = arguments[n]) Object.prototype.hasOwnProperty.call(t, o) && (e[o] = t[o]);
                            return e
                        }).apply(this, arguments)
                    },
                    a = this && this.__importDefault || function(e) {
                        return e && e.__esModule ? e : {
                            default: e
                        }
                    };
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.WrapperGetGamesEventDataCommand = void 0;
                var s = n(9945),
                    u = n(6645),
                    c = n(4019),
                    l = a(n(361)),
                    d = a(n(711)),
                    f = a(n(1584)),
                    p = a(n(4293)),
                    h = a(n(7037)),
                    m = n(5757),
                    _ = function(e) {
                        function t() {
                            return null !== e && e.apply(this, arguments) || this
                        }
                        return o(t, e), t.prototype.execute = function() {
                            this.getGamesEventData()
                        }, t.prototype.getMockData = function() {
                            var e = l.default(m.MOCK_GAME_EVENTS_DATA);
                            this.setGamesEventData(e)
                        }, t.prototype.getGamesEventData = function() {
                            var e = this,
                                t = new XMLHttpRequest;
                            t.open("POST", this.model.serverEventsLink, !0), t.setRequestHeader("Content-Type", "application/json"), t.onreadystatechange = function() {
                                if (4 === t.readyState)
                                    if (200 === t.status) {
                                        if (!c.StringUtil.isValidJSONString(t.responseText)) return void s.Log.warning("Wrong events data received from Overlay manager!");
                                        e.isCorrectGamesEventData(JSON.parse(t.responseText)) ? e.setGamesEventData(JSON.parse(t.responseText)) : s.Log.warning("Wrong events data received from Overlay manager!")
                                    } else s.Log.warning("Unable to connect the overlay manager. You must check the link to the server!\n\t\t\t\t\tThis link: " + e.model.serverEventsLink)
                            }, t.send(JSON.stringify(i(i({}, this.model.wrapperRequestData), {
                                wrapperVersion: this.model.version,
                                libVersion: this.model.versionLib,
                                device: u.PlatformUtil.getDeviceType()
                            })))
                        }, t.prototype.setGamesEventData = function(e) {
                            s.Log.log("server games events data:"), s.Log.log(e), this.model.gamesEventData = e, this.model.onGamesEventDataReceived.dispatch()
                        }, t.prototype.isCorrectGamesEventData = function(e) {
                            var t = !1;
                            return p.default(e.data) || (t = d.default(e.data, (function(e) {
                                return h.default(e.standardEvent) && f.default(e.isMandatory) && h.default(e.eventValue)
                            }))), t
                        }, t
                    }(n(7854).WrapperBaseCommand);
                t.WrapperGetGamesEventDataCommand = _
            },
            1653: function(e, t, n) {
                "use strict";
                var r, o = this && this.__extends || (r = function(e, t) {
                        return (r = Object.setPrototypeOf || {
                                __proto__: []
                            }
                            instanceof Array && function(e, t) {
                                e.__proto__ = t
                            } || function(e, t) {
                                for (var n in t) Object.prototype.hasOwnProperty.call(t, n) && (e[n] = t[n])
                            })(e, t)
                    }, function(e, t) {
                        if ("function" != typeof t && null !== t) throw new TypeError("Class extends value " + String(t) + " is not a constructor or null");

                        function n() {
                            this.constructor = e
                        }
                        r(e, t), e.prototype = null === t ? Object.create(t) : (n.prototype = t.prototype, new n)
                    }),
                    i = this && this.__assign || function() {
                        return (i = Object.assign || function(e) {
                            for (var t, n = 1, r = arguments.length; n < r; n++)
                                for (var o in t = arguments[n]) Object.prototype.hasOwnProperty.call(t, o) && (e[o] = t[o]);
                            return e
                        }).apply(this, arguments)
                    },
                    a = this && this.__importDefault || function(e) {
                        return e && e.__esModule ? e : {
                            default: e
                        }
                    };
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.WrapperGetWidgetsDataCommand = void 0;
                var s = n(9945),
                    u = n(6645),
                    c = n(4019),
                    l = a(n(361)),
                    d = a(n(4486)),
                    f = a(n(4293)),
                    p = a(n(5220)),
                    h = a(n(1763)),
                    m = n(2418),
                    _ = function(e) {
                        function t() {
                            return null !== e && e.apply(this, arguments) || this
                        }
                        return o(t, e), t.prototype.execute = function() {
                            this.getWidgetData()
                        }, t.prototype.getWidgetData = function() {
                            var e = this,
                                t = new XMLHttpRequest;
                            t.open("POST", this.model.serverLink, !0), t.setRequestHeader("Content-Type", "application/json"), t.onreadystatechange = function() {
                                if (4 === t.readyState)
                                    if (200 === t.status) {
                                        if (!c.StringUtil.isValidJSONString(t.responseText) && h.default(JSON.parse(t.responseText)) && p.default(JSON.parse(t.responseText))) return s.Log.warning("Wrong data received for the widget!"), void e.model.onOMDown.dispatch();
                                        var n = JSON.parse(t.responseText),
                                            r = n.hasOwnProperty("data") ? n.data : n;
                                        e.isArrayWidgetsData(r) ? (s.Log.log("server data:"), s.Log.log(r), e.model.widgetsData = r, e.model.onWidgetsDataReceived.dispatch()) : (s.Log.warning("Wrong data received for the widget!"), e.model.onOMDown.dispatch())
                                    } else s.Log.warning("Unable to connect the overlay manager. You must check the link to the server!\n\t\t\t\t\tThis link: " + e.model.serverLink), e.model.onOMDown.dispatch()
                            }, t.send(JSON.stringify(i(i({}, this.model.wrapperRequestData), {
                                wrapperVersion: this.model.version,
                                libVersion: this.model.versionLib,
                                device: u.PlatformUtil.getDeviceType()
                            })))
                        }, t.prototype.isArrayWidgetsData = function(e) {
                            var t = !1;
                            return d.default(e, (function(e) {
                                return f.default(e) || f.default(e.actions) || "number" != typeof e.displaySettings || f.default(e.layout) || "string" != typeof e.widgetBourl || "string" != typeof e.widgetTitle || "number" != typeof e.widgetUniqueId || "string" != typeof e.widgetUrl ? t = !1 : void(t = !0)
                            })), t
                        }, t.prototype.getMockData = function() {
                            var e = l.default(m.MOCK_WIDGET_PUBLIC_DATA);
                            s.Log.log("server mock data:"), s.Log.log(e), this.model.widgetsData = e, this.model.onWidgetsDataReceived.dispatch()
                        }, t
                    }(n(7854).WrapperBaseCommand);
                t.WrapperGetWidgetsDataCommand = _
            },
            6415: function(e, t, n) {
                "use strict";
                var r, o = this && this.__extends || (r = function(e, t) {
                        return (r = Object.setPrototypeOf || {
                                __proto__: []
                            }
                            instanceof Array && function(e, t) {
                                e.__proto__ = t
                            } || function(e, t) {
                                for (var n in t) Object.prototype.hasOwnProperty.call(t, n) && (e[n] = t[n])
                            })(e, t)
                    }, function(e, t) {
                        if ("function" != typeof t && null !== t) throw new TypeError("Class extends value " + String(t) + " is not a constructor or null");

                        function n() {
                            this.constructor = e
                        }
                        r(e, t), e.prototype = null === t ? Object.create(t) : (n.prototype = t.prototype, new n)
                    }),
                    i = this && this.__importDefault || function(e) {
                        return e && e.__esModule ? e : {
                            default: e
                        }
                    };
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.WrapperHideWidgetCommand = void 0;
                var a = i(n(4293)),
                    s = function(e) {
                        function t() {
                            return null !== e && e.apply(this, arguments) || this
                        }
                        return o(t, e), t.prototype.execute = function() {
                            this._widgetData = this.model.getWidgetData(this.model.widgetToHide), this._layoutPosition = this.model.getLayoutPosition(this._widgetData), a.default(this.model.widgetList[this._widgetData.widgetUniqueId]) || this.view.hideWidget(this._layoutPosition, this._widgetData.widgetUniqueId)
                        }, t
                    }(n(7854).WrapperBaseCommand);
                t.WrapperHideWidgetCommand = s
            },
            4330: function(e, t, n) {
                "use strict";
                var r, o = this && this.__extends || (r = function(e, t) {
                        return (r = Object.setPrototypeOf || {
                                __proto__: []
                            }
                            instanceof Array && function(e, t) {
                                e.__proto__ = t
                            } || function(e, t) {
                                for (var n in t) Object.prototype.hasOwnProperty.call(t, n) && (e[n] = t[n])
                            })(e, t)
                    }, function(e, t) {
                        if ("function" != typeof t && null !== t) throw new TypeError("Class extends value " + String(t) + " is not a constructor or null");

                        function n() {
                            this.constructor = e
                        }
                        r(e, t), e.prototype = null === t ? Object.create(t) : (n.prototype = t.prototype, new n)
                    }),
                    i = this && this.__importDefault || function(e) {
                        return e && e.__esModule ? e : {
                            default: e
                        }
                    };
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.WrapperInitWidgetsCommand = void 0;
                var a = n(5402),
                    s = n(64),
                    u = n(9945),
                    c = n(9979),
                    l = i(n(6073)),
                    d = i(n(3105)),
                    f = i(n(3311)),
                    p = i(n(1609)),
                    h = i(n(4293)),
                    m = n(9610),
                    _ = n(3551),
                    g = n(4746),
                    v = n(7854),
                    y = c.UrlUtil.convertUrlToDomainBaseUrl,
                    E = function(e) {
                        function t() {
                            var t = null !== e && e.apply(this, arguments) || this;
                            return t._scriptCounter = 0, t
                        }
                        return o(t, e), t.prototype.execute = function() {
                            var e = this;
                            this.model.checkTestParameters();
                            var t = [];
                            l.default(this.model.widgetsData, (function(n) {
                                var r, o, i, a, s, c = d.default(n.actions, (function(e) {
                                        return e.actionName === m.EWrapperActionName.PRELOADER
                                    })),
                                    v = !1,
                                    y = !1,
                                    E = !1,
                                    b = !1,
                                    O = !1;
                                if (l.default(c, (function(t) {
                                        if (e.model.wrapperRequestData.funMode) {
                                            if (o = f.default(t.parameters, (function(t) {
                                                    return "just_for_real" === t.name && e.checkValues(t.values, !0)
                                                })), !h.default(o)) return y = !0, u.Log.warning(_.EWrapperMessageLog.LOG_WIDGET_AVAILABLE_ONLY_ON_REAL_MODE), void u.Log.warning('Widget available only on real mode! -> name:\n\t\t\t\t\t\t\t"' + n.widgetTitle + '", unique id: ' + n.widgetUniqueId)
                                        } else if (r = f.default(t.parameters, (function(t) {
                                                return "just_for_fun" === t.name && e.checkValues(t.values, !0)
                                            })), !h.default(r)) return v = !0, u.Log.warning(_.EWrapperMessageLog.LOG_WIDGET_AVAILABLE_ONLY_ON_FUN_MODE), void u.Log.warning('Widget available only on fun mode! -> name:\n\t\t\t\t\t\t\t"' + n.widgetTitle + '", unique id: ' + n.widgetUniqueId);
                                        return i = f.default(t.parameters, (function(e) {
                                            return "just_for_lid" === e.name
                                        })), h.default(i) || e.isCurrentLidExist(i) ? (a = f.default(t.parameters, (function(e) {
                                            return "not_for_lid" === e.name
                                        })), !h.default(a) && e.isCurrentLidExist(a) ? (b = !0, u.Log.warning(_.EWrapperMessageLog.LOG_WIDGET_NOT_AVAILABLE_FOR_THIS_LID), void u.Log.warning("Widget not available for this LID's \"" + a.values[0].value + '" :\n\t\t\t\t\t\t\tname = "' + n.widgetTitle + '", unique id: ' + n.widgetUniqueId)) : (s = f.default(t.parameters, (function(e) {
                                            return "disableGAT" === e.name
                                        })), !h.default(s) && e.checkValues(s.values, "true") && (!h.default(e.model.wrapperRequestData.testApiEndpoint) && e.model.wrapperRequestData.testApiEndpoint.length > 0 || !h.default(e.model.wrapperRequestData.testModuleUrl) && e.model.wrapperRequestData.testModuleUrl.length > 0) ? (O = !0, u.Log.warning(_.EWrapperMessageLog.LOG_IN_THIS_CASE_WIDGET_NOT_AVAILABLE_WITH_PARAM_DISABLEGAT), void u.Log.warning('Widget is not available when "disableGAT" parameter is (equal "true") and GAP Launcher link have\n\t\t\t\t\t\t \t"test_api_endpoint" or "test_module_url" parameters:\n\t\t\t\t\t\t\tname = "' + n.widgetTitle + '", unique id: ' + n.widgetUniqueId)) : void l.default(t.parameters, (function(t) {
                                            "js" === t.name ? l.default(t.values, (function(t) {
                                                var n = t.value;
                                                "string" == typeof n ? e.addScript(n) : u.Log.warning("script url must be string type")
                                            })) : "css" === t.name && l.default(t.values, (function(t) {
                                                var n = t.value;
                                                "string" == typeof n ? e.addStyle(n) : u.Log.warning("style url must be string type")
                                            }))
                                        })))) : (E = !0, u.Log.warning(_.EWrapperMessageLog.LOG_WIDGET_AVAILABLE_ONLY_ON_SPECIFIED_LID), void u.Log.warning("Widget available only on specified LID's \"" + i.values[0].value + '" :\n\t\t\t\t\t\t\tname = "' + n.widgetTitle + '", unique id: ' + n.widgetUniqueId))
                                    })), "" === n.widgetUrl || v || E || b || O || y) t.push(n);
                                else {
                                    var w = function() {
                                        e._scriptCounter += 1, e._scriptCounter === e.model.widgetsData.length && (u.Log.log("WrapperInitWidgetsCommand :: all widgets is fully loaded"), e.model.onWidgetsInited.dispatch())
                                    };
                                    e.addScript(n.widgetUrl, w, (function() {
                                        u.Log.warning('Widget "' + n.widgetTitle + "\" wasn't loaded!"), w();
                                        var t = document.getElementById("game");
                                        n.widgetUniqueId === g.EWrapperWidgetID.REQUEST_FULLSCREEN && !h.default(t) && p.default(t.src) && e.model.onSetGameSrc.dispatch()
                                    }))
                                }
                            })), l.default(t, (function(t) {
                                e.model.widgetsData.splice(e.model.widgetsData.indexOf(t), 1)
                            }))
                        }, t.prototype.formFirstUrl = function(e) {
                            return c.UrlUtil.checkUrlParamValue(a.EUrlParamName.URL_CONVERTER, !1) ? e : y(this.model.wrapperPath, e)
                        }, t.prototype.addScript = function(e, t, n) {
                            var r = this,
                                o = this.formFirstUrl(e);
                            s.HtmlUtil.isFileExistOnServer(o, (function() {
                                s.HtmlUtil.addScript(o, t, n)
                            }), (function() {
                                s.HtmlUtil.isFileExistOnServer(e, (function() {
                                    s.HtmlUtil.addScript(e, t, n)
                                }), (function() {
                                    s.HtmlUtil.addScript(r.model.convertToBackupUrl(e), t, n)
                                }))
                            }))
                        }, t.prototype.addStyle = function(e) {
                            var t = this,
                                n = this.formFirstUrl(e);
                            s.HtmlUtil.isFileExistOnServer(n, (function() {
                                s.HtmlUtil.addStyle(n)
                            }), (function() {
                                s.HtmlUtil.isFileExistOnServer(e, (function() {
                                    s.HtmlUtil.addStyle(e)
                                }), (function() {
                                    s.HtmlUtil.addStyle(t.model.convertToBackupUrl(e))
                                }))
                            }))
                        }, t.prototype.checkValues = function(e, t) {
                            return 1 === e.length ? e[0].value === t : !!f.default(e, (function(e) {
                                return e.value === t
                            }))
                        }, t.prototype.isCurrentLidExist = function(e) {
                            var t = this,
                                n = !1;
                            return l.default(e.values, (function(e) {
                                if (e.value === t.model.wrapperRequestData.licenseId) return n = !0, !1
                            })), n
                        }, t
                    }(v.WrapperBaseCommand);
                t.WrapperInitWidgetsCommand = E
            },
            1052: function(e, t, n) {
                "use strict";
                var r, o = this && this.__extends || (r = function(e, t) {
                        return (r = Object.setPrototypeOf || {
                                __proto__: []
                            }
                            instanceof Array && function(e, t) {
                                e.__proto__ = t
                            } || function(e, t) {
                                for (var n in t) Object.prototype.hasOwnProperty.call(t, n) && (e[n] = t[n])
                            })(e, t)
                    }, function(e, t) {
                        if ("function" != typeof t && null !== t) throw new TypeError("Class extends value " + String(t) + " is not a constructor or null");

                        function n() {
                            this.constructor = e
                        }
                        r(e, t), e.prototype = null === t ? Object.create(t) : (n.prototype = t.prototype, new n)
                    }),
                    i = this && this.__awaiter || function(e, t, n, r) {
                        return new(n || (n = Promise))((function(o, i) {
                            function a(e) {
                                try {
                                    u(r.next(e))
                                } catch (e) {
                                    i(e)
                                }
                            }

                            function s(e) {
                                try {
                                    u(r.throw(e))
                                } catch (e) {
                                    i(e)
                                }
                            }

                            function u(e) {
                                var t;
                                e.done ? o(e.value) : (t = e.value, t instanceof n ? t : new n((function(e) {
                                    e(t)
                                }))).then(a, s)
                            }
                            u((r = r.apply(e, t || [])).next())
                        }))
                    },
                    a = this && this.__generator || function(e, t) {
                        var n, r, o, i, a = {
                            label: 0,
                            sent: function() {
                                if (1 & o[0]) throw o[1];
                                return o[1]
                            },
                            trys: [],
                            ops: []
                        };
                        return i = {
                            next: s(0),
                            throw: s(1),
                            return: s(2)
                        }, "function" == typeof Symbol && (i[Symbol.iterator] = function() {
                            return this
                        }), i;

                        function s(i) {
                            return function(s) {
                                return function(i) {
                                    if (n) throw new TypeError("Generator is already executing.");
                                    for (; a;) try {
                                        if (n = 1, r && (o = 2 & i[0] ? r.return : i[0] ? r.throw || ((o = r.return) && o.call(r), 0) : r.next) && !(o = o.call(r, i[1])).done) return o;
                                        switch (r = 0, o && (i = [2 & i[0], o.value]), i[0]) {
                                            case 0:
                                            case 1:
                                                o = i;
                                                break;
                                            case 4:
                                                return a.label++, {
                                                    value: i[1],
                                                    done: !1
                                                };
                                            case 5:
                                                a.label++, r = i[1], i = [0];
                                                continue;
                                            case 7:
                                                i = a.ops.pop(), a.trys.pop();
                                                continue;
                                            default:
                                                if (!(o = a.trys, (o = o.length > 0 && o[o.length - 1]) || 6 !== i[0] && 2 !== i[0])) {
                                                    a = 0;
                                                    continue
                                                }
                                                if (3 === i[0] && (!o || i[1] > o[0] && i[1] < o[3])) {
                                                    a.label = i[1];
                                                    break
                                                }
                                                if (6 === i[0] && a.label < o[1]) {
                                                    a.label = o[1], o = i;
                                                    break
                                                }
                                                if (o && a.label < o[2]) {
                                                    a.label = o[2], a.ops.push(i);
                                                    break
                                                }
                                                o[2] && a.ops.pop(), a.trys.pop();
                                                continue
                                        }
                                        i = t.call(e, a)
                                    } catch (e) {
                                        i = [6, e], r = 0
                                    } finally {
                                        n = o = 0
                                    }
                                    if (5 & i[0]) throw i[1];
                                    return {
                                        value: i[0] ? i[1] : void 0,
                                        done: !0
                                    }
                                }([i, s])
                            }
                        }
                    },
                    s = this && this.__importDefault || function(e) {
                        return e && e.__esModule ? e : {
                            default: e
                        }
                    };
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.WrapperLoadFontsCommand = void 0;
                var u = n(4069),
                    c = s(n(312)),
                    l = n(1039),
                    d = function(e) {
                        function t() {
                            return null !== e && e.apply(this, arguments) || this
                        }
                        return o(t, e), t.prototype.execute = function() {
                            return i(this, void 0, Promise, (function() {
                                var e;
                                return a(this, (function(t) {
                                    switch (t.label) {
                                        case 0:
                                            return e = [{
                                                name: JSON.parse(c.default.defaultFontBold),
                                                file: u.EAssetFontName.BOLD
                                            }, {
                                                name: JSON.parse(c.default.defaultFontBlack),
                                                file: u.EAssetFontName.BLACK
                                            }], l.FontFaceUtil.add({
                                                path: this.model.wrapperPath + "assets/fonts/",
                                                fonts: e,
                                                nodeId: "default_fonts"
                                            }), [4, l.FontFaceUtil.checkLoading({
                                                fonts: e,
                                                signal: this.model.onFontsLoaded,
                                                commandName: "WrapperLoadFontsCommand"
                                            })];
                                        case 1:
                                            return t.sent(), [2]
                                    }
                                }))
                            }))
                        }, t
                    }(n(7854).WrapperBaseCommand);
                t.WrapperLoadFontsCommand = d
            },
            7097: function(e, t, n) {
                "use strict";
                var r, o = this && this.__extends || (r = function(e, t) {
                        return (r = Object.setPrototypeOf || {
                                __proto__: []
                            }
                            instanceof Array && function(e, t) {
                                e.__proto__ = t
                            } || function(e, t) {
                                for (var n in t) Object.prototype.hasOwnProperty.call(t, n) && (e[n] = t[n])
                            })(e, t)
                    }, function(e, t) {
                        if ("function" != typeof t && null !== t) throw new TypeError("Class extends value " + String(t) + " is not a constructor or null");

                        function n() {
                            this.constructor = e
                        }
                        r(e, t), e.prototype = null === t ? Object.create(t) : (n.prototype = t.prototype, new n)
                    }),
                    i = this && this.__createBinding || (Object.create ? function(e, t, n, r) {
                        void 0 === r && (r = n), Object.defineProperty(e, r, {
                            enumerable: !0,
                            get: function() {
                                return t[n]
                            }
                        })
                    } : function(e, t, n, r) {
                        void 0 === r && (r = n), e[r] = t[n]
                    }),
                    a = this && this.__setModuleDefault || (Object.create ? function(e, t) {
                        Object.defineProperty(e, "default", {
                            enumerable: !0,
                            value: t
                        })
                    } : function(e, t) {
                        e.default = t
                    }),
                    s = this && this.__importStar || function(e) {
                        if (e && e.__esModule) return e;
                        var t = {};
                        if (null != e)
                            for (var n in e) "default" !== n && Object.prototype.hasOwnProperty.call(e, n) && i(t, e, n);
                        return a(t, e), t
                    };
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.WrapperRegisterDisablePinchToZoomCommand = void 0;
                var u = s(n(1795)),
                    c = n(4168),
                    l = n(1621),
                    d = n(1504),
                    f = function(e) {
                        function t() {
                            return null !== e && e.apply(this, arguments) || this
                        }
                        return o(t, e), t.prototype.execute = function() {
                            this.validatePlatform() && this.setupDOMEventListeners()
                        }, t.prototype.setupDOMEventListeners = function() {
                            window.addEventListener(c.EWrapperEvents.GESTURESTART, (function(e) {
                                return e.preventDefault()
                            })), window.addEventListener(c.EWrapperEvents.GESTURECHANGE, (function(e) {
                                return e.preventDefault()
                            })), window.addEventListener(c.EWrapperEvents.GESTUREEND, (function(e) {
                                return e.preventDefault()
                            }))
                        }, t.prototype.validatePlatform = function() {
                            return (u.os.family === l.EWrapperOS.IOS && parseInt(u.os.version, 10) > 8 || -1 !== u.ua.indexOf("like Mac OS X")) && u.product !== d.EWrapperPlatforms.IPAD
                        }, t
                    }(n(7854).WrapperBaseCommand);
                t.WrapperRegisterDisablePinchToZoomCommand = f
            },
            1556: function(e, t, n) {
                "use strict";
                var r, o = this && this.__extends || (r = function(e, t) {
                    return (r = Object.setPrototypeOf || {
                            __proto__: []
                        }
                        instanceof Array && function(e, t) {
                            e.__proto__ = t
                        } || function(e, t) {
                            for (var n in t) Object.prototype.hasOwnProperty.call(t, n) && (e[n] = t[n])
                        })(e, t)
                }, function(e, t) {
                    if ("function" != typeof t && null !== t) throw new TypeError("Class extends value " + String(t) + " is not a constructor or null");

                    function n() {
                        this.constructor = e
                    }
                    r(e, t), e.prototype = null === t ? Object.create(t) : (n.prototype = t.prototype, new n)
                });
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.WrapperResizeCommand = void 0;
                var i = function(e) {
                    function t() {
                        return null !== e && e.apply(this, arguments) || this
                    }
                    return o(t, e), t.prototype.execute = function() {
                        e.prototype.execute.call(this)
                    }, t
                }(n(2318).ResizeCommand);
                t.WrapperResizeCommand = i
            },
            3790: function(e, t, n) {
                "use strict";
                var r, o = this && this.__extends || (r = function(e, t) {
                        return (r = Object.setPrototypeOf || {
                                __proto__: []
                            }
                            instanceof Array && function(e, t) {
                                e.__proto__ = t
                            } || function(e, t) {
                                for (var n in t) Object.prototype.hasOwnProperty.call(t, n) && (e[n] = t[n])
                            })(e, t)
                    }, function(e, t) {
                        if ("function" != typeof t && null !== t) throw new TypeError("Class extends value " + String(t) + " is not a constructor or null");

                        function n() {
                            this.constructor = e
                        }
                        r(e, t), e.prototype = null === t ? Object.create(t) : (n.prototype = t.prototype, new n)
                    }),
                    i = this && this.__importDefault || function(e) {
                        return e && e.__esModule ? e : {
                            default: e
                        }
                    };
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.WrapperSetGameSkinSrcCommand = void 0;
                var a = n(9945),
                    s = i(n(6073)),
                    u = i(n(3311)),
                    c = n(9610),
                    l = n(7519),
                    d = n(4746),
                    f = function(e) {
                        function t() {
                            return null !== e && e.apply(this, arguments) || this
                        }
                        return o(t, e), t.prototype.execute = function() {
                            var e = this;
                            this._gameSkinWidgetData = this.model.getWidgetData(d.EWrapperWidgetID.GAMES_SKINS_WIDGET);
                            var t = u.default(this._gameSkinWidgetData.actions, (function(e) {
                                    return e.actionName === c.EWrapperActionName.PRELOADER
                                })),
                                n = this.model.wrapperRequestData.gameLink;
                            n ? n.indexOf(l.EWrapperActionParameterName.GAME_SKIN) >= 0 || n.indexOf(l.EWrapperActionParameterName.UI_SKIN) >= 0 ? a.Log.warning("Game Skin and/or Game UI parameters have already been added to the Game link") : s.default(t.parameters, (function(t) {
                                if (t.name === l.EWrapperActionParameterName.GAME_SKIN || t.name === l.EWrapperActionParameterName.UI_SKIN) {
                                    var r = -1 === n.indexOf("?") ? "?" : "&";
                                    s.default(t.values, (function(n, o) {
                                        n.name === e.model.wrapperRequestData.skinId.toString() && (e.model.wrapperRequestData.gameLink += "" + r + t.name + "=" + t.values[o].value)
                                    }))
                                }
                            })) : a.Log.warning("Game link didn't received yet")
                        }, t
                    }(n(7854).WrapperBaseCommand);
                t.WrapperSetGameSkinSrcCommand = f
            },
            5040: function(e, t, n) {
                "use strict";
                var r, o = this && this.__extends || (r = function(e, t) {
                    return (r = Object.setPrototypeOf || {
                            __proto__: []
                        }
                        instanceof Array && function(e, t) {
                            e.__proto__ = t
                        } || function(e, t) {
                            for (var n in t) Object.prototype.hasOwnProperty.call(t, n) && (e[n] = t[n])
                        })(e, t)
                }, function(e, t) {
                    if ("function" != typeof t && null !== t) throw new TypeError("Class extends value " + String(t) + " is not a constructor or null");

                    function n() {
                        this.constructor = e
                    }
                    r(e, t), e.prototype = null === t ? Object.create(t) : (n.prototype = t.prototype, new n)
                });
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.WrapperSetGameSrcCommand = void 0;
                var i = function(e) {
                    function t() {
                        return null !== e && e.apply(this, arguments) || this
                    }
                    return o(t, e), t.prototype.execute = function() {
                        this.model.wrapperRequestData.gameLink && this.view.setGameSrc(this.model.wrapperRequestData.gameLink), this.model.wrapperRequestData.gameLinkPOSTcontent && this.view.setGamePostLink(this.model.wrapperRequestData.gameLinkPOSTcontent)
                    }, t
                }(n(7854).WrapperBaseCommand);
                t.WrapperSetGameSrcCommand = i
            },
            9852: function(e, t, n) {
                "use strict";
                var r, o = this && this.__extends || (r = function(e, t) {
                        return (r = Object.setPrototypeOf || {
                                __proto__: []
                            }
                            instanceof Array && function(e, t) {
                                e.__proto__ = t
                            } || function(e, t) {
                                for (var n in t) Object.prototype.hasOwnProperty.call(t, n) && (e[n] = t[n])
                            })(e, t)
                    }, function(e, t) {
                        if ("function" != typeof t && null !== t) throw new TypeError("Class extends value " + String(t) + " is not a constructor or null");

                        function n() {
                            this.constructor = e
                        }
                        r(e, t), e.prototype = null === t ? Object.create(t) : (n.prototype = t.prototype, new n)
                    }),
                    i = this && this.__importDefault || function(e) {
                        return e && e.__esModule ? e : {
                            default: e
                        }
                    };
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.WrapperShowWidgetCommand = void 0;
                var a = i(n(4293)),
                    s = function(e) {
                        function t() {
                            return null !== e && e.apply(this, arguments) || this
                        }
                        return o(t, e), t.prototype.execute = function() {
                            this._widgetData = this.model.getWidgetData(this.model.widgetToShow), this._layoutPosition = this.model.getLayoutPosition(this._widgetData), a.default(this.model.widgetList[this._widgetData.widgetUniqueId]) || this.view.showWidget(this._layoutPosition, this._widgetData.widgetUniqueId)
                        }, t
                    }(n(7854).WrapperBaseCommand);
                t.WrapperShowWidgetCommand = s
            },
            3362: function(e, t, n) {
                "use strict";
                var r, o = this && this.__extends || (r = function(e, t) {
                    return (r = Object.setPrototypeOf || {
                            __proto__: []
                        }
                        instanceof Array && function(e, t) {
                            e.__proto__ = t
                        } || function(e, t) {
                            for (var n in t) Object.prototype.hasOwnProperty.call(t, n) && (e[n] = t[n])
                        })(e, t)
                }, function(e, t) {
                    if ("function" != typeof t && null !== t) throw new TypeError("Class extends value " + String(t) + " is not a constructor or null");

                    function n() {
                        this.constructor = e
                    }
                    r(e, t), e.prototype = null === t ? Object.create(t) : (n.prototype = t.prototype, new n)
                });
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.WrapperVersionPrintCommand = void 0;
                var i = n(9945),
                    a = function(e) {
                        function t() {
                            var t = null !== e && e.apply(this, arguments) || this;
                            return t._versionLib = t.model.versionLib, t._versionWrapper = t.model.version, t
                        }
                        return o(t, e), t.prototype.execute = function() {
                            i.Log.forceInfoConsolech("Version: Overlay Wrapper Lib", this._versionLib), i.Log.forceInfoConsolech("Version: Overlay Wrapper", this._versionWrapper)
                        }, t
                    }(n(7854).WrapperBaseCommand);
                t.WrapperVersionPrintCommand = a
            },
            9610: function(e, t) {
                "use strict";
                Object.defineProperty(t, "__esModule", {
                        value: !0
                    }), t.EWrapperActionName = void 0,
                    function(e) {
                        e.PRELOADER = "Preloader", e.DRAGGABLE_DEFAULT_POSITION = "Draggable Default Position"
                    }(t.EWrapperActionName || (t.EWrapperActionName = {}))
            },
            7519: function(e, t) {
                "use strict";
                Object.defineProperty(t, "__esModule", {
                        value: !0
                    }), t.EWrapperActionParameterName = void 0,
                    function(e) {
                        e.GAME_SKIN = "gameSkinURL", e.UI_SKIN = "uiSkinURL", e.DEFAULT_POSITION = "Default Position"
                    }(t.EWrapperActionParameterName || (t.EWrapperActionParameterName = {}))
            },
            1197: function(e, t) {
                "use strict";
                Object.defineProperty(t, "__esModule", {
                        value: !0
                    }), t.EWrapperCommandName = void 0,
                    function(e) {
                        e.GET_WIDGETS_DATA = "GET_WIDGETS_DATA", e.GET_GAMES_EVENT_DATA = "GET_GAMES_EVENT_DATA", e.DRAW_WIDGETS = "DRAW_WIDGETS", e.DRAW_WIDGET = "DRAW_WIDGET", e.HIDE_WIDGET = "HIDE_WIDGET", e.SHOW_WIDGET = "SHOW_WIDGET", e.INIT_WIDGETS = "INIT_WIDGETS", e.SET_GAME_SRC = "SET_GAME_SRC", e.SET_GAME_SKIN_SRC = "SET_GAME_SKIN_SRC", e.ADD_URL_PARAMS_TO_GAME_LINK = "ADD_URL_PARAMS_TO_GAME_LINK", e.REGISTER_DISABLE_PINCH_TO_ZOOM = "REGISTER_DISABLE_PINCH_TO_ZOOM", e.ADD_VERSION_TO_CONSOLE = "ADD_VERSION_TO_CONSOLE", e.ADD_DRAGGABLE_EVENT_LISTENER = "ADD_DRAGGABLE_EVENT_LISTENER", e.CHECK_DRAGGABLE_POSITION = "CHECK_DRAGGABLE_POSITION", e.LOAD_FONTS = "LOAD_FONTS"
                    }(t.EWrapperCommandName || (t.EWrapperCommandName = {}))
            },
            4168: function(e, t) {
                "use strict";
                Object.defineProperty(t, "__esModule", {
                        value: !0
                    }), t.EWrapperEvents = void 0,
                    function(e) {
                        e.GESTURESTART = "gesturestart", e.GESTURECHANGE = "gesturechange", e.GESTUREEND = "gestureend"
                    }(t.EWrapperEvents || (t.EWrapperEvents = {}))
            },
            3113: function(e, t) {
                "use strict";
                Object.defineProperty(t, "__esModule", {
                        value: !0
                    }), t.EWrapperGameLinkUrlParamName = void 0,
                    function(e) {
                        e.AUTO_UPDATE_RTP_SKIN_ID = "autoUpdateRTPSkinId", e.RULES_URL = "rulesURL", e.LID = "lid"
                    }(t.EWrapperGameLinkUrlParamName || (t.EWrapperGameLinkUrlParamName = {}))
            },
            3551: function(e, t) {
                "use strict";
                Object.defineProperty(t, "__esModule", {
                        value: !0
                    }), t.EWrapperMessageLog = void 0,
                    function(e) {
                        e.LOG_REQUIRED_PARAMETER_UNDEFINED_IN_DRAW_WIDGET_METHOD = "One of the required parameters of the 'drawWidget' method, undefined!", e.LOG_WIDGET_AVAILABLE_ONLY_ON_FUN_MODE = "Widget available only on fun mode!", e.LOG_WIDGET_AVAILABLE_ONLY_ON_REAL_MODE = "Widget available only on real mode!", e.LOG_WIDGET_AVAILABLE_ONLY_ON_SPECIFIED_LID = "Widget available only on specified LID's!", e.LOG_WIDGET_NOT_AVAILABLE_FOR_THIS_LID = "Widget available only on specified LID's!", e.LOG_IN_THIS_CASE_WIDGET_NOT_AVAILABLE_WITH_PARAM_DISABLEGAT = "Widget is not available when disableGAT parameter is (equal true) and GAP Launcher link have test_api_endpoint or test_module_url parameters", e.LOG_ALL_WIDGETS_IS_FULLY_LOADED = "WrapperInitWidgetsCommand :: all widgets is fully loaded", e.LOG_SERVER_MOCK_DATA = "server mock data:"
                    }(t.EWrapperMessageLog || (t.EWrapperMessageLog = {}))
            },
            1621: function(e, t) {
                "use strict";
                Object.defineProperty(t, "__esModule", {
                        value: !0
                    }), t.EWrapperOS = void 0,
                    function(e) {
                        e.WINDOWS = "Windows", e.IOS = "iOS", e.UBUNTU = "Ubuntu", e.ANDROID = "Android"
                    }(t.EWrapperOS || (t.EWrapperOS = {}))
            },
            1504: function(e, t) {
                "use strict";
                Object.defineProperty(t, "__esModule", {
                        value: !0
                    }), t.EWrapperPlatforms = void 0,
                    function(e) {
                        e.IPHONE = "iPhone", e.IPAD = "iPad"
                    }(t.EWrapperPlatforms || (t.EWrapperPlatforms = {}))
            },
            60: function(e, t) {
                "use strict";
                Object.defineProperty(t, "__esModule", {
                        value: !0
                    }), t.EWrapperProviderID = void 0,
                    function(e) {
                        e[e.ISB = 1] = "ISB", e[e.PLAYSON = 8] = "PLAYSON", e[e.QUICKSPIN = 13] = "QUICKSPIN", e[e.NSOFT = 32] = "NSOFT", e[e.ORYX = 51] = "ORYX", e[e.NEWPLAYSON = 67] = "NEWPLAYSON"
                    }(t.EWrapperProviderID || (t.EWrapperProviderID = {}))
            },
            3967: function(e, t) {
                "use strict";
                Object.defineProperty(t, "__esModule", {
                        value: !0
                    }), t.EWrapperUrlParamName = void 0,
                    function(e) {
                        e.ENABLE_CONSOLE_PANEL = "enableConsolePanel", e.DISABLE_WRAPPER_ERROR_HANDLER = "disableWrapperErrorHandler"
                    }(t.EWrapperUrlParamName || (t.EWrapperUrlParamName = {}))
            },
            4746: function(e, t) {
                "use strict";
                Object.defineProperty(t, "__esModule", {
                        value: !0
                    }), t.EWrapperWidgetID = void 0,
                    function(e) {
                        e[e.REQUEST_FULLSCREEN = 761] = "REQUEST_FULLSCREEN", e[e.NETWORK_JACKPOT_WIDGET = 932] = "NETWORK_JACKPOT_WIDGET", e[e.GAMES_SKINS_WIDGET = 1516] = "GAMES_SKINS_WIDGET", e[e.UNIVERSAL_WIDGET = 1458] = "UNIVERSAL_WIDGET", e[e.OPT_IN_WIDGET = 1204] = "OPT_IN_WIDGET", e[e.UNIVERSAL_COMPLIANCE_FULLSCREEN = 1523] = "UNIVERSAL_COMPLIANCE_FULLSCREEN"
                    }(t.EWrapperWidgetID || (t.EWrapperWidgetID = {}))
            },
            1669: function(e, t) {
                "use strict";
                Object.defineProperty(t, "__esModule", {
                        value: !0
                    }), t.EWrapperWidgetRequestData = void 0,
                    function(e) {
                        e.GAME_LINK = "gameLink"
                    }(t.EWrapperWidgetRequestData || (t.EWrapperWidgetRequestData = {}))
            },
            6619: function(e, t) {
                "use strict";
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.MOCK_ISB_GAME_EVENTS_DATA = void 0, t.MOCK_ISB_GAME_EVENTS_DATA = [{
                    standardEvent: "LOADING_STARTED",
                    isMandatory: !0,
                    eventValue: "PRELOADER"
                }, {
                    standardEvent: "LOADING_COMPLETED",
                    isMandatory: !0,
                    eventValue: "LOAD_COMPLETE"
                }, {
                    standardEvent: "INTRO_OPENED",
                    isMandatory: !0,
                    eventValue: "INTRO_OPENED"
                }, {
                    standardEvent: "INTRO_CLOSED",
                    isMandatory: !0,
                    eventValue: "INTRO_CLOSED"
                }, {
                    standardEvent: "GAME_READY",
                    isMandatory: !0,
                    eventValue: "GAME_READY"
                }, {
                    standardEvent: "ROUND_STARTED",
                    isMandatory: !0,
                    eventValue: "GAMEROUND_STARTED"
                }, {
                    standardEvent: "ROUND_ENDED",
                    isMandatory: !0,
                    eventValue: "GAMEROUND_ENDED"
                }, {
                    standardEvent: "FEATURE_POP_UP_OPENED",
                    isMandatory: !0,
                    eventValue: "FEATURE_POP_UP_OPEN"
                }, {
                    standardEvent: "SPINS_GROUP_STARTED",
                    isMandatory: !0,
                    eventValue: "SPINS_GROUP_STARTED"
                }, {
                    standardEvent: "SPINS_GROUP_ENDED",
                    isMandatory: !0,
                    eventValue: "SPINS_GROUP_ENDED"
                }, {
                    standardEvent: "FEATURE_STARTED",
                    isMandatory: !0,
                    eventValue: "FEATURE_STARTED"
                }, {
                    standardEvent: "FEATURE_ENDED",
                    isMandatory: !0,
                    eventValue: "FEATURE_ENDED"
                }, {
                    standardEvent: "BONUS_CONFIGURATION",
                    isMandatory: !0,
                    eventValue: "CURRENT_BONUS_DETAILS"
                }, {
                    standardEvent: "BONUS_STEP_STARTED",
                    isMandatory: !0,
                    eventValue: "CURRENT_STEP_DETAILS"
                }, {
                    standardEvent: "BONUS_STARTED",
                    isMandatory: !0,
                    eventValue: "BONUS_READY_TO_CHOOSE"
                }, {
                    standardEvent: "BONUS_STEP_ENDED",
                    isMandatory: !0,
                    eventValue: "BONUS_CHOICE_ANIM_FINISHED"
                }, {
                    standardEvent: "BONUS_WINNINGS_SHOWN",
                    isMandatory: !0,
                    eventValue: "BONUS_WINNINGS"
                }, {
                    standardEvent: "PROGRESSIVE_WON",
                    isMandatory: !0,
                    eventValue: "PROGRESSIVE_WIN"
                }, {
                    standardEvent: "PAGE_OPENED",
                    isMandatory: !0,
                    eventValue: "PAGE_OPEN"
                }, {
                    standardEvent: "PAGE_SCROLLED",
                    isMandatory: !0,
                    eventValue: "PAGE_SCROLL"
                }, {
                    standardEvent: "PAGE_CLOSED",
                    isMandatory: !0,
                    eventValue: "PAGE_CLOSE"
                }, {
                    standardEvent: "RCI_STARTED",
                    isMandatory: !0,
                    eventValue: "RCI_START"
                }, {
                    standardEvent: "RCI_CHECKPOINT",
                    isMandatory: !0,
                    eventValue: "RCI_CHECKPOINT"
                }, {
                    standardEvent: "TURBO_SPIN_RESPONSE_RECEIVED",
                    isMandatory: !0,
                    eventValue: "TURBO_SPIN_END_GAME"
                }, {
                    standardEvent: "TURBO_WHEELS_REELS_STOPPED",
                    isMandatory: !0,
                    eventValue: "TURBO_WHEELS_ANIMATIONS_COMPLETE"
                }, {
                    standardEvent: "GAME_CLOSED",
                    isMandatory: !0,
                    eventValue: "GAME_CLOSED"
                }, {
                    standardEvent: "AUTO_SPIN_ENDED",
                    isMandatory: !0,
                    eventValue: "AUTO_SPIN_END"
                }, {
                    standardEvent: "STOP_AUTO_PLAY",
                    isMandatory: !0,
                    eventValue: "stop_auto_play"
                }, {
                    standardEvent: "SHOW_PAY_TABLE",
                    isMandatory: !0,
                    eventValue: "show_pay_table"
                }, {
                    standardEvent: "HIDE_PAY_TABLE",
                    isMandatory: !0,
                    eventValue: "hide_pay_table"
                }, {
                    standardEvent: "MUTE_SOUNDS",
                    isMandatory: !0,
                    eventValue: "mute"
                }, {
                    standardEvent: "UNMUTE_SOUNDS",
                    isMandatory: !0,
                    eventValue: "unmute"
                }, {
                    standardEvent: "ACTION_NOT_ALLOWED",
                    isMandatory: !0,
                    eventValue: "ACTION_NOT_ALLOWED"
                }, {
                    standardEvent: "SOUNDS_UNMUTED",
                    isMandatory: !0,
                    eventValue: "SOUNDS_UNMUTED"
                }, {
                    standardEvent: "SOUNDS_MUTED",
                    isMandatory: !0,
                    eventValue: "SOUNDS_MUTED"
                }, {
                    standardEvent: "DISPLAY_VALUES",
                    isMandatory: !0,
                    eventValue: "DISPLAY_VALUES"
                }, {
                    standardEvent: "BLOCK_BETS",
                    isMandatory: !0,
                    eventValue: "block_bets"
                }, {
                    standardEvent: "UNBLOCK_BETS",
                    isMandatory: !0,
                    eventValue: "unblock_bets"
                }, {
                    standardEvent: "PRELOADER_STEP",
                    isMandatory: !0,
                    eventValue: "PRELOADER_STEP"
                }, {
                    standardEvent: "POST_LISTENER_INITIALIZED",
                    isMandatory: !0,
                    eventValue: "POST_MESSAGE_LISTENER_INITIALIZED"
                }, {
                    standardEvent: "BETS_BLOCKED",
                    isMandatory: !0,
                    eventValue: "BETS_BLOCKED"
                }, {
                    standardEvent: "BETS_UNBLOCKED",
                    isMandatory: !0,
                    eventValue: "BETS_UNBLOCKED"
                }, {
                    standardEvent: "READY_FOR_EXTERNAL_ACTIONS",
                    isMandatory: !0,
                    eventValue: "READY_FOR_EXTERNAL_ACTIONS"
                }, {
                    standardEvent: "AUTO_SPIN_NOT_IN_PROGRESS",
                    isMandatory: !0,
                    eventValue: "AUTO_SPIN_NOT_IN_PROGRESS"
                }, {
                    standardEvent: "READY_TO_SPIN",
                    isMandatory: !0,
                    eventValue: "READY_TO_SPIN"
                }, {
                    standardEvent: "SYMBOLS_ARRAY",
                    isMandatory: !0,
                    eventValue: "SYMBOLS_ARRAY"
                }]
            },
            5757: function(e, t, n) {
                "use strict";
                var r = this && this.__spreadArray || function(e, t) {
                    for (var n = 0, r = t.length, o = e.length; n < r; n++, o++) e[o] = t[n];
                    return e
                };
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.MOCK_GAME_EVENTS_DATA = void 0;
                var o = n(6619);
                t.MOCK_GAME_EVENTS_DATA = Object.freeze({
                    data: r([], o.MOCK_ISB_GAME_EVENTS_DATA)
                })
            },
            2418: function(e, t) {
                "use strict";
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.MOCK_WIDGET_PUBLIC_DATA = void 0, t.MOCK_WIDGET_PUBLIC_DATA = [{
                    displaySettings: 1,
                    widgetTitle: "Universal Compliance Fullscreen Widget",
                    widgetUrl: "./dist/bundle.js",
                    widgetBourl: "nothing",
                    widgetUniqueId: 1523,
                    layout: {
                        id: 12,
                        position: "Draggable",
                        depth: 0,
                        description: "",
                        layoutSchemaName: "ISOFTBET Layout",
                        layoutSchemaId: 1
                    },
                    actions: [{
                        actionName: "Preloader",
                        isLiveData: !1,
                        parameters: [{
                            name: "css",
                            type: "text",
                            values: [{
                                name: "path",
                                value: "./dist/index.css"
                            }]
                        }],
                        targets: []
                    }]
                }]
            },
            9099: function(e, t, n) {
                "use strict";
                var r, o = this && this.__extends || (r = function(e, t) {
                    return (r = Object.setPrototypeOf || {
                            __proto__: []
                        }
                        instanceof Array && function(e, t) {
                            e.__proto__ = t
                        } || function(e, t) {
                            for (var n in t) Object.prototype.hasOwnProperty.call(t, n) && (e[n] = t[n])
                        })(e, t)
                }, function(e, t) {
                    if ("function" != typeof t && null !== t) throw new TypeError("Class extends value " + String(t) + " is not a constructor or null");

                    function n() {
                        this.constructor = e
                    }
                    r(e, t), e.prototype = null === t ? Object.create(t) : (n.prototype = t.prototype, new n)
                });
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.WrapperDraggableWidgetModel = void 0;
                var i = function(e) {
                    function t(t, n) {
                        var r = e.call(this) || this;
                        return r._data = t, r._cookies = n, r.parseDefaultPositionFromActionParams(), r
                    }
                    return o(t, e), t
                }(n(490).AbstractWidgetDraggableModel);
                t.WrapperDraggableWidgetModel = i
            },
            7696: function(e, t, n) {
                "use strict";
                var r, o = this && this.__extends || (r = function(e, t) {
                        return (r = Object.setPrototypeOf || {
                                __proto__: []
                            }
                            instanceof Array && function(e, t) {
                                e.__proto__ = t
                            } || function(e, t) {
                                for (var n in t) Object.prototype.hasOwnProperty.call(t, n) && (e[n] = t[n])
                            })(e, t)
                    }, function(e, t) {
                        if ("function" != typeof t && null !== t) throw new TypeError("Class extends value " + String(t) + " is not a constructor or null");

                        function n() {
                            this.constructor = e
                        }
                        r(e, t), e.prototype = null === t ? Object.create(t) : (n.prototype = t.prototype, new n)
                    }),
                    i = this && this.__assign || function() {
                        return (i = Object.assign || function(e) {
                            for (var t, n = 1, r = arguments.length; n < r; n++)
                                for (var o in t = arguments[n]) Object.prototype.hasOwnProperty.call(t, o) && (e[o] = t[o]);
                            return e
                        }).apply(this, arguments)
                    },
                    a = this && this.__importDefault || function(e) {
                        return e && e.__esModule ? e : {
                            default: e
                        }
                    };
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.WrapperModel = void 0;
                var s = n(7344),
                    u = n(5272),
                    c = n(8394),
                    l = n(9945),
                    d = n(7388),
                    f = n(4019),
                    p = n(9979),
                    h = a(n(3311)),
                    m = a(n(4293)),
                    _ = a(n(9704)),
                    g = n(3664),
                    v = a(n(4147)),
                    y = n(4076),
                    E = n(60),
                    b = n(9099),
                    O = function(e) {
                        function t() {
                            var t = e.call(this) || this;
                            return t.latestBroadcastEvents = {}, t.currentWidgetID = -1, t.screenManager = null, t.wrapperPath = "", t.draggableWidgetModels = new Map, t.providersListIframeScrolling = [E.EWrapperProviderID.NSOFT], t._backupUrl = "https://static-widgets.isoftbet.com", t._gameUrl = "", t.onHtmlBuilt = new g.Signal, t.onWidgetsDataReceived = new g.Signal, t.onOMDown = new g.Signal, t.onSetGameSrc = new g.Signal, t.onGamesEventDataReceived = new g.Signal, t.onWidgetsInited = new g.Signal, t.onConfigLoaded = new g.Signal, t.onFontsLoaded = new g.Signal, t.interaction = new c.CoreInteractionManager, t.cookies = new d.CookiesManager, t.isActive = !0, t.widgetsData = [], t.widgetList = [], t
                        }
                        return o(t, e), Object.defineProperty(t.prototype, "wrapperRequestData", {
                            get: function() {
                                return this._wrapperRequestData
                            },
                            set: function(e) {
                                var t = e.gameLink.split("/");
                                this._gameUrl = t[0] + "//" + t[1] + t[2], this._wrapperRequestData = e
                            },
                            enumerable: !1,
                            configurable: !0
                        }), Object.defineProperty(t.prototype, "serverEventsLink", {
                            get: function() {
                                return this._serverEventsLink
                            },
                            enumerable: !1,
                            configurable: !0
                        }), Object.defineProperty(t.prototype, "serverLink", {
                            get: function() {
                                return this._serverLink
                            },
                            enumerable: !1,
                            configurable: !0
                        }), Object.defineProperty(t.prototype, "htmlElementName", {
                            get: function() {
                                return "wrapper_container"
                            },
                            enumerable: !1,
                            configurable: !0
                        }), Object.defineProperty(t.prototype, "htmlWidgetContainerName", {
                            get: function() {
                                return "html_layout_container"
                            },
                            enumerable: !1,
                            configurable: !0
                        }), Object.defineProperty(t.prototype, "version", {
                            get: function() {
                                return v.default.version
                            },
                            enumerable: !1,
                            configurable: !0
                        }), t.prototype.setLatestBroadcastEvents = function(e) {
                            m.default(e) ? l.Log.warningch(y.WRAPPER_NAME, "Can't set event to latest broadcast events, it's not defined") : this.latestBroadcastEvents[e.eventName] = {
                                messageData: i({}, e.messageData),
                                timestamp: (new Date).getTime()
                            }
                        }, t.prototype.setupServerLink = function() {
                            this._serverLink = this.config.serverLink, this._serverEventsLink = this.config.serverLink + "/events"
                        }, t.prototype.isWidgetExist = function(e) {
                            return _.default(this.widgetsData, (function(t) {
                                return t.widgetUniqueId === e
                            }))
                        }, Object.defineProperty(t.prototype, "shouldIframeScrollingBeEnabled", {
                            get: function() {
                                var e = this;
                                return _.default(this.providersListIframeScrolling, (function(t) {
                                    return t === e.wrapperRequestData.providerId
                                }))
                            },
                            enumerable: !1,
                            configurable: !0
                        }), t.prototype.isUrlParameterTrue = function(e, t) {
                            return t ? p.UrlUtil.checkUrlParamValue(e, !0, t) : p.UrlUtil.checkUrlParamValue(e, !0)
                        }, t.prototype.checkTestParameters = function() {
                            this.wrapperRequestData.testApiEndpoint = p.UrlUtil.urlParams(this.wrapperRequestData.gameLink, "test_api_endpoint"), this.wrapperRequestData.testModuleUrl = p.UrlUtil.urlParams(this.wrapperRequestData.gameLink, "test_module_url")
                        }, t.prototype.convertToBackupUrl = function(e) {
                            return p.UrlUtil.convertUrlToDomainBaseUrl(this._backupUrl, e, !1)
                        }, t.prototype.convertToGameUrl = function(e) {
                            return p.UrlUtil.convertUrlToDomainBaseUrl(this._gameUrl, e, !1)
                        }, t.prototype.getWrapperConfig = function() {
                            var e = this,
                                t = this.wrapperPath + "wrapper.config.json";
                            fetch(t).then((function(e) {
                                return e.text()
                            })).then((function(t) {
                                e.config = JSON.parse(t), e.setupServerLink(), e.onConfigLoaded.dispatch()
                            }))
                        }, t.prototype.getCurrentWidgetData = function() {
                            return this.getWidgetData(this.currentWidgetID)
                        }, t.prototype.getWidgetData = function(e) {
                            return h.default(this.widgetsData, (function(t) {
                                return t.widgetUniqueId === e
                            }))
                        }, t.prototype.getLayoutPosition = function(e) {
                            return f.StringUtil.toSnakeCase(e.layout.position)
                        }, t.prototype.createDraggableWidgetModel = function() {
                            var e = this.getCurrentWidgetData(),
                                t = new b.WrapperDraggableWidgetModel(e, this.cookies);
                            this.draggableWidgetModels.set(this.currentWidgetID, t)
                        }, t.prototype.getCurrentDraggableWidgetModel = function() {
                            return this.draggableWidgetModels.get(this.currentWidgetID)
                        }, t.prototype.isCurrentWidgetDraggable = function() {
                            return this.getCurrentWidgetData().layout.position === s.EWidgetLayoutType.DRAGGABLE
                        }, t
                    }(u.AbstractModel);
                t.WrapperModel = O
            },
            2304: function(e, t, n) {
                "use strict";
                var r = this && this.__importDefault || function(e) {
                    return e && e.__esModule ? e : {
                        default: e
                    }
                };
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.WrapperView = void 0;
                var o = n(7344),
                    i = n(9945),
                    a = n(4019),
                    s = r(n(6073)),
                    u = r(n(4293)),
                    c = r(n(9704)),
                    l = n(3551),
                    d = function() {
                        function e(e, t, n, r) {
                            this._firstHeaderIteration = !0, this._templateContainers = {}, this._htmlNode = n, this._baseNode = r
                        }
                        return e.prototype.drawWidget = function(t, n, r, d) {
                            var f = c.default(r, (function(e) {
                                return u.default(e)
                            }));
                            if (u.default(t) || 0 === r.length || f) i.Log.warning(l.EWrapperMessageLog.LOG_REQUIRED_PARAMETER_UNDEFINED_IN_DRAW_WIDGET_METHOD);
                            else {
                                var p = t + n;
                                this._templateContainers[p] = document.getElementById(t);
                                var h, m = document.createElement("div");
                                t === a.StringUtil.toSnakeCase(o.EWidgetLayoutType.HEADER) ? (h = document.querySelector("#" + t), this._firstHeaderIteration && (e.obtainCastNode(h), this._firstHeaderIteration = !1), h.classList.remove("widget--hidden"), h.style.height = "auto", h.style.display = "block", m.style.position = "relative", m.style.width = "100%") : ((h = this.createDuplicateLayout().querySelector("#" + t)).style.minHeight = "45px", h.style.display = "inherit", m.style.position = "absolute"), h.style.backgroundColor = "transparent", m.style.pointerEvents = "none", m.style.top = "0", m.style.left = "0", m.style.right = "0", m.style.bottom = "0", m.style.zIndex = "" + d, m.setAttribute("name", t + n), s.default(r, (function(e) {
                                    e.style.pointerEvents = "all", e.style.overflow = "hidden", m.appendChild(e)
                                })), h.appendChild(m)
                            }
                        }, e.prototype.createDuplicateLayout = function() {
                            var t = document.getElementById("html_layout_duplicated");
                            if (u.default(t)) {
                                var n = document.getElementById("html_layout");
                                t = document.createElement("div"), (t = n.cloneNode(!0)).id = "html_layout_duplicated", t.style.opacity = "1", s.default(t.querySelectorAll(".horizontalWidget, .centerWidget, .fullScreenWidget"), (function(e) {
                                    e.style.display = "none"
                                })), s.default(t.children, (function(t) {
                                    var n = t;
                                    e.obtainCastNode(n)
                                })), this._htmlNode.appendChild(t)
                            }
                            return t
                        }, e.prototype.showWidget = function(e, t) {
                            var n = document.querySelector("[name='" + (e + t) + "']");
                            u.default(n) || (n.style.display = "block", n.style.pointerEvents = "none")
                        }, e.prototype.hideWidget = function(e, t) {
                            var n = document.querySelector("[name='" + (e + t) + "']");
                            u.default(n) || (n.style.display = "none", n.style.pointerEvents = "none")
                        }, e.prototype.dispose = function() {}, e.prototype.onEnterFrameRedraw = function() {}, e.prototype.resize = function(e, t) {}, e.prototype.setEnabled = function(e) {}, e.prototype.enableGameIframeScrolling = function() {
                            this.gameIframe.scrolling = "yes"
                        }, e.prototype.setGameSrc = function(e) {
                            document.getElementById("game_form").action = e, document.getElementById("game").src = e
                        }, e.prototype.setGamePostLink = function(e) {
                            document.getElementById("gameLinkPOSTcontent").setAttribute("value", e), document.getElementById("game_form").submit()
                        }, e.prototype.resizeContainer = function(e, t) {
                            this.baseNode && (this.baseNode.style.width = e + "px", this.baseNode.style.height = t + "px")
                        }, Object.defineProperty(e.prototype, "gameIframe", {
                            get: function() {
                                return document.getElementById("game")
                            },
                            enumerable: !1,
                            configurable: !0
                        }), Object.defineProperty(e.prototype, "gameForm", {
                            get: function() {
                                return document.getElementById("game_form")
                            },
                            enumerable: !1,
                            configurable: !0
                        }), Object.defineProperty(e.prototype, "baseNode", {
                            get: function() {
                                return u.default(this._baseNode) && (this._baseNode = document.getElementById("wrapper_container")), this._baseNode
                            },
                            enumerable: !1,
                            configurable: !0
                        }), e.updateDefaultStyles = function(e) {
                            e.style.opacity = "1", e.style.padding = "0", e.style.border = "none", e.style.background = "none"
                        }, e.obtainCastNode = function(t) {
                            t.classList.contains("bodyWidgetsContainer") ? s.default(t.children, (function(t) {
                                var n = t;
                                n.innerHTML = "", e.updateDefaultStyles(n)
                            })) : t.innerHTML = "", e.updateDefaultStyles(t)
                        }, e
                    }();
                t.WrapperView = d
            },
            6214: function(e, t, n) {
                "use strict";
                var r = this && this.__assign || function() {
                        return (r = Object.assign || function(e) {
                            for (var t, n = 1, r = arguments.length; n < r; n++)
                                for (var o in t = arguments[n]) Object.prototype.hasOwnProperty.call(t, o) && (e[o] = t[o]);
                            return e
                        }).apply(this, arguments)
                    },
                    o = this && this.__spreadArray || function(e, t) {
                        for (var n = 0, r = t.length, o = e.length; n < r; n++, o++) e[o] = t[n];
                        return e
                    },
                    i = this && this.__importDefault || function(e) {
                        return e && e.__esModule ? e : {
                            default: e
                        }
                    };
                Object.defineProperty(t, "__esModule", {
                    value: !0
                }), t.PublicApi = void 0;
                var a = i(n(3311)),
                    s = n(3664),
                    u = n(9417);
                n(232), n(4015), n(7147);
                var c = function() {
                    function e() {}
                    return e.addSignal = function(e) {
                        this._signals.has(e) ? console.log("can't add signal... " + e + " already exist...") : this._signals.set(e, new s.Signal)
                    }, e.getSignal = function(e) {
                        if (this._signals.has(e)) return this._signals.get(e);
                        console.log("can't get signal... " + e + " doesn't exist...")
                    }, e.removeSignal = function(e) {
                        this._signals.has(e) ? this._signals.delete(e) : console.log("can't remove signal... " + e + " doesn't exist...")
                    }, e.initWithData = function(t) {
                        e.wrapperContext.initialize(t)
                    }, e.getWidgetData = function(t) {
                        return r(r({}, a.default(e.wrapperContext.getModel().widgetsData, (function(e) {
                            return e.widgetUniqueId === t
                        }))), this.wrapperContext.getModel().wrapperRequestData)
                    }, e.drawWidget = function(e) {
                        for (var t, n = [], r = 1; r < arguments.length; r++) n[r - 1] = arguments[r];
                        (t = this.wrapperContext).drawWidget.apply(t, o([e], n))
                    }, e.setDefaultPositionForDraggableWidget = function(e, t) {
                        this.wrapperContext.setDefaultPositionForDraggableWidget(e, t)
                    }, e.showWidget = function(e) {
                        this.wrapperContext.showWidget(e)
                    }, e.hideWidget = function(e) {
                        this.wrapperContext.hideWidget(e)
                    }, e.widgetAssetsLoaded = function() {
                        this.wrapperContext.onWidgetAssetsLoaded()
                    }, e.getLatestBroadcastEvents = function() {
                        return this.wrapperContext.getModel().latestBroadcastEvents
                    }, e.wrapperContext = new u.WrapperContext, e._signals = new Map, e
                }();
                t.PublicApi = c
            },
            7147: function(e, t, n) {
                "use strict";
                n.r(t), n.d(t, {
                    Headers: function() {
                        return h
                    },
                    Request: function() {
                        return b
                    },
                    Response: function() {
                        return w
                    },
                    DOMException: function() {
                        return P
                    },
                    fetch: function() {
                        return A
                    }
                });
                var r = "undefined" != typeof globalThis && globalThis || "undefined" != typeof self && self || void 0 !== r && r,
                    o = "URLSearchParams" in r,
                    i = "Symbol" in r && "iterator" in Symbol,
                    a = "FileReader" in r && "Blob" in r && function() {
                        try {
                            return new Blob, !0
                        } catch (e) {
                            return !1
                        }
                    }(),
                    s = "FormData" in r,
                    u = "ArrayBuffer" in r;
                if (u) var c = ["[object Int8Array]", "[object Uint8Array]", "[object Uint8ClampedArray]", "[object Int16Array]", "[object Uint16Array]", "[object Int32Array]", "[object Uint32Array]", "[object Float32Array]", "[object Float64Array]"],
                    l = ArrayBuffer.isView || function(e) {
                        return e && c.indexOf(Object.prototype.toString.call(e)) > -1
                    };

                function d(e) {
                    if ("string" != typeof e && (e = String(e)), /[^a-z0-9\-#$%&'*+.^_`|~!]/i.test(e) || "" === e) throw new TypeError('Invalid character in header field name: "' + e + '"');
                    return e.toLowerCase()
                }

                function f(e) {
                    return "string" != typeof e && (e = String(e)), e
                }

                function p(e) {
                    var t = {
                        next: function() {
                            var t = e.shift();
                            return {
                                done: void 0 === t,
                                value: t
                            }
                        }
                    };
                    return i && (t[Symbol.iterator] = function() {
                        return t
                    }), t
                }

                function h(e) {
                    this.map = {}, e instanceof h ? e.forEach((function(e, t) {
                        this.append(t, e)
                    }), this) : Array.isArray(e) ? e.forEach((function(e) {
                        this.append(e[0], e[1])
                    }), this) : e && Object.getOwnPropertyNames(e).forEach((function(t) {
                        this.append(t, e[t])
                    }), this)
                }

                function m(e) {
                    if (e.bodyUsed) return Promise.reject(new TypeError("Already read"));
                    e.bodyUsed = !0
                }

                function _(e) {
                    return new Promise((function(t, n) {
                        e.onload = function() {
                            t(e.result)
                        }, e.onerror = function() {
                            n(e.error)
                        }
                    }))
                }

                function g(e) {
                    var t = new FileReader,
                        n = _(t);
                    return t.readAsArrayBuffer(e), n
                }

                function v(e) {
                    if (e.slice) return e.slice(0);
                    var t = new Uint8Array(e.byteLength);
                    return t.set(new Uint8Array(e)), t.buffer
                }

                function y() {
                    return this.bodyUsed = !1, this._initBody = function(e) {
                        var t;
                        this.bodyUsed = this.bodyUsed, this._bodyInit = e, e ? "string" == typeof e ? this._bodyText = e : a && Blob.prototype.isPrototypeOf(e) ? this._bodyBlob = e : s && FormData.prototype.isPrototypeOf(e) ? this._bodyFormData = e : o && URLSearchParams.prototype.isPrototypeOf(e) ? this._bodyText = e.toString() : u && a && ((t = e) && DataView.prototype.isPrototypeOf(t)) ? (this._bodyArrayBuffer = v(e.buffer), this._bodyInit = new Blob([this._bodyArrayBuffer])) : u && (ArrayBuffer.prototype.isPrototypeOf(e) || l(e)) ? this._bodyArrayBuffer = v(e) : this._bodyText = e = Object.prototype.toString.call(e) : this._bodyText = "", this.headers.get("content-type") || ("string" == typeof e ? this.headers.set("content-type", "text/plain;charset=UTF-8") : this._bodyBlob && this._bodyBlob.type ? this.headers.set("content-type", this._bodyBlob.type) : o && URLSearchParams.prototype.isPrototypeOf(e) && this.headers.set("content-type", "application/x-www-form-urlencoded;charset=UTF-8"))
                    }, a && (this.blob = function() {
                        var e = m(this);
                        if (e) return e;
                        if (this._bodyBlob) return Promise.resolve(this._bodyBlob);
                        if (this._bodyArrayBuffer) return Promise.resolve(new Blob([this._bodyArrayBuffer]));
                        if (this._bodyFormData) throw new Error("could not read FormData body as blob");
                        return Promise.resolve(new Blob([this._bodyText]))
                    }, this.arrayBuffer = function() {
                        if (this._bodyArrayBuffer) {
                            var e = m(this);
                            return e || (ArrayBuffer.isView(this._bodyArrayBuffer) ? Promise.resolve(this._bodyArrayBuffer.buffer.slice(this._bodyArrayBuffer.byteOffset, this._bodyArrayBuffer.byteOffset + this._bodyArrayBuffer.byteLength)) : Promise.resolve(this._bodyArrayBuffer))
                        }
                        return this.blob().then(g)
                    }), this.text = function() {
                        var e, t, n, r = m(this);
                        if (r) return r;
                        if (this._bodyBlob) return e = this._bodyBlob, t = new FileReader, n = _(t), t.readAsText(e), n;
                        if (this._bodyArrayBuffer) return Promise.resolve(function(e) {
                            for (var t = new Uint8Array(e), n = new Array(t.length), r = 0; r < t.length; r++) n[r] = String.fromCharCode(t[r]);
                            return n.join("")
                        }(this._bodyArrayBuffer));
                        if (this._bodyFormData) throw new Error("could not read FormData body as text");
                        return Promise.resolve(this._bodyText)
                    }, s && (this.formData = function() {
                        return this.text().then(O)
                    }), this.json = function() {
                        return this.text().then(JSON.parse)
                    }, this
                }
                h.prototype.append = function(e, t) {
                    e = d(e), t = f(t);
                    var n = this.map[e];
                    this.map[e] = n ? n + ", " + t : t
                }, h.prototype.delete = function(e) {
                    delete this.map[d(e)]
                }, h.prototype.get = function(e) {
                    return e = d(e), this.has(e) ? this.map[e] : null
                }, h.prototype.has = function(e) {
                    return this.map.hasOwnProperty(d(e))
                }, h.prototype.set = function(e, t) {
                    this.map[d(e)] = f(t)
                }, h.prototype.forEach = function(e, t) {
                    for (var n in this.map) this.map.hasOwnProperty(n) && e.call(t, this.map[n], n, this)
                }, h.prototype.keys = function() {
                    var e = [];
                    return this.forEach((function(t, n) {
                        e.push(n)
                    })), p(e)
                }, h.prototype.values = function() {
                    var e = [];
                    return this.forEach((function(t) {
                        e.push(t)
                    })), p(e)
                }, h.prototype.entries = function() {
                    var e = [];
                    return this.forEach((function(t, n) {
                        e.push([n, t])
                    })), p(e)
                }, i && (h.prototype[Symbol.iterator] = h.prototype.entries);
                var E = ["DELETE", "GET", "HEAD", "OPTIONS", "POST", "PUT"];

                function b(e, t) {
                    if (!(this instanceof b)) throw new TypeError('Please use the "new" operator, this DOM object constructor cannot be called as a function.');
                    var n, r, o = (t = t || {}).body;
                    if (e instanceof b) {
                        if (e.bodyUsed) throw new TypeError("Already read");
                        this.url = e.url, this.credentials = e.credentials, t.headers || (this.headers = new h(e.headers)), this.method = e.method, this.mode = e.mode, this.signal = e.signal, o || null == e._bodyInit || (o = e._bodyInit, e.bodyUsed = !0)
                    } else this.url = String(e);
                    if (this.credentials = t.credentials || this.credentials || "same-origin", !t.headers && this.headers || (this.headers = new h(t.headers)), this.method = (n = t.method || this.method || "GET", r = n.toUpperCase(), E.indexOf(r) > -1 ? r : n), this.mode = t.mode || this.mode || null, this.signal = t.signal || this.signal, this.referrer = null, ("GET" === this.method || "HEAD" === this.method) && o) throw new TypeError("Body not allowed for GET or HEAD requests");
                    if (this._initBody(o), !("GET" !== this.method && "HEAD" !== this.method || "no-store" !== t.cache && "no-cache" !== t.cache)) {
                        var i = /([?&])_=[^&]*/;
                        if (i.test(this.url)) this.url = this.url.replace(i, "$1_=" + (new Date).getTime());
                        else {
                            this.url += (/\?/.test(this.url) ? "&" : "?") + "_=" + (new Date).getTime()
                        }
                    }
                }

                function O(e) {
                    var t = new FormData;
                    return e.trim().split("&").forEach((function(e) {
                        if (e) {
                            var n = e.split("="),
                                r = n.shift().replace(/\+/g, " "),
                                o = n.join("=").replace(/\+/g, " ");
                            t.append(decodeURIComponent(r), decodeURIComponent(o))
                        }
                    })), t
                }

                function w(e, t) {
                    if (!(this instanceof w)) throw new TypeError('Please use the "new" operator, this DOM object constructor cannot be called as a function.');
                    t || (t = {}), this.type = "default", this.status = void 0 === t.status ? 200 : t.status, this.ok = this.status >= 200 && this.status < 300, this.statusText = void 0 === t.statusText ? "" : "" + t.statusText, this.headers = new h(t.headers), this.url = t.url || "", this._initBody(e)
                }
                b.prototype.clone = function() {
                    return new b(this, {
                        body: this._bodyInit
                    })
                }, y.call(b.prototype), y.call(w.prototype), w.prototype.clone = function() {
                    return new w(this._bodyInit, {
                        status: this.status,
                        statusText: this.statusText,
                        headers: new h(this.headers),
                        url: this.url
                    })
                }, w.error = function() {
                    var e = new w(null, {
                        status: 0,
                        statusText: ""
                    });
                    return e.type = "error", e
                };
                var S = [301, 302, 303, 307, 308];
                w.redirect = function(e, t) {
                    if (-1 === S.indexOf(t)) throw new RangeError("Invalid status code");
                    return new w(null, {
                        status: t,
                        headers: {
                            location: e
                        }
                    })
                };
                var P = r.DOMException;
                try {
                    new P
                } catch (e) {
                    (P = function(e, t) {
                        this.message = e, this.name = t;
                        var n = Error(e);
                        this.stack = n.stack
                    }).prototype = Object.create(Error.prototype), P.prototype.constructor = P
                }

                function A(e, t) {
                    return new Promise((function(n, o) {
                        var i = new b(e, t);
                        if (i.signal && i.signal.aborted) return o(new P("Aborted", "AbortError"));
                        var s = new XMLHttpRequest;

                        function c() {
                            s.abort()
                        }
                        s.onload = function() {
                            var e, t, r = {
                                status: s.status,
                                statusText: s.statusText,
                                headers: (e = s.getAllResponseHeaders() || "", t = new h, e.replace(/\r?\n[\t ]+/g, " ").split("\r").map((function(e) {
                                    return 0 === e.indexOf("\n") ? e.substr(1, e.length) : e
                                })).forEach((function(e) {
                                    var n = e.split(":"),
                                        r = n.shift().trim();
                                    if (r) {
                                        var o = n.join(":").trim();
                                        t.append(r, o)
                                    }
                                })), t)
                            };
                            r.url = "responseURL" in s ? s.responseURL : r.headers.get("X-Request-URL");
                            var o = "response" in s ? s.response : s.responseText;
                            setTimeout((function() {
                                n(new w(o, r))
                            }), 0)
                        }, s.onerror = function() {
                            setTimeout((function() {
                                o(new TypeError("Network request failed"))
                            }), 0)
                        }, s.ontimeout = function() {
                            setTimeout((function() {
                                o(new TypeError("Network request failed"))
                            }), 0)
                        }, s.onabort = function() {
                            setTimeout((function() {
                                o(new P("Aborted", "AbortError"))
                            }), 0)
                        }, s.open(i.method, function(e) {
                            try {
                                return "" === e && r.location.href ? r.location.href : e
                            } catch (t) {
                                return e
                            }
                        }(i.url), !0), "include" === i.credentials ? s.withCredentials = !0 : "omit" === i.credentials && (s.withCredentials = !1), "responseType" in s && (a ? s.responseType = "blob" : u && i.headers.get("Content-Type") && -1 !== i.headers.get("Content-Type").indexOf("application/octet-stream") && (s.responseType = "arraybuffer")), !t || "object" != typeof t.headers || t.headers instanceof h ? i.headers.forEach((function(e, t) {
                            s.setRequestHeader(t, e)
                        })) : Object.getOwnPropertyNames(t.headers).forEach((function(e) {
                            s.setRequestHeader(e, f(t.headers[e]))
                        })), i.signal && (i.signal.addEventListener("abort", c), s.onreadystatechange = function() {
                            4 === s.readyState && i.signal.removeEventListener("abort", c)
                        }), s.send(void 0 === i._bodyInit ? null : i._bodyInit)
                    }))
                }
                A.polyfill = !0, r.fetch || (r.fetch = A, r.Headers = h, r.Request = b, r.Response = w)
            },
            855: function(e) {
                "use strict";
                e.exports = JSON.parse('{"version":"1.10.1-0.0"}')
            },
            4147: function(e) {
                "use strict";
                e.exports = JSON.parse('{"name":"overlay-wrapper","version":"39.51.48-5.2","description":"","private":true,"scripts":{"tools":"npm run generate-subconfigs && npm run generate-config-assets && npm run generate-enums","generate-subconfigs":"npm run generate-subconfig-images && npm run generate-subconfig-fonts && npm run generate-subconfig-atlases","generate-subconfig-images":"node tools/generate_sub_config.js \\"img\\" \\"imageNames\\" \\"images\\"","generate-subconfig-fonts":"node tools/generate_sub_config.js \\"fonts\\" \\"fontNames\\" \\"fonts\\" \\"woff\\"","generate-subconfig-atlases":"node tools/generate_sub_config.js \\"atlas\\" \\"atlasNames\\" \\"atlases\\" \\"json\\"","generate-config-assets":"node tools/generate_assets_configs.js","generate-enums":"npm run generate-enum-images && npm run generate-enum-fonts && npm run generate-enum-atlases","generate-enum-images":"node tools/generate_filename_enum.js imageNames","generate-enum-fonts":"node tools/generate_filename_enum.js fontNames","generate-enum-atlases":"node tools/generate_atlas_enum.js","config":"node tools/generate_config.js","build-mock":"webpack --node-env MOCK --bail --progress --profile --config ./webpack_config/webpack.dev.js && npm run validate","build-local":"webpack --node-env LOCAL --bail --progress --profile --config ./webpack_config/webpack.dev.js && npm run validate","build-test":"cross-var \\"npm run config %npm_config_ENV_TO_TEST% && webpack --node-env TEST --bail --progress --profile --config ./webpack_config/webpack.dev.js && npm run validate\\"","build-dev":"npm run config DEV && webpack --node-env DEVELOPMENT --bail --progress --profile --config ./webpack_config/webpack.dev.js && npm run validate","build-qa":"npm run config QA && webpack --node-env QA --bail --progress --profile --config ./webpack_config/webpack.dev.js && npm run validate","build-stage":"npm run config STAGE && webpack --node-env STAGE --bail --progress --profile --config ./webpack_config/webpack.prod.js && npm run validate","build-prod":"npm run config PRODUCTION && webpack --node-env PRODUCTION --bail --progress --profile --config ./webpack_config/webpack.prod.js && npm run validate","build":"npm run build-dev","watch":"webpack --node-env DEVELOPMENT --config --watch --info-verbosity verbose","serve":"webpack serve --node-env DEVELOPMENT --config ./webpack_config/webpack.dev.js","test":"karma start karma.conf.js --browsers=ChromeHeadless --reporters=summary --singleRun=true","test-serve":"karma start karma.conf.js","lint":"eslint --ext .js,.ts .","prettier":"prettier \\"./**/*.+(js|json|ts|html|scss|css)\\"","format":"npm run prettier -- --write","check-format":"npm run prettier -- --check","validate":"npm-run-all --parallel lint check-format","prepare":"husky install","pre-commit":"lint-staged"},"repository":{"type":"git","url":"https://bitbucket.isoftbet.com/scm/ov/overlay_wrapper.git"},"keywords":[],"author":"Andrii Barvynko","devDependencies":{"@types/fontfaceobserver":"2.1.0","@types/jasmine":"3.8.2","@types/lodash":"4.14.172","@types/node":"16.6.0","@types/platform":"1.3.4","@types/signals":"1.0.1","@typescript-eslint/eslint-plugin":"4.29.1","@typescript-eslint/parser":"4.29.1","autoprefixer":"10.3.1","clean-css":"5.1.5","clean-webpack-plugin":"4.0.0-alpha.0","copy-webpack-plugin":"9.0.1","cross-var":"1.1.0","css-loader":"5.2.7","css-minimizer-webpack-plugin":"3.0.2","eslint":"7.32.0","eslint-config-prettier":"8.3.0","eslint-plugin-import":"2.24.0","eslint-plugin-jsdoc":"36.0.7","eslint-plugin-unused-imports":"1.1.3","expose-loader":"^3.0.0","fork-ts-checker-webpack-plugin":"6.3.2","html-webpack-plugin":"5.5.0","husky":"7.0.1","ignore-loader":"0.1.2","jasmine":"3.8.0","jasmine-core":"3.8.0","jasmine-spec-reporter":"7.0.0","karma":"6.3.4","karma-chrome-launcher":"3.1.0","karma-coverage":"2.0.3","karma-jasmine":"4.0.1","karma-jasmine-html-reporter":"1.7.0","karma-spec-reporter":"0.0.32","karma-summary-reporter":"2.0.2","karma-webpack":"5.0.0","lint-staged":"11.1.2","mini-css-extract-plugin":"1.6.2","npm-run-all":"4.1.5","postcss-loader":"5.3.0","prettier":"2.3.2","sass":"1.37.5","sass-loader":"12.1.0","source-map-loader":"3.0.0","style-loader":"3.2.1","terser-webpack-plugin":"5.1.4","ts-loader":"9.2.5","typescript":"4.2.4","webpack":"5.50.0","webpack-cli":"4.7.2","webpack-dev-server":"3.11.2","webpack-merge":"5.8.0"},"dependencies":{"@overlay/lib":"1.10.1-0.0","element-closest-polyfill":"1.0.4","fontfaceobserver":"2.1.0","lodash":"4.17.21","platform":"1.3.6","promise":"8.1.0","signals":"1.0.0","whatwg-fetch":"3.6.2"},"browserslist":["last 2 versions","> 1%","last 3 IOS versions"]}')
            }
        },
        t = {};

    function n(r) {
        var o = t[r];
        if (void 0 !== o) return o.exports;
        var i = t[r] = {
            id: r,
            loaded: !1,
            exports: {}
        };
        return e[r].call(i.exports, i, i.exports, n), i.loaded = !0, i.exports
    }
    n.n = function(e) {
        var t = e && e.__esModule ? function() {
            return e.default
        } : function() {
            return e
        };
        return n.d(t, {
            a: t
        }), t
    }, n.d = function(e, t) {
        for (var r in t) n.o(t, r) && !n.o(e, r) && Object.defineProperty(e, r, {
            enumerable: !0,
            get: t[r]
        })
    }, n.g = function() {
        if ("object" == typeof globalThis) return globalThis;
        try {
            return this || new Function("return this")()
        } catch (e) {
            if ("object" == typeof window) return window
        }
    }(), n.o = function(e, t) {
        return Object.prototype.hasOwnProperty.call(e, t)
    }, n.r = function(e) {
        "undefined" != typeof Symbol && Symbol.toStringTag && Object.defineProperty(e, Symbol.toStringTag, {
            value: "Module"
        }), Object.defineProperty(e, "__esModule", {
            value: !0
        })
    }, n.nmd = function(e) {
        return e.paths = [], e.children || (e.children = []), e
    };
    n(1585)
}();