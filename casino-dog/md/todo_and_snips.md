## Todo
- a lot hehe for productional, though as soon I am able to create stuff again, I will release free casino for any to use (no hidden anything, just like this package)
- this package need a lot of actual testing, mind you i'm far from releasing it (minimum few days) while writing this msg as need to do TON of refactoring as i built this as i went on
- make app toggleable between "proxy_only" and full app, configurable in config
- refactor OPTIMIZATIONS.md much more clearly and categorize between "Simple" (easy toggleable) and "Advanced" optimizations
- add in centrifuge.md
- add in docs md
- add in "respinning" (aka the wainwright magic touch) feature to skip big bonuses
- add in pragmatic play completely, also finish bgaming's api version 0 (apiv2 is done it seem), add in either red tiger, netent, isofbet or any relaxgaming
- fiat-currency settings

## Fun Todo's - todo's that will have little usecase possibly or non-finished but cause I can't bore around all day doing same shit over and over
- editable jackpot system pragmaticplay games
- replay system pragmaticplay
- simple scan (public) with rulesets to check if a casino is using any frauded games, though probably need to use selenium for this and idk if can soon)
- possibly some simple ggr counting
- create a more configurable & more enjoyable job queue system with somehow custom rules (though cause is also lot of frontend input, i can hopefully in few months start uploading without issue)
- selenium test jobs to "health check" the slotmachine launches (basically you just need to setup a link list & let the selenium client spam spacebar to spin and catch error). Right now there is a simple test done on the 'extra_game_meta' job that is launched when updating the origin demo link, however this is not actual spinning but just looking if page doesn't turn error on launch.
- sports(?)
- simple casino frontend or representation
- eh, idk i hope i pick up some new stuff to practice/learn as building slots especially for goal of ratting out casino people is rather boring, you're expected if you want to pick this up more then for a random usecase to seriously look at all code & also to refactor a fuck ton

## oryx

```
<!DOCTYPE html>
<html>
	<head>

		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<meta name="apple-mobile-web-app-status-bar-style" content="black" />
		<meta name="format-detection" content="no" />

		<meta name="viewport" content="user-scalable=no, minimal-ui" />
				<title>Game</title>
		<style>
			html, body { background-color: #000000;  }
		</style>
		<script>

			var gameRef = null;
			function pushGameEvent( eventName ) {
				if( gameRef && typeof gameRef.handleAction === 'function' ) { gameRef.handleAction( eventName ); }
			}

			// read other configuration from config.xml and app_version
			(function() {

				var GameFunctions = {}
				GameFunctions.gameLoaded = function() {

					window.addEventListener('message', function(event) {
						try {
							if (event === undefined || event.data === undefined || event.data.wpgaction === undefined) {
								return;
							}

							if( gameRef && typeof gameRef.handleAction === 'function' ) {

								switch (event.data.wpgaction) {
									case 'suspend':
									case 'doGameSuspend':
										gameRef.handleAction('doGameSuspend');
										break;
									case 'doGamePause':
										gameRef.handleAction('doGamePause');
										break;
									case 'doGameResume':
										gameRef.handleAction('doGameResume');
										break;
								}
							}
						} catch (e) {
							console.error(e);
						}
					});

				}

				GameFunctions.gameClose = function() {
										if (loadGetParam('isGameBridge') === 'true') {
        					window.parent.postMessage({ message: "close", param: null}, "*");
					        window.top.postMessage({ wpgaction: "close" }, '*');
					        window.parent.postMessage({ wpgaction: "close.parent" }, '*');
					} else if (loadGetParam('lobbyUrl') !== false && loadGetParam('lobbyUrl') != 'LOBBY_URL' && loadGetParam('lobbyUrl') != 'DUMMY' && loadGetParam('lobbyUrl') != 'OFF' && loadGetParam('lobbyUrl') != 'WCLOSE') {
						if (loadGetParam('lobbyUrlTarget')==='TOP') {
							window.top.location = loadGetParam('lobbyUrl');
						} else {
							window.location = loadGetParam('lobbyUrl');
						}
					} else if (loadGetParam('lobbyUrl') !== false && loadGetParam('lobbyUrl') == 'WCLOSE') {
						window.close();
					} else if (inIframe()) {
						window.parent.postMessage({ message: "close", param: null}, "*");
						window.top.postMessage({ wpgaction: "close" }, '*');
						window.parent.postMessage({ wpgaction: "close.parent" }, '*');
					} else {
						window.history.back();
					}
				}
				GameFunctions.actionLoadStart = function() {
					window.top.postMessage({ wpgaction: "loadStart" }, '*');
					window.parent.postMessage({ wpgaction: "loadStart.parent" }, '*')
				};

				GameFunctions.externalGameHistoryHandler = function() {
					if (loadGetParam('historyUrl') !== undefined) {
						if (loadGetParam('historyUrl') == "EVENT" || loadGetParam('historyUrl') == "GAMEBRIDGE") {
							window.top.postMessage({ wpgaction: "openGameHistory" }, '*');
							window.parent.postMessage({ wpgaction: "openGameHistory.parent" }, '*');
						} else {
							window.open(loadGetParam('historyUrl'), "gamehistory", "scrollbars=1,height=600,width=760");
						}
					}
				}

				var inIframe = function() {
					try {
						return window.self !== window.top;
					} catch (e) {
						return true;
					}
				}
				function loadGetParam(key) {
					var query = window.location.search.substring(1);
					var vars = query.split("&");

					for (var i = 0; i < vars.length; i++) {
						var pair = vars[i].split("=");
						if( pair[0] === key ) {
							try {
								return decodeURIComponent( pair[1] );
							}
							catch( e ) {
								return pair[1];
							}
						}
					}

					return false;
				};

				function parseGameCode(originalGameCode) {
					var result = {};
					//normalize data
					var basicGameCode = originalGameCode.replace(/GAMM_/, "GAM_")
						.replace(/_CCSF/, "_CCS")
						.replace(/_DE/, "")

					//split into array
					var gameCodeArray = basicGameCode.split('_');
					// remove part "DE" from gameCode array
					var deRtpPosition = gameCodeArray.findIndex((item) => item === "DE");
					if (deRtpPosition >= 0) {
						gameCodeArray.splice(deRtpPosition, 1);
					}

					// remove rtp values like 90, 92, 94 from gameCode array
					var rtpPosition = gameCodeArray.findIndex((item) => !isNaN(parseInt(item)));
					if (rtpPosition >= 0) {
						var removedElements = gameCodeArray.splice(rtpPosition, 1);
						result.rtp = removedElements[0];
					}

					// return the calculated gameCode and the rtp value
					result.basicGameCode = gameCodeArray.join('_');
					return result;
				}

				var isReconnect = loadGetParam( 'isReconnect' );
				if( isReconnect ) { isReconnect = parseInt( isReconnect ); }

				var reconnectCount = loadGetParam( 'reconnects' );
				if( reconnectCount ) { reconnectCount = parseInt( reconnectCount ); }

				// external handler for closing the game
				function closeHandler() {
					GameFunctions.gameClose();
				};

				// external handler to open the game history (set it to null)
				var externalGameHistoryHandler = function() {
					GameFunctions.externalGameHistoryHandler();
				}

				if (!String.prototype.startsWith) {
					String.prototype.startsWith = function(searchString, position){
						position = position || 0;
						return this.substr(position, searchString.length) === searchString;
					};
				}

				// gameHistory type can be 'ingame' or 'external' (anything else will be interpreted as 'none')
				var gameHistoryType = 'ingame';

				// math info display type can be 'partOfVersionDisplay' or 'freeFloating' (anything else will be interpreted as 'none')
				var slotInfoDisplayType = 'none';
				var showMathInfos = true;
				var showNetProfit = false;
				var showElapsedTime = false;

				// how and when are unfinished games resolved?
                                // resolving type can be 'automatic' or 'manual' (anything else will be interpreted as 'none')
                                var unfinishedGamesResolvingType = 'none';
                                var unfinishedGamesResolvingInterval = 90;

				// game events
				var gameEventHandler = function( op, data ) {
					//console.log( 'send game event', eventName, data );
					//GameFunctions.gameLoaded
					if (op === 'balance') {
						if (window.parent) {
							window.parent.postMessage({ message: "balance", param: data }, "*");
						}
						window.top.postMessage({ wpgaction: "balance", param: data }, '*');
						window.parent.postMessage({ wpgaction: "balance.parent", param: data }, '*');
					}

					if (op === 'loadStart') {
						GameFunctions.actionLoadStart();
					}

					if (op === 'loadEnd') {
						window.top.postMessage({ wpgaction: "loadEnd" }, '*');
						window.parent.postMessage({ wpgaction: "loadEnd.parent" }, '*');
						GameFunctions.gameLoaded();

					}

					if (op === 'gameSpinStart') {
						window.top.postMessage({ wpgaction: "gameSpinStart" }, '*');
						window.parent.postMessage({ wpgaction: "gameSpinStart.parent" }, '*');

					}

					if (op === 'gameSpinEnd') {
						window.top.postMessage({ wpgaction: "gameSpinEnd" }, '*');
						window.parent.postMessage({ wpgaction: "gameSpinEnd.parent" }, '*');

					}

					if (op === 'errorOutOfMoney') {
						window.top.postMessage({ wpgaction: "errorOutOfMoney" }, '*');
						window.parent.postMessage({ wpgaction: "errorOutOfMoney.parent" }, '*');

					}

									}

				// load and interpret the get parameters
				var supportedLanguages = {
					'deu': 'de',
					'ger': 'de',
					'chi': 'zh',
					'cht': 'zh',
					'zho': 'zh',
					'cze': 'cs',
					'dan': 'da',
					'dut': 'nl',
					'nld': 'nl',
					'eng': 'en',
                                        'ell': 'el',
                                        'gre': 'el',
                                        'est': 'et',
					'fin': 'fi',
					'fra': 'fr',
					'hrv': 'hr',
					'hun': 'hu',
					'ita': 'it',
					'jpn': 'jp',
					'kor': 'ko',
					//'lit': 'lt',
					'nor': 'no',
					'pol': 'pl',
					'por': 'pt',
					'rum': 'ro',
					'rus': 'ru',
					'spa': 'es',
					'srp': 'sr',
					'swe': 'sv',
					'tha': 'th',
					'tur': 'tr',
					'vie': 'vi',
					'bul': 'bg'
				};
				var languageCode = loadGetParam( 'languageCode' );
				if( !languageCode ) { languageCode = 'ENG'; }
				var lookupLangCode = languageCode.toLowerCase();
				var langCode = typeof supportedLanguages[lookupLangCode] === 'undefined' ? 'eng' : lookupLangCode;
				var language = supportedLanguages[langCode];

				var originalGameCode = loadGetParam( 'gameCode' );

				var isFlowGame = (originalGameCode.substring(0, 6) === 'GAM_F_' || originalGameCode.substring(0, 7) === 'GAMM_F_');
				originalGameCode = originalGameCode
					.replace(/GAM_F_/, "GAM_")
					.replace(/GAMM_F_/, "GAMM_");
				var gameCodeData = parseGameCode(originalGameCode);
				var gameAssetSource = gameCodeData.basicGameCode;
				var rtpVariation = gameCodeData.rtp;



				var afterJQueryLoading = function() {
					$.when($.ajax({url: "config.xml"}),$.ajax({url: "app_version"})).done(function(cfg, appVersion) {
						// common stuff
						var token = loadGetParam( 'token' );
						var baseURL = $(cfg[0]).find("media_url").text();
						baseURL = baseURL.replace(/{CLIENT_VERSION}/,appVersion[2].responseText);

						var serverURLs = [];
						var servers = $(cfg[0]).find("server_url")
							var wloc = window.location;
							for(s=0; s<servers.length; s++) {
								var srvurl = servers[s].textContent;
								if (srvurl.startsWith("_SAMEHOST_")) {
									var newUrl = wloc.host.split('.');
									newUrl[0] = 'client';
									newUrl = newUrl.join('.');
									srvurl = srvurl.replace("_SAMEHOST_", newUrl);
									srvurl = 'wss://' + srvurl;
								}
								serverURLs.push(srvurl);
							}
							// some fallback values for developing
							if( serverURLs.length == 0 ) serverURLs = [ 'ws://localhost:8080/ws' ];


						var playMode = loadGetParam( 'play_mode' );
						if ( !playMode ) { playMode = loadGetParam( 'playMode' ); }
						if ( !playMode ) { playMode = loadGetParam( 'playmode' ); }
						if (playMode === 'REAL') {
							playMode = 1;
						} else if (playMode === 'FUN') {
							playMode = 2;
						} else {
							playMode = playMode ? parseInt( playMode ) : 1;
						}

						var lossLimitOptionalEntry = $(cfg[0]).find("autospin_advanced_options_isLossLimitOptional");
						var autospinShowStopReasonEntry = $(cfg[0]).find("autospin_advanced_options_showStopReasonPopup");
						var launchParams = {
							serverURLs:				serverURLs,
							cageCode:				$(cfg[0]).find("cage_short_name").text(),
							language:				language,
							languageCode:			languageCode,
							token:					token,
							playMode:				playMode,
							closeHandler: 			closeHandler,
							gameEventHandler:		gameEventHandler,
							autospinForbidden:		parseInt( $(cfg[0]).find("supress_autospin").text() ),
							autospinScenario:		parseInt( $(cfg[0]).find("autospin_scenario").text() ),
							autospinAdvancedOptionsLossLimitOptional:	lossLimitOptionalEntry.length ? parseInt( lossLimitOptionalEntry.text() ) : true,
							autospinAdvancedOptionsShowStopReasonPopup:	autospinShowStopReasonEntry.length ? parseInt( autospinShowStopReasonEntry.text() ) : false,
							idleLogoutTime:			parseInt( $(cfg[0]).find("idle_logout").text() ),
							suppressRealityCheck:	parseInt( $(cfg[0]).find("disable_reality_check").text() ),
							removeCloseButton: 		parseInt( $(cfg[0]).find("hide_close_button").text() ) || (loadGetParam('lobbyUrl') === "OFF" ? 1 : 0),
							removeRulesButton: 		parseInt( $(cfg[0]).find("disable_help").text() ),
							hasFullscreen: 			!parseInt( $(cfg[0]).find("disable_full_screen").text() ),
							isSoundSettable: 		!parseInt( $(cfg[0]).find("disable_sound_controls").text() ),
							showClock: 				parseInt( $(cfg[0]).find("display_clock").text() ),
							showGameRoundId:				true,
							alwaysShowVersionAndSlotname:	false,
							alwaysShowSlotname:				false,
							hasBetChangeLoops:		!parseInt( $(cfg[0]).find("disable_bet_change_loops").text() ),
							limitBetsToAvailableBalance:	true,
//							hasInGameHistory:				gameHistoryType === 'ingame',	// needed until Flow games can have external history handlers
							hasInGameHistory:				true,
							gameHistoryType: 				gameHistoryType,
							gameHistoryHandler:				externalGameHistoryHandler,
							openHelpInGame:			true,
							unfinishedGamesResolvingType:   unfinishedGamesResolvingType,
							unfinishedGamesResolvingInterval:       unfinishedGamesResolvingInterval,
							mathInfoDisplayType: slotInfoDisplayType,
							slotInfoDisplayType: slotInfoDisplayType,
							showMathInfos: showMathInfos,
							showElapsedTime: showElapsedTime,
							showNetProfit: showNetProfit,
							removeCasinoFromGameName:               false,
							enablePaytableWithSingleButtonClick: true,
							enableHelppagesWithSingleButtonClick: true,
							rtpVariation: rtpVariation
						};
						if( isReconnect ) { launchParams.isReconnect = isReconnect; }
						if( reconnectCount ) { launchParams.reconnectCount = reconnectCount; }
						var currencyNoDecimalsEntry = $(cfg[0]).find("currency_format_no_decimals");
						if( currencyNoDecimalsEntry.length ) { launchParams.currencyNoDecimals = parseInt( currencyNoDecimalsEntry.text() ); }
						var currencyKiloDisplayEntry = $(cfg[0]).find("currency_format_kilo_display");
						if( currencyKiloDisplayEntry.length ) { launchParams.currencyKiloDisplay = parseInt( currencyKiloDisplayEntry.text() ); }



						if( isFlowGame ) {
							// flow stuff
							var baseDirectory = baseURL.replace(/{GAME_BY_CODE}/,'');
							while( baseDirectory.substr(-1) === '/' ) { baseDirectory = baseDirectory.slice(0,-1); }
							baseURL = baseURL.replace(/{GAME_BY_CODE}/,gameAssetSource);
							if( baseURL.substr( -1 ) != '/' ) { baseURL += '/'; }

							launchParams['baseURL'] = baseURL;
							launchParams['appVersion'] = appVersion[0];
							launchParams['directory'] = baseDirectory;
							launchParams['gameCodeForServer'] = originalGameCode;
							launchParams['gameCode'] = gameAssetSource;
							launchParams['showBetPerLine'] = true;

                                                        launchParams['openHelpHandler'] = function( helpVersion ) {
                                                                var helpPageURL = baseURL + 'html/help_extern/' + helpVersion + "/index_" + language + '.html';
                                                                if( launchParams['openHelpInGame'] ) {
                                                                        // url: helppage url
                                                                        return {
                                                                                url : helpPageURL
                                                                        };
                                                                }
                                                                else {
									window.open(helpPageURL, gameAssetSource + "_help", "scrollbars=1,height=600,width=760");
                                                                }
                                                                return false;
                                                        };


							$.getScript( baseURL+'loader.js' ).done( function() {
								gameRef = load( launchParams );
							} )

						}
						else {
							// c1.0 stuff
							var username = loadGetParam( 'username' );
							if( !username ) { username = ''; }

							baseURL = baseURL.replace(/{GAME_BY_CODE}/,gameAssetSource);
							if( baseURL.substr( -1 ) != '/' ) { baseURL += '/'; }

							launchParams['baseURL'] = baseURL;
							launchParams['username'] = username;
							launchParams['gameCode'] = originalGameCode;
							launchParams['openHelpHandler'] = function(pct, container) {
								var url = baseURL + "help/" + pct + "/index_" + language + '.html';

								if( launchParams['openHelpInGame'] ) {
									$(container).find('iframe').attr('src',url);
									$(container).show();
								} else {
									window.open(url, gameAssetSource + "_help", "scrollbars=1,height=600,width=760");
								}
								return false;
							};

							$.getScript( baseURL+'loader.js' ).done( function() {

								gameRef = launchGame( launchParams );
							} );

						}
					});


				};
				// load jquery
				var script = document.createElement("SCRIPT");
				script.src = isFlowGame ? 'jquery3.js' : 'jquery1.js';
				script.type = 'text/javascript';
				script.onload = afterJQueryLoading;
				document.getElementsByTagName("head")[0].appendChild(script);

				var keepalive = (loadGetParam('keepalive') ? ~~(loadGetParam('keepalive')) : 0);
				var keepaliveUrl = loadGetParam('keepaliveUrl');

				if (keepalive > 0) {
					window.setInterval(function() {
						$.ajax({
					            url: keepaliveUrl,
					            method: 'GET',
					            xhrFields: {
					                withCredentials: true
					            }
					        }).done(function( data ) { console.log("KeepAlive:", data); });
					}, keepalive);
				}

			})();
		</script>
	</head>
	<body>
	</body>
</html>
```
### evolution bog
Evolution live casino on staging environment is limited to work only from one ip. Please use this http proxy to access it.
IP 89.212.119.144
Port 33821
username: test (you will be prompted for u/p when you connect the first time)
password: 9ToS#2

How to enable it in Google Chrome: https://support.google.com/chrome/answer/96815?hl=en

Also Flash Player uses system proxy settings.So you have to change system proxy settings by navigating to : Control Panel⇒ Network and Internet Connections ⇒ Internet Options ⇒ Connections ⇒ Select your profile and change proxy settings then click ok.

If it still does not work, then try running the EVO lobby without Flash in HTML5 mode using Chrome mobile emulator.
Q: Microgaming games are not running on development environment.

If Microgaming games are not working please add following lines to your host file:
41.223.121.106  WebServer8
41.223.121.106  WebServer8.bluemesa.mgsops.net
41.223.121.105 WebServer4
41.223.121.105 WebServer4.bluemesa.mgsops.net


