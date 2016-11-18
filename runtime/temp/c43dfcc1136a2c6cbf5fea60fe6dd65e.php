<?php if (!defined('THINK_PATH')) exit(); /*a:7:{s:73:"E:\workspace\wh_demo\public/../application/admin\view\rbac\authorize.html";i:1479281218;s:72:"E:\workspace\wh_demo\public/../application/admin\view\public\header.html";i:1479281218;s:70:"E:\workspace\wh_demo\public/../application/admin\view\public\left.html";i:1479281218;s:69:"E:\workspace\wh_demo\public/../application/admin\view\public\top.html";i:1479281218;s:76:"E:\workspace\wh_demo\public/../application/admin\view\public\rightStart.html";i:1479281218;s:74:"E:\workspace\wh_demo\public/../application/admin\view\public\rightEnd.html";i:1479281218;s:72:"E:\workspace\wh_demo\public/../application/admin\view\public\footer.html";i:1479281218;}*/ ?>
<!DOCTYPE html>
<html lang="zh-cn">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>后台管理系统</title>

    <!-- Bootstrap -->
    <link href="/static/admin/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="/static/admin/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="/static/admin/vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- Dropzone.js -->
    <link href="/static/admin/vendors/dropzone/dist/min/dropzone.min.css" rel="stylesheet">
    <!-- iCheck -->
    <link href="/static/admin/vendors/iCheck/skins/flat/green.css" rel="stylesheet">
    <!-- bootstrap-progressbar -->
    <link href="/static/admin/vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet">
    <!-- JQVMap -->
    <link href="/static/admin/vendors/jqvmap/dist/jqvmap.min.css" rel="stylesheet"/>
    <!-- Select2 -->
    <link href="/static/admin/vendors/select2/dist/css/select2.min.css" rel="stylesheet">
    <!-- starrr -->
    <link href="/static/admin/vendors/starrr/dist/starrr.css" rel="stylesheet">
    <!-- Custom Theme Style -->
    <link href="/static/admin/build/css/custom.min.css" rel="stylesheet">
    <!--upload-->
    <link href="/static/admin/build/css/upload.css" rel="stylesheet">
    <!--  -->
    <link href="/static/admin/css/comm.css" rel="stylesheet">
    <!--zyUpload-->
    <link rel="stylesheet" href="/static/admin/js/zyupload/skins/zyupload-1.0.0.min.css" type="text/css">
    <!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
    <link rel="stylesheet" href="/static/admin/js/jQuery-File-Upload/css/jquery.fileupload.css">
    <!-- treetable -->
    <link href="__JS__/treeTable/treeTable.css" rel="stylesheet">
     <!-- validform -->
    <link rel="stylesheet" href="/static/admin/js/validform/css/style.css">
     <!--The jquery.AutoComplete -->
     <link rel="stylesheet" type="text/css" href="/static/admin/vendors/jquery.AutoComplete/jquery.autocomplete.css">
     
  </head>
  <body class="nav-md">
    <div class="container body">
      <div class="main_container">    
       
       
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">
            <div class="navbar nav_title" style="border: 0;">
              <a href="index.html" class="site_title"><!-- <i class="fa fa-paw"></i> --> <span>管理中心</span></a>
            </div>

            <div class="clearfix"></div>

            <!-- menu profile quick info -->
            <div class="profile">
              <div class="profile_pic">
                <img src="/static/admin/images/user.png" alt="..." class="img-circle profile_img">
              </div>
              <div class="profile_info">
                <span>欢迎您！</span>
                <h2><?php echo output($user_ses,'userName'); ?></h2>
              </div>
            </div>
            <!-- /menu profile quick info -->

            <br />
            
            <!-- sidebar menu -->
            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
              <div class="menu_section">
                <h3>&nbsp;</h3>
                <ul class="nav side-menu">
                <li><a href="<?php echo url('/admin/login/index'); ?>"><i class="fa fa-home"></i>总览 </a></li>
                <!-- 菜单 --> 
                <?php if((iset($menus_ses))): if(is_array($menus_ses) || $menus_ses instanceof \think\Collection): $i = 0; $__LIST__ = $menus_ses;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo1): $mod = ($i % 2 );++$i;if(($vo1['parentId']==0 && $vo1['leftShow']==1)): ?>
                             <li><a><i class="fa fa-<?php echo $vo1['menuIcon']; ?>"></i><?php echo $vo1['showName']; ?> <span class="fa fa-chevron-down"></span></a>
                               <ul class="nav child_menu">
                               <?php if((output($vo1,'leftMenus'))): if(is_array($vo1['leftMenus']) || $vo1['leftMenus'] instanceof \think\Collection): $i = 0; $__LIST__ = $vo1['leftMenus'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo2): $mod = ($i % 2 );++$i;if(($vo2['leftShow']==1)): ?>
	                                 <li><a href="<?php echo url($vo2['menuUrl']); ?>" data-routers="<?php echo output($vo2,'dataRouters'); ?>"><?php echo $vo2['showName']; ?></a></li>
	                               <?php endif; endforeach; endif; else: echo "" ;endif; endif; ?>
                               </ul>
                             </li>
	                 	<?php endif; endforeach; endif; else: echo "" ;endif; endif; ?>
                </ul>
                <!-- 删除下面的  -->  

                <!-- <ul class="nav side-menu">
                 
                  <li><a><i class="fa fa-cog"></i>系统设置 <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="<?php echo url('/configure/listPage'); ?>" data-routers="<?php echo url('/configure/listPage'); ?>,<?php echo url('/configure/add'); ?>,<?php echo url('/configure/edit'); ?>" >系统参数设置</a></li>
                      <li><a href="<?php echo url('/menu/listPage'); ?>" data-routers="<?php echo url('/menu/listPage'); ?>,<?php echo url('/menu/add'); ?>,<?php echo url('/menu/edit'); ?>" >后台菜单</a></li>
                     <li><a href="<?php echo url('/admin/rbac/listPage'); ?>" data-routers="<?php echo url('/admin/rbac/listPage'); ?>,<?php echo url('/admin/rbac/add'); ?>,<?php echo url('/admin/rbac/edit'); ?>,<?php echo url('/admin/rbac/authorize'); ?>" >角色管理</a></li>
                     <li><a href="<?php echo url('/admin/adminuser/showlist'); ?>" data-routers="<?php echo url('/admin/adminuser/showlist'); ?>,<?php echo url('/admin/adminuser/add'); ?>,<?php echo url('/admin/adminuser/edit'); ?>,<?php echo url('/admin/adminuser/info'); ?>">管理员</a></li>
                     <li><a href="<?php echo url('/admin/adminandroid/listPage'); ?>" data-routers="<?php echo url('/admin/adminandroid/listPage'); ?>,<?php echo url('/admin/adminandroid/info'); ?>" >一体机权限管理</a></li>
                     <li><a href="<?php echo url('/admin/content/index'); ?>" data-routers="<?php echo url('/admin/content/index'); ?>,<?php echo url('/admin/content/add'); ?>,<?php echo url('/admin/content/edit'); ?>">平台内容管理</a></li>

                    </ul>
                  </li>
                  <li><a><i class="fa fa-group "></i>会员管理 <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="<?php echo url('/admin/user/listPage'); ?>" data-routers="<?php echo url('/admin/user/listPage'); ?>,<?php echo url('/admin/user/info'); ?>,<?php echo url('/order/listPageU'); ?>" >会员列表</a></li>
                      <li><a href="<?php echo url('/admin/message/index'); ?>" data-routers="<?php echo url('/admin/message/add'); ?>,<?php echo url('/admin/message/edit'); ?>" >信息管理</a></li>

                    </ul>
                  </li>
                  <li><a><i class="fa fa-file-text"></i>订单管理 <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="<?php echo url('/admin/order/listPage'); ?>" data-routers="<?php echo url('/admin/order/listPage'); ?>,<?php echo url('/admin/order/info'); ?>" >供灯订单</a></li>
                      <li><a href="<?php echo url('/admin/donation/listPage'); ?>" data-routers="<?php echo url('/admin/donation/listPage'); ?>,<?php echo url('/admin/donation/info'); ?>" >随喜订单</a></li>
                      
                    </ul>
                  </li>
                  
                  <li><a><i class="fa fa-bank"></i>寺院管理 <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="<?php echo url('/temple/index'); ?>" data-routers="<?php echo url('/temple/index'); ?>,<?php echo url('/temple/add'); ?>,<?php echo url('/temple/edit'); ?>,<?php echo url('/buddha_data/index'); ?>,<?php echo url('/buddha_data/add'); ?>,<?php echo url('/buddha_data/edit'); ?>
                      ,<?php echo url('/palace/index'); ?>,<?php echo url('/palace/add'); ?>,<?php echo url('/palace/edit'); ?>,<?php echo url('/mage/index'); ?>,<?php echo url('/mage/add'); ?>,<?php echo url('/mage/edit'); ?>,<?php echo url('/buddha/form'); ?>,
                      <?php echo url('/buddha/listPage'); ?>,<?php echo url('/palace_pic/listPage'); ?>,<?php echo url('/palace_pic/add'); ?>,<?php echo url('/palace_pic/edit'); ?>,<?php echo url('/price/index'); ?>,<?php echo url('/price/add'); ?>,<?php echo url('/price/edit'); ?>,<?php echo url('/seminar/index'); ?>,<?php echo url('/seminar/add'); ?>,<?php echo url('/seminar/edit'); ?>
                      ,<?php echo url('/temple_show/index'); ?>,<?php echo url('/temple_show/add'); ?>,<?php echo url('/temple_show/edit'); ?>,<?php echo url('/worship_type/listPage'); ?>,<?php echo url('/worship_type/form'); ?>">寺院管理</a></li>
                      <li><a href="<?php echo url('/worship_period/index'); ?>" data-routers="<?php echo url('/worship_period/index'); ?>,<?php echo url('/worship_period/add'); ?>,<?php echo url('/worship_period/edit'); ?>">供奉期</a></li>
                    </ul>
                  </li>
                  <li><a><i class="fa fa-bell"></i>灯控制管理<span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="<?php echo url('/android/listPage'); ?>" data-routers="<?php echo url('/android/listPage'); ?>,<?php echo url('/android/form'); ?>">一体机管理</a></li>
                      <li><a href="<?php echo url('/subarea/listPage'); ?>" data-routers="<?php echo url('/subarea/listPage'); ?>,<?php echo url('/subarea/add'); ?>,<?php echo url('/subarea/edit'); ?>">分区管理</a></li>
                      <li><a href="<?php echo url('/lamp_type/index'); ?>" data-routers="<?php echo url('/lamp_type/index'); ?>,<?php echo url('/lamp_type/add'); ?>,<?php echo url('/lamp_type/edit'); ?>">灯类型</a></li>
                      <li><a href="<?php echo url('/ctrler/listPage'); ?>" data-routers="<?php echo url('/ctrler/listPage'); ?>,<?php echo url('/ctrler/add'); ?>,<?php echo url('/ctrler/edit'); ?>">控制器管理</a></li>
                    </ul>
                  </li>
                  <li><a><i class="fa fa-tasks"></i>信息管理 <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="<?php echo url('/worship/listPage'); ?>" data-routers="<?php echo url('/worship/listPage'); ?>" >供奉信息</a></li>
                      <li><a href="<?php echo url('/badword/listPage'); ?>" data-routers="<?php echo url('/badword/listPage'); ?>,<?php echo url('/badword/add'); ?>,<?php echo url('/badword/edit'); ?>" >坏词管理</a></li>
                    </ul>
                  </li>
                  <li><a><i class="fa fa-bar-chart"></i>统计管理 <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="<?php echo url('/stat/light'); ?>" data-routers="<?php echo url('/stat/light'); ?>" >供灯统计</a></li>
                      <li><a href="<?php echo url('/stat/group'); ?>" data-routers="<?php echo url('/stat/group'); ?>" >分组统计</a></li>
                      <li><a href="<?php echo url('/stat/donation'); ?>" data-routers="<?php echo url('/stat/donation'); ?>" >随喜统计</a></li>
                    </ul>
                  </li>
                </ul> -->
                
              </div>

            </div>
            <!-- /sidebar menu -->

            <!-- /menu footer buttons -->
            <div class="sidebar-footer hidden-small">
              <!-- <a data-toggle="tooltip" data-placement="top" title="Settings">
                <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="FullScreen">
                <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="Lock">
                <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
              </a> -->
              <a href="<?php echo url('/login/clearcache'); ?>" data-toggle="tooltip" data-placement="top" title="清除网站缓存">
                <span class="fa fa-refresh" aria-hidden="true"></span>
              </a>
              <a href="<?php echo url('/login/loginout'); ?>" data-toggle="tooltip" data-placement="top" title="退出">
                <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
              </a>
            </div>
            <!-- /menu footer buttons -->
          </div>
        </div>
        
                <div class="top_nav">
          <div class="nav_menu">
            <nav>
              <div class="nav toggle">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
              </div>

              <ul class="nav navbar-nav navbar-right">
                <li class="">
                  <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                     <img src="/static/admin/images/user.png" alt=""> <?php echo output($user_ses,'userName'); ?>
                    <span class=" fa fa-angle-down"></span>
                  </a>
                  <ul class="dropdown-menu dropdown-usermenu pull-right">
                    <!-- <li><a href="javascript:;"> Profile</a></li>
                    <li>
                      <a href="javascript:;">
                        <span class="badge bg-red pull-right">50%</span>
                        <span>Settings</span>
                      </a>
                    </li>
                    <li><a href="javascript:;">Help</a></li> -->
                    <li><a href="<?php echo url('/admin/login/loginout'); ?>"><i class="fa fa-sign-out pull-right"></i> 退出</a></li>
                  </ul>
                </li>
				  
                <!-- <li role="presentation" class="dropdown">
                  <a href="javascript:;" class="dropdown-toggle info-number" data-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-envelope-o"></i>
                    <span class="badge bg-green">6</span>
                  </a>
                  <ul id="menu1" class="dropdown-menu list-unstyled msg_list" role="menu">
                    <li>
                      <a>
                        <span class="image"><img src="/static/admin/images/img.jpg" alt="Profile Image" /></span>
                        <span>
                          <span>John Smith</span>
                          <span class="time">3 mins ago</span>
                        </span>
                        <span class="message">
                          Film festivals used to be do-or-die moments for movie makers. They were where...
                        </span>
                      </a>
                    </li>
                    <li>
                      <a>
                        <span class="image"><img src="/static/admin/images/img.jpg" alt="Profile Image" /></span>
                        <span>
                          <span>John Smith</span>
                          <span class="time">3 mins ago</span>
                        </span>
                        <span class="message">
                          Film festivals used to be do-or-die moments for movie makers. They were where...
                        </span>
                      </a>
                    </li>
                    <li>
                      <a>
                        <span class="image"><img src="/static/admin/images/img.jpg" alt="Profile Image" /></span>
                        <span>
                          <span>John Smith</span>
                          <span class="time">3 mins ago</span>
                        </span>
                        <span class="message">
                          Film festivals used to be do-or-die moments for movie makers. They were where...
                        </span>
                      </a>
                    </li>
                    <li>
                      <a>
                        <span class="image"><img src="/static/admin/images/img.jpg" alt="Profile Image" /></span>
                        <span>
                          <span>John Smith</span>
                          <span class="time">3 mins ago</span>
                        </span>
                        <span class="message">
                          Film festivals used to be do-or-die moments for movie makers. They were where...
                        </span>
                      </a>
                    </li>
                    <li>
                      <div class="text-center">
                        <a>
                          <strong>See All Alerts</strong>
                          <i class="fa fa-angle-right"></i>
                        </a>
                      </div>
                    </li>
                  </ul>
                </li> -->
              </ul>
            </nav>
          </div>
        </div><div class="right_col" role="main">

