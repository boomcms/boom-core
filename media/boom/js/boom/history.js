$.extend($.boom,
	/** @lends $.boom */
	{
	/**
	Boom history manager. Mostly used by the tag managers.
	* @class
	@static
	*/
	history : {

		_interval: 0,

		options : {
			checkInterval : 100
		},

		hashCallback : function( hash ) {

		},

		/**
		Route hash URLs to functions
		@param {Function} hashCallback Callback to invoke with current fragment identifier
		@param {Function} nohashCallback Callback to invoke if no current fragment identifier
		*/
		route : function(hashCallback, nohashCallback){

			this.hashCallback = hashCallback || function(){};
			this.nohashCallback = nohashCallback || function(){};

			this.current_hash = this._getHash();

			if (this.current_hash) {

				this.hashCallback(this.current_hash);
			}

			if (!this.current_hash) {
				this.nohashCallback();
			}

			this._checkHistory();
		},

		/**
		Invoke the hashCallback for the current fragment identifier.
		@param {String} hash fragment identifier from page URL
		@returns {Object} returns the hstory callback return value, to allow callback chaining.
		*/
		load : function(hash){

			this.current_hash = decodeURIComponent(hash.replace(/\?.*$/, ''));

			this._setHash(this.current_hash);

			var promise = this.hashCallback(this.current_hash);

			this._checkHistory();

			return promise;
		},

		/**
		Reload without changing the current fragment identifier.
		*/
		refresh : function(){

			this.load( this.current_hash );
		},

		/**
		Get the current fragment identifier.
		*/
		getHash : function(){
			return this._getHash();
		},

		/**
		Set the current fragment identifier.
		*/
		setHash : function(val){
			this._setHash(val);
		},


		/**
		Get the current fragment identifier from the window URL.
		*/
		_getHash : function(){
			return $.trim( top.location.hash.replace(/^.*#/, '') );
		},

		/**
		Set the current fragment identifier.
		@param {String} val A fragment identifier, without the # character.
		*/
		_setHash : function(val){

			top.location.hash = ( val == '' ) ? '' : '#' + val;
			$.boom.log( 'setting hash ' + top.location.hash );
		},

		/**
		FIXME: No idea what this does.
		*/
		_checkHistory : function(){

			var self = this;

			clearInterval(this.interval);

			this.interval = setInterval(function(){

				var current_hash = self._getHash();

				if(current_hash != self.current_hash) {
					self.current_hash = current_hash;
					self.hashCallback(current_hash.replace(/^#/, ''));
				}

			}, this.options.checkInterval);
		}
	}
});