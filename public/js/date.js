function formatDate(date,fmt) {
    //todo 小型工厂法
    let o = {
        'y+':date.getFullYear(),
        'M+':date.getMonth()+1,
        'd+':date.getDate(),
        'h+':date.getHours(),
        'm+':date.getMinutes(),
        's+':date.getSeconds()
    }
    //new RegExp('规则')等于/规则/
    for(let k in o ){
        if(new RegExp(`(${k})`).test(fmt)){
            let str = o[k] + '';  //添加空字符串将其转化为字符串
            // fmt = fmt.replace(RegExp.$1, (RegExp.$1.length === 1) ? str : padLeftZero(str));
            fmt = fmt.replace(RegExp.$1,(str.length===1) ? padLeftZero(str):str)
        }
    }
    return fmt;
}
//todo 以上方法要做的就是，不管fmt是'yyyy-MM-dd'还是'y-M-d'都能输出对应的数字
//todo fmt = fmt.replace(RegExp.$1,(str.length===1)?padLeftZero(str):str)指，如果我fmt是不是'dd'而是'd'，那么length===1,那么不加0返回str本身，如果是dd,yy这类，就加0
//测试结果 fmt='y-M-d h:m'，返回都是str本身，没有加0

//首位补0方法
function padLeftZero(str) {
    return ('00'+str).substr(str.length) //技巧：首位补0
}