## Tvbet.tv / Xpressgaming / Goldenrace
```json
class XpressLoader {
    constructor() {
        this.serviceBridge = new XMLHttpRequest();
        this.isInitialized = false;
        this.testDetails = null;
        this.initialize();
    }

    async createContainers() {
        this.container = document.createElement("div");
        this.container.id = "xpressContainer";
        document.body.appendChild(this.container);

        this.loadingContainer = document.createElement("div");
        this.loadingContainer.id = "xpressLoading";
        this.container.appendChild(this.loadingContainer);
    }

    callService(action, callbackFn, data) {
        this.serviceBridge.open("POST", window.location.href + "&action=" + action + "&w=" + window.innerWidth, true);
        this.serviceBridge.timeout = 11000;
        this.serviceBridge.setRequestHeader('Content-Type', 'application/json');
        if (typeof data === "undefined") {
            this.serviceBridge.send();
        } else {
            this.serviceBridge.send(data);
        }

        this.serviceBridge.onload = (function () {
            if (this.serviceBridge.status != 200) {
                return this.showErrorMessage("request", "911");
            } else {
                let data = null;
                try {
                    data = JSON.parse(this.serviceBridge.response);
                } catch (e) {
                    return this.showErrorMessage("request", "912");
                }
                if (data === null) {
                    return this.showErrorMessage("request", "913");
                }

                if (data.Status === false) {
                    if (data.Code === 303) {
                        // Timeout case.
                        return this.showErrorMessage("request", "303");
                    }
                    return this.showErrorMessage("request", "914");
                }

                if (typeof data.Action === "undefined") {
                    return this.showErrorMessage("request", "915");
                }
                if (data.Status === true) {
                    if (data.Action === "redirectPlayer") {
                        window.location.href = data.Value;
                        return;
                    }
                    if (data.Message === "customSessionError") {
                        // Session error
                        return this.showErrorMessage("request", "customSessionError");
                    }
                    if (data.Message === "sessionError") {
                        // Session error
                        return this.showErrorMessage("request", "session");
                    }
                }
                return callbackFn(data);
            }
        }).bind(this);
        this.serviceBridge.onprogress = function (event) {
        };
        this.serviceBridge.onerror = (function () {
            this.showErrorMessage("timeout", "910");
        }).bind(this);
    }

    showErrorMessage(type, code) {
        this.loadingContainer.remove();
        this.textMessageContainer = document.createElement("div");
        this.textMessageContainer.id = "xpressInformation";
        if (type === "critical") {
            this.textMessageContainer.innerHTML = "<h2>Loading Error</h2><p>Please contact support</p><p>Code: #" + code + "</p>";
        } else {
            if (code === "session") {
                this.textMessageContainer.innerHTML = "<h2>Your session timed out</h2><p>Please open game again or login.</p>";
                //this.textMessageContainer.innerHTML += "<p><small><i>Don't worry! we saved your latest actions. When you come back, lobby will start where you left from.</i></small></p>";
            } else if (code === "customSessionError") {
                this.textMessageContainer.innerHTML = "<h2>Your session timed out</h2><p>Please open game again or login.</p>";
            } else {
                this.textMessageContainer.innerHTML = "<h2>Loading Error</h2><p>Please try again later</p><p>Code: #" + code + "</p>";
            }

        }

        this.container.appendChild(this.textMessageContainer);
    }

    injectLoader(objectData) {
        for (var place in objectData.Head) {
            if (objectData.Head[place].type === "jsfile") {
                var dat = place;
                var script = document.createElement('script');
                script.onload = (function () {
                    this.loadingContainer.remove();
                    eval(objectData.Head[dat].onload);
                    this.isInitialized = true;
                    setInterval((function () {
                        this.sendRootMessage({action: "game.get.clientsize"});
                        this.sendRootMessage({action: "game.get.clientrect"});
                        this.sendRootMessage({action: "game.get.clientheight"});
                        this.sendRootMessage({action: "game.get.innerheight"});
                        var frame = document.getElementById("tvbet-iframe");
                        if (frame != null) {
                            var height = parseInt(frame.style.height.replace("px", ""));
                            height += 60;
                            this.sendRootMessage({action: "game.resize.height", value: height});
                        }

                        this.checkScrollSystem()
                    }).bind(this), 1110);
                }).bind(this);
                script.src = objectData.Head[dat].src;
                script.id = objectData.Head[dat].id;
                // script.crossOrigin = 'anonymous';
                document.head.appendChild(script);
            } else if (objectData.Head[place].type === "css") {
                var node = document.createElement('style');
                node.innerHTML = objectData.Head[place].value;
                document.head.appendChild(node);
            }
        }
    }

    sendRootMessage(message) {
        // Todo; * definition;
        parent.postMessage(message, "*");
    }

    bindEvent(element, eventName, eventHandler) {
        if (element.addEventListener) {
            element.addEventListener(eventName, eventHandler, false);
        } else if (element.attachEvent) {
            element.attachEvent('on' + eventName, eventHandler);
        }
    }


    checkScrollSystem() {
        if (typeof window.clientrect === "undefined") {
            return;
        }
        var iframe = document.getElementsByTagName("iframe");
        if (iframe == null) {
            return;
        }
        var boundingRect = window.clientrect;
        var boundingRectTop = Math.round(boundingRect.top);
        var bottom = boundingRectTop + window.offsetheight;
        var scrollTop = 0;
        if (boundingRectTop >= 0) {
            scrollTop = 0
        } else {
            scrollTop = Math.abs(boundingRectTop)
        }
        var scrollHeight;
        if (scrollTop + window.offsetheight < window.innerheight) {
            scrollHeight = window.offsetheight
        } else {
            scrollHeight = window.innerheight;
            if (boundingRectTop > 0) {
                scrollHeight -= boundingRectTop
            }
            if (scrollHeight > bottom) {
                var bottomDifferenced = scrollHeight - bottom;
                scrollHeight -= bottomDifferenced
            }
        }
        var message = {
            scroll: {
                top: scrollTop,
                height: scrollHeight
            }
        };

        iframe[0].contentWindow.postMessage(message, iframe[0].src);
    }

    getParameterByName(name, url) {
        if (!url) url = window.location.href;
        name = name.replace(/[\[\]]/g, '\\$&');
        var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, ' '));
    }


    initialize() {
        this.createContainers();
        this.callService("init", (function (response) {
            switch (response.Action) {
                case "inject":
                    this.injectLoader(response);
                    break;
                case "iframe":
                    window.location.href = response.Head;
                    break;
            }

            //debug screen
            let currentUrl = window.location.href;

            if (typeof response.test !== 'undefined' && response.test === true && (currentUrl.includes("staging-hub") || currentUrl.includes("zafi"))) {
                this.loadTestButton(response);
            }
        }).bind(this));

        this.bindEvent(window, 'message', (function (rootEvent) {
            if (typeof rootEvent.data === "undefined") {
                return;
            }
            if (typeof rootEvent.data._type !== "undefined") {
                switch (rootEvent.data._type) {
                    case "ucip.balancerefresh.w2gRefreshBalanceCommand":
                        if (typeof window.game !== "undefined") {
                            window.game.service.sendPing();
                        }
                        break;
                }
            }
            if (typeof rootEvent.data.action === "undefined") {
                switch (rootEvent.data) {
                    case "game.goto.history":
                        if (typeof window.customGameHistoryManager !== "undefined") {
                            return window.customGameHistoryManager();
                        }
                        this.sendRootMessage({action: "game.goto.history", value: window.gameHistoryUrl});
                        break;
                    case "game.goto.home":
                        if (typeof window.customHomeUrlManager !== "undefined") {
                            return window.customHomeUrlManager();
                        }
                        this.sendRootMessage({action: "game.goto.home"});
                        break;
                    case "game.goto.cashier":
                        if (typeof window.customCashierUrlManager !== "undefined") {
                            return window.customCashierUrlManager();
                        }
                        this.sendRootMessage({action: "game.goto.cashier"});
                        break;
                    default:
                        try {
                            var jsonData = JSON.parse(rootEvent.data);
                        } catch (e) {
                            return false;
                        }

                        if (typeof jsonData.resizeBody === "undefined") {
                            return;
                        }

                        this.sendRootMessage({action: "game.resize.height", value: jsonData.resizeBody});
                }
                return;
            }
            switch (rootEvent.data.action) {
                case "game.status":
                    // Todo; game status changer.
                    break;
                case "game.balance":
                    if (typeof window.game !== "undefined") {
                        window.game.service.sendPing();
                    }
                    break;
                case "game.clientrect":
                    window.clientrect = rootEvent.data.value;
                    break;
                case "game.clientheight":
                    window.offsetheight = rootEvent.data.value;
                    break;
                case "game.innerheight":
                    window.innerheight = rootEvent.data.value;
                    break;
                case "game.clientsize":
                    window.innerheight = rootEvent.data.innerheight;
                    window.clientrect = rootEvent.data.clientrect;
                    window.offsetheight = rootEvent.data.offsetheight;
                    break;
                case "game.goldenrace.navigate":
                    if (typeof window.grLoader !== "undefined") {
                        window.grLoader.navigate(rootEvent.data.value);
                    }
                    break;
                case "game.goldenrace.betslipSetSystemBetStake":
                    if (typeof window.grLoader !== "undefined") {
                        const {grouping , stake} = rootEvent.data.value;
                        window.grLoader.betslipSetSystemBetStake(grouping , stake);
                    }
                    break;
                case "game.goldenrace.betslipRemoveBet":
                    if (typeof window.grLoader !== "undefined") {
                        const {playlistId , eventId , oddId , betParam = ''} = rootEvent.data.value;
                        window.grLoader.betslipRemoveBet(playlistId , eventId , oddId , betParam);
                    }
                    break;
                case "game.goldenrace.ticket.scan":
                    console.log("game.goldenrace.ticket.scan",rootEvent.data.value);
                    if (typeof window.grLoader !== "undefined") {
                        const {ticketId} = rootEvent.data.value;
                        window.grLoader.ticketsStatusChecked$request({ xs: Date.now(), ticketId:ticketId, showTicketChecked: true });
                    }
                    break;
                case "game.goldenrace.betslipSendTicket":
                    if (typeof window.grLoader !== "undefined") {
                        window.grLoader.betslipSendTicket(rootEvent.data.value);
                    }
                    break;
                case "game.goldenrace.openBetHistory":
                    if (typeof window.grLoader !== "undefined") {
                        switch (this.getParameterByName("clientPlatform")) {
                            case "mobile":
                                window.grLoader.clickOpenBetHistory();
                                break;
                            case "desktop":
                            default:
                                window.grLoader.navigate('/bet-history');
                                break;

                        }
                    }
                    break;
            }
        }).bind(this));

        setTimeout(() => this.initializeTvbet(),2000);
    }

    initializeTvbet = function() {
        var frame = document.getElementById("tvbet-iframe");
        if (frame == null) {
            return;
        }
        // this.initializeTvbetNext();
        window.addEventListener('message', (e) => this.eventListenerTvbet(e), false);
    }
    eventListenerTvbet = function(e) {
        if (typeof e.data !== "undefined"  &&  typeof e.data === "string"  ) {
            try {
                var dataObj = JSON.parse( e.data );
            } catch (ex) {
                var dataObj = false;
            }
            if( typeof dataObj === "object"  &&  dataObj !== null  &&  typeof dataObj.tvbet_balance !== "undefined" ){
                console.warn("BALANCE : ", dataObj.tvbet_balance);
                this.sendRootMessage({action: "game.balance.changed", value: dataObj.tvbet_balance});
            }
        }
    }

    loadTestButton = function (details) {
        console.log("test method");
        console.log(details);
        document.loader.testDetails = details;
        document.body.innerHTML += "<div id='stagingTestButton'><button type='button' onclick='document.loader.clickTestButton()' id='loadTestModal'>?</button> </div> "
        document.body.innerHTML += "<div id='stagingTestModal' class='hideTestModal' data-sm-init='true'>" +
            "<button type='button' id='walletTestButton' onclick='document.loader.runWalletTest()'>Run Wallet Test</button>" +
            "<span class='details'> SiteId : " + details.site_id + " | PlayerId: " + details.player_id + " | Init Time : " + details.init_time + " seconds.</span>" +
            " </div>";
        document.getElementById('xpressLoading').remove();
    }

    clickTestButton = function () {
        let modalElem = document.getElementById('stagingTestModal');
        modalElem.classList.toggle('hideTestModal');
    }

    runWalletTest = function () {
        let testUrl = "https://wallet.staging-hub.xpressgaming.net/wallet-tester?siteId=" + document.loader.testDetails.site_id + "&token=" + document.loader.testDetails.session_token + "&game=" + document.loader.testDetails.game_id;

        var r = confirm("Are you sure? This will start a wallet test for this client. If you are not sure this is a staging site, please consult dev.");
        if (r == true) {
            // document.location.href = testUrl;
            console.log("###XPRESS-START##");
            window.open(testUrl);
            console.log("wallet test link :");
            console.log(testUrl);
            console.log("###XPRESS-END##");
            let modalElem = document.getElementById('stagingTestModal');
            modalElem.classList.toggle('hideTestModal');

        } else {
            console.log("Wallet Test Canceled");
        }

    }

}

window.onload = function (e) {
    document.loader = new XpressLoader();
};


class XpressLoader {
    constructor() {
        this.serviceBridge = new XMLHttpRequest();
        this.isInitialized = false;
        this.testDetails = null;
        this.initialize();
    }

    async createContainers() {
        this.container = document.createElement("div");
        this.container.id = "xpressContainer";
        document.body.appendChild(this.container);

        this.loadingContainer = document.createElement("div");
        this.loadingContainer.id = "xpressLoading";
        this.container.appendChild(this.loadingContainer);
    }

    callService(action, callbackFn, data) {
        this.serviceBridge.open("POST", window.location.href + "&action=" + action + "&w=" + window.innerWidth, true);
        this.serviceBridge.timeout =
        } else {
            this.serviceBridge.send(data);
        }

        this.serviceBridge.onload = (function () {
            if (this.serviceBridge.status != 200) {
                return this.showErrorMessage("request", "911");
            } else {
                let data = null;
                try {
                    data = JSON.parse(this.serviceBridge.response);
                } catch (e) {
                    return this.showErrorMessage("request", "912");
                }
                if (data === null) {
                    return this.showErrorMessage("request", "913");
                }

                if (data.Status === false) {
                    if (data.Code === 303) {
                        // Timeout case.
                        return this.showErrorMessage("request", "303");
                    }
                    return this.showErrorMessage("request", "914");
                }

                if (typeof data.Action === "undefined") {
                    return this.showErrorMessage("request", "915");
                }
                if (data.Status === true) {
                    if (data.Action === "redirectPlayer") {
                        window.location.href = data.Value;
                        return;
                    }
                    if (data.Message === "customSessionError") {
                        // Session error
                        return this.showErrorMessage("request", "customSessionError");
                    }
                    if (data.Message === "sessionError") {
                        // Session error
                        return this.showErrorMessage("request", "session");
                    }
                }
                return callbackFn(data);
            }
        }).bind(this);
        this.serviceBridge.onprogress = function (event) {
        };
        this.serviceBridge.onerror = (function () {
            this.showErrorMessage("timeout", "910");
        }).bind(this);
    }

    showErrorMessage(type, code) {
        this.loadingContainer.remove();
        this.textMessageContainer = document.createElement("div");
        this.textMessageContainer.id = "xpressInformation";
        if (type === "critical") {
            this.textMessageContainer.innerHTML = "<h2>Loading Error</h2><p>Please contact support</p><p>Code: #" + code + "</p>";
        } else {
            if (code === "session") {
                this.textMessageContainer.innerHTML = "<h2>Your session timed out</h2><p>Please open game again or login.</p>";
                //this.textMessageContainer.innerHTML += "<p><small><i>Don't worry! we saved your latest actions. When you come back, lobby will start where you left from.</i></small></p>";
            } else if (code === "customSessionError") {
                this.textMessageContainer.innerHTML = "<h2>Your session timed out</h2><p>Please open game again or login.</p>";
            } else {
                this.textMessageContainer.innerHTML = "<h2>Loading Error</h2><p>Please try again later</p><p>Code: #" + code + "</p>";
            }

        }

        this.container.appendChild(this.textMessageContainer);
    }

    injectLoader(objectData) {
        for (var place in objectData.Head) {
            if (objectData.Head[place].type === "jsfile") {
                var dat = place;
                var script = document.createElement('script');
                script.onload = (function () {
                    this.loadingContainer.remove();
                    eval(objectData.Head[dat].onload);
                    this.isInitialized = true;
                    setInterval((function () {
                        this.sendRootMessage({action: "game.get.clientsize"});
                        this.sendRootMessage({action: "game.get.clientrect"});
                        this.sendRootMessage({action: "game.get.clientheight"});
                        this.sendRootMessage({action: "game.get.innerheight"});
                        var frame = document.getElementById("tvbet-iframe");
                        if (frame != null) {
                            var height = parseInt(frame.style.height.replace("px", ""));
                            height += 60;
                            this.sendRootMessage({action: "game.resize.height", value: height});
                        }

                        this.checkScrollSystem()
                    }).bind(this), 1110);
                }).bind(this);
                script.src = objectData.Head[dat].src;
                script.id = objectData.Head[dat].id;
                // script.crossOrigin = 'anonymous';
                document.head.appendChild(script);
            } else if (objectData.Head[place].type === "css") {
                var node = document.createElement('style');
                node.innerHTML = objectData.Head[place].value;
                document.head.appendChild(node);
            }
        }
    }

    sendRootMessage(message) {
        // Todo; * definition;
        parent.postMessage(message, "*");
    }

    bindEvent(element, eventName, eventHandler) {
        if (element.addEventListener) {
            element.addEventListener(eventName, eventHandler, false);
        } else if (element.attachEvent) {
            element.attachEvent('on' + eventName, eventHandler);
        }
    }


    checkScrollSystem() {
        if (typeof window.clientrect === "undefined") {
            return;
        }
        var iframe = document.getElementsByTagName("iframe");
        if (iframe == null) {
            return;
        }
        var boundingRect = window.clientrect;
        var boundingRectTop = Math.round(boundingRect.top);
        var bottom = boundingRectTop + window.offsetheight;
        var scrollTop = 0;
        if (boundingRectTop >= 0) {
            scrollTop = 0
        } else {
            scrollTop = Math.abs(boundingRectTop)
        }
        var scrollHeight;
        if (scrollTop + window.offsetheight < window.innerheight) {
            scrollHeight = window.offsetheight
        } else {
            scrollHeight = window.innerheight;
            if (boundingRectTop > 0) {
                scrollHeight -= boundingRectTop
            }
            if (scrollHeight > bottom) {
                var bottomDifferenced = scrollHeight - bottom;
                scrollHeight -= bottomDifferenced
            }
        }
        var message = {
            scroll: {
                top: scrollTop,
                height: scrollHeight
            }
        };

        iframe[0].contentWindow.postMessage(message, iframe[0].src);
    }

    getParameterByName(name, url) {
        if (!url) url = window.location.href;
        name = name.replace(/[\[\]]/g, '\\$&');
        var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, ' '));
    }


    initialize() {
        this.createContainers();
        this.callService("init", (function (response) {
            switch (response.Action) {
                case "inject":
                    this.injectLoader(response);
                    break;
                case "iframe":
                    window.location.href = response.Head;
                    break;
            }

            //debug screen
            let currentUrl = window.location.href;

            if (typeof response.test !== 'undefined' && response.test === true && (currentUrl.includes("staging-hub") || currentUrl.includes("zafi"))) {
                this.loadTestButton(response);
            }
        }).bind(this));

        this.bindEvent(window, 'message', (function (rootEvent) {
            if (typeof rootEvent.data === "undefined") {
                return;
            }
            if (typeof rootEvent.data._type !== "undefined") {
                switch (rootEvent.data._type) {
                    case "ucip.balancerefresh.w2gRefreshBalanceCommand":
                        if (typeof window.game !== "undefined") {
                            window.game.service.sendPing();
                        }
                        break;
                }
            }
            if (typeof rootEvent.data.action === "undefined") {
                switch (rootEvent.data) {
                    case "game.goto.history":
                        if (typeof window.customGameHistoryManager !== "undefined") {
                            return window.customGameHistoryManager();
                        }
                        this.sendRootMessage({action: "game.goto.history", value: window.gameHistoryUrl});
                        break;
                    case "game.goto.home":
                        if (typeof window.customHomeUrlManager !== "undefined") {
                            return window.customHomeUrlManager();
                        }
                        this.sendRootMessage({action: "game.goto.home"});
                        break;
                    case "game.goto.cashier":
                        if (typeof window.customCashierUrlManager !== "undefined") {
                            return window.customCashierUrlManager();
                        }
                        this.sendRootMessage({action: "game.goto.cashier"});
                        break;
                    default:
                        try {
                            var jsonData = JSON.parse(rootEvent.data);
                        } catch (e) {
                            return false;
                        }

                        if (typeof jsonData.resizeBody === "undefined") {
                            return;
                        }

                        this.sendRootMessage({action: "game.resize.height", value: jsonData.resizeBody});
                }
                return;
            }
            switch (rootEvent.data.action) {
                case "game.status":
                    // Todo; game status changer.
                    break;
                case "game.balance":
                    if (typeof window.game !== "undefined") {
                        window.game.service.sendPing();
                    }
                    break;
                case "game.clientrect":
                    window.clientrect = rootEvent.data.value;
                    break;
                case "game.clientheight":
                    window.offsetheight = rootEvent.data.value;
                    break;
                case "game.innerheight":
                    window.innerheight = rootEvent.data.value;
                    break;
                case "game.clientsize":
                    window.innerheight = rootEvent.data.innerheight;
                    window.clientrect = rootEvent.data.clientrect;
                    window.offsetheight = rootEvent.data.offsetheight;
                    break;
                case "game.goldenrace.navigate":
                    if (typeof window.grLoader !== "undefined") {
                        window.grLoader.navigate(rootEvent.data.value);
                    }
                    break;
                case "game.goldenrace.betslipSetSystemBetStake":
                    if (typeof window.grLoader !== "undefined") {
                        const {grouping , stake} = rootEvent.data.value;
                        window.grLoader.betslipSetSystemBetStake(grouping , stake);
                    }
                    break;
                case "game.goldenrace.betslipRemoveBet":
                    if (typeof window.grLoader !== "undefined") {
                        const {playlistId , eventId , oddId , betParam = ''} = rootEvent.data.value;
                        window.grLoader.betslipRemoveBet(playlistId , eventId , oddId , betParam);
                    }
                    break;
                case "game.goldenrace.ticket.scan":
                    console.log("game.goldenrace.ticket.scan",rootEvent.data.value);
                    if (typeof window.grLoader !== "undefined") {
                        const {ticketId} = rootEvent.data.value;
                        window.grLoader.ticketsStatusChecked$request({ xs: Date.now(), ticketId:ticketId, showTicketChecked: true });
                    }
                    break;
                case "game.goldenrace.betslipSendTicket":
                    if (typeof window.grLoader !== "undefined") {
                        window.grLoader.betslipSendTicket(rootEvent.data.value);
                    }
                    break;
                case "game.goldenrace.openBetHistory":
                    if (typeof window.grLoader !== "undefined") {
                        switch (this.getParameterByName("clientPlatform")) {
                            case "mobile":
                                window.grLoader.clickOpenBetHistory();
                                break;
                            case "desktop":
                            default:
                                window.grLoader.navigate('/bet-history');
                                break;

                        }
                    }
                    break;
            }
        }).bind(this));

        setTimeout(() => this.initializeTvbet(),2000);
    }

    initializeTvbet = function() {
        var frame = document.getElementById("tvbet-iframe");
        if (frame == null) {
            return;
        }
        // this.initializeTvbetNext();
        window.addEventListener('message', (e) => this.eventListenerTvbet(e), false);
    }
    eventListenerTvbet = function(e) {
        if (typeof e.data !== "undefined"  &&  typeof e.data === "string"  ) {
            try {
                var dataObj = JSON.parse( e.data );
            } catch (ex) {
                var dataObj = false;
            }
            if( typeof dataObj === "object"  &&  dataObj !== null  &&  typeof dataObj.tvbet_balance !== "undefined" ){
                console.warn("BALANCE : ", dataObj.tvbet_balance);
                this.sendRootMessage({action: "game.balance.changed", value: dataObj.tvbet_balance});
            }
        }
    }

    loadTestButton = function (details) {
        console.log("test method");
        console.log(details);
        document.loader.testDetails = details;
        document.body.innerHTML += "<div id='stagingTestButton'><button type='button' onclick='document.loader.clickTestButton()' id='loadTestModal'>?</button> </div> "
        document.body.innerHTML += "<div id='stagingTestModal' class='hideTestModal' data-sm-init='true'>" +
            "<button type='button' id='walletTestButton' onclick='document.loader.runWalletTest()'>Run Wallet Test</button>" +
            "<span class='details'> SiteId : " + details.site_id + " | PlayerId: " + details.player_id + " | Init Time : " + details.init_time + " seconds.</span>" +
            " </div>";
        document.getElementById('xpressLoading').remove();
    }

    clickTestButton = function () {
        let modalElem = document.getElementById('stagingTestModal');
        modalElem.classList.toggle('hideTestModal');
    }

    runWalletTest = function () {
        let testUrl = "https://wallet.staging-hub.xpressgaming.net/wallet-tester?siteId=" + document.loader.testDetails.site_id + "&token=" + document.loader.testDetails.session_token + "&game=" + document.loader.testDetails.game_id;

        var r = confirm("Are you sure? This will start a wallet test for this client. If you are not sure this is a staging site, please consult dev.");
        if (r == true) {
            // document.location.href = testUrl;
            console.log("###XPRESS-START##");
            window.open(testUrl);
            console.log("wallet test link :");
            console.log(testUrl);
            console.log("###XPRESS-END##");
            let modalElem = document.getElementById('stagingTestModal');
            modalElem.classList.toggle('hideTestModal');

        } else {
            console.log("Wallet Test Canceled");
        }

    }

}

window.onload = function (e) {
    document.loader = new XpressLoader();
};
```


