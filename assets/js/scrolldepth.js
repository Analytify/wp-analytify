(function ($) {

	(function (factory) {
		if (typeof define === 'function' && define.amd) {
			// AMD
			define(['jquery'], factory);
		} else if (typeof module === 'object' && module.exports) {
			// CommonJS
			module.exports = factory(require('jquery'));
		} else {
			// Browser globals
			factory(jQuery);
		}
	}(function ($) {

		'use strict';

		const defaults = {
			percentage: true
		};

		const $window = $(window);

		// Change these from const to let so they can be reassigned
		let cache = [];
		let scrollEventBound = false;
		let lastPixelDepth = 0;

		$.scrollDepth = function (options) {

			const startTime = +new Date();

			options = $.extend({}, defaults, options);

			function sendEvent( page_link, percentage, scrollDistance, timing ) {
				// Always use gtag for GA4 (since UA is deprecated)
				if ( typeof gtag !== 'undefined' ) {
					// Send scroll depth event for GA4
					gtag('event', 'scroll_depth', {
						'wpa_category': 'Analytify Scroll Depth',
						'wpa_percentage': percentage,
						'non_interaction': true
					});

					// Send timing event - always include timing data
					const eventTiming = timing || (+new Date - startTime);
					gtag('event', 'timing_complete', {
						'event_category': 'Analytify Scroll Depth',
						'event_label': page_link + ' - ' + percentage + '%',
						'value': eventTiming,
						'non_interaction': true
					});
				}
			}

			function calculateMarks(docHeight) {
				return {
					'25': parseInt(docHeight * 0.25, 10),
					'50': parseInt(docHeight * 0.50, 10),
					'75': parseInt(docHeight * 0.75, 10),
					/* Cushion to trigger 100% event in iOS */
					'100': docHeight - 5
				};
			}

			function checkMarks(marks, scrollDistance, timing) {
				/* Check each active mark */
				$.each(marks, function (key, val) {
					if ($.inArray(key, cache) === -1 && scrollDistance >= val) {
						const permalink = ( typeof analytifyScroll !== 'undefined' && analytifyScroll.permalink ) ? analytifyScroll.permalink : window.location.href;
						sendEvent(permalink, key, scrollDistance, timing);
						cache.push(key);
					}
				});
			}

			function rounded(scrollDistance) {
				/* Returns String */
				return (Math.floor(scrollDistance / 250) * 250).toString();
			}

			function init() {
				bindScrollDepth();

				// Fire 100% event on load when content fits in the viewport
				const docHeight = $(document).height();
				const winHeight = window.innerHeight ? window.innerHeight : $window.height();
				if (winHeight >= docHeight) {
					const timing = +new Date - startTime;
					const permalink = ( typeof analytifyScroll !== 'undefined' && analytifyScroll.permalink ) ? analytifyScroll.permalink : window.location.href;
					// Avoid duplicate 100% event
					if ($.inArray('100', cache) === -1) {
						sendEvent(permalink, '100', docHeight, timing);
						cache.push('100');
					}
				}
			}


			/* Reset Scroll Depth with the originally initialized options */
			$.scrollDepth.reset = function () {
				cache = [];
				lastPixelDepth = 0;
				$window.off('scroll.scrollDepth');
				bindScrollDepth();
			};

			/* Add DOM elements to be tracked */
			$.scrollDepth.addElements = function (elems) {

				if (typeof elems == 'undefined' || ! $.isArray(elems)) {
					return;
				}

				$.merge(options.elements, elems);

				/* If scroll event has been unbound from window, rebind */
				if (! scrollEventBound) {
					bindScrollDepth();
				}

			};

			/* Remove DOM elements currently tracked */
			$.scrollDepth.removeElements = function (elems) {

				if (typeof elems == 'undefined' || ! $.isArray(elems)) {
					return;
				}

				$.each(elems, function (index, elem) {

					const inElementsArray = $.inArray(elem, options.elements);
					const inCacheArray = $.inArray(elem, cache);

					if (inElementsArray != -1) {
						options.elements.splice(inElementsArray, 1);
					}

					if (inCacheArray != -1) {
						cache.splice(inCacheArray, 1);
					}

				});

			};

			function throttle(func, wait) {
				let timeout = null;
				let previous = 0;
				return function () {
					const context = this;
					const args = arguments;
					const now = new Date;
					if (! previous) { previous = now; }
					const remaining = wait - (now - previous);

					if (remaining <= 0) {
						clearTimeout(timeout);
						timeout = null;
						previous = now;
						func.apply(context, args);
					} else if (! timeout) {
						timeout = setTimeout(function () {
							previous = new Date;
							timeout = null;
							func.apply(context, args);
						}, remaining);
					}
				};
			}

			/*
			* Scroll Event
			*/

			function bindScrollDepth() {

				scrollEventBound = true;

				$window.on('scroll.scrollDepth', throttle(function () {
				/*
				* We calculate document and window height on each scroll event to
				* account for dynamic DOM changes.
				*/

					const docHeight = $(document).height(),
						winHeight = window.innerHeight ? window.innerHeight : $window.height(),
						scrollDistance = $window.scrollTop() + winHeight,

						/* Recalculate percentage marks */
						marks = calculateMarks(docHeight),

						/* Timing */
						timing = +new Date - startTime;

					checkMarks(marks, scrollDistance, timing);
				}, 500));

			}

			init();
		};

		/* UMD export */
		return $.scrollDepth;

	}));

	// Wait for gtag to be available before initializing scroll depth tracking
	function initScrollDepth() {

		// Check if gtag is available (either directly or via dataLayer)
		if ( typeof gtag !== 'undefined' || ( typeof window.dataLayer !== 'undefined' && typeof window.dataLayer.push === 'function' ) ) {
			$.scrollDepth();
		} else {
			// Retry after a short delay if gtag is not yet available (max 5 seconds)
			const maxRetries = 50;
			let retryCount = 0;
			const checkInterval = setInterval( function () {
				retryCount++;
				if ( typeof gtag !== 'undefined' || ( typeof window.dataLayer !== 'undefined' && typeof window.dataLayer.push === 'function' ) ) {
					clearInterval( checkInterval );
					$.scrollDepth();
				} else if ( retryCount >= maxRetries ) {
					clearInterval( checkInterval );
					// Initialize anyway - gtag might be loaded later
					$.scrollDepth();
				}
			}, 100 );
		}
	}

	// Initialize when DOM is ready
	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', initScrollDepth );
	} else {
		initScrollDepth();
	}

})(jQuery);