<!--  -->
<div class="">
    <div class="page-title">
	<div class="title_left">
        <h3><?php echo output($menu_now_ses,'parentShowName'); ?></h3>
	</div>

	<div class="title_right">
        <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
	       <div class="input-group"></div>
	   </div>
    </div>
</div>
<div class="clearfix"></div>
	<div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2><?php echo output($menu_now_ses,'showName'); ?></h2>
                    <div class="clearfix"></div>
                </div>


<style>



.expanded{margin-left: -20px;}

</style>




                <div class="x_content">
                    <div class="x_content">
                    <br>
                    <form id="role-info-submit"  class="form-horizontal form-label-left" >

                    <div class="table_full">
					<table width="100%" cellspacing="0" id="dnd-example">
							<tbody>
								<?php echo $categorys; ?>
							</tbody>
					</table>
					</div>


                      <div class="ln_solid"></div>
                      <div class="form-group">
                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                          <input type="hidden" name="roleid" value="<?php echo $roleid; ?>" />
                          <button type="submit" class="btn btn-primary">提交</button>
                          <a href="javascript:history.back(-1)" class="btn btn-success">返回</a>
                        </div>
                      </div>

                    </form>
                  </div>
                </div>





        </div>  
	</div>
</div>



<!--  -->

</div>        </div>
        <footer>
            <div class="pull-right">
           
            </div>
            <div class="clearfix"></div>
        </footer>
        <!-- /footer content -->
      </div>
    </div>

    <!-- jQuery -->
    <script src="/static/admin/vendors/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="/static/admin/vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- FastClick -->
    <script src="/static/admin/vendors/fastclick/lib/fastclick.js"></script>
    <!-- NProgress -->
    <script src="/static/admin/vendors/nprogress/nprogress.js"></script>
    <!-- Chart.js -->
    <script src="/static/admin/vendors/Chart.js/dist/Chart.min.js"></script>
    <!-- gauge.js -->
    <script src="/static/admin/vendors/gauge.js/dist/gauge.min.js"></script>
    <!-- bootstrap-progressbar -->
    <script src="/static/admin/vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>
    <!-- iCheck -->
    <script src="/static/admin/vendors/iCheck/icheck.min.js"></script>
    <!-- Skycons -->
    <script src="/static/admin/vendors/skycons/skycons.js"></script>
    <!-- Flot -->
    <script src="/static/admin/vendors/Flot/jquery.flot.js"></script>
    <script src="/static/admin/vendors/Flot/jquery.flot.pie.js"></script>
    <script src="/static/admin/vendors/Flot/jquery.flot.time.js"></script>
    <script src="/static/admin/vendors/Flot/jquery.flot.stack.js"></script>
    <script src="/static/admin/vendors/Flot/jquery.flot.resize.js"></script>
    <!-- Flot plugins -->
    <script src="/static/admin/js/flot/jquery.flot.orderBars.js"></script>
    <script src="/static/admin/js/flot/date.js"></script>
    <script src="/static/admin/js/flot/jquery.flot.spline.js"></script>
    <script src="/static/admin/js/flot/curvedLines.js"></script>
    <!-- JQVMap -->
    <script src="/static/admin/vendors/jqvmap/dist/jquery.vmap.js"></script>
    <script src="/static/admin/vendors/jqvmap/dist/maps/jquery.vmap.world.js"></script>
    <script src="/static/admin/vendors/jqvmap/examples/js/jquery.vmap.sampledata.js"></script>
    <!-- bootstrap-daterangepicker -->
    <script src="/static/admin/js/moment/moment.min.js"></script>
    <script src="/static/admin/js/datepicker/daterangepicker.js"></script>
    <!-- Dropzone.js -->
    <script src="/static/admin/vendors/dropzone/dist/min/dropzone.min.js"></script>
    <!-- ajaxUpload -->
    <script src="/static/admin/build/js/ajaxUpload.js"></script>
    <!-- Validator -->
    <script src="/static/admin/vendors/validator/validator.js"></script>
    <!-- Custom Theme Scripts -->
    <script src="/static/admin/build/js/custom.js"></script>
    <script src="/static/admin/vendors/bootstrap-wysiwyg/js/bootstrap-wysiwyg.min.js"></script>
    <script src="/static/admin/vendors/jquery.hotkeys/jquery.hotkeys.js"></script>
    <script src="/static/admin/vendors/google-code-prettify/src/prettify.js"></script>
    <!-- jQuery Tags Input -->
    <script src="/static/admin/vendors/jquery.tagsinput/src/jquery.tagsinput.js"></script>
    <!-- Switchery -->
    <script src="/static/admin/vendors/switchery/dist/switchery.min.js"></script>
    <!-- Select2 -->
    <script src="/static/admin/vendors/select2/dist/js/select2.full.min.js"></script>
    <!-- Parsley -->
    <script src="/static/admin/vendors/parsleyjs/dist/parsley.min.js"></script>
    <!-- Autosize -->
    <script src="/static/admin/vendors/autosize/dist/autosize.min.js"></script>
    <!-- jQuery autocomplete -->
    <script src="/static/admin/vendors/devbridge-autocomplete/dist/jquery.autocomplete.min.js"></script>
    <!-- Select2 -->
    <script src="/static/admin/vendors/select2/dist/js/select2.full.min.js"></script>
    <!-- starrr -->
    <script src="/static/admin/vendors/starrr/dist/starrr.js"></script>
    <!-- layer.js -->
    <script src="/static/admin/js/layer/layer.js"></script>
    <!-- laydate -->
    <script src="/static/admin/vendors/laydate/laydate.js"></script>
    <!-- xxx -->
    <script type="text/javascript" src="/static/admin/js/xxx/core.js"></script>
    <!--zyupload-->
    <script type="text/javascript" src="/static/admin/js/zyupload/zyupload.basic-1.0.0.min.js"></script>
    <!--uploadify-->
    <script type="text/javascript" src="/static/admin/js/uploadify/jquery.uploadify.min.js"></script>
    <script src="/static/admin/js/common.js"></script>
     <!-- Wind.js -->
    <script type="text/javascript">
	//wind.js变量
	var GV = {
	    DIMAUB: "__ROOT__",
	    JS_ROOT: "__JS__/",
	    TOKEN: ""
	};
	</script>
    <script src="/static/admin/js/wind.js"></script>
    <script src="/static/admin/js/treeTable/treeTable.js"></script>
    
    <!-- The Load Image plugin is included for the preview images and image resizing functionality -->
    <script src="/static/admin/js/JavaScript-Load-Image/js/load-image.all.min.js"></script>
    <!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
    <script src="/static/admin/js/jQuery-File-Upload/js/jquery.iframe-transport.js"></script>
    <!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
    <script src="/static/admin/js/jQuery-File-Upload/js/vendor/jquery.ui.widget.js"></script>
    <!-- The basic File Upload plugin -->
    <script src="/static/admin/js/jQuery-File-Upload/js/jquery.fileupload.js"></script>
    <!-- The File Upload processing plugin -->
    <script src="/static/admin/js/jQuery-File-Upload/js/jquery.fileupload-process.js"></script>
    <!-- The File Upload image preview & resize plugin -->
    <script src="/static/admin/js/jQuery-File-Upload/js/jquery.fileupload-image.js"></script>
    <!-- The File Upload audio preview plugin -->
    <script src="/static/admin/js/jQuery-File-Upload/js/jquery.fileupload-audio.js"></script>
    <!-- The File Upload video preview plugin -->
    <script src="/static/admin/js/jQuery-File-Upload/js/jquery.fileupload-video.js"></script>
    <!-- The File Upload validation plugin -->
    <script src="/static/admin/js/jQuery-File-Upload/js/jquery.fileupload-validate.js"></script>
      <!-- The ueditor plugin -->
	<script type="text/javascript" src="/static/admin/js/ueditor/ueditor.config.js"></script>
	<script type="text/javascript" src="/static/admin/js/ueditor/ueditor.all.min.js"></script>
	<script type="text/javascript"  src="/static/admin/js/ueditor/lang/zh-cn/zh-cn.js"></script>
	<script type="text/javascript" >
	  window.UEDITOR_HOME_URL = "/static/admin/js/ueditor"; //UEDITOR_HOME_URL
	</script>
    <!--The validform -->
	<script type="text/javascript"  src="/static/admin/js/validform/js/Validform_v5.3.2_ncr_min.js"></script>
	
    <!--The jquery.AutoComplete -->
	<script type="text/javascript"  src="/static/admin/vendors/jquery.AutoComplete/jquery.autocomplete.min.js"></script>
	
	
  </body>
