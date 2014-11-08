function boomLink(target, title) {
	this.target = target? target : "";
	this.title = title? title : "";

	boomLink.prototype.isExternal = function() {
		return this.getTarget() === -1 || this.getTarget() === "";
	};

	boomLink.prototype.isInternal = function() {
		return ! isNaN(parseFloat(this.getTarget())) && isFinite(this.getTarget());
	};

	boomLink.prototype.getTarget = function() {
		return this.target;
	};

	boomLink.prototype.getTitle = function() {
		return this.title;
	};
};