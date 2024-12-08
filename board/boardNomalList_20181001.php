
			<div class="du01">
				<ul class="list_contents">
				<?
				if($Ncounts < 1)  { //없을 경우
				} else {
					while($n = $nqStmt->fetch(PDO::FETCH_ASSOC)) {
						$nbNIdx = trim($n['b_NIdx']);	 
						$nTitle = trim($n['b_Title']);	 
						$nsubject = cut_str(stripslashes($nTitle),$b_TitCnt);
				?>

					<li style="border-top:1px solid #dadcdf;" onclick="location.href='view1.html'">
						<p class="text">
							<span class="title">공지사항입니다..공지사항입니다..공지사항입니다공지사항입니다공지사항입니다..공지사항입니다..</span>
						</p>
						<p class="">					
							<span class="more light_gray"><?= DateHard($n[reg_Date],1) ?> </span>
						</p>
					</li>


				<?
					}

				}
				
				if($counts < 1)  { //없을 경우
				?>

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
					while($v = $qStmt->fetch(PDO::FETCH_ASSOC)) {
						$Title = $v[b_Title];
						$subject = cut_str(stripslashes($Title),$b_TitCnt);
				?>

					<a href="/taxi/board/boardView.php?board_id=<?=$board_id ?>&amp;idx=<?=$v[b_NIdx] ?><?=$pageParam?>">
					<li style="border-top:1px solid #dadcdf;">
						<p class="text">
							<span class="title">공지사항입니다..공지사항입니다..공지사항입니다공지사항입니다공지사항입니다..공지사항입니다..</span>
						</p>
						<p class="">					
						<span class="more light_gray"><?= DateHard($v[reg_Date],1) ?> </span>
						</p>
					</li>
					</a>

				<?
					}

				}
				
				?>

		
			</ul>
		</div>