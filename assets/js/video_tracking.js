(function ($) {
	'use strict';

	const defaults = {
		progressTracking: true,
		percentageMarks: [25, 50, 75, 100]
	};

	const cache = [];
	const youtubePlayers = {};
	const vimeoPlayers = {};

	$.videoTracking = function (options) {
		options = $.extend({}, defaults, options);

		const sendEvent = function (videoTitle, action, percentage, provider, duration) {
			// console.log(`Sending event to GA4: ${videoTitle} - ${action} - ${percentage}% - ${provider} - ${duration}s`);
			gtag('event', 'analytify_video_tracking', {
				'wpa_category': 'Analytify Video Tracking',
				'wpa_video_title': videoTitle,
				'value': percentage,
				'non_interaction': true,
				'wpa_video_action': action,
				'wpa_video_provider': provider,
				'wpa_video_duration': duration
			});
		};

		const trackProgress = function (videoTitle, percentage, marks, provider, duration) {
			marks.forEach((mark) => {
				if (! cache.includes(`${videoTitle}-${mark}`) && percentage >= mark) {
					sendEvent(videoTitle, `progress_${mark}%`, percentage, provider, duration);
					cache.push(`${videoTitle}-${mark}`);
				}
			});
		};

		const setupHTML5Tracking = function () {
			$('video').each(function () {
				const video = this;

				$(video).on('play pause ended timeupdate', function (event) {
					const percentage = Math.round((video.currentTime / video.duration) * 100);
					const videoTitle = video.getAttribute('title') || video.currentSrc;
					const duration = Math.round(video.duration) || 0;

					switch (event.type) {
						case 'play':
							sendEvent(videoTitle, 'play', percentage, 'Media/HTML5', duration);
							break;
						case 'pause':
							sendEvent(videoTitle, 'pause', percentage, 'Media/HTML5', duration);
							break;
						case 'ended':
							sendEvent(videoTitle, 'ended', 100, 'Media/HTML5', duration);
							break;
						case 'timeupdate':
							trackProgress(videoTitle, percentage, options.percentageMarks, 'Media/HTML5', duration);
							break;
					}
				});
			});
		};

		const getYouTubeVideoId = function (url) {
			const regex = /(?:youtube\.com\/.*embed\/|youtube\.com\/.*v=|youtu\.be\/)([a-zA-Z0-9_-]+)/;
			const match = url.match(regex);
			return match ? match[1] : null;
		};

		const handleYouTubeEvent = function (event, videoId) {
			const player = youtubePlayers[videoId];

			if (! player || typeof player.getPlayerState !== 'function') {return;}

			const videoTitle = player.getVideoData()?.title || `YouTube Video ID: ${videoId}`;
			const duration = Math.round(player.getDuration());
			const currentTime = Math.round(player.getCurrentTime());
			const percentage = Math.round((currentTime / duration) * 100);

			switch (event.data) {
				case YT.PlayerState.PLAYING:
					sendEvent(videoTitle, 'play', percentage, 'youtube', duration);
					break;
				case YT.PlayerState.PAUSED:
					sendEvent(videoTitle, 'pause', percentage, 'youtube', duration);
					break;
				case YT.PlayerState.ENDED:
					sendEvent(videoTitle, 'ended', 100, 'youtube', duration);
					break;
				case YT.PlayerState.BUFFERING:
					// Optional: Handle buffering state
					break;
				default:
					break;
			}
		};

		const setupYouTubeTracking = function () {
			if (typeof YT === 'undefined' || typeof YT.Player === 'undefined') {
				return;
			}

			$('iframe[src*="youtube.com"]').each(function () {
				const iframe = this;

				if (! iframe.src.includes('enablejsapi=1')) {
					iframe.src += (iframe.src.includes('?') ? '&' : '?') + 'enablejsapi=1';
				}

				const videoId = getYouTubeVideoId(iframe.src);
				if (videoId && ! youtubePlayers[videoId]) {
					youtubePlayers[videoId] = new YT.Player(iframe, {
						events: {
							'onStateChange': (event) => handleYouTubeEvent(event, videoId)
						}
					});
				}
			});
		};

		const initYouTubeAPI = function () {
			if (typeof YT === 'undefined' || typeof YT.Player === 'undefined') {
				const tag = document.createElement('script');
				tag.src = 'https://www.youtube.com/iframe_api';
				document.body.appendChild(tag);

				window.onYouTubeIframeAPIReady = function () {
					setupYouTubeTracking();
				};
			} else {
				setupYouTubeTracking();
			}
		};

		const observeYouTubeIframes = function () {
			const observer = new MutationObserver(() => {
				setupYouTubeTracking();
			});
			observer.observe(document.body, { childList: true, subtree: true });
		};

		const setupVimeoTracking = function () {
			$('iframe[src*="vimeo.com"]').each(function () {
				const iframe = this;
				const player = new Vimeo.Player(iframe);

				player.getVideoTitle().then((title) => {
					player.getDuration().then((duration) => {
						vimeoPlayers[iframe.src] = {
							player,
							title,
							duration: Math.round(duration)
						};
					});
				});

				player.on('play', () => {
					const { title, duration } = vimeoPlayers[iframe.src];
					sendEvent(title, 'play', 0, 'vimeo', duration);
				});

				player.on('pause', () => {
					player.getCurrentTime().then((currentTime) => {
						const { title, duration } = vimeoPlayers[iframe.src];
						const percentage = Math.round((currentTime / duration) * 100);
						sendEvent(title, 'pause', percentage, 'vimeo', duration);
					});
				});

				player.on('ended', () => {
					const { title, duration } = vimeoPlayers[iframe.src];
					sendEvent(title, 'ended', 100, 'vimeo', duration);
				});

				player.on('timeupdate', (data) => {
					const { title, duration } = vimeoPlayers[iframe.src];
					const percentage = Math.round((data.seconds / duration) * 100);
					trackProgress(title, percentage, options.percentageMarks, 'vimeo', duration);
				});
			});
		};

		const initVimeoAPI = function () {
			if (typeof Vimeo === 'undefined') {
				const tag = document.createElement('script');
				tag.src = 'https://player.vimeo.com/api/player.js';
				const firstScriptTag = document.getElementsByTagName('script')[0];
				firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

				tag.onload = function () {
					setupVimeoTracking();
				};
			} else {
				setupVimeoTracking();
			}
		};

		const init = function () {
			setupHTML5Tracking();
			initYouTubeAPI();
			initVimeoAPI();
			observeYouTubeIframes();
		};

		init();
	};

	$(document).ready(function () {
		$.videoTracking();
	});
})(jQuery);
