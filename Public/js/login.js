define(["jquery"], function($){
	var data={
		collapse:"collapse",
		uncollapse:"uncollapse",
		$baseVersionName:$("#baseVersionName"),
		$versionName:$("#versionName"),
		timer:'',//用于记录 单击的settimeout
		selected:"timeline-yellow",
		unSelected:"timeline-green",//用于看是选中还是没选中
		$selectedDir:$("#selectedDir")
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
			$(document).on("dblclick", ".newEdition", this.setSelected);//newEdition  大版本才会有的 class名
			$(document).on("click", "#baseVersion", this.newBaseVersion);//新建大版本
			$(document).on("click", "#version", this.newVersion);//新建小版本
			$(document).on("click, dblclick", ".edition", function(e){e.stopPropagation();});//点击小版本不触发 取子级操作
		},
		showRemark:function(e){
			//console.log($(this).closest("li").hasClass("edition"))
			if($(this).closest("li").hasClass("edition")){
				//不需要 得到子级文件夹内容
				e.stopPropagation();
				//newEdition还会调用getSubVersions
				//
				var length = $(this).closest(".timeline-body").children(".timeline-content").length;
				if(length==0){
					//说明是第一次获取 是edition的
					var $this=$(this);
					var remarkID=$(this).data("id");
					var remarkFolder=$(this).data("parent");//从该文件夹取备注文件
					var data={"refer":"getRemark", "remarkID":remarkID, remarkFolder:remarkFolder}
					$.post("Util.php", data, function(data){
						$this.closest(".timeline-body").append(data);		
					})
				}else{
					//newEdition的下拉 或 edition的非首次取
					$(this).closest(".timeline-body").children(".timeline-content").show();
					e.stopPropagation();
				}
			}else{
				if(!$(this).closest("li").hasClass("edition")){
					//不需要 得到子级文件夹内容
					if($(this).closest(".newEdition").children("li").length!=0){
						$(this).closest(".newEdition").children("li").show();
						e.stopPropagation();
					}
				}
				
			}
			$(this).toggleClass("collapse").toggleClass("uncollapse");
			$(this).siblings(".save").show();
			$(this).siblings(".edit").show();
			//显示备注
			//$(this).closest(".portlet-title").siblings(".timeline-content").toggle();
			
			
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
			//大版本的
			//console.log($(this).closest(".edition").length)
			if($(this).closest(".edition").length==0){
				//console.log($(this).attr("class"))
				$(this).closest(".newEdition").children("li").hide();
			}else{
				//小版本的
				$(this).siblings(".save").hide();
				$(this).siblings(".edit").hide();
				$(this).closest(".timeline-body").children(".timeline-content").hide();
			}
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
			var $this=$(this);
			$(this).find(".collapse:not(.edition .collapse)").removeClass("collapse").addClass("uncollapse");
			clearTimeout(data.timer);
			data.timer=setTimeout(function(){//这里的$(this)是window
				//console.log($this)
				//获得大版本下的子版本
				var curDir=$this.find(".timeline-body>h2").text();
				//console.log(curDir)
				var path=$this.data("dir")+"/"+curDir;
				var contat="&nbsp;&nbsp;&nbsp;&nbsp;";
				//$this=$(this);
				//console.log($this)
				var data={"preDir":path, "prelayer":"", "contat":contat, refer:"ajax", class:"", parent:curDir}
				$.post(
					"IteratorUtil.php", data, function(data){
						//console.log(data)
						$this.append(data);
					}
				);
			}, 300)
			
		},
		setSelected:function(){
			//双击 选中目录
			clearTimeout(data.timer);
			//$(this).toggleClass(data.selected).toggleClass(data.unSelecteded);
			$(this).toggleClass("timeline-yellow").toggleClass("timeline-green");
			if($(this).hasClass(data.selected)){
				//现在处于选中状态就 用hidden框记录选中的哪个目录，方便建立小版本到该目录
				data.$selectedDir.val($(this).data("dir"));
			}else{
				data.$selectedDir.val("");
			}
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
			var dat={"versionName":versionName, "refer":"newBaseVersion", "targetDir":APP.PATHROOT, 'resourceDir':APP.RESROOT};
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