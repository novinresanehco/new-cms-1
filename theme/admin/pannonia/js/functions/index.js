$( function() {



    //===== Hide/show sidebar =====//

    $( '.fullview' ).click( function() {
        $( "body" ).toggleClass( "clean" );
        $( '#sidebar' ).toggleClass( "hide-sidebar mobile-sidebar" );
        $( '#content' ).toggleClass( "full-content" );
    } );



    //===== Hide/show action tabs =====//

    $( '.showmenu' ).click( function() {
        $( '.actions-wrapper' ).slideToggle( 100 );
    } );




    //===== Calendar =====//

    var date = new Date();
    var d = date.getDate();
    var m = date.getMonth();
    var y = date.getFullYear();




    //===== Make code pretty =====//

    window.prettyPrint && prettyPrint();



    //===== Media item hover overlay =====//

    $( '.view' ).hover( function() {
        $( this ).children( ".view-back" ).fadeIn( 200 );
    }, function() {
        $( this ).children( ".view-back" ).fadeOut( 200 );
    } );







    //===== Easy tabs =====//

    $( '.sidebar-tabs' ).easytabs( {
        animationSpeed: 150,
        collapsible: false,
        tabActiveClass: "active"
    } );

    $( '.actions' ).easytabs( {
        animationSpeed: 300,
        collapsible: false,
        tabActiveClass: "current"
    } );





    //===== Collapsible plugin for main nav =====//

    $( '.expand' ).collapsible( {
        defaultOpen: 'current,third',
        cookieName: 'navAct',
        cssOpen: 'subOpened',
        cssClose: 'subClosed',
        speed: 200
    } );


    //===== Form elements styling =====//

    $( ".ui-datepicker-month, .styled, .dataTables_length select" ).uniform( { radioClass: 'choice' } );

} );
//===== Add Icon =====//
var iconsrpp = 100;
function addIcon( target, site, fancyboxPopUp )
{
    $.ajax( {
        url: 'pages/icons.php',
        method: 'post',
        dataType: 'json',
        success: function( result )
        {
            $( "#icons-" + target ).html( 'انتخاب سایز : <a class="iconswither" href="">16x16</a> - <a class="iconswither" href="">24x24</a><hr />' );
            //16x16
            var length = result.small.length;
            $( "#icons-" + target ).append( '<div class="icons16x16 icongroup" id="icons-' + target + '_16x16"><ul class="iconslist"></ul></div>' );
            var total = 0;
            for ( var i = 0; i < length; i++ )
            {
                total++;
                $( "#icons-" + target + "_16x16 .iconslist" ).append( '<li><img src="' + site + 'files/icons/' + result.small[i] + '" /></li>' );
            }
            //pagination
            var totalIconPages = Math.ceil( total / iconsrpp );
            $( "#icons-" + target + '_16x16' ).append( '<div style="clear:both"></div><center><div class="iconPagination"></div><div style="clear:both;margin-bottom:5px"></div></center>' );
            for ( i = 1; i <= totalIconPages; i++ )
            {
                $( "#icons-" + target + "_16x16 .iconPagination" ).append( '<li>' + i + '</li>' );
            }
            $( "#icons-" + target + "_16x16 .iconPagination li:first" ).addClass( 'iconselected' );
            $( "#icons-" + target + "_16x16 .iconslist li" ).slice( 0, iconsrpp ).show();
            //bind actions
            $( "#icons-" + target + "_16x16 .iconslist img" ).click( function()
            {
                if ( fancyboxPopUp )
                {
                    $.fancybox.close();
                }
                $( "#" + target ).val( $( this ).attr( "src" ) );
            } );
            bindIconPagination( target + '_16x16', target );

            //24x24
            var length = result.big.length;
            $( "#icons-" + target ).append( '<div class="icons24x24 icongroup" id="icons-' + target + '_24x24"><ul class="iconslist"></ul></div>' );
            var total = 0;
            for ( var i = 0; i < length; i++ )
            {
                total++;
                $( "#icons-" + target + "_24x24 .iconslist" ).append( '<li><img src="' + site + 'files/icons/' + result.big[i] + '" /></li>' );
            }
            //pagination
            var totalIconPages = Math.ceil( total / iconsrpp );
            $( "#icons-" + target + '_24x24' ).append( '<div style="clear:both"></div><center><div class="iconPagination"></div><div style="clear:both;margin-bottom:5px"></div></center>' );
            for ( i = 1; i <= totalIconPages; i++ )
            {
                $( "#icons-" + target + "_24x24 .iconPagination" ).append( '<li>' + i + '</li>' );
            }
            $( "#icons-" + target + "_24x24 .iconPagination li:first" ).addClass( 'iconselected' );
            $( "#icons-" + target + "_24x24 .iconslist li" ).slice( 0, iconsrpp ).show();
            //bind actions
            $( "#icons-" + target + "_24x24 .iconslist img" ).click( function() {
                if ( fancyboxPopUp )
                {
                    $.fancybox.close();
                }
                $( "#" + target ).val( $( this ).attr( "src" ) );
            } );
            bindIconPagination( target + '_24x24', target );
        },
        error: function()
        {
            alert( 'خطا در اتصال به سرور' );
        }
    } );
}
function bindIconPagination( target, rawtarget )
{
    $( ".iconswither" ).unbind( 'click' );
    $( ".iconswither" ).click( function()
    {
        $( "#icons-" + rawtarget + " .icongroup" ).hide();
        $( "#icons-" + rawtarget + " .icons" + $( this ).text() ).slideDown();
        return false;
    } );
    $( "#icons-" + target + " .iconPagination li" ).click( function()
    {
        $( "#icons-" + target + " .iconPagination li" ).removeClass( 'iconselected' );
        $( this ).addClass( 'iconselected' );
        var page = parseInt( $( this ).html() );
        var start = ( page - 1 ) * iconsrpp;
        var end = start + iconsrpp;
        $( "#icons-" + target + " .iconslist li" ).hide();
        $( "#icons-" + target + " .iconslist li" ).slice( start, end ).show();
    } );
}