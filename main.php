<?php 
    require_once('facebook.php');
?>
<html>
<head>
	<title>Main</title>
	<meta name="description" content="starplus" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/jquery.knob.js"></script>
    <script type="text/javascript" src="js/main.js"></script>
	<link rel="stylesheet" href="css/bootstrap.min.css" />
	<link rel="stylesheet" href="css/bootstrap-responsive.min.css" />
	<link rel="stylesheet" href="css/font-awesome.css" />
    <link rel="stylesheet" href="css/style.css" />

</head>

<body>


<div id='overlay'><img class="loader" src="images/loader2.gif"> </div>
    <div class="container">
        <header class="row">
             <div class="col-md-3 ">
             </div>
             <div class="col-md-6 ">
                <div id="logo"></div>
             </div>
             <div class="col-md-3 ">
             </div>
            
        </header>
        <div class="row">
            <div id="result">
            </div>
        </div>
<script type="text/javascript">
var lowratedfriends=new Array();
var countlow= 0;
console.log(countlow);
</script>
<?php    
if(count($FriendHaveTitle)> 0){

    foreach($FriendHaveTitle as $title=>$friends) {
    ?>

                <div class="col-md-3 holderwrapper">
                <div class="holder">
                    <div class="circlewrapper <?php echo he($friends[0]['empid']);?>_circlewrapper">
                        <img class="loader" src="images/loader.gif">
                        <div id="<?php echo he($friends[0]['empid']);?>_circle" class="circle">
                            <input type="text" id="<?php echo he($friends[0]['empid']);?>_score" class="company" data-fgColor="#AAF200" data-thickness=".1" data-skin="tron" readonly value="0">
                        </div>
                    </div>
                    <a href="https://facebook.com/<?php echo he($friends[0]['empid']); ?>">
                        <h4><b><?php echo $title;?></b></h4>
                    </a>(<?php echo count($friends); ?>)
                    <div class="rating">
                            <div id="<?php echo he($friends[0]['empid']);?>_rating"><img class="loader" src="images/loader.gif"> </div>
                    </div>
                    <div id="people">
                    <?php 
                    $i = 0;
                    foreach ($friends as $friend) {
                        if($i>2)
                        {
                            $i++;
                            break;
                        }    
                            
                        // Extract the pieces of info we need from the requests above
                        $id = idx($friend, 'id');
                        $name = idx($friend, 'name');
                    ?>
                    <a href="https://facebook.com/<?php echo he($id); ?>"><img class="facebook-profile-picture" src="https://graph.facebook.com/<?php echo he($id); ?>/picture?width=50&height=50"></a>
                    <?php 
                    $i++;
                    }
                    if($i>3)
                    {
                    ?>
                    <a data-toggle="modal" data-target="#<?php echo he($friends[0]['empid']);?>_modal" href="#<?php echo he($id); ?>"><img class="facebook-profile-picture" src="images/more.png"></a>
                    <?php
                    }
                    ?>
                    </div>
                    <!-- Modal -->
                    <div class="modal fade" id="<?php echo he($friends[0]['empid']);?>_modal" tabindex="-1" role="dialog" aria-labelledby="<?php echo $title;?>" aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="<?php echo $title;?>"><?php echo $title;?></h4>
                          </div>
                          <div class="modal-body">
                            <?php 
                            foreach ($friends as $friend) {
                                // Extract the pieces of info we need from the requests above
                                $id = idx($friend, 'id');
                                $name = idx($friend, 'name');
                            ?>
                            <a href="https://facebook.com/<?php echo he($id); ?>"><img class="facebook-profile-picture" src="https://graph.facebook.com/<?php echo he($id); ?>/picture?width=100&height=100"></a>
                            <?php 
                            $i++;
                            }
                            ?>
                          </div>
                        </div><!-- /.modal-content -->
                      </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->
                    
                    
                </div>
            </div>

<script type="text/javascript">

$.ajax({
    type: 'GET',
    contentType: 'application/json; charset=UTF-8',
    url: 'glassdoor_crawler.php',
    data: "company=<?php echo $title;?>",
    dataType: 'json',
    success: function(data) {
    
        if(data[0].rating=='Employees are “Dissatisfied”')
            lowratedfriends[countlow++]=<?php echo he($friends[0]['empid']);?>;
            
        if(data[0].rating!="")
        {
            $("#<?php echo he($friends[0]['empid']);?>_rating").html(data[0].rating);
            $('#<?php echo he($friends[0]['empid']);?>_score').val(data[0].score).trigger('change');
            $("#<?php echo he($friends[0]['empid']);?>_circle").show();
            
            /*$('#<?php echo he($friends[0]['empid']);?>_score').knob({
                'fgColor': 'black'
            });*/
            //if (typeof rating[data[0].rating] === "undefined")
        }
        else
        {
            if(data[0].score>0)
            {
                $("#<?php echo he($friends[0]['empid']);?>_rating").html("Rating not found");
                $('#<?php echo he($friends[0]['empid']);?>_score').val(data[0].score).trigger('change');
                $("#<?php echo he($friends[0]['empid']);?>_circle").show();
            }
            else
            {
                $(".<?php echo he($friends[0]['empid']);?>_circlewrapper").css('background','url("images/rated.png") no-repeat center center');
                $(".<?php echo he($friends[0]['empid']);?>_circlewrapper").css('background-size','100%');
                $("#<?php echo he($friends[0]['empid']);?>_circle").hide();
            }

        }
        $(".<?php echo he($friends[0]['empid']);?>_circlewrapper img.loader").hide();
        $(".<?php echo he($friends[0]['empid']);?>_rating img.loader").hide();

    },
    error: function( data ) {
        $("#<?php echo he($friends[0]['empid']);?>_rating").html("Company not found");
        $(".<?php echo he($friends[0]['empid']);?>_circlewrapper").css('background','url("images/rated.png") no-repeat center center');
        $(".<?php echo he($friends[0]['empid']);?>_circlewrapper").css('background-size','100%');
         $(".<?php echo he($friends[0]['empid']);?>_circlewrapper img").hide();
         console.log(<?php echo he($friends[0]['empid']);?>);
    }
});
</script>
        <?php }
    }
    else{
        header('Location: login.php');
    }
    ?>

        </div>

	<script>
    
    function countForText(a)
    {
        var i=0;
        if(window.find)
        {
            while(window.find(a))
            {
                i++
            }
        }
        else if(document.body.createTextRange)
        {
            var rng=document.body.createTextRange();
            while(rng.findText(a))
            {
              i++;
            }
        }
        return i;
    }
        // Wait for window load
		$(window).load(function() {
            $("#overlay").css({
                 'display' : 'none'
              });
              $('#result').html("<h2>"+countForText('Employees are “Dissatisfied”')+" people are unhappy with their jobs</h2>");
                $('.rating .loader').parent().html("Company not Found");
                window.scrollTo();
		});
       

	</script>	
</body>
</html>