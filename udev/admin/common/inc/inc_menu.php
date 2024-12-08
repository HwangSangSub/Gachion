	<nav id="gnb" class="gnb_large ">
        <h2>관리자 주메뉴</h2>
        <ul class="gnb_ul">
                <li class="gnb_li <? if ($menu == "1") {?>on<? } ?>">
                <button type="button" class="btn_op menu-100 menu-order-1" title="개발자메뉴">개발자메뉴</button>
                <div class="gnb_oparea_wr">
                    <div class="gnb_oparea">
                        <h3>개발자메뉴</h3>
                        <ul>
							<li data-menu="1">
								<a href="<?=DU_UDEV_ADMIN?>/config/code_list.php" class="gnb_2da <? if ($menu == "1" && $smenu == "1") {?>on<? } ?>">각종코드관리</a>
							</li>
							<li data-menu="2">
								<a href="<?=DU_UDEV_ADMIN?>/config/report_list.php" class="gnb_2da <? if ($menu == "1" && $smenu == "2") {?>on<? } ?>">신고관리</a>
							</li>	
						</ul>    
					</div>
                </div>
            </li>
         </ul>
    </nav>

</header>
<script>
jQuery(function($){

    var menu_cookie_key = 'g5_admin_btn_gnb';

    $(".tnb_mb_btn").click(function(){
        $(".tnb_mb_area").toggle();
    });

    $("#btn_gnb").click(function(){
        
        var $this = $(this);

        try {
            if( ! $this.hasClass("btn_gnb_open") ){
                set_cookie(menu_cookie_key, 1, 60*60*24*365);
            } else {
                delete_cookie(menu_cookie_key);
            }
        }
        catch(err) {
        }

        $("#container").toggleClass("container-small");
        $("#gnb").toggleClass("gnb_small");
        $this.toggleClass("btn_gnb_open");

    });

    $(".gnb_ul li .btn_op" ).click(function() {
        $(this).parent().addClass("on").siblings().removeClass("on");
    });

});
</script>
