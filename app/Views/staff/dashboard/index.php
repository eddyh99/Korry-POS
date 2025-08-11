<?php if (isset($_SESSION["message"])){?>
<div class="alert alert-success"><?=$_SESSION["message"]?></div>
<?php } ?>


<div class="content" style="background-color:white;height:100%">
    <div class="container-fluid">
<!-- Start -->

            <div class="form-group row">
                <div class="col-md-6">
                    <div id="netincome" style="height: 300px; width: auto;margin-left:10px"></div>                
                </div>
                <div class="col-md-6">
                    <div id="brand" style="height: 300px; width: auto;margin-left:10px"></div>                
                </div>
            </div>
            <div class="form-group row">
                <div class="col-md-12">
                    <div id="brandstore" style="height: 300px; width: auto;margin-left:10px"></div>                
                </div>
            </div>
        
<!-- End Container -->
    </div>
</div>