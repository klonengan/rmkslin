jQuery.noConflict();
/* Fix Bootstap and Prototype conflict */
if (Prototype.BrowserFeatures.ElementExtensions) {
    var disablePrototypeJS = function (method, pluginsToDisable) {
            var handler = function (event) {
                event.target[method] = undefined;
                setTimeout(function () {
                    delete event.target[method];
                }, 0);
            };
            pluginsToDisable.each(function (plugin) {
                jQuery(window).on(method + '.bs.' + plugin, handler);
            });
        },
        pluginsToDisable = ['collapse', 'dropdown', 'modal', 'tooltip', 'popover' , 'tab'];
    disablePrototypeJS('show', pluginsToDisable);
    disablePrototypeJS('hide', pluginsToDisable);
}

$j(document).ready(function(){

/* start format section  */

    /* rm = ranch market, fm = farmer market */
    var rm = $j("#ranch"), fm = $j("#farmer"),rmID = rm.attr('data-id'),fmID = fm.attr('data-id'),optionStore = $j("#rm_locator");
    var city = $j("#city-selector"), store = $j("#store-selector"), form = $j("#format-form");
    /* pre loader */
    var loading = "<option value=\"\">"+"Loading..."+"</option>";
    /* call function */
    formatSwitch();
    selectStore();

    function emptyStore(){
        $j('#store-selector').children('option:not(:first)').remove();
    }

    function formatSwitch()
    {
        $j(rm).on( "click", function() {
            $j("#brand_pick").val(rmID);
            loadCity();
            loadStore();
            optionStore.addClass('open');
            /* motion down */
            rm.removeClass('opacity');fm.removeClass('clickdown');rm.addClass('clickdown');fm.addClass('opacity');optionStore.addClass('clickdown');

        });

        $j(fm).on( "click", function() {
            $j("#brand_pick").val(fmID);
            loadCity();
            loadStore();
            optionStore.addClass('open');
            /* motion down */
            fm.removeClass('opacity');rm.removeClass('clickdown');fm.addClass('clickdown');rm.addClass('opacity');optionStore.addClass('clickdown');
        });

    }

    function loadCity()
    {
        $j.ajax({
            method: "POST",
            url: formatUrl+"loadcity",
            data: { brand_id : $j("#brand_pick").val(), city: 0 },
            beforeSend: function () {
                $j(city).html(loading).prop("disabled", true).addClass('select-disabled');
            },
            success: function (response) {
                var cities = jQuery.parseJSON(response);
                var options = cities.html;
                $j(city).html(options).prop("disabled", false).removeClass('select-disabled');
            },
            error: function () {
                console.log('failed');
            }
        })
    }

    /* load all store */
    function loadStore()
    {
        $j.ajax({
            method: "POST",
            url: formatUrl+"loadstore",
            data: { brand_id: $j("#brand_pick").val(), city: 0 },
            beforeSend: function () {
                $j(store).html(loading).prop("disabled", true).addClass('select-disabled');
            },
            success: function (response) {
                var stores = jQuery.parseJSON(response);
                var options = stores.html;
                $j('#store-selector').html(options).prop("disabled", false).removeClass('select-disabled');
            },
            error: function () {
                console.log('failed');
            }
        })
    }

    /* on change city */
    function selectStore() {
        $j(city).change(function () {
            $j.ajax({
                method: "POST",
                url: formatUrl+"loadstore",
                data: {brand_id: $j("#brand_pick").val(), city: this.value},
                beforeSend: function () {
                    //emptyStore();
                    $j(store).html(loading).prop("disabled", true).addClass('select-disabled');
                },
                success: function (response) {
                    var stores = jQuery.parseJSON(response);
                    var options = stores.html;
                    $j('#store-selector').html(options).prop("disabled", false).removeClass('select-disabled');
                },
                error: function () {
                    console.log('failed');
                }
            })
        });
    }

    /* Submit format form
        $j(form).submit(function(event){
            event.preventDefault();
            if( city.val() == 0 && store.val() == 0 )
                $j("[data-toggle=tooltip]").tooltip({
                    placement: $j(this).data("placement") || 'bottom'
                });
            else
            $j.ajax({
                method: "POST",
                url: "format/index/loadstore",
                data: { brand_id: $j("#brand_pick").val(), city: this.value },
                beforeSend: function () {
                    emptyStore();
                    $j(store).html(loading).prop("disabled", true).addClass('select-disabled');
                },
                success: function (response) {
                    var stores = jQuery.parseJSON(response);
                    var options = stores.html;
                    $j('#store-selector').html(options).prop("disabled", false).removeClass('select-disabled');
                },
                error: function () {
                    console.log('failed');
                }
            })
        });
    */
    /* end format section  */

    /* start homepage section  */

    /* Change Store Homepage */
    var storeswitch = $j(".store-switcher"),storedropdown = $j(".store-dropdown");
    $j(storeswitch).bind( "click", function() {
        storedropdown.toggle();
    });

    /* header city switcher */
    var cityheader = $j("#header-city-switcher");
    //if(cityheader.length)
    //header_city_switcher();
    function header_city_switcher()
    {
        $j.ajax({
            method: "POST",
            url: formatUrl+"loadcityheader",
            data: { brand_id: $j("#brand_pick").val()},
            beforeSend: function () {
                $j(cityheader).html(loading).prop("disabled", true).addClass('select-disabled');
            },
            success: function (response) {
                var stores = jQuery.parseJSON(response);
                var list = stores.html;
                $j(cityheader).html(list).prop("disabled", false).removeClass('select-disabled');
            },
            error: function () {
                console.log('failed');
            }
        });

        $j(cityheader).change(function(){
            $j.ajax({
                method: "POST",
                url: formatUrl+"loadstoreheader",
                data: { brand_id: $j("#brand_id").val(), city: this.value },
                beforeSend: function () {
                    //$j(store).html(loading).prop("disabled", true).addClass('select-disabled');
                },
                success: function (response) {
                    var stores = jQuery.parseJSON(response);
                    var list = stores.html;
                    $j('#store-list-header').html(list);
                },
                error: function () {
                    console.log('failed');
                }
            })
        });
    }

    /* Reset top floating menu always on top  */
    $j(".nav-primary > li").each(function(i){
        var x = i + 1, top = ( i * (-31) );
        $j(".nav-"+ x +" > ul.level0").css("top", top+"px");
    });

    $j(".nav-"+ 1 +" > ul.level0").css("top", 0+"px");

    /* Reset top floating menu always on top  */
    $j(".nav-primary li").each(function(){
        $j(this).hover(function (e){
            $j("#category-menu-title").addClass("title-menu-hover");
            $j(".cat-menu-arrow").addClass("transform");
            $j(this).addClass("");
            $j("#nav").addClass("menu-hover");

        }, function(){
            $j("#category-menu-title").removeClass("title-menu-hover");
            $j(".cat-menu-arrow").removeClass("transform");
            $j("#nav").removeClass("menu-hover");
        });
        //var x = i + 1, top = ( i * (-31) );
        //$j(".nav-"+ x +" > ul.level0").css("top", top+"px");
    });

    var homeMenu = $j("#category-menu-title"), menuDiv = $j("#nav");
    $j(homeMenu).click(function(){
        $j(menuDiv).toggle();
    });

    /* end homepage section  */




});

