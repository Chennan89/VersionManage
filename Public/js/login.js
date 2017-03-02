define(["jquery"], function($){
	var data={
		collapse:"collapse",
		uncollapse:"uncollapse",
		$baseVersionName:$("#baseVersionName"),
		$versionName:$("#versionName"),

	},
	methods={
		init:function(){
			this.getVersions();
			this.common();
		},
		common:function(){
			$(document).on("click", ".collapse", this.showRemark);
			$(document).on("click", ".uncollapse", this.hideRemark);
			$(document).on("click", ".edit", this.editRemark);
			$(document).on("click", ".save", this.saveRemark);

			$(document).on("click", ".newEdition", this.getSubVersions);//newEdition  大版本才会有的 class名
			$(document).on("click", "#baseVersion", this.newBaseVersion);//新建大版本
			$(document).on("click", "#version", this.newVersion);//新建小版本
			$(document).on("click", ".edition", function(e){e.stopPropagation();});//点击小版本不触发 取子级操作
		},
		showRemark:function(e){
			//console.log($(this).closest("li").hasClass("edition"))
			if($(this).closest("li").hasClass("edition")){
				e.stopPropagation();
			}
			//console.log(this);//返回一个对象
			//console.log($(this));//返回
			$(this).toggleClass("collapse").toggleClass("uncollapse");
			$(this).siblings(".save").toggle();
			$(this).siblings(".edit").toggle();
			//显示备注
			//$(this).closest(".portlet-title").siblings(".timeline-content").toggle();
			var length = $(this).closest(".timeline-body").children(".timeline-content").length;
			if(length==0){
				//说明是第一次获取
				var $this=$(this);
				var remarkID=$(this).data("id");
				var remarkFolder=$(this).data("parent");//从该文件夹取备注文件
				var data={"refer":"getRemark", "remarkID":remarkID, remarkFolder:remarkFolder}
				$.post("Util.php", data, function(data){
					$this.closest(".timeline-body").append(data);		
				})
			}else{
				$(this).closest(".timeline-body").children(".timeline-content").show();
			}
			
		},
		saveRemark:function(e){
			e.stopPropagation();
			var $this=$(this);
			var remarkID=$(this).siblings(".arrow").data("id");
			var remarkFolder=$(this).siblings(".arrow").data("parent");//从该文件夹取备注文件
			var remark=$(this).closest(".portlet-title").siblings(".timeline-content").text();
			var data={"refer":"setRemark", "remarkID":remarkID, remarkFolder:remarkFolder, remark:remark}
			
			$.post("Util.php", data, function(data){
				$this.closest(".timeline-body").append(data);
				if(data.code==200){
					alert(data.msg);
				}		
			}, "json")
		},
		hideRemark:function(e){
			e.stopPropagation();
			$(this).toggleClass("collapse").toggleClass("uncollapse");
			$(this).siblings(".save").toggle();
			$(this).siblings(".edit").toggle();
			$(this).closest(".timeline-body").children(".timeline-content").hide();
		},
		editRemark:function(e){
			e.stopPropagation();
			$(this).closest(".portlet-title").siblings(".timeline-content").attr("contentEditable", "true").focus();
		},
		getVersions:function(){
			//得到所有版本信息
			var path="c:/wamp/www/Versions";
			var data={"preDir":path, "prelayer":"", "contat":"", refer:"ajax", class:"newEdition"}
			$.post(
				"IteratorUtil.php", data, function(data){
					//console.log(data)
					$(".timeline").append(data);
				}
			);
		},
		getSubVersions:function(){
			//获得大版本下的子版本
			var curDir=$(this).find(".timeline-body>h2").text();
			//console.log(curDir)
			var path=$(this).data("dir")+"/"+curDir;
			var contat="&nbsp;&nbsp;&nbsp;&nbsp;";
			$this=$(this);
			//console.log($this)
			var data={"preDir":path, "prelayer":"", "contat":contat, refer:"ajax", class:"", parent:curDir}
			$.post(
				"IteratorUtil.php", data, function(data){
					//console.log(data)
					$this.append(data);
				}
			);
		},
		newBaseVersion:function(){
			//新建基础版本（大版本）
			//1. 自动创建该大版本的文件夹比如../Versions/VN, 并自动copy当前的luatest文件夹到新建的VN下
			//2. 自动在目录../Versions/remarks中新建一个VN_remarks.php的文件用于保存该大版本下的所有版本的备注信息
			var versionName = data.$baseVersionName.val();
			if(!versionName){
				alert("版本名不能空");
				return false;
			}
			var dat={"versionName":versionName, "refer":"newBaseVersion", "targetDir":APP.PATHROOT};
			$.post(
				"Util.php", dat, function(data){
					console.log(data)
					alert(data.msg);
					//刷新页面
					window.location.href="index.php";
				}, "json"
			);
		},
		newVersion:function(){
			//新建 小版本
			if(!data.$versionName.val()){
				alert("版本名不能空");
				return false;
			}

		}
	};
	
	methods.init();
	
});