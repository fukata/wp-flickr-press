(function($){

    var fp = null;

    // custom state : this controller contains your application logic
    wp.media.controller.FlickrPress = wp.media.controller.State.extend({
        initialize: function(){
            console.log('controller.FlickrPress.initialize');
            // this model contains all the relevant data needed for the application
            this.props = new Backbone.Model({ custom_data: [] });
            this.props.on( 'change:custom_data', this.refresh, this );

            var _params = $("#wpfp_params");
            var params = {
                apiKey:             _params.data('api_key'),
                apiSecret:          _params.data('api_secret'),
                userId:             _params.data('user_id'),
                oauthToken:         _params.data('oauth_token'),
                enablePathAlias:    _params.data('enable_path_alias') == '1',
                defaultLink:        _params.data('default_link'),
                defaultTarget:      _params.data('default_target'),
                defaultSize:        _params.data('default_size'),
                defaultAlign:       _params.data('default_align'),
                defaultFileUrlSize: _params.data('default_file_url_size'),
                insertTemplate:     _params.data('insert_template')
            };
            fp = {
                flickr: new $.FlickrClient({
                    apiKey:          params.apiKey,
                    apiSecret:       params.apiSecret,
                    userId:          params.userId,
                    oauthToken:      params.oauthToken,
                    enablePathAlias: params.enablePathAlias
                }),
                currentPhotos: null,
                search: null
            };
            fp.params = params;
            fp.options = {
                perpage : 100,
                extras : fp.flickr.SIZE_VALUES.join(',') + ",path_alias",
                sort : "date-posted-desc",
                thumbnailSize : "sq",
            };

            fp['util'] = {
                generateHtml: function(photo, input) {
                    var html = fp.params.insertTemplate;
                    if (html.indexOf('[img]') >= 0) {
                        html = html.replace(/\[img\]/g, fp.util.generateHtmlImg(photo, input));
                    }
                    if (html.indexOf('[title]') >= 0) {
                        html = html.replace(/\[title\]/g, fp.util.generateHtmlTitle(photo, input));
                    }
                    if (html.indexOf('[url]') >= 0) {
                        html = html.replace(/\[url\]/g, fp.util.generateHtmlUrl(photo, input));
                    }
                    if (html.indexOf('[null]') >= 0) {
                        html = html.replace(/\[null\]/g, '');
                    }
        
                    return html;
                },
                generateHtmlImg: function(photo, input) {
                    var size = 'size' in input ? input['size'] : fp.params.defaultSize;
                    var link = fp.flickr.getPhotoPageUrl(photo, photo);
                    var target = 'target' in input ? input['target'] : fp.params.defaultTarget;
                    target = target ? ' target="' + target + '"' : '';
                    var align = 'align' in input ? input['alignment'] : fp.params.defaultAlign;
                    var imageClazz = '';

                    var alt = fp.util.escAttr( photo.title );
                    var src = fp.flickr.getPhotoUrl(photo, size);
                    var clazz = "";
                    var close = $(this).data('close') == '1';

                    if (align) {
                        clazz = 'align' + align;
                    }
                    if (imageClazz) {
                        clazz = clazz ? clazz + ' ' + imageClazz : imageClazz;
                    }
                    clazz = clazz ? ' class="' + clazz + '"' : '';

                    //var rel = _removeInvalidLinkChars( $('input[name="inline-link-rel"]').val() );
                    //if ( rel ) {
                    //    rel = ' rel="' + rel + '"';
                    //}
                    var rel = '';

                    //var aclazz = _removeInvalidLinkChars( $('input[name="inline-link-clazz"]').val() );
                    //if ( aclazz ) {
                    //    aclazz = ' class="' + aclazz + '"';
                    //}
                    var aclazz = '';

                    var html = '<img src="' + src + '" alt="' + alt + '"' + clazz + '/>';
                    if (link) {
                        var title = ' title="' + alt + '"';
                        html = '<a href="' + link + '"' + target + aclazz + rel + title + '>' + html + '</a>';
                    }

                    return html;
                },
                generateHtmlTitle: function(photo, input) {
                    return photo.title;
                },
                generateHtmlUrl: function(photo, input) {
                    var to = 'to' in input ? input['to'] : fp.params.defaultLink;
                    var url;
                    if ( to == 'urlnone' ) {
                        to = '';
                    } else if ( to == 'urlpage' ) {
                        to = fp.flickr.getPhotoPageUrl(photo, photo);
                    } else if ( to == 'urlfile' ) {
                        var size = 'size' in input ? input['size'] : fp.params.defaultSize;
                        to = fp.flickr.getPhotoUrl(photo, size);
                    }

                    return to;
                },
                escAttr: function(str) {
                    if (!str || str == '') return '';
                    if (!/[&<>"]/.test(str)) return str;

                    return str.replace(/&/g, '&amp;')
                              .replace(/</g, '&lt;')
                              .replace(/>/g, '&gt;')
                              .replace(/"/g, '&quot;')
                              ;
                },
                _removeInvalidLinkChars: function(str) {
                    return str.replace(/[^0-9a-zA-Z\[\]\s_]+/g,'');
                }
            };
        },
        
        // called each time the model changes
        refresh: function() {
            console.log('controller.FlickrPress.refresh');
            // update the toolbar
            this.frame.toolbar.get().refresh();
        },
        
        // called when the toolbar button is clicked
        customAction: function(){
            console.log('controller.FlickrPress.customAction');
        },
    });

    // custom toolbar : contains the buttons at the bottom
    wp.media.view.Toolbar.FlickrPress = wp.media.view.Toolbar.extend({
        initialize: function() {
            _.defaults( this.options, {
                close: false,
                items: {
                    custom_event: {
                        text: wp.media.view.l10n.insertIntoPost, // added via 'media_view_strings' filter,
                        style: 'primary',
                        priority: 80,
                        requires: false,
                        click: this.customAction
                    }
                }
            });

            wp.media.view.Toolbar.prototype.initialize.apply( this, arguments );
        },
        // called each time the model changes
        refresh: function() {
            console.log('Toolbar.FlickrPress refresh');
            // you can modify the toolbar behaviour in response to user actions here
            // disable the button if there is no custom data
            var customData = this.controller.state().props.get('custom_data');
            this.get('custom_event').model.set( 'disabled', customData.length == 0 );
           
            // call the parent refresh
            wp.media.view.Toolbar.prototype.refresh.apply( this, arguments );
        },
        // triggered when the button is clicked
        customAction: function(){
            console.log('Toolbar.FlickrPress customAction');

            var input = this.controller.state().props.get('input');
            var photos = this.controller.options.selection.models;
            $.each(photos, function(i, _photo){
                var photo = _photo.attributes;
                console.log(photo);
                var html = fp.util.generateHtml(photo, input);
                var win = parent || top;
                win.fp_send_to_editor(html, false);
            });

            this.controller.options.selection.reset();
            $('#wpfp li.photo.selected').removeClass('selected').removeData('order');

            this.controller.modal.close();
        }
    });

    wp.media.view.FlickrPressSearch = wp.media.view.Search.extend({
        name: 'hoge',
        propertyName: function() { return 'wpfp_' + this.name; },
        events: {
            input: 'update',
            update: 'update'
        },
        render: function() {
            this.el.value = this.model.escape( this.propertyName() );
            return this;
        },
        search: function( event ) {
            if ( event.target.value ) {
                this.model.set( this.propertyName(), event.target.value );
            } else {
                this.model.unset(this.propertyName());
            }
        },
        update: function( event ) {
            console.log('FlickrPressSearch.update. target=%s, value=%s', this.propertyName(), event.target.value);
            this.model.set( this.propertyName(), event.target.value );
        }

    });

    wp.media.view.FlickrPressAttachmentFilters = wp.media.view.AttachmentFilters.extend({
        name: 'hoge',
        propertyName: function() { return 'wpfp_' + this.name; },
        change: function() {
            console.log('FlickrPressAttachmentFilters.change', this.name, this.el.value, this.model.get(this.propertyName()));
            var filter = this.filters[ this.el.value ];
            if ( filter ) {
                this.model.set( this.propertyName(), this.el.value );
            }
        },
        select: function() {
            console.log('FlickrPressAttachmentFilters.select', this.name, this.el.value, this.model.get(this.propertyName()));
            this.$el.val( this.model.get(this.propertyName()) || this.el.value );
            this.model.set( this.propertyName(), this.el.value );
        }

    });

    wp.media.view.FlickrPressSearchTypeFilters = wp.media.view.FlickrPressAttachmentFilters.extend({
        name: 'type',
        className: 'search-type-filters',
        createFilters: function() {
            console.log('FlickrPressSearchTypeFilters.createFilters', this.filters);

            this.filters = {
                recent: {
                    text:  wp.media.view.l10n.wpfpSearchTypeFilterRecent,
                    props: {
                        orderby: 'date',
                        order:   'DESC'
                    },
                    priority: 10
                },
                photosets: {
                    text:  wp.media.view.l10n.wpfpSearchTypeFilterAlbum,
                    props: {
                        orderby: 'date',
                        order:   'DESC'
                    },
                    priority: 10
                },
                advanced: {
                    text:  wp.media.view.l10n.wpfpSearchTypeFilterAdvanced,
                    props: {
                        orderby: 'date',
                        order:   'DESC'
                    },
                    priority: 10
                },

            };
        },
    });

    wp.media.view.FlickrPressSearchSortFilters = wp.media.view.FlickrPressAttachmentFilters.extend({
        name: 'sort',
        className: 'search-sort-filters',
        createFilters: function() {
            this.filters = {
                'date-posted-desc': {
                    text:  wp.media.view.l10n.wpfpSearchSortFilterPostedDESC,
                    priority: 10
                },
                'date-posted-asc': {
                    text:  wp.media.view.l10n.wpfpSearchSortFilterPostedASC,
                    priority: 10
                },
                'date-taken-desc': {
                    text:  wp.media.view.l10n.wpfpSearchSortFilterTakenDESC,
                    priority: 10
                },
                'date-taken-asc': {
                    text:  wp.media.view.l10n.wpfpSearchSortFilterTakenASC,
                    priority: 10
                },
                'interestingness-desc': {
                    text:  wp.media.view.l10n.wpfpSearchSortFilterInterestingnessDESC,
                    priority: 10
                },
                'interestingness-asc': {
                    text:  wp.media.view.l10n.wpfpSearchSortFilterInterestingnessASC,
                    priority: 10
                },
            };
        },
    });

    wp.media.view.FlickrPressSearchPhotosetFilters = wp.media.view.FlickrPressAttachmentFilters.extend({
        name: 'photoset',
        className: 'search-photoset-filters',
        initialized: false,
        filters: {
            '': {
                text: 'Loading...',
                props: {},
                priority: 10
            }
        },
        createFilters: function() {
            console.log('FlickrPressSearchPhotosetFilters.createFilters');
            if ( !this.initialized ) {
                this.initialized = true;
                var that = this;
                fp.flickr.photosets_getList({}, function(res) {
                    that.filters = {};
                    var photosets = res.photosets;
                    for ( var i = 0; i < photosets.photoset.length; i++) {
                        var photoset = photosets.photoset[i];
                        that.filters[photoset.id] = {
                            text:  photoset.title._content,
                            props: {
                                orderby: 'date',
                                order:   'DESC'
                            },
                            priority: 10
                        };
                    }
                    console.log("initialize filters=", that.filters);
                    that.initialize();
                }, function(request, textStatus, errorThrown){
                    that.filters = {
                        '': {
                            text: 'LOAD ERROR',
                            props: {},
                            priority: 10
                        }
                    };
                    that.initialize();
                });
            }
        },
    });

    wp.media.view.FlickrPressSearchTagFilter = wp.media.view.FlickrPressSearch.extend({
        name: 'tag',
        tagName:   'input',
        className: 'search search-tag-filter',
        attributes: {
            type:        'search',
            placeholder: wp.media.view.l10n.wpfpSearchTagFilterPlaceholder
        },
    });

    wp.media.view.FlickrPressSearchKeywordFilter = wp.media.view.FlickrPressSearch.extend({
        name: 'keyword',
        tagName:   'input',
        className: 'search search-keyword-filter',

        attributes: {
            type:        'search',
            placeholder: wp.media.view.l10n.wpfpSearchKeywordFilterPlaceholder
        },
    });

    wp.media.view.FlickrPressSearchButton = wp.media.view.Button.extend({
        className: 'search-button',
        defaults: {
            text: wp.media.view.l10n.wpfpSearchButton,
            style: 'primary',
        },
        click: function(event){
            console.log('SearchButton click. type=%s, sort=%s, photoset=%s, tag=%s, keyword=%s',
                this.controller.state().props.get('wpfp_type'),
                this.controller.state().props.get('wpfp_sort'),
                this.controller.state().props.get('wpfp_photoset'),
                this.controller.state().props.get('wpfp_tag'),
                this.controller.state().props.get('wpfp_keyword')
            );

            var that = this;
            var type = this.controller.state().props.get('wpfp_type');
            console.log('Search. type=%s', type);
            var searchFn;
            var contentFrame = this.controller.content.view.views.get('.media-frame-content')[0];
            var sort = this.controller.state().props.get('wpfp_sort') || fp.options.sort;
            if ( type == 'photosets' ) {
                var options = {
                    per_page: fp.options.perpage,
                    extras:   fp.options.extras
                };

                options['photoset_id'] = this.controller.state().props.get('wpfp_photoset');

                searchFn = this.generateIncrementalSearchFn(options, function(opts) {
                    fp.flickr.photosets_getPhotos(opts, function(res){
                        res.photos = res.photoset;
                        contentFrame.updateContent(res, searchFn);
                    }, that.generateSearchFnErrorCallback(opts, contentFrame));
                });
            } else if ( type == 'advanced' ) {
                var options = {
                    per_page: fp.options.perpage,
                    extras:   fp.options.extras,
                    sort:     sort
                };

                var keyword = this.controller.state().props.get('wpfp_keyword');
                if ( keyword ) {
                    options['text'] = keyword;
                }

                var tag = this.controller.state().props.get('wpfp_tag');
                if ( tag ) {
                    var splited = tag.split(',');
                    var joined = [];
                    for ( var i = 0; i < splited.length; i++) {
                        var s = splited[i].replace(/(^\s+)|(\s+$)/g, "");
                        if (s) {
                            joined.push(s);
                        }
                    }
                    options["tags"] = joined.join();
                }

                searchFn = this.generateIncrementalSearchFn(options, function(opts) {
                    fp.flickr.photos_search(opts, function(res){
                        contentFrame.updateContent(res, searchFn);
                    }, that.generateSearchFnErrorCallback(opts, contentFrame));
                });
            } else {
                var options = {
                    per_page: fp.options.perpage,
                    extras:   fp.options.extras,
                    sort:     sort
                };
                searchFn = this.generateIncrementalSearchFn(options, function(opts) {
                    fp.flickr.photos_search(opts, function(res){
                        contentFrame.updateContent(res, searchFn);
                    }, that.generateSearchFnErrorCallback(opts, contentFrame));
                });
            }

            contentFrame.initContent();
            searchFn();
        },
        generateIncrementalSearchFn: function(options, callback) {
            options['page'] = 0;
            return function() {
                console.log('======================= call searchFn =======================');
                options['page']++;
                console.log(options);
                callback(options);
            };
        },
        generateSearchFnErrorCallback: function(options, contentFrame) {
            return function(request, textStatus, errorThrown) {
                console.log('======================= ERROR ======================= ');
                console.log('request: ', request);
                console.log('textStatus: ', textStatus);
                console.log('errorThrown: ', errorThrown);

                options['page']--;
                console.log(options);
                contentFrame.displayErrorContent();
            };
        }
    });

    wp.media.view.FlickrPressDetails = wp.media.View.extend({
        tagName:   'div',
        className: 'photo-details',

        template:  wp.media.template('wpfp-photo-detail'),

        events: {
            change: 'change',
        },

        initialize: function() {
            console.log('FlickrPressDetails.initialize');
            this.details( this.model, this.controller.state().get('selection') );
        },
        dispose: function() {
            console.log('FlickrPressDetails.dispose');
            wp.media.View.prototype.dispose.apply( this, arguments );
            return this;
        },
        render: function() {
            console.log('FlickrPressDetails.render');
            this.controller.state().props.set('input', {});
            var options = _.defaults( this.model.toJSON(), {
                name: '',
            });
            options['fp'] = {
                size_keys:         fp.flickr.SIZE_KEYS,
                size_labels:       fp.flickr.SIZE_LABELS,
                size_label_values: fp.flickr.SIZE_LABEL_VALUES
            };
            options['params'] = fp.params;
            console.log(options);

            this.views.detach();
            this.$el.html( this.template(options) );
            return this;
        },
        details: function( model, collection ) {
            console.log('FlickrPressDetails.details');
            var selection = this.controller.options.selection,
                details;

            if ( selection !== collection )
                return;

            details = selection.single();
            this.$el.toggleClass( 'details', details === this.model );
        },
        change: function(event) {
            console.log('FlickrPressDetails.change: name=%s, value=%s', event.target.name, event.target.value);
            var input = this.controller.state().props.get('input');
            input[event.target.name] = event.target.value;
            console.log(input);
            this.controller.state().props.set('input', input);
        }
    });
    
    // custom content : this view contains the main panel UI
    wp.media.view.FlickrPress = wp.media.View.extend({
        id: 'wpfp',
        tagName: 'div',
        className: 'flickr-press',

        templateContainer:  wp.media.template('wpfp-photo-container'),
        templateResult:  wp.media.template('wpfp-photo-result'),

        initializedContainer: false,

        // bind view events
        events: {
            'input':  'update',
            'keyup':  'update',
            'change': 'change',
        },
        initialize: function() {
            console.log("view.FlickrPress.initialize");

            this.createToolbar();
            this.updateContent();
            this.createSidebar();

            this.model.set('wpfp_type', 'recent');
            this.model.set('wpfp_sort', 'date-posted-desc');
            this.model.set('wpfp_photoset', '');
            this.model.set('wpfp_tag', '');
            this.model.set('wpfp_keyword', '');
            //this.collection.on( 'add remove reset', this.updateContent, this );

            var that = this;
            $(document).off('click', '#wpfp .result-container .result .photos > li')
                       .on('click', '#wpfp .result-container .result .photos > li', function(e){
                that.selectThumbnail( e, $(this) );
            });
        },
        render: function(){
            console.log("view.FlickrPress.render");
            this.toolbar.get( 'search-button' ).click();
            return this;
        },
        update: function( event ) {
            console.log("view.FlickrPress.update class=%s, value=%s", event.target, event.target.value);
        },
        change: function() {
            console.log("view.FlickrPress.change class=%s, value=%s", event.target, event.target.value);
            if ( $(event.target).hasClass('search-type-filters') ) {
                this.updateToolbar();
            }
        },
        createToolbar: function() {
            this.toolbar = new wp.media.view.Toolbar({
                controller: this.controller
            });
            this.views.add( this.toolbar );

            this.toolbar.set( 'search-button', new wp.media.view.FlickrPressSearchButton({
                controller: this.controller,
                model:      this.controller.state().props,
                priority:   -80
            }).render() );

            this.toolbar.set( 'search-type-filters', new wp.media.view.FlickrPressSearchTypeFilters({
                controller: this.controller,
                model:      this.controller.state().props,
                priority:   -80
            }).render() );

            this.updateToolbar();
        },
        updateToolbar: function() {
            if ( this.toolbar ) {
                _.each(['search-sort-filters', 'search-photoset-filters','search-tag-filter','search-keyword-filter'], function( key ) {
                    this.toolbar.unset(key);
                }, this );
            }

            var searchType = this.model.get('wpfp_type');
            console.log("updateToolbar. searchType=%s", searchType);
            if ( searchType === 'photosets' ) {
                this.toolbar.set( 'search-photoset-filters', new wp.media.view.FlickrPressSearchPhotosetFilters({
                    controller: this.controller,
                    model:      this.controller.state().props,
                    priority:   -80
                }).render() );
            } else if ( searchType === 'advanced' ) {

                this.toolbar.set( 'search-sort-filters', new wp.media.view.FlickrPressSearchSortFilters({
                    controller: this.controller,
                    model:      this.controller.state().props,
                    priority:   -80
                }).render() );

                this.toolbar.set( 'search-tag-filter', new wp.media.view.FlickrPressSearchTagFilter({
                    controller: this.controller,
                    model:      this.controller.state().props,
                    priority:   -80
                }).render() );

                this.toolbar.set( 'search-keyword-filter', new wp.media.view.FlickrPressSearchKeywordFilter({
                    controller: this.controller,
                    model:      this.controller.state().props,
                    priority:   -80
                }).render() );
            } else {
                this.toolbar.set( 'search-sort-filters', new wp.media.view.FlickrPressSearchSortFilters({
                    controller: this.controller,
                    model:      this.controller.state().props,
                    priority:   -80
                }).render() );
            }
        },
        createSidebar: function() {
            var options = this.controller.options,
                selection = options.selection,
                sidebar = this.sidebar = new wp.media.view.Sidebar({
                    controller: this.controller
                });

            this.views.add( sidebar );
//            if ( this.controller.uploader ) {
//                sidebar.set( 'uploads', new media.view.UploaderStatus({
//                    controller: this.controller,
//                    priority:   40
//                }) );
//            }
//
            selection.on( 'selection:single', this.createSingle, this );
            selection.on( 'selection:unsingle', this.disposeSingle, this );
            selection.on( 'add', this.updateSingle, this );

            if ( selection.single() ) {
                this.createSingle();
            }
        },
        updateSingle: function() {
            var selection = this.controller.options.selection; 
            console.log('FlickrPress.updateSingle');
            
            if ( selection.single() && selection.length > 1 ) {
                this.disposeSingle()
            }
        },
        createSingle: function() {
            var sidebar = this.sidebar,
                single = this.controller.options.selection.single();
            console.log('FlickrPress.createSingle', single);

            sidebar.set( 'details', new wp.media.view.FlickrPressDetails({
                controller: this.controller,
                model:      single,
                priority:   80
            }) );

//            sidebar.set( 'compat', new media.view.AttachmentCompat({
//                controller: this.controller,
//                model:      single,
//                priority:   120
//            }) );
//
//            if ( this.options.display ) {
//                sidebar.set( 'display', new media.view.Settings.AttachmentDisplay({
//                    controller:   this.controller,
//                    model:        this.model.display( single ),
//                    attachment:   single,
//                    priority:     160,
//                    userSettings: this.model.get('displayUserSettings')
//                }) );
//            }
        },
        disposeSingle: function() {
            console.log('FlickrPress.disposeSingle');
            var sidebar = this.sidebar;
            sidebar.unset('details');
            //sidebar.unset('compat');
            //sidebar.unset('display');
        },
        initContent: function() {
            $('.flickr-press .result-container .result .photos').empty();
            $('.flickr-press .result-container .result .error').hide();
            $('.flickr-press .result-container .result .loader').show();
            $('.flickr-press .result-container .result .more-btn').hide();
            this.controller.options.selection.reset();
            this.model.unset('result');
            this.model.unset('result_photos_photo');
        },
        displayErrorContent: function() {
            console.log('displayErrorContent');
            $('.flickr-press .result-container .result .loader').hide();
            $('.flickr-press .result-container .result .error').show();
            if ( $('.flickr-press .result-container .result .photos .photo').size() > 0 ) {
                $('.flickr-press .result-container .result .more-btn').show();
            } else {
                $('.flickr-press .result-container .result .more-btn').hide();
            }
        },
        updateContent: function(res, searchFn) {
            console.log(res);
            if ( !this.initializedContainer && !res ) {
                this.initializedContainer = true;

                this.$el.append( this.templateContainer({}) );

                return;
            }

            $('.flickr-press .result-container .result .loader').hide();

            if ( res.stat !== 'ok' ) {
                console.log('Error flickr search.', res);
                this.displayErrorContent();
                return;
            }
            $('.flickr-press .result-container .result .error').hide();

            // cache
            this.model.set('result', res);
            var cachePhotos = this.model.get('result_photos_photo') || [];
            this.model.set('result_photos_photo', cachePhotos.concat(res.photos.photo));

            // display
            var data = res;
            data['fp'] = {
                thumbnailSize: fp.options.thumbnailSize,
                lastIndex:     $('.flickr-press .result-container .result .photos .photo').size() || 0
            };
            $('.flickr-press .result-container .result .photos').append( this.templateResult(data) );

            // more button
            if ( res.photos.page < res.photos.pages ) {
                $('.flickr-press .result-container .result .more-btn').show();
                $(document).off('click', '.flickr-press .result-container .result .more-btn')
                           .on('click', '.flickr-press .result-container .result .more-btn', function(){
                    $(this).hide();
                    $('.flickr-press .result-container .result .loader').show();
                    searchFn();
                });
            }
        },
        selectThumbnail: function(e, $thubmnail) {
            var idx = $thubmnail.data('idx'),
                photo = this.model.get('result_photos_photo')[idx];
            console.log("selectThumbnail. idx=%s", idx);
            console.log(this.controller.options.selection);

            if (e.ctrlKey || e.metaKey) {
                var modeAdd = false;
                if ( $thubmnail.hasClass('selected') ) {
                    $thubmnail.removeClass('selected').removeData('order');
                    this.controller.options.selection.remove( photo );
                } else {
                    modeAdd = true;
                    this.controller.options.selection.add( photo );
                }

                var order = 0;
                this.sortedInsertPhotos().each(function(){
                    $(this).data('order', order);
                    $(this).find('.order').text(order+1);
                    order++;
                });
                console.log("order=%s", order);
                if ( modeAdd ) {
                    $thubmnail.addClass('selected')
                              .data('order', order);
                    $thubmnail.find('.order').text(order+1);
                }
            } else {
                this.controller.options.selection.reset();
                if ( $thubmnail.hasClass('selected') ) {
                    if ($('#wpfp li.photo.selected').size() > 1) {
                        $('#wpfp li.photo.selected').removeClass('selected').removeData('order');
                    } else {
                        $thubmnail.removeClass('selected').removeData('order');
                    }
                } else {
                    $('#wpfp li.photo.selected').removeClass('selected').removeData('order');
                }
                $thubmnail.addClass('selected').data('order', 0);
                $thubmnail.find('.order').text(1);
                this.controller.options.selection.add( photo );
            }

            var idxs = $('#wpfp li.photo.selected').map(function(){
                return $(this).data('idx');
            });
            this.controller.state().props.set('custom_data', idxs);
        },
        sortedInsertPhotos: function() {
            return $('#wpfp li.photo.selected').sort(function(a, b){
                var o1 = $(a).data('order') || 0;
                var o2 = $(b).data('order') || 0;
                if( o1 < o2 ) return -1;
                if( o1 > o2 ) return 1;
                return 0;
            });
        },
    });

    var oldMediaFrame = wp.media.view.MediaFrame.Post;
    wp.media.view.MediaFrame.Post = oldMediaFrame.extend({
        initialize: function() {
            console.log("MediaFrame initialize");
            oldMediaFrame.prototype.initialize.apply( this, arguments );
            
            this.states.add([
                new wp.media.controller.FlickrPress({
                    id:         'wpfp-action',
                    menu:       'default', // menu event = menu:render:default
                    content:    'wpfp',
                    title:      wp.media.view.l10n.wpfpTitle, // added via 'media_view_strings' filter
                    priority:   200,
                    toolbar:    'main-wpfp-action', // toolbar event = toolbar:create:main-my-action
                    type:       'link'
                })
            ]);

            this.on( 'toolbar:create:main-insert',      this.createToolbar,            this );
            this.on( 'content:render:wpfp',             this.customContent,            this );
            this.on( 'toolbar:create:main-wpfp-action', this.createFlickrPressToolbar, this );
            this.on( 'toolbar:render:main-wpfp-action', this.renderFlickrPressToolbar, this );
            this.on( 'toolbar:render:main-insert',      this.mainInsertToolbar,        this );
        },
        createFlickrPressToolbar: function(toolbar){
            console.log("MediaFrame createFlickrPressToolbar");
            toolbar.view = new wp.media.view.Toolbar.FlickrPress({
                controller: this
            });
        },

        customContent: function(){
            console.log("MediaFrame customContent");
            // this view has no router
            this.$el.addClass('hide-router');
    
            // custom content view
            var view = new wp.media.view.FlickrPress({
                controller: this,
                model: this.state().props
            });
    
            this.content.set( view );
        },
    });
})(jQuery);
