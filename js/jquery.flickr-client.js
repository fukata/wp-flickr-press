;
/**
 * jquery.flickr-client.js
 * version: 1.2.0
 */
(function($) {

  /**
   * generate oauth nonce characters.
   *
   * @param {number} length
   * @return {string}
   */
  function generate_oauth_nonce(length) {
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    for(var i = 0; i < length; i++) {
      text += possible.charAt(Math.floor(Math.random() * possible.length));
    }
    return text;
  }

  /**
   * Return keys of hash.
   *
   * @param {Object} hash
   * @return {Array.<string>}
   */
  function hkeys(hash) {
    var keys = [];
    $.each(hash, function(key, val){
      keys.push(key);
    });
    return keys;
  }

  /**
   * Return values of hash.
   *
   * @param {Object} hash
   * @return {Array}
   */
  function hvalues(hash) {
    var values = [];
    $.each(hash, function(key, val){
      values.push(val);
    });
    return values;
  }

  /**
   * Sort HashMap by key
   *
   * @param {Object} params
   * @return {Object}
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
   *
   * @param {Object} params
   * @return {string}
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

  /**
   * Constructor
   *
   * @param {Object} options
   *  see FlickrClient.prototype.DEFAULT_OPTIONS
   */
  function FlickrClient(options) {
    this.options = $.extend(FlickrClient.prototype.DEFAULT_OPTIONS, options);
  }
  FlickrClient.prototype.DEFAULT_OPTIONS = {
    apiKey: "",
    apiSecret: "",
    userId: "",
    authToken: "",
    restEndpoint: "https://api.flickr.com/services/rest/",
    enablePathAlias: false,
    debugMode: false,
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
    "o": "url_o"  // Original
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
   * @param {string} method
   *      Flickr method
   * @param {Object} params
   *      Send parameters
   * @param {function} callback
   *      Success callback function
   * @param {function} errorCallback
   *      Error callback function
   */
  FlickrClient.prototype.request = function(method, params, callback, errorCallback) {
    params = $.extend({
      method: method,
      format: "json",
      api_key: this.options.apiKey,
      oauth_token: this.options.oauthToken,
      user_id: this.options.userId
    }, params);
    if (params.oauth_token) {
      params['oauth_consumer_key'] = this.options.apiKey;
      params['oauth_timestamp'] = Math.floor(new Date().getTime()/1000);
      params['oauth_nonce'] = generate_oauth_nonce(32);
      params['oauth_signature_method'] = 'HMAC-SHA1';
    }

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
   * @param {string} method
   *      Flickr method
   * @return Http method
   */
  FlickrClient.prototype.getHttpMethod = function(method) {
    var type = "GET";
    return type;
  };

  /**
   * Calculate api_sig
   *
   * @param {Object} params
   * @return {string}
   */
  FlickrClient.prototype.generateSignature = function(params) {
    var sig = "";
    $.each(params, function(key, val){
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

  /**
   * Return specified size photo url.
   *
   * @param {Object} photo
   * @param {string} size
   * @return {string}
   */
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

  /**
   * Search owner name from photo or photos.
   *
   * @param {Object} photo
   * @param {Array.<Object>} photos
   * @return {string}
   */
  FlickrClient.prototype.getOwnerName = function(photo, photos) {
    var owner = null;
    if (this.options.enablePathAlias) {
      owner = photo['pathalias'];
    }

    // pathalias return null if not yet set your address.
    if (!owner) {
      owner = 'owner' in photo ? photo['owner'] : null;
      if (!owner) {
        if ('owner' in photos) {
          owner = photos['owner'];
        } else if ('pathalias' in photo) {
          owner = photo['pathalias']
        } else if ('pathalias' in photos) {
          owner = photos['pathalias']
        }
      }
    }

    if (this.options.debugMode) {
      console.log("DEBUG(getOwnerName): owner=%s, photo=%s, photos=%s", owner, JSON.stringify(photo), JSON.stringify(photos));
    }

    return owner;
  }

  /**
   * Calculate include owner name in photo page url.
   *
   * @param {Object} photo
   * @param {Array.<Object>} photos
   * @return {string}
   */
  FlickrClient.prototype.getPhotoPageUrl = function(photo, photos) {
    var owner = this.getOwnerName(photo, photos);
    var url = "https://www.flickr.com/photos/"+owner+"/"+photo['id'];
    return url;
  };

  /**
   * Calculate player url.
   *
   * @param {Object} photo
   * @param {Array.<Object>} photos
   * @param {string} playOf
   * @return {string}
   */
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

  /**
   * Return width, height as object.
   * @params {Object} photo
   * @params {string} size
   * @return {Object}
   */
  FlickrClient.prototype.getPhotoWH = function(photo, size) {
    size = size || "m";
    return {
      width: photo['width_' + size],
      height: photo['height_' + size],
    };
  };

  $.FlickrClient = FlickrClient;
})(jQuery);
