            $(function() {
                $("#amazon_scroller1").amazon_scroller({
                    scroller_title_show: 'enable',
                    scroller_time_interval: '25000',
                    scroller_window_background_color: "",
                    scroller_window_padding: '10',
                    scroller_border_size: '1',
                    scroller_border_color: '',
                    scroller_images_width: '195',
                    scroller_images_height: '307',
                    scroller_title_size: '12',
                    scroller_title_color: 'black',
                    scroller_show_count: '4',
                    directory: 'images'
                });
				$("#amazon_scroller2").amazon_scroller({
                    scroller_title_show: 'disable',
                    scroller_time_interval: '20000',
                    scroller_window_background_color: "none",
                    scroller_window_padding: '10',
                    scroller_border_size: '0',
                    scroller_border_color: '#CCC',
                    scroller_images_width: '100',
                    scroller_images_height: '80',
                    scroller_title_size: '12',
                    scroller_title_color: 'black',
                    scroller_show_count: '2',
                    directory: 'images'
                });
				$("#amazon_scroller3").amazon_scroller({
                    scroller_title_show: 'enable',
                    scroller_time_interval: '25000',
                    scroller_window_background_color: "none",
                    scroller_window_padding: '10',
                    scroller_border_size: '2',
                    scroller_border_color: '#9C6',
                    scroller_images_width: '80',
                    scroller_images_height: '60',
                    scroller_title_size: '11',
                    scroller_title_color: 'black',
                    scroller_show_count: '3',
                    directory: 'images'
                });
            });
