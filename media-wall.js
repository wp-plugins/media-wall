(function() {
	iFrameResize({
				heightCalculationMethod: 'max', 
				enablePublicMethods: true,
				messageCallback: function(msgObj) {
					var width = document.getElementById('grabimo_media_wall').offsetWidth;
					var submitButton = document.getElementById('grabimo_media_wall_submit')
					if (submitButton) {
						var contentWidth = msgObj.message;
						var left = 20 + 0.5 * (width - contentWidth);
						submitButton.style.left = left + "px";
					}
				}
		}, '#grabimo_media_wall');
}());

function showGrabimoCollectionMode(bizAlias, font) {
	var showTitle = 0;
	font = (typeof font !== 'undefined') ? decodeURIComponent(font) : '';
	grab_multimedia_feedback.startFlow(bizAlias, font, showTitle);
}
