<style type="text/css">
	.analytify_compair_wraper{
		padding: 30px 0;
	}
	.analytify_compair_inner{
		overflow: hidden;
	}
	.analytify_features{
		float: left;
		width: 40%;
		border: 1px solid #e2e5e8;
		background-color: #fff;
		border-top: 6px solid #00c853;
	}
	.analytify_simillar_feature{
		width: 20%;
		width: calc(20% - 6px);
	}
	.analytify_compair_logo_wraper{
		text-align: center;
		height: 250px;
		line-height: 250px;
		position: relative;
	}
	.analytify_compair_logo_wraper img{
		vertical-align: middle;
	}
	.analytify_compair_features{
		padding: 0px;
		margin: 0px;
		border-top: 1px solid #e2e5e8;
	}
	.analytify_compair_features li{
		border-bottom: 1px solid #e2e5e8;
		margin-bottom: 0;
		padding: 25px 30px;
		font-size: 17px;
		color: #848484;
		background-color: #fff;
	}
	.analytify_compair_features li:nth-child(odd){
		background-color: #f9fafa;
	}
	.analytify_features.analytify_simillar_feature{
		border:0;
		background-color: transparent;
		padding-top: 6px;
	}
	.analytify_simillar_label{
		position: absolute;
		bottom: 0px;
		left: 50%;
		transform: translateX(-50%);
		background-color: #fff;
		line-height: 1.4;
		padding: 10px 20px;
		font-size: 17px;
		color: #848484;
		border: 1px solid #e2e5e8;
		border-bottom: 0px solid #e2e5e8;
	}
	.analytify_compair_vs{
		font-size: 60px;
		line-height: 200px;
	}
</style>
<div class="analytify_compair_wraper">
	<div class="analytify_compair_inner">
		<div class="analytify_features">
			<div class="analytify_compair_logo_wraper">
				<img src="<?php echo plugins_url( 'assets/images/logo_free_section.png',dirname( __FILE__ )) ?>">
			</div>
			<ul class="analytify_compair_features">
				<li>Dashboard</li>
				<li>Analytics under Posts (admin)</li>
				<li>Analytify under Pages (admin)</li>
				<li></li>
			</ul>
		</div>
		<div class="analytify_features analytify_simillar_feature">
			<div class="analytify_compair_logo_wraper">
				<div class="analytify_compair_vs">
					VS.
				</div>
				<div class="analytify_simillar_label">
					Similar
				</div>
			</div>
			<ul class="analytify_compair_features">
				<li>Similar</li>
				<li>Similar but limited</li>
				<li>Similar but limited</li>
			</ul>
		</div>
		<div class="analytify_features">
			<div class="analytify_compair_logo_wraper">
				<img src="<?php echo plugins_url( 'assets/images/logo_pro_section.png',dirname( __FILE__ )) ?>">
			</div>
			<ul class="analytify_compair_features">
				<li>Dashboard</li>
				<li>Analytics under Posts (admin)</li>
				<li>Analytify under Pages (admin)</li>
				<li>Support</li>
				<li>Live Stats</li>
				<li>ShortCodes</li>
				<li>Extentions</li>
				<li>Analytify under Custom Post Types (front/admin)</li>
				<li></li>
			</ul>
		</div>
	</div>
</div>