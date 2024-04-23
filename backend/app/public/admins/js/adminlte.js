(function (global, factory) {
	typeof exports === 'object' && typeof module !== 'undefined' ? factory(exports) :
			typeof define === 'function' && define.amd ? define(['exports'], factory) :
			(global = global || self, factory(global.adminlte = {}));
}(this, (function (exports) {
	'use strict';

	/**
	 * --------------------------------------------
	 * AdminLTE ControlSidebar.js
	 * License MIT
	 * --------------------------------------------
	 */
	var ControlSidebar = function ($) {
		/**
		 * Constants
		 * ====================================================
		 */
		var NAME = 'ControlSidebar';
		var DATA_KEY = 'lte.controlsidebar';
		var EVENT_KEY = "." + DATA_KEY;
		var JQUERY_NO_CONFLICT = $.fn[NAME];
		var Event = {
			COLLAPSED: "collapsed" + EVENT_KEY,
			EXPANDED: "expanded" + EVENT_KEY
		};
		var Selector = {
			CONTROL_SIDEBAR: '.control-sidebar',
			CONTROL_SIDEBAR_CONTENT: '.control-sidebar-content',
			DATA_TOGGLE: '[data-widget="control-sidebar"]',
			CONTENT: '.content-wrapper',
			HEADER: '.main-header',
			FOOTER: '.main-footer'
		};
		var ClassName = {
			CONTROL_SIDEBAR_ANIMATE: 'control-sidebar-animate',
			CONTROL_SIDEBAR_OPEN: 'control-sidebar-open',
			CONTROL_SIDEBAR_SLIDE: 'control-sidebar-slide-open',
			LAYOUT_FIXED: 'layout-fixed',
			NAVBAR_FIXED: 'layout-navbar-fixed',
			NAVBAR_SM_FIXED: 'layout-sm-navbar-fixed',
			NAVBAR_MD_FIXED: 'layout-md-navbar-fixed',
			NAVBAR_LG_FIXED: 'layout-lg-navbar-fixed',
			NAVBAR_XL_FIXED: 'layout-xl-navbar-fixed',
			FOOTER_FIXED: 'layout-footer-fixed',
			FOOTER_SM_FIXED: 'layout-sm-footer-fixed',
			FOOTER_MD_FIXED: 'layout-md-footer-fixed',
			FOOTER_LG_FIXED: 'layout-lg-footer-fixed',
			FOOTER_XL_FIXED: 'layout-xl-footer-fixed'
		};
		var Default = {
			controlsidebarSlide: true,
			scrollbarTheme: 'os-theme-light',
			scrollbarAutoHide: 'l'
		};
		/**
		 * Class Definition
		 * ====================================================
		 */

		var ControlSidebar =
				/*#__PURE__*/
						function () {
							function ControlSidebar(element, config) {
								this._element = element;
								this._config = config;

								this._init();
							} // Public


							var _proto = ControlSidebar.prototype;

							_proto.collapse = function collapse() {
								// Show the control sidebar
								if (this._config.controlsidebarSlide) {
									$('html').addClass(ClassName.CONTROL_SIDEBAR_ANIMATE);
									$('body').removeClass(ClassName.CONTROL_SIDEBAR_SLIDE).delay(300).queue(function () {
										$(Selector.CONTROL_SIDEBAR).hide();
										$('html').removeClass(ClassName.CONTROL_SIDEBAR_ANIMATE);
										$(this).dequeue();
									});
								} else {
									$('body').removeClass(ClassName.CONTROL_SIDEBAR_OPEN);
								}

								var collapsedEvent = $.Event(Event.COLLAPSED);
								$(this._element).trigger(collapsedEvent);
							};

							_proto.show = function show() {
								// Collapse the control sidebar
								if (this._config.controlsidebarSlide) {
									$('html').addClass(ClassName.CONTROL_SIDEBAR_ANIMATE);
									$(Selector.CONTROL_SIDEBAR).show().delay(10).queue(function () {
										$('body').addClass(ClassName.CONTROL_SIDEBAR_SLIDE).delay(300).queue(function () {
											$('html').removeClass(ClassName.CONTROL_SIDEBAR_ANIMATE);
											$(this).dequeue();
										});
										$(this).dequeue();
									});
								} else {
									$('body').addClass(ClassName.CONTROL_SIDEBAR_OPEN);
								}

								var expandedEvent = $.Event(Event.EXPANDED);
								$(this._element).trigger(expandedEvent);
							};

							_proto.toggle = function toggle() {
								var shouldClose = $('body').hasClass(ClassName.CONTROL_SIDEBAR_OPEN) || $('body').hasClass(ClassName.CONTROL_SIDEBAR_SLIDE);

								if (shouldClose) {
									// Close the control sidebar
									this.collapse();
								} else {
									// Open the control sidebar
									this.show();
								}
							} // Private
							;

							_proto._init = function _init() {
								var _this = this;

								this._fixHeight();

								this._fixScrollHeight();

								$(window).resize(function () {
									_this._fixHeight();

									_this._fixScrollHeight();
								});
								$(window).scroll(function () {
									if ($('body').hasClass(ClassName.CONTROL_SIDEBAR_OPEN) || $('body').hasClass(ClassName.CONTROL_SIDEBAR_SLIDE)) {
										_this._fixScrollHeight();
									}
								});
							};

							_proto._fixScrollHeight = function _fixScrollHeight() {
								var heights = {
									scroll: $(document).height(),
									window: $(window).height(),
									header: $(Selector.HEADER).outerHeight(),
									footer: $(Selector.FOOTER).outerHeight()
								};
								var positions = {
									bottom: Math.abs(heights.window + $(window).scrollTop() - heights.scroll),
									top: $(window).scrollTop()
								};
								var navbarFixed = false;
								var footerFixed = false;

								if ($('body').hasClass(ClassName.LAYOUT_FIXED)) {
									if ($('body').hasClass(ClassName.NAVBAR_FIXED) || $('body').hasClass(ClassName.NAVBAR_SM_FIXED) || $('body').hasClass(ClassName.NAVBAR_MD_FIXED) || $('body').hasClass(ClassName.NAVBAR_LG_FIXED) || $('body').hasClass(ClassName.NAVBAR_XL_FIXED)) {
										if ($(Selector.HEADER).css("position") === "fixed") {
											navbarFixed = true;
										}
									}

									if ($('body').hasClass(ClassName.FOOTER_FIXED) || $('body').hasClass(ClassName.FOOTER_SM_FIXED) || $('body').hasClass(ClassName.FOOTER_MD_FIXED) || $('body').hasClass(ClassName.FOOTER_LG_FIXED) || $('body').hasClass(ClassName.FOOTER_XL_FIXED)) {
										if ($(Selector.FOOTER).css("position") === "fixed") {
											footerFixed = true;
										}
									}

									if (positions.top === 0 && positions.bottom === 0) {
										$(Selector.CONTROL_SIDEBAR).css('bottom', heights.footer);
										$(Selector.CONTROL_SIDEBAR).css('top', heights.header);
										$(Selector.CONTROL_SIDEBAR + ', ' + Selector.CONTROL_SIDEBAR + ' ' + Selector.CONTROL_SIDEBAR_CONTENT).css('height', heights.window - (heights.header + heights.footer));
									} else if (positions.bottom <= heights.footer) {
										if (footerFixed === false) {
											$(Selector.CONTROL_SIDEBAR).css('bottom', heights.footer - positions.bottom);
											$(Selector.CONTROL_SIDEBAR + ', ' + Selector.CONTROL_SIDEBAR + ' ' + Selector.CONTROL_SIDEBAR_CONTENT).css('height', heights.window - (heights.footer - positions.bottom));
										} else {
											$(Selector.CONTROL_SIDEBAR).css('bottom', heights.footer);
										}
									} else if (positions.top <= heights.header) {
										if (navbarFixed === false) {
											$(Selector.CONTROL_SIDEBAR).css('top', heights.header - positions.top);
											$(Selector.CONTROL_SIDEBAR + ', ' + Selector.CONTROL_SIDEBAR + ' ' + Selector.CONTROL_SIDEBAR_CONTENT).css('height', heights.window - (heights.header - positions.top));
										} else {
											$(Selector.CONTROL_SIDEBAR).css('top', heights.header);
										}
									} else {
										if (navbarFixed === false) {
											$(Selector.CONTROL_SIDEBAR).css('top', 0);
											$(Selector.CONTROL_SIDEBAR + ', ' + Selector.CONTROL_SIDEBAR + ' ' + Selector.CONTROL_SIDEBAR_CONTENT).css('height', heights.window);
										} else {
											$(Selector.CONTROL_SIDEBAR).css('top', heights.header);
										}
									}
								}
							};

							_proto._fixHeight = function _fixHeight() {
								var heights = {
									window: $(window).height(),
									header: $(Selector.HEADER).outerHeight(),
									footer: $(Selector.FOOTER).outerHeight()
								};

								if ($('body').hasClass(ClassName.LAYOUT_FIXED)) {
									var sidebarHeight = heights.window - heights.header;

									if ($('body').hasClass(ClassName.FOOTER_FIXED) || $('body').hasClass(ClassName.FOOTER_SM_FIXED) || $('body').hasClass(ClassName.FOOTER_MD_FIXED) || $('body').hasClass(ClassName.FOOTER_LG_FIXED) || $('body').hasClass(ClassName.FOOTER_XL_FIXED)) {
										if ($(Selector.FOOTER).css("position") === "fixed") {
											sidebarHeight = heights.window - heights.header - heights.footer;
										}
									}

									$(Selector.CONTROL_SIDEBAR + ' ' + Selector.CONTROL_SIDEBAR_CONTENT).css('height', sidebarHeight);

									if (typeof $.fn.overlayScrollbars !== 'undefined') {
										$(Selector.CONTROL_SIDEBAR + ' ' + Selector.CONTROL_SIDEBAR_CONTENT).overlayScrollbars({
											className: this._config.scrollbarTheme,
											sizeAutoCapable: true,
											scrollbars: {
												autoHide: this._config.scrollbarAutoHide,
												clickScrolling: true
											}
										});
									}
								}
							} // Static
							;

							ControlSidebar._jQueryInterface = function _jQueryInterface(operation) {
								return this.each(function () {
									var data = $(this).data(DATA_KEY);

									var _options = $.extend({}, Default, $(this).data());

									if (!data) {
										data = new ControlSidebar(this, _options);
										$(this).data(DATA_KEY, data);
									}

									if (data[operation] === 'undefined') {
										throw new Error(operation + " is not a function");
									}

									data[operation]();
								});
							};

							return ControlSidebar;
						}();
				/**
				 *
				 * Data Api implementation
				 * ====================================================
				 */


				$(document).on('click', Selector.DATA_TOGGLE, function (event) {
					event.preventDefault();

					ControlSidebar._jQueryInterface.call($(this), 'toggle');
				});
				/**
				 * jQuery API
				 * ====================================================
				 */

				$.fn[NAME] = ControlSidebar._jQueryInterface;
				$.fn[NAME].Constructor = ControlSidebar;

				$.fn[NAME].noConflict = function () {
					$.fn[NAME] = JQUERY_NO_CONFLICT;
					return ControlSidebar._jQueryInterface;
				};

				return ControlSidebar;
			}(jQuery);

	/**
	 * --------------------------------------------
	 * AdminLTE Layout.js
	 * License MIT
	 * --------------------------------------------
	 */
	var Layout = function ($) {
		/**
		 * Constants
		 * ====================================================
		 */
		var NAME = 'Layout';
		var DATA_KEY = 'lte.layout';
		var JQUERY_NO_CONFLICT = $.fn[NAME];
		var Selector = {
			HEADER: '.main-header',
			MAIN_SIDEBAR: '.main-sidebar',
			SIDEBAR: '.main-sidebar .sidebar',
			CONTENT: '.content-wrapper',
			BRAND: '.brand-link',
			CONTENT_HEADER: '.content-header',
			WRAPPER: '.wrapper',
			CONTROL_SIDEBAR: '.control-sidebar',
			CONTROL_SIDEBAR_CONTENT: '.control-sidebar-content',
			CONTROL_SIDEBAR_BTN: '[data-widget="control-sidebar"]',
			LAYOUT_FIXED: '.layout-fixed',
			FOOTER: '.main-footer',
			PUSHMENU_BTN: '[data-widget="pushmenu"]',
			LOGIN_BOX: '.login-box',
			REGISTER_BOX: '.register-box'
		};
		var ClassName = {
			HOLD: 'hold-transition',
			SIDEBAR: 'main-sidebar',
			CONTENT_FIXED: 'content-fixed',
			SIDEBAR_FOCUSED: 'sidebar-focused',
			LAYOUT_FIXED: 'layout-fixed',
			NAVBAR_FIXED: 'layout-navbar-fixed',
			FOOTER_FIXED: 'layout-footer-fixed',
			LOGIN_PAGE: 'login-page',
			REGISTER_PAGE: 'register-page',
			CONTROL_SIDEBAR_SLIDE_OPEN: 'control-sidebar-slide-open',
			CONTROL_SIDEBAR_OPEN: 'control-sidebar-open'
		};
		var Default = {
			scrollbarTheme: 'os-theme-light',
			scrollbarAutoHide: 'l'
		};
		/**
		 * Class Definition
		 * ====================================================
		 */

		var Layout =
				/*#__PURE__*/
						function () {
							function Layout(element, config) {
								this._config = config;
								this._element = element;

								this._init();
							} // Public


							var _proto = Layout.prototype;

							_proto.fixLayoutHeight = function fixLayoutHeight(extra) {
								if (extra === void 0) {
									extra = null;
								}

								var control_sidebar = 0;

								if ($('body').hasClass(ClassName.CONTROL_SIDEBAR_SLIDE_OPEN) || $('body').hasClass(ClassName.CONTROL_SIDEBAR_OPEN) || extra == 'control_sidebar') {
									control_sidebar = $(Selector.CONTROL_SIDEBAR_CONTENT).height();
								}

								var heights = {
									window: $(window).height(),
									header: $(Selector.HEADER).length !== 0 ? $(Selector.HEADER).outerHeight() : 0,
									footer: $(Selector.FOOTER).length !== 0 ? $(Selector.FOOTER).outerHeight() : 0,
									sidebar: $(Selector.SIDEBAR).length !== 0 ? $(Selector.SIDEBAR).height() : 0,
									control_sidebar: control_sidebar
								};

								var max = this._max(heights);

								if (max == heights.control_sidebar) {
									$(Selector.CONTENT).css('min-height', max);
								} else if (max == heights.window) {
									$(Selector.CONTENT).css('min-height', max - heights.header - heights.footer);
								} else {
									$(Selector.CONTENT).css('min-height', max - heights.header);
								}

								if ($('body').hasClass(ClassName.LAYOUT_FIXED)) {
									$(Selector.CONTENT).css('min-height', max - heights.header - heights.footer);

									if (typeof $.fn.overlayScrollbars !== 'undefined') {
										$(Selector.SIDEBAR).overlayScrollbars({
											className: this._config.scrollbarTheme,
											sizeAutoCapable: true,
											scrollbars: {
												autoHide: this._config.scrollbarAutoHide,
												clickScrolling: true
											}
										});
									}
								}
							} // Private
							;

							_proto._init = function _init() {
								var _this = this;

								// Activate layout height watcher
								this.fixLayoutHeight();
								$(Selector.SIDEBAR).on('collapsed.lte.treeview expanded.lte.treeview', function () {
									_this.fixLayoutHeight();
								});
								$(Selector.PUSHMENU_BTN).on('collapsed.lte.pushmenu shown.lte.pushmenu', function () {
									_this.fixLayoutHeight();
								});
								$(Selector.CONTROL_SIDEBAR_BTN).on('collapsed.lte.controlsidebar', function () {
									_this.fixLayoutHeight();
								}).on('expanded.lte.controlsidebar', function () {
									_this.fixLayoutHeight('control_sidebar');
								});
								$(window).resize(function () {
									_this.fixLayoutHeight();
								});

								if (!$('body').hasClass(ClassName.LOGIN_PAGE) && !$('body').hasClass(ClassName.REGISTER_PAGE)) {
									$('body, html').css('height', 'auto');
								} else if ($('body').hasClass(ClassName.LOGIN_PAGE) || $('body').hasClass(ClassName.REGISTER_PAGE)) {
									var box_height = $(Selector.LOGIN_BOX + ', ' + Selector.REGISTER_BOX).height();
									$('body').css('min-height', box_height);
								}

								$('body.hold-transition').removeClass('hold-transition');
							};

							_proto._max = function _max(numbers) {
								// Calculate the maximum number in a list
								var max = 0;
								Object.keys(numbers).forEach(function (key) {
									if (numbers[key] > max) {
										max = numbers[key];
									}
								});
								return max;
							} // Static
							;

							Layout._jQueryInterface = function _jQueryInterface(config) {
								if (config === void 0) {
									config = '';
								}

								return this.each(function () {
									var data = $(this).data(DATA_KEY);

									var _options = $.extend({}, Default, $(this).data());

									if (!data) {
										data = new Layout($(this), _options);
										$(this).data(DATA_KEY, data);
									}

									if (config === 'init' || config === '') {
										data['_init']();
									}
								});
							};

							return Layout;
						}();
				/**
				 * Data API
				 * ====================================================
				 */


				$(window).on('load', function () {
					Layout._jQueryInterface.call($('body'));
				});
				$(Selector.SIDEBAR + ' a').on('focusin', function () {
					$(Selector.MAIN_SIDEBAR).addClass(ClassName.SIDEBAR_FOCUSED);
				});
				$(Selector.SIDEBAR + ' a').on('focusout', function () {
					$(Selector.MAIN_SIDEBAR).removeClass(ClassName.SIDEBAR_FOCUSED);
				});
				/**
				 * jQuery API
				 * ====================================================
				 */

				$.fn[NAME] = Layout._jQueryInterface;
				$.fn[NAME].Constructor = Layout;

				$.fn[NAME].noConflict = function () {
					$.fn[NAME] = JQUERY_NO_CONFLICT;
					return Layout._jQueryInterface;
				};

				return Layout;
			}(jQuery);

	/**
	 * --------------------------------------------
	 * AdminLTE PushMenu.js
	 * License MIT
	 * --------------------------------------------
	 */
	var PushMenu = function ($) {
		/**
		 * Constants
		 * ====================================================
		 */
		var NAME = 'PushMenu';
		var DATA_KEY = 'lte.pushmenu';
		var EVENT_KEY = "." + DATA_KEY;
		var JQUERY_NO_CONFLICT = $.fn[NAME];
		var Event = {
			COLLAPSED: "collapsed" + EVENT_KEY,
			SHOWN: "shown" + EVENT_KEY
		};
		var Default = {
			autoCollapseSize: 992,
			enableRemember: false,
			noTransitionAfterReload: true
		};
		var Selector = {
			TOGGLE_BUTTON: '[data-widget="pushmenu"]',
			SIDEBAR_MINI: '.sidebar-mini',
			SIDEBAR_COLLAPSED: '.sidebar-collapse',
			BODY: 'body',
			OVERLAY: '#sidebar-overlay',
			WRAPPER: '.wrapper'
		};
		var ClassName = {
			SIDEBAR_OPEN: 'sidebar-open',
			COLLAPSED: 'sidebar-collapse',
			OPEN: 'sidebar-open'
		};
		/**
		 * Class Definition
		 * ====================================================
		 */

		var PushMenu =
				/*#__PURE__*/
						function () {
							function PushMenu(element, options) {
								this._element = element;
								this._options = $.extend({}, Default, options);

								if (!$(Selector.OVERLAY).length) {
									this._addOverlay();
								}

								this._init();
							} // Public


							var _proto = PushMenu.prototype;

							_proto.expand = function expand() {
								if (this._options.autoCollapseSize) {
									if ($(window).width() <= this._options.autoCollapseSize) {
										$(Selector.BODY).addClass(ClassName.OPEN);
									}
								}

								$(Selector.BODY).removeClass(ClassName.COLLAPSED);

								if (this._options.enableRemember) {
									localStorage.setItem("remember" + EVENT_KEY, ClassName.OPEN);
								}

								var shownEvent = $.Event(Event.SHOWN);
								$(this._element).trigger(shownEvent);
							};

							_proto.collapse = function collapse() {
								if (this._options.autoCollapseSize) {
									if ($(window).width() <= this._options.autoCollapseSize) {
										$(Selector.BODY).removeClass(ClassName.OPEN);
									}
								}

								$(Selector.BODY).addClass(ClassName.COLLAPSED);

								if (this._options.enableRemember) {
									localStorage.setItem("remember" + EVENT_KEY, ClassName.COLLAPSED);
								}

								var collapsedEvent = $.Event(Event.COLLAPSED);
								$(this._element).trigger(collapsedEvent);
							};

							_proto.toggle = function toggle() {
								if (!$(Selector.BODY).hasClass(ClassName.COLLAPSED)) {
									this.collapse();
								} else {
									this.expand();
								}
							};

							_proto.autoCollapse = function autoCollapse(resize) {
								if (resize === void 0) {
									resize = false;
								}

								if (this._options.autoCollapseSize) {
									if ($(window).width() <= this._options.autoCollapseSize) {
										if (!$(Selector.BODY).hasClass(ClassName.OPEN)) {
											this.collapse();
										}
									} else if (resize == true) {
										if ($(Selector.BODY).hasClass(ClassName.OPEN)) {
											$(Selector.BODY).removeClass(ClassName.OPEN);
										}
									}
								}
							};

							_proto.remember = function remember() {
								if (this._options.enableRemember) {
									var toggleState = localStorage.getItem("remember" + EVENT_KEY);

									if (toggleState == ClassName.COLLAPSED) {
										if (this._options.noTransitionAfterReload) {
											$("body").addClass('hold-transition').addClass(ClassName.COLLAPSED).delay(50).queue(function () {
												$(this).removeClass('hold-transition');
												$(this).dequeue();
											});
										} else {
											$("body").addClass(ClassName.COLLAPSED);
										}
									} else {
										if (this._options.noTransitionAfterReload) {
											$("body").addClass('hold-transition').removeClass(ClassName.COLLAPSED).delay(50).queue(function () {
												$(this).removeClass('hold-transition');
												$(this).dequeue();
											});
										} else {
											$("body").removeClass(ClassName.COLLAPSED);
										}
									}
								}
							} // Private
							;

							_proto._init = function _init() {
								var _this = this;

								this.remember();
								this.autoCollapse();
								$(window).resize(function () {
									_this.autoCollapse(true);
								});
							};

							_proto._addOverlay = function _addOverlay() {
								var _this2 = this;

								var overlay = $('<div />', {
									id: 'sidebar-overlay'
								});
								overlay.on('click', function () {
									_this2.collapse();
								});
								$(Selector.WRAPPER).append(overlay);
							} // Static
							;

							PushMenu._jQueryInterface = function _jQueryInterface(operation) {
								return this.each(function () {
									var data = $(this).data(DATA_KEY);

									var _options = $.extend({}, Default, $(this).data());

									if (!data) {
										data = new PushMenu(this, _options);
										$(this).data(DATA_KEY, data);
									}

									if (typeof operation === 'string' && operation.match(/collapse|expand|toggle/)) {
										data[operation]();
									}
								});
							};

							return PushMenu;
						}();
				/**
				 * Data API
				 * ====================================================
				 */


				$(document).on('click', Selector.TOGGLE_BUTTON, function (event) {
					event.preventDefault();
					var button = event.currentTarget;

					if ($(button).data('widget') !== 'pushmenu') {
						button = $(button).closest(Selector.TOGGLE_BUTTON);
					}

					PushMenu._jQueryInterface.call($(button), 'toggle');
				});
				$(window).on('load', function () {
					PushMenu._jQueryInterface.call($(Selector.TOGGLE_BUTTON));
				});
				/**
				 * jQuery API
				 * ====================================================
				 */

				$.fn[NAME] = PushMenu._jQueryInterface;
				$.fn[NAME].Constructor = PushMenu;

				$.fn[NAME].noConflict = function () {
					$.fn[NAME] = JQUERY_NO_CONFLICT;
					return PushMenu._jQueryInterface;
				};

				return PushMenu;
			}(jQuery);

	/**
	 * --------------------------------------------
	 * AdminLTE Treeview.js
	 * License MIT
	 * --------------------------------------------
	 */
	var Treeview = function ($) {
		/**
		 * Constants
		 * ====================================================
		 */
		var NAME = 'Treeview';
		var DATA_KEY = 'lte.treeview';
		var EVENT_KEY = "." + DATA_KEY;
		var JQUERY_NO_CONFLICT = $.fn[NAME];
		var Event = {
			SELECTED: "selected" + EVENT_KEY,
			EXPANDED: "expanded" + EVENT_KEY,
			COLLAPSED: "collapsed" + EVENT_KEY,
			LOAD_DATA_API: "load" + EVENT_KEY
		};
		var Selector = {
			LI: '.nav-item',
			LINK: '.nav-link',
			TREEVIEW_MENU: '.nav-treeview',
			OPEN: '.menu-open',
			DATA_WIDGET: '[data-widget="treeview"]'
		};
		var ClassName = {
			LI: 'nav-item',
			LINK: 'nav-link',
			TREEVIEW_MENU: 'nav-treeview',
			OPEN: 'menu-open',
			SIDEBAR_COLLAPSED: 'sidebar-collapse'
		};
		var Default = {
			trigger: Selector.DATA_WIDGET + " " + Selector.LINK,
			animationSpeed: 300,
			accordion: true,
			expandSidebar: false,
			sidebarButtonSelector: '[data-widget="pushmenu"]'
		};
		/**
		 * Class Definition
		 * ====================================================
		 */

		var Treeview =
				/*#__PURE__*/
						function () {
							function Treeview(element, config) {
								this._config = config;
								this._element = element;
							} // Public


							var _proto = Treeview.prototype;

							_proto.init = function init() {
								this._setupListeners();
							};

							_proto.expand = function expand(treeviewMenu, parentLi) {
								var _this = this;

								var expandedEvent = $.Event(Event.EXPANDED);

								if (this._config.accordion) {
									var openMenuLi = parentLi.siblings(Selector.OPEN).first();
									var openTreeview = openMenuLi.find(Selector.TREEVIEW_MENU).first();
									this.collapse(openTreeview, openMenuLi);
								}

								treeviewMenu.stop().slideDown(this._config.animationSpeed, function () {
									parentLi.addClass(ClassName.OPEN);
									$(_this._element).trigger(expandedEvent);
								});

								if (this._config.expandSidebar) {
									this._expandSidebar();
								}
							};

							_proto.collapse = function collapse(treeviewMenu, parentLi) {
								var _this2 = this;

								var collapsedEvent = $.Event(Event.COLLAPSED);
								treeviewMenu.stop().slideUp(this._config.animationSpeed, function () {
									parentLi.removeClass(ClassName.OPEN);
									$(_this2._element).trigger(collapsedEvent);
									treeviewMenu.find(Selector.OPEN + " > " + Selector.TREEVIEW_MENU).slideUp();
									treeviewMenu.find(Selector.OPEN).removeClass(ClassName.OPEN);
								});
							};

							_proto.toggle = function toggle(event) {
								var $relativeTarget = $(event.currentTarget);
								var $parent = $relativeTarget.parent();
								var treeviewMenu = $parent.find('> ' + Selector.TREEVIEW_MENU);

								if (!treeviewMenu.is(Selector.TREEVIEW_MENU)) {
									if (!$parent.is(Selector.LI)) {
										treeviewMenu = $parent.parent().find('> ' + Selector.TREEVIEW_MENU);
									}

									if (!treeviewMenu.is(Selector.TREEVIEW_MENU)) {
										return;
									}
								}

								event.preventDefault();
								var parentLi = $relativeTarget.parents(Selector.LI).first();
								var isOpen = parentLi.hasClass(ClassName.OPEN);

								if (isOpen) {
									this.collapse($(treeviewMenu), parentLi);
								} else {
									this.expand($(treeviewMenu), parentLi);
								}
							} // Private
							;

							_proto._setupListeners = function _setupListeners() {
								var _this3 = this;

								$(document).on('click', this._config.trigger, function (event) {
									_this3.toggle(event);
								});
							};

							_proto._expandSidebar = function _expandSidebar() {
								if ($('body').hasClass(ClassName.SIDEBAR_COLLAPSED)) {
									$(this._config.sidebarButtonSelector).PushMenu('expand');
								}
							} // Static
							;

							Treeview._jQueryInterface = function _jQueryInterface(config) {
								return this.each(function () {
									var data = $(this).data(DATA_KEY);

									var _options = $.extend({}, Default, $(this).data());

									if (!data) {
										data = new Treeview($(this), _options);
										$(this).data(DATA_KEY, data);
									}

									if (config === 'init') {
										data[config]();
									}
								});
							};

							return Treeview;
						}();
				/**
				 * Data API
				 * ====================================================
				 */


				$(window).on(Event.LOAD_DATA_API, function () {
					$(Selector.DATA_WIDGET).each(function () {
						Treeview._jQueryInterface.call($(this), 'init');
					});
				});
				/**
				 * jQuery API
				 * ====================================================
				 */

				$.fn[NAME] = Treeview._jQueryInterface;
				$.fn[NAME].Constructor = Treeview;

				$.fn[NAME].noConflict = function () {
					$.fn[NAME] = JQUERY_NO_CONFLICT;
					return Treeview._jQueryInterface;
				};

				return Treeview;
			}(jQuery);

	/**
	 * --------------------------------------------
	 * AdminLTE Dropdown.js
	 * License MIT
	 * --------------------------------------------
	 */
	var Dropdown = function ($) {
		/**
		 * Constants
		 * ====================================================
		 */
		var NAME = 'Dropdown';
		var DATA_KEY = 'lte.dropdown';
		var JQUERY_NO_CONFLICT = $.fn[NAME];
		var Selector = {
			DROPDOWN_MENU: 'ul.dropdown-menu',
			DROPDOWN_TOGGLE: '[data-toggle="dropdown"]'
		};
		var Default = {};
		/**
		 * Class Definition
		 * ====================================================
		 */

		var Dropdown =
				/*#__PURE__*/
						function () {
							function Dropdown(element, config) {
								this._config = config;
								this._element = element;
							} // Public


							var _proto = Dropdown.prototype;

							_proto.toggleSubmenu = function toggleSubmenu() {
								this._element.siblings().show().toggleClass("show");

								if (!this._element.next().hasClass('show')) {
									this._element.parents('.dropdown-menu').first().find('.show').removeClass("show").hide();
								}

								this._element.parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function (e) {
									$('.dropdown-submenu .show').removeClass("show").hide();
								});
							} // Static
							;

							Dropdown._jQueryInterface = function _jQueryInterface(config) {
								return this.each(function () {
									var data = $(this).data(DATA_KEY);

									var _config = $.extend({}, Default, $(this).data());

									if (!data) {
										data = new Dropdown($(this), _config);
										$(this).data(DATA_KEY, data);
									}

									if (config === 'toggleSubmenu') {
										data[config]();
									}
								});
							};

							return Dropdown;
						}();
				/**
				 * Data API
				 * ====================================================
				 */


				$(Selector.DROPDOWN_MENU + ' ' + Selector.DROPDOWN_TOGGLE).on("click", function (event) {
					event.preventDefault();
					event.stopPropagation();

					Dropdown._jQueryInterface.call($(this), 'toggleSubmenu');
				}); // $(Selector.SIDEBAR + ' a').on('focusin', () => {
				//   $(Selector.MAIN_SIDEBAR).addClass(ClassName.SIDEBAR_FOCUSED);
				// })
				// $(Selector.SIDEBAR + ' a').on('focusout', () => {
				//   $(Selector.MAIN_SIDEBAR).removeClass(ClassName.SIDEBAR_FOCUSED);
				// })

				/**
				 * jQuery API
				 * ====================================================
				 */

				$.fn[NAME] = Dropdown._jQueryInterface;
				$.fn[NAME].Constructor = Dropdown;

				$.fn[NAME].noConflict = function () {
					$.fn[NAME] = JQUERY_NO_CONFLICT;
					return Dropdown._jQueryInterface;
				};

				return Dropdown;
			}(jQuery);

	/**
	 * --------------------------------------------
	 * AdminLTE Toasts.js
	 * License MIT
	 * --------------------------------------------
	 */
	var Toasts = function ($) {
		/**
		 * Constants
		 * ====================================================
		 */
		var NAME = 'Toasts';
		var DATA_KEY = 'lte.toasts';
		var EVENT_KEY = "." + DATA_KEY;
		var JQUERY_NO_CONFLICT = $.fn[NAME];
		var Event = {
			INIT: "init" + EVENT_KEY,
			CREATED: "created" + EVENT_KEY,
			REMOVED: "removed" + EVENT_KEY
		};
		var Selector = {
			BODY: 'toast-body',
			CONTAINER_TOP_RIGHT: '#toastsContainerTopRight',
			CONTAINER_TOP_LEFT: '#toastsContainerTopLeft',
			CONTAINER_BOTTOM_RIGHT: '#toastsContainerBottomRight',
			CONTAINER_BOTTOM_LEFT: '#toastsContainerBottomLeft'
		};
		var ClassName = {
			TOP_RIGHT: 'toasts-top-right',
			TOP_LEFT: 'toasts-top-left',
			BOTTOM_RIGHT: 'toasts-bottom-right',
			BOTTOM_LEFT: 'toasts-bottom-left',
			FADE: 'fade'
		};
		var Position = {
			TOP_RIGHT: 'topRight',
			TOP_LEFT: 'topLeft',
			BOTTOM_RIGHT: 'bottomRight',
			BOTTOM_LEFT: 'bottomLeft'
		};
		var Default = {
			position: Position.TOP_RIGHT,
			fixed: true,
			autohide: false,
			autoremove: true,
			delay: 1000,
			fade: true,
			icon: null,
			image: null,
			imageAlt: null,
			imageHeight: '25px',
			title: null,
			subtitle: null,
			close: true,
			body: null,
			class: null
		};
		/**
		 * Class Definition
		 * ====================================================
		 */

		var Toasts =
				/*#__PURE__*/
						function () {
							function Toasts(element, config) {
								this._config = config;

								this._prepareContainer();

								var initEvent = $.Event(Event.INIT);
								$('body').trigger(initEvent);
							} // Public


							var _proto = Toasts.prototype;

							_proto.create = function create() {
								var toast = $('<div class="toast" role="alert" aria-live="assertive" aria-atomic="true"/>');
								toast.data('autohide', this._config.autohide);
								toast.data('animation', this._config.fade);

								if (this._config.class) {
									toast.addClass(this._config.class);
								}

								if (this._config.delay && this._config.delay != 500) {
									toast.data('delay', this._config.delay);
								}

								var toast_header = $('<div class="toast-header">');

								if (this._config.image != null) {
									var toast_image = $('<img />').addClass('rounded mr-2').attr('src', this._config.image).attr('alt', this._config.imageAlt);

									if (this._config.imageHeight != null) {
										toast_image.height(this._config.imageHeight).width('auto');
									}

									toast_header.append(toast_image);
								}

								if (this._config.icon != null) {
									toast_header.append($('<i />').addClass('mr-2').addClass(this._config.icon));
								}

								if (this._config.title != null) {
									toast_header.append($('<strong />').addClass('mr-auto').html(this._config.title));
								}

								if (this._config.subtitle != null) {
									toast_header.append($('<small />').html(this._config.subtitle));
								}

								if (this._config.close == true) {
									var toast_close = $('<button data-dismiss="toast" />').attr('type', 'button').addClass('ml-2 mb-1 close').attr('aria-label', 'Close').append('<span aria-hidden="true">&times;</span>');

									if (this._config.title == null) {
										toast_close.toggleClass('ml-2 ml-auto');
									}

									toast_header.append(toast_close);
								}

								toast.append(toast_header);

								if (this._config.body != null) {
									toast.append($('<div class="toast-body" />').html(this._config.body));
								}

								$(this._getContainerId()).prepend(toast);
								var createdEvent = $.Event(Event.CREATED);
								$('body').trigger(createdEvent);
								toast.toast('show');

								if (this._config.autoremove) {
									toast.on('hidden.bs.toast', function () {
										$(this).delay(200).remove();
										var removedEvent = $.Event(Event.REMOVED);
										$('body').trigger(removedEvent);
									});
								}
							} // Static
							;

							_proto._getContainerId = function _getContainerId() {
								if (this._config.position == Position.TOP_RIGHT) {
									return Selector.CONTAINER_TOP_RIGHT;
								} else if (this._config.position == Position.TOP_LEFT) {
									return Selector.CONTAINER_TOP_LEFT;
								} else if (this._config.position == Position.BOTTOM_RIGHT) {
									return Selector.CONTAINER_BOTTOM_RIGHT;
								} else if (this._config.position == Position.BOTTOM_LEFT) {
									return Selector.CONTAINER_BOTTOM_LEFT;
								}
							};

							_proto._prepareContainer = function _prepareContainer() {
								if ($(this._getContainerId()).length === 0) {
									var container = $('<div />').attr('id', this._getContainerId().replace('#', ''));

									if (this._config.position == Position.TOP_RIGHT) {
										container.addClass(ClassName.TOP_RIGHT);
									} else if (this._config.position == Position.TOP_LEFT) {
										container.addClass(ClassName.TOP_LEFT);
									} else if (this._config.position == Position.BOTTOM_RIGHT) {
										container.addClass(ClassName.BOTTOM_RIGHT);
									} else if (this._config.position == Position.BOTTOM_LEFT) {
										container.addClass(ClassName.BOTTOM_LEFT);
									}

									$('body').append(container);
								}

								if (this._config.fixed) {
									$(this._getContainerId()).addClass('fixed');
								} else {
									$(this._getContainerId()).removeClass('fixed');
								}
							} // Static
							;

							Toasts._jQueryInterface = function _jQueryInterface(option, config) {
								return this.each(function () {
									var _options = $.extend({}, Default, config);

									var toast = new Toasts($(this), _options);

									if (option === 'create') {
										toast[option]();
									}
								});
							};

							return Toasts;
						}();
				/**
				 * jQuery API
				 * ====================================================
				 */


				$.fn[NAME] = Toasts._jQueryInterface;
				$.fn[NAME].Constructor = Toasts;

				$.fn[NAME].noConflict = function () {
					$.fn[NAME] = JQUERY_NO_CONFLICT;
					return Toasts._jQueryInterface;
				};

				return Toasts;
			}(jQuery);

	exports.ControlSidebar = ControlSidebar;
	exports.Dropdown = Dropdown;
	exports.Layout = Layout;
	exports.PushMenu = PushMenu;
	exports.Toasts = Toasts;
	exports.Treeview = Treeview;

	Object.defineProperty(exports, '__esModule', {value: true});

})));
//# sourceMappingURL=adminlte.js.map
