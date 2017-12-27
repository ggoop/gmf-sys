<?php

use Gmf\Sys\Builder;
use Gmf\Sys\Models\Editor\Template;
use Illuminate\Database\Seeder;

class SysEditorTemplateSeeder extends Seeder {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function run() {
		$uid = '';
		Template::build(function (Builder $b) use ($uid) {
			$b->code('md-table')
				->title('Materia表格')
				->memo('Materia风格表格')
				->content(@'
					<div class="md-table md-theme-default">
						<div class="md-content md-table-content">
							<table>
								<tbody>
									<tr class="md-table-row">
										<th class="md-table-head md-numeric">
											<div class="md-table-head-container">
												<div class="md-table-head-label">ID</div>
											</div>
										</th>
										<th class="md-table-head">
											<div class="md-table-head-container">
												<div class="md-table-head-label">Name</div>
											</div>
										</th>
										<th class="md-table-head">
											<div class="md-table-head-container">
												<div class="md-table-head-label">Email</div>
											</div>
										</th>
										<th class="md-table-head">
											<div class="md-table-head-container">
												<div class="md-table-head-label">Gender</div>
											</div>
										</th>
										<th class="md-table-head">
											<div class="md-table-head-container">
												<div class="md-table-head-label">Job Title</div>
											</div>
										</th>
									</tr>
									<tr class="md-table-row">
										<td class="md-table-cell md-numeric"><div class="md-table-cell-container">1</div></td>
										<td class="md-table-cell"><div class="md-table-cell-container">Shawna Dubbin</div></td>
										<td class="md-table-cell"><div class="md-table-cell-container">sdubbin0@geocities.com</div></td>
										<td class="md-table-cell"><div class="md-table-cell-container">Male</div></td>
										<td class="md-table-cell"><div class="md-table-cell-container">Assistant Media Planner</div></td>
									</tr>
									<tr class="md-table-row">
										<td class="md-table-cell md-numeric"><div class="md-table-cell-container">2</div></td>
										<td class="md-table-cell"><div class="md-table-cell-container">Odette Demageard</div></td>
										<td class="md-table-cell"><div class="md-table-cell-container">odemageard1@spotify.com</div></td>
										<td class="md-table-cell"><div class="md-table-cell-container">Female</div></td>
										<td class="md-table-cell"><div class="md-table-cell-container">Account Coordinator</div></td>
									</tr>
									<tr class="md-table-row"><td class="md-table-cell md-numeric"><div class="md-table-cell-container">3</div></td>
										<td class="md-table-cell"><div class="md-table-cell-container">Vera Taleworth</div></td>
										<td class="md-table-cell"><div class="md-table-cell-container">vtaleworth2@google.ca</div></td>
										<td class="md-table-cell"><div class="md-table-cell-container">Male</div></td>
										<td class="md-table-cell"><div class="md-table-cell-container">Community Outreach Specialist</div></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>');
		});

		Template::build(function (Builder $b) use ($uid) {
			$b->code('md-card')
				->title('Materia卡片')
				->memo('Materia风格表格')
				->content(@'
					<div class="md-card md-theme-default">
						<div class="md-card-header md-card-header-flex">
							<div class="md-card-header-text">
								<div class="md-title">Actions left aligned</div>
								<div class="md-subhead">Subtitle here</div>
							</div>
						</div>
					<div class="md-card-content">Lorem ipsum dolor sit amet, consectet.</div>
				</div>');
		});

		Template::build(function (Builder $b) use ($uid) {
			$b->code('md-button')
				->title('Materia按钮')
				->memo('Materia风格按钮')
				->content(@'
					<div class="md-button md-raised md-primary md-theme-demo-light">
						<div class="md-ripple"><span class="md-button-content">Primary</span></div>
					</div>');
		});

	}
}
