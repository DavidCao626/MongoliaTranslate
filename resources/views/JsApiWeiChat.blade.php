<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    <META HTTP-EQUIV="Cache-Control" CONTENT="no-cache,no-store, must-revalidate">
    <META HTTP-EQUIV="pragma" CONTENT="no-cache">
    <META HTTP-EQUIV="expires" CONTENT="0">

    <script type="text/javascript" src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <title>订单支付</title>
    <script type="text/javascript">
		 function close() {
			  WeixinJSBridge.call('closeWindow');
		 }
		 function pushHistory() {
			  var state = {
					 title: "title",
					  url: "#"
				};
			 window.history.pushState(state, "title", "#");
		  }
		 
		 if (typeof window.addEventListener != "undefined") {
			 window.addEventListener("popstate", function (e) {
				 WeixinJSBridge.call('closeWindow');
			 }, false);
		 } else {
			 window.attachEvent("popstate", function (e) {
				 WeixinJSBridge.call('closeWindow');
			 });
		 }
		  pushHistory();
        //调用微信JS api 支付
        function jsApiCall()
        {
            WeixinJSBridge.invoke(
                    'getBrandWCPayRequest',
                    <?= $json ?>,
                    function(res){
                        WeixinJSBridge.log(res.err_msg);
                        //	alert(res.err_code+res.err_desc+res.err_msg);

                        var sres = res.err_msg.split(':');
                        if(sres[1]=="cancel"){

                            document.getElementById("light").style.display="block";
                            document.getElementById("fade").style.display="block";
                        }
                        if(sres[1]=="ok"){
                            //$("#new_c").html("数据处理中...");
                            //$("#new_c").html("正在跳转...");
                            document.getElementById('light2').style.display='block';
                            document.getElementById('fade2').style.display='block';
                        }
                        if(sres[1]=="fail"){
                            document.getElementById('light3').style.display='block';
                            document.getElementById('fade3').style.display='block';
                        }
                    }
            );
        }
        function callpay()
        {
            if (typeof WeixinJSBridge == "undefined"){
                if( document.addEventListener ){
                    document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
                }else if (document.attachEvent){
                    document.attachEvent('WeixinJSBridgeReady', jsApiCall);
                    document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
                }
            }else{
                jsApiCall();
            }
        }
        window.onload = function(){
			//if(sessionStorage.iszhifu==1){
			/*	alert("您刚刚已经成功支付了一笔订单,如您再次续费需要关闭本微信页面,重新进入");
					$("#new_c").text("请手动关闭本窗口");
		     	 WeixinJSBridge.call('closeWindow');
				WeixinJSBridge.invoke('closeWindow',{},function(res){

					if(res.err_msg=="close_window:error"){
					alert("关闭失败，请手动关闭");
					}

				});
			
				return  false;*/
		//	}else{
				var a="<?php echo $out_orderid;?>";
				var b=document.getElementById(''+a+'').value;
				if(b=='yes'){
					document.getElementById('new_c').style.display='none';
					document.getElementById('light4').style.display='block';
					document.getElementById('fade4').style.display='block';
				}else{
					callpay();
				}
			//}
        };
        function gback(){
            document.getElementById('light').style.display='none';
            document.getElementById('fade').style.display='none';
            window.history.back();
        }
      


</script>

    <style>
        .black_overlay{
            display: none;
            position: absolute;
            top: 0%;  left: 0%;  width: 100%;
            height: 100%;  background-color: black;
            z-index:1001;  -moz-opacity: 0.8;  opacity:.80;
            filter: alpha(opacity=80);
        }
        .white_content {
            display: none;  position: absolute;
            top: 25%;  left: 20%;  width: 50%;  height: 22%;
            padding: 16px;  border: 8px solid #F0EFF5;
            background-color: white;  z-index:1002;
            overflow: auto;
        }
    </style>
</head>

<body>

<input type="hidden" id="<?php echo $out_orderid;?>" value="<?php echo SESSION($out_orderid);?>">
<input type="hidden" id="openid" value="<?php echo $openId;?>" />
<input type="hidden" id="orderid" value="<?php echo $out_orderid;?>" 

<div id="news">
    <table style="width:100%;height:98%;padding:7px">
        <tr style="font-size:20px;text-align:center">
            <td><span id="new_c">支付中,请稍候...</span></img></td>
        </tr>
    </table>
</div>

<div id="light" class="white_content">
    <table style="width:100%;height:98%;padding:7px">
        <tr style="font-size:20px;text-align:center">
            <td>支付已取消</td>
        </tr>
        <tr style="width:100%;text-align:center;background-color:#04BE02;" onclick="gback()">
            <td><font color="#fff">关闭页面</font></td>
        </tr>
    </table>
</div>
<div id="fade" class="black_overlay">
</div>

<div id="light2" class="white_content">
    <table style="width:100%;height:98%;padding:7px">
        <tr style="font-size:20px;text-align:center">
            <td>支付成功！稍后我们会与您取到联系。</td>
        </tr>
       
    </table>
</div>
<div id="fade2" class="black_overlay">
</div>


<div id="light3" class="white_content">
    <table style="width:100%;height:98%;padding:7px">
        <tr style="font-size:20px;text-align:center">
            <td>支付失败</td>
        </tr>
        <tr style="width:100%;text-align:center;background-color:#04BE02;" onclick="close()">
            <td><font color="#fff">关闭本页面</font></td>
        </tr>
    </table>
</div>
<div id="fade3" class="black_overlay">
</div>

<div id="light4" class="white_content">
    <table style="width:100%;height:98%;padding:7px">
        <tr style="font-size:20px;text-align:center">
            <td>页面已过期</td>
        </tr>
       
    </table>
</div>
<div id="fade4" class="black_overlay">
</div>

<div id="light5" class="white_content">
    <table style="width:100%;height:98%;padding:7px">
        <tr style="font-size:20px;text-align:center">
            <td>失败，该订单已经支付。</td>
        </tr>
       
    </table>
</div>
<div id="fade5" class="black_overlay">
</div>


</body>
</html>