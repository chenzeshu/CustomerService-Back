
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>原生HTML5波浪滚动动画</title>
    <style>
        *{margin:0;padding:0;list-style:none;}
        body{background-color:#ff5146;}
        .wave{width:200px;height:200px;overflow:hidden;border-radius:50%;background:rgba(255,203,103,.6);margin:100px auto;position:relative;text-align:center;display:table-cell;vertical-align:middle;}
        .wave span{display:inline-block;color:#fff;font-size:20px;position:relative;z-index:2;}
        .wave canvas{position:absolute;left:0;top:0;z-index:1;}
    </style>
</head>
<body>
<!-- 代码部分begin -->
<div id="wave" class="wave"><span>60%</span></div>
<script>
    var wave = (function () {
        var ctx;
        var waveImage;
        var canvasWidth;
        var canvasHeight;
        var needAnimate = false;

        function init (callback) {
            var wave = document.getElementById('wave');
            var canvas = document.createElement('canvas');
            if (!canvas.getContext) return;
            ctx = canvas.getContext('2d');
            canvasWidth = wave.offsetWidth;
            canvasHeight = wave.offsetHeight;
            canvas.setAttribute('width', canvasWidth);
            canvas.setAttribute('height', canvasHeight);
            wave.appendChild(canvas);
            waveImage = new Image();
            waveImage.onload = function () {
                waveImage.onload = null;
                callback();
            }
            waveImage.src = 'assets/wave.png';
        }

        function animate () {
            var waveX = 0;
            var waveY = 0;
            var waveX_min = -203;
            var waveY_max = canvasHeight * 0.7;
            var requestAnimationFrame =
                window.requestAnimationFrame ||
                window.mozRequestAnimationFrame ||
                window.webkitRequestAnimationFrame ||
                window.msRequestAnimationFrame ||
                function (callback) { window.setTimeout(callback, 1000 / 60); };
            function loop () {
                ctx.clearRect(0, 0, canvasWidth, canvasHeight);
                if (!needAnimate) return;
                if (waveY < waveY_max) waveY += 1.5;
                if (waveX < waveX_min) waveX = 0; else waveX -= 3;

                ctx.globalCompositeOperation = 'source-over';
                ctx.beginPath();
                ctx.arc(canvasWidth/2, canvasHeight/2, canvasHeight/2, 0, Math.PI*2, true);
                ctx.closePath();
                ctx.fill();

                ctx.globalCompositeOperation = 'source-in';
                ctx.drawImage(waveImage, waveX, canvasHeight - waveY);

                requestAnimationFrame(loop);
            }
            loop();
        }

        function start () {
            if (!ctx) return init(start);
            needAnimate = true;
            setTimeout(function () {
                if (needAnimate) animate();
            }, 500);
        }
        function stop () {
            needAnimate = false;
        }
        return {start: start, stop: stop};
    }());
    wave.start();
</script>
<!-- 代码部分end -->
</body>
</html>