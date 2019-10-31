<!-- Navigation -->
<nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <img src="../components/admin/images/setting.png" alt="Settings" class="img-circle hidden-xs"> <a class="navbar-brand" href="admincenter.php">WebSPELL NOR</a>
    </div>
    <!-- /.navbar-header -->

    <ul class="nav navbar-top-links navbar-right">

        <!-- /.dropdown -->
        <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                <span class="fa fa-times"></span> Logout <span class="fa fa-caret-down"></span>
            </a>
            <ul class="dropdown-menu dropdown-user">

                <li><a href="../index.php"><span class="fa fa-undo"></span> Back to Website</a>
                </li>
                <li class="divider"></li>
                <li><a href="../logout.php"><span class="fa fa-sign-out"></span> Logout</a>
                </li>
            </ul>
            <!-- /.dropdown-user -->
        </li>

        <!-- /.dropdown -->

    </ul>
    <!-- /.navbar-top-links -->

    <!-- sidebar-links -->

    <div class="navbar-default sidebar" role="navigation">
        <div class="sidebar-nav navbar-collapse">



            <ul class="nav" id="side-menu">
                <li class="sidebar-search">
                    <div class="input-group custom-search-form">


                        <div class="profile_pic">
        <?php echo $l_avatar ?>
        </div>
        <div class="profile_info">
        <span>Welcome,</span>
        <h2><?php echo $username ?></h2>

        </div>

                    </div>
                    <!-- /input-group -->
                </li>

                <li>
                    <a href="#"><span class="fa fa-area-chart"></span> <?php echo $_language->module['main_panel']; ?><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                            <li><a href="admincenter.php"><?php echo $_language->module['overview']; ?></a></li>
                            <li><a href="admincenter.php?site=page_statistic"><?php echo $_language->module['page_statistics']; ?></a></li>
                            <li><a href="admincenter.php?site=visitor_statistic"><?php echo $_language->module['visitor_statistics']; ?></a></li>
                                <?php echo admincenternav(1); ?>

                    </ul>
                    <!-- /.nav-second-level -->
                </li>


                <?php
                if (isuseradmin($userID)) {
                ?>
                <li>
                    <a href="#"><span class="fa fa-user"></span> <?php echo $_language->module['user_administration']; ?><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                            <li><a href="admincenter.php?site=users"><?php echo $_language->module['registered_users']; ?></a></li>
                            <li><a href="admincenter.php?site=squads"><?php echo $_language->module['squads']; ?></a></li>
                            <li><a href="admincenter.php?site=members"><?php echo $_language->module['clanmembers']; ?></a></li>
                            <li><a href="admincenter.php?site=contact"><?php echo $_language->module['contact']; ?></a></li>
                            <li><a href="admincenter.php?site=newsletter"><?php echo $_language->module['newsletter']; ?></a></li>
                            <?php echo admincenternav(2); ?>
                    </ul>
                    <!-- /.nav-second-level -->
                </li>


                <?php
                }
                if (ispageadmin($userID)) {
                ?>
                <li>
                    <a href="#"><span class="fa fa-warning"></span> <?php echo $_language->module['spam']; ?><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                            <li><a href="admincenter.php?site=spam&amp;action=forum_spam"><?php echo $_language->module['blocked_content']; ?></a></li>
                            <li><a href="admincenter.php?site=spam&amp;action=user"><?php echo $_language->module['spam_user']; ?></a></li>
                            <li><a href="admincenter.php?site=spam&amp;action=multi"><?php echo $_language->module['multiaccounts']; ?></a></li>
                            <li><a href="admincenter.php?site=spam&amp;action=api_log"><?php echo $_language->module['api_log']; ?></a></li>
                        <?php echo admincenternav(3); ?>
                    </ul>
                    <!-- /.nav-second-level -->
                </li>

                <?php
                }
                if (isnewsadmin($userID) || isfileadmin($userID) || ispageadmin($userID)) {
                ?>
                <li>
                    <a href="#"><span class="fa fa-indent"></span> <?php echo $_language->module['rubrics']; ?><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <?php } if (isnewsadmin($userID)) { ?>
                        <li><a href="admincenter.php?site=rubrics"><?php echo $_language->module['news_rubrics']; ?></a></li>
                        <li><a href="admincenter.php?site=newslanguages"><?php echo $_language->module['news_languages']; ?></a></li>
                        <?php } if (isfileadmin($userID)) { ?>
                        <li><a href="admincenter.php?site=filecategories"><?php echo $_language->module['file_categories']; ?></a></li>
                        <?php } if (ispageadmin($userID)) { ?>
                        <li><a href="admincenter.php?site=faqcategories"><?php echo $_language->module['faq_categories']; ?></a></li>
                        <li><a href="admincenter.php?site=linkcategories"><?php echo $_language->module['link_categories']; ?></a></li>
                            <?php echo admincenternav(4); ?>
                    </ul>
                    <!-- /.nav-second-level -->
                </li>


                <?php
                }
                if (ispageadmin($userID)) {
                ?>
                <li>
                    <a href="#"><span class="fa fa-pencil-square"></span> <?php echo $_language->module['settings']; ?><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li><a href="admincenter.php?site=settings"><?php echo $_language->module['settings']; ?></a></li>
                        <li><a href="admincenter.php?site=styles"><?php echo $_language->module['styles']; ?></a></li>
                        <li><a href="admincenter.php?site=dashnavi"><?php echo $_language->module['dashnavi']; ?></a></li>
                        <li><a href="admincenter.php?site=navigation"><?php echo $_language->module['web_navigation']; ?></a></li>
                        <li><a href="admincenter.php?site=countries"><?php echo $_language->module['countries']; ?></a></li>
                        <li><a href="admincenter.php?site=games"><?php echo $_language->module['games']; ?></a></li>
                        <li><a href="admincenter.php?site=modrewrite"><?php echo $_language->module['modrewrite']; ?></a></li>
                        <li><a href="admincenter.php?site=database"><?php echo $_language->module['database']; ?></a></li>
                        <li><a href="admincenter.php?site=update&amp;action=update"><?php echo $_language->module['update_webspell']; ?></a></li>
                        <li><a href="admincenter.php?site=email"><?php echo $_language->module['email']; ?></a></li>

                <?php echo admincenternav(5); ?>
                </ul>
                    <!-- /.nav-second-level -->
                </li>
                <li>
                    <a href="#"><span class="fa fa-font"></span> <?php echo $_language->module['content']; ?><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li><a href="admincenter.php?site=carousel"><?php echo $_language->module['carousel']; ?></a></li>
                        <li><a href="admincenter.php?site=static"><?php echo $_language->module['static_pages']; ?></a></li>
                        <li><a href="admincenter.php?site=faq"><?php echo $_language->module['faq']; ?></a></li>
                        <li><a href="admincenter.php?site=servers"><?php echo $_language->module['servers']; ?></a></li>
                        <li><a href="admincenter.php?site=sponsors"><?php echo $_language->module['sponsors']; ?></a></li>
                        <li><a href="admincenter.php?site=partners"><?php echo $_language->module['partners']; ?></a></li>
                        <li><a href="admincenter.php?site=history"><?php echo $_language->module['history']; ?></a></li>
                        <li><a href="admincenter.php?site=about"><?php echo $_language->module['about_us']; ?></a></li>
                        <li><a href="admincenter.php?site=imprint"><?php echo $_language->module['imprint']; ?></a></li>
                        <li><a href="admincenter.php?site=bannerrotation"><?php echo $_language->module['bannerrotation']; ?></a></li>
                        <?php echo admincenternav(6); ?>
                    </ul>
                    <!-- /.nav-second-level -->
                </li>

                <?php
                }
                if (isforumadmin($userID)) {
                ?>

                <li>
                    <a href="#"><span class="fa fa-list"></span> <?php echo $_language->module['forum']; ?><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li><a href="admincenter.php?site=boards"><?php echo $_language->module['boards']; ?></a></li>
                        <li><a href="admincenter.php?site=groups"><?php echo $_language->module['manage_user_groups']; ?></a></li>
                        <li><a href="admincenter.php?site=group-users"><?php echo $_language->module['manage_group_users']; ?></a></li>
                        <li><a href="admincenter.php?site=ranks"><?php echo $_language->module['user_ranks']; ?></a></li>
                        <?php echo admincenternav(7); ?>
                    </ul>
                    <!-- /.nav-second-level -->
                </li>

                <?php
                }
                if (isgalleryadmin($userID)) {
                ?>
                <li>
                    <a href="#"><span class="fa fa-file-image-o"></span> <?php echo $_language->module['gallery']; ?><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li><a href="admincenter.php?site=gallery&amp;part=groups"><?php echo $_language->module['manage_groups']; ?></a></li>
                        <li><a href="admincenter.php?site=gallery&amp;part=gallerys"><?php echo $_language->module['manage_galleries']; ?></a></li>
                        <?php echo admincenternav(8); ?>
                    </ul>
                    <!-- /.nav-second-level -->
                </li>

                <?php
                }
                if (ispageadmin($userID)) {
                ?>
                <li>
                    <a href="#"><span class="fa fa-arrow-right"></span> <?php echo $_language->module['plugin_base']; ?><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                    <li><a href="admincenter.php?site=plugin-manager"><?php echo $_language->module['plugin_manages']; ?></a></li>

                        <?php echo admincenternav(9); ?>
                    </ul>
                    <!-- /.nav-second-level -->
                </li>


                <?php echo addonnav(); ?>
                </li>
                <?php
                } ?>

            </ul>
        </div>
        <!-- /.sidebar-collapse -->

        <!-- Copy -->
        <div class="copy">
            <em>&nbsp;&copy; 2016 webspell-nor.de&nbsp;Admin Template by <a href="http://designperformance.de/" target="_blank">T-Seven</a></em>
        </div>

    </div>
    <!-- /.navbar-static-side -->
</nav>