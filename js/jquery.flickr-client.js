;
(function($) {

    function hkeys(hash) {
        var keys = [];
        $.each(hash, function(key, val){
            keys.push(key);
        });
        return keys;
    }

    function hvalues(hash) {
        var values = [];
        $.each(hash, function(key, val){
            values.push(val);
        });
        return values;
    }

    /**
     * return HashMap by HashMap's key sorted. 
     * 
     * @return
     */
    function ksort(params) {
        var keys = hkeys(params);
        keys.sort();

        sorted = {};
        $.each(keys, function(idx, key){
            if (!( params[key] == "" || params[key] == null )) {
                sorted[key] = params[key];
            }
        });
        
        return sorted;
    };
    
    /**
     * Convert HashMap to query string.
     */
    function hash2query(params) {
        var query = "";
        
        $.each(params, function(key, val){
            var _query = "";
            if ($.isArray(val)) {
                $.each(val, function(idx, _val){
                    if (query.length>0) _query += "&";
                    _query += key + "[]=" + val;
                });
            } else {
                _query = key + "=" + val;
            }
            
            if (query.length>0) query += "&";
            query += _query;
        });
        
        return query;
    };
    
    function FlickrClient(options) {
        this.options = $.extend(FlickrClient.prototype.DEFAULT_OPTIONS, options);
    }
    FlickrClient.prototype.DEFAULT_OPTIONS = {
        apiKey: "",
        apiSecret: "",
        userId: "",
        oauthToken: "",
        restEndpoint: "https://api.flickr.com/services/rest/",
        enablePathAlias: false
    };
    FlickrClient.prototype.SIZES = {
        "sq": "url_sq", // Square 75
        "q": "url_q",   // Square 150
        "t": "url_t",   // Thumbnail 
        "s": "url_s",   // Small 240
        "n": "url_n",   // Small 320
        "m": "url_m",   // Medium 500
        "z": "url_z",   // Medium 640
        "c": "url_c",   // Medium 800
        "l": "url_l",   // Large
        "h": "url_h",   // Large 1600
        "k": "url_k",   // Large 2048
        "o": "url_o"    // Original
    };
    FlickrClient.prototype.SIZE_KEYS   = hkeys(FlickrClient.prototype.SIZES); 
    FlickrClient.prototype.SIZE_VALUES = hvalues(FlickrClient.prototype.SIZES); 

    FlickrClient.prototype.SIZE_LABELS = {
        "sq": "Square",
        "q": "Square",
        "t": "Thumbnail",
        "s": "Small",
        "n": "Small",
        "m": "Medium",
        "z": "Medium",
        "c": "Medium",
        "l": "Large",
        "h": "Large",
        "k": "Large",
        "o": "Original"
    };
    FlickrClient.prototype.SIZE_LABEL_VALUES = hvalues(FlickrClient.prototype.SIZE_LABELS); 

    /**
     * execute request.
     * 
     * @param method
     *            Flickr method
     * @param params
     *            Send parameters
     * @param callback
     *            Success callback function
     * @param errorCallback
     *            Error callback function
     * @returns
     */
    FlickrClient.prototype.request = function(method, params, callback, errorCallback) {
        params = $.extend({
            method: method,
            format: "json",
            api_key: this.options.apiKey,
            auth_token: this.options.oauthToken,
            user_id: this.options.userId
        }, params);
        
        var type = this.getHttpMethod(method);
        var url = this.options.restEndpoint;
        var async = params['async'] || true;
        delete params['async'];
        
        var callbackName = 'flickr_callback_' + Math.floor( Math.random() * 100000000 );
        window[callbackName] = function(res) {
            if ( $.isFunction(callback) ) callback(res);
            delete window[callbackName];
        };
        
        params['jsoncallback'] = callbackName;
        // for disable cache
        params['_'] = $.now();
        params = ksort(params);
        if (this.options.apiSecret) {
            params['api_sig'] = this.generateSignature(params);
        }
        params['jsoncallback'] = '?';
        
        var ajaxOption = {
            type: type,
            url: url + '?' + hash2query(params),
            async: async,
            cache: true,
            dataType: "jsonp",
            jsonp: "jsoncallback",
            jsonpCallback: callbackName
        };
        if ( $.isFunction(errorCallback) ) {
            ajaxOption['error'] = errorCallback;
        }
        
        return $.ajax(ajaxOption);
    };
    
    /**
     * return Http method
     * 
     * @param method
     *            Flickr method
     * @return Http method
     */
    FlickrClient.prototype.getHttpMethod = function(method) {
        var type = "GET";
        return type;
    };

    /**
     * calculate api_sig
     * @return
     */
    FlickrClient.prototype.generateSignature = function(params) {
        var sig = "";
        $.each(params, function(key, val){
//            console.log("%s=%s", key, val);
            if (val == "") {
                delete params[key];
            } else {
                sig += String(key) + String(val);
            }
        });
        
        sig = this.options.apiSecret + sig;
        return $.md5(sig);
    };
    
    FlickrClient.prototype.photos_search = function(options, callback, errorCallback){
            return this.request("flickr.photos.search", options, callback, errorCallback);
    };
    FlickrClient.prototype.photos_getSizes = function(options, callback, errorCallback){
            return this.request("flickr.photos.getSizes", options, callback, errorCallback);
    };

    FlickrClient.prototype.photosets_getList = function(options, callback, errorCallback){
            return this.request("flickr.photosets.getList", options, callback, errorCallback);
    };
    FlickrClient.prototype.photosets_getPhotos = function(options, callback, errorCallback){
        return this.request("flickr.photosets.getPhotos", options, callback, errorCallback);
    };
    
    FlickrClient.prototype.tags_getListUser = function(options, callback, errorCallback) {
        return this.request("flickr.tags.getListUser", options, callback, errorCallback);
    };
    
    FlickrClient.prototype.getPhotoUrl = function(photo, size) {
        size = size || "m";

        if ( size != 'o' && !photo[FlickrClient.prototype.SIZES[size]] ) {
            var sizes = FlickrClient.prototype.SIZE_KEYS;
            var idx = sizes.indexOf(size);
            for ( var i=idx+1; i<sizes.length; i++ ) {
                var s = sizes[i];
                if ( photo[FlickrClient.prototype.SIZES[s]] ) {
                    return FlickrClient.prototype.getPhotoUrl(photo, s);
                }
            }
            return '';
        } else {
            return photo[FlickrClient.prototype.SIZES[size]];
        }
    };

    FlickrClient.prototype.getOwnerName = function(photo, photos) {
        var owner = null;
        if (this.options.enablePathAlias) {
            owner = photo['pathalias'];
        } else {
            owner = 'owner' in photo ? photo['owner'] : null;
            if (!owner && 'owner' in photos) {
                owner = photos['owner'];
            }
        }

        return owner;
    }

    FlickrClient.prototype.getPhotoPageUrl = function(photo, photos) {
        var owner = this.getOwnerName(photo, photos);
        var url = "http://www.flickr.com/photos/"+owner+"/"+photo['id'];
        return url;
    };

    FlickrClient.prototype.getPlayerUrl = function(photo, photos, playOf) {
        var owner = this.getOwnerName(photo, photos);
        var playPath = '';
        if ( playOf ) {
            playPath = '/in/' + playOf + '/player';
        } else {
            playPath = '/player';
        }
        var url = "http://www.flickr.com/photos/"+owner+"/"+photo['id']+playPath;
        return url;
    };

    $.FlickrClient = FlickrClient;
})(jQuery);
