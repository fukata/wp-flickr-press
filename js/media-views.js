(function($){

    var fp = null;

    // custom state : this controller contains your application logic
    wp.media.controller.FlickrPress = wp.media.controller.State.extend({
        initialize: function(){
            console.log('controller.FlickrPress.initialize');
            // this model contains all the relevant data needed for the application
            this.props = new Backbone.Model({ custom_data: '' });
            this.props.on( 'change:custom_data', this.refresh, this );

            fp = {
                flickr: new $.FlickrClient({
                    apiKey:          $("#wpfp_api_key").val(),
                    apiSecret:       $("#wpfp_api_secret").val(),
                    userId:          $("#wpfp_user_id").val(),
                    oauthToken:      $("#wpfp_oauth_token").val(),
                    enablePathAlias: $("#wpfp_enable_path_alias").val() == '1'
                }),
                currentPhotos: null,
                search: null
            };
            fp.options = {
                perpage : 20,
                extras : fp.flickr.SIZE_VALUES.join(',') + ",path_alias",
                sort : "date-posted-desc",
                thumbnailSize : "sq"
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
            console.log(this.props.get('custom_data'));
        },
    });

    // custom toolbar : contains the buttons at the bottom
    wp.media.view.Toolbar.FlickrPress = wp.media.view.Toolbar.extend({
//        initialize: function() {
//            _.defaults( this.options, {
//                event: 'custom_event',
//                close: false,
//                items: {
//                    custom_event: {
//                        text: wp.media.view.l10n.wpfpTitle, // added via 'media_view_strings' filter,
//                        style: 'primary',
//                        priority: 80,
//                        requires: false,
//                        click: this.customAction
//                    }
//                }
//            });
//
//            wp.media.view.Toolbar.prototype.initialize.apply( this, arguments );
//        },
//
//        // called each time the model changes
//        refresh: function() {
//            // you can modify the toolbar behaviour in response to user actions here
//            // disable the button if there is no custom data
//            var custom_data = this.controller.state().props.get('custom_data');
//            this.get('custom_event').model.set( 'disabled', ! custom_data );
//            
//            // call the parent refresh
//            wp.media.view.Toolbar.prototype.refresh.apply( this, arguments );
//        },
//        
//        // triggered when the button is clicked
//        customAction: function(){
//            this.controller.state().customAction();
//        }
    });

    wp.media.view.FlickrPressSearchTypeFilters = wp.media.view.AttachmentFilters.extend({
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
					text:  wp.media.view.l10n.wpfpSearchTypeFilterPhotosets,
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
        change: function() {
            console.log('FlickrPressSearchTypeFilters.change', this.el.value, this.model.get('wpfp_search_type'));
			var filter = this.filters[ this.el.value ];
			if ( filter ) {
                this.model.set( 'wpfp_search_type', this.el.value );
            }
        },
		select: function() {
            console.log('FlickrPressSearchTypeFilters.select', this.el.value, this.model.get('wpfp_search_type'));
			this.$el.val( this.model.get('wpfp_search_type') || this.el.value );
        }
    });

    wp.media.view.FlickrPressSearchSortFilters = wp.media.view.AttachmentFilters.extend({
        className: 'search-sort-filters',
		createFilters: function() {
			this.filters = {
				postedAsc: {
					text:  wp.media.view.l10n.wpfpSearchSortFilterPostedASC,
					props: {
						orderby: 'date',
						order:   'DESC'
					},
					priority: 10
				},
				postedDesc: {
					text:  wp.media.view.l10n.wpfpSearchSortFilterPostedDESC,
					props: {
						orderby: 'date',
						order:   'DESC'
					},
					priority: 10
				},
				takenAsc: {
					text:  wp.media.view.l10n.wpfpSearchSortFilterTakenASC,
					props: {
						orderby: 'date',
						order:   'DESC'
					},
					priority: 10
				},
				takenDesc: {
					text:  wp.media.view.l10n.wpfpSearchSortFilterTakenDESC,
					props: {
						orderby: 'date',
						order:   'DESC'
					},
					priority: 10
				},
				interestingnessAsc: {
					text:  wp.media.view.l10n.wpfpSearchSortFilterInterestingnessASC,
					props: {
						orderby: 'date',
						order:   'DESC'
					},
					priority: 10
				},
				interestingnessDesc: {
					text:  wp.media.view.l10n.wpfpSearchSortFilterInterestingnessDESC,
					props: {
						orderby: 'date',
						order:   'DESC'
					},
					priority: 10
				},
			};
		},
        change: function() {
            console.log('FlickrPressSearchSortFilters.change');
			var filter = this.filters[ this.el.value ];
            console.log(filter);
//			if ( filter ) {
//				this.model.set( filter.props );
//            }
        },
		select: function() {
            console.log('FlickrPressSearchSortFilters.select');
        }
    });

    wp.media.view.FlickrPressSearchPhotosetFilters = wp.media.view.AttachmentFilters.extend({
        className: 'search-photoset-filters',
		createFilters: function() {
            console.log('FlickrPressSearchPhotosetFilters.createFilters');
            var that = this;
            if ( typeof that.filters === 'undefined' ) {
                that.filters = {};
                fp.flickr.photosets_getList({}, function(res) {
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
                });
            }
		},
        change: function() {
            console.log('FlickrPressSearchPhotosetFilters.change');
			var filter = this.filters[ this.el.value ];
            console.log(filter);
//			if ( filter ) {
//				this.model.set( filter.props );
//            }
        },
		select: function() {
            console.log('FlickrPressSearchPhotosetFilters.select');
        }
    });

    wp.media.view.FlickrPressSearchTagFilter = wp.media.view.Search.extend({
		tagName:   'input',
		className: 'search search-tag-filter',

		attributes: {
			type:        'search',
			placeholder: wp.media.view.l10n.wpfpSearchTagFilterPlaceholder
		},
    });

    wp.media.view.FlickrPressSearchKeywordFilter = wp.media.view.Search.extend({
		tagName:   'input',
		className: 'search search-keyword-filter',

		attributes: {
			type:        'search',
			placeholder: wp.media.view.l10n.wpfpSearchKeywordFilterPlaceholder
		},
    });

    // custom content : this view contains the main panel UI
    wp.media.view.FlickrPress = wp.media.View.extend({
        tagName: 'div',
        className: 'flickr-press',
        
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

            //this.collection.on( 'add remove reset', this.updateContent, this );
        },
        render: function(){
            console.log("view.FlickrPress.render");
            return this;
        },
        update: function( event ) {
            console.log("view.FlickrPress.update class=%s, value=%s", event.target, event.target.value);
            var $target = $(event.target);
            if ( $target.hasClass('search-tag-filter') ) {
                this.model.set( 'wpfp_tag', event.target.value );
            }
            if ( $target.hasClass('search-keyword-filter') ) {
                this.model.set( 'wpfp_keyword', event.target.value );
            }

        },
        change: function() {
            console.log("view.FlickrPress.change class=%s, value=%s", event.target, event.target.value);
            var $target = $(event.target);
            if ( $target.hasClass('search-type-filters') ) {
                this.model.set( 'wpfp_search_type', event.target.value );
                this.updateToolbar();
            }
            if ( $target.hasClass('search-sort-filters') ) {
                this.model.set( 'wpfp_sort', event.target.value );
            }
            if ( $target.hasClass('search-photoset-filters') ) {
                this.model.set( 'wpfp_photoset', event.target.value );
            }
        },
        createToolbar: function() {
            this.toolbar = new wp.media.view.Toolbar({
                controller: this.controller
            });
            this.views.add( this.toolbar );

            this.toolbar.set( 'search-type-filters', new wp.media.view.FlickrPressSearchTypeFilters({
                controller: this.controller,
                model:      this.controller.state(),
                priority:   -80
            }).render() );

            this.toolbar.set( 'search-sort-filters', new wp.media.view.FlickrPressSearchSortFilters({
                controller: this.controller,
                model:      this.controller.state(),
                priority:   -80
            }).render() );

            this.updateToolbar();
        },
        updateToolbar: function() {
            if ( this.toolbar ) {
                _.each(['search-photoset-filters','search-tag-filter','search-keyword-filter'], function( key ) {
                    this.toolbar.unset(key);
                }, this );
            }

            var searchType = this.model.get('wpfp_search_type');
            console.log("updateToolbar. searchType=%s", searchType);
            if ( searchType === 'photosets' ) {
                this.toolbar.set( 'search-photoset-filters', new wp.media.view.FlickrPressSearchPhotosetFilters({
                    controller: this.controller,
                    model:      this.controller.state(),
                    priority:   -80
                }).render() );
            } else if ( searchType === 'advanced' ) {
                this.toolbar.set( 'search-tag-filter', new wp.media.view.FlickrPressSearchTagFilter({
                    controller: this.controller,
                    model:      this.controller.state(),
                    priority:   -80
                }).render() );

                this.toolbar.set( 'search-keyword-filter', new wp.media.view.FlickrPressSearchKeywordFilter({
                    controller: this.controller,
                    model:      this.controller.state(),
                    priority:   -80
                }).render() );
            }
        },
        createSidebar: function() {
			var options = this.options,
				selection = options.selection,
				sidebar = this.sidebar = new wp.media.view.Sidebar({
					controller: this.controller
				});

			this.views.add( sidebar );
//			if ( this.controller.uploader ) {
//				sidebar.set( 'uploads', new media.view.UploaderStatus({
//					controller: this.controller,
//					priority:   40
//				}) );
//			}
//
//			selection.on( 'selection:single', this.createSingle, this );
//			selection.on( 'selection:unsingle', this.disposeSingle, this );
//
//			if ( selection.single() ) {
//				this.createSingle();
//            }
        },
//		createSingle: function() {
//			var sidebar = this.sidebar,
//				single = this.options.selection.single();
//
//			sidebar.set( 'details', new media.view.Attachment.Details({
//				controller: this.controller,
//				model:      single,
//				priority:   80
//			}) );
//
//			sidebar.set( 'compat', new media.view.AttachmentCompat({
//				controller: this.controller,
//				model:      single,
//				priority:   120
//			}) );
//
//			if ( this.options.display ) {
//				sidebar.set( 'display', new media.view.Settings.AttachmentDisplay({
//					controller:   this.controller,
//					model:        this.model.display( single ),
//					attachment:   single,
//					priority:     160,
//					userSettings: this.model.get('displayUserSettings')
//				}) );
//			}
//		},
//		disposeSingle: function() {
//			var sidebar = this.sidebar;
//			sidebar.unset('details');
//			sidebar.unset('compat');
//			sidebar.unset('display');
//		},
        updateContent: function() {
            var view = this;
            //this.$el.append('<p>aaa</p>');
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
    
            this.on( 'content:render:wpfp', this.customContent, this );
            this.on( 'toolbar:create:main-wpfp-action', this.createFlickrPressToolbar, this );
            this.on( 'toolbar:render:main-wpfp-action', this.renderFlickrPressToolbar, this );
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
