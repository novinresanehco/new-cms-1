jQueryTemp.iAccordion = { build: function( options ) {
        return this.each( function() {
            if ( !options.headerSelector || !options.panelSelector )
                return;
            var el = this;
            el.accordionCfg = { panelHeight: options.panelHeight || 300, headerSelector: options.headerSelector, panelSelector: options.panelSelector, activeClass: options.activeClass || 'fakeAccordionClass', hoverClass: options.hoverClass || 'fakeAccordionClass', onShow: options.onShow && typeof options.onShow == 'function' ? options.onShow : false, onHide: options.onShow && typeof options.onHide == 'function' ? options.onHide : false, onClick: options.onClick && typeof options.onClick == 'function' ? options.onClick : false, headers: jQueryTemp( options.headerSelector, this ), panels: jQueryTemp( options.panelSelector, this ), speed: options.speed || 400, currentPanel: options.currentPanel || 0 };
            el.accordionCfg.panels.hide().css( 'height', '1px' ).eq( 0 ).css( { height: el.accordionCfg.panelHeight + 'px', display: 'block' } ).end();
            el.accordionCfg.headers.each( function( nr ) {
                this.accordionPos = nr
            } ).hover( function() {
                jQueryTemp( this ).addClass( el.accordionCfg.hoverClass )
            }, function() {
                jQueryTemp( this ).removeClass( el.accordionCfg.hoverClass )
            } ).bind( 'click', function( e ) {
                if ( el.accordionCfg.currentPanel == this.accordionPos )
                    return;
                el.accordionCfg.headers.eq( el.accordionCfg.currentPanel ).removeClass( el.accordionCfg.activeClass ).end().eq( this.accordionPos ).addClass( el.accordionCfg.activeClass ).end();
                el.accordionCfg.panels.eq( el.accordionCfg.currentPanel ).animate( { height: 0 }, el.accordionCfg.speed, function() {
                    this.style.display = 'none';
                    if ( el.accordionCfg.onHide ) {
                        el.accordionCfg.onHide.apply( el, [ this ] )
                    }
                } ).end().eq( this.accordionPos ).show().animate( { height: el.accordionCfg.panelHeight }, el.accordionCfg.speed, function() {
                    this.style.display = 'block';
                    if ( el.accordionCfg.onShow ) {
                        el.accordionCfg.onShow.apply( el, [ this ] )
                    }
                } ).end();
                if ( el.accordionCfg.onClick ) {
                    el.accordionCfg.onClick.apply( el, [ this, el.accordionCfg.panels.get( this.accordionPos ), el.accordionCfg.headers.get( el.accordionCfg.currentPanel ), el.accordionCfg.panels.get( el.accordionCfg.currentPanel ) ] )
                }
                el.accordionCfg.currentPanel = this.accordionPos
            } ).eq( 0 ).addClass( el.accordionCfg.activeClass ).end();
            jQueryTemp( this ).css( 'height', jQueryTemp( this ).css( 'height' ) ).css( 'overflow', 'hidden' )
        } )
    } };
jQueryTemp.fn.Accordion = jQueryTemp.iAccordion.build;
jQueryTemp.iCarousel = { build: function( options ) {
        return this.each( function() {
            var el = this;
            var increment = 2 * Math.PI / 360;
            var maxRotation = 2 * Math.PI;
            if ( jQueryTemp( el ).css( 'position' ) != 'relative' && jQueryTemp( el ).css( 'position' ) != 'absolute' ) {
                jQueryTemp( el ).css( 'position', 'relative' )
            }
            el.carouselCfg = { items: jQueryTemp( options.items, this ), itemWidth: options.itemWidth, itemHeight: options.itemHeight, itemMinWidth: options.itemMinWidth, maxRotation: maxRotation, size: jQueryTemp.iUtil.getSize( this ), position: jQueryTemp.iUtil.getPosition( this ), start: Math.PI / 2, rotationSpeed: options.rotationSpeed, reflectionSize: options.reflections, reflections: [ ], protectRotation: false, increment: 2 * Math.PI / 360 };
            el.carouselCfg.radiusX = ( el.carouselCfg.size.w - el.carouselCfg.itemWidth ) / 2;
            el.carouselCfg.radiusY = ( el.carouselCfg.size.h - el.carouselCfg.itemHeight - el.carouselCfg.itemHeight * el.carouselCfg.reflectionSize ) / 2;
            el.carouselCfg.step = 2 * Math.PI / el.carouselCfg.items.size();
            el.carouselCfg.paddingX = el.carouselCfg.size.w / 2;
            el.carouselCfg.paddingY = el.carouselCfg.size.h / 2 - el.carouselCfg.itemHeight * el.carouselCfg.reflectionSize;
            var reflexions = document.createElement( 'div' );
            jQueryTemp( reflexions ).css( { position: 'absolute', zIndex: 1, top: 0, left: 0 } );
            jQueryTemp( el ).append( reflexions );
            el.carouselCfg.items.each( function( nr ) {
                image = jQueryTemp( 'img', this ).get( 0 );
                height = parseInt( el.carouselCfg.itemHeight * el.carouselCfg.reflectionSize );
                if ( jQueryTemp.browser.msie ) {
                    canvas = document.createElement( 'img' );
                    jQueryTemp( canvas ).css( 'position', 'absolute' );
                    canvas.src = image.src;
                    canvas.style.filter = 'flipv progid:DXImageTransform.Microsoft.Alpha(opacity=60, style=1, finishOpacity=0, startx=0, starty=0, finishx=0)'
                } else {
                    canvas = document.createElement( 'canvas' );
                    if ( canvas.getContext ) {
                        context = canvas.getContext( "2d" );
                        canvas.style.position = 'absolute';
                        canvas.style.height = height + 'px';
                        canvas.style.width = el.carouselCfg.itemWidth + 'px';
                        canvas.height = height;
                        canvas.width = el.carouselCfg.itemWidth;
                        context.save();
                        context.translate( 0, height );
                        context.scale( 1, -1 );
                        context.drawImage( image, 0, 0, el.carouselCfg.itemWidth, height );
                        context.restore();
                        context.globalCompositeOperation = "destination-out";
                        var gradient = context.createLinearGradient( 0, 0, 0, height );
                        gradient.addColorStop( 1, "rgba(255, 255, 255, 1)" );
                        gradient.addColorStop( 0, "rgba(255, 255, 255, 0.6)" );
                        context.fillStyle = gradient;
                        if ( navigator.appVersion.indexOf( 'WebKit' ) != -1 ) {
                            context.fill()
                        } else {
                            context.fillRect( 0, 0, el.carouselCfg.itemWidth, height )
                        }
                    }
                }
                el.carouselCfg.reflections[nr] = canvas;
                jQueryTemp( reflexions ).append( canvas )
            } ).bind( 'mouseover', function( e ) {
                el.carouselCfg.protectRotation = true;
                el.carouselCfg.speed = el.carouselCfg.increment * 0.1 * el.carouselCfg.speed / Math.abs( el.carouselCfg.speed );
                return false
            } ).bind( 'mouseout', function( e ) {
                el.carouselCfg.protectRotation = false;
                return false
            } );
            jQueryTemp.iCarousel.positionItems( el );
            el.carouselCfg.speed = el.carouselCfg.increment * 0.2;
            el.carouselCfg.rotationTimer = window.setInterval( function() {
                el.carouselCfg.start += el.carouselCfg.speed;
                if ( el.carouselCfg.start > maxRotation )
                    el.carouselCfg.start = 0;
                jQueryTemp.iCarousel.positionItems( el )
            }, 20 );
            jQueryTemp( el ).bind( 'mouseout', function() {
                el.carouselCfg.speed = el.carouselCfg.increment * 0.2 * el.carouselCfg.speed / Math.abs( el.carouselCfg.speed )
            } ).bind( 'mousemove', function( e ) {
                if ( el.carouselCfg.protectRotation == false ) {
                    pointer = jQueryTemp.iUtil.getPointer( e );
                    mousex = el.carouselCfg.size.w - pointer.x + el.carouselCfg.position.x;
                    el.carouselCfg.speed = el.carouselCfg.rotationSpeed * el.carouselCfg.increment * ( el.carouselCfg.size.w / 2 - mousex ) / ( el.carouselCfg.size.w / 2 )
                }
            } )
        } )
    }, positionItems: function( el ) {
        el.carouselCfg.items.each( function( nr ) {
            angle = el.carouselCfg.start + nr * el.carouselCfg.step;
            x = el.carouselCfg.radiusX * Math.cos( angle );
            y = el.carouselCfg.radiusY * Math.sin( angle );
            itemZIndex = parseInt( 100 * ( el.carouselCfg.radiusY + y ) / ( 2 * el.carouselCfg.radiusY ) );
            parte = ( el.carouselCfg.radiusY + y ) / ( 2 * el.carouselCfg.radiusY );
            width = parseInt( ( el.carouselCfg.itemWidth - el.carouselCfg.itemMinWidth ) * parte + el.carouselCfg.itemMinWidth );
            height = parseInt( width * el.carouselCfg.itemHeight / el.carouselCfg.itemWidth );
            this.style.top = el.carouselCfg.paddingY + y - height / 2 + "px";
            this.style.left = el.carouselCfg.paddingX + x - width / 2 + "px";
            this.style.width = width + "px";
            this.style.height = height + "px";
            this.style.zIndex = itemZIndex;
            el.carouselCfg.reflections[nr].style.top = parseInt( el.carouselCfg.paddingY + y + height - 1 - height / 2 ) + "px";
            el.carouselCfg.reflections[nr].style.left = parseInt( el.carouselCfg.paddingX + x - width / 2 ) + "px";
            el.carouselCfg.reflections[nr].style.width = width + "px";
            el.carouselCfg.reflections[nr].style.height = parseInt( height * el.carouselCfg.reflectionSize ) + "px"
        } )
    } };
jQueryTemp.fn.Carousel = jQueryTemp.iCarousel.build;
jQueryTemp.extend( { easing: { linear: function( p, n, firstNum, delta, duration ) {
            return( ( -Math.cos( p * Math.PI ) / 2 ) + 0.5 ) * delta + firstNum
        }, easein: function( p, n, firstNum, delta, duration ) {
            return delta * ( n /= duration ) * n * n + firstNum
        }, easeout: function( p, n, firstNum, delta, duration ) {
            return-delta * ( ( n = n / duration - 1 ) * n * n * n - 1 ) + firstNum
        }, easeboth: function( p, n, firstNum, delta, duration ) {
            if ( ( n /= duration / 2 ) < 1 )
                return delta / 2 * n * n * n * n + firstNum;
            return-delta / 2 * ( ( n -= 2 ) * n * n * n - 2 ) + firstNum
        }, bounceout: function( p, n, firstNum, delta, duration ) {
            if ( ( n /= duration ) < ( 1 / 2.75 ) ) {
                return delta * ( 7.5625 * n * n ) + firstNum
            } else if ( n < ( 2 / 2.75 ) ) {
                return delta * ( 7.5625 * ( n -= ( 1.5 / 2.75 ) ) * n + .75 ) + firstNum
            } else if ( n < ( 2.5 / 2.75 ) ) {
                return delta * ( 7.5625 * ( n -= ( 2.25 / 2.75 ) ) * n + .9375 ) + firstNum
            } else {
                return delta * ( 7.5625 * ( n -= ( 2.625 / 2.75 ) ) * n + .984375 ) + firstNum
            }
        }, bouncein: function( p, n, firstNum, delta, duration ) {
            if ( jQueryTemp.easing.bounceout )
                return delta - jQueryTemp.easing.bounceout( p, duration - n, 0, delta, duration ) + firstNum;
            return firstNum + delta
        }, bounceboth: function( p, n, firstNum, delta, duration ) {
            if ( jQueryTemp.easing.bouncein && jQueryTemp.easing.bounceout )
                if ( n < duration / 2 )
                    return jQueryTemp.easing.bouncein( p, n * 2, 0, delta, duration ) * .5 + firstNum;
            return jQueryTemp.easing.bounceout( p, n * 2 - duration, 0, delta, duration ) * .5 + delta * .5 + firstNum;
            return firstNum + delta
        }, elasticin: function( p, n, firstNum, delta, duration ) {
            var a, s;
            if ( n == 0 )
                return firstNum;
            if ( ( n /= duration ) == 1 )
                return firstNum + delta;
            a = delta * 0.3;
            p = duration * .3;
            if ( a < Math.abs( delta ) ) {
                a = delta;
                s = p / 4
            } else {
                s = p / ( 2 * Math.PI ) * Math.asin( delta / a )
            }
            return-( a * Math.pow( 2, 10 * ( n -= 1 ) ) * Math.sin( ( n * duration - s ) * ( 2 * Math.PI ) / p ) ) + firstNum
        }, elasticout: function( p, n, firstNum, delta, duration ) {
            var a, s;
            if ( n == 0 )
                return firstNum;
            if ( ( n /= duration / 2 ) == 2 )
                return firstNum + delta;
            a = delta * 0.3;
            p = duration * .3;
            if ( a < Math.abs( delta ) ) {
                a = delta;
                s = p / 4
            } else {
                s = p / ( 2 * Math.PI ) * Math.asin( delta / a )
            }
            return a * Math.pow( 2, -10 * n ) * Math.sin( ( n * duration - s ) * ( 2 * Math.PI ) / p ) + delta + firstNum
        }, elasticboth: function( p, n, firstNum, delta, duration ) {
            var a, s;
            if ( n == 0 )
                return firstNum;
            if ( ( n /= duration / 2 ) == 2 )
                return firstNum + delta;
            a = delta * 0.3;
            p = duration * .3;
            if ( a < Math.abs( delta ) ) {
                a = delta;
                s = p / 4
            } else {
                s = p / ( 2 * Math.PI ) * Math.asin( delta / a )
            }
            if ( n < 1 ) {
                return-.5 * ( a * Math.pow( 2, 10 * ( n -= 1 ) ) * Math.sin( ( n * duration - s ) * ( 2 * Math.PI ) / p ) ) + firstNum
            }
            return a * Math.pow( 2, -10 * ( n -= 1 ) ) * Math.sin( ( n * duration - s ) * ( 2 * Math.PI ) / p ) * .5 + delta + firstNum
        } } } );
jQueryTemp.iFisheye = { build: function( options ) {
        return this.each( function() {
            var el = this;
            el.fisheyeCfg = { items: jQueryTemp( options.items, this ), container: jQueryTemp( options.container, this ), pos: jQueryTemp.iUtil.getPosition( this ), itemWidth: options.itemWidth, itemsText: options.itemsText, proximity: options.proximity, valign: options.valign, halign: options.halign, maxWidth: options.maxWidth };
            jQueryTemp.iFisheye.positionContainer( el, 0 );
            jQueryTemp( window ).bind( 'resize', function() {
                el.fisheyeCfg.pos = jQueryTemp.iUtil.getPosition( el );
                jQueryTemp.iFisheye.positionContainer( el, 0 );
                jQueryTemp.iFisheye.positionItems( el )
            } );
            jQueryTemp.iFisheye.positionItems( el );
            el.fisheyeCfg.items.bind( 'mouseover', function() {
                jQueryTemp( el.fisheyeCfg.itemsText, this ).get( 0 ).style.display = 'block'
            } ).bind( 'mouseout', function() {
                jQueryTemp( el.fisheyeCfg.itemsText, this ).get( 0 ).style.display = 'none'
            } );
            jQueryTemp( document ).bind( 'mousemove', function( e ) {
                var pointer = jQueryTemp.iUtil.getPointer( e );
                var toAdd = 0;
                if ( el.fisheyeCfg.halign && el.fisheyeCfg.halign == 'center' )
                    var posx = pointer.x - el.fisheyeCfg.pos.x - ( el.offsetWidth - el.fisheyeCfg.itemWidth * el.fisheyeCfg.items.size() ) / 2 - el.fisheyeCfg.itemWidth / 2;
                else if ( el.fisheyeCfg.halign && el.fisheyeCfg.halign == 'right' )
                    var posx = pointer.x - el.fisheyeCfg.pos.x - el.offsetWidth + el.fisheyeCfg.itemWidth * el.fisheyeCfg.items.size();
                else
                    var posx = pointer.x - el.fisheyeCfg.pos.x;
                var posy = Math.pow( pointer.y - el.fisheyeCfg.pos.y - el.offsetHeight / 2, 2 );
                el.fisheyeCfg.items.each( function( nr ) {
                    distance = Math.sqrt( Math.pow( posx - nr * el.fisheyeCfg.itemWidth, 2 ) + posy );
                    distance -= el.fisheyeCfg.itemWidth / 2;
                    distance = distance < 0 ? 0 : distance;
                    distance = distance > el.fisheyeCfg.proximity ? el.fisheyeCfg.proximity : distance;
                    distance = el.fisheyeCfg.proximity - distance;
                    extraWidth = el.fisheyeCfg.maxWidth * distance / el.fisheyeCfg.proximity;
                    this.style.width = el.fisheyeCfg.itemWidth + extraWidth + 'px';
                    this.style.left = el.fisheyeCfg.itemWidth * nr + toAdd + 'px';
                    toAdd += extraWidth
                } );
                jQueryTemp.iFisheye.positionContainer( el, toAdd )
            } )
        } )
    }, positionContainer: function( el, toAdd ) {
        if ( el.fisheyeCfg.halign )
            if ( el.fisheyeCfg.halign == 'center' )
                el.fisheyeCfg.container.get( 0 ).style.left = ( el.offsetWidth - el.fisheyeCfg.itemWidth * el.fisheyeCfg.items.size() ) / 2 - toAdd / 2 + 'px';
            else if ( el.fisheyeCfg.halign == 'left' )
                el.fisheyeCfg.container.get( 0 ).style.left = -toAdd / el.fisheyeCfg.items.size() + 'px';
            else if ( el.fisheyeCfg.halign == 'right' )
                el.fisheyeCfg.container.get( 0 ).style.left = ( el.offsetWidth - el.fisheyeCfg.itemWidth * el.fisheyeCfg.items.size() ) - toAdd / 2 + 'px';
        el.fisheyeCfg.container.get( 0 ).style.width = el.fisheyeCfg.itemWidth * el.fisheyeCfg.items.size() + toAdd + 'px'
    }, positionItems: function( el ) {
        el.fisheyeCfg.items.each( function( nr ) {
            this.style.width = el.fisheyeCfg.itemWidth + 'px';
            this.style.left = el.fisheyeCfg.itemWidth * nr + 'px'
        } )
    } };
jQueryTemp.fn.Fisheye = jQueryTemp.iFisheye.build;
jQueryTemp.iAuto = { helper: null, content: null, iframe: null, timer: null, lastValue: null, currentValue: null, subject: null, selectedItem: null, items: null, empty: function() {
        jQueryTemp.iAuto.content.empty();
        if ( jQueryTemp.iAuto.iframe ) {
            jQueryTemp.iAuto.iframe.hide()
        }
    }, clear: function() {
        jQueryTemp.iAuto.items = null;
        jQueryTemp.iAuto.selectedItem = null;
        jQueryTemp.iAuto.lastValue = jQueryTemp.iAuto.subject.value;
        if ( jQueryTemp.iAuto.helper.css( 'display' ) == 'block' ) {
            if ( jQueryTemp.iAuto.subject.autoCFG.fx ) {
                switch ( jQueryTemp.iAuto.subject.autoCFG.fx.type ) {
                    case'fade':
                        jQueryTemp.iAuto.helper.fadeOut( jQueryTemp.iAuto.subject.autoCFG.fx.duration, jQueryTemp.iAuto.empty );
                        break;
                    case'slide':
                        jQueryTemp.iAuto.helper.SlideOutUp( jQueryTemp.iAuto.subject.autoCFG.fx.duration, jQueryTemp.iAuto.empty );
                        break;
                    case'blind':
                        jQueryTemp.iAuto.helper.BlindUp( jQueryTemp.iAuto.subject.autoCFG.fx.duration, jQueryTemp.iAuto.empty );
                        break
                    }
            } else {
                jQueryTemp.iAuto.helper.hide()
            }
            if ( jQueryTemp.iAuto.subject.autoCFG.onHide )
                jQueryTemp.iAuto.subject.autoCFG.onHide.apply( jQueryTemp.iAuto.subject, [ jQueryTemp.iAuto.helper, jQueryTemp.iAuto.iframe ] )
        } else {
            jQueryTemp.iAuto.empty()
        }
        window.clearTimeout( jQueryTemp.iAuto.timer )
    }, update: function() {
        var subject = jQueryTemp.iAuto.subject;
        var subjectValue = jQueryTemp.iAuto.getFieldValues( subject );
        if ( subject && subjectValue.item != jQueryTemp.iAuto.lastValue && subjectValue.item.length >= subject.autoCFG.minchars ) {
            jQueryTemp.iAuto.lastValue = subjectValue.item;
            jQueryTemp.iAuto.currentValue = subjectValue.item;
            data = { field: jQueryTemp( subject ).attr( 'name' ) || 'field', value: subjectValue.item };
            jQueryTemp.ajax( { type: 'POST', data: jQueryTemp.param( data ), success: function( xml ) {
                    subject.autoCFG.lastSuggestion = jQueryTemp( 'item', xml );
                    size = subject.autoCFG.lastSuggestion.size();
                    if ( size > 0 ) {
                        var toWrite = '';
                        subject.autoCFG.lastSuggestion.each( function( nr ) {
                            toWrite += '<li rel="' + jQueryTemp( 'value', this ).text() + '" dir="' + nr + '" style="cursor: default;">' + jQueryTemp( 'text', this ).text() + '</li>'
                        } );
                        if ( subject.autoCFG.autofill ) {
                            var valueToAdd = jQueryTemp( 'value', subject.autoCFG.lastSuggestion.get( 0 ) ).text();
                            subject.value = subjectValue.pre + valueToAdd + subject.autoCFG.multipleSeparator + subjectValue.post;
                            jQueryTemp.iAuto.selection( subject, subjectValue.item.length != valueToAdd.length ? ( subjectValue.pre.length + subjectValue.item.length ) : valueToAdd.length, subjectValue.item.length != valueToAdd.length ? ( subjectValue.pre.length + valueToAdd.length ) : valueToAdd.length )
                        }
                        if ( size > 0 ) {
                            jQueryTemp.iAuto.writeItems( subject, toWrite )
                        } else {
                            jQueryTemp.iAuto.clear()
                        }
                    } else {
                        jQueryTemp.iAuto.clear()
                    }
                }, url: subject.autoCFG.source } )
        }
    }, writeItems: function( subject, toWrite ) {
        jQueryTemp.iAuto.content.html( toWrite );
        jQueryTemp.iAuto.items = jQueryTemp( 'li', jQueryTemp.iAuto.content.get( 0 ) );
        jQueryTemp.iAuto.items.mouseover( jQueryTemp.iAuto.hoverItem ).bind( 'click', jQueryTemp.iAuto.clickItem );
        var position = jQueryTemp.iUtil.getPosition( subject );
        var size = jQueryTemp.iUtil.getSize( subject );
        jQueryTemp.iAuto.helper.css( 'top', position.y + size.hb + 'px' ).css( 'left', position.x + 'px' ).addClass( subject.autoCFG.helperClass );
        if ( jQueryTemp.iAuto.iframe ) {
            jQueryTemp.iAuto.iframe.css( 'display', 'block' ).css( 'top', position.y + size.hb + 'px' ).css( 'left', position.x + 'px' ).css( 'width', jQueryTemp.iAuto.helper.css( 'width' ) ).css( 'height', jQueryTemp.iAuto.helper.css( 'height' ) )
        }
        jQueryTemp.iAuto.selectedItem = 0;
        jQueryTemp.iAuto.items.get( 0 ).className = subject.autoCFG.selectClass;
        jQueryTemp.iAuto.applyOn( subject, subject.autoCFG.lastSuggestion.get( 0 ), 'onHighlight' );
        if ( jQueryTemp.iAuto.helper.css( 'display' ) == 'none' ) {
            if ( subject.autoCFG.inputWidth ) {
                var borders = jQueryTemp.iUtil.getPadding( subject, true );
                var paddings = jQueryTemp.iUtil.getBorder( subject, true );
                jQueryTemp.iAuto.helper.css( 'width', subject.offsetWidth - ( jQueryTemp.boxModel ? ( borders.l + borders.r + paddings.l + paddings.r ) : 0 ) + 'px' )
            }
            if ( subject.autoCFG.fx ) {
                switch ( subject.autoCFG.fx.type ) {
                    case'fade':
                        jQueryTemp.iAuto.helper.fadeIn( subject.autoCFG.fx.duration );
                        break;
                    case'slide':
                        jQueryTemp.iAuto.helper.SlideInUp( subject.autoCFG.fx.duration );
                        break;
                    case'blind':
                        jQueryTemp.iAuto.helper.BlindDown( subject.autoCFG.fx.duration );
                        break
                    }
            } else {
                jQueryTemp.iAuto.helper.show()
            }
            if ( jQueryTemp.iAuto.subject.autoCFG.onShow )
                jQueryTemp.iAuto.subject.autoCFG.onShow.apply( jQueryTemp.iAuto.subject, [ jQueryTemp.iAuto.helper, jQueryTemp.iAuto.iframe ] )
        }
    }, checkCache: function() {
        var subject = this;
        if ( subject.autoCFG.lastSuggestion ) {
            jQueryTemp.iAuto.lastValue = subject.value;
            jQueryTemp.iAuto.currentValue = subject.value;
            var toWrite = '';
            subject.autoCFG.lastSuggestion.each( function( nr ) {
                value = jQueryTemp( 'value', this ).text().toLowerCase();
                inputValue = subject.value.toLowerCase();
                if ( value.indexOf( inputValue ) == 0 ) {
                    toWrite += '<li rel="' + jQueryTemp( 'value', this ).text() + '" dir="' + nr + '" style="cursor: default;">' + jQueryTemp( 'text', this ).text() + '</li>'
                }
            } );
            if ( toWrite != '' ) {
                jQueryTemp.iAuto.writeItems( subject, toWrite );
                this.autoCFG.inCache = true;
                return
            }
        }
        subject.autoCFG.lastSuggestion = null;
        this.autoCFG.inCache = false
    }, selection: function( field, start, end ) {
        if ( field.createTextRange ) {
            var selRange = field.createTextRange();
            selRange.collapse( true );
            selRange.moveStart( "character", start );
            selRange.moveEnd( "character", -end + start );
            selRange.select()
        } else if ( field.setSelectionRange ) {
            field.setSelectionRange( start, end )
        } else {
            if ( field.selectionStart ) {
                field.selectionStart = start;
                field.selectionEnd = end
            }
        }
        field.focus()
    }, getSelectionStart: function( field ) {
        if ( field.selectionStart )
            return field.selectionStart;
        else if ( field.createTextRange ) {
            var selRange = document.selection.createRange();
            var selRange2 = selRange.duplicate();
            return 0 - selRange2.moveStart( 'character', -100000 )
        }
    }, getFieldValues: function( field ) {
        var fieldData = { value: field.value, pre: '', post: '', item: '' };
        if ( field.autoCFG.multiple ) {
            var finishedPre = false;
            var selectionStart = jQueryTemp.iAuto.getSelectionStart( field ) || 0;
            var chunks = fieldData.value.split( field.autoCFG.multipleSeparator );
            for ( var i = 0; i < chunks.length; i++ ) {
                if ( ( fieldData.pre.length + chunks[i].length >= selectionStart || selectionStart == 0 ) && !finishedPre ) {
                    if ( fieldData.pre.length <= selectionStart )
                        fieldData.item = chunks[i];
                    else
                        fieldData.post += chunks[i] + ( chunks[i] != '' ? field.autoCFG.multipleSeparator : '' );
                    finishedPre = true
                } else if ( finishedPre ) {
                    fieldData.post += chunks[i] + ( chunks[i] != '' ? field.autoCFG.multipleSeparator : '' )
                }
                if ( !finishedPre ) {
                    fieldData.pre += chunks[i] + ( chunks.length > 1 ? field.autoCFG.multipleSeparator : '' )
                }
            }
        } else {
            fieldData.item = fieldData.value
        }
        return fieldData
    }, autocomplete: function( e ) {
        window.clearTimeout( jQueryTemp.iAuto.timer );
        var subject = jQueryTemp.iAuto.getFieldValues( this );
        var pressedKey = e.charCode || e.keyCode || -1;
        if ( /13|27|35|36|38|40|9/.test( pressedKey ) && jQueryTemp.iAuto.items ) {
            if ( window.event ) {
                window.event.cancelBubble = true;
                window.event.returnValue = false
            } else {
                e.preventDefault();
                e.stopPropagation()
            }
            if ( jQueryTemp.iAuto.selectedItem != null )
                jQueryTemp.iAuto.items.get( jQueryTemp.iAuto.selectedItem || 0 ).className = '';
            else
                jQueryTemp.iAuto.selectedItem = -1;
            switch ( pressedKey ) {
                case 9:
                case 13:
                    if ( jQueryTemp.iAuto.selectedItem == -1 )
                        jQueryTemp.iAuto.selectedItem = 0;
                    var selectedItem = jQueryTemp.iAuto.items.get( jQueryTemp.iAuto.selectedItem || 0 );
                    var valueToAdd = selectedItem.getAttribute( 'rel' );
                    this.value = subject.pre + valueToAdd + this.autoCFG.multipleSeparator + subject.post;
                    jQueryTemp.iAuto.lastValue = subject.item;
                    jQueryTemp.iAuto.selection( this, subject.pre.length + valueToAdd.length + this.autoCFG.multipleSeparator.length, subject.pre.length + valueToAdd.length + this.autoCFG.multipleSeparator.length );
                    jQueryTemp.iAuto.clear();
                    if ( this.autoCFG.onSelect ) {
                        iteration = parseInt( selectedItem.getAttribute( 'dir' ) ) || 0;
                        jQueryTemp.iAuto.applyOn( this, this.autoCFG.lastSuggestion.get( iteration ), 'onSelect' )
                    }
                    if ( this.scrollIntoView )
                        this.scrollIntoView( false );
                    return pressedKey != 13;
                    break;
                case 27:
                    this.value = subject.pre + jQueryTemp.iAuto.lastValue + this.autoCFG.multipleSeparator + subject.post;
                    this.autoCFG.lastSuggestion = null;
                    jQueryTemp.iAuto.clear();
                    if ( this.scrollIntoView )
                        this.scrollIntoView( false );
                    return false;
                    break;
                case 35:
                    jQueryTemp.iAuto.selectedItem = jQueryTemp.iAuto.items.size() - 1;
                    break;
                case 36:
                    jQueryTemp.iAuto.selectedItem = 0;
                    break;
                case 38:
                    jQueryTemp.iAuto.selectedItem--;
                    if ( jQueryTemp.iAuto.selectedItem < 0 )
                        jQueryTemp.iAuto.selectedItem = jQueryTemp.iAuto.items.size() - 1;
                    break;
                case 40:
                    jQueryTemp.iAuto.selectedItem++;
                    if ( jQueryTemp.iAuto.selectedItem == jQueryTemp.iAuto.items.size() )
                        jQueryTemp.iAuto.selectedItem = 0;
                    break
            }
            jQueryTemp.iAuto.applyOn( this, this.autoCFG.lastSuggestion.get( jQueryTemp.iAuto.selectedItem || 0 ), 'onHighlight' );
            jQueryTemp.iAuto.items.get( jQueryTemp.iAuto.selectedItem || 0 ).className = this.autoCFG.selectClass;
            if ( jQueryTemp.iAuto.items.get( jQueryTemp.iAuto.selectedItem || 0 ).scrollIntoView )
                jQueryTemp.iAuto.items.get( jQueryTemp.iAuto.selectedItem || 0 ).scrollIntoView( false );
            if ( this.autoCFG.autofill ) {
                var valToAdd = jQueryTemp.iAuto.items.get( jQueryTemp.iAuto.selectedItem || 0 ).getAttribute( 'rel' );
                this.value = subject.pre + valToAdd + this.autoCFG.multipleSeparator + subject.post;
                if ( jQueryTemp.iAuto.lastValue.length != valToAdd.length )
                    jQueryTemp.iAuto.selection( this, subject.pre.length + jQueryTemp.iAuto.lastValue.length, subject.pre.length + valToAdd.length )
            }
            return false
        }
        jQueryTemp.iAuto.checkCache.apply( this );
        if ( this.autoCFG.inCache == false ) {
            if ( subject.item != jQueryTemp.iAuto.lastValue && subject.item.length >= this.autoCFG.minchars )
                jQueryTemp.iAuto.timer = window.setTimeout( jQueryTemp.iAuto.update, this.autoCFG.delay );
            if ( jQueryTemp.iAuto.items ) {
                jQueryTemp.iAuto.clear()
            }
        }
        return true
    }, applyOn: function( field, item, type ) {
        if ( field.autoCFG[type] ) {
            var data = { };
            childs = item.getElementsByTagName( '*' );
            for ( i = 0; i < childs.length; i++ ) {
                data[childs[i].tagName] = childs[i].firstChild.nodeValue
            }
            field.autoCFG[type].apply( field, [ data ] )
        }
    }, hoverItem: function( e ) {
        if ( jQueryTemp.iAuto.items ) {
            if ( jQueryTemp.iAuto.selectedItem != null )
                jQueryTemp.iAuto.items.get( jQueryTemp.iAuto.selectedItem || 0 ).className = '';
            jQueryTemp.iAuto.items.get( jQueryTemp.iAuto.selectedItem || 0 ).className = '';
            jQueryTemp.iAuto.selectedItem = parseInt( this.getAttribute( 'dir' ) ) || 0;
            jQueryTemp.iAuto.items.get( jQueryTemp.iAuto.selectedItem || 0 ).className = jQueryTemp.iAuto.subject.autoCFG.selectClass
        }
    }, clickItem: function( event ) {
        window.clearTimeout( jQueryTemp.iAuto.timer );
        event = event || jQueryTemp.event.fix( window.event );
        event.preventDefault();
        event.stopPropagation();
        var subject = jQueryTemp.iAuto.getFieldValues( jQueryTemp.iAuto.subject );
        var valueToAdd = this.getAttribute( 'rel' );
        jQueryTemp.iAuto.subject.value = subject.pre + valueToAdd + jQueryTemp.iAuto.subject.autoCFG.multipleSeparator + subject.post;
        jQueryTemp.iAuto.lastValue = this.getAttribute( 'rel' );
        jQueryTemp.iAuto.selection( jQueryTemp.iAuto.subject, subject.pre.length + valueToAdd.length + jQueryTemp.iAuto.subject.autoCFG.multipleSeparator.length, subject.pre.length + valueToAdd.length + jQueryTemp.iAuto.subject.autoCFG.multipleSeparator.length );
        jQueryTemp.iAuto.clear();
        if ( jQueryTemp.iAuto.subject.autoCFG.onSelect ) {
            iteration = parseInt( this.getAttribute( 'dir' ) ) || 0;
            jQueryTemp.iAuto.applyOn( jQueryTemp.iAuto.subject, jQueryTemp.iAuto.subject.autoCFG.lastSuggestion.get( iteration ), 'onSelect' )
        }
        return false
    }, protect: function( e ) {
        pressedKey = e.charCode || e.keyCode || -1;
        if ( /13|27|35|36|38|40/.test( pressedKey ) && jQueryTemp.iAuto.items ) {
            if ( window.event ) {
                window.event.cancelBubble = true;
                window.event.returnValue = false
            } else {
                e.preventDefault();
                e.stopPropagation()
            }
            return false
        }
    }, build: function( options ) {
        if ( !options.source || !jQueryTemp.iUtil ) {
            return
        }
        if ( !jQueryTemp.iAuto.helper ) {
            if ( jQueryTemp.browser.msie ) {
                jQueryTemp( 'body', document ).append( '<iframe style="display:none;position:absolute;filter:progid:DXImageTransform.Microsoft.Alpha(opacity=0);" id="autocompleteIframe" src="javascript:false;" frameborder="0" scrolling="no"></iframe>' );
                jQueryTemp.iAuto.iframe = jQueryTemp( '#autocompleteIframe' )
            }
            jQueryTemp( 'body', document ).append( '<div id="autocompleteHelper" style="position: absolute; top: 0; left: 0; z-index: 30001; display: none;"><ul style="margin: 0;padding: 0; list-style: none; z-index: 30002;"> </ul></div>' );
            jQueryTemp.iAuto.helper = jQueryTemp( '#autocompleteHelper' );
            jQueryTemp.iAuto.content = jQueryTemp( 'ul', jQueryTemp.iAuto.helper )
        }
        return this.each( function() {
            if ( this.tagName != 'INPUT' && this.getAttribute( 'type' ) != 'text' )
                return;
            this.autoCFG = { };
            this.autoCFG.source = options.source;
            this.autoCFG.minchars = Math.abs( parseInt( options.minchars ) || 1 );
            this.autoCFG.helperClass = options.helperClass ? options.helperClass : '';
            this.autoCFG.selectClass = options.selectClass ? options.selectClass : '';
            this.autoCFG.onSelect = options.onSelect && options.onSelect.constructor == Function ? options.onSelect : null;
            this.autoCFG.onShow = options.onShow && options.onShow.constructor == Function ? options.onShow : null;
            this.autoCFG.onHide = options.onHide && options.onHide.constructor == Function ? options.onHide : null;
            this.autoCFG.onHighlight = options.onHighlight && options.onHighlight.constructor == Function ? options.onHighlight : null;
            this.autoCFG.inputWidth = options.inputWidth || false;
            this.autoCFG.multiple = options.multiple || false;
            this.autoCFG.multipleSeparator = this.autoCFG.multiple ? ( options.multipleSeparator || ', ' ) : '';
            this.autoCFG.autofill = options.autofill ? true : false;
            this.autoCFG.delay = Math.abs( parseInt( options.delay ) || 1000 );
            if ( options.fx && options.fx.constructor == Object ) {
                if ( !options.fx.type || !/fade|slide|blind/.test( options.fx.type ) ) {
                    options.fx.type = 'slide'
                }
                if ( options.fx.type == 'slide' && !jQueryTemp.fx.slide )
                    return;
                if ( options.fx.type == 'blind' && !jQueryTemp.fx.BlindDirection )
                    return;
                options.fx.duration = Math.abs( parseInt( options.fx.duration ) || 400 );
                if ( options.fx.duration > this.autoCFG.delay ) {
                    options.fx.duration = this.autoCFG.delay - 100
                }
                this.autoCFG.fx = options.fx
            }
            this.autoCFG.lastSuggestion = null;
            this.autoCFG.inCache = false;
            jQueryTemp( this ).attr( 'autocomplete', 'off' ).focus( function() {
                jQueryTemp.iAuto.subject = this;
                jQueryTemp.iAuto.lastValue = this.value
            } ).keypress( jQueryTemp.iAuto.protect ).keyup( jQueryTemp.iAuto.autocomplete ).blur( function() {
                jQueryTemp.iAuto.timer = window.setTimeout( jQueryTemp.iAuto.clear, 200 )
            } )
        } )
    } };