```json
class XpressLoader {
    constructor() {
        this.serviceBridge = new XMLHttpRequest();
        this.isInitialized = false;
        this.testDetails = null;
        this.initialize();
    }

    async createContainers() {
        this.container = document.createElement("div");
        this.container.id = "xpressContainer";
        document.body.appendChild(this.container);

        this.loadingContainer = document.createElement("div");
        this.loadingContainer.id = "xpressLoading";
        this.container.appendChild(this.loadingContainer);
    }

    callService(action, callbackFn, data) {
        this.serviceBridge.open("POST", window.location.href + "&action=" + action + "&w=" + window.innerWidth, true);
        this.serviceBridge.timeout = 11000;
        this.serviceBridge.setRequestHeader('Content-Type', 'application/json');
        if (typeof data === "undefined") {
            this.serviceBridge.send();
        } else {
            this.serviceBridge.send(data);
        }

        this.serviceBridge.onload = (function () {
            if (this.serviceBridge.status != 200) {
                return this.showErrorMessage("request", "911");
            } else {
                let data = null;
                try {
                    data = JSON.parse(this.serviceBridge.response);
                } catch (e) {
                    return this.showErrorMessage("request", "912");
                }
                if (data === null) {
                    return this.showErrorMessage("request", "913");
                }

                if (data.Status === false) {
                    if (data.Code === 303) {
                        // Timeout case.
                        return this.showErrorMessage("request", "303");
                    }
                    return this.showErrorMessage("request", "914");
                }

                if (typeof data.Action === "undefined") {
                    return this.showErrorMessage("request", "915");
                }
                if (data.Status === true) {
                    if (data.Action === "redirectPlayer") {
                        window.location.href = data.Value;
                        return;
                    }
                    if (data.Message === "customSessionError") {
                        // Session error
                        return this.showErrorMessage("request", "customSessionError");
                    }
                    if (data.Message === "sessionError") {
                        // Session error
                        return this.showErrorMessage("request", "session");
                    }
                }
                return callbackFn(data);
            }
        }).bind(this);
        this.serviceBridge.onprogress = function (event) {
        };
        this.serviceBridge.onerror = (function () {
            this.showErrorMessage("timeout", "910");
        }).bind(this);
    }

    showErrorMessage(type, code) {
        this.loadingContainer.remove();
        this.textMessageContainer = document.createElement("div");
        this.textMessageContainer.id = "xpressInformation";
        if (type === "critical") {
            this.textMessageContainer.innerHTML = "<h2>Loading Error</h2><p>Please contact support</p><p>Code: #" + code + "</p>";
        } else {
            if (code === "session") {
                this.textMessageContainer.innerHTML = "<h2>Your session timed out</h2><p>Please open game again or login.</p>";
                //this.textMessageContainer.innerHTML += "<p><small><i>Don't worry! we saved your latest actions. When you come back, lobby will start where you left from.</i></small></p>";
            } else if (code === "customSessionError") {
                this.textMessageContainer.innerHTML = "<h2>Your session timed out</h2><p>Please open game again or login.</p>";
            } else {
                this.textMessageContainer.innerHTML = "<h2>Loading Error</h2><p>Please try again later</p><p>Code: #" + code + "</p>";
            }

        }

        this.container.appendChild(this.textMessageContainer);
    }

    injectLoader(objectData) {
        for (var place in objectData.Head) {
            if (objectData.Head[place].type === "jsfile") {
                var dat = place;
                var script = document.createElement('script');
                script.onload = (function () {
                    this.loadingContainer.remove();
                    eval(objectData.Head[dat].onload);
                    this.isInitialized = true;
                    setInterval((function () {
                        this.sendRootMessage({action: "game.get.clientsize"});
                        this.sendRootMessage({action: "game.get.clientrect"});
                        this.sendRootMessage({action: "game.get.clientheight"});
                        this.sendRootMessage({action: "game.get.innerheight"});
                        var frame = document.getElementById("tvbet-iframe");
                        if (frame != null) {
                            var height = parseInt(frame.style.height.replace("px", ""));
                            height += 60;
                            this.sendRootMessage({action: "game.resize.height", value: height});
                        }

                        this.checkScrollSystem()
                    }).bind(this), 1110);
                }).bind(this);
                script.src = objectData.Head[dat].src;
                script.id = objectData.Head[dat].id;
                // script.crossOrigin = 'anonymous';
                document.head.appendChild(script);
            } else if (objectData.Head[place].type === "css") {
                var node = document.createElement('style');
                node.innerHTML = objectData.Head[place].value;
                document.head.appendChild(node);
            }
        }
    }

    sendRootMessage(message) {
        // Todo; * definition;
        parent.postMessage(message, "*");
    }

    bindEvent(element, eventName, eventHandler) {
        if (element.addEventListener) {
            element.addEventListener(eventName, eventHandler, false);
        } else if (element.attachEvent) {
            element.attachEvent('on' + eventName, eventHandler);
        }
    }


    checkScrollSystem() {
        if (typeof window.clientrect === "undefined") {
            return;
        }
        var iframe = document.getElementsByTagName("iframe");
        if (iframe == null) {
            return;
        }
        var boundingRect = window.clientrect;
        var boundingRectTop = Math.round(boundingRect.top);
        var bottom = boundingRectTop + window.offsetheight;
        var scrollTop = 0;
        if (boundingRectTop >= 0) {
            scrollTop = 0
        } else {
            scrollTop = Math.abs(boundingRectTop)
        }
        var scrollHeight;
        if (scrollTop + window.offsetheight < window.innerheight) {
            scrollHeight = window.offsetheight
        } else {
            scrollHeight = window.innerheight;
            if (boundingRectTop > 0) {
                scrollHeight -= boundingRectTop
            }
            if (scrollHeight > bottom) {
                var bottomDifferenced = scrollHeight - bottom;
                scrollHeight -= bottomDifferenced
            }
        }
        var message = {
            scroll: {
                top: scrollTop,
                height: scrollHeight
            }
        };

        iframe[0].contentWindow.postMessage(message, iframe[0].src);
    }

    getParameterByName(name, url) {
        if (!url) url = window.location.href;
        name = name.replace(/[\[\]]/g, '\\$&');
        var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, ' '));
    }


    initialize() {
        this.createContainers();
        this.callService("init", (function (response) {
            switch (response.Action) {
                case "inject":
                    this.injectLoader(response);
                    break;
                case "iframe":
                    window.location.href = response.Head;
                    break;
            }

            //debug screen
            let currentUrl = window.location.href;

            if (typeof response.test !== 'undefined' && response.test === true && (currentUrl.includes("staging-hub") || currentUrl.includes("zafi"))) {
                this.loadTestButton(response);
            }
        }).bind(this));

        this.bindEvent(window, 'message', (function (rootEvent) {
            if (typeof rootEvent.data === "undefined") {
                return;
            }
            if (typeof rootEvent.data._type !== "undefined") {
                switch (rootEvent.data._type) {
                    case "ucip.balancerefresh.w2gRefreshBalanceCommand":
                        if (typeof window.game !== "undefined") {
                            window.game.service.sendPing();
                        }
                        break;
                }
            }
            if (typeof rootEvent.data.action === "undefined") {
                switch (rootEvent.data) {
                    case "game.goto.history":
                        if (typeof window.customGameHistoryManager !== "undefined") {
                            return window.customGameHistoryManager();
                        }
                        this.sendRootMessage({action: "game.goto.history", value: window.gameHistoryUrl});
                        break;
                    case "game.goto.home":
                        if (typeof window.customHomeUrlManager !== "undefined") {
                            return window.customHomeUrlManager();
                        }
                        this.sendRootMessage({action: "game.goto.home"});
                        break;
                    case "game.goto.cashier":
                        if (typeof window.customCashierUrlManager !== "undefined") {
                            return window.customCashierUrlManager();
                        }
                        this.sendRootMessage({action: "game.goto.cashier"});
                        break;
                    default:
                        try {
                            var jsonData = JSON.parse(rootEvent.data);
                        } catch (e) {
                            return false;
                        }

                        if (typeof jsonData.resizeBody === "undefined") {
                            return;
                        }

                        this.sendRootMessage({action: "game.resize.height", value: jsonData.resizeBody});
                }
                return;
            }
            switch (rootEvent.data.action) {
                case "game.status":
                    // Todo; game status changer.
                    break;
                case "game.balance":
                    if (typeof window.game !== "undefined") {
                        window.game.service.sendPing();
                    }
                    break;
                case "game.clientrect":
                    window.clientrect = rootEvent.data.value;
                    break;
                case "game.clientheight":
                    window.offsetheight = rootEvent.data.value;
                    break;
                case "game.innerheight":
                    window.innerheight = rootEvent.data.value;
                    break;
                case "game.clientsize":
                    window.innerheight = rootEvent.data.innerheight;
                    window.clientrect = rootEvent.data.clientrect;
                    window.offsetheight = rootEvent.data.offsetheight;
                    break;
                case "game.goldenrace.navigate":
                    if (typeof window.grLoader !== "undefined") {
                        window.grLoader.navigate(rootEvent.data.value);
                    }
                    break;
                case "game.goldenrace.betslipSetSystemBetStake":
                    if (typeof window.grLoader !== "undefined") {
                        const {grouping , stake} = rootEvent.data.value;
                        window.grLoader.betslipSetSystemBetStake(grouping , stake);
                    }
                    break;
                case "game.goldenrace.betslipRemoveBet":
                    if (typeof window.grLoader !== "undefined") {
                        const {playlistId , eventId , oddId , betParam = ''} = rootEvent.data.value;
                        window.grLoader.betslipRemoveBet(playlistId , eventId , oddId , betParam);
                    }
                    break;
                case "game.goldenrace.ticket.scan":
                    console.log("game.goldenrace.ticket.scan",rootEvent.data.value);
                    if (typeof window.grLoader !== "undefined") {
                        const {ticketId} = rootEvent.data.value;
                        window.grLoader.ticketsStatusChecked$request({ xs: Date.now(), ticketId:ticketId, showTicketChecked: true });
                    }
                    break;
                case "game.goldenrace.betslipSendTicket":
                    if (typeof window.grLoader !== "undefined") {
                        window.grLoader.betslipSendTicket(rootEvent.data.value);
                    }
                    break;
                case "game.goldenrace.openBetHistory":
                    if (typeof window.grLoader !== "undefined") {
                        switch (this.getParameterByName("clientPlatform")) {
                            case "mobile":
                                window.grLoader.clickOpenBetHistory();
                                break;
                            case "desktop":
                            default:
                                window.grLoader.navigate('/bet-history');
                                break;

                        }
                    }
                    break;
            }
        }).bind(this));

        setTimeout(() => this.initializeTvbet(),2000);
    }

    initializeTvbet = function() {
        var frame = document.getElementById("tvbet-iframe");
        if (frame == null) {
            return;
        }
        // this.initializeTvbetNext();
        window.addEventListener('message', (e) => this.eventListenerTvbet(e), false);
    }
    eventListenerTvbet = function(e) {
        if (typeof e.data !== "undefined"  &&  typeof e.data === "string"  ) {
            try {
                var dataObj = JSON.parse( e.data );
            } catch (ex) {
                var dataObj = false;
            }
            if( typeof dataObj === "object"  &&  dataObj !== null  &&  typeof dataObj.tvbet_balance !== "undefined" ){
                console.warn("BALANCE : ", dataObj.tvbet_balance);
                this.sendRootMessage({action: "game.balance.changed", value: dataObj.tvbet_balance});
            }
        }
    }

    loadTestButton = function (details) {
        console.log("test method");
        console.log(details);
        document.loader.testDetails = details;
        document.body.innerHTML += "<div id='stagingTestButton'><button type='button' onclick='document.loader.clickTestButton()' id='loadTestModal'>?</button> </div> "
        document.body.innerHTML += "<div id='stagingTestModal' class='hideTestModal' data-sm-init='true'>" +
            "<button type='button' id='walletTestButton' onclick='document.loader.runWalletTest()'>Run Wallet Test</button>" +
            "<span class='details'> SiteId : " + details.site_id + " | PlayerId: " + details.player_id + " | Init Time : " + details.init_time + " seconds.</span>" +
            " </div>";
        document.getElementById('xpressLoading').remove();
    }

    clickTestButton = function () {
        let modalElem = document.getElementById('stagingTestModal');
        modalElem.classList.toggle('hideTestModal');
    }

    runWalletTest = function () {
        let testUrl = "https://wallet.staging-hub.xpressgaming.net/wallet-tester?siteId=" + document.loader.testDetails.site_id + "&token=" + document.loader.testDetails.session_token + "&game=" + document.loader.testDetails.game_id;

        var r = confirm("Are you sure? This will start a wallet test for this client. If you are not sure this is a staging site, please consult dev.");
        if (r == true) {
            // document.location.href = testUrl;
            console.log("###XPRESS-START##");
            window.open(testUrl);
            console.log("wallet test link :");
            console.log(testUrl);
            console.log("###XPRESS-END##");
            let modalElem = document.getElementById('stagingTestModal');
            modalElem.classList.toggle('hideTestModal');

        } else {
            console.log("Wallet Test Canceled");
        }

    }

}

window.onload = function (e) {
    document.loader = new XpressLoader();
};
```


## Favicon Redirect

.htaccess
```xml
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

```


## Snippets below are for myself to memorize and contain random snippets and stuff I came across copy pasted, it's not meant and will never be organized yet maybe can help people in right direction with making stuff.

## Quick start:
You can check .env.example and a docker-compose example below. Replace .env after running first docker command & docker-compose.yml then run **./vendor/bin/sail down** and then **./vendor/bin/sail up -d**. You may have to truncate your images, you can do so in sail itself (./vendor/bin/sail down --rmi all -v) or by running the .sh script noted below to make a complete clean on your docker system, use with "bash truncate.sh".

## Docker install
```bash
docker run --rm \
    -v "$(pwd)":/opt \
    -w /opt \
    laravelsail/php81-composer:latest \
    bash -c "laravel new example-app && cd example-app && php ./artisan sail:install --with=mariadb,redis,selenium "
```


Add to composer.json:
```json
    "repositories": [
        {
        "type": "path",
        "url": "casino"
        }
    ],
```

Add to require in composer.json:
```json
        "respins.io/casino": "*"
```

Run following commands:
```bash
    ./vendor/bin/sail composer require laravel/jetstream
    ./vendor/bin/sail artisan jetstream:install livewire
    ./vendor/bin/sail composer require laravel/telescope
    ./vendor/bin/sail migrate:fresh
    ./vendor/bin/sail npm install
    ./vendor/bin/sail npm run build
    ./vendor/bin/sail artisan package:discover
    ./vendor/bin/sail composer update -o --no-ansi --no-suggest --no-cache
    ./vendor/bin/sail vendor:publish (SELECT PACKAGE MIGRATIONS)
    ./vendor/bin/sail migrate:fresh
```

## truncate.sh
Please note that this will remove all docker containers and images, also all images/containers you may have created outside this project.

```bash
#!/bin/bash
docker info > /dev/null 2>&1

# Ensure that Docker is running...
if [ $? -ne 0 ]; then
    echo "Docker is not running."
    exit 1
fi

read -p "Are you sure you want to remove all docker images, docker containers, prune docker system & recreating main network? <y/N> " prompt
if [[ $prompt =~ [yY](es)* ]]; then
    echo "Stopping containers.."
    sleep 5
    docker stop $(docker ps -a -q)
    echo "Pruning docker containers & images.."
    sleep 5
    docker rm $(docker ps -a -q)
    echo "Removing all docker images.."
    sleep 5
    docker rmi $(docker images -a -q) -f
    echo "Pruning docker system.."
    sleep 5
    docker system prune -f
    echo "Recreating main network (ipv6 disabled).."
    sleep 5
    docker network create --ipv6=false sail
    exit 1
fi
```

