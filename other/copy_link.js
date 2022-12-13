/**
 * 复制当前页面 标题+链接
 */
javascript:void((function(d){let b=d.createElement('input');b.id='copy_box'+Math.random();b.value='['+d.title+']( '+window.location.href+')';d.body.appendChild(b);d.getElementById(b.id).select();d.execCommand('copy');})(document));