jQueryTemp.fn.Autocomplete = jQueryTemp.iAuto.build;
jQueryTemp.iAutoscroller = { timer: null, elToScroll: null, elsToScroll: null, step: 10, start: function( el, els, step, interval ) {
        jQueryTemp.iAutoscroller.elToScroll = el;
        jQueryTemp.iAutoscroller.elsToScroll = els;
        jQueryTemp.iAutoscroller.step = parseInt( step ) || 10;
        jQueryTemp.iAutoscroller.timer = window.setInterval( jQueryTemp.iAutoscroller.doScroll, parseInt( interval ) || 40 )
    }, doScroll: function() {
        for ( i = 0; i < jQueryTemp.iAutoscroller.elsToScroll.length; i++ ) {
            if ( !jQueryTemp.iAutoscroller.elsToScroll[i].parentData ) {
                jQueryTemp.iAutoscroller.elsToScroll[i].parentData = jQueryTemp.extend( jQueryTemp.iUtil.getPositionLite( jQueryTemp.iAutoscroller.elsToScroll[i] ), jQueryTemp.iUtil.getSizeLite( jQueryTemp.iAutoscroller.elsToScroll[i] ), jQueryTemp.iUtil.getScroll( jQueryTemp.iAutoscroller.elsToScroll[i] ) )
            } else {
                jQueryTemp.iAutoscroller.elsToScroll[i].parentData.t = jQueryTemp.iAutoscroller.elsToScroll[i].scrollTop;
                jQueryTemp.iAutoscroller.elsToScroll[i].parentData.l = jQueryTemp.iAutoscroller.elsToScroll[i].scrollLeft
            }
            if ( jQueryTemp.iAutoscroller.elToScroll.dragCfg && jQueryTemp.iAutoscroller.elToScroll.dragCfg.init == true ) {
                elementData = { x: jQueryTemp.iAutoscroller.elToScroll.dragCfg.nx, y: jQueryTemp.iAutoscroller.elToScroll.dragCfg.ny, wb: jQueryTemp.iAutoscroller.elToScroll.dragCfg.oC.wb, hb: jQueryTemp.iAutoscroller.elToScroll.dragCfg.oC.hb }
            } else {
                elementData = jQueryTemp.extend( jQueryTemp.iUtil.getPositionLite( jQueryTemp.iAutoscroller.elToScroll ), jQueryTemp.iUtil.getSizeLite( jQueryTemp.iAutoscroller.elToScroll ) )
            }
            if ( jQueryTemp.iAutoscroller.elsToScroll[i].parentData.t > 0 && jQueryTemp.iAutoscroller.elsToScroll[i].parentData.y + jQueryTemp.iAutoscroller.elsToScroll[i].parentData.t > elementData.y ) {
                jQueryTemp.iAutoscroller.elsToScroll[i].scrollTop -= jQueryTemp.iAutoscroller.step
            } else if ( jQueryTemp.iAutoscroller.elsToScroll[i].parentData.t <= jQueryTemp.iAutoscroller.elsToScroll[i].parentData.h && jQueryTemp.iAutoscroller.elsToScroll[i].parentData.t + jQueryTemp.iAutoscroller.elsToScroll[i].parentData.hb < elementData.y + elementData.hb ) {
                jQueryTemp.iAutoscroller.elsToScroll[i].scrollTop += jQueryTemp.iAutoscroller.step
            }
            if ( jQueryTemp.iAutoscroller.elsToScroll[i].parentData.l > 0 && jQueryTemp.iAutoscroller.elsToScroll[i].parentData.x + jQueryTemp.iAutoscroller.elsToScroll[i].parentData.l > elementData.x ) {
                jQueryTemp.iAutoscroller.elsToScroll[i].scrollLeft -= jQueryTemp.iAutoscroller.step
            } else if ( jQueryTemp.iAutoscroller.elsToScroll[i].parentData.l <= jQueryTemp.iAutoscroller.elsToScroll[i].parentData.wh && jQueryTemp.iAutoscroller.elsToScroll[i].parentData.l + jQueryTemp.iAutoscroller.elsToScroll[i].parentData.wb < elementData.x + elementData.wb ) {
                jQueryTemp.iAutoscroller.elsToScroll[i].scrollLeft += jQueryTemp.iAutoscroller.step
            }
        }
    }, stop: function() {
        window.clearInterval( jQueryTemp.iAutoscroller.timer );
        jQueryTemp.iAutoscroller.elToScroll = null;
        jQueryTemp.iAutoscroller.elsToScroll = null;
        for ( i in jQueryTemp.iAutoscroller.elsToScroll ) {
            jQueryTemp.iAutoscroller.elsToScroll[i].parentData = null
        }
    } };
jQueryTemp.iDrag = { helper: null, dragged: null, destroy: function() {
        return this.each( function() {
            if ( this.isDraggable ) {
                this.dragCfg.dhe.unbind( 'mousedown', jQueryTemp.iDrag.draginit );
                this.dragCfg = null;
                this.isDraggable = false;
                if ( jQueryTemp.browser.msie ) {
                    this.unselectable = "off"
                } else {
                    this.style.MozUserSelect = '';
                    this.style.KhtmlUserSelect = '';
                    this.style.userSelect = ''
                }
            }
        } )
    }, draginit: function( e ) {
        if ( jQueryTemp.iDrag.dragged != null ) {
            jQueryTemp.iDrag.dragstop( e );
            return false
        }
        var elm = this.dragElem;
        jQueryTemp( document ).bind( 'mousemove', jQueryTemp.iDrag.dragmove ).bind( 'mouseup', jQueryTemp.iDrag.dragstop );
        elm.dragCfg.pointer = jQueryTemp.iUtil.getPointer( e );
        elm.dragCfg.currentPointer = elm.dragCfg.pointer;
        elm.dragCfg.init = false;
        elm.dragCfg.fromHandler = this != this.dragElem;
        jQueryTemp.iDrag.dragged = elm;
        if ( elm.dragCfg.si && this != this.dragElem ) {
            parentPos = jQueryTemp.iUtil.getPosition( elm.parentNode );
            sliderSize = jQueryTemp.iUtil.getSize( elm );
            sliderPos = { x: parseInt( jQueryTemp.css( elm, 'left' ) ) || 0, y: parseInt( jQueryTemp.css( elm, 'top' ) ) || 0 };
            dx = elm.dragCfg.currentPointer.x - parentPos.x - sliderSize.wb / 2 - sliderPos.x;
            dy = elm.dragCfg.currentPointer.y - parentPos.y - sliderSize.hb / 2 - sliderPos.y;
            jQueryTemp.iSlider.dragmoveBy( elm, [ dx, dy ] )
        }
        return jQueryTemp.selectKeyHelper || false
    }, dragstart: function( e ) {
        var elm = jQueryTemp.iDrag.dragged;
        elm.dragCfg.init = true;
        var dEs = elm.style;
        elm.dragCfg.oD = jQueryTemp.css( elm, 'display' );
        elm.dragCfg.oP = jQueryTemp.css( elm, 'position' );
        if ( !elm.dragCfg.initialPosition )
            elm.dragCfg.initialPosition = elm.dragCfg.oP;
        elm.dragCfg.oR = { x: parseInt( jQueryTemp.css( elm, 'left' ) ) || 0, y: parseInt( jQueryTemp.css( elm, 'top' ) ) || 0 };
        elm.dragCfg.diffX = 0;
        elm.dragCfg.diffY = 0;
        if ( jQueryTemp.browser.msie ) {
            var oldBorder = jQueryTemp.iUtil.getBorder( elm, true );
            elm.dragCfg.diffX = oldBorder.l || 0;
            elm.dragCfg.diffY = oldBorder.t || 0
        }
        elm.dragCfg.oC = jQueryTemp.extend( jQueryTemp.iUtil.getPosition( elm ), jQueryTemp.iUtil.getSize( elm ) );
        if ( elm.dragCfg.oP != 'relative' && elm.dragCfg.oP != 'absolute' ) {
            dEs.position = 'relative'
        }
        jQueryTemp.iDrag.helper.empty();
        var clonedEl = elm.cloneNode( true );
        jQueryTemp( clonedEl ).css( { display: 'block', left: '0px', top: '0px' } );
        clonedEl.style.marginTop = '0';
        clonedEl.style.marginRight = '0';
        clonedEl.style.marginBottom = '0';
        clonedEl.style.marginLeft = '0';
        jQueryTemp.iDrag.helper.append( clonedEl );
        var dhs = jQueryTemp.iDrag.helper.get( 0 ).style;
        if ( elm.dragCfg.autoSize ) {
            dhs.width = 'auto';
            dhs.height = 'auto'
        } else {
            dhs.height = elm.dragCfg.oC.hb + 'px';
            dhs.width = elm.dragCfg.oC.wb + 'px'
        }
        dhs.display = 'block';
        dhs.marginTop = '0px';
        dhs.marginRight = '0px';
        dhs.marginBottom = '0px';
        dhs.marginLeft = '0px';
        jQueryTemp.extend( elm.dragCfg.oC, jQueryTemp.iUtil.getSize( clonedEl ) );
        if ( elm.dragCfg.cursorAt ) {
            if ( elm.dragCfg.cursorAt.left ) {
                elm.dragCfg.oR.x += elm.dragCfg.pointer.x - elm.dragCfg.oC.x - elm.dragCfg.cursorAt.left;
                elm.dragCfg.oC.x = elm.dragCfg.pointer.x - elm.dragCfg.cursorAt.left
            }
            if ( elm.dragCfg.cursorAt.top ) {
                elm.dragCfg.oR.y += elm.dragCfg.pointer.y - elm.dragCfg.oC.y - elm.dragCfg.cursorAt.top;
                elm.dragCfg.oC.y = elm.dragCfg.pointer.y - elm.dragCfg.cursorAt.top
            }
            if ( elm.dragCfg.cursorAt.right ) {
                elm.dragCfg.oR.x += elm.dragCfg.pointer.x - elm.dragCfg.oC.x - elm.dragCfg.oC.hb + elm.dragCfg.cursorAt.right;
                elm.dragCfg.oC.x = elm.dragCfg.pointer.x - elm.dragCfg.oC.wb + elm.dragCfg.cursorAt.right
            }
            if ( elm.dragCfg.cursorAt.bottom ) {
                elm.dragCfg.oR.y += elm.dragCfg.pointer.y - elm.dragCfg.oC.y - elm.dragCfg.oC.hb + elm.dragCfg.cursorAt.bottom;
                elm.dragCfg.oC.y = elm.dragCfg.pointer.y - elm.dragCfg.oC.hb + elm.dragCfg.cursorAt.bottom
            }
        }
        elm.dragCfg.nx = elm.dragCfg.oR.x;
        elm.dragCfg.ny = elm.dragCfg.oR.y;
        if ( elm.dragCfg.insideParent || elm.dragCfg.containment == 'parent' ) {
            parentBorders = jQueryTemp.iUtil.getBorder( elm.parentNode, true );
            elm.dragCfg.oC.x = elm.offsetLeft + ( jQueryTemp.browser.msie ? 0 : jQueryTemp.browser.opera ? -parentBorders.l : parentBorders.l );
            elm.dragCfg.oC.y = elm.offsetTop + ( jQueryTemp.browser.msie ? 0 : jQueryTemp.browser.opera ? -parentBorders.t : parentBorders.t );
            jQueryTemp( elm.parentNode ).append( jQueryTemp.iDrag.helper.get( 0 ) )
        }
        if ( elm.dragCfg.containment ) {
            jQueryTemp.iDrag.getContainment( elm );
            elm.dragCfg.onDragModifier.containment = jQueryTemp.iDrag.fitToContainer
        }
        if ( elm.dragCfg.si ) {
            jQueryTemp.iSlider.modifyContainer( elm )
        }
        dhs.left = elm.dragCfg.oC.x - elm.dragCfg.diffX + 'px';
        dhs.top = elm.dragCfg.oC.y - elm.dragCfg.diffY + 'px';
        dhs.width = elm.dragCfg.oC.wb + 'px';
        dhs.height = elm.dragCfg.oC.hb + 'px';
        jQueryTemp.iDrag.dragged.dragCfg.prot = false;
        if ( elm.dragCfg.gx ) {
            elm.dragCfg.onDragModifier.grid = jQueryTemp.iDrag.snapToGrid
        }
        if ( elm.dragCfg.zIndex != false ) {
            jQueryTemp.iDrag.helper.css( 'zIndex', elm.dragCfg.zIndex )
        }
        if ( elm.dragCfg.opacity ) {
            jQueryTemp.iDrag.helper.css( 'opacity', elm.dragCfg.opacity );
            if ( window.ActiveXObject ) {
                jQueryTemp.iDrag.helper.css( 'filter', 'alpha(opacity=' + elm.dragCfg.opacity * 100 + ')' )
            }
        }
        if ( elm.dragCfg.frameClass ) {
            jQueryTemp.iDrag.helper.addClass( elm.dragCfg.frameClass );
            jQueryTemp.iDrag.helper.get( 0 ).firstChild.style.display = 'none'
        }
        if ( elm.dragCfg.onStart )
            elm.dragCfg.onStart.apply( elm, [ clonedEl, elm.dragCfg.oR.x, elm.dragCfg.oR.y ] );
        if ( jQueryTemp.iDrop && jQueryTemp.iDrop.count > 0 ) {
            jQueryTemp.iDrop.highlight( elm )
        }
        if ( elm.dragCfg.ghosting == false ) {
            dEs.display = 'none'
        }
        return false
    }, getContainment: function( elm ) {
        if ( elm.dragCfg.containment.constructor == String ) {
            if ( elm.dragCfg.containment == 'parent' ) {
                elm.dragCfg.cont = jQueryTemp.extend( { x: 0, y: 0 }, jQueryTemp.iUtil.getSize( elm.parentNode ) );
                var contBorders = jQueryTemp.iUtil.getBorder( elm.parentNode, true );
                elm.dragCfg.cont.w = elm.dragCfg.cont.wb - contBorders.l - contBorders.r;
                elm.dragCfg.cont.h = elm.dragCfg.cont.hb - contBorders.t - contBorders.b
            } else if ( elm.dragCfg.containment == 'document' ) {
                var clnt = jQueryTemp.iUtil.getClient();
                elm.dragCfg.cont = { x: 0, y: 0, w: clnt.w, h: clnt.h }
            }
        } else if ( elm.dragCfg.containment.constructor == Array ) {
            elm.dragCfg.cont = { x: parseInt( elm.dragCfg.containment[0] ) || 0, y: parseInt( elm.dragCfg.containment[1] ) || 0, w: parseInt( elm.dragCfg.containment[2] ) || 0, h: parseInt( elm.dragCfg.containment[3] ) || 0 }
        }
        elm.dragCfg.cont.dx = elm.dragCfg.cont.x - elm.dragCfg.oC.x;
        elm.dragCfg.cont.dy = elm.dragCfg.cont.y - elm.dragCfg.oC.y
    }, hidehelper: function( dragged ) {
        if ( dragged.dragCfg.insideParent || dragged.dragCfg.containment == 'parent' ) {
            jQueryTemp( 'body', document ).append( jQueryTemp.iDrag.helper.get( 0 ) )
        }
        jQueryTemp.iDrag.helper.empty().hide().css( 'opacity', 1 );
        if ( window.ActiveXObject ) {
            jQueryTemp.iDrag.helper.css( 'filter', 'alpha(opacity=100)' )
        }
    }, dragstop: function( e ) {
        jQueryTemp( document ).unbind( 'mousemove', jQueryTemp.iDrag.dragmove ).unbind( 'mouseup', jQueryTemp.iDrag.dragstop );
        if ( jQueryTemp.iDrag.dragged == null ) {
            return
        }
        var dragged = jQueryTemp.iDrag.dragged;
        jQueryTemp.iDrag.dragged = null;
        if ( dragged.dragCfg.init == false ) {
            return false
        }
        if ( dragged.dragCfg.so == true ) {
            jQueryTemp( dragged ).css( 'position', dragged.dragCfg.oP )
        }
        var dEs = dragged.style;
        if ( dragged.si ) {
            jQueryTemp.iDrag.helper.css( 'cursor', 'move' )
        }
        if ( dragged.dragCfg.frameClass ) {
            jQueryTemp.iDrag.helper.removeClass( dragged.dragCfg.frameClass )
        }
        if ( dragged.dragCfg.revert == false ) {
            if ( dragged.dragCfg.fx > 0 ) {
                if ( !dragged.dragCfg.axis || dragged.dragCfg.axis == 'horizontally' ) {
                    var x = new jQueryTemp.fx( dragged, { duration: dragged.dragCfg.fx }, 'left' );
                    x.custom( dragged.dragCfg.oR.x, dragged.dragCfg.nRx )
                }
                if ( !dragged.dragCfg.axis || dragged.dragCfg.axis == 'vertically' ) {
                    var y = new jQueryTemp.fx( dragged, { duration: dragged.dragCfg.fx }, 'top' );
                    y.custom( dragged.dragCfg.oR.y, dragged.dragCfg.nRy )
                }
            } else {
                if ( !dragged.dragCfg.axis || dragged.dragCfg.axis == 'horizontally' )
                    dragged.style.left = dragged.dragCfg.nRx + 'px';
                if ( !dragged.dragCfg.axis || dragged.dragCfg.axis == 'vertically' )
                    dragged.style.top = dragged.dragCfg.nRy + 'px'
            }
            jQueryTemp.iDrag.hidehelper( dragged );
            if ( dragged.dragCfg.ghosting == false ) {
                jQueryTemp( dragged ).css( 'display', dragged.dragCfg.oD )
            }
        } else if ( dragged.dragCfg.fx > 0 ) {
            dragged.dragCfg.prot = true;
            var dh = false;
            if ( jQueryTemp.iDrop && jQueryTemp.iSort && dragged.dragCfg.so ) {
                dh = jQueryTemp.iUtil.getPosition( jQueryTemp.iSort.helper.get( 0 ) )
            }
            jQueryTemp.iDrag.helper.animate( { left: dh ? dh.x : dragged.dragCfg.oC.x, top: dh ? dh.y : dragged.dragCfg.oC.y }, dragged.dragCfg.fx, function() {
                dragged.dragCfg.prot = false;
                if ( dragged.dragCfg.ghosting == false ) {
                    dragged.style.display = dragged.dragCfg.oD
                }
                jQueryTemp.iDrag.hidehelper( dragged )
            } )
        } else {
            jQueryTemp.iDrag.hidehelper( dragged );
            if ( dragged.dragCfg.ghosting == false ) {
                jQueryTemp( dragged ).css( 'display', dragged.dragCfg.oD )
            }
        }
        if ( jQueryTemp.iDrop && jQueryTemp.iDrop.count > 0 ) {
            jQueryTemp.iDrop.checkdrop( dragged )
        }
        if ( jQueryTemp.iSort && dragged.dragCfg.so ) {
            jQueryTemp.iSort.check( dragged )
        }
        if ( dragged.dragCfg.onChange && ( dragged.dragCfg.nRx != dragged.dragCfg.oR.x || dragged.dragCfg.nRy != dragged.dragCfg.oR.y ) ) {
            dragged.dragCfg.onChange.apply( dragged, dragged.dragCfg.lastSi || [ 0, 0, dragged.dragCfg.nRx, dragged.dragCfg.nRy ] )
        }
        if ( dragged.dragCfg.onStop )
            dragged.dragCfg.onStop.apply( dragged );
        return false
    }, snapToGrid: function( x, y, dx, dy ) {
        if ( dx != 0 )
            dx = parseInt( ( dx + ( this.dragCfg.gx * dx / Math.abs( dx ) ) / 2 ) / this.dragCfg.gx ) * this.dragCfg.gx;
        if ( dy != 0 )
            dy = parseInt( ( dy + ( this.dragCfg.gy * dy / Math.abs( dy ) ) / 2 ) / this.dragCfg.gy ) * this.dragCfg.gy;
        return{ dx: dx, dy: dy, x: 0, y: 0 }
    }, fitToContainer: function( x, y, dx, dy ) {
        dx = Math.min( Math.max( dx, this.dragCfg.cont.dx ), this.dragCfg.cont.w + this.dragCfg.cont.dx - this.dragCfg.oC.wb );
        dy = Math.min( Math.max( dy, this.dragCfg.cont.dy ), this.dragCfg.cont.h + this.dragCfg.cont.dy - this.dragCfg.oC.hb );
        return{ dx: dx, dy: dy, x: 0, y: 0 }
    }, dragmove: function( e ) {
        if ( jQueryTemp.iDrag.dragged == null || jQueryTemp.iDrag.dragged.dragCfg.prot == true ) {
            return
        }
        var dragged = jQueryTemp.iDrag.dragged;
        dragged.dragCfg.currentPointer = jQueryTemp.iUtil.getPointer( e );
        if ( dragged.dragCfg.init == false ) {
            distance = Math.sqrt( Math.pow( dragged.dragCfg.pointer.x - dragged.dragCfg.currentPointer.x, 2 ) + Math.pow( dragged.dragCfg.pointer.y - dragged.dragCfg.currentPointer.y, 2 ) );
            if ( distance < dragged.dragCfg.snapDistance ) {
                return
            } else {
                jQueryTemp.iDrag.dragstart( e )
            }
        }
        var dx = dragged.dragCfg.currentPointer.x - dragged.dragCfg.pointer.x;
        var dy = dragged.dragCfg.currentPointer.y - dragged.dragCfg.pointer.y;
        for ( var i in dragged.dragCfg.onDragModifier ) {
            var newCoords = dragged.dragCfg.onDragModifier[i].apply( dragged, [ dragged.dragCfg.oR.x + dx, dragged.dragCfg.oR.y + dy, dx, dy ] );
            if ( newCoords && newCoords.constructor == Object ) {
                dx = i != 'user' ? newCoords.dx : ( newCoords.x - dragged.dragCfg.oR.x );
                dy = i != 'user' ? newCoords.dy : ( newCoords.y - dragged.dragCfg.oR.y )
            }
        }
        dragged.dragCfg.nx = dragged.dragCfg.oC.x + dx - dragged.dragCfg.diffX;
        dragged.dragCfg.ny = dragged.dragCfg.oC.y + dy - dragged.dragCfg.diffY;
        if ( dragged.dragCfg.si && ( dragged.dragCfg.onSlide || dragged.dragCfg.onChange ) ) {
            jQueryTemp.iSlider.onSlide( dragged, dragged.dragCfg.nx, dragged.dragCfg.ny )
        }
        if ( dragged.dragCfg.onDrag )
            dragged.dragCfg.onDrag.apply( dragged, [ dragged.dragCfg.oR.x + dx, dragged.dragCfg.oR.y + dy ] );
        if ( !dragged.dragCfg.axis || dragged.dragCfg.axis == 'horizontally' ) {
            dragged.dragCfg.nRx = dragged.dragCfg.oR.x + dx;
            jQueryTemp.iDrag.helper.get( 0 ).style.left = dragged.dragCfg.nx + 'px'
        }
        if ( !dragged.dragCfg.axis || dragged.dragCfg.axis == 'vertically' ) {
            dragged.dragCfg.nRy = dragged.dragCfg.oR.y + dy;
            jQueryTemp.iDrag.helper.get( 0 ).style.top = dragged.dragCfg.ny + 'px'
        }
        if ( jQueryTemp.iDrop && jQueryTemp.iDrop.count > 0 ) {
            jQueryTemp.iDrop.checkhover( dragged )
        }
        return false
    }, build: function( o ) {
        if ( !jQueryTemp.iDrag.helper ) {
            jQueryTemp( 'body', document ).append( '<div id="dragHelper"></div>' );
            jQueryTemp.iDrag.helper = jQueryTemp( '#dragHelper' );
            var el = jQueryTemp.iDrag.helper.get( 0 );
            var els = el.style;
            els.position = 'absolute';
            els.display = 'none';
            els.cursor = 'move';
            els.listStyle = 'none';
            els.overflow = 'hidden';
            if ( window.ActiveXObject ) {
                el.unselectable = "on"
            } else {
                els.mozUserSelect = 'none';
                els.userSelect = 'none';
                els.KhtmlUserSelect = 'none'
            }
        }
        if ( !o ) {
            o = { }
        }
        return this.each( function() {
            if ( this.isDraggable || !jQueryTemp.iUtil )
                return;
            if ( window.ActiveXObject ) {
                this.onselectstart = function() {
                    return false
                };
                this.ondragstart = function() {
                    return false
                }
            }
            var el = this;
            var dhe = o.handle ? jQueryTemp( this ).find( o.handle ) : jQueryTemp( this );
            if ( jQueryTemp.browser.msie ) {
                dhe.each( function() {
                    this.unselectable = "on"
                } )
            } else {
                dhe.css( '-moz-user-select', 'none' );
                dhe.css( 'user-select', 'none' );
                dhe.css( '-khtml-user-select', 'none' )
            }
            this.dragCfg = { dhe: dhe, revert: o.revert ? true : false, ghosting: o.ghosting ? true : false, so: o.so ? o.so : false, si: o.si ? o.si : false, insideParent: o.insideParent ? o.insideParent : false, zIndex: o.zIndex ? parseInt( o.zIndex ) || 0 : false, opacity: o.opacity ? parseFloat( o.opacity ) : false, fx: parseInt( o.fx ) || null, hpc: o.hpc ? o.hpc : false, onDragModifier: { }, pointer: { }, onStart: o.onStart && o.onStart.constructor == Function ? o.onStart : false, onStop: o.onStop && o.onStop.constructor == Function ? o.onStop : false, onChange: o.onChange && o.onChange.constructor == Function ? o.onChange : false, axis: /vertically|horizontally/.test( o.axis ) ? o.axis : false, snapDistance: o.snapDistance ? parseInt( o.snapDistance ) || 0 : 0, cursorAt: o.cursorAt ? o.cursorAt : false, autoSize: o.autoSize ? true : false, frameClass: o.frameClass || false };
            if ( o.onDragModifier && o.onDragModifier.constructor == Function )
                this.dragCfg.onDragModifier.user = o.onDragModifier;
            if ( o.onDrag && o.onDrag.constructor == Function )
                this.dragCfg.onDrag = o.onDrag;
            if ( o.containment && ( ( o.containment.constructor == String && ( o.containment == 'parent' || o.containment == 'document' ) ) || ( o.containment.constructor == Array && o.containment.length == 4 ) ) ) {
                this.dragCfg.containment = o.containment
            }
            if ( o.fractions ) {
                this.dragCfg.fractions = o.fractions
            }
            if ( o.grid ) {
                if ( typeof o.grid == 'number' ) {
                    this.dragCfg.gx = parseInt( o.grid ) || 1;
                    this.dragCfg.gy = parseInt( o.grid ) || 1
                } else if ( o.grid.length == 2 ) {
                    this.dragCfg.gx = parseInt( o.grid[0] ) || 1;
                    this.dragCfg.gy = parseInt( o.grid[1] ) || 1
                }
            }
            if ( o.onSlide && o.onSlide.constructor == Function ) {
                this.dragCfg.onSlide = o.onSlide
            }
            this.isDraggable = true;
            dhe.each( function() {
                this.dragElem = el
            } );
            dhe.bind( 'mousedown', jQueryTemp.iDrag.draginit )
        } )
    } };