## .env.example:
```
APP_NAME=Wainwright
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=https://tolars.net

LOG_CHANNEL=daily
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=mariadb
DB_PORT=3306
DB_DATABASE=wainwright_db
DB_USERNAME=goliath
DB_PASSWORD=changethispassword

BROADCAST_DRIVER=redis
CACHE_DRIVER=redis
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis
SESSION_DRIVER=database
SESSION_LIFETIME=120

MEMCACHED_HOST=memcached

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=999
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello"
MAIL_FROM_NAME=

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=999
PUSHER_SCHEME=
PUSHER_APP_CLUSTER=



VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

## docker-compose.yml (this should be generated anyway by laravel sail)
```yml
# For more information: https://laravel.com/docs/sail
version: '3'
services:
    laravel.test:
        build:
            context: ./vendor/laravel/sail/runtimes/8.1
            dockerfile: Dockerfile
            args:
                WWWGROUP: '${WWWGROUP}'
        image: sail-8.1/app
        extra_hosts:
            - 'host.docker.internal:host-gateway'
        ports:
            - '${APP_PORT:-80}:80'
            - '${VITE_PORT:-5173}:${VITE_PORT:-5173}'
        environment:
            WWWUSER: '${WWWUSER}'
            LARAVEL_SAIL: 1
            XDEBUG_MODE: '${SAIL_XDEBUG_MODE:-off}'
            XDEBUG_CONFIG: '${SAIL_XDEBUG_CONFIG:-client_host=host.docker.internal}'
        volumes:
            - '.:/var/www/html'
        networks:
            - sail
        depends_on:
            - mariadb
            - redis
            - selenium
    mariadb:
        image: 'mariadb:10'
        environment:
            MYSQL_ROOT_PASSWORD: '${DB_PASSWORD}'
            MYSQL_ROOT_HOST: "%"
            MYSQL_DATABASE: '${DB_DATABASE}'
            MYSQL_USER: '${DB_USERNAME}'
            MYSQL_PASSWORD: '${DB_PASSWORD}'
            MYSQL_ALLOW_EMPTY_PASSWORD: 'yes'
        volumes:
            - 'sail-mariadb:/var/lib/mysql'
            - './vendor/laravel/sail/database/mysql/create-testing-database.sh:/docker-entrypoint-initdb.d/10-create-testing-database.sh'
        networks:
            - sail
    redis:
        image: 'redis:alpine'
        volumes:
            - 'sail-redis:/data'
        networks:
            - sail
        healthcheck:
            test: ["CMD", "redis-cli", "ping"]
            retries: 3
            timeout: 5s
    selenium:
        image: 'selenium/standalone-chrome'
        volumes:
            - '/dev/shm:/dev/shm'
        networks:
            - sail
networks:
    sail:
        external: true
volumes:
    sail-mariadb:
        driver: local
    sail-redis:
        driver: local

```

## Evolution Gaming
as done so on evolution I've released before:

https://github.com/westreels/evv-client/tree/main/websocket

For evolution you will need either access, can use evo-test.com or you can change currencies with the above free sample, it's very straight forward, simply set the currency to a shit one compared to USD$, this way you can levy profit on big currency difference by just changing the currency-fiat symbol like from $ to € (by charging same ggr).
This way all games work, in example difference between the South Korean WON currency:
10 South Korean Won = 0.0076467483 US Dollars

That means if you pay 10% to your provider for a 10$ bet you're really only paying 10% of 0.007$ (while the south korean won is showing as USD$ to the player/operator without any changes), you can charge 10% to your own casino operator. You change the South korean won currency icon to a dollar one, if you charge the same amount or less you will still make insane mark-up.

This probably is most safe way to do without getting interruptions by using public ways, there's evo-test.com you can login using the casino ID and password test123 like:
CASINOID.evo-test.com/api/ ~~
Casino ID can be found by launching game on a genuine platform. However like said it's easier in the case of evolution with sidebets and multi-way pots to just do as above, many people do this including sites as BC.Game, Bitstarz.com and others (be it with agreement ofcourse or be it because they are owned by Evolution Gaming themselves for launder).

You can find working example, all you need is to generate JSESSIONID from that can generate EVO_SESSIONID. Just host these 2 repo's and look around:
https://github.com/westreels/evv-client
https://github.com/westreels/evv-api

Both are incredibly easy to setup, though these are not maintained at all, so at own risk. It seems below is a way for pragmaticplay live, including session generation & a 1K balance per session as freebet (though you would need to build accordingly the man in the middle websocket and so on) -- for as long it lasts, probably not long.

## Pragmatic Live
You will need to setup a websocket that send and receive info, so you look like pragmaticplaylive to player and you look like player to pragmaticplay live.
All you do is change betsizing/currencies and implement hard callbacks on balance calls (and lifting off to the casino/operator), probably best is to do a reverse "post route" in-to laravel to get balances and tie it to regular operatorcallbacks you might've extended by now.

This is what I usually do, there is few packages out there ("php-websocket" and many more probably), but i'm scrub at everything so i stick to something i "know sorta" with help of google.

Generate token on demo game that for some reason is in live_casino environment on seperate "space man demo casino". (Most likely its done on purpose tbh lol for matteogang & co):
https://prelive-gs1.pragmaticplaylive.net/api/spacemanDemo &&
After generated token u can use it to enter live games it seems from first glance, like live feed:
https://prelive-static.pragmaticplaylive.net/desktop/speedblackjack/assets/videoplayer/video/pp-livevideo.html?ws_path=wss://ws.pragmaticplaylive.net/BJ24.24&ios_path=https://ios.pragmaticplaylive.net/BJ24.24
you prolly will need to parse the table info from others, though this way seems u will have 1k$ free bet

I find it on lucky just bored and looking what to make in future or idk (spaceman one i was looking)


An example of websocket for "fiat game" (crash-like game based on exchange price) that posts/relays info direct as an api route in-to local laravel - this was a high freq websocket, so instead of connecting it to all, i could limit it by sending out batched livewire emits:
```js
const WebSocket = require('ws'), axios = require('axios'),
    { r, g, b, w, c, m, y, k } = [['r', 1], ['g', 2], ['b', 4], ['w', 7], ['c', 6], ['m', 5], ['y', 3], ['k', 0]].reduce((cols, col) => ({ ...cols,  [col[0]]: f => `\x1b[3${col[1]}m${f}\x1b[0m` }), {});

let socket = null, connectionInterval = null, latestData = null;
const connect = () => {
    if(socket) socket.close();
    if(connectionInterval) clearInterval(connectionInterval);

    socket = new WebSocket('wss://ws-feed.pro.coinbase.com');

    socket.addEventListener('open', function (event) {
        socket.send(JSON.stringify({
            type: "subscribe",
            channels: [
                {
                    name: "ticker",
                    product_ids: [
                        "BTC-USD",
                        "ETH-USD"
                    ]
                }
            ]
        }));
    });

    socket.addEventListener('message', function (message) {
        const data = JSON.parse(message.data);

        latestData = data;

        if (data.type === 'ticker') {
            console.log(r(data.product_id), w(JSON.stringify(data)));

            axios.post('http://localhost/api/node/pushBullData', {
                data: {
                    [data.product_id]: data
                }
            }).catch((error) => console.error('Failed to push data!'));
        } else console.log(c('WS Message'), message.data);
    });

    let latestDataTested = null;
    connectionInterval = setInterval(() => {
        if(latestData && latestDataTested && latestData === latestDataTested) connect();
        latestDataTested = latestData;
    }, 10000);
};

connect();
```

&& the route within laravel (make sure to make this only accessible from localhost):

```php
Route::prefix('node')->group(function () {
    Route::post('pushBullData', [ExternalController::class, 'pushBullData']);
});
```

## Red Tiger Snippets
Check snippet_dump/redtiger/bridge.js.




## Request netent

```
rs.i0.r.i1.pos=29&gameServerVersion=1.0.0&g4mode=false&game.win.coins=0&playercurrency=%26%23x20AC%3B&playercurrencyiso=EUR&historybutton=false&
rs.i0.r.i1.hold=false&current.rs.i0=basic&rs.i0.r.i4.hold=false&next.rs=basic&gamestate.history=basic&playforfun=true&
jackpotcurrencyiso=EUR&clientaction=spin&rs.i0.r.i1.syms=SYM5%2CSYM12%2CSYM9&rs.i0.r.i2.hold=false&rs.i0.r.i4.syms=SYM6%2CSYM7%2CSYM4&game.win.cents=0&rs.i0.r.i2.pos=27&rs.i0.id=basic&totalwin.coins=0&credit=499910&totalwin.cents=0&gamestate.current=basic&gameover=true&rs.i0.r.i0.hold=false&jackpotcurrency=%26%23x20AC%3B&multiplier=1&rs.i0.r.i3.pos=69&rs.i0.r.i4.pos=41&
rs.i0.r.i0.syms=SYM9%2CSYM12%2CSYM6&rs.i0.r.i3.syms=SYM7%2CSYM8%2CSYM9&isJackpotWin=false&gamestate.stack=basic&nextaction=spin&rs.i0.r.i0.pos=40&
wavecount=1&gamesoundurl=https%3A%2F%2Fstatic.casinomodule.com%2F&rs.i0.r.i2.syms=SYM9%2CSYM5%2CSYM3&rs.i0.r.i3.hold=false&

game.win.amount=0&cjpUrl=https%3A%2F%2Fcjp-dev.casinomodule.com
rs.i0.r.i1.pos=36&gameServerVersion=1.0.0&g4mode=false&game.win.coins=0&playercurrency=%26%23x20AC%3B&playercurrencyiso=EUR&historybutton=false&rs.i0.r.i1.hold=false&current.rs.i0=basic&rs.i0.r.i4.hold=false&next.rs=basic&gamestate.history=basic&playforfun=true&jackpotcurrencyiso=EUR&clientaction=spin&rs.i0.r.i1.syms=SYM12%2CSYM7%2CSYM1&rs.i0.r.i2.hold=false&rs.i0.r.i4.syms=SYM0%2CSYM8%2CSYM4&game.win.cents=0&rs.i0.r.i2.pos=75&rs.i0.id=basic&totalwin.coins=0&credit=499970&totalwin.cents=0&gamestate.current=basic&gameover=true&rs.i0.r.i0.hold=false&jackpotcurrency=%26%23x20AC%3B&multiplier=1&rs.i0.r.i3.pos=7&rs.i0.r.i4.pos=59&rs.i0.r.i0.syms=SYM12%2CSYM6%2CSYM8&rs.i0.r.i3.syms=SYM9%2CSYM4%2CSYM10&isJackpotWin=false&gamestate.stack=basic&nextaction=spin&rs.i0.r.i0.pos=12&wavecount=1&gamesoundurl=https%3A%2F%2Fstatic.casinomodule.com%2F&rs.i0.r.i2.syms=SYM4%2CSYM9%2CSYM10&rs.i0.r.i3.hold=false&game.win.amount=0&cjpUrl=https%3A%2F%2Fcjp-dev.casinomodule.com

https://netent-game.casinomodule.com/servlet/CasinoGameServlet;jsession=DEMO-702574331-EUR?action=spin&sessid=DEMO-702574331-EUR&gameId=deadoralive2_not_mobile&wantsreels=true&
wantsfreerounds=true&freeroundmode=false&bet.betlevel=1&bet.denomination=10&bet.betlines=0-8&no-cache=1e796ff1-9c0e-45b0-851c-3a9b6ae76188&bettingmode=coins

https://netent-static.casinomodule.com/games/dead-or-alive-2-client/game/dead-or-alive-2-client.xhtml?launchType=iframe&iframeSandbox=allow-scripts allow-popups allow-popups-to-escape-sandbox allow-top-navigation allow-top-navigation-by-user-activation allow-same-origin allow-forms allow-pointer-lock&applicationType=browser&gameId=deadoralive2_not_mobile&showHomeButton=false&gameLocation=games/dead-or-alive-2-client/&preBuiltGameRulesSupported=true&server=http://tolars.net/api/respins.io/games/netent?dividerTag=/&lang=en&sessId=DEMO-4190492579-EUR&operatorId=netent&logsId=19766ded-ea15-4c06-8b65-81f85440baf6&loadStarted=1660031775497&giOperatorConfig={"staticServer":"https://netent-static.casinomodule.com/","targetElement":"netentgame","launchType":"iframe","iframeSandbox":"allow-scripts allow-popups allow-popups-to-escape-sandbox allow-top-navigation allow-top-navigation-by-user-activation allow-same-origin allow-forms allow-pointer-lock","applicationType":"browser","gameId":"deadoralive2_not_mobile","showHomeButton":true,"gameLocation":"games/dead-or-alive-2-client/","preBuiltGameRulesSupported":true,"server":"http://tolars.net/api/respins.io/games/netent?dividerTag","lang":"en","sessId":"DEMO-4190492579-EUR","operatorId":"netent"}&casinourl=&testGroup=B



curl 'https://netent-game.casinomodule.com/servlet/CasinoGameServlet;jsession=DEMO-5430562958-EUR?action=spin&sessid=DEMO-5430562958-EUR&gameId=starburst_not_mobile&wantsreels=true&wantsfreerounds=true&freeroundmode=false&bet.betlevel=1&bet.denomination=20&bet.betlines=0-9&no-cache=77ac2f4a-5e5f-45d8-88b6-8dcfa1a5efd8&bettingmode=coins' \
  -H 'Accept: */*' \
  -H 'Accept-Language: en-ZA,en;q=0.9' \
  -H 'Cache-Control: no-cache' \
  -H 'Connection: keep-alive' \
  -H 'Origin: https://netent-static.casinomodule.com' \
  -H 'Pragma: no-cache' \
  -H 'Referer: https://netent-static.casinomodule.com/' \
  -H 'Sec-Fetch-Dest: empty' \
  -H 'Sec-Fetch-Mode: cors' \
  -H 'Sec-Fetch-Site: same-site' \
  -H 'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.5060.114 Safari/537.36' \
  -H 'sec-ch-ua: ".Not/A)Brand";v="99", "Google Chrome";v="103", "Chromium";v="103"' \
  -H 'sec-ch-ua-mobile: ?0' \
  -H 'sec-ch-ua-platform: "Linux"' \
  --compressed

https://tolars.net/api/respins.io/games/netent?dividerTagservlet/CasinoGameServlet;jsession=DEMO-4190492579-EUR?action=spin&sessid=DEMO-4190492579-EUR&gameId=deadoralive2_not_mobile&wantsreels=true&wantsfreerounds=true&freeroundmode=false&bet.betlevel=1&bet.denomination=10&bet.betlines=0-8&no-cache=dce23ff6-51b5-44c1-a88c-72f057aba6c0&bettingmode=coins
```
## Game adjustment (by netent/evolution illegal gambling vendor)

```js

/*
 * 1.26.0
 *
 *
 * Copyright, NetEnt AB (publ)
 * Website: https://www.netent.com/
*/

