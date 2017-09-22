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
        url:"http://cus.app/api/v1/company/400",
        type:"delete",
        data:{
            name:"世界重铸者",
            address:"瑞士",
            profession:2,
            type:'已签约'
        },
        success:function (res) {
          console.log(res)
        },
        error:function (msg) {
            console.log(msg)
            let text = JSON.parse(msg.responseText)
            console.log(text)
        }
    })
</script>
</body>

</html>