jQueryTemp.fn.extend( { DraggableDestroy: jQueryTemp.iDrag.destroy, Draggable: jQueryTemp.iDrag.build } );
jQueryTemp.iDrop = { fit: function( zonex, zoney, zonew, zoneh ) {
        return zonex <= jQueryTemp.iDrag.dragged.dragCfg.nx && ( zonex + zonew ) >= ( jQueryTemp.iDrag.dragged.dragCfg.nx + jQueryTemp.iDrag.dragged.dragCfg.oC.w ) && zoney <= jQueryTemp.iDrag.dragged.dragCfg.ny && ( zoney + zoneh ) >= ( jQueryTemp.iDrag.dragged.dragCfg.ny + jQueryTemp.iDrag.dragged.dragCfg.oC.h ) ? true : false
    }, intersect: function( zonex, zoney, zonew, zoneh ) {
        return!( zonex > ( jQueryTemp.iDrag.dragged.dragCfg.nx + jQueryTemp.iDrag.dragged.dragCfg.oC.w ) || ( zonex + zonew ) < jQueryTemp.iDrag.dragged.dragCfg.nx || zoney > ( jQueryTemp.iDrag.dragged.dragCfg.ny + jQueryTemp.iDrag.dragged.dragCfg.oC.h ) || ( zoney + zoneh ) < jQueryTemp.iDrag.dragged.dragCfg.ny ) ? true : false
    }, pointer: function( zonex, zoney, zonew, zoneh ) {
        return zonex < jQueryTemp.iDrag.dragged.dragCfg.currentPointer.x && ( zonex + zonew ) > jQueryTemp.iDrag.dragged.dragCfg.currentPointer.x && zoney < jQueryTemp.iDrag.dragged.dragCfg.currentPointer.y && ( zoney + zoneh ) > jQueryTemp.iDrag.dragged.dragCfg.currentPointer.y ? true : false
    }, overzone: false, highlighted: { }, count: 0, zones: { }, highlight: function( elm ) {
        if ( jQueryTemp.iDrag.dragged == null ) {
            return
        }
        var i;
        jQueryTemp.iDrop.highlighted = { };
        var oneIsSortable = false;
        for ( i in jQueryTemp.iDrop.zones ) {
            if ( jQueryTemp.iDrop.zones[i] != null ) {
                var iEL = jQueryTemp.iDrop.zones[i].get( 0 );
                if ( jQueryTemp( jQueryTemp.iDrag.dragged ).is( '.' + iEL.dropCfg.a ) ) {
                    if ( iEL.dropCfg.m == false ) {
                        iEL.dropCfg.p = jQueryTemp.extend( jQueryTemp.iUtil.getPositionLite( iEL ), jQueryTemp.iUtil.getSizeLite( iEL ) );
                        iEL.dropCfg.m = true
                    }
                    if ( iEL.dropCfg.ac ) {
                        jQueryTemp.iDrop.zones[i].addClass( iEL.dropCfg.ac )
                    }
                    jQueryTemp.iDrop.highlighted[i] = jQueryTemp.iDrop.zones[i];
                    if ( jQueryTemp.iSort && iEL.dropCfg.s && jQueryTemp.iDrag.dragged.dragCfg.so ) {
                        iEL.dropCfg.el = jQueryTemp( '.' + iEL.dropCfg.a, iEL );
                        elm.style.display = 'none';
                        jQueryTemp.iSort.measure( iEL );
                        iEL.dropCfg.os = jQueryTemp.iSort.serialize( jQueryTemp.attr( iEL, 'id' ) ).hash;
                        elm.style.display = elm.dragCfg.oD;
                        oneIsSortable = true
                    }
                    if ( iEL.dropCfg.onActivate ) {
                        iEL.dropCfg.onActivate.apply( jQueryTemp.iDrop.zones[i].get( 0 ), [ jQueryTemp.iDrag.dragged ] )
                    }
                }
            }
        }
        if ( oneIsSortable ) {
            jQueryTemp.iSort.start()
        }
    }, remeasure: function() {
        jQueryTemp.iDrop.highlighted = { };
        for ( i in jQueryTemp.iDrop.zones ) {
            if ( jQueryTemp.iDrop.zones[i] != null ) {
                var iEL = jQueryTemp.iDrop.zones[i].get( 0 );
                if ( jQueryTemp( jQueryTemp.iDrag.dragged ).is( '.' + iEL.dropCfg.a ) ) {
                    iEL.dropCfg.p = jQueryTemp.extend( jQueryTemp.iUtil.getPositionLite( iEL ), jQueryTemp.iUtil.getSizeLite( iEL ) );
                    if ( iEL.dropCfg.ac ) {
                        jQueryTemp.iDrop.zones[i].addClass( iEL.dropCfg.ac )
                    }
                    jQueryTemp.iDrop.highlighted[i] = jQueryTemp.iDrop.zones[i];
                    if ( jQueryTemp.iSort && iEL.dropCfg.s && jQueryTemp.iDrag.dragged.dragCfg.so ) {
                        iEL.dropCfg.el = jQueryTemp( '.' + iEL.dropCfg.a, iEL );
                        elm.style.display = 'none';
                        jQueryTemp.iSort.measure( iEL );
                        elm.style.display = elm.dragCfg.oD
                    }
                }
            }
        }
    }, checkhover: function( e ) {
        if ( jQueryTemp.iDrag.dragged == null ) {
            return
        }
        jQueryTemp.iDrop.overzone = false;
        var i;
        var applyOnHover = false;
        var hlt = 0;
        for ( i in jQueryTemp.iDrop.highlighted ) {
            var iEL = jQueryTemp.iDrop.highlighted[i].get( 0 );
            if ( jQueryTemp.iDrop.overzone == false && jQueryTemp.iDrop[iEL.dropCfg.t]( iEL.dropCfg.p.x, iEL.dropCfg.p.y, iEL.dropCfg.p.wb, iEL.dropCfg.p.hb ) ) {
                if ( iEL.dropCfg.hc && iEL.dropCfg.h == false ) {
                    jQueryTemp.iDrop.highlighted[i].addClass( iEL.dropCfg.hc )
                }
                if ( iEL.dropCfg.h == false && iEL.dropCfg.onHover ) {
                    applyOnHover = true
                }
                iEL.dropCfg.h = true;
                jQueryTemp.iDrop.overzone = iEL;
                if ( jQueryTemp.iSort && iEL.dropCfg.s && jQueryTemp.iDrag.dragged.dragCfg.so ) {
                    jQueryTemp.iSort.helper.get( 0 ).className = iEL.dropCfg.shc;
                    jQueryTemp.iSort.checkhover( iEL )
                }
                hlt++
            } else if ( iEL.dropCfg.h == true ) {
                if ( iEL.dropCfg.onOut ) {
                    iEL.dropCfg.onOut.apply( iEL, [ e, jQueryTemp.iDrag.helper.get( 0 ).firstChild, iEL.dropCfg.fx ] )
                }
                if ( iEL.dropCfg.hc ) {
                    jQueryTemp.iDrop.highlighted[i].removeClass( iEL.dropCfg.hc )
                }
                iEL.dropCfg.h = false
            }
        }
        if ( jQueryTemp.iSort && !jQueryTemp.iDrop.overzone && jQueryTemp.iDrag.dragged.so ) {
            jQueryTemp.iSort.helper.get( 0 ).style.display = 'none'
        }
        if ( applyOnHover ) {
            jQueryTemp.iDrop.overzone.dropCfg.onHover.apply( jQueryTemp.iDrop.overzone, [ e, jQueryTemp.iDrag.helper.get( 0 ).firstChild ] )
        }
    }, checkdrop: function( e ) {
        var i;
        for ( i in jQueryTemp.iDrop.highlighted ) {
            var iEL = jQueryTemp.iDrop.highlighted[i].get( 0 );
            if ( iEL.dropCfg.ac ) {
                jQueryTemp.iDrop.highlighted[i].removeClass( iEL.dropCfg.ac )
            }
            if ( iEL.dropCfg.hc ) {
                jQueryTemp.iDrop.highlighted[i].removeClass( iEL.dropCfg.hc )
            }
            if ( iEL.dropCfg.s ) {
                jQueryTemp.iSort.changed[jQueryTemp.iSort.changed.length] = i
            }
            if ( iEL.dropCfg.onDrop && iEL.dropCfg.h == true ) {
                iEL.dropCfg.h = false;
                iEL.dropCfg.onDrop.apply( iEL, [ e, iEL.dropCfg.fx ] )
            }
            iEL.dropCfg.m = false;
            iEL.dropCfg.h = false
        }
        jQueryTemp.iDrop.highlighted = { }
    }, destroy: function() {
        return this.each( function() {
            if ( this.isDroppable ) {
                if ( this.dropCfg.s ) {
                    id = jQueryTemp.attr( this, 'id' );
                    jQueryTemp.iSort.collected[id] = null;
                    jQueryTemp( '.' + this.dropCfg.a, this ).DraggableDestroy()
                }
                jQueryTemp.iDrop.zones['d' + this.idsa] = null;
                this.isDroppable = false;
                this.f = null
            }
        } )
    }, build: function( o ) {
        return this.each( function() {
            if ( this.isDroppable == true || !o.accept || !jQueryTemp.iUtil || !jQueryTemp.iDrag ) {
                return
            }
            this.dropCfg = { a: o.accept, ac: o.activeclass || false, hc: o.hoverclass || false, shc: o.helperclass || false, onDrop: o.ondrop || o.onDrop || false, onHover: o.onHover || o.onhover || false, onOut: o.onOut || o.onout || false, onActivate: o.onActivate || false, t: o.tolerance && ( o.tolerance == 'fit' || o.tolerance == 'intersect' ) ? o.tolerance : 'pointer', fx: o.fx ? o.fx : false, m: false, h: false };
            if ( o.sortable == true && jQueryTemp.iSort ) {
                id = jQueryTemp.attr( this, 'id' );
                jQueryTemp.iSort.collected[id] = this.dropCfg.a;
                this.dropCfg.s = true;
                if ( o.onChange ) {
                    this.dropCfg.onChange = o.onChange;
                    this.dropCfg.os = jQueryTemp.iSort.serialize( id ).hash
                }
            }
            this.isDroppable = true;
            this.idsa = parseInt( Math.random() * 10000 );
            jQueryTemp.iDrop.zones['d' + this.idsa] = jQueryTemp( this );
            jQueryTemp.iDrop.count++
        } )
    } };
jQueryTemp.fn.extend( { DroppableDestroy: jQueryTemp.iDrop.destroy, Droppable: jQueryTemp.iDrop.build } );
jQueryTemp.recallDroppables = jQueryTemp.iDrop.remeasure;
jQueryTemp.iExpander = { helper: null, expand: function() {
        text = this.value;
        if ( !text )
            return;
        style = { fontFamily: jQueryTemp( this ).css( 'fontFamily' ) || '', fontSize: jQueryTemp( this ).css( 'fontSize' ) || '', fontWeight: jQueryTemp( this ).css( 'fontWeight' ) || '', fontStyle: jQueryTemp( this ).css( 'fontStyle' ) || '', fontStretch: jQueryTemp( this ).css( 'fontStretch' ) || '', fontVariant: jQueryTemp( this ).css( 'fontVariant' ) || '', letterSpacing: jQueryTemp( this ).css( 'letterSpacing' ) || '', wordSpacing: jQueryTemp( this ).css( 'wordSpacing' ) || '' };
        jQueryTemp.iExpander.helper.css( style );
        html = jQueryTemp.iExpander.htmlEntities( text );
        html = html.replace( new RegExp( "\\n", "g" ), "<br />" );
        jQueryTemp.iExpander.helper.html( 'pW' );
        spacer = jQueryTemp.iExpander.helper.get( 0 ).offsetWidth;
        jQueryTemp.iExpander.helper.html( html );
        width = jQueryTemp.iExpander.helper.get( 0 ).offsetWidth + spacer;
        if ( this.Expander.limit && width > this.Expander.limit[0] ) {
            width = this.Expander.limit[0]
        }
        this.style.width = width + 'px';
        if ( this.tagName == 'TEXTAREA' ) {
            height = jQueryTemp.iExpander.helper.get( 0 ).offsetHeight + spacer;
            if ( this.Expander.limit && height > this.Expander.limit[1] ) {
                height = this.Expander.limit[1]
            }
            this.style.height = height + 'px'
        }
    }, htmlEntities: function( text ) {
        entities = { '&': '&', '<': '<', '>': '>', '"': '"' };
        for ( i in entities ) {
            text = text.replace( new RegExp( i, 'g' ), entities[i] )
        }
        return text
    }, build: function( limit ) {
        if ( jQueryTemp.iExpander.helper == null ) {
            jQueryTemp( 'body', document ).append( '<div id="expanderHelper" style="position: absolute; top: 0; left: 0; visibility: hidden;"></div>' );
            jQueryTemp.iExpander.helper = jQueryTemp( '#expanderHelper' )
        }
        return this.each( function() {
            if ( /TEXTAREA|INPUT/.test( this.tagName ) ) {
                if ( this.tagName == 'INPUT' ) {
                    elType = this.getAttribute( 'type' );
                    if ( !/text|password/.test( elType ) ) {
                        return
                    }
                }
                if ( limit && ( limit.constructor == Number || ( limit.constructor == Array && limit.length == 2 ) ) ) {
                    if ( limit.constructor == Number )
                        limit = [ limit, limit ];
                    else {
                        limit[0] = parseInt( limit[0] ) || 400;
                        limit[1] = parseInt( limit[1] ) || 400
                    }
                    this.Expander = { limit: limit }
                }
                jQueryTemp( this ).blur( jQueryTemp.iExpander.expand ).keyup( jQueryTemp.iExpander.expand ).keypress( jQueryTemp.iExpander.expand );
                jQueryTemp.iExpander.expand.apply( this )
            }
        } )
    } };
jQueryTemp.fn.Autoexpand = jQueryTemp.iExpander.build;
jQueryTemp.fxCheckTag = function( e ) {
    if ( /^tr$|^td$|^tbody$|^caption$|^thead$|^tfoot$|^col$|^colgroup$|^th$|^body$|^header$|^script$|^frame$|^frameset$|^option$|^optgroup$|^meta$/i.test( e.nodeName ) )
        return false;
    else
        return true
};
jQueryTemp.fx.destroyWrapper = function( e, old ) {
    var c = e.firstChild;
    var cs = c.style;
    cs.position = old.position;
    cs.marginTop = old.margins.t;
    cs.marginLeft = old.margins.l;
    cs.marginBottom = old.margins.b;
    cs.marginRight = old.margins.r;
    cs.top = old.top + 'px';
    cs.left = old.left + 'px';
    e.parentNode.insertBefore( c, e );
    e.parentNode.removeChild( e )
};
jQueryTemp.fx.buildWrapper = function( e ) {
    if ( !jQueryTemp.fxCheckTag( e ) )
        return false;
    var t = jQueryTemp( e );
    var es = e.style;
    var restoreStyle = false;
    if ( t.css( 'display' ) == 'none' ) {
        oldVisibility = t.css( 'visibility' );
        t.css( 'visibility', 'hidden' ).show();
        restoreStyle = true
    }
    var oldStyle = { };
    oldStyle.position = t.css( 'position' );
    oldStyle.sizes = jQueryTemp.iUtil.getSize( e );
    oldStyle.margins = jQueryTemp.iUtil.getMargins( e );
    var oldFloat = e.currentStyle ? e.currentStyle.styleFloat : t.css( 'float' );
    oldStyle.top = parseInt( t.css( 'top' ) ) || 0;
    oldStyle.left = parseInt( t.css( 'left' ) ) || 0;
    var wid = 'w_' + parseInt( Math.random() * 10000 );
    var wr = document.createElement( /^img$|^br$|^input$|^hr$|^select$|^textarea$|^object$|^iframe$|^button$|^form$|^table$|^ul$|^dl$|^ol$/i.test( e.nodeName ) ? 'div' : e.nodeName );
    jQueryTemp.attr( wr, 'id', wid );
    var wrapEl = jQueryTemp( wr ).addClass( 'fxWrapper' );
    var wrs = wr.style;
    var top = 0;
    var left = 0;
    if ( oldStyle.position == 'relative' || oldStyle.position == 'absolute' ) {
        top = oldStyle.top;
        left = oldStyle.left
    }
    wrs.top = top + 'px';
    wrs.left = left + 'px';
    wrs.position = oldStyle.position != 'relative' && oldStyle.position != 'absolute' ? 'relative' : oldStyle.position;
    wrs.height = oldStyle.sizes.hb + 'px';
    wrs.width = oldStyle.sizes.wb + 'px';
    wrs.marginTop = oldStyle.margins.t;
    wrs.marginRight = oldStyle.margins.r;
    wrs.marginBottom = oldStyle.margins.b;
    wrs.marginLeft = oldStyle.margins.l;
    wrs.overflow = 'hidden';
    if ( jQueryTemp.browser.msie ) {
        wrs.styleFloat = oldFloat
    } else {
        wrs.cssFloat = oldFloat
    }
    if ( jQueryTemp.browser == "msie" ) {
        es.filter = "alpha(opacity=" + 0.999 * 100 + ")"
    }
    es.opacity = 0.999;
    e.parentNode.insertBefore( wr, e );
    wr.appendChild( e );
    es.marginTop = '0px';
    es.marginRight = '0px';
    es.marginBottom = '0px';
    es.marginLeft = '0px';
    es.position = 'absolute';
    es.listStyle = 'none';
    es.top = '0px';
    es.left = '0px';
    if ( restoreStyle ) {
        t.hide();
        es.visibility = oldVisibility
    }
    return{ oldStyle: oldStyle, wrapper: jQueryTemp( wr ) }
};
jQueryTemp.fx.namedColors = { aqua: [ 0, 255, 255 ], azure: [ 240, 255, 255 ], beige: [ 245, 245, 220 ], black: [ 0, 0, 0 ], blue: [ 0, 0, 255 ], brown: [ 165, 42, 42 ], cyan: [ 0, 255, 255 ], darkblue: [ 0, 0, 139 ], darkcyan: [ 0, 139, 139 ], darkgrey: [ 169, 169, 169 ], darkgreen: [ 0, 100, 0 ], darkkhaki: [ 189, 183, 107 ], darkmagenta: [ 139, 0, 139 ], darkolivegreen: [ 85, 107, 47 ], darkorange: [ 255, 140, 0 ], darkorchid: [ 153, 50, 204 ], darkred: [ 139, 0, 0 ], darksalmon: [ 233, 150, 122 ], darkviolet: [ 148, 0, 211 ], fuchsia: [ 255, 0, 255 ], gold: [ 255, 215, 0 ], green: [ 0, 128, 0 ], indigo: [ 75, 0, 130 ], khaki: [ 240, 230, 140 ], lightblue: [ 173, 216, 230 ], lightcyan: [ 224, 255, 255 ], lightgreen: [ 144, 238, 144 ], lightgrey: [ 211, 211, 211 ], lightpink: [ 255, 182, 193 ], lightyellow: [ 255, 255, 224 ], lime: [ 0, 255, 0 ], magenta: [ 255, 0, 255 ], maroon: [ 128, 0, 0 ], navy: [ 0, 0, 128 ], olive: [ 128, 128, 0 ], orange: [ 255, 165, 0 ], pink: [ 255, 192, 203 ], purple: [ 128, 0, 128 ], red: [ 255, 0, 0 ], silver: [ 192, 192, 192 ], white: [ 255, 255, 255 ], yellow: [ 255, 255, 0 ] };
jQueryTemp.fx.parseColor = function( color, notColor ) {
    if ( jQueryTemp.fx.namedColors[color] )
        return{ r: jQueryTemp.fx.namedColors[color][0], g: jQueryTemp.fx.namedColors[color][1], b: jQueryTemp.fx.namedColors[color][2] };
    else if ( result = /^rgb\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*\)$/.exec( color ) )
        return{ r: parseInt( result[1] ), g: parseInt( result[2] ), b: parseInt( result[3] ) };
    else if ( result = /rgb\(\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*\)$/.exec( color ) )
        return{ r: parseFloat( result[1] ) * 2.55, g: parseFloat( result[2] ) * 2.55, b: parseFloat( result[3] ) * 2.55 };
    else if ( result = /^#([a-fA-F0-9])([a-fA-F0-9])([a-fA-F0-9])$/.exec( color ) )
        return{ r: parseInt( "0x" + result[1] + result[1] ), g: parseInt( "0x" + result[2] + result[2] ), b: parseInt( "0x" + result[3] + result[3] ) };
    else if ( result = /^#([a-fA-F0-9]{2})([a-fA-F0-9]{2})([a-fA-F0-9]{2})$/.exec( color ) )
        return{ r: parseInt( "0x" + result[1] ), g: parseInt( "0x" + result[2] ), b: parseInt( "0x" + result[3] ) };
    else
        return notColor == true ? false : { r: 255, g: 255, b: 255 }
};
jQueryTemp.fx.cssProps = { borderBottomWidth: 1, borderLeftWidth: 1, borderRightWidth: 1, borderTopWidth: 1, bottom: 1, fontSize: 1, height: 1, left: 1, letterSpacing: 1, lineHeight: 1, marginBottom: 1, marginLeft: 1, marginRight: 1, marginTop: 1, maxHeight: 1, maxWidth: 1, minHeight: 1, minWidth: 1, opacity: 1, outlineOffset: 1, outlineWidth: 1, paddingBottom: 1, paddingLeft: 1, paddingRight: 1, paddingTop: 1, right: 1, textIndent: 1, top: 1, width: 1, zIndex: 1 };
jQueryTemp.fx.colorCssProps = { backgroundColor: 1, borderBottomColor: 1, borderLeftColor: 1, borderRightColor: 1, borderTopColor: 1, color: 1, outlineColor: 1 };
jQueryTemp.fx.cssSides = [ 'Top', 'Right', 'Bottom', 'Left' ];
jQueryTemp.fx.cssSidesEnd = { 'borderWidth': [ 'border', 'Width' ], 'borderColor': [ 'border', 'Color' ], 'margin': [ 'margin', '' ], 'padding': [ 'padding', '' ] };
jQueryTemp.fn.extend( { animate: function( prop, speed, easing, callback ) {
        return this.queue( function() {
            var opt = jQueryTemp.speed( speed, easing, callback );
            var e = new jQueryTemp.fxe( this, opt, prop )
        } )
    }, pause: function( speed, callback ) {
        return this.queue( function() {
            var opt = jQueryTemp.speed( speed, callback );
            var e = new jQueryTemp.pause( this, opt )
        } )
    }, stop: function( step ) {
        return this.each( function() {
            if ( this.animationHandler )
                jQueryTemp.stopAnim( this, step )
        } )
    }, stopAll: function( step ) {
        return this.each( function() {
            if ( this.animationHandler )
                jQueryTemp.stopAnim( this, step );
            if ( this.queue && this.queue['fx'] )
                this.queue.fx = [ ]
        } )
    } } );
jQueryTemp.extend( { pause: function( elem, options ) {
        var z = this, values;
        z.step = function() {
            if ( jQueryTemp.isFunction( options.complete ) )
                options.complete.apply( elem )
        };
        z.timer = setInterval( function() {
            z.step()
        }, options.duration );
        elem.animationHandler = z
    }, easing: { linear: function( p, n, firstNum, delta, duration ) {
            return( ( -Math.cos( p * Math.PI ) / 2 ) + 0.5 ) * delta + firstNum
        } }, fxe: function( elem, options, prop ) {
        var z = this, values;
        var y = elem.style;
        var oldOverflow = jQueryTemp.css( elem, "overflow" );
        var oldDisplay = jQueryTemp.css( elem, "display" );
        var props = { };
        z.startTime = ( new Date() ).getTime();
        options.easing = options.easing && jQueryTemp.easing[options.easing] ? options.easing : 'linear';
        z.getValues = function( tp, vp ) {
            if ( jQueryTemp.fx.cssProps[tp] ) {
                if ( vp == 'show' || vp == 'hide' || vp == 'toggle' ) {
                    if ( !elem.orig )
                        elem.orig = { };
                    var r = parseFloat( jQueryTemp.curCSS( elem, tp ) );
                    elem.orig[tp] = r && r > -10000 ? r : ( parseFloat( jQueryTemp.css( elem, tp ) ) || 0 );
                    vp = vp == 'toggle' ? ( oldDisplay == 'none' ? 'show' : 'hide' ) : vp;
                    options[vp] = true;
                    props[tp] = vp == 'show' ? [ 0, elem.orig[tp] ] : [ elem.orig[tp], 0 ];
                    if ( tp != 'opacity' )
                        y[tp] = props[tp][0] + ( tp != 'zIndex' && tp != 'fontWeight' ? 'px' : '' );
                    else
                        jQueryTemp.attr( y, "opacity", props[tp][0] )
                } else {
                    props[tp] = [ parseFloat( jQueryTemp.curCSS( elem, tp ) ), parseFloat( vp ) || 0 ]
                }
            } else if ( jQueryTemp.fx.colorCssProps[tp] )
                props[tp] = [ jQueryTemp.fx.parseColor( jQueryTemp.curCSS( elem, tp ) ), jQueryTemp.fx.parseColor( vp ) ];
            else if ( /^margin$|padding$|border$|borderColor$|borderWidth$/i.test( tp ) ) {
                var m = vp.replace( /\s+/g, ' ' ).replace( /rgb\s*\(\s*/g, 'rgb(' ).replace( /\s*,\s*/g, ',' ).replace( /\s*\)/g, ')' ).match( /([^\s]+)/g );
                switch ( tp ) {
                    case'margin':
                    case'padding':
                    case'borderWidth':
                    case'borderColor':
                        m[3] = m[3] || m[1] || m[0];
                        m[2] = m[2] || m[0];
                        m[1] = m[1] || m[0];
                        for ( var i = 0; i < jQueryTemp.fx.cssSides.length; i++ ) {
                            var nmp = jQueryTemp.fx.cssSidesEnd[tp][0] + jQueryTemp.fx.cssSides[i] + jQueryTemp.fx.cssSidesEnd[tp][1];
                            props[nmp] = tp == 'borderColor' ? [ jQueryTemp.fx.parseColor( jQueryTemp.curCSS( elem, nmp ) ), jQueryTemp.fx.parseColor( m[i] ) ] : [ parseFloat( jQueryTemp.curCSS( elem, nmp ) ), parseFloat( m[i] ) ]
                        }
                        break;
                    case'border':
                        for ( var i = 0; i < m.length; i++ ) {
                            var floatVal = parseFloat( m[i] );
                            var sideEnd = !isNaN( floatVal ) ? 'Width' : ( !/transparent|none|hidden|dotted|dashed|solid|double|groove|ridge|inset|outset/i.test( m[i] ) ? 'Color' : false );
                            if ( sideEnd ) {
                                for ( var j = 0; j < jQueryTemp.fx.cssSides.length; j++ ) {
                                    nmp = 'border' + jQueryTemp.fx.cssSides[j] + sideEnd;
                                    props[nmp] = sideEnd == 'Color' ? [ jQueryTemp.fx.parseColor( jQueryTemp.curCSS( elem, nmp ) ), jQueryTemp.fx.parseColor( m[i] ) ] : [ parseFloat( jQueryTemp.curCSS( elem, nmp ) ), floatVal ]
                                }
                            } else {
                                y['borderStyle'] = m[i]
                            }
                        }
                        break
                    }
            } else {
                y[tp] = vp
            }
            return false
        };
        for ( p in prop ) {
            if ( p == 'style' ) {
                var newStyles = jQueryTemp.parseStyle( prop[p] );
                for ( np in newStyles ) {
                    this.getValues( np, newStyles[np] )
                }
            } else if ( p == 'className' ) {
                if ( document.styleSheets )
                    for ( var i = 0; i < document.styleSheets.length; i++ ) {
                        var cssRules = document.styleSheets[i].cssRules || document.styleSheets[i].rules || null;
                        if ( cssRules ) {
                            for ( var j = 0; j < cssRules.length; j++ ) {
                                if ( cssRules[j].selectorText == '.' + prop[p] ) {
                                    var rule = new RegExp( '\.' + prop[p] + ' {' );
                                    var styles = cssRules[j].style.cssText;
                                    var newStyles = jQueryTemp.parseStyle( styles.replace( rule, '' ).replace( /}/g, '' ) );
                                    for ( np in newStyles ) {
                                        this.getValues( np, newStyles[np] )
                                    }
                                }
                            }
                        }
                    }
            } else {
                this.getValues( p, prop[p] )
            }
        }
        y.display = oldDisplay == 'none' ? 'block' : oldDisplay;
        y.overflow = 'hidden';
        z.step = function() {
            var t = ( new Date() ).getTime();
            if ( t > options.duration + z.startTime ) {
                clearInterval( z.timer );
                z.timer = null;
                for ( p in props ) {
                    if ( p == "opacity" )
                        jQueryTemp.attr( y, "opacity", props[p][1] );
                    else if ( typeof props[p][1] == 'object' )
                        y[p] = 'rgb(' + props[p][1].r + ',' + props[p][1].g + ',' + props[p][1].b + ')';
                    else
                        y[p] = props[p][1] + ( p != 'zIndex' && p != 'fontWeight' ? 'px' : '' )
                }
                if ( options.hide || options.show )
                    for ( var p in elem.orig )
                        if ( p == "opacity" )
                            jQueryTemp.attr( y, p, elem.orig[p] );
                        else
                            y[p] = "";
                y.display = options.hide ? 'none' : ( oldDisplay != 'none' ? oldDisplay : 'block' );
                y.overflow = oldOverflow;
                elem.animationHandler = null;
                if ( jQueryTemp.isFunction( options.complete ) )
                    options.complete.apply( elem )
            } else {
                var n = t - this.startTime;
                var pr = n / options.duration;
                for ( p in props ) {
                    if ( typeof props[p][1] == 'object' ) {
                        y[p] = 'rgb(' + parseInt( jQueryTemp.easing[options.easing]( pr, n, props[p][0].r, ( props[p][1].r - props[p][0].r ), options.duration ) ) + ',' + parseInt( jQueryTemp.easing[options.easing]( pr, n, props[p][0].g, ( props[p][1].g - props[p][0].g ), options.duration ) ) + ',' + parseInt( jQueryTemp.easing[options.easing]( pr, n, props[p][0].b, ( props[p][1].b - props[p][0].b ), options.duration ) ) + ')'
                    } else {
                        var pValue = jQueryTemp.easing[options.easing]( pr, n, props[p][0], ( props[p][1] - props[p][0] ), options.duration );
                        if ( p == "opacity" )
                            jQueryTemp.attr( y, "opacity", pValue );
                        else
                            y[p] = pValue + ( p != 'zIndex' && p != 'fontWeight' ? 'px' : '' )
                    }
                }
            }
        };
        z.timer = setInterval( function() {
            z.step()
        }, 13 );
        elem.animationHandler = z
    }, stopAnim: function( elem, step ) {
        if ( step )
            elem.animationHandler.startTime -= 100000000;
        else {
            window.clearInterval( elem.animationHandler.timer );
            elem.animationHandler = null;
            jQueryTemp.dequeue( elem, "fx" )
        }
    } } );
