<?php
use yii\helpers\Html;
use	yii\widgets\LinkPager;
/* @var $this yii\web\View */

$this->title = 'Мои рапорта';
?>

<div class="row">
	<div class="col-md-2">
		<label class="form-label">Месяц</label>
		<select class="form-control">
			<option>Октябрь</option>
		</select>
	</div>
	<div class="col-md-2 text-right">
		<button class="btn btn-success" style="margin-top: 10px;">Фильтровать</button>
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
