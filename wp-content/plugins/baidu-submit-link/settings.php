<style>
    .form-table th, .form-wrap label{font-size:12px;color:#333;}
    .form-table th{width:120px;}
    .locale-zh-cn p.description{font-size:12px;}
    .adside{display:block; width:350px; padding-top:30px; margin-left:20px;}
    .adside a{display:block; text-decoration:none; background-color:#ccc; margin:0 5px 10px 5px; text-align:center;color:#fff;}
    .adside .ad1,.adside .ad2{width:150px; line-height:150px; float:left;}
    .adside .ad3{width:260px; line-height:20px; clear:both;}
    .adside .ad4{width:310px; line-height:60px;}
    .admain{overflow:hidden; }
</style>
<div class="wrap" id="poststuff">
	<h1>百度URL推送设置</h1>
	<form method="post" action="options.php">
		<?php 
		settings_fields( $setting_field );
		 ?>
        <!-- 基本设置 S-->
        <div class="postbox-container">
            <div class="postbox">
                <h3 class="hndle">
                    <span>基本设置</span>
                </h3>
                <div class="inside">
                    <table class="form-table">
                        <tr valign="top">
                            <th>
                                <label for="<?php echo $option_name;?>_token">
                                    准入密钥
                                </label>
                            </th>
                            <td>
                                <input type="text"
                                       id="<?php echo $option_name;?>_token"
                                       name="<?php echo $option_name;?>[token]"
                                       class="regular-text"
                                       value="<?php echo isset($op_sets['token'])?$op_sets['token']:'';?>" />
                                <p class="description"><a href="http://zhanzhang.baidu.com/badlink/index?site=">访问百度站长平台</a>申请或获取URL准入密钥</p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <!-- 基本设置 E-->


        <div class="postbox-container">
            <p class="submit">
                <input type="submit"
                       name="Submit"
                       class="button-primary"
                       value="保存" />
            </p>
        </div>
	</form>
</div>