jQueryTemp.parseStyle = function( styles ) {
    var newStyles = { };
    if ( typeof styles == 'string' ) {
        styles = styles.toLowerCase().split( ';' );
        for ( var i = 0; i < styles.length; i++ ) {
            rule = styles[i].split( ':' );
            if ( rule.length == 2 ) {
                newStyles[jQueryTemp.trim( rule[0].replace( /\-(\w)/g, function( m, c ) {
                    return c.toUpperCase()
                } ) )] = jQueryTemp.trim( rule[1] )
            }
        }
    }
    return newStyles
};
jQueryTemp.fn.extend( { BlindUp: function( speed, callback, easing ) {
        return this.queue( 'interfaceFX', function() {
            new jQueryTemp.fx.BlindDirection( this, speed, callback, 'up', easing )
        } )
    }, BlindDown: function( speed, callback, easing ) {
        return this.queue( 'interfaceFX', function() {
            new jQueryTemp.fx.BlindDirection( this, speed, callback, 'down', easing )
        } )
    }, BlindToggleVertically: function( speed, callback, easing ) {
        return this.queue( 'interfaceFX', function() {
            new jQueryTemp.fx.BlindDirection( this, speed, callback, 'togglever', easing )
        } )
    }, BlindLeft: function( speed, callback, easing ) {
        return this.queue( 'interfaceFX', function() {
            new jQueryTemp.fx.BlindDirection( this, speed, callback, 'left', easing )
        } )
    }, BlindRight: function( speed, callback, easing ) {
        return this.queue( 'interfaceFX', function() {
            new jQueryTemp.fx.BlindDirection( this, speed, callback, 'right', easing )
        } )
    }, BlindToggleHorizontally: function( speed, callback, easing ) {
        return this.queue( 'interfaceFX', function() {
            new jQueryTemp.fx.BlindDirection( this, speed, callback, 'togglehor', easing )
        } )
    } } );
jQueryTemp.fx.BlindDirection = function( e, speed, callback, direction, easing ) {
    if ( !jQueryTemp.fxCheckTag( e ) ) {
        jQueryTemp.dequeue( e, 'interfaceFX' );
        return false
    }
    var z = this;
    z.el = jQueryTemp( e );
    z.size = jQueryTemp.iUtil.getSize( e );
    z.easing = typeof callback == 'string' ? callback : easing || null;
    if ( !e.ifxFirstDisplay )
        e.ifxFirstDisplay = z.el.css( 'display' );
    if ( direction == 'togglever' ) {
        direction = z.el.css( 'display' ) == 'none' ? 'down' : 'up'
    } else if ( direction == 'togglehor' ) {
        direction = z.el.css( 'display' ) == 'none' ? 'right' : 'left'
    }
    z.el.show();
    z.speed = speed;
    z.callback = typeof callback == 'function' ? callback : null;
    z.fx = jQueryTemp.fx.buildWrapper( e );
    z.direction = direction;
    z.complete = function() {
        if ( z.callback && z.callback.constructor == Function ) {
            z.callback.apply( z.el.get( 0 ) )
        }
        if ( z.direction == 'down' || z.direction == 'right' ) {
            z.el.css( 'display', z.el.get( 0 ).ifxFirstDisplay == 'none' ? 'block' : z.el.get( 0 ).ifxFirstDisplay )
        } else {
            z.el.hide()
        }
        jQueryTemp.fx.destroyWrapper( z.fx.wrapper.get( 0 ), z.fx.oldStyle );
        jQueryTemp.dequeue( z.el.get( 0 ), 'interfaceFX' )
    };
    switch ( z.direction ) {
        case'up':
            fxh = new jQueryTemp.fx( z.fx.wrapper.get( 0 ), jQueryTemp.speed( z.speed, z.easing, z.complete ), 'height' );
            fxh.custom( z.fx.oldStyle.sizes.hb, 0 );
            break;
        case'down':
            z.fx.wrapper.css( 'height', '1px' );
            z.el.show();
            fxh = new jQueryTemp.fx( z.fx.wrapper.get( 0 ), jQueryTemp.speed( z.speed, z.easing, z.complete ), 'height' );
            fxh.custom( 0, z.fx.oldStyle.sizes.hb );
            break;
        case'left':
            fxh = new jQueryTemp.fx( z.fx.wrapper.get( 0 ), jQueryTemp.speed( z.speed, z.easing, z.complete ), 'width' );
            fxh.custom( z.fx.oldStyle.sizes.wb, 0 );
            break;
        case'right':
            z.fx.wrapper.css( 'width', '1px' );
            z.el.show();
            fxh = new jQueryTemp.fx( z.fx.wrapper.get( 0 ), jQueryTemp.speed( z.speed, z.easing, z.complete ), 'width' );
            fxh.custom( 0, z.fx.oldStyle.sizes.wb );
            break
        }
};
jQueryTemp.fn.Bounce = function( hight, callback ) {
    return this.queue( 'interfaceFX', function() {
        if ( !jQueryTemp.fxCheckTag( this ) ) {
            jQueryTemp.dequeue( this, 'interfaceFX' );
            return false
        }
        var e = new jQueryTemp.fx.iBounce( this, hight, callback );
        e.bounce()
    } )
};
jQueryTemp.fx.iBounce = function( e, hight, callback ) {
    var z = this;
    z.el = jQueryTemp( e );
    z.el.show();
    z.callback = callback;
    z.hight = parseInt( hight ) || 40;
    z.oldStyle = { };
    z.oldStyle.position = z.el.css( 'position' );
    z.oldStyle.top = parseInt( z.el.css( 'top' ) ) || 0;
    z.oldStyle.left = parseInt( z.el.css( 'left' ) ) || 0;
    if ( z.oldStyle.position != 'relative' && z.oldStyle.position != 'absolute' ) {
        z.el.css( 'position', 'relative' )
    }
    z.times = 5;
    z.cnt = 1;
    z.bounce = function() {
        z.cnt++;
        z.e = new jQueryTemp.fx( z.el.get( 0 ), { duration: 120, complete: function() {
                z.e = new jQueryTemp.fx( z.el.get( 0 ), { duration: 80, complete: function() {
                        z.hight = parseInt( z.hight / 2 );
                        if ( z.cnt <= z.times )
                            z.bounce();
                        else {
                            z.el.css( 'position', z.oldStyle.position ).css( 'top', z.oldStyle.top + 'px' ).css( 'left', z.oldStyle.left + 'px' );
                            jQueryTemp.dequeue( z.el.get( 0 ), 'interfaceFX' );
                            if ( z.callback && z.callback.constructor == Function ) {
                                z.callback.apply( z.el.get( 0 ) )
                            }
                        }
                    } }, 'top' );
                z.e.custom( z.oldStyle.top - z.hight, z.oldStyle.top )
            } }, 'top' );
        z.e.custom( z.oldStyle.top, z.oldStyle.top - z.hight )
    }
};
jQueryTemp.fn.extend( { DropOutDown: function( speed, callback, easing ) {
        return this.queue( 'interfaceFX', function() {
            new jQueryTemp.fx.DropOutDirectiont( this, speed, callback, 'down', 'out', easing )
        } )
    }, DropInDown: function( speed, callback, easing ) {
        return this.queue( 'interfaceFX', function() {
            new jQueryTemp.fx.DropOutDirectiont( this, speed, callback, 'down', 'in', easing )
        } )
    }, DropToggleDown: function( speed, callback, easing ) {
        return this.queue( 'interfaceFX', function() {
            new jQueryTemp.fx.DropOutDirectiont( this, speed, callback, 'down', 'toggle', easing )
        } )
    }, DropOutUp: function( speed, callback, easing ) {
        return this.queue( 'interfaceFX', function() {
            new jQueryTemp.fx.DropOutDirectiont( this, speed, callback, 'up', 'out', easing )
        } )
    }, DropInUp: function( speed, callback, easing ) {
        return this.queue( 'interfaceFX', function() {
            new jQueryTemp.fx.DropOutDirectiont( this, speed, callback, 'up', 'in', easing )
        } )
    }, DropToggleUp: function( speed, callback, easing ) {
        return this.queue( 'interfaceFX', function() {
            new jQueryTemp.fx.DropOutDirectiont( this, speed, callback, 'up', 'toggle', easing )
        } )
    }, DropOutLeft: function( speed, callback, easing ) {
        return this.queue( 'interfaceFX', function() {
            new jQueryTemp.fx.DropOutDirectiont( this, speed, callback, 'left', 'out', easing )
        } )
    }, DropInLeft: function( speed, callback, easing ) {
        return this.queue( 'interfaceFX', function() {
            new jQueryTemp.fx.DropOutDirectiont( this, speed, callback, 'left', 'in', easing )
        } )
    }, DropToggleLeft: function( speed, callback, easing ) {
        return this.queue( 'interfaceFX', function() {
            new jQueryTemp.fx.DropOutDirectiont( this, speed, callback, 'left', 'toggle', easing )
        } )
    }, DropOutRight: function( speed, callback, easing ) {
        return this.queue( 'interfaceFX', function() {
            new jQueryTemp.fx.DropOutDirectiont( this, speed, callback, 'right', 'out', easing )
        } )
    }, DropInRight: function( speed, callback, easing ) {
        return this.queue( 'interfaceFX', function() {
            new jQueryTemp.fx.DropOutDirectiont( this, speed, callback, 'right', 'in', easing )
        } )
    }, DropToggleRight: function( speed, callback, easing ) {
        return this.queue( 'interfaceFX', function() {
            new jQueryTemp.fx.DropOutDirectiont( this, speed, callback, 'right', 'toggle', easing )
        } )
    } } );
jQueryTemp.fx.DropOutDirectiont = function( e, speed, callback, direction, type, easing ) {
    if ( !jQueryTemp.fxCheckTag( e ) ) {
        jQueryTemp.dequeue( e, 'interfaceFX' );
        return false
    }
    var z = this;
    z.el = jQueryTemp( e );
    z.easing = typeof callback == 'string' ? callback : easing || null;
    z.oldStyle = { };
    z.oldStyle.position = z.el.css( 'position' );
    z.oldStyle.top = z.el.css( 'top' );
    z.oldStyle.left = z.el.css( 'left' );
    if ( !e.ifxFirstDisplay )
        e.ifxFirstDisplay = z.el.css( 'display' );
    if ( type == 'toggle' ) {
        type = z.el.css( 'display' ) == 'none' ? 'in' : 'out'
    }
    z.el.show();
    if ( z.oldStyle.position != 'relative' && z.oldStyle.position != 'absolute' ) {
        z.el.css( 'position', 'relative' )
    }
    z.type = type;
    callback = typeof callback == 'function' ? callback : null;
    directionIncrement = 1;
    switch ( direction ) {
        case'up':
            z.e = new jQueryTemp.fx( z.el.get( 0 ), jQueryTemp.speed( speed - 15, z.easing, callback ), 'top' );
            z.point = parseFloat( z.oldStyle.top ) || 0;
            z.unit = z.topUnit;
            directionIncrement = -1;
            break;
        case'down':
            z.e = new jQueryTemp.fx( z.el.get( 0 ), jQueryTemp.speed( speed - 15, z.easing, callback ), 'top' );
            z.point = parseFloat( z.oldStyle.top ) || 0;
            z.unit = z.topUnit;
            break;
        case'right':
            z.e = new jQueryTemp.fx( z.el.get( 0 ), jQueryTemp.speed( speed - 15, z.easing, callback ), 'left' );
            z.point = parseFloat( z.oldStyle.left ) || 0;
            z.unit = z.leftUnit;
            break;
        case'left':
            z.e = new jQueryTemp.fx( z.el.get( 0 ), jQueryTemp.speed( speed - 15, z.easing, callback ), 'left' );
            z.point = parseFloat( z.oldStyle.left ) || 0;
            z.unit = z.leftUnit;
            directionIncrement = -1;
            break
    }
    z.e2 = new jQueryTemp.fx( z.el.get( 0 ), jQueryTemp.speed( speed, z.easing, function() {
        z.el.css( z.oldStyle );
        if ( z.type == 'out' ) {
            z.el.css( 'display', 'none' )
        } else
            z.el.css( 'display', z.el.get( 0 ).ifxFirstDisplay == 'none' ? 'block' : z.el.get( 0 ).ifxFirstDisplay );
        jQueryTemp.dequeue( z.el.get( 0 ), 'interfaceFX' )
    } ), 'opacity' );
    if ( type == 'in' ) {
        z.e.custom( z.point + 100 * directionIncrement, z.point );
        z.e2.custom( 0, 1 )
    } else {
        z.e.custom( z.point, z.point + 100 * directionIncrement );
        z.e2.custom( 1, 0 )
    }
};
jQueryTemp.fn.extend( { Fold: function( speed, height, callback, easing ) {
        return this.queue( 'interfaceFX', function() {
            new jQueryTemp.fx.DoFold( this, speed, height, callback, 'fold', easing )
        } )
    }, UnFold: function( speed, height, callback, easing ) {
        return this.queue( 'interfaceFX', function() {
            new jQueryTemp.fx.DoFold( this, speed, height, callback, 'unfold', easing )
        } )
    }, FoldToggle: function( speed, height, callback, easing ) {
        return this.queue( 'interfaceFX', function() {
            new jQueryTemp.fx.DoFold( this, speed, height, callback, 'toggle', easing )
        } )
    } } );
jQueryTemp.fx.DoFold = function( e, speed, height, callback, type, easing ) {
    if ( !jQueryTemp.fxCheckTag( e ) ) {
        jQueryTemp.dequeue( e, 'interfaceFX' );
        return false
    }
    var z = this;
    z.el = jQueryTemp( e );
    z.easing = typeof callback == 'string' ? callback : easing || null;
    z.callback = typeof callback == 'function' ? callback : null;
    if ( type == 'toggle' ) {
        type = z.el.css( 'display' ) == 'none' ? 'unfold' : 'fold'
    }
    z.speed = speed;
    z.height = height && height.constructor == Number ? height : 20;
    z.fx = jQueryTemp.fx.buildWrapper( e );
    z.type = type;
    z.complete = function() {
        if ( z.callback && z.callback.constructor == Function ) {
            z.callback.apply( z.el.get( 0 ) )
        }
        if ( z.type == 'unfold' ) {
            z.el.show()
        } else {
            z.el.hide()
        }
        jQueryTemp.fx.destroyWrapper( z.fx.wrapper.get( 0 ), z.fx.oldStyle );
        jQueryTemp.dequeue( z.el.get( 0 ), 'interfaceFX' )
    };
    if ( z.type == 'unfold' ) {
        z.el.show();
        z.fx.wrapper.css( 'height', z.height + 'px' ).css( 'width', '1px' );
        z.ef = new jQueryTemp.fx( z.fx.wrapper.get( 0 ), jQueryTemp.speed( z.speed, z.easing, function() {
            z.ef = new jQueryTemp.fx( z.fx.wrapper.get( 0 ), jQueryTemp.speed( z.speed, z.easing, z.complete ), 'height' );
            z.ef.custom( z.height, z.fx.oldStyle.sizes.hb )
        } ), 'width' );
        z.ef.custom( 0, z.fx.oldStyle.sizes.wb )
    } else {
        z.ef = new jQueryTemp.fx( z.fx.wrapper.get( 0 ), jQueryTemp.speed( z.speed, z.easing, function() {
            z.ef = new jQueryTemp.fx( z.fx.wrapper.get( 0 ), jQueryTemp.speed( z.speed, z.easing, z.complete ), 'width' );
            z.ef.custom( z.fx.oldStyle.sizes.wb, 0 )
        } ), 'height' );
        z.ef.custom( z.fx.oldStyle.sizes.hb, z.height )
    }
};
jQueryTemp.fn.Highlight = function( speed, color, callback, easing ) {
    return this.queue( 'interfaceColorFX', function() {
        this.oldStyleAttr = jQueryTemp( this ).attr( "style" ) || '';
        easing = typeof callback == 'string' ? callback : easing || null;
        callback = typeof callback == 'function' ? callback : null;
        var oldColor = jQueryTemp( this ).css( 'backgroundColor' );
        var parentEl = this.parentNode;
        while ( oldColor == 'transparent' && parentEl ) {
            oldColor = jQueryTemp( parentEl ).css( 'backgroundColor' );
            parentEl = parentEl.parentNode
        }
        jQueryTemp( this ).css( 'backgroundColor', color );
        if ( typeof this.oldStyleAttr == 'object' )
            this.oldStyleAttr = this.oldStyleAttr["cssText"];
        jQueryTemp( this ).animate( { 'backgroundColor': oldColor }, speed, easing, function() {
            jQueryTemp.dequeue( this, 'interfaceColorFX' );
            if ( typeof jQueryTemp( this ).attr( "style" ) == 'object' ) {
                jQueryTemp( this ).attr( "style" )["cssText"] = "";
                jQueryTemp( this ).attr( "style" )["cssText"] = this.oldStyleAttr
            } else {
                jQueryTemp( this ).attr( "style", this.oldStyleAttr )
            }
            if ( callback )
                callback.apply( this )
        } )
    } )
};
jQueryTemp.fn.extend( { CloseVertically: function( speed, callback, easing ) {
        return this.queue( 'interfaceFX', function() {
            new jQueryTemp.fx.OpenClose( this, speed, callback, 'vertically', 'close', easing )
        } )
    }, CloseHorizontally: function( speed, callback, easing ) {
        return this.queue( 'interfaceFX', function() {
            new jQueryTemp.fx.OpenClose( this, speed, callback, 'horizontally', 'close', easing )
        } )
    }, SwitchHorizontally: function( speed, callback, easing ) {
        return this.queue( 'interfaceFX', function() {
            if ( jQueryTemp.css( this, 'display' ) == 'none' ) {
                new jQueryTemp.fx.OpenClose( this, speed, callback, 'horizontally', 'open', easing )
            } else {
                new jQueryTemp.fx.OpenClose( this, speed, callback, 'horizontally', 'close', easing )
            }
        } )
    }, SwitchVertically: function( speed, callback, easing ) {
        return this.queue( 'interfaceFX', function() {
            if ( jQueryTemp.css( this, 'display' ) == 'none' ) {
                new jQueryTemp.fx.OpenClose( this, speed, callback, 'vertically', 'open', easing )
            } else {
                new jQueryTemp.fx.OpenClose( this, speed, callback, 'vertically', 'close', easing )
            }
        } )
    }, OpenVertically: function( speed, callback, easing ) {
        return this.queue( 'interfaceFX', function() {
            new jQueryTemp.fx.OpenClose( this, speed, callback, 'vertically', 'open', easing )
        } )
    }, OpenHorizontally: function( speed, callback, easing ) {
        return this.queue( 'interfaceFX', function() {
            new jQueryTemp.fx.OpenClose( this, speed, callback, 'horizontally', 'open', easing )
        } )
    } } );
