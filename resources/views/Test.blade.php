<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>

<script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
<script>
    $.ajax({
        url:"http://cus.app/api/v1/contracts",
        type:"POST",
        data:{
            company_id:20,
            contract_id:"20170723001",
            type1:"1",
            type2:"客服",
            PM:"1,3",
            TM: "2",
            time1:"2017-09-23 14:51:01"
        },
        success:function (res) {
          console.log(res)
        },
        error:function (msg) {
            let text = JSON.parse(msg.responseText)
            for ( let key in text.errors){
                console.log(`${key} : ${text.errors[key]}`)
            }

        }
    })
</script>
<script src="{{asset('js/date.js')}}"></script>
</body>

</html>