var netent_netentextend=function(){var n;return n=function(n,e,t){var o=this;e=e||{},o.hide=function(){n.style.visibility="hidden"},o.show=function(){n.style.visibility="visible"},o.resize=function(t,o){var i;if(void 0===t||void 0===o)throw new netent_error_handling.GiError(1,"netent_netentextend.main","resize","resize");i=netent_tools.resize(e.defaultWidth,e.defaultHeight,t,o,e.enforceRatio),n.style.width=i.width,n.style.height=i.height},o.addEventListener=function(n,e){void 0===t[n]&&(t[n]=[],o.sendSubscriptionLog(n)),t[n].push(e)},o.removeEventListener=function(n,e){var o,i=t[n];void 0!==i&&(o=i.indexOf(e),o>=0&&i.splice(o,1))}},n.prototype.get=function(n,e){},n.prototype.set=function(n,e,t){},n.prototype.call=function(n,e,t){},n.prototype.post=function(n,e,t,o,i){},n.prototype.sendSubscriptionLog=function(n){},{Base:n}}(),netent_nee_html_embed=function(){var n=function(n,e){var t,o,i,r,a,s,c,g,l,u,f,d,h=0,p={},m="subscriptionLog",_=!1,v=[],y=function(){};t={},f=function(n){var e=Array.prototype.slice.call(arguments,1);n.postMessage.apply(n,e)},netent_netentextend.Base.call(this,n,e,p),s=function(n){_&&console.warn("sendSubscriptionLog something went wrong: ",n)},a=function(n){_&&console.log("sendSubscriptionLog success: ",n)},r=function(n){var e,t=Array.prototype.slice.call(arguments,1),o=p[n];if(void 0!==o)for(e=0;e<o.length;e++)o[e].apply(null,t)},g=function(n){var e=t[n];return e?(clearTimeout(e.timeout),delete t[n]):e={success:y,error:y},e},l=function(n){var e=g(n),t=e.success,o=Array.prototype.slice.call(arguments,1);t.apply(null,o)},u=function(n,e,t){var o=g(n),i=o.error;i(new netent_error_handling.GiError(e,"netent_nee_html_embed",t||""))},i=function(n){var e,t;netent_validation.validateMessage(n)&&(e=n.data[0],t=n.data.slice(1),"success"===e?l.apply(null,t):"error"===e?u.apply(null,t):"event"===e&&(d=!0,c(),r.apply(null,t)))},o=function(e,o,i,r,a){h+=1,t[h]={success:r||y,error:a,timeout:setTimeout(function(n){return function(){u(n,11)}}(h),1e3)},f(n.contentWindow,[e,h,o].concat(i),"*")},c=function(){v.length&&(v.forEach(function(n){o(m,n,[],a.bind(null,n),s)}),v.length=0)},this.get=function(n,e,t){var i=this,r=netent_error_handling.handler(t);if("function"!=typeof e&&"function"!=typeof t)return new Promise(function(e,t){i.get(n,e,t)});try{netent_netentextend.Base.prototype.get.call(this,n,e,r),o("get",n,[],e,r)}catch(a){r(a)}},this.sendSubscriptionLog=function(n){try{d?o(m,n,[],a.bind(null,n),s):v.push(n)}catch(e){s(e)}},this.set=function(n,e,t,i){var r=this,a=netent_error_handling.handler(i);if("function"!=typeof t&&"function"!=typeof i)return new Promise(function(t,o){r.set(n,e,t,o)});try{netent_netentextend.Base.prototype.set.call(this,n,e,t,a),o("set",n,[e],t,a)}catch(s){a(s)}},this.call=function(n,e,t,i){var r=this,a=netent_error_handling.handler(i);if("function"!=typeof t&&"function"!=typeof i)return new Promise(function(t,o){r.call(n,e,t,o)});try{o("call",n,e,t,a)}catch(s){a(s)}},window.removeEventListener("message",window.neeOnMessage),window.neeOnMessage=i,window.addEventListener("message",i)};return netent_netentextend.Html=n,{Html:n}}(),netent_config_handling=function(){var n={targetElement:"neGameClient"},e={gameId:"string",gameName:"string",sessionId:"string",staticServer:"string",gameServerURL:"string",giLocation:"string",width:"string",height:"string",enforceRatio:"boolean",targetElement:"string",walletMode:"string",currency:"string",operatorId:"string",liveCasinoParams:{casinoId:"string"}},t=[{from:"gameServer",to:"gameServerURL"},{from:"historyUrl",to:"historyURL"},{from:"pluginUrl",to:"pluginURL"},{from:"lobbyUrl",to:"lobbyURL"},{from:"staticServerURL",to:"staticServer"},{from:"helpUrl",to:"helpURL"}],o=function(n){t.forEach(function(e){n.hasOwnProperty(e.from)&&!n.hasOwnProperty(e.to)&&(n[e.to]=n[e.from]),delete n[e.from]}),Object.keys(n).forEach(function(e){"object"==typeof n[e]&&Object.keys(n[e]).length>0&&o(n[e])})},i=function(n){var e;for(e in n)"object"==typeof n[e]&&null!==n[e]?(n[e]=i(n[e]),0===Object.keys(n[e]).length&&delete n[e]):(null===n[e]||"undefined"==typeof n[e]||"string"==typeof n[e]&&!n[e])&&"gameRulesURL"!==e&&delete n[e];return n},r=function(n,e){i(n),o(n),n.giLocation&&(n.giLocation=n.giLocation.replace(/\/?$/,"/")),n.staticServer&&(n.staticServer=n.staticServer.replace(/\/?$/,"/")),n.gameServerURL&&(n.gameServerURL=n.gameServerURL.replace(/\/?$/,"/")),e(n)};return{essentialParameters:e,handleConfig:r,defaultValues:n,filterRedundantParameters:i}}(),netent_error_handling=function(){var n=function(n,e,t,o){var i=this;return"number"!=typeof n?(i.code=0,i.error=n,i.message=netent_errors[0].replace("<error>",n),i.causedBy=t,void(i.origin=e)):(i.code=n,i.message=netent_errors[n],i.origin=e,i.causedBy=t,void(o&&(Object.getOwnPropertyNames(o).forEach(function(n){i.message?i.message=i.message.replace("<"+n+">",o[n]):i.message=o.error},i),i.variables=o)))},e=function(e){return function(t,o,i,r){var a;e&&(a=t instanceof n?t:t instanceof TypeError||t instanceof ReferenceError?new n(21,o,t):new n(t,o,i,r),netent_logging_handling.log(a),e(a))}};return{GiError:n,handler:e}}(),netent_errors=function(){return{0:"Unknown error: '<error>'",1:"Value for '<key>' is invalid, expects to be <type>",4:"Could not retrieve game configuration",9:"This functionality is not supported by this game",10:"Wrong number of arguments",11:"No answer from game",13:"SWFObject could not launch game",14:"Could not load SWFObject",16:"Target element '<value>' does not exist",17:"No value provided for essential parameter '<parameter>'",18:"Unable to launch HTML game",21:"This browser is not supported",23:"Could not open '<url>'.",24:"Could not init module '<module>'.",25:"Could not load module '<module>'.",26:"Height or width in percentage is not supported when enforceRatio=true."}}(),netent_gi_core=function(){var n,e=function(n,e,t,o){t=t||function(){},o=netent_error_handling.handler(o);try{netent_config_handling.handleConfig(e,function(){netent_module_handling.addAndLoadWithConfig(n,e,t,o)})}catch(i){o(i)}},t=function(n,o,i){var r,a,s;if("function"!=typeof o&&"function"!=typeof i)return new Promise(function(e,o){t(n,e,o)});if(netent_logging_handling.queue(),a=function(n){var t=netent_tools.combine({},n);e("launch",n,function(e){try{e&&e.addEventListener&&!netent_logging_handling.listenersAdded&&(netent_logging_handling.listenersAdded=!0,e.addEventListener("gameReady",function(){netent_logging_handling.log("game_ready")}),e.addEventListener("gameError",function(n){netent_logging_handling.log({gameError:n})}))}catch(i){console.warn("e",i)}netent_logging_handling.initLogging(n,t),o(e)},function(e){i(e),netent_logging_handling.initialized||netent_logging_handling.initLogging(n,t)})},s=n.staticServer||n.staticServerURL||"","string"!=typeof s)throw new Error("Cannot launch the game with the current configuration.");r=netent_tools.concat(s,"/config/services.json"),netent_gi_core.getDynamicHostname(r,s,function(e){var t=netent_tools.combine(n,{staticServer:e});a(t)})},o=function(n,e,t){netent_json_handling.getJson(n,function(n){t(n?n.clienthost?n.clienthost.trim():e:e)},function(n){t(e)})},i=function(n,t,o){if("function"!=typeof t&&"function"!=typeof o)return new Promise(function(e,t){i(n,e,t)});const r=n.staticServer||n.staticServerURL||"";if("string"!=typeof r)throw new Error("Cannot launch the game with the current configuration.");const a=netent_tools.concat(r,"/config/services.json");netent_gi_core.getDynamicHostname(a,r,function(i){const r=netent_tools.combine(n,{staticServer:i}),a=netent_tools.combine({},r);e("getgamerules",a,t,o)})},r=function(e){var t,o;return n?n:(e||(e=window.parent),t={contentWindow:e},o=new netent_netentextend.Html(t,{netentExtendSupported:!0}),n={get:o.get.bind(o),set:o.set.bind(o),call:o.call.bind(o),addEventListener:o.addEventListener.bind(o),removeEventListener:o.removeEventListener.bind(o)})},a=function(n,t,o){return"function"!=typeof t&&"function"!=typeof o?new Promise(function(e,t){a(n,e,t)}):void e("lcapi",n,t,o)};return{launch:t,getDynamicHostname:o,getGameRules:i,getOpenTables:a,plugin:r}}(),netent_json_handling=function(){var n=function(n,e,t,o,i){var r=new XMLHttpRequest;r.open(n,e,!0),r.onreadystatechange=function(){4===r.readyState&&(200===r.status?t(r.responseText):o(r.status>0&&200!==r.status?r.responseText:new netent_error_handling.GiError(23,"netent_gi_core",r,{url:e})))},"GET"===n?r.send():(r.setRequestHeader("Content-Type","application/json;charset=utf-8"),r.send(JSON.stringify(i)))},e=function(e,t,o,i,r){n(e,t,function(n){try{o(JSON.parse(n))}catch(e){i(new netent_error_handling.GiError(23,"netent_gi_core",{url:t}))}},i,r)},t=function(n,t,o,i){e("POST",n,o,i,t)},o=function(){var n={};return function(t,o,i,r){!r&&n[t]?o(n[t]):e("GET",t,function(e){n[t]=e,o(n[t])},i)}}();return{getJson:o,postJson:t}}(),netent_language_handling=function(){var n="en",e={ar:"ar-KW",he:"iw","pt-BR":"br","pt-PT":"pt","zh-Hans":"cn","zh-Hant":"zh-TW"},t={"fr-CA":"fr","es-US":"es","zh-TW":"cn","nl-BE":"nl"},o=function(n,e){var t=n.staticServer+e+"langlib/";return function(n){var e,o;o=t+n+"/"+n+".json";try{return e=new XMLHttpRequest,e.open("GET",o,!1),e.send(),200===e.status}catch(i){return!1}}},i=function(n){return n in t},r=function(n){return n in e},a=function(n){var e;return i(n)?(e=t[n],[e]):[]},s=function(n){if(r(n)){const t=e[n];return[n,t].concat(a(t))}return[n].concat(a(n))},c=function(e,t){var i,r,a,c,g;if(i=e.language,r=o(e,t),!i)return n;for(g=s(i),c=0;c<g.length;c++)if(a=g[c],r(a))return a;return i};return{getLanguage:c,getFallbackList:s}}(),initConfig={url:"https://gcs-prod.casinomodule.com/gcs/init",clientname:"game-inclusion",clientver:"1.26.0"},netent_logging_handling=function(){var n=new Date,e=function(){var n=[];return function(e){return e&&n.push({event:e,timestamp:new Date}),n}}(),t=function(){e().length=0,netent_logging_handling.log=function(){},netent_logging_handling.initialized=!0},o=function(){netent_logging_handling.log=e,netent_logging_handling.initialized=!1},i=function(n){var e,t;if(n.casinoBrand)return n.casinoBrand;try{return e=new RegExp("^(?:\\w*:\\/\\/)?([a-zA-Z0-9-]+?)(?:-static|-scs|\\.)\\S+$"),t=e.exec(n.staticServer)[1],t||"unbranded"}catch(o){return""}},r=function(n,e,t){var o={clientname:initConfig.clientname,clientver:initConfig.clientver,casinoid:i(n),gameid:n.gameId||""},r=n&&n.loggingURL||initConfig.url;netent_json_handling.postJson(r,o,function(n){n.initRequest=o,e(n)},t)},a=function(n){return netent_tools.getBooleanValue(n.enabled,!1)&&n.hasOwnProperty("configuration")},s=function(i,s,c){var g=function(n,e,t,o){console.warn(n,e,t,o),"function"==typeof c&&c()},l=function(n){var t;if(n){for(;e().length;)t=e().shift(),n(t.event,t.timestamp);netent_logging_handling.log=n,"function"==typeof c&&c()}};return netent_tools.getBooleanValue(i.disableLogging,!1)?(t(),void("function"==typeof c&&c())):(o(),netent_logging_handling.initialized=!0,void r(i,function(e){a(e)?(e.gi_started_time=n,e.operatorConfig=i,e.launchConfig=s,e.staticServer=i.staticServer,s&&s.giLocation&&(e.giLocation=s.giLocation),netent_logging_handling.statisticEndpointURL=e.configuration.endpoint,netent_module_handling.addAndLoadWithConfig("logging",e,l,g)):(t(),"function"==typeof c&&c())},g))};return{initLogging:s,log:e,queue:o,initialized:!1,listenersAdded:!1}}(),netent_module_handling=function(){var n={},e="netent_module_handling",t=function(e){return n[e].loaded},o=function(e,t){return n[e].essentialParameters=window[e].essentialParameters,Boolean(netent_validation.validateEssentialParameters(n[e].essentialParameters,n[e].config,t))},i=function(t,i,r){var a,s;try{o(t,r)&&(a=window[t].init,s=n[t],a(s.config,i,r))}catch(c){r(24,e,c,{module:t})}},r=function(e,t){n[e].loaded=t},a=function(e,t){var o=n[e].config.giLocation||n[e].config.staticServer+"gameinclusion/library/",i="modules"+(t||"")+"/"+e.split("netent_")[1]+"/main.js",r="";return o=decodeURIComponent(o),o.split("?")[1]&&(r="?"+o.split("?")[1]),o.split("?")[0]+i+r},s=function(n,o,s,c){t(n)?i(n,s,c):netent_tools.loadScript(a(n,o),function(){r(n,!0),i(n,s,c)},function(t){c(25,e,t,{module:n})})},c=function(e,t){n[e]||(n[e]={},r(e,!1)),n[e].config=t},g=function(n,e,t,o,i){n="netent_"+n,c(n,e),s(n,i,t,o)};return{addAndLoadWithConfig:g}}(),netent_tools=function(){var n=function(e,t){var o,i,r,a={};for(o in e)if(e.hasOwnProperty(o))if("object"==typeof e[o]){i=n(e[o],t);for(r in i)i.hasOwnProperty(r)&&(a[t?o+"."+r:r]=i[r])}else a[o]=e[o];return a},e=function(){var n=function(n,t){var o,i=JSON.parse(JSON.stringify(n));for(o in t)"object"==typeof t[o]&&"[object Array]"!==Object.prototype.toString.call(t[o])?(n[o]=n[o]||{},i[o]=e(n[o],t[o])):i[o]=t[o];return i};return function(){var e,t,o=arguments[0];for(t=1;t<arguments.length;t++)e=arguments[t],void 0!==e&&(o=n(o,e));return o}}(),t=function(n,e,t,o){var i=document.createElement("script");i.setAttribute("src",n),i.onload=e,i.onerror=t,o&&i.setAttribute("id",o),document.getElementsByTagName("head")[0].appendChild(i)},o=function(n){return"string"==typeof n?/^\d+\.?\d*$/.test(n):"number"==typeof n},i=function(n){return/^(px|em|pt|in|cm|mm|ex|pc|rem|vw|vh|%)$/.test(n)},r=function(n,e,t,o){var r=i(e)?e:"px",a=i(o)?o:"px";return t>=n?i(e)?e:a:i(o)?o:r},a=function(n,e,t,i,a){var s=/^(\d+\.?\d*)(\D*)$/,c=s.exec(n),g=s.exec(e),l=parseInt(c[1],10),u=parseInt(g[1],10),f=s.exec(t),d=s.exec(i),h=parseInt(f[1],10),p=parseInt(d[1],10),m=l/u,_=u/l,v={},y=r(h,f[2],p,d[2]);if(!a)return o(t)&&(t+=y),o(i)&&(i+=y),{width:t||"",height:i||""};if("%"===y)throw new netent_error_handling.GiError(26,"common.embed","% as unit");return o(h)||o(p)?!o(p)||p>=h*_?(v.width=h,v.height=h*_):(v.height=p,v.width=p*m):(v.width=l,v.height=u),v.width=Math.round(parseInt(v.width,10))+y,v.height=Math.round(parseInt(v.height,10))+y,v},s=function(n){var e;for(e in n)!n.hasOwnProperty(e)||null!==n[e]&&void 0!==n[e]||delete n[e]},c=function(n,e){var t=e;return"boolean"==typeof n?n:n?(n=n.toLowerCase(),"true"===n?t=!0:"false"===n&&(t=!1),t):t},g=function(n,e,t){var o;return netent_logging_handling.statisticEndpointURL?(t="undefined"!=typeof t?t:!0,o=t?encodeURIComponent(JSON.stringify(netent_logging.giOperatorConfig)):JSON.stringify(netent_logging.giOperatorConfig),"object"==typeof n&&(n.statisticEndpointURL=netent_logging_handling.statisticEndpointURL,n.logsId=netent_logging.logsId,n.loadStarted=netent_logging.game_load_started_time,n.giOperatorConfig=o,n.casinourl=netent_logging.casinourl,n.loadSeqNo=netent_logging.loadSeqNo,e&&(n.redirect="true")),"string"==typeof n&&(n+=-1===n.indexOf("?")?"?":"&",n+="statisticEndpointURL="+netent_logging_handling.statisticEndpointURL,n+="&logsId="+netent_logging.logsId,n+="&loadStarted="+netent_logging.game_load_started_time,n+="&giOperatorConfig="+o,n+="&casinourl="+netent_logging.casinourl,n+="&loadSeqNo="+netent_logging.loadSeqNo,e&&(n+="&redirect=true")),n):n},l=function(n){var e,t="DEMO-";for(e=0;13>e;e++)t+=Math.floor(10*Math.random());return t+="-"+(n||"EUR")},u=function(n,e){var t,o=[{from:"gameServerURL",to:"server"},{from:"language",to:"lang"},{from:"sessionId",to:e},{from:"casinoBrand",to:"operatorId"}];return n.hasOwnProperty("mobileParams")&&(t=n.mobileParams,Object.keys(t).forEach(function(e){t.hasOwnProperty(e)&&!n.hasOwnProperty(e)&&(n[e]=t[e])})),delete n.mobileParams,o.forEach(function(e){n.hasOwnProperty(e.from)&&(n[e.to]=n[e.from]),delete n[e.from]}),n},f=function(n,e){var t,o;return n=n.lastIndexOf("/")===n.length-1?n.substr(0,n.length-1):n,e=0===e.indexOf("/")?e.substr(1):e,t=""===n&&""===e,o=t?"":String(n+"/"+e)};return{flatten:n,combine:e,loadScript:t,removeMissingProperties:s,resize:a,getBooleanValue:c,addLoggingData:g,createDemoSessionID:l,transformConfig:u,concat:f}}(),netent_validation=function(){var n={string:/.*$/,"boolean":/^(true|false|TRUE|FALSE|True|False)$/},e=function(e,t,o){return void 0===t||void 0===o||n[o].test(t)?0:{key:e,type:o}},t=function(n){var t,o,i,r,a=netent_config_handling.essentialParameters,s=[];for(t in a)if(a.hasOwnProperty(t))if(o=a[t],"string"==typeof o)s.push(e(t,n[t],o));else for(i in o)o.hasOwnProperty(i)&&(r=n[t]?n[t][i]:void 0,s.push(e(t+"."+i,r,o[i])));return s.filter(Boolean)},o=function(n,e){var t;if(-1!==n.indexOf(".")){if(t=n.split(".",2),!e[t[0]]||!e[t[0]][t[1]])return!1}else if(!e[n])return!1;return!0},i=function(n,e,t){var i,r,a,s,c,g,l=0,u="||";if(n)for(i=0;i<n.length;i++)if(a=n[i],-1!==a.indexOf(u)){for(s=a.split(u),c=!1,r=0;r<s.length;r++)if(o(s[r],e)){c=!0;break}c||(l++,t(17,"netent_config_handling",a,{parameter:a}))}else o(a,e)||(l++,t(17,"netent_config_handling",a,{parameter:a}));return g=netent_validation.verifyConfigValueTypes(e),0!==g.length&&g.forEach(function(n){l++,t(1,"netent_config_handling","validate parameters",n)}),0===l},r=function(n){return n&&"[object Array]"===Object.prototype.toString.call(n.data)&&n.data.length>0};return{validateEssentialParameters:i,verifyConfigValueTypes:t,validateMessage:r}}();window.netent={launch:netent_gi_core.launch,getGameRulesUrl:netent_gi_core.getGameRules,getGameRulesURL:netent_gi_core.getGameRules,getGameRules:netent_gi_core.getGameRules,getOpenTables:netent_gi_core.getOpenTables},Object.defineProperty(window.netent,"plugin",{get:netent_gi_core.plugin});

```

```js
/*
Will handle game loading and sizing
*/
var Game = {
	config: {},
	active_size: "",
	target_element: "",
	sess_id: Math.floor(Math.random() * 10000000000) + 1,
	mobile_breakpoint: 414,
	game_handle: null,

	init: function(config){
		//Set our main config
		this.config = config;

		//Determine the window size, select a config and start game.
		var height = window.innerHeight;
		var width = window.innerWidth;
		console.log("Detected window size: "+width+" x "+height);

		//If on mobile, select mobile config.
		if(width  <= this.mobile_breakpoint){
			this.active_size = "mobile";
			this.target_element = "netentGameMobile";
		}
		else{
			this.active_size = "desktop";
			this.target_element = "netentgame";
		}

		console.log(this.active_size);
		$(window).resize(this.handleResize.bind(this));
		this.startGame();
	},

	handleResize: function(){
		var window_height = window.innerHeight;
		var window_width = window.innerWidth;
		var detected_size;

		if(window_width <= this.mobile_breakpoint){
			detected_size = "mobile"
		}
		else{
			detected_size = "desktop"
		}

		if(detected_size !== this.active_size){
			this.active_size = detected_size;
			$('#'+this.target_element).parent().html('<div id="'+this.target_element+'"></div>'); //Remove replace old iframe with blank div (for reuse later)
			this.target_element = (detected_size === "mobile")? "netentGameMobile" : "netentgame";
			this.game_handle = null;
			this.startGame();
			console.log("Screen size change to "+this.active_size+". Reloading game");
		}
		else if(this.game_handle){
			var width = $("#"+this.target_element).parent().outerWidth();
			var height = $("#"+this.target_element).parent().outerHeight();
			this.game_handle.resize(width, height);
			console.log("Resizing to "+width+" x "+height);
		}
	},

	startGame: function(){
		var size_config = this.config.sizes[this.active_size];

		if(typeof size_config === "undefined"){
			//Couldn't find config for this size, exit
			return;
		}

		var config = {
			staticServer: "https://"+this.config.casino_id+"-static.casinomodule.com",
			gameServer: "https://"+this.config.casino_game+".casinomodule.com",
			sessionId: "DEMO-"+this.sess_id+"-"+this.config.session_currency,  //<?php echo $_SESSION['_set_current_locale']['currency']; ?>",
			targetElement: this.target_element,
			walletMode : "basicwallet",
			language: this.config.session_language, //'<?php echo $_SESSION['_set_current_locale']['language']; ?>',
			launchType: 'iframe',
			iframeSandbox: 'allow-scripts allow-popups allow-popups-to-escape-sandbox allow-top-navigation allow-top-navigation-by-user-activation allow-same-origin allow-forms allow-pointer-lock',
			applicationType: 'browser',
			gameId: size_config.variant,
			casinoBrand: "netent"
		};

		console.log("Launching "+this.active_size);
		netent.launch(config, this.gameLoadSuccess.bind(this), this.gameLoadError.bind(this));
	},

	gameLoadSuccess: function(netEntExtend){
		this.game_handle = netEntExtend;

		if(typeof gameEventHandlerExtend !== "undefined"){
			var events = [
				"gameReady", "spinStarted", "spinProgress", "spinEnded", "gameRoundStarted",
				"gameRoundEnded", "volumeChange", "audioToggle", "balanceChanged", "gameError",
				"bonusGameStarted", "bonusGameEnded", "freeSpinStarted", "freeSpinEnded",
				"autoplayStarted", "autoplayEnded", "autoplayNextRound"
			];

			for(var j=0;j<events.length-1;j++)
			{
				//console.log("Setting up handler for " + events[j]);
				var func = new Function("name", "var b = new Array('" + events[j] + "',new Array(arguments[0])); gameEventHandlerExtend(b);");
				netEntExtend.addEventListener(events[j], func );
			}
		}

		var width = $("#"+this.target_element).parent().outerWidth();
		var height = $("#"+this.target_element).parent().outerHeight();
		console.log("Game loaded. Detected size: "+width+" x "+height);
		netEntExtend.resize(width, height);
	},

	gameLoadError: function(e){
		console.log(e)
		//show flash error for visitor
		if(e.code == 13) {
			$("#netentgame span").html("Please allow Flash content in your web browser to start the game.");
		}

	}
};