jQueryTemp.fx.OpenClose = function( e, speed, callback, direction, type, easing ) {
    if ( !jQueryTemp.fxCheckTag( e ) ) {
        jQueryTemp.dequeue( e, 'interfaceFX' );
        return false
    }
    var z = this;
    var restoreStyle = false;
    z.el = jQueryTemp( e );
    z.easing = typeof callback == 'string' ? callback : easing || null;
    z.callback = typeof callback == 'function' ? callback : null;
    z.type = type;
    z.speed = speed;
    z.oldP = jQueryTemp.iUtil.getSize( e );
    z.oldStyle = { };
    z.oldStyle.position = z.el.css( 'position' );
    z.oldStyle.display = z.el.css( 'display' );
    if ( z.oldStyle.display == 'none' ) {
        oldVisibility = z.el.css( 'visibility' );
        z.el.show();
        restoreStyle = true
    }
    z.oldStyle.top = z.el.css( 'top' );
    z.oldStyle.left = z.el.css( 'left' );
    if ( restoreStyle ) {
        z.el.hide();
        z.el.css( 'visibility', oldVisibility )
    }
    z.oldStyle.width = z.oldP.w + 'px';
    z.oldStyle.height = z.oldP.h + 'px';
    z.oldStyle.overflow = z.el.css( 'overflow' );
    z.oldP.top = parseInt( z.oldStyle.top ) || 0;
    z.oldP.left = parseInt( z.oldStyle.left ) || 0;
    if ( z.oldStyle.position != 'relative' && z.oldStyle.position != 'absolute' ) {
        z.el.css( 'position', 'relative' )
    }
    z.el.css( 'overflow', 'hidden' ).css( 'height', type == 'open' && direction == 'vertically' ? 1 : z.oldP.h + 'px' ).css( 'width', type == 'open' && direction == 'horizontally' ? 1 : z.oldP.w + 'px' );
    z.complete = function() {
        z.el.css( z.oldStyle );
        if ( z.type == 'close' )
            z.el.hide();
        else
            z.el.show();
        jQueryTemp.dequeue( z.el.get( 0 ), 'interfaceFX' )
    };
    switch ( direction ) {
        case'vertically':
            z.eh = new jQueryTemp.fx( z.el.get( 0 ), jQueryTemp.speed( speed - 15, z.easing, callback ), 'height' );
            z.et = new jQueryTemp.fx( z.el.get( 0 ), jQueryTemp.speed( z.speed, z.easing, z.complete ), 'top' );
            if ( z.type == 'close' ) {
                z.eh.custom( z.oldP.h, 0 );
                z.et.custom( z.oldP.top, z.oldP.top + z.oldP.h / 2 )
            } else {
                z.eh.custom( 0, z.oldP.h );
                z.et.custom( z.oldP.top + z.oldP.h / 2, z.oldP.top )
            }
            break;
        case'horizontally':
            z.eh = new jQueryTemp.fx( z.el.get( 0 ), jQueryTemp.speed( speed - 15, z.easing, callback ), 'width' );
            z.et = new jQueryTemp.fx( z.el.get( 0 ), jQueryTemp.speed( z.speed, z.easing, z.complete ), 'left' );
            if ( z.type == 'close' ) {
                z.eh.custom( z.oldP.w, 0 );
                z.et.custom( z.oldP.left, z.oldP.left + z.oldP.w / 2 )
            } else {
                z.eh.custom( 0, z.oldP.w );
                z.et.custom( z.oldP.left + z.oldP.w / 2, z.oldP.left )
            }
            break
        }
};
jQueryTemp.fn.Pulsate = function( speed, times, callback ) {
    return this.queue( 'interfaceFX', function() {
        if ( !jQueryTemp.fxCheckTag( this ) ) {
            jQueryTemp.dequeue( this, 'interfaceFX' );
            return false
        }
        var fx = new jQueryTemp.fx.Pulsate( this, speed, times, callback );
        fx.pulse()
    } )
};
jQueryTemp.fx.Pulsate = function( el, speed, times, callback ) {
    var z = this;
    z.times = times;
    z.cnt = 1;
    z.el = el;
    z.speed = speed;
    z.callback = callback;
    jQueryTemp( z.el ).show();
    z.pulse = function() {
        z.cnt++;
        z.e = new jQueryTemp.fx( z.el, jQueryTemp.speed( z.speed, function() {
            z.ef = new jQueryTemp.fx( z.el, jQueryTemp.speed( z.speed, function() {
                if ( z.cnt <= z.times )
                    z.pulse();
                else {
                    jQueryTemp.dequeue( z.el, 'interfaceFX' );
                    if ( z.callback && z.callback.constructor == Function ) {
                        z.callback.apply( z.el )
                    }
                }
            } ), 'opacity' );
            z.ef.custom( 0, 1 )
        } ), 'opacity' );
        z.e.custom( 1, 0 )
    }
};
jQueryTemp.fn.extend( { Grow: function( speed, callback, easing ) {
        return this.queue( 'interfaceFX', function() {
            new jQueryTemp.fx.Scale( this, speed, 1, 100, true, callback, 'grow', easing )
        } )
    }, Shrink: function( speed, callback, easing ) {
        return this.queue( 'interfaceFX', function() {
            new jQueryTemp.fx.Scale( this, speed, 100, 1, true, callback, 'shrink', easing )
        } )
    }, Puff: function( speed, callback, easing ) {
        return this.queue( 'interfaceFX', function() {
            var easing = easing || 'easeout';
            new jQueryTemp.fx.Scale( this, speed, 100, 150, true, callback, 'puff', easing )
        } )
    }, Scale: function( speed, from, to, restore, callback, easing ) {
        return this.queue( 'interfaceFX', function() {
            new jQueryTemp.fx.Scale( this, speed, from, to, restore, callback, 'Scale', easing )
        } )
    } } );
jQueryTemp.fx.Scale = function( e, speed, from, to, restore, callback, type, easing ) {
    if ( !jQueryTemp.fxCheckTag( e ) ) {
        jQueryTemp.dequeue( e, 'interfaceFX' );
        return false
    }
    var z = this;
    z.el = jQueryTemp( e );
    z.from = parseInt( from ) || 100;
    z.to = parseInt( to ) || 100;
    z.easing = typeof callback == 'string' ? callback : easing || null;
    z.callback = typeof callback == 'function' ? callback : null;
    z.duration = jQueryTemp.speed( speed ).duration;
    z.restore = restore || null;
    z.oldP = jQueryTemp.iUtil.getSize( e );
    z.oldStyle = { width: z.el.css( 'width' ), height: z.el.css( 'height' ), fontSize: z.el.css( 'fontSize' ) || '100%', position: z.el.css( 'position' ), display: z.el.css( 'display' ), top: z.el.css( 'top' ), left: z.el.css( 'left' ), overflow: z.el.css( 'overflow' ), borderTopWidth: z.el.css( 'borderTopWidth' ), borderRightWidth: z.el.css( 'borderRightWidth' ), borderBottomWidth: z.el.css( 'borderBottomWidth' ), borderLeftWidth: z.el.css( 'borderLeftWidth' ), paddingTop: z.el.css( 'paddingTop' ), paddingRight: z.el.css( 'paddingRight' ), paddingBottom: z.el.css( 'paddingBottom' ), paddingLeft: z.el.css( 'paddingLeft' ) };
    z.width = parseInt( z.oldStyle.width ) || e.offsetWidth || 0;
    z.height = parseInt( z.oldStyle.height ) || e.offsetHeight || 0;
    z.top = parseInt( z.oldStyle.top ) || 0;
    z.left = parseInt( z.oldStyle.left ) || 0;
    sizes = [ 'em', 'px', 'pt', '%' ];
    for ( i in sizes ) {
        if ( z.oldStyle.fontSize.indexOf( sizes[i] ) > 0 ) {
            z.fontUnit = sizes[i];
            z.fontSize = parseFloat( z.oldStyle.fontSize )
        }
        if ( z.oldStyle.borderTopWidth.indexOf( sizes[i] ) > 0 ) {
            z.borderTopUnit = sizes[i];
            z.borderTopSize = parseFloat( z.oldStyle.borderTopWidth ) || 0
        }
        if ( z.oldStyle.borderRightWidth.indexOf( sizes[i] ) > 0 ) {
            z.borderRightUnit = sizes[i];
            z.borderRightSize = parseFloat( z.oldStyle.borderRightWidth ) || 0
        }
        if ( z.oldStyle.borderBottomWidth.indexOf( sizes[i] ) > 0 ) {
            z.borderBottomUnit = sizes[i];
            z.borderBottomSize = parseFloat( z.oldStyle.borderBottomWidth ) || 0
        }
        if ( z.oldStyle.borderLeftWidth.indexOf( sizes[i] ) > 0 ) {
            z.borderLeftUnit = sizes[i];
            z.borderLeftSize = parseFloat( z.oldStyle.borderLeftWidth ) || 0
        }
        if ( z.oldStyle.paddingTop.indexOf( sizes[i] ) > 0 ) {
            z.paddingTopUnit = sizes[i];
            z.paddingTopSize = parseFloat( z.oldStyle.paddingTop ) || 0
        }
        if ( z.oldStyle.paddingRight.indexOf( sizes[i] ) > 0 ) {
            z.paddingRightUnit = sizes[i];
            z.paddingRightSize = parseFloat( z.oldStyle.paddingRight ) || 0
        }
        if ( z.oldStyle.paddingBottom.indexOf( sizes[i] ) > 0 ) {
            z.paddingBottomUnit = sizes[i];
            z.paddingBottomSize = parseFloat( z.oldStyle.paddingBottom ) || 0
        }
        if ( z.oldStyle.paddingLeft.indexOf( sizes[i] ) > 0 ) {
            z.paddingLeftUnit = sizes[i];
            z.paddingLeftSize = parseFloat( z.oldStyle.paddingLeft ) || 0
        }
    }
    if ( z.oldStyle.position != 'relative' && z.oldStyle.position != 'absolute' ) {
        z.el.css( 'position', 'relative' )
    }
    z.el.css( 'overflow', 'hidden' );
    z.type = type;
    switch ( z.type ) {
        case'grow':
            z.startTop = z.top + z.oldP.h / 2;
            z.endTop = z.top;
            z.startLeft = z.left + z.oldP.w / 2;
            z.endLeft = z.left;
            break;
        case'shrink':
            z.endTop = z.top + z.oldP.h / 2;
            z.startTop = z.top;
            z.endLeft = z.left + z.oldP.w / 2;
            z.startLeft = z.left;
            break;
        case'puff':
            z.endTop = z.top - z.oldP.h / 4;
            z.startTop = z.top;
            z.endLeft = z.left - z.oldP.w / 4;
            z.startLeft = z.left;
            break
    }
    z.firstStep = false;
    z.t = ( new Date ).getTime();
    z.clear = function() {
        clearInterval( z.timer );
        z.timer = null
    };
    z.step = function() {
        if ( z.firstStep == false ) {
            z.el.show();
            z.firstStep = true
        }
        var t = ( new Date ).getTime();
        var n = t - z.t;
        var p = n / z.duration;
        if ( t >= z.duration + z.t ) {
            setTimeout( function() {
                o = 1;
                if ( z.type ) {
                    t = z.endTop;
                    l = z.endLeft;
                    if ( z.type == 'puff' )
                        o = 0
                }
                z.zoom( z.to, l, t, true, o )
            }, 13 );
            z.clear()
        } else {
            o = 1;
            if ( !jQueryTemp.easing || !jQueryTemp.easing[z.easing] ) {
                s = ( ( -Math.cos( p * Math.PI ) / 2 ) + 0.5 ) * ( z.to - z.from ) + z.from
            } else {
                s = jQueryTemp.easing[z.easing]( p, n, z.from, ( z.to - z.from ), z.duration )
            }
            if ( z.type ) {
                if ( !jQueryTemp.easing || !jQueryTemp.easing[z.easing] ) {
                    t = ( ( -Math.cos( p * Math.PI ) / 2 ) + 0.5 ) * ( z.endTop - z.startTop ) + z.startTop;
                    l = ( ( -Math.cos( p * Math.PI ) / 2 ) + 0.5 ) * ( z.endLeft - z.startLeft ) + z.startLeft;
                    if ( z.type == 'puff' )
                        o = ( ( -Math.cos( p * Math.PI ) / 2 ) + 0.5 ) * ( -0.9999 ) + 0.9999
                } else {
                    t = jQueryTemp.easing[z.easing]( p, n, z.startTop, ( z.endTop - z.startTop ), z.duration );
                    l = jQueryTemp.easing[z.easing]( p, n, z.startLeft, ( z.endLeft - z.startLeft ), z.duration );
                    if ( z.type == 'puff' )
                        o = jQueryTemp.easing[z.easing]( p, n, 0.9999, -0.9999, z.duration )
                }
            }
            z.zoom( s, l, t, false, o )
        }
    };
    z.timer = setInterval( function() {
        z.step()
    }, 13 );
    z.zoom = function( percent, left, top, finish, opacity ) {
        z.el.css( 'height', z.height * percent / 100 + 'px' ).css( 'width', z.width * percent / 100 + 'px' ).css( 'left', left + 'px' ).css( 'top', top + 'px' ).css( 'fontSize', z.fontSize * percent / 100 + z.fontUnit );
        if ( z.borderTopSize )
            z.el.css( 'borderTopWidth', z.borderTopSize * percent / 100 + z.borderTopUnit );
        if ( z.borderRightSize )
            z.el.css( 'borderRightWidth', z.borderRightSize * percent / 100 + z.borderRightUnit );
        if ( z.borderBottomSize )
            z.el.css( 'borderBottomWidth', z.borderBottomSize * percent / 100 + z.borderBottomUnit );
        if ( z.borderLeftSize )
            z.el.css( 'borderLeftWidth', z.borderLeftSize * percent / 100 + z.borderLeftUnit );
        if ( z.paddingTopSize )
            z.el.css( 'paddingTop', z.paddingTopSize * percent / 100 + z.paddingTopUnit );
        if ( z.paddingRightSize )
            z.el.css( 'paddingRight', z.paddingRightSize * percent / 100 + z.paddingRightUnit );
        if ( z.paddingBottomSize )
            z.el.css( 'paddingBottom', z.paddingBottomSize * percent / 100 + z.paddingBottomUnit );
        if ( z.paddingLeftSize )
            z.el.css( 'paddingLeft', z.paddingLeftSize * percent / 100 + z.paddingLeftUnit );
        if ( z.type == 'puff' ) {
            if ( window.ActiveXObject )
                z.el.get( 0 ).style.filter = "alpha(opacity=" + opacity * 100 + ")";
            z.el.get( 0 ).style.opacity = opacity
        }
        if ( finish ) {
            if ( z.restore ) {
                z.el.css( z.oldStyle )
            }
            if ( z.type == 'shrink' || z.type == 'puff' ) {
                z.el.css( 'display', 'none' );
                if ( z.type == 'puff' ) {
                    if ( window.ActiveXObject )
                        z.el.get( 0 ).style.filter = "alpha(opacity=" + 100 + ")";
                    z.el.get( 0 ).style.opacity = 1
                }
            } else
                z.el.css( 'display', 'block' );
            if ( z.callback )
                z.callback.apply( z.el.get( 0 ) );
            jQueryTemp.dequeue( z.el.get( 0 ), 'interfaceFX' )
        }
    }
};
jQueryTemp.fn.extend( { ScrollTo: function( speed, axis, easing ) {
        o = jQueryTemp.speed( speed );
        return this.queue( 'interfaceFX', function() {
            new jQueryTemp.fx.ScrollTo( this, o, axis, easing )
        } )
    }, ScrollToAnchors: function( speed, axis, easing ) {
        return this.each( function() {
            jQueryTemp( 'a[@href*="#"]', this ).click( function( e ) {
                parts = this.href.split( '#' );
                jQueryTemp( '#' + parts[1] ).ScrollTo( speed, axis, easing );
                return false
            } )
        } )
    } } );
jQueryTemp.fx.ScrollTo = function( e, o, axis, easing ) {
    var z = this;
    z.o = o;
    z.e = e;
    z.axis = /vertical|horizontal/.test( axis ) ? axis : false;
    z.easing = easing;
    p = jQueryTemp.iUtil.getPosition( e );
    s = jQueryTemp.iUtil.getScroll();
    z.clear = function() {
        clearInterval( z.timer );
        z.timer = null;
        jQueryTemp.dequeue( z.e, 'interfaceFX' )
    };
    z.t = ( new Date ).getTime();
    s.h = s.h > s.ih ? ( s.h - s.ih ) : s.h;
    s.w = s.w > s.iw ? ( s.w - s.iw ) : s.w;
    z.endTop = p.y > s.h ? s.h : p.y;
    z.endLeft = p.x > s.w ? s.w : p.x;
    z.startTop = s.t;
    z.startLeft = s.l;
    z.step = function() {
        var t = ( new Date ).getTime();
        var n = t - z.t;
        var p = n / z.o.duration;
        if ( t >= z.o.duration + z.t ) {
            z.clear();
            setTimeout( function() {
                z.scroll( z.endTop, z.endLeft )
            }, 13 )
        } else {
            if ( !z.axis || z.axis == 'vertical' ) {
                if ( !jQueryTemp.easing || !jQueryTemp.easing[z.easing] ) {
                    st = ( ( -Math.cos( p * Math.PI ) / 2 ) + 0.5 ) * ( z.endTop - z.startTop ) + z.startTop
                } else {
                    st = jQueryTemp.easing[z.easing]( p, n, z.startTop, ( z.endTop - z.startTop ), z.o.duration )
                }
            } else {
                st = z.startTop
            }
            if ( !z.axis || z.axis == 'horizontal' ) {
                if ( !jQueryTemp.easing || !jQueryTemp.easing[z.easing] ) {
                    sl = ( ( -Math.cos( p * Math.PI ) / 2 ) + 0.5 ) * ( z.endLeft - z.startLeft ) + z.startLeft
                } else {
                    sl = jQueryTemp.easing[z.easing]( p, n, z.startLeft, ( z.endLeft - z.startLeft ), z.o.duration )
                }
            } else {
                sl = z.startLeft
            }
            z.scroll( st, sl )
        }
    };
    z.scroll = function( t, l ) {
        window.scrollTo( l, t )
    };
    z.timer = setInterval( function() {
        z.step()
    }, 13 )
};
jQueryTemp.fn.Shake = function( times, callback ) {
    return this.queue( 'interfaceFX', function() {
        if ( !jQueryTemp.fxCheckTag( this ) ) {
            jQueryTemp.dequeue( this, 'interfaceFX' );
            return false
        }
        var e = new jQueryTemp.fx.Shake( this, times, callback );
        e.shake()
    } )
};
jQueryTemp.fx.Shake = function( e, times, callback ) {
    var z = this;
    z.el = jQueryTemp( e );
    z.el.show();
    z.times = parseInt( times ) || 3;
    z.callback = callback;
    z.cnt = 1;
    z.oldStyle = { };
    z.oldStyle.position = z.el.css( 'position' );
    z.oldStyle.top = parseInt( z.el.css( 'top' ) ) || 0;
    z.oldStyle.left = parseInt( z.el.css( 'left' ) ) || 0;
    if ( z.oldStyle.position != 'relative' && z.oldStyle.position != 'absolute' ) {
        z.el.css( 'position', 'relative' )
    }
    z.shake = function() {
        z.cnt++;
        z.e = new jQueryTemp.fx( z.el.get( 0 ), { duration: 60, complete: function() {
                z.e = new jQueryTemp.fx( z.el.get( 0 ), { duration: 60, complete: function() {
                        z.e = new jQueryTemp.fx( e, { duration: 60, complete: function() {
                                if ( z.cnt <= z.times )
                                    z.shake();
                                else {
                                    z.el.css( 'position', z.oldStyle.position ).css( 'top', z.oldStyle.top + 'px' ).css( 'left', z.oldStyle.left + 'px' );
                                    jQueryTemp.dequeue( z.el.get( 0 ), 'interfaceFX' );
                                    if ( z.callback && z.callback.constructor == Function ) {
                                        z.callback.apply( z.el.get( 0 ) )
                                    }
                                }
                            } }, 'left' );
                        z.e.custom( z.oldStyle.left - 20, z.oldStyle.left )
                    } }, 'left' );
                z.e.custom( z.oldStyle.left + 20, z.oldStyle.left - 20 )
            } }, 'left' );
        z.e.custom( z.oldStyle.left, z.oldStyle.left + 20 )
    }
};
jQueryTemp.fn.extend( { SlideInUp: function( speed, callback, easing ) {
        return this.queue( 'interfaceFX', function() {
            new jQueryTemp.fx.slide( this, speed, callback, 'up', 'in', easing )
        } )
    }, SlideOutUp: function( speed, callback, easing ) {
        return this.queue( 'interfaceFX', function() {
            new jQueryTemp.fx.slide( this, speed, callback, 'up', 'out', easing )
        } )
    }, SlideToggleUp: function( speed, callback, easing ) {
        return this.queue( 'interfaceFX', function() {
            new jQueryTemp.fx.slide( this, speed, callback, 'up', 'toggle', easing )
        } )
    }, SlideInDown: function( speed, callback, easing ) {
        return this.queue( 'interfaceFX', function() {
            new jQueryTemp.fx.slide( this, speed, callback, 'down', 'in', easing )
        } )
    }, SlideOutDown: function( speed, callback, easing ) {
        return this.queue( 'interfaceFX', function() {
            new jQueryTemp.fx.slide( this, speed, callback, 'down', 'out', easing )
        } )
    }, SlideToggleDown: function( speed, callback, easing ) {
        return this.queue( 'interfaceFX', function() {
            new jQueryTemp.fx.slide( this, speed, callback, 'down', 'toggle', easing )
        } )
    }, SlideInLeft: function( speed, callback, easing ) {
        return this.queue( 'interfaceFX', function() {
            new jQueryTemp.fx.slide( this, speed, callback, 'left', 'in', easing )
        } )
    }, SlideOutLeft: function( speed, callback, easing ) {
        return this.queue( 'interfaceFX', function() {
            new jQueryTemp.fx.slide( this, speed, callback, 'left', 'out', easing )
        } )
    }, SlideToggleLeft: function( speed, callback, easing ) {
        return this.queue( 'interfaceFX', function() {
            new jQueryTemp.fx.slide( this, speed, callback, 'left', 'toggle', easing )
        } )
    }, SlideInRight: function( speed, callback, easing ) {
        return this.queue( 'interfaceFX', function() {
            new jQueryTemp.fx.slide( this, speed, callback, 'right', 'in', easing )
        } )
    }, SlideOutRight: function( speed, callback, easing ) {
        return this.queue( 'interfaceFX', function() {
            new jQueryTemp.fx.slide( this, speed, callback, 'right', 'out', easing )
        } )
    }, SlideToggleRight: function( speed, callback, easing ) {
        return this.queue( 'interfaceFX', function() {
            new jQueryTemp.fx.slide( this, speed, callback, 'right', 'toggle', easing )
        } )
    } } );