$j(document).ready(function() {
/*
    $j("#top-banner-slideshow").owlCarousel({


        pagination: true,
        navigation:true,
        navigationText: [
            "<i class='icon-chevron-left icon-white'></i>",
            "<i class='icon-chevron-right icon-white'></i>"
        ]

    });*/

    $j("#owl-demo").owlCarousel({

        autoPlay: 3000, //Set AutoPlay to 3 seconds
        items : 4,
        itemsDesktop : [1199,3],
        itemsDesktopSmall : [979,3],
        pagination: false,
        navigation:true,
        navigationText: [
            "<i class='icon-chevron-left icon-white'></i>",
            "<i class='icon-chevron-right icon-white'></i>"
        ]

    });

    $j("#owl-demo2").owlCarousel({

        autoPlay: 300000, //Set AutoPlay to 3 seconds
        items : 4,
        itemsDesktop : [1199,3],
        itemsDesktopSmall : [979,3],
        itemsMobile: [479,2],
        pagination: false

    });

    $j("#promo-mobile-banner").owlCarousel({

        autoPlay: 300000, //Set AutoPlay to 3 seconds
        itemsCustom : [
            [0, 1]
        ],
        pagination: false

    });

    $j("#owl-demo3").owlCarousel({

        autoPlay: 300000, //Set AutoPlay to 3 seconds
        items : 4,
        itemsDesktop : [1199,3],
        itemsDesktopSmall : [979,3],
        itemsMobile: [479,2],
        pagination: false

    })

    // top-banner-slideshow
    $j("#top-banner-slideshow").owlCarousel({

        navigation : true, // Show next and prev buttons
        slideSpeed : 300,
        paginationSpeed : 400,
        singleItem:true

    });

    //hompage slider tweek
    function homapagepunya() {

        var widthbrowser = jQuery( window ).width();
        var wslid = $j("#banner-top").width();

        if(widthbrowser <= 768) {
            $j("body.cms-home div.owl-wrapper-outer").css("width", wslid);
            $j("body.cms-home div.owl-wrapper-outer > div > div.item").css("width", wslid);
        }
    }

    homapagepunya();

    $j(window).resize(homapagepunya);
});

