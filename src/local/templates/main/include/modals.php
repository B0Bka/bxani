<!-- Вход / Регистрация -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
 <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
			<div class="modal-body">
				<div class="modal-tab">
					 <!-- Nav tabs -->
					<ul class="nav nav-tabs" role="tablist">
						<li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Вход</a></li>
						<li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Регистрация</a></li>
					</ul>
					 <!-- Tab panes -->
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane active" id="home">
							<form id="auth_login" method="POST" action="javascript:void(0);" role="form" enctype="multipart/form-data">
								<div class="log-form">
									<div class="log-form-left">
										<div class="one-log">
 <span>
											E-mail </span> <input name="AUTH-LOGIN" type="text" data-req="1" placeholder="">
										</div>
										<div class="one-log">
 <span>
											Пароль </span> <input name="AUTH-PASSWORD" type="password" data-req="1" placeholder="">
										</div>
 <span class="system-error error-mes"></span>
									</div>
									<div class="log-form-right">
										<div class="log-forg">
 <a href="#" class="forg-bt">
											Забыли пароль? </a>
										</div>
										<div class="log-bt">
 <input id="auth_submit" type="button" value="Войти">
										</div>
									</div>
								</div>
								<div class="social-block">
									<div class="social-title">
										Авторизация через соцсети
									</div>
									<div class="social-auth">
 <a class="google" href="javascript:void(0)"> </a> <a class="facebook" href="javascript:void(0)"> </a>
									</div>
								</div>
							</form>
						</div>
						<div role="tabpanel" class="tab-pane" id="profile">
							<script src='https://www.google.com/recaptcha/api.js'></script>
							<form id="auth_register" method="POST" action="javascript:void(0);" role="form" enctype="multipart/form-data">
								<div class="log-form">
									<div class="log-form-left">
										<div class="one-log">
 <span>Имя*</span> <input name="AUTH-NAME" type="text" data-req="1" placeholder="">
										</div>
										<div class="one-log">
 <span>Фамилия*</span> <input name="AUTH-LAST_NAME" type="text" data-req="1" placeholder="">
										</div>
										<div class="one-log">
 <span>E-mail*</span> <input name="AUTH-EMAIL" type="text" data-req="1" placeholder="">
										</div>
										<div class="one-log">
 <span>Пароль*</span> <input name="AUTH-PASSWORD" type="password" data-req="1" placeholder="">
										</div>
										<div class="one-log">
 <span>Пароль еще раз*</span> <input name="AUTH-CONFIRM_PASSWORD" type="password" data-req="1" placeholder="">
										</div>
									</div>
									<div class="log-form-right">
										<div class="one-log">
 <span>Телефон*</span> <input name="AUTH-PHONE" type="text" data-req="1" id="phone_input" placeholder="+38 (___) ___-__-__">
										</div>
										<div class="one-log">
 <span>Город</span> <input name="AUTH-CITY" type="text" data-req="0" placeholder="">
										</div>
										<div class="one-log">
 <span>Улица</span> <input name="AUTH-STREET" type="text" data-req="0" placeholder="">
										</div>
										<div class="one-log">
											<div class="one-log-house">
 <span>Дом</span> <input name="AUTH-HOUSE" type="text" data-req="0" placeholder="">
											</div>
											<div class="one-log-kv">
 <span>Квартира</span> <input name="AUTH-FLAT" type="text" data-req="0" placeholder="">
											</div>
										</div>
										<div class="one-log-check">
											<div class="one-log-check-in">
 <label> <input name="AUTH-SUB" type="checkbox" autocomplete="off" checked=""> <span>Получать письма о новинках и акциях</span> </label>
											</div>
										</div>
										<div class="one-log">
										<div class="g-recaptcha" data-sitekey="<?=GRECAPTCHA_KEY_PUBLIC?>"></div>
											<span id="recaptcha_error" class="error-mes" style="display: none;"></span>
										</div>
									</div>
									<div class="bt-reg">
 <span class="system-error error-mes"></span> <input id="auth_register_submit" type="button" value="Зарегистрироваться">
									</div>
								</div>
								<div class="social-block">
									<div class="social-title">
										Регистрация через соцсети
									</div>
									<div class="social-auth">
 <a class="google" href="javascript:void(0)"> </a> <a class="facebook" href="javascript:void(0)"> </a>
									</div>
								</div>
							</form>
						</div>
						 <!-- Забыли пароль -->
						<div class="forg-block">
							<form id="auth_forgot" method="POST" action="javascript:void(0);" role="form" enctype="multipart/form-data">
								<div class="forg-tit">
									 Забыли пароль?
								</div>
								<div class="log-form">
									<div class="one-log">
 <span>
										Email </span> <input type="text" name="AUTH-EMAIL" placeholder="Ваш email">
									</div>
                                    <span id="restoreOk"></span>
									<div class="log-form-left">
										<div class="log-forg">
 <a href="#" class="forg-bt">
											Вспомнил пароль </a>
										</div>
									</div>
									<div class="log-form-right">
										<div class="log-bt">
 <input type="button" id="forgot_submit" value="Восстановить пароль">
										</div>
									</div>
								</div>
							</form>
						</div>
						 <!-- Конец Забыли пароль -->
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
 <!-- Конец Вход / Регистрация --> <!-- Instagram -->
