function boomLink(target) {
	this.target = target? target : "";

	boomLink.prototype.isExternal = function() {
		return this.getTarget() === -1 || this.getTarget() === "";
	};

	boomLink.prototype.isInternal = function() {
		return ! isNaN(parseFloat(this.getTarget())) && isFinite(this.getTarget());
	};

	boomLink.prototype.getTarget = function() {
		return this.target;
	};
};