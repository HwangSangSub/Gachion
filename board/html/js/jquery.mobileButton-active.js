//모바일에서 버튼 클릭시 클릭효과 속도 개선
$(window).load(function() {
	$('.mBtn').each(function(i,node) {
		new NoClickDelay(node);
	});
});

function NoClickDelay(el) {
	this.element = el;
	if( window.Touch ) this.element.addEventListener('touchstart', this, false);
}
  
NoClickDelay.prototype = {
	handleEvent: function(e) {
		switch(e.type) {
			case 'touchstart': this.onTouchStart(e); break;
			case 'touchmove': this.onTouchMove(e); break;
			case 'touchend': this.onTouchEnd(e); break;
		}
	},

	onTouchStart: function(e) {
		var obj = this.element;
		$(obj).addClass('active');

		e.preventDefault();
		this.moved = false;

		this.element.addEventListener('touchmove', this, false);
		this.element.addEventListener('touchend', this, false);
	},

	onTouchMove: function(e) {
		this.moved = true;
	},

	onTouchEnd: function(e) {
		var obj = this.element;
		$(obj).removeClass('active');

		this.element.removeEventListener('touchmove', this, false);
		this.element.removeEventListener('touchend', this, false);


		var theTarget = document.elementFromPoint(e.changedTouches[0].clientX, e.changedTouches[0].clientY);
		if(theTarget.nodeType == 3) theTarget = theTarget.parentNode;

		var theEvent = document.createEvent('MouseEvents');
		theEvent.initEvent('click', true, true);
		theTarget.dispatchEvent(theEvent);

	}
};