<div class="modal fade" id="insta" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
 <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
			<div class="modal-body">
				<div class="modal-tab">
					<div class="pop-inst">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
 <!-- Конец Instagram --> <!-- быстрая покупка -->
<div class="modal" id="size" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
 <button type="button" class="close close-reverce" data-dismiss="modal" aria-label="Close"></button>
			<div class="modal-body size-body">
				<div class="modal-tab product-body">
				</div>
			</div>
		</div>
	</div>
</div>
 <!-- Комплект -->
<div class="modal" id="set-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
 <button type="button" class="close close-reverce" data-dismiss="modal" aria-label="Close"></button>
			<div class="modal-body set-body">
				<div class="modal-tab">
				</div>
			</div>
		</div>
	</div>
</div>
 <!--Доступные магазины со страницы детально-->
<div class="modal" id="shops-avalible" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
 <button type="button" class="close close-reverce" data-dismiss="modal" aria-label="Close"></button>
			<div class="modal-body">
				<div id="map" style="height: 500px">
				</div>
			</div>
		</div>
	</div>
</div>
 <!-- Таблица размеров -->
<div class="modal fade" id="size-table" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<h2>Размер и посадка</h2>
 <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
			<div class="modal-body">
				<div class="girl">
					<img src="/local/templates/main/images/sizes_table_girl.png">
				</div>
				<div class="modal-table-sizes">
					<div class="sizes-container">
						<div class="table-block">
							<table class="table responsive">
							<thead>
							<tr>
								<th colspan="8">
									Таблица размеров
								</th>
							</tr>
							</thead>
							<tbody>
							<tr class="dark-grey light-up">
								<td>
									EU
								</td>
								<td>
									34
								</td>
								<td>
									36
								</td>
								<td>
									38
								</td>
								<td>
									40
								</td>
								<td>
									42
								</td>
								<td>
									44
								</td>
								<td>
									46
								</td>
							</tr>
							<tr>
								<td>
									UA
								</td>
								<td>
									40
								</td>
								<td>
									42
								</td>
								<td>
									44
								</td>
								<td>
									46
								</td>
								<td>
									48
								</td>
								<td>
									50
								</td>
								<td>
									52
								</td>
							</tr>
							<tr>
								<td>
									RU
								</td>
								<td>
									40
								</td>
								<td>
									42
								</td>
								<td>
									44
								</td>
								<td>
									46
								</td>
								<td>
									48
								</td>
								<td>
									50
								</td>
								<td>
									52
								</td>
							</tr>
							<tr>
								<td>
									US
								</td>
								<td>
									XS
								</td>
								<td>
									S
								</td>
								<td>
									M
								</td>
								<td>
									L
								</td>
								<td>
									L/XL
								</td>
								<td>
									XXL
								</td>
								<td>
									XXXL
								</td>
							</tr>
							</tbody>
							</table>
						</div>
						<div class="table-block">
							<table class="table responsive">
							<thead>
							<tr>
								<th colspan="8">
									Таблица размеров
								</th>
							</tr>
							</thead>
							<tbody>
							<tr>
								<td class="width">
									Обхват груди (см)
								</td>
								<td>
									84
								</td>
								<td>
									88
								</td>
								<td>
									92
								</td>
								<td>
									96
								</td>
								<td>
									100
								</td>
								<td>
									104
								</td>
								<td>
									108
								</td>
							</tr>
							<tr>
								<td class="width">
									Обхват талии (см)
								</td>
								<td>
									64
								</td>
								<td>
									68
								</td>
								<td>
									72
								</td>
								<td>
									76
								</td>
								<td>
									80
								</td>
								<td>
									84
								</td>
								<td>
									88
								</td>
							</tr>
							<tr>
								<td class="width">
									Обхват бедер (см)
								</td>
								<td>
									89
								</td>
								<td>
									93
								</td>
								<td>
									97
								</td>
								<td>
									101
								</td>
								<td>
									105
								</td>
								<td>
									109
								</td>
								<td>
									113
								</td>
							</tr>
							</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="info-container">
					<div class="info-heading">
						 Как замерить?
					</div>
					<div class="row">
						<div class="block info-block">
 <i class="number">1</i><span class="info-head">Обхват груди см</span>
							<div class="info-text">
								 Оберните сантиметр вокруг самой объемной части груди, наполовину вдохните и зафиксируйте объем.
							</div>
						</div>
						<div class="block info-block">
 <i class="number">2</i><span class="info-head">Обхват талии см</span>
							<div class="info-text">
								 Оберните сантиметр вокруг самой тонкой части талии, измеряйте на полувдохе.
							</div>
						</div>
						<div class="block info-block">
 <i class="number">3</i><span class="info-head">Обхват бедер см</span>
							<div class="info-text">
								 Измеряется по самой объемной части ягодиц, примерно на уровне тазобедренных суставов.
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal" id="metapopup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
            <button type="button" class="close close-reverce" data-dismiss="modal" aria-label="Close"></button>
			<div class="modal-body meta-body">
				<div class="modal-tab">

				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal" id="feedbackpopup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-body meta-body">
				<div class="modal-tab">

				</div>
			</div>
		</div>
	</div>
</div>
<script>
    $(document).ready(function () {
//        #phone_input
        $("#phone_input").mask("+38 (999) 999-99-99", {placeholder: "+38 (___) ___-__-__"});
    })
</script>
<!-- Конец Размер -->