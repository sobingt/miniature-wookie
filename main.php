<html>
<head>
	<title>Main</title>
	<meta name="description" content="starplus" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="stylesheet" href="css/bootstrap.min.css" />
	<link rel="stylesheet" href="css/bootstrap-responsive.min.css" />
	<link rel="stylesheet" href="css/style.css" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<body>
<div class="row" style="font-size:40px;text-align:center;font-style:italic;">Where my friends work</div>

<!--START OF A ROW -->

<div class="col-lg-4 holder">
     <input type="text" class="company" data-fgColor="#AAF200" data-thickness=".1" data-skin="tron" readonly value="4.5">
	 <br/>
	 <br/>
	 <b>Bit Brothers(3)</b>
</div>

<div class="col-lg-4 holder">
     <input type="text" class="company" data-fgColor="#D5D50D" data-thickness=".1" data-skin="tron" readonly value="4">
	 <br/>
	 <br/>
	 <b>Infosys Ltd(56)</b>
</div>

<div  class="col-lg-4 holder">
     <input type="text" class="company" data-fgColor="#FFFF00" data-thickness=".1" data-skin="tron" readonly value="3">
	 <br/>
	 <br/>
	 <b>Accenture(34)</b>
</div>

<div class="col-lg-4 holder">
     <input type="text" class="company" data-fgColor="#FF6600" data-thickness=".1" data-skin="tron" readonly value="2">
	 <br/>
	 <br/>
	 <b>ICICI Bank(49)</b>
</div>

<!--END OF ONE ROW -->


<!--
<div  style="position:relative;float:left;width:350px;height:320px;padding:20px;background-color:rgb(51, 183, 189);text-align:center;font-size:20px;margin-right:30px;margin-bottom:30px;">

	<div style="position:absolute;top:10px;left:10px;">
     <input type="text" class="company" data-fgColor="#FF0000" data-width="240" data-height="300" data-thickness=".1" data-skin="tron" readonly value="1"> 
	</div>

	<div style="position:absolute;top:29px;left:31px;">
		<input class="knob" data-min="0" data-max="60"  data-bgColor="#333" readonly data-displayInput=false data-width="200" data-height="200" data-thickness=".45"><img src="http://graph.facebook.com/sobingt/picture" width="50px" height="50px" style="border-radius:50%"/>
	</div>

</div> -->
<!--
 <input class="knob" data-width="150" data-angleOffset="180" readonly data-fgColor="#FF0000" data-skin="tron" data-thickness=".1" value="35">
-->



<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/jquery.knob.js"></script>
<script type="text/javascript">
$(function() {
	
	$(".knob").knob();

	$(".company").knob({
					'min':0
					,'max':5
					,'step':0.1
					,draw : function () {

                        // "tron" case
                        if(this.$.data('skin') == 'tron') {

                            var a = this.angle(this.cv)  // Angle
                                , sa = this.startAngle          // Previous start angle
                                , sat = this.startAngle         // Start angle
                                , ea                            // Previous end angle
                                , eat = sat + a                 // End angle
                                , r = 1;

                            this.g.lineWidth = this.lineWidth;

                            this.o.cursor
                                && (sat = eat - 0.3)
                                && (eat = eat + 0.3);

                            if (this.o.displayPrevious) {
                                ea = this.startAngle + this.angle(this.v);
                                this.o.cursor
                                    && (sa = ea - 0.3)
                                    && (ea = ea + 0.3);
                                this.g.beginPath();
                                this.g.strokeStyle = this.pColor;
                                this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sa, ea, false);
                                this.g.stroke();
                            }

                            this.g.beginPath();
                            this.g.strokeStyle = r ? this.o.fgColor : this.fgColor ;
                            this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sat, eat, false);
                            this.g.stroke();

                            this.g.lineWidth = 2;
                            this.g.beginPath();
                            this.g.strokeStyle = this.o.fgColor;
                            this.g.arc( this.xy, this.xy, this.radius - this.lineWidth + 1 + this.lineWidth * 2 / 3, 0, 2 * Math.PI, false);
                            this.g.stroke();

                            return false;
                        }
                    }
                });
});
</script>
</body>
</html>