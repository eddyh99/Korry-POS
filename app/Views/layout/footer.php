			</div>
		</div>
		<?php if (@$_SESSION["logged_status"]["is_login"]){?>
        <footer class="footer">
            <div class="container-fluid">
                <p class="copyright pull-right">
                    &copy;
                    <script>
                        document.write(new Date().getFullYear())
                    </script>
                    <a href="https://www.softwarebali.com/" rel="nofollow" target="_blank">Software Bali</a>
                </p>
            </div>
        </footer>
        <?php } ?>
    </div>
<!--   Core JS Files   -->
<script src="<?=base_url()?>assets/js/jquery-3.1.1.min.js" type="text/javascript"></script>
<script src="<?=base_url()?>assets/js/jquery-ui.min.js" type="text/javascript"></script>
<script src="<?=base_url()?>assets/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?=base_url()?>assets/js/material.min.js" type="text/javascript"></script>
<script src="<?=base_url()?>assets/js/perfect-scrollbar.jquery.min.js" type="text/javascript"></script>
<!-- Forms Validations Plugin -->
<script src="<?=base_url()?>assets/js/jquery.validate.min.js"></script>
<!--  Plugin for Date Time Picker and Full Calendar Plugin-->
<script src="<?=base_url()?>assets/js/moment.min.js"></script>
<!--  Charts Plugin -->
<script src="<?=base_url()?>assets/js/chartist.min.js"></script>
<!--  Plugin for the Wizard -->
<script src="<?=base_url()?>assets/js/jquery.bootstrap-wizard.js"></script>
<!--  Notifications Plugin    -->
<script src="<?=base_url()?>assets/js/bootstrap-notify.js"></script>
<!--   Sharrre Library    -->
<script src="<?=base_url()?>assets/js/jquery.sharrre.js"></script>
<!-- DateTimePicker Plugin -->
<script src="<?=base_url()?>assets/js/bootstrap-datetimepicker.js"></script>
<!-- Vector Map plugin -->
<script src="<?=base_url()?>assets/js/jquery-jvectormap.js"></script>
<!-- Sliders Plugin -->
<script src="<?=base_url()?>assets/js/nouislider.min.js"></script>
<!-- Select Plugin -->
<script src="<?=base_url()?>assets/js/jquery.select-bootstrap.js"></script>
<!--  DataTables.net Plugin    -->
<script src="<?=base_url()?>assets/js/jquery.datatables.js"></script>
<!-- Sweet Alert 2 plugin -->
<script src="<?=base_url()?>assets/js/sweetalert2.js"></script>
<!--	Plugin for Fileupload, full documentation here: http://www.jasny.net/bootstrap/javascript/#fileinput -->
<script src="<?=base_url()?>assets/js/jasny-bootstrap.min.js"></script>
<!--  Full Calendar Plugin    -->
<script src="<?=base_url()?>assets/js/fullcalendar.min.js"></script>
<!-- TagsInput Plugin -->
<script src="<?=base_url()?>assets/js/jquery.tagsinput.js"></script>
<!-- Material Dashboard javascript methods -->
<script src="<?=base_url()?>assets/js/material-dashboard.js"></script>

<script src="<?=base_url()?>assets/js/demo.js"></script>
<script type="text/javascript">
    $().ready(function() {
        demo.checkFullPageBackgroundImage();

        setTimeout(function() {
            // after 1000 ms we add the class animated to the login/register card
            $('.card').removeClass('card-hidden');
        }, 700)
    });
    
    function isNumber(evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    }    
</script>

<?php 
if (isset($extra)){
	echo view($extra);
}
?>
</body>
</html>