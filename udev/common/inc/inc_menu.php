	<nav id="gnb" class="gnb_large ">
        <h2>관리자 주메뉴</h2>
        <ul class="gnb_ul">
<? if ( $du_udev['lv'] == 0 ) { //최고권한관리자 	?>
                <li class="gnb_li <? if ($menu == "0") {?>on<? } ?>">
                <button type="button" class="btn_op menu-200 menu-order-1" title="환경설정">환경설정</button>
                <div class="gnb_oparea_wr">
                    <div class="gnb_oparea">
                        <h3>환경설정</h3>
                        <ul>

							<li data-menu="1">
								<a href="<?=DU_UDEV_DIR?>/config/configReg.php" class="gnb_2da <? if ($menu == "0" && $smenu == "1") {?>on<? } ?>">환경설정</a>
							</li>
							<!-- <li data-menu="7">
								<a href="<?=DU_UDEV_DIR?>/config/configLangList.php" class="gnb_2da <? if ($menu == "0" && $smenu == "7") {?>on<? } ?>">언어 환경설정</a>
							</li> -->
							<li data-menu="3">
								<a href="<?=DU_UDEV_DIR?>/config/configEtcReg.php" class="gnb_2da <? if ($menu == "0" && $smenu == "3") {?>on<? } ?>">기타 환경설정</a>
							</li>
							<li data-menu="5">
								<a href="/phpinfo.php" class="gnb_2da gnb_grp_div" target="_blank">phpinfo()</a>
							</li>
						</ul>    
					</div>
                </div>
            </li>
<? } ?>

			<li class="gnb_li <? if ($menu == "1") {?>on<? } ?>">
                <button type="button" class="btn_op menu-100 menu-order-1" title="회원관리">회원관리</button>
                <div class="gnb_oparea_wr">
                    <div class="gnb_oparea">
                        <h3>회원관리</h3>
                        <ul>
							<li data-menu="6">
								<a href="<?=DU_UDEV_DIR?>/member/memberAdminList.php" class="gnb_2da <? if ($menu == "1" && $smenu == "6") {?>on<? } ?>">관리자 관리</a>
							</li>
							<li data-menu="2">
								<a href="<?=DU_UDEV_DIR?>/member/memberList.php" class="gnb_2da <? if ($menu == "1" && $smenu == "2") {?>on<? } ?>">회원 관리</a>
							</li>
							<li data-menu="3">
								<a href="<?=DU_UDEV_DIR?>/member/memberLeaveList.php" class="gnb_2da <? if ($menu == "1" && $smenu == "3") {?>on<? } ?>">탈퇴회원 관리</a>
							</li>
							<li data-menu="4">
								<a href="<?=DU_UDEV_DIR?>/member/memStatList.php" class="gnb_2da <? if ($menu == "1" && $smenu == "7") {?>on<? } ?>">회원통계</a>
							</li>
						</ul>                    
					</div>
                </div>
            </li>
			<li class="gnb_li <? if ($menu == "2") {?>on<? } ?>">
                <button type="button" class="btn_op menu-500 menu-order-2" title="지도관리;">지도관리</button>
                <div class="gnb_oparea_wr">
                    <div class="gnb_oparea">
                        <h3>지도관리</h3>
                        <ul>
							<li data-menu="1">
								<a href="<?=DU_UDEV_DIR?>/contents/list_contents.php" class="gnb_2da <? if ($menu == "2" && $smenu == "1") {?>on<? } ?>">지도관리</a>
							</li>
							<li data-menu="1">
								<a href="<?=DU_UDEV_DIR?>/contents/list_place.php" class="gnb_2da <? if ($menu == "2" && $smenu == "2") {?>on<? } ?>">지점관리</a>
							</li>
						</ul>                    
					</div>
                </div>
            </li>
			<li class="gnb_li <? if ($menu == "3") {?>on<? } ?>">
                <button type="button" class="btn_op menu-400 menu-order-3" title="기타관리;">기타관리</button>
                <div class="gnb_oparea_wr">
                    <div class="gnb_oparea">
                        <h3>기타관리</h3>
                        <ul>
							<li data-menu="1">
								<a href="<?=DU_UDEV_DIR?>/etc/list_report.php" class="gnb_2da <? if ($menu == "3" && $smenu == "1") {?>on<? } ?>">신고관리</a>
							</li>
						</ul>                    
					</div>
                </div>
            </li>
			<li class="gnb_li <? if ($menu == "6") {?>on<? } ?>">
                <button type="button" class="btn_op menu-300 menu-order-4" title="게시판관리">게시판관리</button>
                <div class="gnb_oparea_wr">
                    <div class="gnb_oparea">
                        <h3>게시판관리</h3>
                        <ul>
							<li data-menu="3">
							<? if ( $du_udev['lv'] == 0 ) { //최고권한관리자 	
							?>
								<a href="<?=DU_UDEV_DIR?>/boardM/boardManagerList.php" class="gnb_2da <? if ($menu == "6" && $smenu == "1") {?>on<? } ?>">게시판 환경설정 관리</a>
							<? } else { ?>
								<a href="<?=DU_UDEV_DIR?>/boardM/boardManagerList.php" class="gnb_2da <? if ($menu == "6" && $smenu == "1") {?>on<? } ?>">게시판 관리</a>
							<? } ?>
							</li>
							<li data-menu="3">
								<a href="/board/boardList.php?board_id=1" target="_BLANK" class="gnb_2da <? if ($menu == "6" && $smenu == "2") {?>on<? } ?>">공지사항 바로가기</a>
							</li>
							<li data-menu="3">
								<a href="/board/boardList.php?board_id=2" target="_BLANK" class="gnb_2da <? if ($menu == "6" && $smenu == "3") {?>on<? } ?>">문의사항 바로가기</a>
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