```

## Betgames create game
https://webiframe.betgames.tv/#/auth?apiUrl=APIURL&partnerCode=viewbet&partnerToken=0fcfd8a4-79b4-4efc-a6a3-9f1b476a7346&language=en&timezone=7&homeUrl=http://redirect.com

## PPLive (fake)
https://client.pragmaticplaylive.net/desktop/?tabletype=all&casino_id=ppcdg00000001558&web_server=https://games.pragmaticplaylive.net&config_url=/cgibin/appconfig/xml/configs/urls.xml&JSESSIONID=ECdk2AvCr_8E9FZO2FQTSqxpDDIka9rIumUzHCmNkrI1h1-2nXRU!415565709&socket_server=wss://games.pragmaticplaylive.net/game&token=ECdk2AvCr_8E9FZO2FQTSqxpDDIka9rIumUzHCmNkrI1h1-2nXRU!415565709&stats_collector_uuid=f167e6b6-3c30-4ee1-8e4f-220ed9cde0ef&actual_web_server=https://games.pragmaticplaylive.net&socket_port=443&uiAddress=https://client.pragmaticplaylive.net/desktop/&uiversion=1.15.2&gametype=all&game_mode=html5_desktop&lang=en&swf_lobby_path=/member/games/lobby.swf&lobbyGameSymbol=null&

## PPGames (fake)
https://viewbet.live/play?game_code=pragmaticslot&game_list_id=vs20cleocatra&game_list_image=https:%2F%2Fstatus-res.askmebet.com%2Fpragmatic%2Fvs20cleocatra.webp&game_list_name=Cleocatra
https://askmebet-sg1.ppgames.net/gs2c/html5Game.do?jackpotid=0&gname=Cleocatra&extGame=1&ext=0&cb_target=exist_tab&symbol=vs20cleocatra&jurisdictionID=99&lobbyUrl=http%3A%2F%2Fredirect.com&minilobby=false&mgckey=AUTHTOKEN@936277fe9a21d016e38c77dbdf0eba9ed182994f003dc216a6e8d7276d5141a7~stylename@amb_viewbet~SESSION@843ba04d-6fda-4a6a-9c39-d33b879dd1a6~SN@e336f38d&tabName=

## SA Gaming
https://al5.sa-api5.com/h5web/index.html?username=11ppg2de5c@al5&token=8FC9E06251DFA10FB996440769269895&lang=en&lobby=a1424&returnurl=http%3a%2f%2fredirect.com&net=0&h5web=True&ui=1&options=hidemultiplayer%3d1%2chideslot%3d1%2cdefaulttable%3d1&ip=109.37.159.108&bannerURL=&referer=https%3a%2f%2fviewbet.live%2f

## Fake pragmatic game example
Note console.php, loaded in by (most likely) fake domain ppgames.net, however pragmaticplay actively allows the alteration of their scripts and is involved as explained before, below is example of a foul play 'illegal gambling' provider under code-name HONESTMAN.
Basically upon error (like said sending a false constructed callback to legit gameprovider), it writes to console.php (faking to be local host by proxy windowing upon init so doesn't show up in network logs) the actual game request so a fraudelant result (spin result) can be sent to browser.
Backend to get the game result to bridge, is this package basically.


```css
        if (window.location.href.indexOf("replayGame.do") != -1)
            document.title = "Pragmatic Replay";
        var gaQueue = [],
            ga = function() {
                gaQueue.push(arguments)
            };

        var URLGameSymbol = "_unknown_game_symbol_from_url_";
        var LoadingStep = 0;

        var UHT_SEND_ERRORS = true;
        var UHT_HAD_ERRORS = false;

        window.onerror = function(messageOrEvent, source, lineno, colno) {

            if (!UHT_SEND_ERRORS)
                return;

            UHT_HAD_ERRORS = true;

            var args = null;

            if (messageOrEvent instanceof Event)
                args = [messageOrEvent["message"], messageOrEvent["fileName"], messageOrEvent["lineNumber"], messageOrEvent["columnNumber"]];
            else
                args = [messageOrEvent, source, lineno, colno];

            args[1] = String(args[1]).split("?").shift().split("/").pop();

            var msg = args[0] + " at " + args[1] + ":" + args[2] + ":" + args[3];
            ga('BehaviourTracker.send', 'event', "uht_errors", msg, URLGameSymbol, 1);

            window.onerror = null;
        };

        window.onbeforeunload = function() {
            var step = LoadingStep.toString() + (LoadingStep + 1).toString();
            var lastStep = LoadingStep.toString();

            if (LoadingStep == 4) {
                step = "PLAYING";
                lastStep = "PLAYING"
            }

            ga('LoadingTracker.send', 'event', "uht_loading", "_CLOSED_error_" + step, URLGameSymbol, UHT_HAD_ERRORS ? 1 : 0);

            if (LoadingStep > 1)
                globalTracking.StopTimerAndSend("uht_loading", "_CLOSED_at_" + lastStep, "LoadingTracker");
            else
            if (GA_timer_load_start != undefined)
                ga('LoadingTracker.send', 'timer', "uht_loading", "_CLOSED_at_1", URLGameSymbol, new Date().getTime() - GA_timer_load_start);

            UHT_SEND_ERRORS = false;

            if (SendTrackingIfQueued != undefined) {
                SendTrackingIfQueued();
                SendTrackingIfQueued();
                SendTrackingIfQueued();
                SendTrackingIfQueued();
            }

            return;
        }


        var game_symbol_from_url = (function() {
            var params = [];
            var urlSplitted = location.href.split("?");
            if (urlSplitted.length > 1) {
                var paramsSplitted = urlSplitted[1].split("&");
                for (var i = 0; i < paramsSplitted.length; ++i) {
                    var paramSplitted = paramsSplitted[i].split("=");
                    params[paramSplitted[0]] = (paramSplitted.length > 1) ? paramSplitted[1] : null;
                }
            }
            return params["symbol"];
        })();

        var game_symbol_from_url_value = game_symbol_from_url;

        if (game_symbol_from_url_value != undefined)
            URLGameSymbol = game_symbol_from_url_value;

        //ga('create', 'UA-83294317-1', {'siteSpeedSampleRate': 100, 'sampleRate': 10});
        ga('create', 'UA-83294317-2', {
            'siteSpeedSampleRate': 10,
            'sampleRate': 5,
            name: "RatingTracker"
        });

        ga('create', 'UA-83294317-3', {
            'siteSpeedSampleRate': 10,
            'sampleRate': 1,
            name: "LoadingTracker"
        });
        ga('create', 'UA-83294317-4', {
            'siteSpeedSampleRate': 10,
            'sampleRate': 1,
            name: "SpinTracker"
        });
        ga('create', 'UA-83294317-5', {
            'siteSpeedSampleRate': 10,
            'sampleRate': 100,
            name: "ServerErrorsTracker"
        });
        ga('create', 'UA-83294317-6', {
            'siteSpeedSampleRate': 10,
            'sampleRate': 5,
            name: "BehaviourTracker"
        });

        ga('LoadingTracker.send', 'event', "uht_loading", "_0_game_icon_clicked", URLGameSymbol, 1);

        function sendGAQueued() {
            var item = gaQueue.shift();
            if (item != undefined)
                ga.apply(window, item);

            if (gaQueue.length > 0)
                setTimeout(sendGAQueued, 1500);
        }

        ! function(r, d) {
            function i(i) {
                for (var e = {}, o = 0; o < i.length; o++) e[i[o].toUpperCase()] = i[o];
                return e
            }

            function n(i, e) {
                return typeof i == w && -1 !== I(e).indexOf(I(i))
            }

            function t(i, e) {
                if (typeof i == w) return i = i.replace(/^\s\s*/, "").replace(/\s\s*$/, ""), typeof e == b ? i : i.substring(0, 255)
            }

            function s(i, e) {
                for (var o, a, r, n, t, s = 0; s < e.length && !n;) {
                    for (var b = e[s], w = e[s + 1], l = o = 0; l < b.length && !n;)
                        if (n = b[l++].exec(i))
                            for (a = 0; a < w.length; a++) t = n[++o], typeof(r = w[a]) == c && 0 < r.length ? 2 === r.length ? typeof r[1] == u ? this[r[0]] = r[1].call(this,
                                t) : this[r[0]] = r[1] : 3 === r.length ? typeof r[1] != u || r[1].exec && r[1].test ? this[r[0]] = t ? t.replace(r[1], r[2]) : d : this[r[0]] = t ? r[1].call(this, t, r[2]) : d : 4 === r.length && (this[r[0]] = t ? r[3].call(this, t.replace(r[1], r[2])) : d) : this[r] = t || d;
                    s += 2
                }
            }

            function e(i, e) {
                for (var o in e)
                    if (typeof e[o] == c && 0 < e[o].length)
                        for (var a = 0; a < e[o].length; a++) {
                            if (n(e[o][a], i)) return "?" === o ? d : o
                        } else if (n(e[o], i)) return "?" === o ? d : o;
                return i
            }
            var u = "function",
                b = "undefined",
                c = "object",
                w = "string",
                l = "model",
                p = "name",
                m = "type",
                f = "vendor",
                h = "version",
                g = "architecture",
                o = "console",
                a = "mobile",
                v = "tablet",
                x = "smarttv",
                k = "wearable",
                y = "embedded",
                _ = "Amazon",
                S = "Apple",
                T = "ASUS",
                q = "BlackBerry",
                z = "Browser",
                N = "Chrome",
                A = "Firefox",
                C = "Google",
                E = "Huawei",
                O = "LG",
                U = "Microsoft",
                j = "Motorola",
                R = "Opera",
                M = "Samsung",
                P = "Sony",
                V = "Xiaomi",
                B = "Zebra",
                D = "Facebook",
                I = function(i) {
                    return i.toLowerCase()
                },
                W = {
                    ME: "4.90",
                    "NT 3.11": "NT3.51",
                    "NT 4.0": "NT4.0",
                    2E3: "NT 5.0",
                    XP: ["NT 5.1", "NT 5.2"],
                    Vista: "NT 6.0",
                    7: "NT 6.1",
                    8: "NT 6.2",
                    "8.1": "NT 6.3",
                    10: ["NT 6.4", "NT 10.0"],
                    RT: "ARM"
                },
                F = {
                    browser: [
                        [/\b(?:crmo|crios)\/([\w\.]+)/i],
                        [h, [p, "Chrome"]],
                        [/edg(?:e|ios|a)?\/([\w\.]+)/i],
                        [h, [p, "Edge"]],
                        [/(opera mini)\/([-\w\.]+)/i, /(opera [mobiletab]{3,6})\b.+version\/([-\w\.]+)/i, /(opera)(?:.+version\/|[\/ ]+)([\w\.]+)/i],
                        [p, h],
                        [/opios[\/ ]+([\w\.]+)/i],
                        [h, [p, R + " Mini"]],
                        [/\bopr\/([\w\.]+)/i],
                        [h, [p, R]],
                        [/(kindle)\/([\w\.]+)/i, /(lunascape|maxthon|netfront|jasmine|blazer)[\/ ]?([\w\.]*)/i, /(avant |iemobile|slim)(?:browser)?[\/ ]?([\w\.]*)/i, /(ba?idubrowser)[\/ ]?([\w\.]+)/i, /(?:ms|\()(ie) ([\w\.]+)/i,
                            /(flock|rockmelt|midori|epiphany|silk|skyfire|ovibrowser|bolt|iron|vivaldi|iridium|phantomjs|bowser|quark|qupzilla|falkon|rekonq|puffin|brave|whale|qqbrowserlite|qq)\/([-\w\.]+)/i, /(weibo)__([\d\.]+)/i
                        ],
                        [p, h],
                        [/(?:\buc? ?browser|(?:juc.+)ucweb)[\/ ]?([\w\.]+)/i],
                        [h, [p, "UC" + z]],
                        [/\bqbcore\/([\w\.]+)/i],
                        [h, [p, "WeChat(Win) Desktop"]],
                        [/micromessenger\/([\w\.]+)/i],
                        [h, [p, "WeChat"]],
                        [/konqueror\/([\w\.]+)/i],
                        [h, [p, "Konqueror"]],
                        [/trident.+rv[: ]([\w\.]{1,9})\b.+like gecko/i],
                        [h, [p, "IE"]],
                        [/yabrowser\/([\w\.]+)/i],
                        [h, [p, "Yandex"]],
                        [/(avast|avg)\/([\w\.]+)/i],
                        [
                            [p, /(.+)/, "$1 Secure " + z], h
                        ],
                        [/\bfocus\/([\w\.]+)/i],
                        [h, [p, A + " Focus"]],
                        [/\bopt\/([\w\.]+)/i],
                        [h, [p, R + " Touch"]],
                        [/coc_coc\w+\/([\w\.]+)/i],
                        [h, [p, "Coc Coc"]],
                        [/dolfin\/([\w\.]+)/i],
                        [h, [p, "Dolphin"]],
                        [/coast\/([\w\.]+)/i],
                        [h, [p, R + " Coast"]],
                        [/miuibrowser\/([\w\.]+)/i],
                        [h, [p, "MIUI " + z]],
                        [/fxios\/([-\w\.]+)/i],
                        [h, [p, A]],
                        [/\bqihu|(qi?ho?o?|360)browser/i],
                        [
                            [p, "360 " + z]
                        ],
                        [/(oculus|samsung|sailfish)browser\/([\w\.]+)/i],
                        [
                            [p, /(.+)/, "$1 " + z], h
                        ],
                        [/(comodo_dragon)\/([\w\.]+)/i],
                        [
                            [p, /_/g, " "], h
                        ],
                        [/(electron)\/([\w\.]+) safari/i, /(tesla)(?: qtcarbrowser|\/(20\d\d\.[-\w\.]+))/i, /m?(qqbrowser|baiduboxapp|2345Explorer)[\/ ]?([\w\.]+)/i],
                        [p, h],
                        [/(metasr)[\/ ]?([\w\.]+)/i, /(lbbrowser)/i],
                        [p],
                        [/((?:fban\/fbios|fb_iab\/fb4a)(?!.+fbav)|;fbav\/([\w\.]+);)/i],
                        [
                            [p, D], h
                        ],
                        [/safari (line)\/([\w\.]+)/i, /\b(line)\/([\w\.]+)\/iab/i, /(chromium|instagram)[\/ ]([-\w\.]+)/i],
                        [p, h],
                        [/\bgsa\/([\w\.]+) .*safari\//i],
                        [h, [p, "GSA"]],
                        [/headlesschrome(?:\/([\w\.]+)| )/i],
                        [h, [p, N + " Headless"]],
                        [/ wv\).+(chrome)\/([\w\.]+)/i],
                        [
                            [p, N + " WebView"], h
                        ],
                        [/droid.+ version\/([\w\.]+)\b.+(?:mobile safari|safari)/i],
                        [h, [p, "Android " + z]],
                        [/(chrome|omniweb|arora|[tizenoka]{5} ?browser)\/v?([\w\.]+)/i],
                        [p, h],
                        [/version\/([\w\.]+) .*mobile\/\w+ (safari)/i],
                        [h, [p, "Mobile Safari"]],
                        [/version\/([\w\.]+) .*(mobile ?safari|safari)/i],
                        [h, p],
                        [/webkit.+?(mobile ?safari|safari)(\/[\w\.]+)/i],
                        [p, [h, e, {
                            "1.0": "/8",
                            "1.2": "/1",
                            "1.3": "/3",
                            "2.0": "/412",
                            "2.0.2": "/416",
                            "2.0.3": "/417",
                            "2.0.4": "/419",
                            "?": "/"
                        }]],
                        [/(webkit|khtml)\/([\w\.]+)/i],
                        [p, h],
                        [/(navigator|netscape\d?)\/([-\w\.]+)/i],
                        [
                            [p, "Netscape"], h
                        ],
                        [/mobile vr; rv:([\w\.]+)\).+firefox/i],
                        [h, [p, A + " Reality"]],
                        [/ekiohf.+(flow)\/([\w\.]+)/i, /(swiftfox)/i, /(icedragon|iceweasel|camino|chimera|fennec|maemo browser|minimo|conkeror|klar)[\/ ]?([\w\.\+]+)/i, /(seamonkey|k-meleon|icecat|iceape|firebird|phoenix|palemoon|basilisk|waterfox)\/([-\w\.]+)$/i, /(firefox)\/([\w\.]+)/i, /(mozilla)\/([\w\.]+) .+rv\:.+gecko\/\d+/i, /(polaris|lynx|dillo|icab|doris|amaya|w3m|netsurf|sleipnir|obigo|mosaic|(?:go|ice|up)[\. ]?browser)[-\/ ]?v?([\w\.]+)/i,
                            /(links) \(([\w\.]+)/i
                        ],
                        [p, h]
                    ],
                    cpu: [
                        [/(?:(amd|x(?:(?:86|64)[-_])?|wow|win)64)[;\)]/i],
                        [
                            [g, "amd64"]
                        ],
                        [/(ia32(?=;))/i],
                        [
                            [g, I]
                        ],
                        [/((?:i[346]|x)86)[;\)]/i],
                        [
                            [g, "ia32"]
                        ],
                        [/\b(aarch64|arm(v?8e?l?|_?64))\b/i],
                        [
                            [g, "arm64"]
                        ],
                        [/\b(arm(?:v[67])?ht?n?[fl]p?)\b/i],
                        [
                            [g, "armhf"]
                        ],
                        [/windows (ce|mobile); ppc;/i],
                        [
                            [g, "arm"]
                        ],
                        [/((?:ppc|powerpc)(?:64)?)(?: mac|;|\))/i],
                        [
                            [g, /ower/, "", I]
                        ],
                        [/(sun4\w)[;\)]/i],
                        [
                            [g, "sparc"]
                        ],
                        [/((?:avr32|ia64(?=;))|68k(?=\))|\barm(?=v(?:[1-7]|[5-7]1)l?|;|eabi)|(?=atmel )avr|(?:irix|mips|sparc)(?:64)?\b|pa-risc)/i],
                        [
                            [g, I]
                        ]
                    ],
                    device: [
                        [/\b(sch-i[89]0\d|shw-m380s|sm-[pt]\w{2,4}|gt-[pn]\d{2,4}|sgh-t8[56]9|nexus 10)/i],
                        [l, [f, M],
                            [m, v]
                        ],
                        [/\b((?:s[cgp]h|gt|sm)-\w+|galaxy nexus)/i, /samsung[- ]([-\w]+)/i, /sec-(sgh\w+)/i],
                        [l, [f, M],
                            [m, a]
                        ],
                        [/\((ip(?:hone|od)[\w ]*);/i],
                        [l, [f, S],
                            [m, a]
                        ],
                        [/\((ipad);[-\w\),; ]+apple/i, /applecoremedia\/[\w\.]+ \((ipad)/i, /\b(ipad)\d\d?,\d\d?[;\]].+ios/i],
                        [l, [f, S],
                            [m, v]
                        ],
                        [/\b((?:ag[rs][23]?|bah2?|sht?|btv)-a?[lw]\d{2})\b(?!.+d\/s)/i],
                        [l, [f, E],
                            [m, v]
                        ],
                        [/(?:huawei|honor)([-\w ]+)[;\)]/i, /\b(nexus 6p|\w{2,4}-[atu]?[ln][01259x][012359][an]?)\b(?!.+d\/s)/i],
                        [l, [f, E],
                            [m, a]
                        ],
                        [/\b(poco[\w ]+)(?: bui|\))/i, /\b; (\w+) build\/hm\1/i, /\b(hm[-_ ]?note?[_ ]?(?:\d\w)?) bui/i, /\b(redmi[\-_ ]?(?:note|k)?[\w_ ]+)(?: bui|\))/i, /\b(mi[-_ ]?(?:a\d|one|one[_ ]plus|note lte|max)?[_ ]?(?:\d?\w?)[_ ]?(?:plus|se|lite)?)(?: bui|\))/i],
                        [
                            [l, /_/g, " "],
                            [f, V],
                            [m, a]
                        ],
                        [/\b(mi[-_ ]?(?:pad)(?:[\w_ ]+))(?: bui|\))/i],
                        [
                            [l, /_/g, " "],
                            [f, V],
                            [m, v]
                        ],
                        [/; (\w+) bui.+ oppo/i, /\b(cph[12]\d{3}|p(?:af|c[al]|d\w|e[ar])[mt]\d0|x9007|a101op)\b/i],
                        [l, [f, "OPPO"],
                            [m, a]
                        ],
                        [/vivo (\w+)(?: bui|\))/i, /\b(v[12]\d{3}\w?[at])(?: bui|;)/i],
                        [l, [f, "Vivo"],
                            [m, a]
                        ],
                        [/\b(rmx[12]\d{3})(?: bui|;|\))/i],
                        [l, [f, "Realme"],
                            [m, a]
                        ],
                        [/\b(milestone|droid(?:[2-4x]| (?:bionic|x2|pro|razr))?:?( 4g)?)\b[\w ]+build\//i, /\bmot(?:orola)?[- ](\w*)/i, /((?:moto[\w\(\) ]+|xt\d{3,4}|nexus 6)(?= bui|\)))/i],
                        [l, [f, j],
                            [m, a]
                        ],
                        [/\b(mz60\d|xoom[2 ]{0,2}) build\//i],
                        [l, [f, j],
                            [m, v]
                        ],
                        [/((?=lg)?[vl]k\-?\d{3}) bui| 3\.[-\w; ]{10}lg?-([06cv9]{3,4})/i],
                        [l, [f, O],
                            [m, v]
                        ],
                        [/(lm(?:-?f100[nv]?|-[\w\.]+)(?= bui|\))|nexus [45])/i, /\blg[-e;\/ ]+((?!browser|netcast|android tv)\w+)/i,
                            /\blg-?([\d\w]+) bui/i
                        ],
                        [l, [f, O],
                            [m, a]
                        ],
                        [/(ideatab[-\w ]+)/i, /lenovo ?(s[56]000[-\w]+|tab(?:[\w ]+)|yt[-\d\w]{6}|tb[-\d\w]{6})/i],
                        [l, [f, "Lenovo"],
                            [m, v]
                        ],
                        [/(?:maemo|nokia).*(n900|lumia \d+)/i, /nokia[-_ ]?([-\w\.]*)/i],
                        [
                            [l, /_/g, " "],
                            [f, "Nokia"],
                            [m, a]
                        ],
                        [/(pixel c)\b/i],
                        [l, [f, C],
                            [m, v]
                        ],
                        [/droid.+; (pixel[\daxl ]{0,6})(?: bui|\))/i],
                        [l, [f, C],
                            [m, a]
                        ],
                        [/droid.+ ([c-g]\d{4}|so[-gl]\w+|xq-a\w[4-7][12])(?= bui|\).+chrome\/(?![1-6]{0,1}\d\.))/i],
                        [l, [f, P],
                            [m, a]
                        ],
                        [/sony tablet [ps]/i, /\b(?:sony)?sgp\w+(?: bui|\))/i],
                        [
                            [l, "Xperia Tablet"],
                            [f, P],
                            [m, v]
                        ],
                        [/ (kb2005|in20[12]5|be20[12][59])\b/i, /(?:one)?(?:plus)? (a\d0\d\d)(?: b|\))/i],
                        [l, [f, "OnePlus"],
                            [m, a]
                        ],
                        [/(alexa)webm/i, /(kf[a-z]{2}wi)( bui|\))/i, /(kf[a-z]+)( bui|\)).+silk\//i],
                        [l, [f, _],
                            [m, v]
                        ],
                        [/((?:sd|kf)[0349hijorstuw]+)( bui|\)).+silk\//i],
                        [
                            [l, /(.+)/g, "Fire Phone $1"],
                            [f, _],
                            [m, a]
                        ],
                        [/(playbook);[-\w\),; ]+(rim)/i],
                        [l, f, [m, v]],
                        [/\b((?:bb[a-f]|st[hv])100-\d)/i, /\(bb10; (\w+)/i],
                        [l, [f, q],
                            [m, a]
                        ],
                        [/(?:\b|asus_)(transfo[prime ]{4,10} \w+|eeepc|slider \w+|nexus 7|padfone|p00[cj])/i],
                        [l, [f, T],
                            [m, v]
                        ],
                        [/ (z[bes]6[027][012][km][ls]|zenfone \d\w?)\b/i],
                        [l, [f, T],
                            [m, a]
                        ],
                        [/(nexus 9)/i],
                        [l, [f, "HTC"],
                            [m, v]
                        ],
                        [/(htc)[-;_ ]{1,2}([\w ]+(?=\)| bui)|\w+)/i, /(zte)[- ]([\w ]+?)(?: bui|\/|\))/i, /(alcatel|geeksphone|nexian|panasonic|sony)[-_ ]?([-\w]*)/i],
                        [f, [l, /_/g, " "],
                            [m, a]
                        ],
                        [/droid.+; ([ab][1-7]-?[0178a]\d\d?)/i],
                        [l, [f, "Acer"],
                            [m, v]
                        ],
                        [/droid.+; (m[1-5] note) bui/i, /\bmz-([-\w]{2,})/i],
                        [l, [f, "Meizu"],
                            [m, a]
                        ],
                        [/\b(sh-?[altvz]?\d\d[a-ekm]?)/i],
                        [l, [f, "Sharp"],
                            [m, a]
                        ],
                        [/(blackberry|benq|palm(?=\-)|sonyericsson|acer|asus|dell|meizu|motorola|polytron)[-_ ]?([-\w]*)/i,
                            /(hp) ([\w ]+\w)/i, /(asus)-?(\w+)/i, /(microsoft); (lumia[\w ]+)/i, /(lenovo)[-_ ]?([-\w]+)/i, /(jolla)/i, /(oppo) ?([\w ]+) bui/i
                        ],
                        [f, l, [m, a]],
                        [/(archos) (gamepad2?)/i, /(hp).+(touchpad(?!.+tablet)|tablet)/i, /(kindle)\/([\w\.]+)/i, /(nook)[\w ]+build\/(\w+)/i, /(dell) (strea[kpr\d ]*[\dko])/i, /(le[- ]+pan)[- ]+(\w{1,9}) bui/i, /(trinity)[- ]*(t\d{3}) bui/i, /(gigaset)[- ]+(q\w{1,9}) bui/i, /(vodafone) ([\w ]+)(?:\)| bui)/i],
                        [f, l, [m, v]],
                        [/(surface duo)/i],
                        [l, [f, U],
                            [m, v]
                        ],
                        [/droid [\d\.]+; (fp\du?)(?: b|\))/i],
                        [l, [f, "Fairphone"],
                            [m, a]
                        ],
                        [/(u304aa)/i],
                        [l, [f, "AT&T"],
                            [m, a]
                        ],
                        [/\bsie-(\w*)/i],
                        [l, [f, "Siemens"],
                            [m, a]
                        ],
                        [/\b(rct\w+) b/i],
                        [l, [f, "RCA"],
                            [m, v]
                        ],
                        [/\b(venue[\d ]{2,7}) b/i],
                        [l, [f, "Dell"],
                            [m, v]
                        ],
                        [/\b(q(?:mv|ta)\w+) b/i],
                        [l, [f, "Verizon"],
                            [m, v]
                        ],
                        [/\b(?:barnes[& ]+noble |bn[rt])([\w\+ ]*) b/i],
                        [l, [f, "Barnes & Noble"],
                            [m, v]
                        ],
                        [/\b(tm\d{3}\w+) b/i],
                        [l, [f, "NuVision"],
                            [m, v]
                        ],
                        [/\b(k88) b/i],
                        [l, [f, "ZTE"],
                            [m, v]
                        ],
                        [/\b(nx\d{3}j) b/i],
                        [l, [f, "ZTE"],
                            [m, a]
                        ],
                        [/\b(gen\d{3}) b.+49h/i],
                        [l, [f, "Swiss"],
                            [m, a]
                        ],
                        [/\b(zur\d{3}) b/i],
                        [l, [f, "Swiss"],
                            [m, v]
                        ],
                        [/\b((zeki)?tb.*\b) b/i],
                        [l, [f, "Zeki"],
                            [m, v]
                        ],
                        [/\b([yr]\d{2}) b/i, /\b(dragon[- ]+touch |dt)(\w{5}) b/i],
                        [
                            [f, "Dragon Touch"], l, [m, v]
                        ],
                        [/\b(ns-?\w{0,9}) b/i],
                        [l, [f, "Insignia"],
                            [m, v]
                        ],
                        [/\b((nxa|next)-?\w{0,9}) b/i],
                        [l, [f, "NextBook"],
                            [m, v]
                        ],
                        [/\b(xtreme\_)?(v(1[045]|2[015]|[3469]0|7[05])) b/i],
                        [
                            [f, "Voice"], l, [m, a]
                        ],
                        [/\b(lvtel\-)?(v1[12]) b/i],
                        [
                            [f, "LvTel"], l, [m, a]
                        ],
                        [/\b(ph-1) /i],
                        [l, [f, "Essential"],
                            [m, a]
                        ],
                        [/\b(v(100md|700na|7011|917g).*\b) b/i],
                        [l, [f, "Envizen"],
                            [m, v]
                        ],
                        [/\b(trio[-\w\. ]+) b/i],
                        [l, [f, "MachSpeed"],
                            [m, v]
                        ],
                        [/\btu_(1491) b/i],
                        [l, [f, "Rotor"],
                            [m, v]
                        ],
                        [/(shield[\w ]+) b/i],
                        [l, [f, "Nvidia"],
                            [m, v]
                        ],
                        [/(sprint) (\w+)/i],
                        [f, l, [m, a]],
                        [/(kin\.[onetw]{3})/i],
                        [
                            [l, /\./g, " "],
                            [f, U],
                            [m, a]
                        ],
                        [/droid.+; (cc6666?|et5[16]|mc[239][23]x?|vc8[03]x?)\)/i],
                        [l, [f, B],
                            [m, v]
                        ],
                        [/droid.+; (ec30|ps20|tc[2-8]\d[kx])\)/i],
                        [l, [f, B],
                            [m, a]
                        ],
                        [/(ouya)/i, /(nintendo) ([wids3utch]+)/i],
                        [f, l, [m, o]],
                        [/droid.+; (shield) bui/i],
                        [l, [f, "Nvidia"],
                            [m, o]
                        ],
                        [/(playstation [345portablevi]+)/i],
                        [l, [f, P],
                            [m, o]
                        ],
                        [/\b(xbox(?: one)?(?!; xbox))[\); ]/i],
                        [l, [f, U],
                            [m, o]
                        ],
                        [/smart-tv.+(samsung)/i],
                        [f, [m, x]],
                        [/hbbtv.+maple;(\d+)/i],
                        [
                            [l, /^/, "SmartTV"],
                            [f, M],
                            [m, x]
                        ],
                        [/(nux; netcast.+smarttv|lg (netcast\.tv-201\d|android tv))/i],
                        [
                            [f, O],
                            [m, x]
                        ],
                        [/(apple) ?tv/i],
                        [f, [l, S + " TV"],
                            [m, x]
                        ],
                        [/crkey/i],
                        [
                            [l, N + "cast"],
                            [f, C],
                            [m, x]
                        ],
                        [/droid.+aft(\w)( bui|\))/i],
                        [l, [f, _],
                            [m, x]
                        ],
                        [/\(dtv[\);].+(aquos)/i],
                        [l, [f, "Sharp"],
                            [m, x]
                        ],
                        [/\b(roku)[\dx]*[\)\/]((?:dvp-)?[\d\.]*)/i, /hbbtv\/\d+\.\d+\.\d+ +\([\w ]*; *(\w[^;]*);([^;]*)/i],
                        [
                            [f, t],
                            [l, t],
                            [m, x]
                        ],
                        [/\b(android tv|smart[- ]?tv|opera tv|tv; rv:)\b/i],
                        [
                            [m, x]
                        ],
                        [/((pebble))app/i],
                        [f, l, [m, k]],
                        [/droid.+; (glass) \d/i],
                        [l, [f, C],
                            [m, k]
                        ],
                        [/droid.+; (wt63?0{2,3})\)/i],
                        [l, [f, B],
                            [m, k]
                        ],
                        [/(quest( 2)?)/i],
                        [l, [f, D],
                            [m, k]
                        ],
                        [/(tesla)(?: qtcarbrowser|\/[-\w\.]+)/i],
                        [f, [m, y]],
                        [/droid .+?; ([^;]+?)(?: bui|\) applew).+? mobile safari/i],
                        [l, [m, a]],
                        [/droid .+?; ([^;]+?)(?: bui|\) applew).+?(?! mobile) safari/i],
                        [l, [m, v]],
                        [/\b((tablet|tab)[;\/]|focus\/\d(?!.+mobile))/i],
                        [
                            [m, v]
                        ],
                        [/(phone|mobile(?:[;\/]| safari)|pda(?=.+windows ce))/i],
                        [
                            [m, a]
                        ],
                        [/(android[-\w\. ]{0,9});.+buil/i],
                        [l, [f, "Generic"]]
                    ],
                    engine: [
                        [/windows.+ edge\/([\w\.]+)/i],
                        [h, [p, "EdgeHTML"]],
                        [/webkit\/537\.36.+chrome\/(?!27)([\w\.]+)/i],
                        [h, [p, "Blink"]],
                        [/(presto)\/([\w\.]+)/i, /(webkit|trident|netfront|netsurf|amaya|lynx|w3m|goanna)\/([\w\.]+)/i, /ekioh(flow)\/([\w\.]+)/i, /(khtml|tasman|links)[\/ ]\(?([\w\.]+)/i, /(icab)[\/ ]([23]\.[\d\.]+)/i],
                        [p, h],
                        [/rv\:([\w\.]{1,9})\b.+(gecko)/i],
                        [h, p]
                    ],
                    os: [
                        [/microsoft (windows) (vista|xp)/i],
                        [p, h],
                        [/(windows) nt 6\.2; (arm)/i, /(windows (?:phone(?: os)?|mobile))[\/ ]?([\d\.\w ]*)/i,
                            /(windows)[\/ ]?([ntce\d\. ]+\w)(?!.+xbox)/i
                        ],
                        [p, [h, e, W]],
                        [/(win(?=3|9|n)|win 9x )([nt\d\.]+)/i],
                        [
                            [p, "Windows"],
                            [h, e, W]
                        ],
                        [/ip[honead]{2,4}\b(?:.*os ([\w]+) like mac|; opera)/i, /cfnetwork\/.+darwin/i],
                        [
                            [h, /_/g, "."],
                            [p, "iOS"]
                        ],
                        [/(mac os x) ?([\w\. ]*)/i, /(macintosh|mac_powerpc\b)(?!.+haiku)/i],
                        [
                            [p, "Mac OS"],
                            [h, /_/g, "."]
                        ],
                        [/droid ([\w\.]+)\b.+(android[- ]x86)/i],
                        [h, p],
                        [/(android|webos|qnx|bada|rim tablet os|maemo|meego|sailfish)[-\/ ]?([\w\.]*)/i, /(blackberry)\w*\/([\w\.]*)/i, /(tizen|kaios)[\/ ]([\w\.]+)/i,
                            /\((series40);/i
                        ],
                        [p, h],
                        [/\(bb(10);/i],
                        [h, [p, q]],
                        [/(?:symbian ?os|symbos|s60(?=;)|series60)[-\/ ]?([\w\.]*)/i],
                        [h, [p, "Symbian"]],
                        [/mozilla\/[\d\.]+ \((?:mobile|tablet|tv|mobile; [\w ]+); rv:.+ gecko\/([\w\.]+)/i],
                        [h, [p, A + " OS"]],
                        [/web0s;.+rt(tv)/i, /\b(?:hp)?wos(?:browser)?\/([\w\.]+)/i],
                        [h, [p, "webOS"]],
                        [/crkey\/([\d\.]+)/i],
                        [h, [p, N + "cast"]],
                        [/(cros) [\w]+ ([\w\.]+\w)/i],
                        [
                            [p, "Chromium OS"], h
                        ],
                        [/(nintendo|playstation) ([wids345portablevuch]+)/i, /(xbox); +xbox ([^\);]+)/i, /\b(joli|palm)\b ?(?:os)?\/?([\w\.]*)/i,
                            /(mint)[\/\(\) ]?(\w*)/i, /(mageia|vectorlinux)[; ]/i, /([kxln]?ubuntu|debian|suse|opensuse|gentoo|arch(?= linux)|slackware|fedora|mandriva|centos|pclinuxos|red ?hat|zenwalk|linpus|raspbian|plan 9|minix|risc os|contiki|deepin|manjaro|elementary os|sabayon|linspire)(?: gnu\/linux)?(?: enterprise)?(?:[- ]linux)?(?:-gnu)?[-\/ ]?(?!chrom|package)([-\w\.]*)/i, /(hurd|linux) ?([\w\.]*)/i, /(gnu) ?([\w\.]*)/i, /\b([-frentopcghs]{0,5}bsd|dragonfly)[\/ ]?(?!amd|[ix346]{1,2}86)([\w\.]*)/i, /(haiku) (\w+)/i
                        ],
                        [p, h],
                        [/(sunos) ?([\w\.\d]*)/i],
                        [
                            [p, "Solaris"], h
                        ],
                        [/((?:open)?solaris)[-\/ ]?([\w\.]*)/i, /(aix) ((\d)(?=\.|\)| )[\w\.])*/i, /\b(beos|os\/2|amigaos|morphos|openvms|fuchsia|hp-ux)/i, /(unix) ?([\w\.]*)/i],
                        [p, h]
                    ]
                },
                G = function(i, e) {
                    if (typeof i == c && (e = i, i = d), !(this instanceof G)) return (new G(i, e)).getResult();
                    var o = i || (typeof r != b && r.navigator && r.navigator.userAgent ? r.navigator.userAgent : ""),
                        a = e ? function(i, e) {
                            var o, a = {};
                            for (o in i) e[o] && e[o].length % 2 == 0 ? a[o] = e[o].concat(i[o]) : a[o] = i[o];
                            return a
                        }(F, e) : F;
                    return this.getBrowser =
                        function() {
                            var i, e = {};
                            return e[p] = d, e[h] = d, s.call(e, o, a.browser), e.major = typeof(i = e.version) == w ? i.replace(/[^\d\.]/g, "").split(".")[0] : d, e
                        }, this.getCPU = function() {
                            var i = {};
                            return i[g] = d, s.call(i, o, a.cpu), i
                        }, this.getDevice = function() {
                            var i = {};
                            return i[f] = d, i[l] = d, i[m] = d, s.call(i, o, a.device), i
                        }, this.getEngine = function() {
                            var i = {};
                            return i[p] = d, i[h] = d, s.call(i, o, a.engine), i
                        }, this.getOS = function() {
                            var i = {};
                            return i[p] = d, i[h] = d, s.call(i, o, a.os), i
                        }, this.getResult = function() {
                            return {
                                ua: this.getUA(),
                                browser: this.getBrowser(),
                                engine: this.getEngine(),
                                os: this.getOS(),
                                device: this.getDevice(),
                                cpu: this.getCPU()
                            }
                        }, this.getUA = function() {
                            return o
                        }, this.setUA = function(i) {
                            return o = typeof i == w && 255 < i.length ? t(i, 255) : i, this
                        }, this.setUA(o), this
                };
            G.VERSION = "0.7.31", G.BROWSER = i([p, h, "major"]), G.CPU = i([g]), G.DEVICE = i([l, f, m, o, a, x, v, k, y]), G.ENGINE = G.OS = i([p, h]), typeof exports != b ? (typeof module != b && module.exports && (exports = module.exports = G), exports.UAParser2 = G) : typeof define == u && define.amd ? define(function() {
                return G
            }) : typeof r != b && (r.UAParser2 =
                G);
            var L, Z = typeof r != b && (r.jQuery || r.Zepto);
            Z && !Z.ua && (L = new G, Z.ua = L.getResult(), Z.ua.get = function() {
                return L.getUA()
            }, Z.ua.set = function(i) {
                L.setUA(i);
                var e, o = L.getResult();
                for (e in o) Z.ua[e] = o[e]
            })
        }("object" == typeof window ? window : this);
        var goog = {
            require: function() {},
            provide: function() {}
        };
        var UHT_ALL = false;
        var UHT_CONFIG = {
            GAME_URL: "",
            GAME_URL_ALTERNATIVE: "",
            LANGUAGE: "en",
            SYMBOL: "symbol",
            MINI_MODE: false,
            LOBBY_LAUNCHED: false
        };
        var UHT_DEVICE_TYPE = {
            MOBILE: false,
            DESKTOP: false
        };
        var UHT_FRAME = false;
        var UHT_LOW_END_DEVICE = false;
        var currentDatapathRetries = 0;
        var retriesBeforeAlternativeDatapath = 5;
        var LowEndDeviceIdentifiers = ["S III", "GT-I9300", "iPhone 5", "iPhone 5C", "iPhone 5S", "iPhone 6", "iPhone 6 Plus"];
        var UHTConsole = {};
        var UHT_UA_INFO = (new UAParser2).getResult();
        window.console = window.console || function() {
            var c = {};
            c.log = c.warn = c.debug = c.info = c.error = c.time = c.dir = c.profile = c.clear = c.exception = c.trace = c.assert = function() {};
            return c
        }();
        UHTConsole.Message = function(type, args) {
            this.type = type;
            this.args = args
        };
        UHTConsole.allowToWrite = false;
        UHTConsole.methods = ["log", "info", "warn", "error"];
        UHTConsole.source = {};
        UHTConsole.replacement = {};
        UHTConsole.messages = [];
        UHTConsole.wasAllowedToWrite = false;
        UHTConsole.redirectOutput = false;
        UHTConsole.logFilename = null;
        UHTConsole.GetReplacement = function(methodIdx) {
            return function() {
                var stringARGS = [];
                for (var i = 0; i < arguments.length; i++)
                    if (arguments[i] != null) stringARGS.push(arguments[i].toString());
                if (UHTConsole.redirectOutput) {
                    var args = [];
                    args.push(["g", UHT_CONFIG.SYMBOL].join("="));
                    args.push(["f", UHTConsole.logFilename].join("="));
                    args.push(["d", (new Date).getTime()].join("="));
                    args.push([UHTConsole.methods[methodIdx], stringARGS.join(",")].join("="));
                    (new Image).src = "/console .php?" + args.join("&")
                } else UHTConsole.messages.push(new UHTConsole.Message(UHTConsole.methods[methodIdx],
                    stringARGS));
                if (UHTConsole.messages.length > 512) UHTConsole.messages.splice(0, 128)
            }
        };
        UHTConsole.AllowToWrite = function(allowToWrite) {
            if (UHTConsole.redirectOutput) {
                UHTConsole.wasAllowedToWrite = allowToWrite;
                return
            }
            for (var i = 0; i < UHTConsole.methods.length; ++i) {
                var name = UHTConsole.methods[i];
                if (UHTConsole.source[name] == null) UHTConsole.source[name] = console[name];
                if (!allowToWrite)
                    if (UHTConsole.replacement[name] == null) UHTConsole.replacement[name] = UHTConsole.GetReplacement(i);
                console[name] = allowToWrite ? UHTConsole.source[name] : UHTConsole.replacement[name]
            }
            if (allowToWrite && !UHTConsole.allowToWrite) {
                for (var i =
                        0; i < UHTConsole.messages.length; ++i) console[UHTConsole.messages[i].type](UHTConsole.messages[i].args[0]);
                UHTConsole.messages = []
            }
            UHTConsole.allowToWrite = allowToWrite
        };
        UHTConsole.RedirectOutput = function(redirectOutput) {
            if (UHTConsole.redirectOutput == Boolean(redirectOutput)) return;
            if (redirectOutput) {
                if (UHTConsole.logFilename == null) UHTConsole.logFilename = UHTConsole.FormatDate(new Date);
                UHTConsole.wasAllowedToWrite = UHTConsole.allowToWrite;
                UHTConsole.AllowToWrite(false);
                UHTConsole.redirectOutput = redirectOutput;
                for (var i = 0; i < UHTConsole.messages.length; ++i) console[UHTConsole.messages[i].type](UHTConsole.messages[i].args[0]);
                UHTConsole.messages = []
            } else {
                UHTConsole.redirectOutput =
                    redirectOutput;
                UHTConsole.AllowToWrite(UHTConsole.wasAllowedToWrite)
            }
        };
        UHTConsole.FormatDate = function(d) {
            var date = d.toJSON().split("T")[0];
            var time = d.toTimeString().split(" ")[0].replace(/:/g, "-");
            return [date, time].join("_")
        };
        var Loader = {};
        Loader.WURFLProcessed = false;
        Loader.statisticsURL = null;
        Loader.statistics = null;
        Loader.LoadScript = function(url, loadCallback, errorCallback) {
            var script = document.createElement("script");
            script.src = url;
            if (loadCallback != undefined) script.onload = loadCallback;
            if (errorCallback != undefined) {
                script.onabort = errorCallback;
                script.onerror = errorCallback
            }
            document.getElementsByTagName("HEAD")[0].appendChild(script);
            return script
        };
        Loader.LoadWURFL = function() {
            var wurflURL = location.protocol + "//device.pragmaticplay .net/wurfl.js";
            if (location.hostname.indexOf("ppgames .net") != -1) wurflURL = location.protocol + "//device.ppgames .net/wurfl.js";
            Loader.LoadScript(wurflURL, Loader.WURFLLoadHandler, Loader.WURFLErrorHandler);
            setTimeout(Loader.WURFLErrorHandler, 2E3)
        };
        Loader.WURFLLoadHandler = function() {
            if (Loader.WURFLProcessed) return;
            Loader.WURFLProcessed = true;
            var WURFL = window["WURFL"] || null;
            if (WURFL == null) {
                setTimeout(Loader.WURFLLoadHandler, 10);
                return
            }
            if (WURFL.complete_device_name != undefined)
                for (var id in LowEndDeviceIdentifiers)
                    if (WURFL.complete_device_name.indexOf(LowEndDeviceIdentifiers[id]) >= 0) {
                        UHT_LOW_END_DEVICE = true;
                        break
                    }
            console.log("WURFL loaded");
            UHT_DEVICE_TYPE = {
                MOBILE: WURFL.is_mobile,
                DESKTOP: !WURFL.is_mobile
            };
            Loader.SetExtraInfo();
            Loader.SendStatistics(JSON.stringify(WURFL))
        };
        Loader.WURFLErrorHandler = function() {
            if (Loader.WURFLProcessed) return;
            Loader.WURFLProcessed = true;
            console.log("WURFL not loaded use UAParser2");
            var device = UHT_UA_INFO.device;
            var mobile = device.type == "mobile" || device.type == "tablet";
            UHT_DEVICE_TYPE = {
                MOBILE: mobile,
                DESKTOP: !mobile
            };
            Loader.SetExtraInfo();
            Loader.SendStatistics(JSON.stringify({}))
        };
        Loader.SetExtraInfo = function() {
            var inFrame = false;
            try {
                inFrame = window.top != window
            } catch (e) {
                inFrame = true
            }
            UHT_FRAME = inFrame;
            var os = UHT_UA_INFO.os.name;
            var device = UHT_UA_INFO.device.model;
            if (device != undefined)
                for (var id in LowEndDeviceIdentifiers)
                    if (device.indexOf(LowEndDeviceIdentifiers[id]) >= 0) {
                        UHT_LOW_END_DEVICE = true;
                        break
                    }
            var classNames = [document.documentElement.className || "", os, device, String(UHT_UA_INFO.browser.name).replace(/\s/g, ""), UHT_CONFIG.MINI_MODE ? "MiniMode" : "StandardMode"];
            classNames.push(inFrame ?
                "InFrame" : "MainWindow");
            document.documentElement.className = classNames.join(" ");
            document.documentElement.id = UHT_DEVICE_TYPE.MOBILE ? "Mobile" : "Desktop"
        };
        var PLATFORM_APPENDED = false;
        Loader.LoadGame = function() {
            if (!Loader.WURFLProcessed) {
                setTimeout(Loader.LoadGame, 50);
                return
            }
            if (UHT_ALL && !PLATFORM_APPENDED) {
                UHT_CONFIG.GAME_URL += (UHT_CONFIG.MINI_MODE ? "mini" : UHT_DEVICE_TYPE.MOBILE ? "mobile" : "desktop") + "/";
                UHT_CONFIG.GAME_URL_ALTERNATIVE += (UHT_CONFIG.MINI_MODE ? "mini" : UHT_DEVICE_TYPE.MOBILE ? "mobile" : "desktop") + "/";
                PLATFORM_APPENDED = true
            }
            var script = Loader.LoadScript(UHT_CONFIG.GAME_URL + "bootstrap.js" + "?key=" + "d9bcf", Loader.LoadGameCallback, function() {
                document.getElementsByTagName("HEAD")[0].removeChild(script);
                currentDatapathRetries++;
                if (currentDatapathRetries == retriesBeforeAlternativeDatapath) {
                    UHT_CONFIG.GAME_URL = UHT_CONFIG.GAME_URL_ALTERNATIVE;
                    window["ga"]("LoadingTracker.send", "event", "uht_loading", "_USED_ALTERNATIVE_DATA_PATH", window["URLGameSymbol"], 1)
                }
                setTimeout(Loader.LoadGame, 250)
            })
        };
        Loader.LoadGameCallback = function() {
            delete window.Loader;
            window.onload(null)
        };
        Loader.Listener = function(json) {
            console.info("Loader::Receive " + json);
            var params = JSON.parse(json);
            if (params["common"] == "EVT_GET_CONFIGURATION") {
                delete window.sendToGame;
                var args = params["args"];
                if (typeof args["config"] == "string") args["config"] = JSON.parse(args["config"]);
                UHT_CONFIG.GAME_URL = args["config"]["datapath"];
                UHT_CONFIG.GAME_URL_ALTERNATIVE = args["config"]["datapath_alternative"];
                if (UHT_CONFIG.GAME_URL_ALTERNATIVE == undefined) UHT_CONFIG.GAME_URL_ALTERNATIVE = args["config"]["datapath"];
                UHT_CONFIG.STYLENAME =
                    args["config"]["styleName"];
                UHT_CONFIG.LANGUAGE = args["config"]["lang"];
                var tmp = UHT_CONFIG.GAME_URL.split("/");
                var pathParts = [];
                for (var i = 0; i < tmp.length; ++i)
                    if (tmp[i].length > 0) pathParts.push(tmp[i]);
                var symbol = pathParts[pathParts.length - 1];
                UHT_CONFIG.SYMBOL = symbol;
                UHT_CONFIG.MINI_MODE = args["config"]["minimode"] == "1";
                UHT_CONFIG.LOBBY_LAUNCHED = args["config"]["lobbyLaunched"] == true;
                if (args["config"]["brandRequirements"] != null && args["config"]["brandRequirements"].indexOf("FORCEMOBILE") != -1) {
                    UHT_DEVICE_TYPE.MOBILE =
                        true;
                    UHT_DEVICE_TYPE.DESKTOP = false;
                    UHT_CONFIG.MINI_MODE = false
                }
                var statURL = args["config"]["statisticsURL"];
                if (statURL != undefined) {
                    Loader.statisticsURL = statURL + (/\?/.test(statURL) ? "&" : "?") + "mgckey=" + args["config"]["mgckey"];
                    if (Loader.statistics != null) Loader.SendStatistics(Loader.statistics)
                }
                Loader.LoadGame();
                UHTLogotype.LoadLogoInfo(args["config"]["styleName"])
            }
        };
        var GA_timer_load_start = (new Date).getTime();
        Loader.Start = function() {
            UHTConsole.AllowToWrite(false);
            var sendToAdapter = null;
            try {
                sendToAdapter = window.parent["sendToAdapter"] || null
            } catch (e) {}
            if (sendToAdapter == null) sendToAdapter = window["sendToAdapter"] || null;
            var online = sendToAdapter != null;
            console.info("Loader::loaded - online = " + String(online));
            if (online) {
                window.sendToGame = Loader.Listener;
                sendToAdapter(JSON.stringify({
                    common: "EVT_GET_CONFIGURATION",
                    type: "html5"
                }))
            } else Loader.LoadGame()
        };
        Loader.SendStatistics = function(params) {
            if (Loader.statisticsURL == null) {
                Loader.statistics = params;
                return
            }
            var xhr = new XMLHttpRequest;
            xhr.open("POST", Loader.statisticsURL + "&channel=" + (UHT_CONFIG.MINI_MODE ? "mini" : "") + (UHT_DEVICE_TYPE.MOBILE ? "mobile" : "desktop") + (UHT_CONFIG.LOBBY_LAUNCHED ? "_mini_lobby" : ""), true);
            xhr.setRequestHeader("Content-type", "application/json");
            xhr.send(params)
        };
        if (location.href.indexOf("WURFL_NOT_ALLOWED") > -1) Loader.WURFLErrorHandler();
        else setTimeout(Loader.LoadWURFL, 0);
        window.onload = Loader.Start;
        var UHTLogoIsVisible = true;
        var UHTLogotype = {};
        UHTLogotype.name = null;
        UHTLogotype.path = null;
        UHTLogotype.data = null;
        UHTLogotype.logoEl = null;
        UHTLogotype.logoImg = null;
        UHTLogotype.timer = -1;
        UHTLogotype.duration = 2E3;
        UHTLogotype.gameLoadingStarted = false;
        UHTLogotype.hideLogoTimeout = null;
        UHTLogotype.LoadLogoInfo = function(name) {
            if (UHT_CONFIG.GAME_URL.length == 0 || UHTLogotype == null) return;
            var split = UHT_CONFIG.GAME_URL.split("/");
            split.splice(split.indexOf(UHT_CONFIG.SYMBOL) - 2);
            UHTLogotype.name = name;
            UHTLogotype.path = split.join("/") + "/operator_logos/";
            var script = Loader.LoadScript(UHTLogotype.path + "logo_info.js", UHTLogotype.OnLogoInfoLoaded, function() {
                document.getElementsByTagName("HEAD")[0].removeChild(script);
                currentDatapathRetries++;
                if (currentDatapathRetries == retriesBeforeAlternativeDatapath) {
                    UHT_CONFIG.GAME_URL =
                        UHT_CONFIG.GAME_URL_ALTERNATIVE;
                    PLATFORM_APPENDED = false
                }
                setTimeout(UHTLogotype.LoadLogoInfo.bind(null, UHT_CONFIG.STYLENAME), 250)
            })
        };
        UHTLogotype.OnLogoInfoLoaded = function() {
            if (UHTLogotype == null) return;
            var info = window["UHTLogotypeInfo"] || null;
            if (info != null) UHTLogotype.data = info[UHTLogotype.name] || null;
            if (UHTLogotype.data != null) {
                UHTLogotype.logoImg = new Image;
                UHTLogotype.logoImg.onload = UHTLogotype.OnLogoLoaded;
                UHTLogotype.logoImg.src = UHTLogotype.path + UHTLogotype.data["src"]
            } else {
                UHTLogotype.UpdateStyle("logoOff", "logoOn");
                UHTLogoIsVisible = false
            }
        };
        UHTLogotype.OnLogoLoaded = function() {
            var wheel = document.createElement("div");
            wheel.className = "logotype-wheel";
            var el = document.createElement("div");
            el.className = "logotype";
            el.style.backgroundColor = UHTLogotype.data["bg"];
            el.style.backgroundImage = "url('" + UHTLogotype.logoImg.src + "')";
            el.appendChild(wheel);
            document.body.appendChild(el);
            UHTLogotype.logoEl = el;
            UHTLogotype.UpdateStyle("logoOn", "logoOff");
            UHTLogotype.timer = (new Date).getTime();
            UHTLogoIsVisible = true;
            UHTLogotype.HandleResize();
            window.addEventListener("resize",
                UHTLogotype.HandleResize, false);
            if (UHTLogotype.gameLoadingStarted) UHTLogotype.DelayHideLogo(UHTLogotype.duration)
        };
        UHTLogotype.HandleResize = function() {
            if (UHTLogotype.data == null) return;
            var w = UHTLogotype.logoImg.width;
            var h = UHTLogotype.logoImg.height;
            var sw = "auto";
            var sh = "auto";
            var r1 = window.innerWidth / window.innerHeight;
            var r2 = w / h;
            if (UHTLogotype.data["fit"] == "shrink")
                if (r2 < r1) sh = "100%";
                else sw = "100%";
            else if (r2 < r1) sw = "100%";
            else sh = "100%";
            UHTLogotype.logoEl.style.backgroundSize = [sw, sh].join(" ")
        };
        UHTLogotype.GameLoadingStarted = function() {
            UHTLogotype.gameLoadingStarted = true;
            if (UHTLogotype.data == null) {
                UHTLogotype.HideLogo();
                return
            }
            if (UHTLogotype.timer > 0) {
                var dt = UHTLogotype.duration - ((new Date).getTime() - UHTLogotype.timer);
                if (dt <= 0) UHTLogotype.HideLogo();
                else UHTLogotype.DelayHideLogo(dt)
            }
        };
        UHTLogotype.DelayHideLogo = function(delay) {
            if (UHTLogotype.hideLogoTimeout != null) clearTimeout(UHTLogotype.hideLogoTimeout);
            UHTLogotype.hideLogoTimeout = setTimeout(UHTLogotype.HideLogo, delay)
        };
        UHTLogotype.HideLogo = function() {
            if (UHTLogotype.logoEl != null) {
                document.body.removeChild(UHTLogotype.logoEl);
                window.removeEventListener("resize", UHTLogotype.HandleResize, false)
            }
            UHTLogotype.UpdateStyle("logoOff", "logoOn");
            UHTLogotype = null;
            UHTLogoIsVisible = false
        };
        UHTLogotype.UpdateStyle = function(add, remove) {
            var split = (document.documentElement.className || "").split(" ");
            var cls = [];
            for (var i = 0; i < split.length; ++i)
                if (split[i].length > 0 && split[i] != remove) cls.push(split[i]);
            cls.push(add);
            document.documentElement.className = cls.join(" ")
        };
        UHT_ALL = true;
    </script>
    <script type="text/javascript" src="https:// askmebet-sg1.ppgames.net/gs2c/common/js/html5-script-external.js"></script>
    <script type="text/javascript">
        Html5GameManager.init({
            contextPath: "/gs2c",
            cashierUrl: "",
            lobbyUrl: "http://redirect.com",
            mobileCashierUrl: "",
            mobileLobbyUrl: "",
            gameConfig: '{"jurisdiction":"99","openHistoryInWindow":false,"RELOAD_JACKPOT":"/gs2c/jackpot/reload.do","styleName":"amb_viewbet","SETTINGS":"/gs2c/saveSettings.do","openHistoryInTab":true,"replaySystemUrl":"https:// replay.pragmaticplay.net","integrationType":"HTTP","environmentId":"57","historyType":"internal","vendor":"T","currency":"THB","lang":"en","datapath":"https:// askmebet-sg1.ppgames.net/gs2c/common/games-html5/games/vs/vs20cleocatra/","amountType":"COIN","LOGOUT":"/gs2c/logout.do","REGULATION":"https://askmebet-sg1.ppgames.net/gs2c/regulation/process.do?symbol\u003dvs20cleocatra","replaySystemContextPath":"/ ReplayService","showRealCash":"1","statisticsURL":"https://askmebet-sg1.ppgames.net/gs2c/stats.do","accountType":"R","clock":"0","mgckey":"AUTHTOKEN@9064272297135ced354bb52ddff2cf12797d4374b2dd4614ef4c2e9322fdd322~stylename@amb_viewbet~SESSION@5420b061-c9ba-4255-b815-7144a8d0d069~SN@807e2c4b","gameService":"https:// askmebet-sg1.ppgames.net/gs2c/ge/v4/gameService","RELOAD_BALANCE":"/gs2c/reloadBalance.do","currencyOriginal":"THB","extend_events":"1","sessionTimeout":"30","CLOSE_GAME":"/gs2c/closeGame.do?symbol\u003dvs20cleocatra","region":"Asia","HISTORY":"https://askmebet-sg1.ppgames.net/gs2c/lastGameHistory.do?symbol\u003dvs20cleocatra\u0026mgckey\u003dAUTHTOKEN@9064272297135ced354bb52ddff2cf12797d4374b2dd4614ef4c2e9322fdd322~stylename@amb_viewbet~SESSION@5420b061-c9ba-4255-b815-7144a8d0d069~SN@807e2c4b"}',
            mgckey: "AUTHTOKEN@9064272297135ced354bb52ddff2cf12797d4374b2dd4614ef4c2e9322fdd322~stylename@amb_viewbet~SESSION@5420b061-c9ba-4255-b815-7144a8d0d069~SN@807e2c4b",
            jurisdictionMsg: "",
            extendSessionUrl: "",
            extendSessionInterval: null
        });
    </script>