jQueryTemp.fx.slide = function( e, speed, callback, direction, type, easing ) {
    if ( !jQueryTemp.fxCheckTag( e ) ) {
        jQueryTemp.dequeue( e, 'interfaceFX' );
        return false
    }
    var z = this;
    z.el = jQueryTemp( e );
    z.easing = typeof callback == 'string' ? callback : easing || null;
    z.callback = typeof callback == 'function' ? callback : null;
    if ( type == 'toggle' ) {
        type = z.el.css( 'display' ) == 'none' ? 'in' : 'out'
    }
    if ( !e.ifxFirstDisplay )
        e.ifxFirstDisplay = z.el.css( 'display' );
    z.el.show();
    z.speed = speed;
    z.fx = jQueryTemp.fx.buildWrapper( e );
    z.type = type;
    z.direction = direction;
    z.complete = function() {
        if ( z.type == 'out' )
            z.el.css( 'visibility', 'hidden' );
        jQueryTemp.fx.destroyWrapper( z.fx.wrapper.get( 0 ), z.fx.oldStyle );
        if ( z.type == 'in' ) {
            z.el.css( 'display', z.el.get( 0 ).ifxFirstDisplay == 'none' ? 'block' : z.el.get( 0 ).ifxFirstDisplay )
        } else {
            z.el.css( 'display', 'none' );
            z.el.css( 'visibility', 'visible' )
        }
        if ( z.callback && z.callback.constructor == Function ) {
            z.callback.apply( z.el.get( 0 ) )
        }
        jQueryTemp.dequeue( z.el.get( 0 ), 'interfaceFX' )
    };
    switch ( z.direction ) {
        case'up':
            z.ef = new jQueryTemp.fx( z.el.get( 0 ), jQueryTemp.speed( z.speed, z.easing, z.complete ), 'top' );
            z.efx = new jQueryTemp.fx( z.fx.wrapper.get( 0 ), jQueryTemp.speed( z.speed, z.easing ), 'height' );
            if ( z.type == 'in' ) {
                z.ef.custom( -z.fx.oldStyle.sizes.hb, 0 );
                z.efx.custom( 0, z.fx.oldStyle.sizes.hb )
            } else {
                z.ef.custom( 0, -z.fx.oldStyle.sizes.hb );
                z.efx.custom( z.fx.oldStyle.sizes.hb, 0 )
            }
            break;
        case'down':
            z.ef = new jQueryTemp.fx( z.el.get( 0 ), jQueryTemp.speed( z.speed, z.easing, z.complete ), 'top' );
            if ( z.type == 'in' ) {
                z.ef.custom( z.fx.oldStyle.sizes.hb, 0 )
            } else {
                z.ef.custom( 0, z.fx.oldStyle.sizes.hb )
            }
            break;
        case'left':
            z.ef = new jQueryTemp.fx( z.el.get( 0 ), jQueryTemp.speed( z.speed, z.easing, z.complete ), 'left' );
            z.efx = new jQueryTemp.fx( z.fx.wrapper.get( 0 ), jQueryTemp.speed( z.speed, z.easing ), 'width' );
            if ( z.type == 'in' ) {
                z.ef.custom( -z.fx.oldStyle.sizes.wb, 0 );
                z.efx.custom( 0, z.fx.oldStyle.sizes.wb )
            } else {
                z.ef.custom( 0, -z.fx.oldStyle.sizes.wb );
                z.efx.custom( z.fx.oldStyle.sizes.wb, 0 )
            }
            break;
        case'right':
            z.ef = new jQueryTemp.fx( z.el.get( 0 ), jQueryTemp.speed( z.speed, z.easing, z.complete ), 'left' );
            if ( z.type == 'in' ) {
                z.ef.custom( z.fx.oldStyle.sizes.wb, 0 )
            } else {
                z.ef.custom( 0, z.fx.oldStyle.sizes.wb )
            }
            break
        }
};
jQueryTemp.transferHelper = null;
jQueryTemp.fn.TransferTo = function( o ) {
    return this.queue( 'interfaceFX', function() {
        new jQueryTemp.fx.itransferTo( this, o )
    } )
};
jQueryTemp.fx.itransferTo = function( e, o ) {
    if ( jQueryTemp.transferHelper == null ) {
        jQueryTemp( 'body', document ).append( '<div id="transferHelper"></div>' );
        jQueryTemp.transferHelper = jQueryTemp( '#transferHelper' )
    }
    jQueryTemp.transferHelper.css( 'display', 'block' ).css( 'position', 'absolute' );
    var z = this;
    z.el = jQueryTemp( e );
    if ( !o || !o.to ) {
        return
    }
    if ( o.to.constructor == String && document.getElementById( o.to ) ) {
        o.to = document.getElementById( o.to )
    } else if ( !o.to.childNodes ) {
        return
    }
    if ( !o.duration ) {
        o.duration = 500
    }
    z.duration = o.duration;
    z.to = o.to;
    z.classname = o.className;
    z.complete = o.complete;
    if ( z.classname ) {
        jQueryTemp.transferHelper.addClass( z.classname )
    }
    z.diffWidth = 0;
    z.diffHeight = 0;
    if ( jQueryTemp.boxModel ) {
        z.diffWidth = ( parseInt( jQueryTemp.transferHelper.css( 'borderLeftWidth' ) ) || 0 ) + ( parseInt( jQueryTemp.transferHelper.css( 'borderRightWidth' ) ) || 0 ) + ( parseInt( jQueryTemp.transferHelper.css( 'paddingLeft' ) ) || 0 ) + ( parseInt( jQueryTemp.transferHelper.css( 'paddingRight' ) ) || 0 );
        z.diffHeight = ( parseInt( jQueryTemp.transferHelper.css( 'borderTopWidth' ) ) || 0 ) + ( parseInt( jQueryTemp.transferHelper.css( 'borderBottomWidth' ) ) || 0 ) + ( parseInt( jQueryTemp.transferHelper.css( 'paddingTop' ) ) || 0 ) + ( parseInt( jQueryTemp.transferHelper.css( 'paddingBottom' ) ) || 0 )
    }
    z.start = jQueryTemp.extend( jQueryTemp.iUtil.getPosition( z.el.get( 0 ) ), jQueryTemp.iUtil.getSize( z.el.get( 0 ) ) );
    z.end = jQueryTemp.extend( jQueryTemp.iUtil.getPosition( z.to ), jQueryTemp.iUtil.getSize( z.to ) );
    z.start.wb -= z.diffWidth;
    z.start.hb -= z.diffHeight;
    z.end.wb -= z.diffWidth;
    z.end.hb -= z.diffHeight;
    z.callback = o.complete;
    jQueryTemp.transferHelper.css( 'width', z.start.wb + 'px' ).css( 'height', z.start.hb + 'px' ).css( 'top', z.start.y + 'px' ).css( 'left', z.start.x + 'px' ).animate( { top: z.end.y, left: z.end.x, width: z.end.wb, height: z.end.hb }, z.duration, function() {
        if ( z.classname )
            jQueryTemp.transferHelper.removeClass( z.classname );
        jQueryTemp.transferHelper.css( 'display', 'none' );
        if ( z.complete && z.complete.constructor == Function ) {
            z.complete.apply( z.el.get( 0 ), [ z.to ] )
        }
        jQueryTemp.dequeue( z.el.get( 0 ), 'interfaceFX' )
    } )
};
jQueryTemp.ImageBox = { options: { border: 10, loaderSRC: 'images/loading.gif', closeHTML: '<img src="images/close.jpg" />', overlayOpacity: 0.8, textImage: 'Showing image', textImageFrom: 'from', fadeDuration: 400 }, imageLoaded: false, firstResize: false, currentRel: null, animationInProgress: false, opened: false, keyPressed: function( event ) {
        if ( !jQueryTemp.ImageBox.opened || jQueryTemp.ImageBox.animationInProgress )
            return;
        var pressedKey = event.charCode || event.keyCode || -1;
        switch ( pressedKey ) {
            case 35:
                if ( jQueryTemp.ImageBox.currentRel )
                    jQueryTemp.ImageBox.start( null, jQueryTemp( 'a[@rel=' + jQueryTemp.ImageBox.currentRel + ']:last' ).get( 0 ) );
                break;
            case 36:
                if ( jQueryTemp.ImageBox.currentRel )
                    jQueryTemp.ImageBox.start( null, jQueryTemp( 'a[@rel=' + jQueryTemp.ImageBox.currentRel + ']:first' ).get( 0 ) );
                break;
            case 37:
            case 8:
            case 33:
            case 80:
            case 112:
                var prevEl = jQueryTemp( '#ImageBoxPrevImage' );
                if ( prevEl.get( 0 ).onclick != null ) {
                    prevEl.get( 0 ).onclick.apply( prevEl.get( 0 ) )
                }
                break;
            case 38:
                break;
            case 39:
            case 34:
            case 32:
            case 110:
            case 78:
                var nextEl = jQueryTemp( '#ImageBoxNextImage' );
                if ( nextEl.get( 0 ).onclick != null ) {
                    nextEl.get( 0 ).onclick.apply( nextEl.get( 0 ) )
                }
                break;
            case 40:
                break;
            case 27:
                jQueryTemp.ImageBox.hideImage();
                break
            }
    }, init: function( options ) {
        if ( options )
            jQueryTemp.extend( jQueryTemp.ImageBox.options, options );
        if ( window.event ) {
            jQueryTemp( 'body', document ).bind( 'keyup', jQueryTemp.ImageBox.keyPressed )
        } else {
            jQueryTemp( document ).bind( 'keyup', jQueryTemp.ImageBox.keyPressed )
        }
        jQueryTemp( 'a' ).each( function() {
            el = jQueryTemp( this );
            relAttr = el.attr( 'rel' ) || '';
            hrefAttr = el.attr( 'href' ) || '';
            imageTypes = /\.jpg|\.jpeg|\.png|\.gif|\.bmp/g;
            if ( hrefAttr.toLowerCase().match( imageTypes ) != null && relAttr.toLowerCase().indexOf( 'imagebox' ) == 0 ) {
                el.bind( 'click', jQueryTemp.ImageBox.start )
            }
        } );
        if ( jQueryTemp.browser.msie ) {
            iframe = document.createElement( 'iframe' );
            jQueryTemp( iframe ).attr( { id: 'ImageBoxIframe', src: 'javascript:false;', frameborder: 'no', scrolling: 'no' } ).css( { display: 'none', position: 'absolute', top: '0', left: '0', filter: 'progid:DXImageTransform.Microsoft.Alpha(opacity=0)' } );
            jQueryTemp( 'body' ).append( iframe )
        }
        overlay = document.createElement( 'div' );
        jQueryTemp( overlay ).attr( 'id', 'ImageBoxOverlay' ).css( { position: 'absolute', display: 'none', top: '0', left: '0', opacity: 0 } ).append( document.createTextNode( ' ' ) ).bind( 'click', jQueryTemp.ImageBox.hideImage );
        captionText = document.createElement( 'div' );
        jQueryTemp( captionText ).attr( 'id', 'ImageBoxCaptionText' ).css( { paddingLeft: jQueryTemp.ImageBox.options.border + 'px' } ).append( document.createTextNode( ' ' ) );
        captionImages = document.createElement( 'div' );
        jQueryTemp( captionImages ).attr( 'id', 'ImageBoxCaptionImages' ).css( { paddingLeft: jQueryTemp.ImageBox.options.border + 'px', paddingBottom: jQueryTemp.ImageBox.options.border + 'px' } ).append( document.createTextNode( ' ' ) );
        closeEl = document.createElement( 'a' );
        jQueryTemp( closeEl ).attr( { id: 'ImageBoxClose', href: '#' } ).css( { position: 'absolute', right: jQueryTemp.ImageBox.options.border + 'px', top: '0' } ).append( jQueryTemp.ImageBox.options.closeHTML ).bind( 'click', jQueryTemp.ImageBox.hideImage );
        captionEl = document.createElement( 'div' );
        jQueryTemp( captionEl ).attr( 'id', 'ImageBoxCaption' ).css( { position: 'relative', textAlign: 'left', margin: '0 auto', zIndex: 1 } ).append( captionText ).append( captionImages ).append( closeEl );
        loader = document.createElement( 'img' );
        loader.src = jQueryTemp.ImageBox.options.loaderSRC;
        jQueryTemp( loader ).attr( 'id', 'ImageBoxLoader' ).css( { position: 'absolute' } );
        prevImage = document.createElement( 'a' );
        jQueryTemp( prevImage ).attr( { id: 'ImageBoxPrevImage', href: '#' } ).css( { position: 'absolute', display: 'none', overflow: 'hidden', textDecoration: 'none' } ).append( document.createTextNode( ' ' ) );
        nextImage = document.createElement( 'a' );
        jQueryTemp( nextImage ).attr( { id: 'ImageBoxNextImage', href: '#' } ).css( { position: 'absolute', overflow: 'hidden', textDecoration: 'none' } ).append( document.createTextNode( ' ' ) );
        container = document.createElement( 'div' );
        jQueryTemp( container ).attr( 'id', 'ImageBoxContainer' ).css( { display: 'none', position: 'relative', overflow: 'hidden', textAlign: 'left', margin: '0 auto', top: '0', left: '0', zIndex: 2 } ).append( [ loader, prevImage, nextImage ] );
        outerContainer = document.createElement( 'div' );
        jQueryTemp( outerContainer ).attr( 'id', 'ImageBoxOuterContainer' ).css( { display: 'none', position: 'absolute', overflow: 'hidden', top: '0', left: '0', textAlign: 'center', backgroundColor: 'transparent', lineHeigt: '0' } ).append( [ container, captionEl ] );
        jQueryTemp( 'body' ).append( overlay ).append( outerContainer )
    }, start: function( e, elm ) {
        el = elm ? jQueryTemp( elm ) : jQueryTemp( this );
        linkRel = el.attr( 'rel' );
        var totalImages, iteration, prevImage, nextImage;
        if ( linkRel != 'imagebox' ) {
            jQueryTemp.ImageBox.currentRel = linkRel;
            gallery = jQueryTemp( 'a[@rel=' + linkRel + ']' );
            totalImages = gallery.size();
            iteration = gallery.index( elm ? elm : this );
            prevImage = gallery.get( iteration - 1 );
            nextImage = gallery.get( iteration + 1 )
        }
        imageSrc = el.attr( 'href' );
        captionText = el.attr( 'title' );
        pageSize = jQueryTemp.iUtil.getScroll();
        overlay = jQueryTemp( '#ImageBoxOverlay' );
        if ( !jQueryTemp.ImageBox.opened ) {
            jQueryTemp.ImageBox.opened = true;
            if ( jQueryTemp.browser.msie ) {
                jQueryTemp( '#ImageBoxIframe' ).css( 'height', Math.max( pageSize.ih, pageSize.h ) + 'px' ).css( 'width', Math.max( pageSize.iw, pageSize.w ) + 'px' ).show()
            }
            overlay.css( 'height', Math.max( pageSize.ih, pageSize.h ) + 'px' ).css( 'width', Math.max( pageSize.iw, pageSize.w ) + 'px' ).show().fadeTo( 300, jQueryTemp.ImageBox.options.overlayOpacity, function() {
                jQueryTemp.ImageBox.loadImage( imageSrc, captionText, pageSize, totalImages, iteration, prevImage, nextImage )
            } );
            jQueryTemp( '#ImageBoxOuterContainer' ).css( 'width', Math.max( pageSize.iw, pageSize.w ) + 'px' )
        } else {
            jQueryTemp( '#ImageBoxPrevImage' ).get( 0 ).onclick = null;
            jQueryTemp( '#ImageBoxNextImage' ).get( 0 ).onclick = null;
            jQueryTemp.ImageBox.loadImage( imageSrc, captionText, pageSize, totalImages, iteration, prevImage, nextImage )
        }
        return false
    }, loadImage: function( imageSrc, captiontext, pageSize, totalImages, iteration, prevImage, nextImage ) {
        jQueryTemp( '#ImageBoxCurrentImage' ).remove();
        prevImageEl = jQueryTemp( '#ImageBoxPrevImage' );
        prevImageEl.hide();
        nextImageEl = jQueryTemp( '#ImageBoxNextImage' );
        nextImageEl.hide();
        loader = jQueryTemp( '#ImageBoxLoader' );
        container = jQueryTemp( '#ImageBoxContainer' );
        outerContainer = jQueryTemp( '#ImageBoxOuterContainer' );
        captionEl = jQueryTemp( '#ImageBoxCaption' ).css( 'visibility', 'hidden' );
        jQueryTemp( '#ImageBoxCaptionText' ).html( captionText );
        jQueryTemp.ImageBox.animationInProgress = true;
        if ( totalImages )
            jQueryTemp( '#ImageBoxCaptionImages' ).html( jQueryTemp.ImageBox.options.textImage + ' ' + ( iteration + 1 ) + ' ' + jQueryTemp.ImageBox.options.textImageFrom + ' ' + totalImages );
        if ( prevImage ) {
            prevImageEl.get( 0 ).onclick = function() {
                this.blur();
                jQueryTemp.ImageBox.start( null, prevImage );
                return false
            }
        }
        if ( nextImage ) {
            nextImageEl.get( 0 ).onclick = function() {
                this.blur();
                jQueryTemp.ImageBox.start( null, nextImage );
                return false
            }
        }
        loader.show();
        containerSize = jQueryTemp.iUtil.getSize( container.get( 0 ) );
        containerW = Math.max( containerSize.wb, loader.get( 0 ).width + jQueryTemp.ImageBox.options.border * 2 );
        containerH = Math.max( containerSize.hb, loader.get( 0 ).height + jQueryTemp.ImageBox.options.border * 2 );
        loader.css( { left: ( containerW - loader.get( 0 ).width ) / 2 + 'px', top: ( containerH - loader.get( 0 ).height ) / 2 + 'px' } );
        container.css( { width: containerW + 'px', height: containerH + 'px' } ).show();
        clientSize = jQueryTemp.iUtil.getClient();
        outerContainer.css( 'top', pageSize.t + ( clientSize.h / 15 ) + 'px' );
        if ( outerContainer.css( 'display' ) == 'none' ) {
            outerContainer.show().fadeIn( jQueryTemp.ImageBox.options.fadeDuration )
        }
        imageEl = new Image;
        jQueryTemp( imageEl ).attr( 'id', 'ImageBoxCurrentImage' ).bind( 'load', function() {
            containerW = imageEl.width + jQueryTemp.ImageBox.options.border * 2;
            containerH = imageEl.height + jQueryTemp.ImageBox.options.border * 2;
            loader.hide();
            container.animate( { height: containerH }, containerSize.hb != containerH ? jQueryTemp.ImageBox.options.fadeDuration : 1, function() {
                container.animate( { width: containerW }, containerSize.wb != containerW ? jQueryTemp.ImageBox.options.fadeDuration : 1, function() {
                    container.prepend( imageEl );
                    jQueryTemp( imageEl ).css( { position: 'absolute', left: jQueryTemp.ImageBox.options.border + 'px', top: jQueryTemp.ImageBox.options.border + 'px' } ).fadeIn( jQueryTemp.ImageBox.options.fadeDuration, function() {
                        captionSize = jQueryTemp.iUtil.getSize( captionEl.get( 0 ) );
                        if ( prevImage ) {
                            prevImageEl.css( { left: jQueryTemp.ImageBox.options.border + 'px', top: jQueryTemp.ImageBox.options.border + 'px', width: containerW / 2 - jQueryTemp.ImageBox.options.border * 3 + 'px', height: containerH - jQueryTemp.ImageBox.options.border * 2 + 'px' } ).show()
                        }
                        if ( nextImage ) {
                            nextImageEl.css( { left: containerW / 2 + jQueryTemp.ImageBox.options.border * 2 + 'px', top: jQueryTemp.ImageBox.options.border + 'px', width: containerW / 2 - jQueryTemp.ImageBox.options.border * 3 + 'px', height: containerH - jQueryTemp.ImageBox.options.border * 2 + 'px' } ).show()
                        }
                        captionEl.css( { width: containerW + 'px', top: -captionSize.hb + 'px', visibility: 'visible' } ).animate( { top: -1 }, jQueryTemp.ImageBox.options.fadeDuration, function() {
                            jQueryTemp.ImageBox.animationInProgress = false
                        } )
                    } )
                } )
            } )
        } );
        imageEl.src = imageSrc
    }, hideImage: function() {
        jQueryTemp( '#ImageBoxCurrentImage' ).remove();
        jQueryTemp( '#ImageBoxOuterContainer' ).hide();
        jQueryTemp( '#ImageBoxCaption' ).css( 'visibility', 'hidden' );
        jQueryTemp( '#ImageBoxOverlay' ).fadeTo( 300, 0, function() {
            jQueryTemp( this ).hide();
            if ( jQueryTemp.browser.msie ) {
                jQueryTemp( '#ImageBoxIframe' ).hide()
            }
        } );
        jQueryTemp( '#ImageBoxPrevImage' ).get( 0 ).onclick = null;
        jQueryTemp( '#ImageBoxNextImage' ).get( 0 ).onclick = null;
        jQueryTemp.ImageBox.currentRel = null;
        jQueryTemp.ImageBox.opened = false;
        jQueryTemp.ImageBox.animationInProgress = false;
        return false
    } };
jQueryTemp.iResize = { resizeElement: null, resizeDirection: null, dragged: null, pointer: null, sizes: null, position: null, startDrag: function( e ) {
        jQueryTemp.iResize.dragged = ( this.dragEl ) ? this.dragEl : this;
        jQueryTemp.iResize.pointer = jQueryTemp.iUtil.getPointer( e );
        jQueryTemp.iResize.sizes = { width: parseInt( jQueryTemp( jQueryTemp.iResize.dragged ).css( 'width' ) ) || 0, height: parseInt( jQueryTemp( jQueryTemp.iResize.dragged ).css( 'height' ) ) || 0 };
        jQueryTemp.iResize.position = { top: parseInt( jQueryTemp( jQueryTemp.iResize.dragged ).css( 'top' ) ) || 0, left: parseInt( jQueryTemp( jQueryTemp.iResize.dragged ).css( 'left' ) ) || 0 };
        jQueryTemp( document ).bind( 'mousemove', jQueryTemp.iResize.moveDrag ).bind( 'mouseup', jQueryTemp.iResize.stopDrag );
        if ( typeof jQueryTemp.iResize.dragged.resizeOptions.onDragStart === 'function' ) {
            jQueryTemp.iResize.dragged.resizeOptions.onDragStart.apply( jQueryTemp.iResize.dragged )
        }
        return false
    }, stopDrag: function( e ) {
        jQueryTemp( document ).unbind( 'mousemove', jQueryTemp.iResize.moveDrag ).unbind( 'mouseup', jQueryTemp.iResize.stopDrag );
        if ( typeof jQueryTemp.iResize.dragged.resizeOptions.onDragStop === 'function' ) {
            jQueryTemp.iResize.dragged.resizeOptions.onDragStop.apply( jQueryTemp.iResize.dragged )
        }
        jQueryTemp.iResize.dragged = null
    }, moveDrag: function( e ) {
        if ( !jQueryTemp.iResize.dragged ) {
            return
        }
        pointer = jQueryTemp.iUtil.getPointer( e );
        newTop = jQueryTemp.iResize.position.top - jQueryTemp.iResize.pointer.y + pointer.y;
        newLeft = jQueryTemp.iResize.position.left - jQueryTemp.iResize.pointer.x + pointer.x;
        newTop = Math.max( Math.min( newTop, jQueryTemp.iResize.dragged.resizeOptions.maxBottom - jQueryTemp.iResize.sizes.height ), jQueryTemp.iResize.dragged.resizeOptions.minTop );
        newLeft = Math.max( Math.min( newLeft, jQueryTemp.iResize.dragged.resizeOptions.maxRight - jQueryTemp.iResize.sizes.width ), jQueryTemp.iResize.dragged.resizeOptions.minLeft );
        if ( typeof jQueryTemp.iResize.dragged.resizeOptions.onDrag === 'function' ) {
            var newPos = jQueryTemp.iResize.dragged.resizeOptions.onDrag.apply( jQueryTemp.iResize.dragged, [ newLeft, newTop ] );
            if ( typeof newPos == 'array' && newPos.length == 2 ) {
                newLeft = newPos[0];
                newTop = newPos[1]
            }
        }
        jQueryTemp.iResize.dragged.style.top = newTop + 'px';
        jQueryTemp.iResize.dragged.style.left = newLeft + 'px';
        return false
    }, start: function( e ) {
        jQueryTemp( document ).bind( 'mousemove', jQueryTemp.iResize.move ).bind( 'mouseup', jQueryTemp.iResize.stop );
        jQueryTemp.iResize.resizeElement = this.resizeElement;
        jQueryTemp.iResize.resizeDirection = this.resizeDirection;
        jQueryTemp.iResize.pointer = jQueryTemp.iUtil.getPointer( e );
        jQueryTemp.iResize.sizes = { width: parseInt( jQueryTemp( this.resizeElement ).css( 'width' ) ) || 0, height: parseInt( jQueryTemp( this.resizeElement ).css( 'height' ) ) || 0 };
        jQueryTemp.iResize.position = { top: parseInt( jQueryTemp( this.resizeElement ).css( 'top' ) ) || 0, left: parseInt( jQueryTemp( this.resizeElement ).css( 'left' ) ) || 0 };
        if ( jQueryTemp.iResize.resizeElement.resizeOptions.onStart ) {
            jQueryTemp.iResize.resizeElement.resizeOptions.onStart.apply( jQueryTemp.iResize.resizeElement, [ this ] )
        }
        return false
    }, stop: function() {
        jQueryTemp( document ).unbind( 'mousemove', jQueryTemp.iResize.move ).unbind( 'mouseup', jQueryTemp.iResize.stop );
        if ( jQueryTemp.iResize.resizeElement.resizeOptions.onStop ) {
            jQueryTemp.iResize.resizeElement.resizeOptions.onStop.apply( jQueryTemp.iResize.resizeElement, [ jQueryTemp.iResize.resizeDirection ] )
        }
        jQueryTemp.iResize.resizeElement = null;
        jQueryTemp.iResize.resizeDirection = null
    }, getWidth: function( dx, side ) {
        return Math.min( Math.max( jQueryTemp.iResize.sizes.width + dx * side, jQueryTemp.iResize.resizeElement.resizeOptions.minWidth ), jQueryTemp.iResize.resizeElement.resizeOptions.maxWidth )
    }, getHeight: function( dy, side ) {
        return Math.min( Math.max( jQueryTemp.iResize.sizes.height + dy * side, jQueryTemp.iResize.resizeElement.resizeOptions.minHeight ), jQueryTemp.iResize.resizeElement.resizeOptions.maxHeight )
    }, getHeightMinMax: function( height ) {
        return Math.min( Math.max( height, jQueryTemp.iResize.resizeElement.resizeOptions.minHeight ), jQueryTemp.iResize.resizeElement.resizeOptions.maxHeight )
    }, move: function( e ) {
        if ( jQueryTemp.iResize.resizeElement == null ) {
            return
        }
        pointer = jQueryTemp.iUtil.getPointer( e );
        dx = pointer.x - jQueryTemp.iResize.pointer.x;
        dy = pointer.y - jQueryTemp.iResize.pointer.y;
        newSizes = { width: jQueryTemp.iResize.sizes.width, height: jQueryTemp.iResize.sizes.height };
        newPosition = { top: jQueryTemp.iResize.position.top, left: jQueryTemp.iResize.position.left };
        switch ( jQueryTemp.iResize.resizeDirection ) {
            case'e':
                newSizes.width = jQueryTemp.iResize.getWidth( dx, 1 );
                break;
            case'se':
                newSizes.width = jQueryTemp.iResize.getWidth( dx, 1 );
                newSizes.height = jQueryTemp.iResize.getHeight( dy, 1 );
                break;
            case'w':
                newSizes.width = jQueryTemp.iResize.getWidth( dx, -1 );
                newPosition.left = jQueryTemp.iResize.position.left - newSizes.width + jQueryTemp.iResize.sizes.width;
                break;
            case'sw':
                newSizes.width = jQueryTemp.iResize.getWidth( dx, -1 );
                newPosition.left = jQueryTemp.iResize.position.left - newSizes.width + jQueryTemp.iResize.sizes.width;
                newSizes.height = jQueryTemp.iResize.getHeight( dy, 1 );
                break;
            case'nw':
                newSizes.height = jQueryTemp.iResize.getHeight( dy, -1 );
                newPosition.top = jQueryTemp.iResize.position.top - newSizes.height + jQueryTemp.iResize.sizes.height;
                newSizes.width = jQueryTemp.iResize.getWidth( dx, -1 );
                newPosition.left = jQueryTemp.iResize.position.left - newSizes.width + jQueryTemp.iResize.sizes.width;
                break;
            case'n':
                newSizes.height = jQueryTemp.iResize.getHeight( dy, -1 );
                newPosition.top = jQueryTemp.iResize.position.top - newSizes.height + jQueryTemp.iResize.sizes.height;
                break;
            case'ne':
                newSizes.height = jQueryTemp.iResize.getHeight( dy, -1 );
                newPosition.top = jQueryTemp.iResize.position.top - newSizes.height + jQueryTemp.iResize.sizes.height;
                newSizes.width = jQueryTemp.iResize.getWidth( dx, 1 );
                break;
            case's':
                newSizes.height = jQueryTemp.iResize.getHeight( dy, 1 );
                break
        }
        if ( jQueryTemp.iResize.resizeElement.resizeOptions.ratio ) {
            if ( jQueryTemp.iResize.resizeDirection == 'n' || jQueryTemp.iResize.resizeDirection == 's' )
                nWidth = newSizes.height * jQueryTemp.iResize.resizeElement.resizeOptions.ratio;
            else
                nWidth = newSizes.width;
            nHeight = jQueryTemp.iResize.getHeightMinMax( nWidth * jQueryTemp.iResize.resizeElement.resizeOptions.ratio );
            nWidth = nHeight / jQueryTemp.iResize.resizeElement.resizeOptions.ratio;
            switch ( jQueryTemp.iResize.resizeDirection ) {
                case'n':
                case'nw':
                case'ne':
                    newPosition.top += newSizes.height - nHeight;
                    break
            }
            switch ( jQueryTemp.iResize.resizeDirection ) {
                case'nw':
                case'w':
                case'sw':
                    newPosition.left += newSizes.width - nWidth;
                    break
            }
            newSizes.height = nHeight;
            newSizes.width = nWidth
        }
        if ( newPosition.top < jQueryTemp.iResize.resizeElement.resizeOptions.minTop ) {
            nHeight = newSizes.height + newPosition.top - jQueryTemp.iResize.resizeElement.resizeOptions.minTop;
            newPosition.top = jQueryTemp.iResize.resizeElement.resizeOptions.minTop;
            if ( jQueryTemp.iResize.resizeElement.resizeOptions.ratio ) {
                nWidth = nHeight / jQueryTemp.iResize.resizeElement.resizeOptions.ratio;
                switch ( jQueryTemp.iResize.resizeDirection ) {
                    case'nw':
                    case'w':
                    case'sw':
                        newPosition.left += newSizes.width - nWidth;
                        break
                }
                newSizes.width = nWidth
            }
            newSizes.height = nHeight
        }
        if ( newPosition.left < jQueryTemp.iResize.resizeElement.resizeOptions.minLeft ) {
            nWidth = newSizes.width + newPosition.left - jQueryTemp.iResize.resizeElement.resizeOptions.minLeft;
            newPosition.left = jQueryTemp.iResize.resizeElement.resizeOptions.minLeft;
            if ( jQueryTemp.iResize.resizeElement.resizeOptions.ratio ) {
                nHeight = nWidth * jQueryTemp.iResize.resizeElement.resizeOptions.ratio;
                switch ( jQueryTemp.iResize.resizeDirection ) {
                    case'n':
                    case'nw':
                    case'ne':
                        newPosition.top += newSizes.height - nHeight;
                        break
                }
                newSizes.height = nHeight
            }
            newSizes.width = nWidth
        }
        if ( newPosition.top + newSizes.height > jQueryTemp.iResize.resizeElement.resizeOptions.maxBottom ) {
            newSizes.height = jQueryTemp.iResize.resizeElement.resizeOptions.maxBottom - newPosition.top;
            if ( jQueryTemp.iResize.resizeElement.resizeOptions.ratio ) {
                newSizes.width = newSizes.height / jQueryTemp.iResize.resizeElement.resizeOptions.ratio
            }
        }
        if ( newPosition.left + newSizes.width > jQueryTemp.iResize.resizeElement.resizeOptions.maxRight ) {
            newSizes.width = jQueryTemp.iResize.resizeElement.resizeOptions.maxRight - newPosition.left;
            if ( jQueryTemp.iResize.resizeElement.resizeOptions.ratio ) {
                newSizes.height = newSizes.width * jQueryTemp.iResize.resizeElement.resizeOptions.ratio
            }
        }
        var newDimensions = false;
        if ( jQueryTemp.iResize.resizeElement.resizeOptions.onResize ) {
            newDimensions = jQueryTemp.iResize.resizeElement.resizeOptions.onResize.apply( jQueryTemp.iResize.resizeElement, [ newSizes, newPosition ] );
            if ( newDimensions ) {
                if ( newDimensions.sizes ) {
                    jQueryTemp.extend( newSizes, newDimensions.sizes )
                }
                if ( newDimensions.position ) {
                    jQueryTemp.extend( newPosition, newDimensions.position )
                }
            }
        }
        elS = jQueryTemp.iResize.resizeElement.style;
        elS.left = newPosition.left + 'px';
        elS.top = newPosition.top + 'px';
        elS.width = newSizes.width + 'px';
        elS.height = newSizes.height + 'px';
        return false
    }, build: function( options ) {
        if ( !options || !options.handlers || options.handlers.constructor != Object ) {
            return
        }
        return this.each( function() {
            var el = this;
            el.resizeOptions = options;
            el.resizeOptions.minWidth = options.minWidth || 10;
            el.resizeOptions.minHeight = options.minHeight || 10;
            el.resizeOptions.maxWidth = options.maxWidth || 3000;
            el.resizeOptions.maxHeight = options.maxHeight || 3000;
            el.resizeOptions.minTop = options.minTop || -1000;
            el.resizeOptions.minLeft = options.minLeft || -1000;
            el.resizeOptions.maxRight = options.maxRight || 3000;
            el.resizeOptions.maxBottom = options.maxBottom || 3000;
            elPosition = jQueryTemp( el ).css( 'position' );
            if ( !( elPosition == 'relative' || elPosition == 'absolute' ) ) {
                el.style.position = 'relative'
            }
            directions = /n|ne|e|se|s|sw|w|nw/g;
            for ( i in el.resizeOptions.handlers ) {
                if ( i.toLowerCase().match( directions ) != null ) {
                    if ( el.resizeOptions.handlers[i].constructor == String ) {
                        handle = jQueryTemp( el.resizeOptions.handlers[i] );
                        if ( handle.size() > 0 ) {
                            el.resizeOptions.handlers[i] = handle.get( 0 )
                        }
                    }
                    if ( el.resizeOptions.handlers[i].tagName ) {
                        el.resizeOptions.handlers[i].resizeElement = el;
                        el.resizeOptions.handlers[i].resizeDirection = i;
                        jQueryTemp( el.resizeOptions.handlers[i] ).bind( 'mousedown', jQueryTemp.iResize.start )
                    }
                }
            }
            if ( el.resizeOptions.dragHandle ) {
                if ( typeof el.resizeOptions.dragHandle === 'string' ) {
                    handleEl = jQueryTemp( el.resizeOptions.dragHandle );
                    if ( handleEl.size() > 0 ) {
                        handleEl.each( function() {
                            this.dragEl = el
                        } );
                        handleEl.bind( 'mousedown', jQueryTemp.iResize.startDrag )
                    }
                } else if ( el.resizeOptions.dragHandle == true ) {
                    jQueryTemp( this ).bind( 'mousedown', jQueryTemp.iResize.startDrag )
                }
            }
        } )
    }, destroy: function() {
        return this.each( function() {
            var el = this;
            for ( i in el.resizeOptions.handlers ) {
                el.resizeOptions.handlers[i].resizeElement = null;
                el.resizeOptions.handlers[i].resizeDirection = null;
                jQueryTemp( el.resizeOptions.handlers[i] ).unbind( 'mousedown', jQueryTemp.iResize.start )
            }
            if ( el.resizeOptions.dragHandle ) {
                if ( typeof el.resizeOptions.dragHandle === 'string' ) {
                    handle = jQueryTemp( el.resizeOptions.dragHandle );
                    if ( handle.size() > 0 ) {
                        handle.unbind( 'mousedown', jQueryTemp.iResize.startDrag )
                    }
                } else if ( el.resizeOptions.dragHandle == true ) {
                    jQueryTemp( this ).unbind( 'mousedown', jQueryTemp.iResize.startDrag )
                }
            }
            el.resizeOptions = null
        } )
    } };
