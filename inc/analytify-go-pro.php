<style type="text/css">
	.analytify_compair_wraper{
		max-width: 1100px;
		margin: 0 auto;
		padding: 30px 30px;
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
		margin-bottom: 30px;
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
		padding: 15px 30px;
		font-size: 15px;
		color: #555;
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
	.analytify_go_pro_features{
		border-top-color:#ff5252; 
	}
	.analytify_features.analytify_simillar_feature ul li{
		text-align: center;
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
	.analytify_discount_code{
		text-align: center;
		clear: both;
/*	    background-color: #ddeaff;
	    border-top: 1px solid #ddeaff;
	    border-bottom: 1px solid #ddeaff;*/
	    padding:  40px 0;
	    font: 300 22px 'Roboto', Arial, Helvetica, sans-serif;
	    margin-bottom: 30px;
	}
	.analytify_discount_code span{
		font-weight: 700;
		color: #00c853;
	}
	.analytify_btn_buy{
	    font: 400 25px 'Roboto', Arial, Helvetica, sans-serif;
	    line-height: 1.2;
	    color: #fff;
	    padding: 15px 24px;
	    background: #00c853;
	    box-shadow: 0 2px 3px rgba(0,0,0, .2);
	    border: 0px;
		margin: 0 auto 20px;
		display: block;
		width: 200px;
		text-align: center;
		text-decoration: none;
		border-radius: 4px;
	}
	.analytify_btn_buy:hover{
		color: #fff;
		box-shadow: 0 2px 3px rgba(0,0,0, 0);
	}
</style>
<div class="analytify_compair_wraper">
	<div class="analytify_compair_inner">
		<div class="analytify_features analytify_go_pro_features">
			<div class="analytify_compair_logo_wraper">
				<img src="<?php echo plugins_url( 'assets/images/logo_pro_section.png',dirname( __FILE__ )) ?>">
			</div>
			<ul class="analytify_compair_features">
				<li>Dashboard (Advanced)</li>
				<li>Analytics under Posts (admin)</li>
				<li>Analytics under Pages (admin)</li>
				<li>Comparison Stats (Visitors & Views monthly/yearly)</li>
				<li>Live Stats</li>
				<li>ShortCodes</li>
				<li>Extentions</li>
				<li>Analytics under Custom Post Types (front/admin)</li>
				<li>Ajax & JS Error Stats</li>
				<li>404 Page Error Stats</li>
				<li>Priority Email Support</li>
				<li>No promotional ads</li>
			</ul>
		</div>
		<div class="analytify_features analytify_simillar_feature">
			<div class="analytify_compair_logo_wraper">
				<div class="analytify_compair_vs">
					VS.
				</div>
<!-- 				<div class="analytify_simillar_label">
					Similar
				</div> -->
			</div>
			<ul class="analytify_compair_features">
				<li>Similar but limited</li>
				<li>Similar but limited</li>
				<li>Similar but limited</li>
			</ul>
		</div>


				<div class="analytify_features">
			<div class="analytify_compair_logo_wraper">
				<img src="<?php echo plugins_url( 'assets/images/logo_free_section.png',dirname( __FILE__ )) ?>">
			</div>
			<ul class="analytify_compair_features">
				<li>Dashboard</li>
				<li>Analytics under Posts (admin)</li>
				<li>Analytics under Pages (admin)</li>
				<li>No</li>
				<li>No</li>
				<li>No</li>
				<li>No</li>
				<li>No</li>
				<li>Only Tracking</li>
				<li>Only Tracking</li>
				<li>WordPress.org Forum Support</li>
				<li>Promotional Ads</li>


			</ul>
		</div>
		<div class="analytify_discount_code"> 
			<a href="https://analytify.io/go/PRO" class="analytify_btn_buy">Upgrade Now</a>
		use <span>GOPRO10</span> discount code for 10% OFF </div>
		
	</div>
</div>