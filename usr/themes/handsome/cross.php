<?php
/**
* 时光机
*
* @package custom
*/
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('component/header.php');
function echoTimeMemoryItem($i,$month,$content){
    $color = array("b-light","b-info","b-dark","b-success","b-black","b-warning","b-primary","b-danger","");
    echo <<<EOF
<div class="sl-item {$color[($i -1) % 9 ]} b-l">
                                <div class="m-l">
                                    <div class="text-muted">{$month} 月前</div>
                                    <p>{$content}</p>
                                </div>
                            </div>
EOF;
}
?>


	<!-- aside -->
	<?php $this->need('component/aside.php'); ?>
	<!-- / aside -->

  <!-- content -->
<!-- <div id="content" class="app-content"> -->
    <a class="off-screen-toggle hide"></a>
  	<main class="app-content-body <?php  Content::returnPageAnimateClass($this); ?>">
        <div class="hbox hbox-auto-xs hbox-auto-sm">
            <div class="col center-part">
            <iframe src="/about/aboutme.html" style="width: 100%; height: 1386px;" frameborder="0" scrolling="no" id="aboutPage"></iframe>
                <div class="wrapper bg-white">
                    <ul class="nav nav-pills nav-sm" id="time-tabs">
                        <li class="active"><a href="#talk" role="tab" data-toggle="tab" aria-expanded="true"><?php _me("我的动态") ?></a></li>
                        <?php

                        $json = '['.Typecho_Widget::widget('Widget_Options')->rssItems.']';
                        $rssItems = json_decode($json);
                        $tabPanes = "";
                        foreach ($rssItems as $rssItem) {
                            $itemId = $rssItem->id;
                            $itemUrl = $rssItem->url;
                            $itemName = $rssItem->name;
                            @$itemType = $rssItem->type;
                            @$itemImg = $rssItem->img;
                            echo Content::returnTimeTab($itemId,$itemName,$itemUrl,$itemType,$itemImg);
                            $tabPanes .= Content::returnTimeTabPane($itemId);
                        }
                        ?>
                    </ul>
                </div>

                <div class="tab-content">
                    <div id="talk" class="padder tab-pane  fade in active">
                        <?php $this->need('component/say.php') ?>
                    </div><!--end of #pedder-->

                    <?php echo $tabPanes; ?>
                </div>
            </div><!--end of .center-part -->
        </div>
	</main>

<script>

    var timeTemple = '<div class="m-l-n-md">\n' +
        '          <a class="pull-left thumb-sm avatar">\n' +
        '            <img class="img-square" src="{IMG_AVATAR}">\n' +
        '          </a>          \n' +
        '          <div class="time-machine m-l-xxl panel">\n' +
        '            <div class="panel-heading pos-rlt">\n' +
        '              <span class="arrow left pull-up"></span>\n' +
        '              <span class="text-muted m-l-sm pull-right">\n' +
        '                {TIME}\n' +
        '              </span>\n' +
        '              {CONTENT}</div><div class="text-muted say_footer panel-footer">\n' +
        '                <a target="_blank" href="{LINK}" class="text-muted m-xs"><i class="iconfont icon-redo"></i>&nbsp;&nbsp;查看全文</a>\n' +
        '              </div>' +
        '          </div>' +
        '        </div>';

    $('#time-tabs').find('a').click(function (e) {
        var object = $(this);
        var rss = $(this).data("rss");
        var id = $(this).data("id");
        var flag = $(this).attr("data-status");
        var type = $(this).data("type");
        var img = $(this).data("img");
        // console.log(flag);
        // console.log(rss);
        if ('undefined' !== rss && 'undefined' !== id && flag === "false"){
            //动态加载内容
            handsome_util.addScript(LocalConst.BASE_SCRIPT_URL + "assets/js/features/jFeed.min.js","feed_js",function () {
                $.getFeed({
                    url: rss,
                    success: function(feed){
                        $.each(feed.items,function(i,item){

                            var date = new Date(Date.parse(item.updated));
                            Y = date.getFullYear() + '-';
                            M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + '-';
                            D = date.getDate() + ' ';
                            h = date.getHours() + ':';
                            m = date.getMinutes();
                            date=Y+M+D+h+m;
                            var itemContent="";
                            if (type!==""){
                                if (type === "title"){
                                    itemContent = item.title;
                                }else if (type === "description"){
                                    itemContent = item.description;
                                }else {
                                    itemContent = item.description;
                                }
                            }else {
                                itemContent = item.description;
                            }
                            if (img ===""){
                                img = "<?php $this->options->BlogPic(); ?>";
                            }
                            if (date === "NaN-NaN-NaN NaN:NaN"){
                                date = "";
                            }
                            var content=timeTemple.
                            replace("{TIME}",date).
                            replace("{CONTENT}",itemContent).
                            replace("{LINK}",item.link).
                            replace("{IMG_AVATAR}",img);


                            $("#"+id).find(".comment-list").append(content);
                            $("#"+id).find(".streamline").removeClass("hide");
                            $("#"+id).find(".loading-nav").addClass("hide");
                            object.attr("data-status","true");

                            /*lightGallery  */
                            handsome_content.seFancyBox();

                        });
                    },
                    error: function (feed) {
                        $("#"+id).find(".loading-nav").addClass("hide");
                        $("#"+id).find(".error-nav").removeClass("hide");
                    }
                });
            });
        }
    });
    $("#time-upload").bind("click",function () {
        $("#time_file").trigger("click");

    });

    /*监听文件上传框*/
    $("#time_file").bind("change",function () {
        if (!$(this).val()) {
            $("#file-info").html("没有选择文件");
            return;
        }


        var input = $('#time_file');
        // 相当于： $input[0].files, $input.get(0).files
        var files = input.prop('files');
        // console.log(files);
        //判断文件类型
        if (files[0].type!=="image/jpeg" && files[0].type!=="image/png" && files[0].type!=="image/gif"){
            $("#file-info").val("错误的文件类型！" + files[0].type);
            return;
        }
        var suffix = "." + files[0].type.slice(6);
        // console.log(suffix);
        //开始上传文件
        var file = files[0];
        var reader = new FileReader();
        reader.onload = function(e) {
            var data = e.target.result;//base64 加密后的图片数据
            var content = new FormData();
            content.append("action","upload_img");
            content.append("file",data);
            content.append("time_code",'<?php echo md5($this->options->time_code) ?>');

            content.append("suffix",suffix);

            
            $.ajax({
                url: "?action=upload_img",
                type: 'post',
                data: content,
                cache: false,
                processData: false,
                contentType: false,
                success: function (data) {
                    data = JSON.parse(data);
                    $("#time-upload").text("选择文件");
                    $("#time-upload").attr("disabled",false);
                    $("input[ name='imageInsertModal']").val(data.data);//插入返回的图片地址

                }, error: function (jqXHR, textStatus, errorThrown) {
                    $("#time-upload").attr("disabled",false);
                    $("#time-upload").text("选择文件");
                    $("#file-info").val($("#file-info").val() + "上传失败" + textStatus);
                }
            });
        };

        // data.append('data', "2333");
        $("#file-info").val(files[0].name);
        $("#time-upload").text("正在上传");
        $("#time-upload").attr("disabled",true);
        reader.readAsDataURL(file);
    })

</script>

    <!-- footer -->
	<?php $this->need('component/footer.php'); ?>
  	<!-- / footer -->
