	<nav id="gnb" class="gnb_large ">
        <h2>관리자 주메뉴</h2>
        <ul class="gnb_ul">
                <li class="gnb_li <? if ($menu == "1") {?>on<? } ?>">
                <button type="button" class="btn_op menu-100 menu-order-1" title="환경설정">환경설정</button>
                <div class="gnb_oparea_wr">
                    <div class="gnb_oparea">
                        <h3>환경설정</h3>
                        <ul>

							<li data-menu="1">
								<a href="<?=DU_UDEV_DIR?>/config/configReg.php" class="gnb_2da <? if ($menu == "1" && $smenu == "1") {?>on<? } ?>">환경설정</a>
							</li>
							<li data-menu="7">
								<a href="<?=DU_UDEV_DIR?>/config/configExcReg.php" class="gnb_2da <? if ($menu == "1" && $smenu == "7") {?>on<? } ?>">캐시환전 환경설정</a>
							</li>
							<li data-menu="2">
								<a href="<?=DU_UDEV_DIR?>/config/configEtcReg.php" class="gnb_2da <? if ($menu == "1" && $smenu == "2") {?>on<? } ?>">기타 환경설정</a>
							</li>
							<li data-menu="6">
								<a href="<?=DU_UDEV_DIR?>/config/configGuideReg.php" class="gnb_2da <? if ($menu == "1" && $smenu == "6") {?>on<? } ?>">가이드이미지등록</a>
							</li>
							<li data-menu="3">
								<a href="<?=DU_UDEV_DIR?>/config/memPointManagerList.php" class="gnb_2da <? if ($menu == "1" && $smenu == "4") {?>on<? } ?>">포인트 정책 관리</a>
							</li>							
							
							<? if ( $du_udev['lv'] == 0 ) { //최고권한관리자 	?>
							<!--
							<li data-menu="100310">
								<a href="#" class="gnb_2da  ">팝업레이어관리</a>
							</li>
							-->
							<li data-menu="4">
								<a href="<?=DU_UDEV_DIR?>/config/sessionFileDel.php" class="gnb_2da <? if ($menu == "1" && $smenu == "3") {?>on<? } ?>">세션파일 일괄삭제</a>
							</li>
							<li data-menu="5">
								<a href="/phpinfo.php" class="gnb_2da gnb_grp_div" target="_blank">phpinfo()</a>
							</li>
							<? } ?>
						</ul>    
					</div>
                </div>
            </li>

			<li class="gnb_li <? if ($menu == "2") {?>on<? } ?>">
                <button type="button" class="btn_op menu-200 menu-order-2" title="회원관리">회원관리</button>
                <div class="gnb_oparea_wr">
                    <div class="gnb_oparea">
                        <h3>회원관리</h3>
                        <ul>
							<? if ( $du_udev['lv'] == 0 || $du_udev['lv'] == 1 ) { //최고권한관리자 	?>
							<li data-menu="1">
								<a href="<?=DU_UDEV_DIR?>/member/memManagerList.php" class="gnb_2da <? if ($menu == "2" && $smenu == "1") {?>on<? } ?>">회원등급 관리</a>
							</li>
							<? } ?>
							<li data-menu="2">
								<a href="<?=DU_UDEV_DIR?>/member/memberList.php" class="gnb_2da <? if ($menu == "2" && $smenu == "2") {?>on<? } ?>">회원 관리</a>
							</li>
							<li data-menu="3">
								<a href="<?=DU_UDEV_DIR?>/member/memberLeaveList.php" class="gnb_2da <? if ($menu == "2" && $smenu == "3") {?>on<? } ?>">탈퇴회원 관리</a>
							</li>
							<li data-menu="4">
								<a href="<?=DU_UDEV_DIR?>/member/pointList.php" class="gnb_2da <? if ($menu == "2" && $smenu == "4") {?>on<? } ?>">캐시 관리</a>
							</li>
							<li data-menu="4">
								<a href="<?=DU_UDEV_DIR?>/member/mirpayList.php" class="gnb_2da <? if ($menu == "2" && $smenu == "5") {?>on<? } ?>">미르패이 관리</a>
							</li>
							<li data-menu="4">
								<a href="<?=DU_UDEV_DIR?>/member/memStatList.php" class="gnb_2da <? if ($menu == "2" && $smenu == "6") {?>on<? } ?>">회원통계</a>
							</li>
						</ul>                    
					</div>
                </div>
            </li>

			<li class="gnb_li <? if ($menu == "6") {?>on<? } ?>">
                <button type="button" class="btn_op menu-300 menu-order-3" title="게시판관리">게시판관리</button>
                <div class="gnb_oparea_wr">
                    <div class="gnb_oparea">
                        <h3>게시판관리</h3>
                        <ul>
							<li data-menu="3">
							<? if ( $du_udev['lv'] == 0 ) { //최고권한관리자 	?>
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



			<li class="gnb_li <? if ($menu == "3") {?>on<? } ?>">
                <button type="button" class="btn_op menu-500 menu-order-5" title="기타관리">기타관리</button>
                <div class="gnb_oparea_wr">
                    <div class="gnb_oparea">
                        <h3>기타관리</h3>
                        <ul>
							<li data-menu="5">
								<a href="<?=DU_UDEV_DIR?>/etc/cardList.php" class="gnb_2da <? if ($menu == "3" && $smenu == "1") {?>on<? } ?>">결제카드 관리</a>
							</li>
							<li data-menu="5">
								<a href="<?=DU_UDEV_DIR?>/coupon/couponManagerList.php" class="gnb_2da <? if ($menu == "3" && $smenu == "2") {?>on<? } ?>">쿠폰 관리</a>
							</li>
							<li data-menu="5">
								<a href="<?=DU_UDEV_DIR?>/coupon/couponUseList.php" class="gnb_2da <? if ($menu == "3" && $smenu == "6") {?>on<? } ?>">쿠폰 사용내역 관리</a>
							</li>
							<li data-menu="5">
								<a href="<?=DU_UDEV_DIR?>/etc/eventManagerList.php" class="gnb_2da <? if ($menu == "3" && $smenu == "3") {?>on<? } ?>">이벤트배너 관리</a>
							</li>
							<!-- 게시판으로 작업 한 부분으로 주석치 그리고 문의하기 게시판에서 문의할수 있는지 여부 파악해서 문의받기 작업일 2019-01-22
							<li data-menu="5">
								<a href="<?=DU_UDEV_DIR?>/etc/qnaList.php" class="gnb_2da <? if ($menu == "3" && $smenu == "4") {?>on<? } ?>">QNA관리</a>
							</li>
							-->
							<li data-menu="5">
								<a href="<?=DU_UDEV_DIR?>/etc/inquiryList.php" class="gnb_2da <? if ($menu == "3" && $smenu == "5") {?>on<? } ?>">문의리스트 관리</a>
							</li>
							<li data-menu="5">
								<a href="<?=DU_UDEV_DIR?>/etc/cardStatList.php" class="gnb_2da <? if ($menu == "3" && $smenu == "7") {?>on<? } ?>">카드등록통계</a>
							</li>
						</ul>                
					</div>
                </div>
            </li>


			<? //if ( $du_udev[lv] == 0 ) { //최고권한관리자 	?>

			<li class="gnb_li <? if ($menu == "4") {?>on<? } ?>">
                <button type="button" class="btn_op menu-400 menu-order-4" title="매칭관리">매칭관리</button>
                <div class="gnb_oparea_wr">
                    <div class="gnb_oparea">
                        <h3>매칭관리</h3>
                        <ul>
							<li data-menu="4">
								<a href="<?=DU_UDEV_DIR?>/taxiSharing/taxiSharingList.php" class="gnb_2da <? if ($menu == "4" && $smenu == "1") {?>on<? } ?>">매칭 관리</a>
							</li>

			<? if ( $du_udev[lv] == 0 ) { //최고권한관리자 	?>
							<li data-menu="4">
								<a href="<?=DU_UDEV_DIR?>/taxiSharing/taxiSharingCList.php" class="gnb_2da <? if ($menu == "4" && $smenu == "2") {?>on<? } ?>">취소 내역</a>
							</li>
							<li data-menu="4">
								<a href="<?=DU_UDEV_DIR?>/taxiSharing/taxiSharingCRList.php" class="gnb_2da <? if ($menu == "4" && $smenu == "3") {?>on<? } ?>">취소처리 관리</a>
							</li>
							<li data-menu="4">
								<a href="<?=DU_UDEV_DIR?>/taxiSharing/taxiSharingRList.php" class="gnb_2da <? if ($menu == "4" && $smenu == "4") {?>on<? } ?>">완료처리 관리</a>
							</li>
							<li data-menu="4">
								<a href="<?=DU_UDEV_DIR?>/taxiSharing/taxiSharingComList.php" class="gnb_2da <? if ($menu == "4" && $smenu == "5") {?>on<? } ?>">완료처리 내역</a>
							</li>
							<li data-menu="4">
								<a href="<?=DU_UDEV_DIR?>/taxiSharing/taxiSharungStatList.php" class="gnb_2da <? if ($menu == "4" && $smenu == "6") {?>on<? } ?>">매칭통계</a>
							</li>
                            <!--
							<li data-menu="4">
								<a href="<?=DU_UDEV_DIR?>/taxiSharing/cateSExcelUp.php" class="gnb_2da <? if ($menu == "4" && $smenu == "2") {?>on<? } ?>">쉐어링 매칭중 관리</a>
							</li>
							<li data-menu="4">
								<a href="<?=DU_UDEV_DIR?>/shop/category_list.php" class="gnb_2da <? if ($menu == "4" && $smenu == "3") {?>on<? } ?>">쉐어링 매칭요청 관리</a>
							</li>
							<li data-menu="4">
								<a href="<?=DU_UDEV_DIR?>/shop/carSize_list.php" class="gnb_2da <? if ($menu == "4" && $smenu == "4") {?>on<? } ?>">차종사이즈관리</a>
							</li>
							<li data-menu="4">
								<a href="<?=DU_UDEV_DIR?>/shop/brcategory_list.php" class="gnb_2da <? if ($menu == "4" && $smenu == "5") {?>on<? } ?>">브랜드관리</a>
							</li>
							<li data-menu="4">
								<a href="<?=DU_UDEV_DIR?>/shop/shop_list.php" class="gnb_2da <? if ($menu == "4" && $smenu == "6") {?>on<? } ?>">상품관리</a>
							</li>
							<li data-menu="4">
								<a href="<?=DU_UDEV_DIR?>/shop/option_list.php" class="gnb_2da <? if ($menu == "4" && $smenu == "7") {?>on<? } ?>">장착비용관리</a>
							</li>
							-->
			<? } ?>
						</ul>                
					</div>
                </div>
            </li>

		<? //} ?>


			<li class="gnb_li <? if ($menu == "5") {?>on<? } ?>">
                <button type="button" class="btn_op menu-600 menu-order-6" title="주문관리">주문관리</button>
                <div class="gnb_oparea_wr">
                    <div class="gnb_oparea">
                        <h3>주문관리</h3>
                        <ul>
							<li data-menu="6">
								<a href="<?=DU_UDEV_DIR?>/order/orderList.php" class="gnb_2da <? if ($menu == "5" && $smenu == "1") {?>on<? } ?>">주문 관리</a>
							</li>
							<!--
							<? if ( $du_udev['lv'] == 0 ) { //최고권한관리자 	?>
							<li data-menu="6">
								<a href="/udev/order/sale.php" class="gnb_2da <? if ($menu == "5" && $smenu == "2") {?>on<? } ?>">매출현황 관리</a>
							</li>
							<li data-menu="6">
								<a href="/udev/order/orderprint.php" class="gnb_2da <? if ($menu == "5" && $smenu == "3") {?>on<? } ?>">주문엑셀출력 관리</a>
							</li>-->
							<? } ?>

						</ul>                
					</div>
                </div>
            </li>


			<li class="gnb_li <? if ($menu == "7") {?>on<? } ?>">
                <button type="button" class="btn_op menu-700 menu-order-7" title="정산관리">정산관리</button>
                <div class="gnb_oparea_wr">
                    <div class="gnb_oparea">
                        <h3>정산관리</h3>
                        <ul>
							<li data-menu="7">
								<a href="<?=DU_UDEV_DIR?>/account/profitList.php" class="gnb_2da <? if ($menu == "7" && $smenu == "1") {?>on<? } ?>">수익 관리</a>
							</li>
							<li data-menu="7">
								<a href="<?=DU_UDEV_DIR?>/account/pointExcList.php" class="gnb_2da <? if ($menu == "7" && $smenu == "2") {?>on<? } ?>">환전요청 관리</a>
							</li>
							<li data-menu="7">
								<a href="<?=DU_UDEV_DIR?>/account/pointStatList.php" class="gnb_2da <? if ($menu == "7" && $smenu == "3") {?>on<? } ?>">수익통계</a>
							</li>
						</ul>                
					</div>
                </div>
            </li>



			<li class="gnb_li <? if ($menu == "10") {?>on<? } ?>">
                <button type="button" class="btn_op menu-400 menu-order-4" title="푸쉬관리">푸쉬관리</button>
                <div class="gnb_oparea_wr">
                    <div class="gnb_oparea">
                        <h3>푸쉬관리</h3>
                        <ul>
							<li data-menu="10">
								<a href="<?=DU_UDEV_DIR?>/push/pushList.php" class="gnb_2da <? if ($menu == "10" && $smenu == "1") {?>on<? } ?>">푸쉬 관리</a>
							</li>
							<li data-menu="10">
								<a href="<?=DU_UDEV_DIR?>/push/pushDisableList.php" class="gnb_2da <? if ($menu == "10" && $smenu == "2") {?>on<? } ?>">수신거부리스트</a>
							</li>
							

						</ul>                
					</div>
                </div>
            </li>
<!--

			<li class="gnb_li <? if ($menu == "8") {?>on<? } ?>">
                <button type="button" class="btn_op menu-700 menu-order-8" title="테스트">테스트</button>
                <div class="gnb_oparea_wr">
                    <div class="gnb_oparea">
                        <h3>테스트</h3>
                        <ul>
							<li data-menu="8">
								<a href="<?=DU_UDEV_DIR?>/test/test1.php" class="gnb_2da <? if ($menu == "8" && $smenu == "1") {?>on<? } ?>">그래프테스트</a>
							</li>
						</ul>                
					</div>
                </div>
            </li>
-->

			<!--

			<li class="gnb_li">
                <button type="button" class="btn_op menu-500 menu-order-5" title="쇼핑몰현황/기타">쇼핑몰현황/기타</button>
                <div class="gnb_oparea_wr">
                    <div class="gnb_oparea">
                        <h3>쇼핑몰현황/기타</h3>
                        <ul><li data-menu="500110"><a href="http://soloution.cafe24.com/adm/shop_admin/sale1.php" class="gnb_2da  ">매출현황</a></li><li data-menu="500100"><a href="http://soloution.cafe24.com/adm/shop_admin/itemsellrank.php" class="gnb_2da  ">상품판매순위</a></li><li data-menu="500120"><a href="http://soloution.cafe24.com/adm/shop_admin/orderprint.php" class="gnb_2da gnb_grp_style gnb_grp_div">주문내역출력</a></li><li data-menu="500400"><a href="http://soloution.cafe24.com/adm/shop_admin/itemstocksms.php" class="gnb_2da gnb_grp_style ">재입고SMS알림</a></li><li data-menu="500300"><a href="http://soloution.cafe24.com/adm/shop_admin/itemevent.php" class="gnb_2da  gnb_grp_div">이벤트관리</a></li><li data-menu="500310"><a href="http://soloution.cafe24.com/adm/shop_admin/itemeventlist.php" class="gnb_2da  ">이벤트일괄처리</a></li><li data-menu="500500"><a href="http://soloution.cafe24.com/adm/shop_admin/bannerlist.php" class="gnb_2da gnb_grp_style gnb_grp_div">배너관리</a></li><li data-menu="500140"><a href="http://soloution.cafe24.com/adm/shop_admin/wishlist.php" class="gnb_2da  gnb_grp_div">보관함현황</a></li><li data-menu="500210"><a href="http://soloution.cafe24.com/adm/shop_admin/price.php" class="gnb_2da gnb_grp_style gnb_grp_div">가격비교사이트</a></li></ul>                    </div>
                </div>
            </li>



                        <li class="gnb_li">
                <button type="button" class="btn_op menu-900 menu-order-6" title="SMS 관리">SMS 관리</button>
                <div class="gnb_oparea_wr">
                    <div class="gnb_oparea">
                        <h3>SMS 관리</h3>
                        <ul><li data-menu="900100"><a href="http://soloution.cafe24.com/adm/sms_admin/config.php" class="gnb_2da  ">SMS 기본설정</a></li><li data-menu="900200"><a href="http://soloution.cafe24.com/adm/sms_admin/member_update.php" class="gnb_2da  ">회원정보업데이트</a></li><li data-menu="900300"><a href="http://soloution.cafe24.com/adm/sms_admin/sms_write.php" class="gnb_2da  ">문자 보내기</a></li><li data-menu="900400"><a href="http://soloution.cafe24.com/adm/sms_admin/history_list.php" class="gnb_2da gnb_grp_style gnb_grp_div">전송내역-건별</a></li><li data-menu="900410"><a href="http://soloution.cafe24.com/adm/sms_admin/history_num.php" class="gnb_2da gnb_grp_style ">전송내역-번호별</a></li><li data-menu="900500"><a href="http://soloution.cafe24.com/adm/sms_admin/form_group.php" class="gnb_2da  gnb_grp_div">이모티콘 그룹</a></li><li data-menu="900600"><a href="http://soloution.cafe24.com/adm/sms_admin/form_list.php" class="gnb_2da  ">이모티콘 관리</a></li><li data-menu="900700"><a href="http://soloution.cafe24.com/adm/sms_admin/num_group.php" class="gnb_2da gnb_grp_style gnb_grp_div">휴대폰번호 그룹</a></li><li data-menu="900800"><a href="http://soloution.cafe24.com/adm/sms_admin/num_book.php" class="gnb_2da gnb_grp_style ">휴대폰번호 관리</a></li><li data-menu="900900"><a href="http://soloution.cafe24.com/adm/sms_admin/num_book_file.php" class="gnb_2da gnb_grp_style ">휴대폰번호 파일</a></li></ul>                    </div>
                </div>
            </li>
			-->
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
