<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
//new dBug($arParams, '', true);
//new dBug($arResult, '', true);
?>

<div class="hist-cont-in">

	<!-- Заголовок для моб -->
	<div class="lk-tit show-tit">
		Подписки
	</div>
	<!-- Заголовок для моб -->
	<div class="subs-list" data-user_subscription_id="<?=$arResult["DATA"]["ID"]?>">
		<?foreach($arResult["RUBRIC_LIST"] as $rubricItem) {?>

			<div class="one-subs">
				<label data-rubric_id="<?= $rubricItem["RUBRIC"]["ID"]?>" >

					<?if($rubricItem["IS_USER_SUBSCRIBED"]) {?>
						<input type="checkbox" checked data-rubric_id="<?= $rubricItem["RUBRIC"]["ID"]?>" >
					<?} else {?>
						<input type="checkbox" data-rubric_id="<?= $rubricItem["RUBRIC"]["ID"]?>">
					<? } ?>

					<span>
						<?=$rubricItem["RUBRIC"]["NAME"]?>
					</span>

				</label>
			</div>

		<? } ?>
	</div>

</div>