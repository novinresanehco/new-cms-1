jQueryTemp(function(jQueryTemp) {

	/* highlight current menu group
	------------------------------------------------------------------------- */
	jQueryTemp('#menu-group li[id="group-' + current_group_id + '"]').addClass('current');

	/* global ajax setup
	------------------------------------------------------------------------- */
	jQueryTemp.ajaxSetup({
		type: 'GET',
		datatype: 'json',
		timeout: 20000
	});
	jQueryTemp('#loading').ajaxStart(function() {
		jQueryTemp(this).show();
	});
	jQueryTemp('#loading').ajaxStop(function() {
		jQueryTemp(this).hide();
	});

	/* modal box
	------------------------------------------------------------------------- */
	gbox = {
		defaults: {
			autohide: false,
			buttons: {
				'بستن': function() {
					gbox.hide();
				}
			}
		},
		init: function() {
			var winHeight = jQueryTemp(window).height();
			var winWidth = jQueryTemp(window).width();
			var box =
				'<div id="gbox">' +
					'<div id="gbox_content"></div>' +
				'</div>' +
				'<div id="gbox_bg"></div>';

			jQueryTemp('body').append(box);

			jQueryTemp('#gbox').css({
				top: '15%',
				left: winWidth / 2 - jQueryTemp('#gbox').width() / 2
			});

			jQueryTemp('#gbox_close, #gbox_bg').click(gbox.hide);
		},
		show: function(options) {
			var options = jQueryTemp.extend({}, this.defaults, options);
			switch (options.type) {
				case 'ajax':
					jQueryTemp.ajax({
						type: 'GET',
						datatype: 'html',
						url: options.url,
						success: function(data) {
							options.content = data;
							gbox._show(options);
						}
					});
					break;
				default:
					this._show(options);
					break;
			}
		},
		_show: function(options) {
			jQueryTemp('#gbox_footer').remove();
			if (options.buttons) {
				jQueryTemp('#gbox').append('<div id="gbox_footer"></div>');
				jQueryTemp.each(options.buttons, function(k, v) {
					jQueryTemp('<button></button>').text(k).click(v).appendTo('#gbox_footer');
				});
			}

			jQueryTemp('#gbox, #gbox_bg').fadeIn();
			jQueryTemp('#gbox_content').html(options.content);
			jQueryTemp('#gbox_content input:first').focus();
			if (options.autohide) {
				setTimeout(function() {
					gbox.hide();
				}, options.autohide);
			}
		},
		hide: function() {
			jQueryTemp('#gbox').fadeOut(function() {
				jQueryTemp('#gbox_content').html('');
				jQueryTemp('#gbox_footer').remove();
			});
			jQueryTemp('#gbox_bg').fadeOut();
		}
	};
	gbox.init();

	/* same as site_url() in php
	------------------------------------------------------------------------- */
	function site_url(url) {
		return _BASE_URL + url;
	}

	/* nested sortables
	------------------------------------------------------------------------- */
	var menu_serialized;
	var fixSortable = function() {
		if (!jQueryTemp.browser.msie) return;
		//this is fix for ie
		jQueryTemp('#easymm').NestedSortableDestroy();
		jQueryTemp('#easymm').NestedSortable({
			accept: 'sortable',
			helperclass: 'ns-helper',
			rightToLeft: true,
			opacity: .8,
			handle: '.ns-title',
			onStop: function() {
				fixSortable();
			},
			onChange: function(serialized) {
				menu_serialized = serialized[0].hash;
				jQueryTemp('#btn-save-menu').attr('disabled', false);
			}
		});
	};
	jQueryTemp('#easymm').NestedSortable({
		accept: 'sortable',
		helperclass: 'ns-helper',
		opacity: .8,
		rightToLeft: true,
		handle: '.ns-title',
		onStop: function() {
			fixSortable();
		},
		onChange: function(serialized) {
			menu_serialized = serialized[0].hash;
			jQueryTemp('#btn-save-menu').attr('disabled', false);
		}
	});

	/* edit menu
	------------------------------------------------------------------------- */
	jQueryTemp('.edit-menu').live('click', function() {
		var menu_id = jQueryTemp(this).next().next().val();
		var menu_div = jQueryTemp(this).parent().parent();
		gbox.show({
			type: 'ajax',
			url: site_url('&do=editmenu&id=' + menu_id),
			buttons: {
				'ذخيره': function() {
					jQueryTemp.ajax({
						type: 'POST',
						url: jQueryTemp('#gbox form').attr('action'),
						data: jQueryTemp('#gbox form').serialize(),
						success: function(data) {
							switch (data.status) {
								case 1:
									gbox.hide();
									menu_div.find('.ns-title').html(data.menu.title);
									menu_div.find('.ns-url').html(data.menu.url);
									menu_div.find('.ns-class').html(data.menu.klass);
									break;
								case 2:
									gbox.hide();
									break;
							}
						}
					});
				},
				'انصراف': gbox.hide
			}
		});
		return false;
	});

	/* delete menu
	------------------------------------------------------------------------- */
	jQueryTemp('.delete-menu').live('click', function() {
		var li = jQueryTemp(this).closest('li');
		var param = { id : jQueryTemp(this).next().val() };
		var menu_title = jQueryTemp(this).parent().parent().children('.ns-title').text();
		gbox.show({
			content: '<h2>حذف منو</h2>آيا مطمئن هستيد كه ميخواهيد منو <b>'
				+ menu_title +
				'</b> را حذف نماييد ؟<br><br>اين عمليات غير قابل برگشت مي باشد  .',
			buttons: {
				'بلی': function() {
					jQueryTemp.post(site_url('&do=deletemenu'), param, function(data) {
						if (data.success) {
							gbox.hide();
							li.remove();
						} else {
							gbox.show({
								content: 'خطا در حذف منو .'
							});
						}
					});
				},
				'خیر': gbox.hide
			}
		});
		return false;
	});

	/* add menu
	------------------------------------------------------------------------- */
	jQueryTemp('#form-add-menu').submit(function() {
		if (jQueryTemp('#menu-title').val() == '') {
			jQueryTemp('#menu-title').focus();
		} else {
			jQueryTemp.ajax({
				type: 'POST',
				url: jQueryTemp(this).attr('action'),
				data: jQueryTemp(this).serialize(),
				error: function() {
					gbox.show({
						content: 'خطا در اضافه فهرست ؛ لطفا دوباره تلاش كنيد .',
						autohide: 1000
					});
				},
				success: function(data) {
					switch (data.status) {
						case 1:
							jQueryTemp('#form-add-menu')[0].reset();
							jQueryTemp('#easymm')
								.append(data.li)
								.SortableAddItem(jQueryTemp('#'+data.li_id)[0]);
							break;
						case 2:
							gbox.show({
								content: data.msg,
								autohide: 1000
							});
							break;
						case 3:
							jQueryTemp('#menu-title').val('').focus();
							break;
					}
				}
			});
		}
		return false;
	});

	jQueryTemp('#gbox form').live('submit', function() {
		return false;
	});

	/* add menu group
	------------------------------------------------------------------------- */
	jQueryTemp('#add-group a').click(function() {
		gbox.show({
			type: 'ajax',
			url: jQueryTemp(this).attr('href'),
			buttons: {
				'ذخيره': function() {
					var group_title = jQueryTemp('#menu-group-title').val();
					if (group_title == '') {
						jQueryTemp('#menu-group-title').focus();
					} else {
						//jQueryTemp('#gbox_ok').attr('disabled', true);
						jQueryTemp.ajax({
							type: 'POST',
							url: site_url('&do=addgroup'),
							data: 'title=' + group_title,
							error: function() {
								//jQueryTemp('#gbox_ok').attr('disabled', false);
							},
							success: function(data) {
								//jQueryTemp('#gbox_ok').attr('disabled', false);
								switch (data.status) {
									case 1:
										gbox.hide();
										jQueryTemp('#menu-group').append('<li><a href="' + site_url('&group_id=' + data.id) + '">' + group_title + '</a></li>');
										break;
									case 2:
										jQueryTemp('<span class="error"></span>')
											.text(data.msg)
											.prependTo('#gbox_footer')
											.delay(1000)
											.fadeOut(500, function() {
												jQueryTemp(this).remove();
											});
										break;
									case 3:
										jQueryTemp('#menu-group-title').val('').focus();
										break;
								}
							}
						});
					}
				},
				'انصراف': gbox.hide
			}
		});
		return false;
	});

	/* update menu / save menu position
	------------------------------------------------------------------------- */
	jQueryTemp('#btn-save-menu').attr('disabled', true);
	jQueryTemp('#form-menu').submit(function() {
		jQueryTemp('#btn-save-menu').attr('disabled', true);
		jQueryTemp.ajax({
			type: 'POST',
			url: jQueryTemp(this).attr('action'),
			data: menu_serialized,
			error: function() {
				jQueryTemp('#btn-save-menu').attr('disabled', false);
				gbox.show({
					content: '<h2>بروز خطا</h2>در ذخيره فهرست مشكلي پيدا شده است ! لطفا دوباره تلاش كنيد .',
					autohide: 3000
				});
			},
			success: function(data) {
				gbox.show({
					content: '<h2>تبريك</h2>تنظيمات فهرست بدرستي بروزرساني گرديد ',
					autohide: 3000
				});
			}
		});
		return false;
	});

	/* delete menu group
	------------------------------------------------------------------------- */
	jQueryTemp('#delete-group').click(function() {
		var group_title = jQueryTemp('#menu-group li.current a').text();
		var param = { id : current_group_id };
		gbox.show({
			content: '<h2>حذف گروه</h2>آيا مطمئن هستيد كه ميخواهيد فهرست <b>'
				+ group_title +
				'</b> را حذف نماييد ؟<br><br>اين عمليات غير قابل برگشت مي باشد .',
			buttons: {
				'بلی': function() {
					jQueryTemp.post(site_url('&do=deletegroup'), param, function(data) {
						if (data.success) {
							window.location = site_url('');
						} else {
							gbox.show({
								content: 'بروز خطا در حذف فهرست .'
							});
						}
					});
				},
				'خیر': gbox.hide
			}
		});
		return false;
	});
	
	/* Save menu group options
	------------------------------------------------------------------------- */
	jQueryTemp('#save-group-option').click(function() {
		var group_title = jQueryTemp('#edit-group-input').val();
		var group_style = jQueryTemp('#menu-group-style').val();
		if(group_title && group_style)
		{
			
			jQueryTemp.ajax({
				type: 'POST',
				url: site_url('&do=updategroup'),
				data: 'id=' + current_group_id + '&title=' + group_title + '&style=' + group_style,
				success: function(data) {
					if (data.success) {
						gbox.show({
							content: '<h2>تبريك</h2>تنظيمات فهرست بدرستي بروزرساني گرديد ',
							autohide: 3000
						});
						jQueryTemp('#group-' + current_group_id + ' a').text(title);
					} else {
							gbox.show({
								content: '<h2>بروز خطا</h2> خطا در بروزرساني تنظيمات فهرست . '
							});
						}
				}
			});
		
		} else {
			gbox.show({
					content: '<h2>بروز خطا</h2> خطا در بروزرساني تنظيمات فهرست . ',
					autohide: 3000
				});
		}
		jQueryTemp('#loading').hide();
		return false;
	});
	/* Add Page to menu
	------------------------------------------------------------ */
	jQueryTemp('#add-page-butt').click(function() {
		var addurl = '';
		var bi = 0;
		jQueryTemp('.extracbx:checked').each(function(a,b) {
			addurl += '&bt['+bi+']='+jQueryTemp('#extracbt_'+b.value+'').text()+'&bid['+bi+']='+b.value+'';
			bi++;
		});
		
		if(bi == 0) return;
		
		jQueryTemp.ajax({
			type: 'POST',
			url: site_url('&do=addmenu'),
			data: 'bulk1=true' + addurl + '&group_id=' + current_group_id ,
			error: function() {
				gbox.show({
					content: 'خطا در اضافه منو ؛ لطفا دوباره تلاش كنيد .',
					autohide: 1000
				});
			},
			success: function(data) {
				switch (data.status) {
					case 1:
						for(i = 0; i < data.count; i++)
						{
							eval('jQueryTemp(\'#easymm\').append(data.i'+i+'.li).SortableAddItem(jQueryTemp(\'#\'+data.i'+i+'.li_id)[0]);');
						}
						break;
					case 2:
						gbox.show({
							content: data.msg,
							autohide: 1000
						});
						break;
				}
			}
		});

		return false;
	});
	/* Add Plugin to menu
	------------------------------------------------------------ */
	jQueryTemp('#add-plugin-butt').click(function() {
		var addurl = '';
		var bi = 0;
		jQueryTemp('.plugincbx:checked').each(function(a,b) {
			addurl += '&bt['+bi+']='+jQueryTemp('#plugincbt_'+b.value+'').text()+'&bid['+bi+']='+b.value+'';
			bi++;
		});
		
		if(bi == 0) return;
		
		jQueryTemp.ajax({
			type: 'POST',
			url: site_url('&do=addmenu'),
			data: 'bulk3=true' + addurl + '&group_id=' + current_group_id ,
			error: function() {
				gbox.show({
					content: 'خطا در اضافه منو ؛ لطفا دوباره تلاش كنيد .',
					autohide: 1000
				});
			},
			success: function(data) {
				switch (data.status) {
					case 1:
						for(i = 0; i < data.count; i++)
						{
							eval('jQueryTemp(\'#easymm\').append(data.i'+i+'.li).SortableAddItem(jQueryTemp(\'#\'+data.i'+i+'.li_id)[0]);');
						}
						break;
					case 2:
						gbox.show({
							content: data.msg,
							autohide: 1000
						});
						break;
				}
			}
		});

		return false;
	});
	
	/* Add Data to menu
	------------------------------------------------------------ */
	jQueryTemp('#add-data-butt').click(function() {
		var addurl = '';
		var bi = 0;
		jQueryTemp('.datacbx:checked').each(function(a,b) {
			addurl += '&bt['+bi+']='+jQueryTemp('#datacbt_'+b.value+'').text()+'&bid['+bi+']='+b.value+'';
			bi++;
		});
		
		if(bi == 0) return;
		
		jQueryTemp.ajax({
			type: 'POST',
			url: site_url('&do=addmenu'),
			data: 'bulk2=true' + addurl + '&group_id=' + current_group_id ,
			error: function() {
				gbox.show({
					content: 'خطا در اضافه منو ؛ لطفا دوباره تلاش كنيد .',
					autohide: 1000
				});
			},
			success: function(data) {
				switch (data.status) {
					case 1:
						for(i = 0; i < data.count; i++)
						{
							eval('jQueryTemp(\'#easymm\').append(data.i'+i+'.li).SortableAddItem(jQueryTemp(\'#\'+data.i'+i+'.li_id)[0]);');
						}
						break;
					case 2:
						gbox.show({
							content: data.msg,
							autohide: 1000
						});
						break;
				}
			}
		});

		return false;
	});

});