jQueryTemp.fn.extend( { Resizable: jQueryTemp.iResize.build, ResizableDestroy: jQueryTemp.iResize.destroy } );
jQueryTemp.selectHelper = null;
jQueryTemp.selectKeyHelper = false;
jQueryTemp.selectdrug = null;
jQueryTemp.selectCurrent = [ ];
jQueryTemp.selectKeyDown = function( e ) {
    var pressedKey = e.charCode || e.keyCode || -1;
    if ( pressedKey == 17 || pressedKey == 16 ) {
        jQueryTemp.selectKeyHelper = true
    }
};
jQueryTemp.selectKeyUp = function( e ) {
    jQueryTemp.selectKeyHelper = false
};
jQueryTemp.selectstart = function( e ) {
    this.f.pointer = jQueryTemp.iUtil.getPointer( e );
    this.f.pos = jQueryTemp.extend( jQueryTemp.iUtil.getPosition( this ), jQueryTemp.iUtil.getSize( this ) );
    this.f.scr = jQueryTemp.iUtil.getScroll( this );
    this.f.pointer.x -= this.f.pos.x;
    this.f.pointer.y -= this.f.pos.y;
    jQueryTemp( this ).append( jQueryTemp.selectHelper.get( 0 ) );
    if ( this.f.hc )
        jQueryTemp.selectHelper.addClass( this.f.hc ).css( 'display', 'block' );
    jQueryTemp.selectHelper.css( { display: 'block', width: '0px', height: '0px' } );
    if ( this.f.o ) {
        jQueryTemp.selectHelper.css( 'opacity', this.f.o )
    }
    jQueryTemp.selectdrug = this;
    jQueryTemp.selectedone = false;
    jQueryTemp.selectCurrent = [ ];
    this.f.el.each( function() {
        this.pos = { x: this.offsetLeft + ( this.currentStyle && !jQueryTemp.browser.opera ? parseInt( this.currentStyle.borderLeftWidth ) || 0 : 0 ) + ( jQueryTemp.selectdrug.scrollLeft || 0 ), y: this.offsetTop + ( this.currentStyle && !jQueryTemp.browser.opera ? parseInt( this.currentStyle.borderTopWidth ) || 0 : 0 ) + ( jQueryTemp.selectdrug.scrollTop || 0 ), wb: this.offsetWidth, hb: this.offsetHeight };
        if ( this.s == true ) {
            if ( jQueryTemp.selectKeyHelper == false ) {
                this.s = false;
                jQueryTemp( this ).removeClass( jQueryTemp.selectdrug.f.sc )
            } else {
                jQueryTemp.selectedone = true;
                jQueryTemp.selectCurrent[jQueryTemp.selectCurrent.length] = jQueryTemp.attr( this, 'id' )
            }
        }
    } );
    jQueryTemp.selectcheck.apply( this, [ e ] );
    jQueryTemp( document ).bind( 'mousemove', jQueryTemp.selectcheck ).bind( 'mouseup', jQueryTemp.selectstop );
    return false
};
jQueryTemp.selectcheck = function( e ) {
    if ( !jQueryTemp.selectdrug )
        return;
    jQueryTemp.selectcheckApply.apply( jQueryTemp.selectdrug, [ e ] )
};
jQueryTemp.selectcheckApply = function( e ) {
    if ( !jQueryTemp.selectdrug )
        return;
    var pointer = jQueryTemp.iUtil.getPointer( e );
    var scr = jQueryTemp.iUtil.getScroll( jQueryTemp.selectdrug );
    pointer.x += scr.l - this.f.scr.l - this.f.pos.x;
    pointer.y += scr.t - this.f.scr.t - this.f.pos.y;
    var sx = Math.min( pointer.x, this.f.pointer.x );
    var sw = Math.min( Math.abs( pointer.x - this.f.pointer.x ), Math.abs( this.f.scr.w - sx ) );
    var sy = Math.min( pointer.y, this.f.pointer.y );
    var sh = Math.min( Math.abs( pointer.y - this.f.pointer.y ), Math.abs( this.f.scr.h - sy ) );
    if ( this.scrollTop > 0 && pointer.y - 20 < this.scrollTop ) {
        var diff = Math.min( scr.t, 10 );
        sy -= diff;
        sh += diff;
        this.scrollTop -= diff
    } else if ( this.scrollTop + this.f.pos.h < this.f.scr.h && pointer.y + 20 > this.scrollTop + this.f.pos.h ) {
        var diff = Math.min( this.f.scr.h - this.scrollTop, 10 );
        this.scrollTop += diff;
        if ( this.scrollTop != scr.t )
            sh += diff
    }
    if ( this.scrollLeft > 0 && pointer.x - 20 < this.scrollLeft ) {
        var diff = Math.min( scr.l, 10 );
        sx -= diff;
        sw += diff;
        this.scrollLeft -= diff
    } else if ( this.scrollLeft + this.f.pos.w < this.f.scr.w && pointer.x + 20 > this.scrollLeft + this.f.pos.w ) {
        var diff = Math.min( this.f.scr.w - this.scrollLeft, 10 );
        this.scrollLeft += diff;
        if ( this.scrollLeft != scr.l )
            sw += diff
    }
    jQueryTemp.selectHelper.css( { left: sx + 'px', top: sy + 'px', width: sw + 'px', height: sh + 'px' } );
    jQueryTemp.selectHelper.l = sx + this.f.scr.l;
    jQueryTemp.selectHelper.t = sy + this.f.scr.t;
    jQueryTemp.selectHelper.r = jQueryTemp.selectHelper.l + sw;
    jQueryTemp.selectHelper.b = jQueryTemp.selectHelper.t + sh;
    jQueryTemp.selectedone = false;
    this.f.el.each( function() {
        iIndex = jQueryTemp.selectCurrent.indexOf( jQueryTemp.attr( this, 'id' ) );
        if ( !( this.pos.x > jQueryTemp.selectHelper.r || ( this.pos.x + this.pos.wb ) < jQueryTemp.selectHelper.l || this.pos.y > jQueryTemp.selectHelper.b || ( this.pos.y + this.pos.hb ) < jQueryTemp.selectHelper.t ) ) {
            jQueryTemp.selectedone = true;
            if ( this.s != true ) {
                this.s = true;
                jQueryTemp( this ).addClass( jQueryTemp.selectdrug.f.sc )
            }
            if ( iIndex != -1 ) {
                this.s = false;
                jQueryTemp( this ).removeClass( jQueryTemp.selectdrug.f.sc )
            }
        } else if ( ( this.s == true ) && ( iIndex == -1 ) ) {
            this.s = false;
            jQueryTemp( this ).removeClass( jQueryTemp.selectdrug.f.sc )
        } else if ( ( !this.s ) && ( jQueryTemp.selectKeyHelper == true ) && ( iIndex != -1 ) ) {
            this.s = true;
            jQueryTemp( this ).addClass( jQueryTemp.selectdrug.f.sc )
        }
    } );
    return false
};
jQueryTemp.selectstop = function( e ) {
    if ( !jQueryTemp.selectdrug )
        return;
    jQueryTemp.selectstopApply.apply( jQueryTemp.selectdrug, [ e ] )
};
jQueryTemp.selectstopApply = function( e ) {
    jQueryTemp( document ).unbind( 'mousemove', jQueryTemp.selectcheck ).unbind( 'mouseup', jQueryTemp.selectstop );
    if ( !jQueryTemp.selectdrug )
        return;
    jQueryTemp.selectHelper.css( 'display', 'none' );
    if ( this.f.hc )
        jQueryTemp.selectHelper.removeClass( this.f.hc );
    jQueryTemp.selectdrug = false;
    jQueryTemp( 'body' ).append( jQueryTemp.selectHelper.get( 0 ) );
    if ( jQueryTemp.selectedone == true ) {
        if ( this.f.onselect )
            this.f.onselect( jQueryTemp.Selectserialize( jQueryTemp.attr( this, 'id' ) ) )
    } else {
        if ( this.f.onselectstop )
            this.f.onselectstop( jQueryTemp.Selectserialize( jQueryTemp.attr( this, 'id' ) ) )
    }
    jQueryTemp.selectCurrent = [ ]
};
jQueryTemp.Selectserialize = function( s ) {
    var h = '';
    var o = [ ];
    if ( a = jQueryTemp( '#' + s ) ) {
        a.get( 0 ).f.el.each( function() {
            if ( this.s == true ) {
                if ( h.length > 0 ) {
                    h += '&'
                }
                h += s + '[]=' + jQueryTemp.attr( this, 'id' );
                o[o.length] = jQueryTemp.attr( this, 'id' )
            }
        } )
    }
    return{ hash: h, o: o }
};
jQueryTemp.fn.Selectable = function( o ) {
    if ( !jQueryTemp.selectHelper ) {
        jQueryTemp( 'body', document ).append( '<div id="selectHelper"></div>' ).bind( 'keydown', jQueryTemp.selectKeyDown ).bind( 'keyup', jQueryTemp.selectKeyUp );
        jQueryTemp.selectHelper = jQueryTemp( '#selectHelper' );
        jQueryTemp.selectHelper.css( { position: 'absolute', display: 'none' } );
        if ( window.event ) {
            jQueryTemp( 'body', document ).bind( 'keydown', jQueryTemp.selectKeyDown ).bind( 'keyup', jQueryTemp.selectKeyUp )
        } else {
            jQueryTemp( document ).bind( 'keydown', jQueryTemp.selectKeyDown ).bind( 'keyup', jQueryTemp.selectKeyUp )
        }
    }
    if ( !o ) {
        o = { }
    }
    return this.each( function() {
        if ( this.isSelectable )
            return;
        this.isSelectable = true;
        this.f = { a: o.accept, o: o.opacity ? parseFloat( o.opacity ) : false, sc: o.selectedclass ? o.selectedclass : false, hc: o.helperclass ? o.helperclass : false, onselect: o.onselect ? o.onselect : false, onselectstop: o.onselectstop ? o.onselectstop : false };
        this.f.el = jQueryTemp( '.' + o.accept );
        jQueryTemp( this ).bind( 'mousedown', jQueryTemp.selectstart ).css( 'position', 'relative' )
    } )
};
jQueryTemp.iSlider = { tabindex: 1, set: function( values ) {
        var values = values;
        return this.each( function() {
            this.slideCfg.sliders.each( function( key ) {
                jQueryTemp.iSlider.dragmoveBy( this, values[key] )
            } )
        } )
    }, get: function() {
        var values = [ ];
        this.each( function( slider ) {
            if ( this.isSlider ) {
                values[slider] = [ ];
                var elm = this;
                var sizes = jQueryTemp.iUtil.getSize( this );
                this.slideCfg.sliders.each( function( key ) {
                    var x = this.offsetLeft;
                    var y = this.offsetTop;
                    xproc = parseInt( x * 100 / ( sizes.w - this.offsetWidth ) );
                    yproc = parseInt( y * 100 / ( sizes.h - this.offsetHeight ) );
                    values[slider][key] = [ xproc || 0, yproc || 0, x || 0, y || 0 ]
                } )
            }
        } );
        return values
    }, modifyContainer: function( elm ) {
        elm.dragCfg.containerMaxx = elm.dragCfg.cont.w - elm.dragCfg.oC.wb;
        elm.dragCfg.containerMaxy = elm.dragCfg.cont.h - elm.dragCfg.oC.hb;
        if ( elm.SliderContainer.slideCfg.restricted ) {
            next = elm.SliderContainer.slideCfg.sliders.get( elm.SliderIteration + 1 );
            if ( next ) {
                elm.dragCfg.cont.w = ( parseInt( jQueryTemp( next ).css( 'left' ) ) || 0 ) + elm.dragCfg.oC.wb;
                elm.dragCfg.cont.h = ( parseInt( jQueryTemp( next ).css( 'top' ) ) || 0 ) + elm.dragCfg.oC.hb
            }
            prev = elm.SliderContainer.slideCfg.sliders.get( elm.SliderIteration - 1 );
            if ( prev ) {
                var prevLeft = parseInt( jQueryTemp( prev ).css( 'left' ) ) || 0;
                var prevTop = parseInt( jQueryTemp( prev ).css( 'left' ) ) || 0;
                elm.dragCfg.cont.x += prevLeft;
                elm.dragCfg.cont.y += prevTop;
                elm.dragCfg.cont.w -= prevLeft;
                elm.dragCfg.cont.h -= prevTop
            }
        }
        elm.dragCfg.maxx = elm.dragCfg.cont.w - elm.dragCfg.oC.wb;
        elm.dragCfg.maxy = elm.dragCfg.cont.h - elm.dragCfg.oC.hb;
        if ( elm.dragCfg.fractions ) {
            elm.dragCfg.gx = ( ( elm.dragCfg.cont.w - elm.dragCfg.oC.wb ) / elm.dragCfg.fractions ) || 1;
            elm.dragCfg.gy = ( ( elm.dragCfg.cont.h - elm.dragCfg.oC.hb ) / elm.dragCfg.fractions ) || 1;
            elm.dragCfg.fracW = elm.dragCfg.maxx / elm.dragCfg.fractions;
            elm.dragCfg.fracH = elm.dragCfg.maxy / elm.dragCfg.fractions
        }
        elm.dragCfg.cont.dx = elm.dragCfg.cont.x - elm.dragCfg.oR.x;
        elm.dragCfg.cont.dy = elm.dragCfg.cont.y - elm.dragCfg.oR.y;
        jQueryTemp.iDrag.helper.css( 'cursor', 'default' )
    }, onSlide: function( elm, x, y ) {
        if ( elm.dragCfg.fractions ) {
            xfrac = parseInt( x / elm.dragCfg.fracW );
            xproc = xfrac * 100 / elm.dragCfg.fractions;
            yfrac = parseInt( y / elm.dragCfg.fracH );
            yproc = yfrac * 100 / elm.dragCfg.fractions
        } else {
            xproc = parseInt( x * 100 / elm.dragCfg.containerMaxx );
            yproc = parseInt( y * 100 / elm.dragCfg.containerMaxy )
        }
        elm.dragCfg.lastSi = [ xproc || 0, yproc || 0, x || 0, y || 0 ];
        if ( elm.dragCfg.onSlide )
            elm.dragCfg.onSlide.apply( elm, elm.dragCfg.lastSi )
    }, dragmoveByKey: function( event ) {
        pressedKey = event.charCode || event.keyCode || -1;
        switch ( pressedKey ) {
            case 35:
                jQueryTemp.iSlider.dragmoveBy( this.dragElem, [ 2000, 2000 ] );
                break;
            case 36:
                jQueryTemp.iSlider.dragmoveBy( this.dragElem, [ -2000, -2000 ] );
                break;
            case 37:
                jQueryTemp.iSlider.dragmoveBy( this.dragElem, [ -this.dragElem.dragCfg.gx || -1, 0 ] );
                break;
            case 38:
                jQueryTemp.iSlider.dragmoveBy( this.dragElem, [ 0, -this.dragElem.dragCfg.gy || -1 ] );
                break;
            case 39:
                jQueryTemp.iSlider.dragmoveBy( this.dragElem, [ this.dragElem.dragCfg.gx || 1, 0 ] );
                break;
            case 40:
                jQueryTemp.iDrag.dragmoveBy( this.dragElem, [ 0, this.dragElem.dragCfg.gy || 1 ] );
                break
            }
    }, dragmoveBy: function( elm, position ) {
        if ( !elm.dragCfg ) {
            return
        }
        elm.dragCfg.oC = jQueryTemp.extend( jQueryTemp.iUtil.getPosition( elm ), jQueryTemp.iUtil.getSize( elm ) );
        elm.dragCfg.oR = { x: parseInt( jQueryTemp.css( elm, 'left' ) ) || 0, y: parseInt( jQueryTemp.css( elm, 'top' ) ) || 0 };
        elm.dragCfg.oP = jQueryTemp.css( elm, 'position' );
        if ( elm.dragCfg.oP != 'relative' && elm.dragCfg.oP != 'absolute' ) {
            elm.style.position = 'relative'
        }
        jQueryTemp.iDrag.getContainment( elm );
        jQueryTemp.iSlider.modifyContainer( elm );
        dx = parseInt( position[0] ) || 0;
        dy = parseInt( position[1] ) || 0;
        nx = elm.dragCfg.oR.x + dx;
        ny = elm.dragCfg.oR.y + dy;
        if ( elm.dragCfg.fractions ) {
            newCoords = jQueryTemp.iDrag.snapToGrid.apply( elm, [ nx, ny, dx, dy ] );
            if ( newCoords.constructor == Object ) {
                dx = newCoords.dx;
                dy = newCoords.dy
            }
            nx = elm.dragCfg.oR.x + dx;
            ny = elm.dragCfg.oR.y + dy
        }
        newCoords = jQueryTemp.iDrag.fitToContainer.apply( elm, [ nx, ny, dx, dy ] );
        if ( newCoords && newCoords.constructor == Object ) {
            dx = newCoords.dx;
            dy = newCoords.dy
        }
        nx = elm.dragCfg.oR.x + dx;
        ny = elm.dragCfg.oR.y + dy;
        if ( elm.dragCfg.si && ( elm.dragCfg.onSlide || elm.dragCfg.onChange ) ) {
            jQueryTemp.iSlider.onSlide( elm, nx, ny )
        }
        nx = !elm.dragCfg.axis || elm.dragCfg.axis == 'horizontally' ? nx : elm.dragCfg.oR.x || 0;
        ny = !elm.dragCfg.axis || elm.dragCfg.axis == 'vertically' ? ny : elm.dragCfg.oR.y || 0;
        elm.style.left = nx + 'px';
        elm.style.top = ny + 'px'
    }, build: function( o ) {
        return this.each( function() {
            if ( this.isSlider == true || !o.accept || !jQueryTemp.iUtil || !jQueryTemp.iDrag || !jQueryTemp.iDrop ) {
                return
            }
            toDrag = jQueryTemp( o.accept, this );
            if ( toDrag.size() == 0 ) {
                return
            }
            var params = { containment: 'parent', si: true, onSlide: o.onSlide && o.onSlide.constructor == Function ? o.onSlide : null, onChange: o.onChange && o.onChange.constructor == Function ? o.onChange : null, handle: this, opacity: o.opacity || false };
            if ( o.fractions && parseInt( o.fractions ) ) {
                params.fractions = parseInt( o.fractions ) || 1;
                params.fractions = params.fractions > 0 ? params.fractions : 1
            }
            if ( toDrag.size() == 1 )
                toDrag.Draggable( params );
            else {
                jQueryTemp( toDrag.get( 0 ) ).Draggable( params );
                params.handle = null;
                toDrag.Draggable( params )
            }
            toDrag.keydown( jQueryTemp.iSlider.dragmoveByKey );
            toDrag.attr( 'tabindex', jQueryTemp.iSlider.tabindex++ );
            this.isSlider = true;
            this.slideCfg = { };
            this.slideCfg.onslide = params.onslide;
            this.slideCfg.fractions = params.fractions;
            this.slideCfg.sliders = toDrag;
            this.slideCfg.restricted = o.restricted ? true : false;
            sliderEl = this;
            sliderEl.slideCfg.sliders.each( function( nr ) {
                this.SliderIteration = nr;
                this.SliderContainer = sliderEl
            } );
            if ( o.values && o.values.constructor == Array ) {
                for ( i = o.values.length - 1; i >= 0; i-- ) {
                    if ( o.values[i].constructor == Array && o.values[i].length == 2 ) {
                        el = this.slideCfg.sliders.get( i );
                        if ( el.tagName ) {
                            jQueryTemp.iSlider.dragmoveBy( el, o.values[i] )
                        }
                    }
                }
            }
        } )
    } };
jQueryTemp.fn.extend( { Slider: jQueryTemp.iSlider.build, SliderSetValues: jQueryTemp.iSlider.set, SliderGetValues: jQueryTemp.iSlider.get } );
jQueryTemp.islideshow = { slideshows: [ ], gonext: function() {
        this.blur();
        slideshow = this.parentNode;
        id = jQueryTemp.attr( slideshow, 'id' );
        if ( jQueryTemp.islideshow.slideshows[id] != null ) {
            window.clearInterval( jQueryTemp.islideshow.slideshows[id] )
        }
        slide = slideshow.ss.currentslide + 1;
        if ( slideshow.ss.images.length < slide ) {
            slide = 1
        }
        images = jQueryTemp( 'img', slideshow.ss.holder );
        slideshow.ss.currentslide = slide;
        if ( images.size() > 0 ) {
            images.fadeOut( slideshow.ss.fadeDuration, jQueryTemp.islideshow.showImage )
        }
    }, goprev: function() {
        this.blur();
        slideshow = this.parentNode;
        id = jQueryTemp.attr( slideshow, 'id' );
        if ( jQueryTemp.islideshow.slideshows[id] != null ) {
            window.clearInterval( jQueryTemp.islideshow.slideshows[id] )
        }
        slide = slideshow.ss.currentslide - 1;
        images = jQueryTemp( 'img', slideshow.ss.holder );
        if ( slide < 1 ) {
            slide = slideshow.ss.images.length
        }
        slideshow.ss.currentslide = slide;
        if ( images.size() > 0 ) {
            images.fadeOut( slideshow.ss.fadeDuration, jQueryTemp.islideshow.showImage )
        }
    }, timer: function( c ) {
        slideshow = document.getElementById( c );
        if ( slideshow.ss.random ) {
            slide = slideshow.ss.currentslide;
            while ( slide == slideshow.ss.currentslide ) {
                slide = 1 + parseInt( Math.random() * slideshow.ss.images.length )
            }
        } else {
            slide = slideshow.ss.currentslide + 1;
            if ( slideshow.ss.images.length < slide ) {
                slide = 1
            }
        }
        images = jQueryTemp( 'img', slideshow.ss.holder );
        slideshow.ss.currentslide = slide;
        if ( images.size() > 0 ) {
            images.fadeOut( slideshow.ss.fadeDuration, jQueryTemp.islideshow.showImage )
        }
    }, go: function( o ) {
        var slideshow;
        if ( o && o.constructor == Object ) {
            if ( o.loader ) {
                slideshow = document.getElementById( o.loader.slideshow );
                url = window.location.href.split( "#" );
                o.loader.onload = null;
                if ( url.length == 2 ) {
                    slide = parseInt( url[1] );
                    show = url[1].replace( slide, '' );
                    if ( jQueryTemp.attr( slideshow, 'id' ) != show ) {
                        slide = 1
                    }
                } else {
                    slide = 1
                }
            }
            if ( o.link ) {
                o.link.blur();
                slideshow = o.link.parentNode.parentNode;
                id = jQueryTemp.attr( slideshow, 'id' );
                if ( jQueryTemp.islideshow.slideshows[id] != null ) {
                    window.clearInterval( jQueryTemp.islideshow.slideshows[id] )
                }
                url = o.link.href.split( "#" );
                slide = parseInt( url[1] );
                show = url[1].replace( slide, '' );
                if ( jQueryTemp.attr( slideshow, 'id' ) != show ) {
                    slide = 1
                }
            }
            if ( slideshow.ss.images.length < slide || slide < 1 ) {
                slide = 1
            }
            slideshow.ss.currentslide = slide;
            slidePos = jQueryTemp.iUtil.getSize( slideshow );
            slidePad = jQueryTemp.iUtil.getPadding( slideshow );
            slideBor = jQueryTemp.iUtil.getBorder( slideshow );
            if ( slideshow.ss.prevslide ) {
                slideshow.ss.prevslide.o.css( 'display', 'none' )
            }
            if ( slideshow.ss.nextslide ) {
                slideshow.ss.nextslide.o.css( 'display', 'none' )
            }
            if ( slideshow.ss.loader ) {
                y = parseInt( slidePad.t ) + parseInt( slideBor.t );
                if ( slideshow.ss.slideslinks ) {
                    if ( slideshow.ss.slideslinks.linksPosition == 'top' ) {
                        y += slideshow.ss.slideslinks.dimm.hb
                    } else {
                        slidePos.h -= slideshow.ss.slideslinks.dimm.hb
                    }
                }
                if ( slideshow.ss.slideCaption ) {
                    if ( slideshow.ss.slideCaption && slideshow.ss.slideCaption.captionPosition == 'top' ) {
                        y += slideshow.ss.slideCaption.dimm.hb
                    } else {
                        slidePos.h -= slideshow.ss.slideCaption.dimm.hb
                    }
                }
                if ( !slideshow.ss.loaderWidth ) {
                    slideshow.ss.loaderHeight = o.loader ? o.loader.height : ( parseInt( slideshow.ss.loader.css( 'height' ) ) || 0 );
                    slideshow.ss.loaderWidth = o.loader ? o.loader.width : ( parseInt( slideshow.ss.loader.css( 'width' ) ) || 0 )
                }
                slideshow.ss.loader.css( 'top', y + ( slidePos.h - slideshow.ss.loaderHeight ) / 2 + 'px' );
                slideshow.ss.loader.css( 'left', ( slidePos.wb - slideshow.ss.loaderWidth ) / 2 + 'px' );
                slideshow.ss.loader.css( 'display', 'block' )
            }
            images = jQueryTemp( 'img', slideshow.ss.holder );
            if ( images.size() > 0 ) {
                images.fadeOut( slideshow.ss.fadeDuration, jQueryTemp.islideshow.showImage )
            } else {
                lnk = jQueryTemp( 'a', slideshow.ss.slideslinks.o ).get( slide - 1 );
                jQueryTemp( lnk ).addClass( slideshow.ss.slideslinks.activeLinkClass );
                var img = new Image();
                img.slideshow = jQueryTemp.attr( slideshow, 'id' );
                img.slide = slide - 1;
                img.src = slideshow.ss.images[slideshow.ss.currentslide - 1].src;
                if ( img.complete ) {
                    img.onload = null;
                    jQueryTemp.islideshow.display.apply( img )
                } else {
                    img.onload = jQueryTemp.islideshow.display
                }
                if ( slideshow.ss.slideCaption ) {
                    slideshow.ss.slideCaption.o.html( slideshow.ss.images[slide - 1].caption )
                }
            }
        }
    }, showImage: function() {
        slideshow = this.parentNode.parentNode;
        slideshow.ss.holder.css( 'display', 'none' );
        if ( slideshow.ss.slideslinks.activeLinkClass ) {
            lnk = jQueryTemp( 'a', slideshow.ss.slideslinks.o ).removeClass( slideshow.ss.slideslinks.activeLinkClass ).get( slideshow.ss.currentslide - 1 );
            jQueryTemp( lnk ).addClass( slideshow.ss.slideslinks.activeLinkClass )
        }
        var img = new Image();
        img.slideshow = jQueryTemp.attr( slideshow, 'id' );
        img.slide = slideshow.ss.currentslide - 1;
        img.src = slideshow.ss.images[slideshow.ss.currentslide - 1].src;
        if ( img.complete ) {
            img.onload = null;
            jQueryTemp.islideshow.display.apply( img )
        } else {
            img.onload = jQueryTemp.islideshow.display
        }
        if ( slideshow.ss.slideCaption ) {
            slideshow.ss.slideCaption.o.html( slideshow.ss.images[slideshow.ss.currentslide - 1].caption )
        }
    }, display: function() {
        slideshow = document.getElementById( this.slideshow );
        if ( slideshow.ss.prevslide ) {
            slideshow.ss.prevslide.o.css( 'display', 'none' )
        }
        if ( slideshow.ss.nextslide ) {
            slideshow.ss.nextslide.o.css( 'display', 'none' )
        }
        slidePos = jQueryTemp.iUtil.getSize( slideshow );
        y = 0;
        if ( slideshow.ss.slideslinks ) {
            if ( slideshow.ss.slideslinks.linksPosition == 'top' ) {
                y += slideshow.ss.slideslinks.dimm.hb
            } else {
                slidePos.h -= slideshow.ss.slideslinks.dimm.hb
            }
        }
        if ( slideshow.ss.slideCaption ) {
            if ( slideshow.ss.slideCaption && slideshow.ss.slideCaption.captionPosition == 'top' ) {
                y += slideshow.ss.slideCaption.dimm.hb
            } else {
                slidePos.h -= slideshow.ss.slideCaption.dimm.hb
            }
        }
        par = jQueryTemp( '.slideshowHolder', slideshow );
        y = y + ( slidePos.h - this.height ) / 2;
        x = ( slidePos.wb - this.width ) / 2;
        slideshow.ss.holder.css( 'top', y + 'px' ).css( 'left', x + 'px' ).html( '<img src="' + this.src + '" />' );
        slideshow.ss.holder.fadeIn( slideshow.ss.fadeDuration );
        nextslide = slideshow.ss.currentslide + 1;
        if ( nextslide > slideshow.ss.images.length ) {
            nextslide = 1
        }
        prevslide = slideshow.ss.currentslide - 1;
        if ( prevslide < 1 ) {
            prevslide = slideshow.ss.images.length
        }
        slideshow.ss.nextslide.o.css( 'display', 'block' ).css( 'top', y + 'px' ).css( 'left', x + 2 * this.width / 3 + 'px' ).css( 'width', this.width / 3 + 'px' ).css( 'height', this.height + 'px' ).attr( 'title', slideshow.ss.images[nextslide - 1].caption );
        slideshow.ss.nextslide.o.get( 0 ).href = '#' + nextslide + jQueryTemp.attr( slideshow, 'id' );
        slideshow.ss.prevslide.o.css( 'display', 'block' ).css( 'top', y + 'px' ).css( 'left', x + 'px' ).css( 'width', this.width / 3 + 'px' ).css( 'height', this.height + 'px' ).attr( 'title', slideshow.ss.images[prevslide - 1].caption );
        slideshow.ss.prevslide.o.get( 0 ).href = '#' + prevslide + jQueryTemp.attr( slideshow, 'id' )
    }, build: function( o ) {
        if ( !o || !o.container || jQueryTemp.islideshow.slideshows[o.container] )
            return;
        var container = jQueryTemp( '#' + o.container );
        var el = container.get( 0 );
        if ( el.style.position != 'absolute' && el.style.position != 'relative' ) {
            el.style.position = 'relative'
        }
        el.style.overflow = 'hidden';
        if ( container.size() == 0 )
            return;
        el.ss = { };
        el.ss.images = o.images ? o.images : [ ];
        el.ss.random = o.random && o.random == true || false;
        imgs = el.getElementsByTagName( 'IMG' );
        for ( i = 0; i < imgs.length; i++ ) {
            indic = el.ss.images.length;
            el.ss.images[indic] = { src: imgs[i].src, caption: imgs[i].title || imgs[i].alt || '' }
        }
        if ( el.ss.images.length == 0 ) {
            return
        }
        el.ss.oP = jQueryTemp.extend( jQueryTemp.iUtil.getPosition( el ), jQueryTemp.iUtil.getSize( el ) );
        el.ss.oPad = jQueryTemp.iUtil.getPadding( el );
        el.ss.oBor = jQueryTemp.iUtil.getBorder( el );
        t = parseInt( el.ss.oPad.t ) + parseInt( el.ss.oBor.t );
        b = parseInt( el.ss.oPad.b ) + parseInt( el.ss.oBor.b );
        jQueryTemp( 'img', el ).remove();
        el.ss.fadeDuration = o.fadeDuration ? o.fadeDuration : 500;
        if ( o.linksPosition || o.linksClass || o.activeLinkClass ) {
            el.ss.slideslinks = { };
            container.append( '<div class="slideshowLinks"></div>' );
            el.ss.slideslinks.o = jQueryTemp( '.slideshowLinks', el );
            if ( o.linksClass ) {
                el.ss.slideslinks.linksClass = o.linksClass;
                el.ss.slideslinks.o.addClass( o.linksClass )
            }
            if ( o.activeLinkClass ) {
                el.ss.slideslinks.activeLinkClass = o.activeLinkClass
            }
            el.ss.slideslinks.o.css( 'position', 'absolute' ).css( 'width', el.ss.oP.w + 'px' );
            if ( o.linksPosition && o.linksPosition == 'top' ) {
                el.ss.slideslinks.linksPosition = 'top';
                el.ss.slideslinks.o.css( 'top', t + 'px' )
            } else {
                el.ss.slideslinks.linksPosition = 'bottom';
                el.ss.slideslinks.o.css( 'bottom', b + 'px' )
            }
            el.ss.slideslinks.linksSeparator = o.linksSeparator ? o.linksSeparator : ' ';
            for ( var i = 0; i < el.ss.images.length; i++ ) {
                indic = parseInt( i ) + 1;
                el.ss.slideslinks.o.append( '<a href="#' + indic + o.container + '" class="slideshowLink" title="' + el.ss.images[i].caption + '">' + indic + '</a>' + ( indic != el.ss.images.length ? el.ss.slideslinks.linksSeparator : '' ) )
            }
            jQueryTemp( 'a', el.ss.slideslinks.o ).bind( 'click', function() {
                jQueryTemp.islideshow.go( { link: this } )
            } );
            el.ss.slideslinks.dimm = jQueryTemp.iUtil.getSize( el.ss.slideslinks.o.get( 0 ) )
        }
        if ( o.captionPosition || o.captionClass ) {
            el.ss.slideCaption = { };
            container.append( '<div class="slideshowCaption"> </div>' );
            el.ss.slideCaption.o = jQueryTemp( '.slideshowCaption', el );
            if ( o.captionClass ) {
                el.ss.slideCaption.captionClass = o.captionClass;
                el.ss.slideCaption.o.addClass( o.captionClass )
            }
            el.ss.slideCaption.o.css( 'position', 'absolute' ).css( 'width', el.ss.oP.w + 'px' );
            if ( o.captionPosition && o.captionPosition == 'top' ) {
                el.ss.slideCaption.captionPosition = 'top';
                el.ss.slideCaption.o.css( 'top', ( el.ss.slideslinks && el.ss.slideslinks.linksPosition == 'top' ? el.ss.slideslinks.dimm.hb + t : t ) + 'px' )
            } else {
                el.ss.slideCaption.captionPosition = 'bottom';
                el.ss.slideCaption.o.css( 'bottom', ( el.ss.slideslinks && el.ss.slideslinks.linksPosition == 'bottom' ? el.ss.slideslinks.dimm.hb + b : b ) + 'px' )
            }
            el.ss.slideCaption.dimm = jQueryTemp.iUtil.getSize( el.ss.slideCaption.o.get( 0 ) )
        }
        if ( o.nextslideClass ) {
            el.ss.nextslide = { nextslideClass: o.nextslideClass };
            container.append( '<a href="#2' + o.container + '" class="slideshowNextSlide"> </a>' );
            el.ss.nextslide.o = jQueryTemp( '.slideshowNextSlide', el );
            el.ss.nextslide.o.css( 'position', 'absolute' ).css( 'display', 'none' ).css( 'overflow', 'hidden' ).css( 'fontSize', '30px' ).addClass( el.ss.nextslide.nextslideClass );
            el.ss.nextslide.o.bind( 'click', jQueryTemp.islideshow.gonext )
        }
        if ( o.prevslideClass ) {
            el.ss.prevslide = { prevslideClass: o.prevslideClass };
            container.append( '<a href="#0' + o.container + '" class="slideshowPrevslide"> </a>' );
            el.ss.prevslide.o = jQueryTemp( '.slideshowPrevslide', el );
            el.ss.prevslide.o.css( 'position', 'absolute' ).css( 'display', 'none' ).css( 'overflow', 'hidden' ).css( 'fontSize', '30px' ).addClass( el.ss.prevslide.prevslideClass );
            el.ss.prevslide.o.bind( 'click', jQueryTemp.islideshow.goprev )
        }
        container.prepend( '<div class="slideshowHolder"></div>' );
        el.ss.holder = jQueryTemp( '.slideshowHolder', el );
        el.ss.holder.css( 'position', 'absolute' ).css( 'top', '0px' ).css( 'left', '0px' ).css( 'display', 'none' );
        if ( o.loader ) {
            container.prepend( '<div class="slideshowLoader" style="display: none;"><img src="' + o.loader + '" /></div>' );
            el.ss.loader = jQueryTemp( '.slideshowLoader', el );
            el.ss.loader.css( 'position', 'absolute' );
            var img = new Image();
            img.slideshow = o.container;
            img.src = o.loader;
            if ( img.complete ) {
                img.onload = null;
                jQueryTemp.islideshow.go( { loader: img } )
            } else {
                img.onload = function() {
                    jQueryTemp.islideshow.go( { loader: this } )
                }
            }
        } else {
            jQueryTemp.islideshow.go( { container: el } )
        }
        if ( o.autoplay ) {
            time = parseInt( o.autoplay ) * 1000
        }
        jQueryTemp.islideshow.slideshows[o.container] = o.autoplay ? window.setInterval( 'jQueryTemp.islideshow.timer(\'' + o.container + '\')', time ) : null
    } };
