<!DOCTYPE html>
<html>
<head>
    <title>Вход в Изнанку</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css" media="screen">*{transition:all .2s}body,html{font-family:"Helvetica",Arial,sans-serif;width:100%;padding:0;margin:0}#logo{display:block;margin:0 auto;height:117px;padding-bottom:17px}button{width:100%;cursor:pointer;font-size:16px;border-radius:3px;background-color:#e8e8e8;color:#000;font-weight:800;padding:9px 28px;border:none}button:focus:enabled,button:hover:enabled{background-color:#f3f3f3}button:disabled{background-color:#efeded;color:#6d6d6d}input[type=login],input[type=password]{box-sizing:border-box;padding:0 9px;font-weight:700;font-size:1em;margin-bottom:13px;height:30px;border:1px solid #dedede;border-radius:3px;width:100%}#w{font-size:13px;margin-left:11px}input[type=login]:focus,input[type=password]:focus{border:1px solid #999dd2;outline:none}.wrong{border-color:#df9e9e!important}.right{border-color:#3eb05c!important;background-color:#dceee0!important}#loginblock{margin-top:7%;max-width:396px;margin-right:auto;margin-left:auto;box-shadow:0 8px 50px #e0e0e0;border-radius:5px}#tableblock{max-width:274px;margin:0 auto;padding:18px 0}@media (max-width:430px){#loginblock{box-shadow:none}}@media (max-width:248px){#logo{height:86px}button{font-size:1em;padding:7px 26px}input[type=login],input[type=password]{font-size:.9em}}</style>
</head>
<body>
<script type="text/javascript">
var passinput,button,caption;document.addEventListener("DOMContentLoaded",function(a){passinput=document.getElementById("passw"),button=document.getElementById("button_login"),caption=document.getElementById("w"),passinput.addEventListener("keyup",function(){""!==caption.textContent&&(caption.textContent="",passinput.className=passinput.className.replace(RegExp("(^|\\b)wrong(\\b|$)","gi")," ")),button.disabled=""===passinput.value},!1),button.addEventListener("click",function(){var a=new XMLHttpRequest;a.onreadystatechange=function(){4==a.readyState&&200==a.status&&("1"==a.responseText?(passinput.className+=" right",setTimeout("document.location.href='/'",1500)):(passinput.className+=" wrong",caption.textContent="Неверный пароль"))},a.open("POST","/",!0),a.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=UTF-8"),a.send("passw="+passinput.value)},!1),passinput.addEventListener("change",function(){""!==passinput.value&&(button.disabled=!1)})});
</script>
<div id="loginblock"> 
    <input type="hidden" value="1" name="authorize">
    <table id="tableblock">
  <tr><td><img id="logo" src="/img/iznanka.png"></td></tr>
  <tr><td><input id="passw" name="passw" type="password" placeholder="Пароль"></td></tr>
  <tr>
  <td><button class="button" id="button_login" disabled>Войти</button>
  <span id="w"></span></td>
  </tr>
  </table>
</div>
</body>
</html>  