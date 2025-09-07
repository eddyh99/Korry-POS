<div class="full-page login-page" filter-color="black" data-image="<?=base_url()?>assets/img/login2.jpg?<?=time()?>">
    <!--   you can change the color of the filter page using: data-color="blue | purple | green | orange | red | rose " -->
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-sm-6 col-md-offset-4 col-sm-offset-3">
                    <form action="<?= base_url()?>auth/auth_login" method="post">
                        <div class="card card-login card-hidden">
                            <div class="card-header text-center" data-background-color="rose">
                                <h4 class="card-title">Login</h4>
                            </div>
                            <div class="card-content">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="material-icons">face</i>
                                    </span>
                                    <div class="form-group label-floating">
                                        <label class="control-label">Username</label>
                                        <input type="text" name="uname" class="form-control">
                                    </div>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="material-icons">lock_outline</i>
                                    </span>
                                    <div class="form-group label-floating">
                                        <label class="control-label">Password</label>
                                        <input type="password" name="pass" class="form-control">
                                    </div>
                                    <?php echo @$_SESSION["error"]; ?>
                                </div>
                            </div>
                            <div class="footer text-center">
                                <button type="submit" class="btn btn-rose btn-simple btn-wd btn-lg">Let's go</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