jQueryTemp.slideshow = jQueryTemp.islideshow.build;
jQueryTemp.iSort = { changed: [ ], collected: { }, helper: false, inFrontOf: null, start: function() {
        if ( jQueryTemp.iDrag.dragged == null ) {
            return
        }
        var shs, margins, c, cs;
        jQueryTemp.iSort.helper.get( 0 ).className = jQueryTemp.iDrag.dragged.dragCfg.hpc;
        shs = jQueryTemp.iSort.helper.get( 0 ).style;
        shs.display = 'block';
        jQueryTemp.iSort.helper.oC = jQueryTemp.extend( jQueryTemp.iUtil.getPosition( jQueryTemp.iSort.helper.get( 0 ) ), jQueryTemp.iUtil.getSize( jQueryTemp.iSort.helper.get( 0 ) ) );
        shs.width = jQueryTemp.iDrag.dragged.dragCfg.oC.wb + 'px';
        shs.height = jQueryTemp.iDrag.dragged.dragCfg.oC.hb + 'px';
        margins = jQueryTemp.iUtil.getMargins( jQueryTemp.iDrag.dragged );
        shs.marginTop = margins.t;
        shs.marginRight = margins.r;
        shs.marginBottom = margins.b;
        shs.marginLeft = margins.l;
        if ( jQueryTemp.iDrag.dragged.dragCfg.ghosting == true ) {
            c = jQueryTemp.iDrag.dragged.cloneNode( true );
            cs = c.style;
            cs.marginTop = '0px';
            cs.marginRight = '0px';
            cs.marginBottom = '0px';
            cs.marginLeft = '0px';
            cs.display = 'block';
            jQueryTemp.iSort.helper.empty().append( c )
        }
        jQueryTemp( jQueryTemp.iDrag.dragged ).after( jQueryTemp.iSort.helper.get( 0 ) );
        jQueryTemp.iDrag.dragged.style.display = 'none'
    }, check: function( e ) {
        if ( !e.dragCfg.so && jQueryTemp.iDrop.overzone.sortable ) {
            if ( e.dragCfg.onStop )
                e.dragCfg.onStop.apply( dragged );
            jQueryTemp( e ).css( 'position', e.dragCfg.initialPosition || e.dragCfg.oP );
            jQueryTemp( e ).DraggableDestroy();
            jQueryTemp( jQueryTemp.iDrop.overzone ).SortableAddItem( e )
        }
        jQueryTemp.iSort.helper.removeClass( e.dragCfg.hpc ).html( ' ' );
        jQueryTemp.iSort.inFrontOf = null;
        var shs = jQueryTemp.iSort.helper.get( 0 ).style;
        shs.display = 'none';
        jQueryTemp.iSort.helper.after( e );
        if ( e.dragCfg.fx > 0 ) {
            jQueryTemp( e ).fadeIn( e.dragCfg.fx )
        }
        jQueryTemp( 'body' ).append( jQueryTemp.iSort.helper.get( 0 ) );
        var ts = [ ];
        var fnc = false;
        for ( var i = 0; i < jQueryTemp.iSort.changed.length; i++ ) {
            var iEL = jQueryTemp.iDrop.zones[jQueryTemp.iSort.changed[i]].get( 0 );
            var id = jQueryTemp.attr( iEL, 'id' );
            var ser = jQueryTemp.iSort.serialize( id );
            if ( iEL.dropCfg.os != ser.hash ) {
                iEL.dropCfg.os = ser.hash;
                if ( fnc == false && iEL.dropCfg.onChange ) {
                    fnc = iEL.dropCfg.onChange
                }
                ser.id = id;
                ts[ts.length] = ser
            }
        }
        jQueryTemp.iSort.changed = [ ];
        if ( fnc != false && ts.length > 0 ) {
            fnc( ts )
        }
    }, checkhover: function( e, o ) {
        if ( !jQueryTemp.iDrag.dragged )
            return;
        var cur = false;
        var i = 0;
        if ( e.dropCfg.el.size() > 0 ) {
            for ( i = e.dropCfg.el.size(); i > 0; i-- ) {
                if ( e.dropCfg.el.get( i - 1 ) != jQueryTemp.iDrag.dragged ) {
                    if ( !e.sortCfg.floats ) {
                        if ( ( e.dropCfg.el.get( i - 1 ).pos.y + e.dropCfg.el.get( i - 1 ).pos.hb / 2 ) > jQueryTemp.iDrag.dragged.dragCfg.ny ) {
                            cur = e.dropCfg.el.get( i - 1 )
                        } else {
                            break
                        }
                    } else {
                        if ( ( e.dropCfg.el.get( i - 1 ).pos.x + e.dropCfg.el.get( i - 1 ).pos.wb / 2 ) > jQueryTemp.iDrag.dragged.dragCfg.nx && ( e.dropCfg.el.get( i - 1 ).pos.y + e.dropCfg.el.get( i - 1 ).pos.hb / 2 ) > jQueryTemp.iDrag.dragged.dragCfg.ny ) {
                            cur = e.dropCfg.el.get( i - 1 )
                        }
                    }
                }
            }
        }
        if ( cur && jQueryTemp.iSort.inFrontOf != cur ) {
            jQueryTemp.iSort.inFrontOf = cur;
            jQueryTemp( cur ).before( jQueryTemp.iSort.helper.get( 0 ) )
        } else if ( !cur && ( jQueryTemp.iSort.inFrontOf != null || jQueryTemp.iSort.helper.get( 0 ).parentNode != e ) ) {
            jQueryTemp.iSort.inFrontOf = null;
            jQueryTemp( e ).append( jQueryTemp.iSort.helper.get( 0 ) )
        }
        jQueryTemp.iSort.helper.get( 0 ).style.display = 'block'
    }, measure: function( e ) {
        if ( jQueryTemp.iDrag.dragged == null ) {
            return
        }
        e.dropCfg.el.each( function() {
            this.pos = jQueryTemp.extend( jQueryTemp.iUtil.getSizeLite( this ), jQueryTemp.iUtil.getPositionLite( this ) )
        } )
    }, serialize: function( s ) {
        var i;
        var h = '';
        var o = { };
        if ( s ) {
            if ( jQueryTemp.iSort.collected[s] ) {
                o[s] = [ ];
                jQueryTemp( '#' + s + ' .' + jQueryTemp.iSort.collected[s] ).each( function() {
                    if ( h.length > 0 ) {
                        h += '&'
                    }
                    h += s + '[]=' + jQueryTemp.attr( this, 'id' );
                    o[s][o[s].length] = jQueryTemp.attr( this, 'id' )
                } )
            } else {
                for ( a in s ) {
                    if ( jQueryTemp.iSort.collected[s[a]] ) {
                        o[s[a]] = [ ];
                        jQueryTemp( '#' + s[a] + ' .' + jQueryTemp.iSort.collected[s[a]] ).each( function() {
                            if ( h.length > 0 ) {
                                h += '&'
                            }
                            h += s[a] + '[]=' + jQueryTemp.attr( this, 'id' );
                            o[s[a]][o[s[a]].length] = jQueryTemp.attr( this, 'id' )
                        } )
                    }
                }
            }
        } else {
            for ( i in jQueryTemp.iSort.collected ) {
                o[i] = [ ];
                jQueryTemp( '#' + i + ' .' + jQueryTemp.iSort.collected[i] ).each( function() {
                    if ( h.length > 0 ) {
                        h += '&'
                    }
                    h += i + '[]=' + jQueryTemp.attr( this, 'id' );
                    o[i][o[i].length] = jQueryTemp.attr( this, 'id' )
                } )
            }
        }
        return{ hash: h, o: o }
    }, addItem: function( e ) {
        if ( !e.childNodes ) {
            return
        }
        return this.each( function() {
            if ( !this.sortCfg || !jQueryTemp( e ).is( '.' + this.sortCfg.accept ) )
                jQueryTemp( e ).addClass( this.sortCfg.accept );
            jQueryTemp( e ).Draggable( this.sortCfg.dragCfg )
        } )
    }, destroy: function() {
        return this.each( function() {
            jQueryTemp( '.' + this.sortCfg.accept ).DraggableDestroy();
            jQueryTemp( this ).DroppableDestroy();
            this.sortCfg = null;
            this.isSortable = null
        } )
    }, build: function( o ) {
        if ( o.accept && jQueryTemp.iUtil && jQueryTemp.iDrag && jQueryTemp.iDrop ) {
            if ( !jQueryTemp.iSort.helper ) {
                jQueryTemp( 'body', document ).append( '<div id="sortHelper"> </div>' );
                jQueryTemp.iSort.helper = jQueryTemp( '#sortHelper' );
                jQueryTemp.iSort.helper.get( 0 ).style.display = 'none'
            }
            this.Droppable( { accept: o.accept, activeclass: o.activeclass ? o.activeclass : false, hoverclass: o.hoverclass ? o.hoverclass : false, helperclass: o.helperclass ? o.helperclass : false, onHover: o.onHover || o.onhover, onOut: o.onOut || o.onout, sortable: true, onChange: o.onChange || o.onchange, fx: o.fx ? o.fx : false, ghosting: o.ghosting ? true : false, tolerance: o.tolerance ? o.tolerance : 'intersect' } );
            return this.each( function() {
                var dragCfg = { revert: o.revert ? true : false, zindex: 3000, opacity: o.opacity ? parseFloat( o.opacity ) : false, hpc: o.helperclass ? o.helperclass : false, fx: o.fx ? o.fx : false, so: true, ghosting: o.ghosting ? true : false, handle: o.handle ? o.handle : null, containment: o.containment ? o.containment : null, onStart: o.onStart && o.onStart.constructor == Function ? o.onStart : false, onDrag: o.onDrag && o.onDrag.constructor == Function ? o.onDrag : false, onStop: o.onStop && o.onStop.constructor == Function ? o.onStop : false, axis: /vertically|horizontally/.test( o.axis ) ? o.axis : false, snapDistance: o.snapDistance ? parseInt( o.snapDistance ) || 0 : false, cursorAt: o.cursorAt ? o.cursorAt : false };
                jQueryTemp( '.' + o.accept, this ).Draggable( dragCfg );
                this.isSortable = true;
                this.sortCfg = { accept: o.accept, revert: o.revert ? true : false, zindex: 3000, opacity: o.opacity ? parseFloat( o.opacity ) : false, hpc: o.helperclass ? o.helperclass : false, fx: o.fx ? o.fx : false, so: true, ghosting: o.ghosting ? true : false, handle: o.handle ? o.handle : null, containment: o.containment ? o.containment : null, floats: o.floats ? true : false, dragCfg: dragCfg }
            } )
        }
    } };
jQueryTemp.fn.extend( { Sortable: jQueryTemp.iSort.build, SortableAddItem: jQueryTemp.iSort.addItem, SortableDestroy: jQueryTemp.iSort.destroy } );
jQueryTemp.SortSerialize = jQueryTemp.iSort.serialize;
jQueryTemp.iTooltip = { current: null, focused: false, oldTitle: null, focus: function( e ) {
        jQueryTemp.iTooltip.focused = true;
        jQueryTemp.iTooltip.show( e, this, true )
    }, hidefocused: function( e ) {
        if ( jQueryTemp.iTooltip.current != this )
            return;
        jQueryTemp.iTooltip.focused = false;
        jQueryTemp.iTooltip.hide( e, this )
    }, show: function( e, el, focused ) {
        if ( jQueryTemp.iTooltip.current != null )
            return;
        if ( !el ) {
            el = this
        }
        jQueryTemp.iTooltip.current = el;
        pos = jQueryTemp.extend( jQueryTemp.iUtil.getPosition( el ), jQueryTemp.iUtil.getSize( el ) );
        jEl = jQueryTemp( el );
        title = jEl.attr( 'title' );
        href = jEl.attr( 'href' );
        if ( title ) {
            jQueryTemp.iTooltip.oldTitle = title;
            jEl.attr( 'title', '' );
            jQueryTemp( '#tooltipTitle' ).html( title );
            if ( href )
                jQueryTemp( '#tooltipURL' ).html( href.replace( 'http://', '' ) );
            else
                jQueryTemp( '#tooltipURL' ).html( '' );
            helper = jQueryTemp( '#tooltipHelper' );
            if ( el.tooltipCFG.className ) {
                helper.get( 0 ).className = el.tooltipCFG.className
            } else {
                helper.get( 0 ).className = ''
            }
            helperSize = jQueryTemp.iUtil.getSize( helper.get( 0 ) );
            filteredPosition = focused && el.tooltipCFG.position == 'mouse' ? 'bottom' : el.tooltipCFG.position;
            switch ( filteredPosition ) {
                case'top':
                    ny = pos.y - helperSize.hb;
                    nx = pos.x;
                    break;
                case'left':
                    ny = pos.y;
                    nx = pos.x - helperSize.wb;
                    break;
                case'right':
                    ny = pos.y;
                    nx = pos.x + pos.wb;
                    break;
                case'mouse':
                    jQueryTemp( 'body' ).bind( 'mousemove', jQueryTemp.iTooltip.mousemove );
                    pointer = jQueryTemp.iUtil.getPointer( e );
                    ny = pointer.y + 15;
                    nx = pointer.x + 15;
                    break;
                default:
                    ny = pos.y + pos.hb;
                    nx = pos.x;
                    break
            }
            helper.css( { top: ny + 'px', left: nx + 'px' } );
            if ( el.tooltipCFG.delay == false ) {
                helper.show()
            } else {
                helper.fadeIn( el.tooltipCFG.delay )
            }
            if ( el.tooltipCFG.onShow )
                el.tooltipCFG.onShow.apply( el );
            jEl.bind( 'mouseout', jQueryTemp.iTooltip.hide ).bind( 'blur', jQueryTemp.iTooltip.hidefocused )
        }
    }, mousemove: function( e ) {
        if ( jQueryTemp.iTooltip.current == null ) {
            jQueryTemp( 'body' ).unbind( 'mousemove', jQueryTemp.iTooltip.mousemove );
            return
        }
        pointer = jQueryTemp.iUtil.getPointer( e );
        jQueryTemp( '#tooltipHelper' ).css( { top: pointer.y + 15 + 'px', left: pointer.x + 15 + 'px' } )
    }, hide: function( e, el ) {
        if ( !el ) {
            el = this
        }
        if ( jQueryTemp.iTooltip.focused != true && jQueryTemp.iTooltip.current == el ) {
            jQueryTemp.iTooltip.current = null;
            jQueryTemp( '#tooltipHelper' ).fadeOut( 1 );
            jQueryTemp( el ).attr( 'title', jQueryTemp.iTooltip.oldTitle ).unbind( 'mouseout', jQueryTemp.iTooltip.hide ).unbind( 'blur', jQueryTemp.iTooltip.hidefocused );
            if ( el.tooltipCFG.onHide )
                el.tooltipCFG.onHide.apply( el );
            jQueryTemp.iTooltip.oldTitle = null
        }
    }, build: function( options ) {
        if ( !jQueryTemp.iTooltip.helper ) {
            jQueryTemp( 'body' ).append( '<div id="tooltipHelper"><div id="tooltipTitle"></div><div id="tooltipURL"></div></div>' );
            jQueryTemp( '#tooltipHelper' ).css( { position: 'absolute', zIndex: 3000, display: 'none' } );
            jQueryTemp.iTooltip.helper = true
        }
        return this.each( function() {
            if ( jQueryTemp.attr( this, 'title' ) ) {
                this.tooltipCFG = { position: /top|bottom|left|right|mouse/.test( options.position ) ? options.position : 'bottom', className: options.className ? options.className : false, delay: options.delay ? options.delay : false, onShow: options.onShow && options.onShow.constructor == Function ? options.onShow : false, onHide: options.onHide && options.onHide.constructor == Function ? options.onHide : false };
                var el = jQueryTemp( this );
                el.bind( 'mouseover', jQueryTemp.iTooltip.show );
                el.bind( 'focus', jQueryTemp.iTooltip.focus )
            }
        } )
    } };
jQueryTemp.fn.ToolTip = jQueryTemp.iTooltip.build;
jQueryTemp.iTTabs = { doTab: function( e ) {
        pressedKey = e.charCode || e.keyCode || -1;
        if ( pressedKey == 9 ) {
            if ( window.event ) {
                window.event.cancelBubble = true;
                window.event.returnValue = false
            } else {
                e.preventDefault();
                e.stopPropagation()
            }
            if ( this.createTextRange ) {
                document.selection.createRange().text = "\t";
                this.onblur = function() {
                    this.focus();
                    this.onblur = null
                }
            } else if ( this.setSelectionRange ) {
                start = this.selectionStart;
                end = this.selectionEnd;
                this.value = this.value.substring( 0, start ) + "\t" + this.value.substr( end );
                this.setSelectionRange( start + 1, start + 1 );
                this.focus()
            }
            return false
        }
    }, destroy: function() {
        return this.each( function() {
            if ( this.hasTabsEnabled && this.hasTabsEnabled == true ) {
                jQueryTemp( this ).unbind( 'keydown', jQueryTemp.iTTabs.doTab );
                this.hasTabsEnabled = false
            }
        } )
    }, build: function() {
        return this.each( function() {
            if ( this.tagName == 'TEXTAREA' && ( !this.hasTabsEnabled || this.hasTabsEnabled == false ) ) {
                jQueryTemp( this ).bind( 'keydown', jQueryTemp.iTTabs.doTab );
                this.hasTabsEnabled = true
            }
        } )
    } };
jQueryTemp.fn.extend( { EnableTabs: jQueryTemp.iTTabs.build, DisableTabs: jQueryTemp.iTTabs.destroy } );
jQueryTemp.iUtil = { getPosition: function( e ) {
        var x = 0;
        var y = 0;
        var es = e.style;
        var restoreStyles = false;
        if ( jQueryTemp( e ).css( 'display' ) == 'none' ) {
            var oldVisibility = es.visibility;
            var oldPosition = es.position;
            restoreStyles = true;
            es.visibility = 'hidden';
            es.display = 'block';
            es.position = 'absolute'
        }
        var el = e;
        while ( el ) {
            x += el.offsetLeft + ( el.currentStyle && !jQueryTemp.browser.opera ? parseInt( el.currentStyle.borderLeftWidth ) || 0 : 0 );
            y += el.offsetTop + ( el.currentStyle && !jQueryTemp.browser.opera ? parseInt( el.currentStyle.borderTopWidth ) || 0 : 0 );
            el = el.offsetParent
        }
        el = e;
        while ( el && el.tagName && el.tagName.toLowerCase() != 'body' ) {
            x -= el.scrollLeft || 0;
            y -= el.scrollTop || 0;
            el = el.parentNode
        }
        if ( restoreStyles == true ) {
            es.display = 'none';
            es.position = oldPosition;
            es.visibility = oldVisibility
        }
        return{ x: x, y: y }
    }, getPositionLite: function( el ) {
        var x = 0, y = 0;
        while ( el ) {
            x += el.offsetLeft || 0;
            y += el.offsetTop || 0;
            el = el.offsetParent
        }
        return{ x: x, y: y }
    }, getSize: function( e ) {
        var w = jQueryTemp.css( e, 'width' );
        var h = jQueryTemp.css( e, 'height' );
        var wb = 0;
        var hb = 0;
        var es = e.style;
        if ( jQueryTemp( e ).css( 'display' ) != 'none' ) {
            wb = e.offsetWidth;
            hb = e.offsetHeight
        } else {
            var oldVisibility = es.visibility;
            var oldPosition = es.position;
            es.visibility = 'hidden';
            es.display = 'block';
            es.position = 'absolute';
            wb = e.offsetWidth;
            hb = e.offsetHeight;
            es.display = 'none';
            es.position = oldPosition;
            es.visibility = oldVisibility
        }
        return{ w: w, h: h, wb: wb, hb: hb }
    }, getSizeLite: function( el ) {
        return{ wb: el.offsetWidth || 0, hb: el.offsetHeight || 0 }
    }, getClient: function( e ) {
        var h, w, de;
        if ( e ) {
            w = e.clientWidth;
            h = e.clientHeight
        } else {
            de = document.documentElement;
            w = window.innerWidth || self.innerWidth || ( de && de.clientWidth ) || document.body.clientWidth;
            h = window.innerHeight || self.innerHeight || ( de && de.clientHeight ) || document.body.clientHeight
        }
        return{ w: w, h: h }
    }, getScroll: function( e ) {
        var t = 0, l = 0, w = 0, h = 0, iw = 0, ih = 0;
        if ( e && e.nodeName.toLowerCase() != 'body' ) {
            t = e.scrollTop;
            l = e.scrollLeft;
            w = e.scrollWidth;
            h = e.scrollHeight;
            iw = 0;
            ih = 0
        } else {
            if ( document.documentElement ) {
                t = document.documentElement.scrollTop;
                l = document.documentElement.scrollLeft;
                w = document.documentElement.scrollWidth;
                h = document.documentElement.scrollHeight
            } else if ( document.body ) {
                t = document.body.scrollTop;
                l = document.body.scrollLeft;
                w = document.body.scrollWidth;
                h = document.body.scrollHeight
            }
            iw = self.innerWidth || document.documentElement.clientWidth || document.body.clientWidth || 0;
            ih = self.innerHeight || document.documentElement.clientHeight || document.body.clientHeight || 0
        }
        return{ t: t, l: l, w: w, h: h, iw: iw, ih: ih }
    }, getMargins: function( e, toInteger ) {
        var el = jQueryTemp( e );
        var t = el.css( 'marginTop' ) || '';
        var r = el.css( 'marginRight' ) || '';
        var b = el.css( 'marginBottom' ) || '';
        var l = el.css( 'marginLeft' ) || '';
        if ( toInteger )
            return{ t: parseInt( t ) || 0, r: parseInt( r ) || 0, b: parseInt( b ) || 0, l: parseInt( l ) };
        else
            return{ t: t, r: r, b: b, l: l }
    }, getPadding: function( e, toInteger ) {
        var el = jQueryTemp( e );
        var t = el.css( 'paddingTop' ) || '';
        var r = el.css( 'paddingRight' ) || '';
        var b = el.css( 'paddingBottom' ) || '';
        var l = el.css( 'paddingLeft' ) || '';
        if ( toInteger )
            return{ t: parseInt( t ) || 0, r: parseInt( r ) || 0, b: parseInt( b ) || 0, l: parseInt( l ) };
        else
            return{ t: t, r: r, b: b, l: l }
    }, getBorder: function( e, toInteger ) {
        var el = jQueryTemp( e );
        var t = el.css( 'borderTopWidth' ) || '';
        var r = el.css( 'borderRightWidth' ) || '';
        var b = el.css( 'borderBottomWidth' ) || '';
        var l = el.css( 'borderLeftWidth' ) || '';
        if ( toInteger )
            return{ t: parseInt( t ) || 0, r: parseInt( r ) || 0, b: parseInt( b ) || 0, l: parseInt( l ) || 0 };
        else
            return{ t: t, r: r, b: b, l: l }
    }, getPointer: function( event ) {
        var x = event.pageX || ( event.clientX + ( document.documentElement.scrollLeft || document.body.scrollLeft ) ) || 0;
        var y = event.pageY || ( event.clientY + ( document.documentElement.scrollTop || document.body.scrollTop ) ) || 0;
        return{ x: x, y: y }
    }, traverseDOM: function( nodeEl, func ) {
        func( nodeEl );
        nodeEl = nodeEl.firstChild;
        while ( nodeEl ) {
            jQueryTemp.iUtil.traverseDOM( nodeEl, func );
            nodeEl = nodeEl.nextSibling
        }
    }, purgeEvents: function( nodeEl ) {
        jQueryTemp.iUtil.traverseDOM( nodeEl, function( el ) {
            for ( var attr in el ) {
                if ( typeof el[attr] === 'function' ) {
                    el[attr] = null
                }
            }
        } )
    }, centerEl: function( el, axis ) {
        var clientScroll = jQueryTemp.iUtil.getScroll();
        var windowSize = jQueryTemp.iUtil.getSize( el );
        if ( !axis || axis == 'vertically' )
            jQueryTemp( el ).css( { top: clientScroll.t + ( ( Math.max( clientScroll.h, clientScroll.ih ) - clientScroll.t - windowSize.hb ) / 2 ) + 'px' } );
        if ( !axis || axis == 'horizontally' )
            jQueryTemp( el ).css( { left: clientScroll.l + ( ( Math.max( clientScroll.w, clientScroll.iw ) - clientScroll.l - windowSize.wb ) / 2 ) + 'px' } )
    }, fixPNG: function( el, emptyGIF ) {
        var images = jQueryTemp( 'img[@src*="png"]', el || document ), png;
        images.each( function() {
            png = this.src;
            this.src = emptyGIF;
            this.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" + png + "')"
        } )
    } };
[ ].indexOf || ( Array.prototype.indexOf = function( v, n ) {
    n = ( n == null ) ? 0 : n;
    var m = this.length;
    for ( var i = n; i < m; i++ )
        if ( this[i] == v )
            return i;
    return-1
} );