/**
@function
*/
String.prototype.ucfirst = function() {
    return this.substr(0, 1).toUpperCase() + this.substr(1, this.length);
};

/**
@function
*/
String.prototype.toInt = function() {
    return parseInt(this, 10);
};

String.prototype.toQueryParams = function() {
    var params = {},
        queryArray = this.split('&');

    for (var i = 0; i < queryArray.length; i++) {
        var q = queryArray[i].split('=');

        params[q[0]] = q[1];
    }

    return params;
};
