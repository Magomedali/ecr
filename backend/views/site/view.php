<?php

/* @var $this yii\web\View */

$this->title = 'Бригадир: Иванов Иван';
?>

<div class="row">
	<div class="col-md-4">
		<h3>Данные:</h3>
		<table class="table table-bordered table-collapsed table-hover">
			<tbody>
				<tr>
					<td><strong>Логин</strong></td>
					<td>IvanovIvan</td>
				</tr>
				<tr>
					<td><strong>Ф.И.О</strong></td>
					<td>Иванов Иван</td>
				</tr>
				<tr>
					<td><a href="#">Сменить пароль бригадира</a></td>
					<td></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-md-4">
		<h3>Состав бригады:</h3>
		<table class="table table-bordered table-collapsed table-hover">
			<thead>
				<tr>
					<th>#</th>
					<th>Ф.И.О</th>
					<th>КТУ</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>1</td>
					<td>Ф.И.О</td>
					<td>Иванов Иван</td>
				</tr>
				<tr>
					<td>2</td>
					<td>Иванов Иван</td>
					<td>2</td>
				</tr>
				<tr>
					<td>3</td>
					<td>Иванов Иван</td>
					<td>3</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-md-4">
		<h3>Состав техники:</h3>
		<table class="table table-bordered table-collapsed table-hover">
			<thead>
				<tr>
					<th>#</th>
					<th>Марка</th>
					<th>Модель</th>
					<th>Гос. Номер</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>1</td>
					<td>Марка техники</td>
					<td>Модель</td>
					<td>ау111е11</td>
				</tr>
				<tr>
					<td>2</td>
					<td>Марка техники</td>
					<td>Модель техники</td>
					<td>ау222е22</td>
				</tr>
				<tr>
					<td>3</td>
					<td>Техника</a></td>
					<td>Модель техники</td>
					<td>ау333е33</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<div class="row">
	<div class="col-md-5">
		<h3>Рапорты бригадира:</h3>
	</div>
</div>
<div class="row">
	<div class="col-md-2">
		<label class="form-label">Месяц</label>
		<select class="form-control">
			<option>Октябрь</option>
		</select>
	</div>
	<div class="col-md-2 text-right">
		<button class="btn btn-success" style="margin-top: 25px;">Фильтровать</button>
	</div>
</div>
<div class="row" style="margin-top: 10px;">
	<div class="col-md-12">
		<table class="table table-bordered table-collapsed table-hover">
			<thead>
				<tr>
					<th>#</th>
					<th>Номер</th>
					<th>Дата</th>
					<th>Объект</th>
					<th>Граница</th>
					<th>Контракт</th>
					<th>Статус</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>1</td>
					<td>1000765</td>
					<td>22.10.2018</td>
					<td>Объект</td>
					<td>Граница</td>
					<td>Контракт</td>
					<td style="color:red;">Не выгружен</td>
					<th><a href="#asd">Изменить</a><br><a href="#asd">Удалить</a></th>
				</tr>
				<tr>
					<td>2</td>
					<td>1000764</td>
					<td>21.10.2018</td>
					<td>Объект</td>
					<td>Граница</td>
					<td>Контракт</td>
					<td style="color:blue;">На подтверждении</td>
					<th><a href="#asd">Изменить</a><br><a href="#asd">Удалить</a></th>
				</tr>
				<tr>
					<td>3</td>
					<td>1000763</td>
					<td>20.10.2018</td>
					<td>Объект</td>
					<td>Граница</td>
					<td>Контракт</td>
					<td style="color:red;">Не выгружен</td>
					<th><a href="#asd">Изменить</a><br><a href="#asd">Удалить</a></th>
				</tr>
				<tr>
					<td>4</td>
					<td>1000762</td>
					<td>19.10.2018</td>
					<td>Объект</td>
					<td>Граница</td>
					<td>Контракт</td>
					<td style="color:green;">Подтвержден</td>
					<th><a href="#asd">Удалить</a></th>
				</tr>
				<tr>
					<td>1</td>
					<td>1000765</td>
					<td>22.10.2018</td>
					<td>Объект</td>
					<td>Граница</td>
					<td>Контракт</td>
					<td style="color:red;">Не выгружен</td>
					<th><a href="#asd">Изменить</a><br><a href="#asd">Удалить</a></th>
				</tr>
				<tr>
					<td>2</td>
					<td>1000764</td>
					<td>21.10.2018</td>
					<td>Объект</td>
					<td>Граница</td>
					<td>Контракт</td>
					<td style="color:blue;">На подтверждении</td>
					<th><a href="#asd">Изменить</a><br><a href="#asd">Удалить</a></th>
				</tr>
				<tr>
					<td>3</td>
					<td>1000763</td>
					<td>20.10.2018</td>
					<td>Объект</td>
					<td>Граница</td>
					<td>Контракт</td>
					<td style="color:red;">Не выгружен</td>
					<th><a href="#asd">Изменить</a><br><a href="#asd">Удалить</a></th>
				</tr>
				<tr>
					<td>4</td>
					<td>1000762</td>
					<td>19.10.2018</td>
					<td>Объект</td>
					<td>Граница</td>
					<td>Контракт</td>
					<td style="color:green;">Подтвержден</td>
					<th><a href="#asd">Удалить</a></th>
				</tr>
				<tr>
					<td>1</td>
					<td>1000765</td>
					<td>22.10.2018</td>
					<td>Объект</td>
					<td>Граница</td>
					<td>Контракт</td>
					<td style="color:red;">Не выгружен</td>
					<th><a href="#asd">Изменить</a></th>
				</tr>
				<tr>
					<td>2</td>
					<td>1000764</td>
					<td>21.10.2018</td>
					<td>Объект</td>
					<td>Граница</td>
					<td>Контракт</td>
					<td style="color:blue;">На подтверждении</td>
					<th><a href="#asd">Изменить</a></th>
				</tr>
				<tr>
					<td>3</td>
					<td>1000763</td>
					<td>20.10.2018</td>
					<td>Объект</td>
					<td>Граница</td>
					<td>Контракт</td>
					<td style="color:red;">Не выгружен</td>
					<th><a href="#asd">Изменить</a></th>
				</tr>
				<tr>
					<td>4</td>
					<td>1000762</td>
					<td>19.10.2018</td>
					<td>Объект</td>
					<td>Граница</td>
					<td>Контракт</td>
					<td style="color:green;">Подтвержден</td>
					<th></th>
				</tr>
			</tbody>
		</table>
	</div>
</div>