$j(document).on('click', function(event) {
    var triggermenu = $j('#nav-expander');
    if (!$j(event.target).closest('#sidebar-menu').length && !$j(event.target).closest(triggermenu).length ) {
        $j('body').removeClass('nav-expanded');
        $j('#sidebar-catlevel-0').removeClass('hidecontent');
        $j('#sidebar-catlevel-1').addClass('hidecontent');
        $j('#sidebar-catlevel-2').addClass('hidecontent');
        $j('.overlay').addClass('hidecontent');
        //console.log('close side menu');
    }
});
$j(document).ready(function(){
    //Navigation Menu Slider
    /**/
    $j('#nav-expander').on('click',function(e){
        e.preventDefault();
        $j('body').toggleClass('nav-expanded');
        $j('.overlay').removeClass('hidecontent');
    });

    $j('#sidebar-close').on('click',function(e){
        e.preventDefault();
        $j('body').removeClass('nav-expanded');
        $j('.overlay').addClass('hidecontent');
    });


    /* link back from main menu to category level 2    */
    $j('#sidebar-content-category').on('click', function(e) {
        e.preventDefault();
        $j('.catlevel-1-header').show();
        $j('#sidebar-catlevel-0').addClass('hidecontent');
        $j('#sidebar-catlevel-1').removeClass('hidecontent');
    });

    /* link back from level 1 to main menu  */
    $j('#sidebar-catlevel-1-back').on('click', function(e) {
        e.preventDefault();
        $j('.catlevel-1-header').show();
        if($j('#sidebar-catlevel-1').hasClass('level2')){
            //console.log('level2');
            $j('#sidebar-catlevel-1 ul').children().show();
            $j('.submenucategory').hide();

        }else{
            //console.log('main menu');
            $j('#sidebar-catlevel-1').addClass('hidecontent');
            $j('#sidebar-catlevel-0').removeClass('hidecontent');
        }
    });

    /* link back from level 2 to main menu  */
    $j('#sidebar-catlevel-2-back').on('click', function(e) {
        e.preventDefault();
        $j('.catlevel-1-header').show();
        $j('#sidebar-catlevel-1').removeClass('hidecontent');
        $j('#sidebar-catlevel-2').addClass('hidecontent');
        $j('.submenucategory').hide();
    });

    $j('#sidebar-mainmenu-back').on('click', function(e){
        $j('#sidebar-catlevel-0').removeClass('hidecontent');
        $j('#sidebar-catlevel-1').addClass('hidecontent');
    });

    var level2 = $j("#sidebar-catlevel-1>ul.mainmenucategory>li");
    $j.each( level2, function( i, val ) {
        var dataid = $j(this).attr('data-id');
        $j(this).on('click', function(e) {
            var submenu = $j("#sidebar-"+dataid).show();
            $j('#sidebar-catlevel-1').addClass('hidecontent');
            $j('#sidebar-catlevel-2').removeClass('hidecontent');
            $j('.catlevel-1-header').hide();
        });
    });

    /* Show hide category header menu */
    $j("#category-mobile-block .cat-item:gt(7)").hide();
    $j("#category-mobile-block").show();
    $j("#category-mobile-shorcut-header").on('click',function(e){
        $j("#category-mobile-block .cat-item:gt(7)").show();
        $j(".btn-cat-holder").hide();

    });
    //$j("#category-mobile-shorcut-header").fadeOut();

    /* hack owl-carousel  */
    //$j('#owl-demo2 .owl-wrapper-outer').addClass('noverflow');



    //var p = $j( ".add-to-cart-wrapper" );
    //var p_offset = $j( ".add-to-cart-wrapper" ).offset().top;
    //var height = $j( window ).height();
    //var position = p.position();
    //console.log( "Height Offset: " + p_offset + ", Height: " + height + ", left: " + position.left + ", top: " + position.top );
    $j(function() {
        jQuery(window).scroll(sticky_relocate);
        sticky_relocate();
    });

    function sticky_relocate() {
        var addtocart   = $j( ".add-to-cart-wrapper" );
        var window_top  = $j(window).scrollTop();
        var header      = $j('#header-mobile').height();
        var width       = $j( window ).width();

        if( width <= 768 ) {
            if (window_top > header) {
                $j(addtocart).show();
                $j(addtocart).addClass('addtocartfixed');
            } else {
                $j(addtocart).removeClass('addtocartfixed');
            }
        }



        /* buat tombol scrol top */
        var scrollToBotm = $j(document).scrollTop() + $j(window).height();
        var onFoot = $j("#footer-area").offset().top

        if( scrollToBotm > onFoot ){
            $j(".btn-scroll-top").show()
        }else{
            $j(".btn-scroll-top").hide()
        }
    }


    $j(".btn-scroll-top").click(function(){
        $j('html, body').animate({scrollTop : 0},800);
        return false;
    });

});