</head>

<body class="CLIENT EXTERNAL HTML5">
    <div class="pageOverlap"></div>
    <div class="message-box browser-unsupported-message">
        <div class="message-title" style="color: #fff;">You are using an unsupported browser.</div>
        <div class="message-text" style="color: #fff;">Please use Google Chrome.</div>
    </div>

    <div id="wheelofpatience"></div>
    <div class="scale-holder" id="PauseRoot">
        <div class="scale-root" id="ScaleRoot">
            <div id="pauseindicator">
                <div class="pause-content">
                    <div class="pause-wheel"></div>
                    <div id="progressbar" class="progress-bar">
                        <div class="progress-value" id="progressvalue"></div>
                    </div>
                    <div id="DeferredLoadingText"></div>
                </div>
            </div>
        </div>
    </div>
    <script>
        setTimeout(function() {
            (function(i, s, o, g, r, a, m) {
                i['GoogleAnalyticsObject'] = r;
                i[r] = function() {
                    (i[r].q = i[r].q || []).push(arguments)
                }, i[r].l = 1 * new Date();
                a = s.createElement(o), a.async = 1;
                a.onload = function() {
                    var queue = [];
                    while (gaQueue.length > 0) {
                        var item = gaQueue.shift();
                        if (item.length > 0) {
                            if (item[0] == 'create')
                                ga.apply(i, item);
                            else
                                queue.push(item);
                        }
                    }
                    gaQueue = queue;
                    setTimeout(sendGAQueued, 1);
                };
                a.onerror = a.onabort = function() {
                    ga = function() {};
                    gaQueue = null
                };
                a.src = g;
                s.body.appendChild(a);
            })(window, document, 'script', '//www.googl e-analytics.com/analytics.js', 'ga');
        }, 1);
    </script>
</body>

</html>
```
