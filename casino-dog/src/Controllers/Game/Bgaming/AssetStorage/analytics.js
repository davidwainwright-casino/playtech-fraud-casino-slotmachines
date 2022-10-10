(() => {
	/**
	 * @typedef {Object} ResizeData
	 * @property {Number} width - stage width
	 * @property {Number} height - stage height
	 * @property {Boolean} isPortrait - indicates that current device orientation is portrait
	 * @property {Boolean} isMobile - indicates that current device is mobile
	 *
	 * @typedef {Object} InitData
	 * @extends ResizeData
	 * @property {Boolean} isProvabilityAvailable
	 * @property {Boolean} isGambleAvailable
	 *
	 * @typedef {Object} SpinData
	 * @property {Number} totalBet - total bet for spin
	 * @property {Number} bet - total bet for spin (0 for freespins and respins)
	 * @property {Number} win - spin total win amount
	 * @property {Boolean} isAutoSpin - true for autospins
	 * @property {Boolean} isFreeSpin - true for freespins
	 * @property {Boolean} isRespin - true for respins
	 * @property {String} purchasedFeature - name of a purchased feature
	 *
	 * @typedef {Object} GamblePlayData
	 * @property {('black'|'red'|'d'|'h'|'s'|'c')} [choice] - For classic gamble.
	 * * Card color ('black' or 'red')
	 * * Card suit:
	 * ** 'd' - diamonds ♦
	 * ** 'h' - hearts ♥
	 * ** 's' - spades ♠
	 * ** 'c' - clubs ♣
	 * @property {String} [dealerCard] - Cars name. For BeatTheDealer gamble (or RiskGame).
	 *
	 * @typedef {Object} EventInfo
	 * @property {String} action - event action name
	 * @property {String} label - event label name
	 * @property {Number} value - event value (coast)
	 * @property {Boolean} isNonInteraction - true if event is a non_interaction event
	 */

	/**
	 * game events with sended data descriptions
	 */
	const GAME_EVENTS = {
		/**
		 * Fires when a game just show up
		 * @property {String} targetName - Game identifier (name)
		 * @property {InitData} context - Game size, orientation data and flags about game state (provabiity, gamble)
		 */
		GAME_LOADED: 'game_loaded',
		/**
		 * Fires when a game initialized with al basic data (API)
		 * @property {String} targetName - Game identifier (name)
		 * @property {InitData} context - Game size, orientation data and flags about game state (provabiity, gamble)
		 */
		GAME_INITIALIZED: 'game_initialized',
		/**
		 * Fires on a game resize
		 * @property {undefined} targetName
		 * @property {ResizeData} context - Game size and orientation data
		 */
		RESIZE: 'render_resize',
		/**
		 * Fires when player clicks spin or autospinning
		 * @property {undefined} targetName
		 * @property {SpinData} context - Spin result data, but without win value
		 */
		PRE_SPIN: 'pre_spin',
		/**
		 * Fires when player makes spin
		 * @property {undefined} targetName
		 * @property {SpinData} context - Spin result data (win, bet...)
		 */
		SPIN: 'spin',
		/**
		 * Fires when player collects money
		 * @property {undefined} targetName
		 * @property {undefined} context
		 */
		COLLECT: 'collect',
		/**
		 * Fires when player clicks verify button (provability)
		 * @property {undefined} targetName
		 * @property {undefined} context
		 */
		VERIFY: 'verify',
		/**
		 * Fires when player plays gamble (selects color or suit)
		 * @property {undefined} targetName
		 * @property {GamblePlayData} context - player selection info for classic gamble or dealer card for BeatTheDealer gamble
		 */
		GAMBLE_PLAY: 'gamble_play',
		/**
		 * Fires when player open gamble (click on gamble button)
		 * @property {undefined} targetName
		 * @property {undefined} context
		 */
		GAMBLE_OPEN: 'gamble_open',
		/**
		 * Fires when player clicks any button
		 * @property {String} targetName - button name
		 * @property {String} context - additional button info ('invoke', 'pointerdown', 'autorepeat', 'hotkey')
		 */
		BUTTON_CLICK: 'button-click',
		/**
		 * Fires when player changes any setting value
		 * @property {String} targetName - setting name
		 * @property {String} context - setting value
		 */
		SETTINGS_CHANGE: 'settings-change',
		/**
		 * Fires when player skips any animation
		 * @property {String} targetName - animation name (slot-animation name)
		 * @property {String} context - additional skip information (skip-trigger name)
		 */
		SKIP: 'skip',
		NO_SKIP: 'no-skip',
		/**
		 * custom game events
		 * @property {String} targetName - action name
		 * @property {EventInfo} context - event info
		 */
		CUSTOM_EVENT: 'custom_event',
	};

	const CATEGORIES = {
		APPLICATION: 'Application',
		GAME: window.__OPTIONS__.identifier,
		DEVICE_ORIENTATION: 'Device Orientation',
		PROVABILITY: 'Provability',
		GAMBLE: 'Gamble',
		BUTTONS: 'Buttons',
		SETTINGS: 'Settings',
		SPINNING: 'Spinning',
		ANIMATIONS: 'Animations',
		BUY_FEATURES: 'Buy Features',
	};
	const ACTIONS = {
		APPLICATION_LOADED: 'Application Loaded',
		START_GAME: 'Start Game',
		CHANGE_GAME: 'Change Game',
		START_ORIENTATION: 'Start Orientation',
		CHANGE_ORIENTATION: 'Change Orientation',
		USING_ORIENTATION: 'Using Orientation',
		SPIN: 'Spin',
		SPIN_TYPE: 'Spin Type',
		GAMBLE_PLAY: 'Gamble Play',
		BUTTON_CLICK: 'Button Click',
		SETTINGS_CHANGE: 'Settings Change',
		SKIPPING: 'Skipping',
	};

	let startAppDate = Date.now();
	let loadedAppDate;
	let isGameStarted = false;
	let isProvabilityAvailable = false;
	let isBuyFeaturesAvailable = false;
	let isGambleAvailable = false;
	let ignoreOrientationEvents = false;
	let lastSpinData = {};
	let sessionData = {};

	if (!window.trackGameEventListeners) {
		window.trackGameEventListeners = [];
	}

	window.trackGameEventListeners.push(eventProcessor);

	// TODO: remove when all games will use trackGameEventListeners instead (utils 9.0.0 or higher)
	window.trackGameEvent = eventProcessor;

	function eventProcessor(eventName, targetName, context) {
		switch (eventName) {
		case GAME_EVENTS.GAME_LOADED: {
			if (!loadedAppDate) {
				loadedAppDate = Date.now();
				const loadTime = (loadedAppDate - startAppDate) / 1000;
				sendEvent(CATEGORIES.APPLICATION, ACTIONS.APPLICATION_LOADED, CATEGORIES.GAME, Math.floor(loadTime), true);
				if (window.gtag) {
					const event_label = window.__OPTIONS__.resources_path.indexOf('cloudfront') >= 0
						? `${CATEGORIES.GAME} (CloudFront)`
						: `${CATEGORIES.GAME} (Cloudflare)`;
					window.gtag('event', ACTIONS.APPLICATION_LOADED, { event_category: CATEGORIES.APPLICATION, event_label, value: Math.floor(loadTime), non_interaction: true });
				}
				console.log(`Game loaded in ${loadTime}s`);
			}
			break;
		}
		case GAME_EVENTS.GAME_INITIALIZED: {
			const previousGame = CATEGORIES.GAME;
			CATEGORIES.GAME = targetName;

			lastSpinData = {};

			if (!isGameStarted) {
				isGameStarted = true;
				isProvabilityAvailable = !!context.isProvabilityAvailable;
				isBuyFeaturesAvailable = !!context.isBuyFeaturesAvailable;
				isGambleAvailable = !!context.isGambleAvailable;
				ignoreOrientationEvents = !!context.ignoreOrientationEvents;
				sendEvent(previousGame, ACTIONS.START_GAME);
				sendDeviceOrientationEvent(context);
			}

			if (previousGame && previousGame !== CATEGORIES.GAME) {
				sendEvent(previousGame, ACTIONS.CHANGE_GAME, `Game changed to: ${CATEGORIES.GAME}`);
				sendEvent(CATEGORIES.GAME, ACTIONS.START_GAME, `Game started from: ${previousGame}`);
			}

			break;
		}
		case GAME_EVENTS.RESIZE: {
			sendDeviceOrientationEvent(context);
			break;
		}
		case GAME_EVENTS.PRE_SPIN: {
			if (!context) break;

			// if (context.isFreeSpin) {
			// 	sendEvent(CATEGORIES.SPINNING, ACTIONS.SPIN_TYPE, 'Free Spin');
			// } else if (context.isAutoSpin) {
			// 	sendEvent(CATEGORIES.SPINNING, ACTIONS.SPIN_TYPE, 'Auto Spin');
			// } else if (!context.isRespin) {
			// 	sendEvent(CATEGORIES.SPINNING, ACTIONS.SPIN_TYPE, 'User Spin');
			// }
			break;
		}
		case GAME_EVENTS.SPIN: {
			sendLastSpinEvents(context);
			break;
		}
		case GAME_EVENTS.COLLECT: {
			sendLastSpinEvents({});
			break;
		}
		case GAME_EVENTS.VERIFY: {
			lastSpinData.isVerifyClicked = true;
			break;
		}
		case GAME_EVENTS.GAMBLE_PLAY: {
			lastSpinData.isGamblePlayed = true;

			if (context && context.choice) {
				// sendEvent(CATEGORIES.GAMBLE, ACTIONS.GAMBLE_PLAY, `User choice is: "${context.choice}"`);
			} else if (context && context.dealerCard) {
				// sendEvent(CATEGORIES.GAMBLE, ACTIONS.GAMBLE_PLAY, `Dealer card is: "${context.dealerCard}"`);
			}
			break;
		}
		case GAME_EVENTS.GAMBLE_OPEN: {
			lastSpinData.isGambleOpened = true;
			break;
		}
		case GAME_EVENTS.BUTTON_CLICK: {
			if (['info-button', 'paytable-button'].indexOf(targetName) !== -1) {
				sendEvent(CATEGORIES.BUTTONS, ACTIONS.BUTTON_CLICK, 'Paytable/Info Toggle');
			}
			if ('rules-button' === targetName) {
				sendEvent(CATEGORIES.BUTTONS, ACTIONS.BUTTON_CLICK, 'Rules');
			}
			if (targetName && targetName.startsWith('mute-button')) {
				sendEvent(CATEGORIES.SETTINGS, ACTIONS.SETTINGS_CHANGE, 'Mute/Unmute');
				sessionData.isMusicVolChangedByBtn = true;
			}
			if ('settings-music' === targetName) {
				sendEvent(CATEGORIES.SETTINGS, ACTIONS.SETTINGS_CHANGE, 'Music Checkbox');
				sessionData.isMusicVolChangedByBtn = true;
			}
			if ('settings-fx' === targetName) {
				sendEvent(CATEGORIES.SETTINGS, ACTIONS.SETTINGS_CHANGE, 'SFX Checkbox');
				sessionData.isMusicVolChangedByBtn = true;
			}
			break;
		}
		case GAME_EVENTS.SETTINGS_CHANGE: {
			if (targetName === 'musicVol') {
				if (!sessionData.isMusicVolChangedByBtn) {
					sendEvent(CATEGORIES.SETTINGS, ACTIONS.SETTINGS_CHANGE, 'Volume Drag');
				}
				sessionData.isMusicVolChangedByBtn = false;
			}
			break;
		}
		case GAME_EVENTS.SKIP:
		case GAME_EVENTS.NO_SKIP: {
			break; // eslint-disable-next-line no-unreachable
			if (targetName === 'popup/free-spins-win') {
				sendEvent(CATEGORIES.ANIMATIONS, ACTIONS.SKIPPING, `Freespins Total Win (${eventName})`);
			}
			if (targetName === 'spin') {
				sendEvent(CATEGORIES.ANIMATIONS, ACTIONS.SKIPPING, `Spinning (${eventName})`);
			}
			if (targetName.startsWith('popup/win')) {
				sendEvent(CATEGORIES.ANIMATIONS, ACTIONS.SKIPPING, `Low Win (${eventName})`);
			}
			if (targetName.indexOf('anticipation') !== -1) {
				sendEvent(CATEGORIES.ANIMATIONS, ACTIONS.SKIPPING, `Anticipation (${eventName})`);
			}
			if (targetName === 'popup/big-win') {
				if (!lastSpinData || !lastSpinData.isBigWinSkipped) {
					lastSpinData = lastSpinData || {};
					lastSpinData.isBigWinSkipped = eventName === GAME_EVENTS.SKIP;
					if (eventName === GAME_EVENTS.SKIP || context === 'skip-end') {
						sendEvent(CATEGORIES.ANIMATIONS, ACTIONS.SKIPPING, `Big Win (${eventName})`);
					}
				}
				if (context === 'skip-counter') {
					sendEvent(CATEGORIES.ANIMATIONS, ACTIONS.SKIPPING, `Big Win Counter (${eventName})`);
				}
				if (context === 'skip-end') {
					sendEvent(CATEGORIES.ANIMATIONS, ACTIONS.SKIPPING, `Big Win End (${eventName})`);
				}
			}
			break;
		}
		case GAME_EVENTS.CUSTOM_EVENT: {
			if (!context) break;
			sendEvent(CATEGORIES.GAME, context.action, context.label, context.value, context.isNonInteraction);
			break;
		}
		}
	}


	function sendEvent(event_category, event_action, event_label, value, non_interaction = false) {
		if (window.gtag) {
			//window.gtag('event', event_action, { event_category, event_label, value, non_interaction });
		} else {
			console.log('🅰️', 'event', event_action, { event_category, event_label, value, non_interaction });
		}
	}

	let lastResizeData;
	let lastOrientationChangeTime = Date.now();
	/**
	 * @param {ResizeData}
	 */
	function sendDeviceOrientationEvent(resizeData) {
		if (!resizeData || ignoreOrientationEvents) return;
		if (lastResizeData && resizeData.isPortrait === lastResizeData.isPortrait) return;

		if (!lastResizeData) {
			sendEvent(
				CATEGORIES.DEVICE_ORIENTATION,
				ACTIONS.START_ORIENTATION,
				resizeData.isMobile
					? (resizeData.isPortrait ? 'Portrait' : 'Landscape')
					: 'Desktop',
				true
			);
		}
		sendUsingOrientationEvent();

		lastResizeData = resizeData;
	}

	function sendUsingOrientationEvent() {
		if (!lastResizeData || ignoreOrientationEvents) return;

		// sendEvent(
		// 	CATEGORIES.DEVICE_ORIENTATION,
		// 	ACTIONS.USING_ORIENTATION,
		// 	lastResizeData.isPortrait ? 'Portrait' : 'Landscape',
		// 	Math.floor((Date.now() - lastOrientationChangeTime) / 1000),
		// 	true
		// );
		lastOrientationChangeTime = Date.now();
	}

	/**
	 * @param {SpinData} spinData
	 */
	function sendLastSpinEvents(spinData) {
		const {win = 0, bet = 0, isVerifyClicked = false, isGamblePlayed = false, isGambleOpened = false, purchasedFeature} = lastSpinData;

		if (isBuyFeaturesAvailable && (bet || purchasedFeature)) {
			// sendEvent(CATEGORIES.BUY_FEATURES, ACTIONS.SPIN, purchasedFeature ? `WITH ${purchasedFeature}` : 'SIMPLE');
		}
		if (isProvabilityAvailable && bet) {
			// sendEvent(CATEGORIES.PROVABILITY, ACTIONS.SPIN, isVerifyClicked ? 'VERIFIED' : 'NOT VERIFIED');
		}
		if (isGambleAvailable && ((win && bet) || isGamblePlayed)) {
			// sendEvent(CATEGORIES.GAMBLE, ACTIONS.SPIN, isGamblePlayed ? 'WITH GAMBLE' : (isGambleOpened ? 'WITHOUT GAMBLE (BUT OPENED)' : 'WITHOUT GAMBLE'));
		}
		lastSpinData = spinData || {};
	}

})();

