			<div class="du01">
				<ul class="list_contents">
				<?if ( $totalCnt < 1)  { //없을 경우 ?>
					<li>
						<p class="text">
						<span class="title">동록된 게시물이 없습니다.</span>
						</p>
						<p class="">					
						<span class="more light_gray"></span>
						</p>
					</li>
				<?
				} else {
					while($n = $nqStmt->fetch(PDO::FETCH_ASSOC)) {
						$nbNIdx = trim($n['b_NIdx']);	 
						$nTitle = trim($n['b_Title']);	 
						$nsubject = cut_str(stripslashes($nTitle),$b_TitCnt);
				?>

					<? if ($du_udev['lv']== '0' || $du_udev[lv]== '1')  {  //관리자 && 최고권한관리자일경우 ?>
						<a href="/board/boardView.php<?=$qstr?>&amp;idx=<?=$n['b_NIdx'] ?>">
					<? } else { //게시판 글 보기권한 체크가 없을 경우?>
						<? if ( $n['b_Hide'] == "Y" ) { //비밀글일경우...?>
							<a href="/board/boardPwView.php<?=$qstr?>&amp;idx=<?=$n['b_NIdx'] ?>&amp;mode=V">
						<? } else { //비밀글이 아닐경우?>
							 <a href="/board/boardView.php<?=$qstr?>&amp;idx=<?=$n['b_NIdx'] ?>">
						<? } ?>
					
					<? } ?>

						<li style="border-top:1px solid #dadcdf;">
							<p class="text notice">
								<span class="ic_notice">공지</span>
								<span class="title"><?=$nsubject?></span>
							</p>
							<p class="">					
								<span class="more light_gray"><?= DateHard($n['reg_Date'],1) ?> </span>
							</p>
						</li>
						</a>
				<?
					}

				}?>
				

			<div class="du01">
				<ul class="list_contents">
				<?
				if($counts < 1)  { //없을 경우
				} else {
					while($v = $qStmt->fetch(PDO::FETCH_ASSOC)) {
						$Title = $v['b_Title'];
						$subject = cut_str(stripslashes($Title),$b_TitCnt);
				?>
				<? if ($du_udev['lv']== '0' || $du_udev['lv']== '1')  {  //관리자 && 최고권한관리자일경우 ?>
					<a href="/board/boardView.php<?=$qstr?>&amp;idx=<?=$v['b_NIdx'] ?>">
				<? } else { //게시판 글 보기권한 체크가 없을 경우?>
					<? if ( $v['b_Hide'] == "Y" ) { //비밀글일경우...?>
						<a href="/board/boardPwView.php<?=$qstr?>&amp;idx=<?=$v['b_NIdx'] ?>&amp;mode=V">
					<? } else { //비밀글이 아닐경우?>
						 <a href="/board/boardView.php<?=$qstr?>&amp;idx=<?=$v['b_NIdx'] ?>">
					<? } ?>
				
				<? } ?>

					<li style="border-top:1px solid #dadcdf;">
						<p class="text">
							<span class="title"><?=$subject?></span>
						</p>
						<p class="">					
							<span class="more light_gray"><?= DateHard($v['reg_Date'],1) ?> </span>
						</p>
					</li>
					</a>

				<?
					}
				}
				
				?>

		
			</ul>
		</div>