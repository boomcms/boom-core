$.extend($.boom,
	/** @lends $.boom */
	{
	/**
	Boom logging. Extends console logging.
	*/
	log : function(type, msg){

		if (!$.boom.config.logs.show || ! window.console) return;

		if (msg === undefined) {
			msg = type;
			type = 'info';
		}

		if ($.boom.config.logs.showTimes){

			$.boom.config.logs.times.push((new Date).getTime());
			var time = $.boom.config.logs.times[$.boom.config.logs.times.length - 1] - $.boom.config.logs.times[$.boom.config.logs.times.length - 2];

			if (time) $.boom.config.logs.totalTime += parseInt( time, 10 );
			else time = 0;

			msg += ' : ' + time + 'ms : ' + ($.boom.config.logs.totalTime) + 'ms';
		}

		var log;

		switch(type.toLowerCase()) {
			case 'debug': log = window.console.debug; break;
			case 'error': log = window.console.error; break;
			case 'warning': log = window.console.warning; break;
			case 'info': log = window.console.info; break;
			default: log = window.console.log;
		}

		// FIXME for webkit
		//log.apply(this, [msg]);
		console.log(msg);
	}

});