</html>
<script type="text/javascript">


var _isadd=0;
$("#role-info-submit").on("submit", function(){
	if(_isadd==1){layer.msg('提交中...请稍等片刻！');return false;}
	_isadd=1;
    $.ajax({
        url:"<?php echo url('/admin/rbac/authorize_post'); ?>",
        data:$(this).serializeArray(),
        type:"POST",
        dataType:"json",
        success: function(v){
        	_isadd=0;
        	layer.msg(v.msg) ;
			_isld=0;
			if(v.code==200){
				setTimeout(function(){window.location.href="<?php echo url('/admin/rbac/listPage'); ?>";},1000);
			}

        }
    });
    return false;
});

$(document).ready(function () {
	Wind.css('treeTable');
    Wind.use('treeTable', function () {
        $("#dnd-example").treeTable({
            indent: 20
        });
    });
});

function checknode(obj) {
    var chk = $("input[type='checkbox']");
    var count = chk.length;
    var num = chk.index(obj);
    var level_top = level_bottom = chk.eq(num).attr('level');
    //console.log(num);
    //console.log(level_top);
    for (var i = num; i >= 0; i--) {
        var le = chk.eq(i).attr('level');
        
        if (eval(le) < eval(level_top)) {
            chk.eq(i).prop("checked", true);
            var level_top = level_top - 1;
        }
    }
    for (var j = num + 1; j < count; j++) {
        var le = chk.eq(j).attr('level');
       // console.log(chk.eq(num).is(':checked'));
        
        if (chk.eq(num).is(':checked') == true) {
        //console.log(le);
            if (eval(le) > eval(level_bottom)){
            	chk.eq(j).prop("checked", true);
            }
            else if (eval(le) == eval(level_bottom)){
            	break;
            }
        } else {
            if (eval(le) > eval(level_bottom)){
            	chk.eq(j).prop("checked", false);
            }else if(eval(le) == eval(level_bottom)){
            	break;
            }
        }
    }
}

/* 
$("#dnd-example").on("change", "input[type='checkbox']", function(){

	var $this = $(this).closest("tr"),
    isChecked = $(this).prop("checked"),
	   level = $(this).attr("level"),

    //   筛选子集
  $child = $this.siblings().filter(function(){

		return $(this).find("input[type='checkbox']").attr("level") - level == 1 && $(this).attr("class").split(" ")[0].indexOf($this.attr("id")) !== -1;

	});
  // 给子集选中效果
  $child.find("input[type='checkbox']").prop("checked",isChecked);

}) */

</script>
